<?php
//session_start(); 
require_once 'define.php';

/*Directories that contain controller classes*/
$classesDir = array ( MODEL_DIR, AUTH_DIR );
/*
 * Include controller files
 * @param unknown_type $string
 * @return Full Message String
 */

function classLoader($class_name)
    {
       global $classesDir;
       foreach ($classesDir as $directory)
       {
            if (file_exists($directory . $class_name . '.php')) 
            {
                require_once($directory . $class_name . '.php');
                return;
            }
        }
    }
// register the loader functions //
spl_autoload_register('classLoader');

/*
 * Include the files inside includes folder
 * @param unknown_type $string
 * @return Full Message String
 */
function include_all_php($folder){
    foreach (glob("{$folder}/*.php") as $filename)
    {
        require_once($filename);
    }
}
/*
 * Enter description here...
 * @param unknown_type $string
 * @return Full Message String
 */
function text( $string )
{
	global $strings;
	if (isset($strings[$string]) && $strings[$string]!='')
	{
		return $strings[ $string ];
	}
	else
	{
		return $string;

	}
}
/**
 * Get replace later for forget mail
 * @return Array
 */
function ForgetPasswordLetter()
{
    return array('{name}','{password}');
}

function GeneratePassword($numAlpha=6,$numNonAlpha=2)
{
   $listAlpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
   $listNonAlpha = ',;:!?.$/*-+&@_+;./*&?$-!,';
   $listNonAlpha = '';
   return str_shuffle(
      substr(str_shuffle($listAlpha),0,$numAlpha) .
      substr(str_shuffle($listNonAlpha),0,$numNonAlpha)
    );
}
function generateRandStr($length){
    $randstr = "";
    for($i=0; $i< $length; $i++){
        $randnum = mt_rand(0,61);
        if($randnum < 10){
            $randstr .= chr($randnum+48);
        }else if($randnum < 36){
            $randstr .= chr($randnum+55);
        }else{
            $randstr .= chr($randnum+61);
        }
    }
    return $randstr;
}
function base64_to_jpeg($base64_string, $output_file) {
    $ifp = fopen($output_file, "wb");

    $data = explode(',', $base64_string);

    fwrite($ifp, base64_decode($base64_string));
    //fwrite($ifp, base64_decode($data[1]));
    fclose($ifp);

    return $output_file;
}
function CreateDir( $path )
{
    if(!is_dir($path))
    {
            $pathNew = explode('/', $path);
        $create_dir = '';
        try{
            foreach ($pathNew as $dir)
            {
                    $create_dir .= $dir . '/';
                    @mkdir( $create_dir, 0777,true);
            }

        }
        catch (Exception $error_string)
        {
            return false;
        }
    }

    return true;
}

?>
