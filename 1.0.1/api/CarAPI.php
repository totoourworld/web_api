<?php

/**
 * The CarAPI class is a sample class.
 *
 * @category  CarAPI
 * @license   http://localhost/vinay/taxi/index.php
 * @version   0.01
 * @since     2016-09-04
 * @author    Taxi
 */
require_once INCLUDES_DIR . 'SendMail.php';

class CarAPI
{

    public $restler;
    public $defaultMessageResponse=array();

    /**
     * FUNCTION - Create new Car.
     *
     * Create new User by passing valid fields. HTTP 201 Created on
     * Success.
     *
     * @param string category_id
     * @param string image_id
     * @param string car_name
     * @param string car_desc
     * @param string car_reg_no
     * @param string car_model
     * @param string car_fare_per_km
     * @param string car_fare_per_min
     * @param string car_currency
     * @param string car_created
     * @param string car_modified
     *
     * @return  JSON
     *
     * @since   2016-09-04
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/carapi/save?
     */
    public function postSave($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        $carModel=new CarModel();
        if (false == $carModel->validate($request_data))
        {
            throw new RestException(400, $carModel->error);
        }
        if (( ! isset($request_data['category_id']) || ($request_data['category_id'] == '')))
        {
            throw new RestException(400, 'please enter car category');
        }
        if (( ! isset($request_data['car_name']) || ($request_data['car_name'] == '')))
        {
            throw new RestException(400, 'please enter car name');
        }
        if (isset($request_data['category_id']))
        {
            $categoryModel=new CategoryModel();
            $result=$categoryModel->selectWhere(array('category_id' => $request_data['category_id']));
            if ( ! $result->num_rows)
            {
                throw new RestException(400, 'Invalid Category');
            }
        }
        //----Image Upload-------
        require_once INCLUDES_DIR . 'UploadContent.php';
        $userImage="";
        $imageType="";
        $image_description="";
        $is_fb_image=0;
        $is_map_image=0;
        //-----------
        if (isset($request_data['image_type']))
        {
            if ( ! isset($request_data['car_image']))
            {
                throw new RestException(400, 'Forgot something? All fields must be filled');
            }
            $userImage=$request_data['car_image'];
            $imageType=$request_data['image_type'];
            $image_description=$request_data['image_description'];
            unset($request_data['car_image']);
            unset($request_data['image_type']);
            unset($request_data['image_description']);
        }
        $imageId=NULL;
        if (($userImage != '') && ($imageType != ''))
        {
            $uploaderObj=new Uploader();
            $restul=$uploaderObj->UploadImage($userImage, 'jpg', $image_description, 'Car Profile Images');
            if ($restul['result'])
            {
                $request_data['image_id']=$restul['image_id'];
            }
        }
        //----------
        $request_data['car_created']=date('Y-m-d H:i:s');
        $request_data['car_modified']=date('Y-m-d H:i:s');
        $result=$carModel->insert($request_data);
        if ($result)
        {
            $result=$carModel->GetCarDetail(array('car_id' => $carModel->callback));
            $returnArr['status']='OK';
            $returnArr['code']=200;
            $returnArr['message']=null;
            $returnArr['response']=$result;
            return $returnArr;
        }
        throw new RestException(500);
    }

