<?php

class Uploader
{

    var $maxSize;
    var $allowedExt;
    var $fileInfo=array();

    function __construct()
    {
        //$this->maxSize = IMG_MAX_SIZE;
        //$this->allowedExt = ALLOW_EXT;
    }

    function config($maxSize, $allowedExt)
    {
        $this->maxSize=$maxSize;
        $this->allowedExt=$allowedExt;
    }

    function generateRandStr($length)
    {
        $randstr="";
        for ($i=0; $i < $length; $i ++ )
        {
            $randnum=mt_rand(0, 61);
            if ($randnum < 10)
            {
                $randstr .= chr($randnum + 48);
            }
            else if ($randnum < 36)
            {
                $randstr .= chr($randnum + 55);
            }
            else
            {
                $randstr .= chr($randnum + 61);
            }
        }
        return $randstr;
    }

    //-----
    function check($uploadName)
    {
        if (isset($_FILES[$uploadName]))
        {
            $this->fileInfo['ext']=substr(strrchr($_FILES[$uploadName]["name"], '.'), 1);
            $this->fileInfo['name']=basename($_FILES[$uploadName]["name"]);
            $this->fileInfo['size']=$_FILES[$uploadName]["size"];
            $this->fileInfo['temp']=$_FILES[$uploadName]["tmp_name"];
            $this->fileInfo['type']=$_FILES[$uploadName]["type"];
            if ($this->fileInfo['size'] < $this->maxSize)
            {
                if (strlen($this->allowedExt) > 0)
                {
                    $exts=explode(',', $this->allowedExt);
                    if (in_array($this->fileInfo['ext'], $exts))
                    {
                        return true;
                    }
                    //echo 'Invalid file extension. Allowed extensions are '.$this->allowedExt;
                    return 3; //failed ext
                }
                //echo 'Sorry but there is an error in our server. Please try again later.';
                return true; //All ext allowed
            }
            else
            {
                if ($this->maxSize < 1000000)
                {
                    $rsi=round($this->maxSize / 1000, 2) . ' Kb';
                }
                else if ($this->maxSize < 1000000000)
                {
                    $rsi=round($this->maxSize / 1000000, 2) . ' Mb';
                }
                else
                {
                    $rsi=round($this->maxSize / 1000000000, 2) . ' Gb';
                }
                //echo 'File is too big. Maximum allowed size is '.$rsi;
                return 2; //failed size
            }
        }
        //echo 'Oops! An unexpected error occurred, please try again later.';
        return 1; //Either form not submitted or file/s not found
    }

    //-----
    function upload($name, $dir, $fname=false)
    {
        if ( ! $this->CreateDir($dir))
        {
            //--Directory doesn't exist!-----
            die('Sorry but there is an error in our server. Please try again later(Directory doesn\'t exist!).');
        }
        $status=$this->check($name);
        if ($status === true)
        {
            //---Process upload. All info stored in array fileinfo:-----
            //---Dir OK, keep going:----
            //---Get a new filename:---
            if ( ! $fname)
            {
                $this->fileInfo['fname']=$this->generateRandStr(15) . '.' . $this->fileInfo['ext'];
            }
            else
            {
                $this->fileInfo['fname']=$fname;
            }
            //------
            while (file_exists($dir . $this->fileInfo['fname']))
            {
                $this->fileInfo['fname']=$this->generateRandStr(15) . '.' . $this->fileInfo['ext'];
            }
            //----Unique name gotten----
            //---Move file:----
            if (@move_uploaded_file($this->fileInfo['temp'], $dir . $this->fileInfo['fname']))
            {
                //Done
                return true;
            }
            else
            {
                //echo 'The file could not be uploaded, although everything went ok :S ... Please try again later.';
                return 4; //File not moved
            }
        }
        else
        {
            return $status;
        }
    }

    //-----
    function CreateDir($path)
    {
        if ( ! is_dir($path))
        {
            $pathNew=explode('/', $path);
            $create_dir='';
            try
            {
                foreach ($pathNew as $dir)
                {
                    $create_dir .= $dir . '/';
                    @mkdir($create_dir, 0777, true);
                }
            }
            catch (Exception $error_string)
            {
                return false;
            }
        }
        return true;
    }