    /**
     * FUNCTION - Update car profile.
     *
     * @param string category_id
     * @param string image_id
     * @param string car_name
     * @param string car_desc
     * @param string car_reg_no
     * @param string car_model
     * @param string car_fare_per_km
     * @param string car_fare_per_min
     * @param string car_currency
     * @param string car_created
     * @param string car_modified
     * @param string is_send_email
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  taxi
     *
     * @example http://aploads.com/development/taxi/index.php/carapi/updatecarprofile?
     */
    protected function postUpdateCarProfile($request_data)
    {
        if (empty($request_data))
        {

            throw new RestException(400);
        }
        $isSendEmail=1;
        $carModel=new CarModel();
        if (isset($request_data['is_send_email']))
        {
            $isSendEmail=0;
            unset($request_data['is_send_email']);
        }
        if (false == $carModel->validate($request_data))
        {
            throw new RestException(400, $carModel->error);
        }
        if (( ! isset($request_data['car_id']) || ($request_data['car_id'] == '')))
        {
            throw new RestException(400, 'Car Id Missing');
        }
        if (isset($request_data['category_id']))
        {
            $categoryModel=new CategoryModel();
            $result=$categoryModel->selectWhere(array('category_id' => $request_data['category_id']));
            if ( ! $result->num_rows)
            {
                throw new RestException(400, 'Invalid Category');
            }
        }
        //-------------------
        $carID=$request_data['car_id'];
        //--------------------
        $query="AND car_id !=$carID";
        //----Image Upload-------
        require_once INCLUDES_DIR . 'UploadContent.php';
        $userImage="";
        $imageType="";
        $image_description="";
        $is_fb_image=0;
        $is_map_image=0;
        //-----------
        if (isset($request_data['image_type']))
        {
            if ( ! isset($request_data['car_image']))
            {
                throw new RestException(400, 'Forgot something? All fields must be filled');
            }
            $userImage=$request_data['car_image'];
            $imageType=$request_data['image_type'];
            $image_description=$request_data['image_description'];
            unset($request_data['car_image']);
            unset($request_data['image_type']);
            unset($request_data['image_description']);
        }
        $imageId=NULL;
        if (($userImage != '') && ($imageType != ''))
        {
            $uploaderObj=new Uploader();
            $restul=$uploaderObj->UploadImage($userImage, 'jpg', $image_description, 'Car Profile Images');
            if ($restul['result'])
            {
                $request_data['image_id']=$restul['image_id'];
            }
        }
        //----------
        $request_data['car_modified']=date('Y-m-d H:i:s');
        //-------
        unset($request_data['api_key']);
        //--------
        if ( ! empty($request_data))
        {
            $result=$carModel->update($request_data, "car_id='{$carID}'");
            if ($result)
            {
                $result=$carModel->GetCarDetail(array('car_id' => $carID));
                $returnArr['status']='OK';
                $returnArr['code']=200;
                $returnArr['message']=null;
                $returnArr['response']=$result;
                return $returnArr;
            }
            throw new RestException(400);
        }
        throw new RestException(500);
    }

    /**
     * FUNCTION - Get all cars with respect
     *
     * @param string $api_key The api_key string to set
     * @param integer $car_id The car id integer to set
     * @param integer $car_name The name string to set
     * @param integer $offset The offset integer to set
     * @param integer $limit The limit integer to set
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/carapi/getcars?
     */
    protected function postGetCars($request_data)
    {
        unset($request_data['api_key']);
        $offset=isset($request_data['offset']) ? $request_data['offset'] : 0;
        $limit=isset($request_data['limit']) ? $request_data['limit'] : 1000;
        $carId=isset($request_data['car_id']) ? $request_data['car_id'] : NULL;
        $name=isset($request_data['car_name']) ? $request_data['car_name'] : NULL;
        $categoryId=isset($request_data['category_id']) ? $request_data['category_id'] : NULL;
        $carModel=new CarModel();
        $response=$carModel->getCars($carId, $name, $categoryId, $offset, $limit);
        if (( ! isset($response['result']) || ($response['result'] == '')))
        {
            throw new RestException(400, 'Record Not Found.');
        }
        $returnArr['status']='OK';
        $returnArr['code']=200;
        $returnArr['message']=null;
        $returnArr['next_offset']=$response['next_offset'];
        $returnArr['last_offset']=$response['last_offset'];
        $returnArr['response']=$response['result'];
        return $returnArr;
    }

    function jschars($str)
    {
        $str=mb_ereg_replace("\\\\", "\\\\", $str);
        $str=mb_ereg_replace("\"", "\\\"", $str);
        //$str = mb_ereg_replace("'", "\\'", $str);
        $str=mb_ereg_replace("\r\n", "\\n", $str);
        $str=mb_ereg_replace("\r", "\\n", $str);
        $str=mb_ereg_replace("\n", "\\n", $str);
        $str=mb_ereg_replace("\t", "\\t", $str);
        $str=mb_ereg_replace("<", "\\x3C", $str); // for inclusion in HTML
        $str=mb_ereg_replace(">", "\\x3E", $str);
        return $str;
    }

}

?>