    //-----------
    function copyImage($wallpost_image, $image_type, $image_description, $image_type_name='')
    {
        ini_set('max_execution_time', 30000);
        $extention='.jpg';
        if ((strtolower($image_type) == 'jpg') || (strtolower($image_type) == 'jpeg'))
        {
            $extention='.jpg';
        }
        else if (strtolower($image_type) == 'png')
        {
            $extention='.png';
        }
        if ($extention != '')
        {
            $folderName=rand(0, 9);
            $hdpath=UPLOAD_IMAGE_PATH . $folderName . '/';
            $thumbnailpath=THUMBNAILS_UPLOAD_IMAGE_PATH . $folderName . '/';
            $smallthumbnailpath=SMALL_THUMBNAILS_UPLOAD_IMAGE_PATH . $folderName . '/';
            if ( ! CreateDir($hdpath))
            {
                $returnArr['result']=false;
                $returnArr['message']='Sorry but there is an error in our server. Please try again later(Directory doesn\'t exist!).'; //Directory doesn't exist!
            }
            if ( ! CreateDir($thumbnailpath))
            {
                $returnArr['result']=false;
                $returnArr['message']='Sorry but there is an error in our server. Please try again later(Directory doesn\'t exist!).'; //Directory doesn't exist!
            }
            if ( ! CreateDir($smallthumbnailpath))
            {
                $returnArr['result']=false;
                $returnArr['message']='Sorry but there is an error in our server. Please try again later(Directory doesn\'t exist!).'; //Directory doesn't exist!
            }
            $fileName=generateRandStr(15) . $extention;
            while (file_exists($hdpath . $fileName))
            {
                $fileName=generateRandStr(15) . $extention;
            }
            $hd_output_file=$hdpath . $fileName;
            $thumbnail_output_file=$thumbnailpath . $fileName;
            $small_thumbnail_output_file=$smallthumbnailpath . $fileName;
            //$this->compress($wallpost_image, $thumbnail_output_file, 20);
            //$this->compress($wallpost_image, $small_thumbnail_output_file, 5);
            $result=copy($wallpost_image, $hd_output_file);
            if (file_exists($hd_output_file))
            {

                $imageModel=new ImageModel();
                $imageArr['image_type']=$image_type_name;
                $imageArr['image_name']=$fileName;
                $imageArr['image_description']=$image_description;
                $imageArr['image_path']=$folderName . '/' . $fileName;
                $imageArr['image_timestamp']=date('Y-m-d H:i:s');
                $imageArr['image_created']=date('Y-m-d H:i:s');
                $imageArr['image_modified']=date('Y-m-d H:i:s');
                if ($imageModel->insert($imageArr))
                {
                    $imageId=$imageModel->callback;
                }
                $returnArr['image_id']=$imageId;
                $returnArr['result']=true;
            }
            else
            {
                $returnArr['result']=false;
                $returnArr['message']='Sorry but there is an error in our server. Please try again later(Directory doesn\'t exist!).';
            }
        }
        else
        {
            $returnArr['result']=false;
            $returnArr['message']='Invalid file extension. Allowed extensions are ' . ALLOW_EXT;
        }
        return $returnArr;
    }

    //-----------
    function UploadImage($wallpost_image, $image_type, $image_description, $image_type_name='')
    {
        ini_set('max_execution_time', 30000);
        $extention='';
        if ((strtolower($image_type) == 'jpg') || (strtolower($image_type) == 'jpeg'))
        {
            $extention='.jpg';
        }
        else if (strtolower($image_type) == 'png')
        {
            $extention='.png';
        }
        if ($extention != '')
        {
            $wallpost_image=str_replace("%2B", "+", $wallpost_image);
            $folderName=rand(0, 9);
            $hdpath=UPLOAD_IMAGE_PATH . $folderName . '/';
            $thumbnailpath=THUMBNAILS_UPLOAD_IMAGE_PATH . $folderName . '/';
            $smallthumbnailpath=SMALL_THUMBNAILS_UPLOAD_IMAGE_PATH . $folderName . '/';
            if ( ! CreateDir($hdpath))
            {
                $returnArr['result']=false;
                $returnArr['message']='Sorry but there is an error in our server. Please try again later(Directory doesn\'t exist!).'; //Directory doesn't exist!
            }
            if ( ! CreateDir($thumbnailpath))
            {
                $returnArr['result']=false;
                $returnArr['message']='Sorry but there is an error in our server. Please try again later(Directory doesn\'t exist!).'; //Directory doesn't exist!
            }
            if ( ! CreateDir($smallthumbnailpath))
            {
                $returnArr['result']=false;
                $returnArr['message']='Sorry but there is an error in our server. Please try again later(Directory doesn\'t exist!).'; //Directory doesn't exist!
            }
            $fileName=generateRandStr(15) . $extention;
            while (file_exists($hdpath . $fileName))
            {
                $fileName=generateRandStr(15) . $extention;
            }
            $hd_output_file=$hdpath . $fileName;
            $thumbnail_output_file=$thumbnailpath . $fileName;
            $small_thumbnail_output_file=$smallthumbnailpath . $fileName;
            $base64='data:image/.jpg' . ';base64,' . $wallpost_image;
            //$this->compress($base64, $thumbnail_output_file, 20);
            //$this->compress($base64, $small_thumbnail_output_file, 5);
            $result=base64_to_jpeg($wallpost_image, $hd_output_file);
            if (file_exists($hd_output_file))
            {
                $imageModel=new ImageModel();
                $imageArr['img_type'] = $image_type_name;
                $imageArr['img_name'] = $fileName;
                $imageArr['img_description'] = $image_description;
                $imageArr['img_path'] = $folderName . '/' . $fileName;
                $imageArr['img_created'] = date('Y-m-d H:i:s');
                $imageArr['img_modified'] = date('Y-m-d H:i:s');
                if ($imageModel->insert($imageArr))
                {
                    $imageId=$imageModel->callback;
                }
                $returnArr['image_id'] = $imageId;
                $returnArr['result'] = true;
            }
            else
            {
                $returnArr['result']=false;
                $returnArr['message']='Sorry but there is an error in our server. Please try again later(Directory doesn\'t exist!).';
            }
        }
        else
        {
            $returnArr['result']=false;
            $returnArr['message']='Invalid file extension. Allowed extensions are ' . ALLOW_EXT;
        }
        return $returnArr;
    }

    function UploadVideo($wallpost_image, $image_type, $image_description, $image_type_name='', $ImageId=0)
    {
        //$returnArray = array();
        $extention='.mp4';
        if ($extention != '')
        {
            $wallpost_image=str_replace("%2B", "+", $wallpost_image);
            $folderName=rand(0, 9);
            $hdpath=UPLOAD_VIDEO_PATH . $folderName . '/';
            if ( ! CreateDir($hdpath))
            {
                $returnArr['result']=false;
                $returnArr['message']='Sorry but there is an error in our server. Please try again later(Directory doesn\'t exist!).';
                //Directory doesn't exist!
            }
            $fileName=generateRandStr(15) . $extention;
            while (file_exists($hdpath . $fileName))
            {
                $fileName=generateRandStr(15) . $extention;
            }
            $hd_output_file=$hdpath . $fileName;
            $base64=$wallpost_image;
            $result=base64_to_jpeg($wallpost_image, $hd_output_file);
            if (file_exists($hd_output_file))
            {
                $imageModel=new ImageModel();
                if ($ImageId)
                {
                    $imageArr['video_path']=$folderName . '/' . $fileName;
                    $imageModel->update(array('video_path' => $folderName . '/' . $fileName), "image_id={$ImageId}");
                    $returnArr['result']=true;
                }
                else
                {
                    $imageArr['img_type']=$image_type_name;
                    $imageArr['img_name']=$fileName;
                    $imageArr['img_description']=$image_description;
                    $imageArr['img_path']=$folderName . '/' . $fileName;
                    $imageArr['img_created']=date('Y-m-d H:i:s');
                    $imageArr['img_modified']=date('Y-m-d H:i:s');
                    if ($imageModel->insert($imageArr))
                    {
                        $imageId=$imageModel->callback;
                    }
                    $returnArr['image_id']=$imageId;
                    $returnArr['result']=true;
                }
            }
            else
            {
                $returnArr['result']=false;
                $returnArr['message']='Sorry but there is an error in our server. Please try again later(Directory doesn\'t exist!).';
            }
            //if(CreateDir($output_file))
        }
        else
        {
            $returnArr['result']=false;
            $returnArr['message']='Invalid file extension. Allowed extensions are ' . ALLOW_EXT;
        }
        return $returnArr;
    }

    function compress($source, $destination, $quality)
    {
        $info=getimagesize($source);
        if ($info['mime'] == 'image/jpeg')
            $image=imagecreatefromjpeg($source);
        elseif ($info['mime'] == 'image/gif')
            $image=imagecreatefromgif($source);
        elseif ($info['mime'] == 'image/png')
            $image=imagecreatefrompng($source);
        imagejpeg($image, $destination, $quality);
        return $destination;
    }

}

?>