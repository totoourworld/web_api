<?php

/**
 * The DriverAPI class is a sample class.
 *
 * @category  DriverAPI
 * @license   http://localhost/vinay/taxi/index.php
 * @version   0.01
 * @since     2016-09-04
 * @author    Taxi
 */
require_once INCLUDES_DIR . 'SendMail.php';

class DriverAPI
{

    public $restler;
    public $defaultMessageResponse=array();

    /**
     * FUNCTION - Create new Driver.
     *
     * Create new User by passing valid fields. HTTP 201 Created on
     * Success.
     *
     * @param string api_key The api_key string to set
     * @param string d_fname
     * @param string d_lname
     * @param string d_email
     * @param string d_password
     * @param string d_full_name
     * @param string d_phone
     * @param string d_address
     * @param string d_city
     * @param string d_state
     * @param string d_country
     * @param string d_zip
     * @param string d_lat
     * @param string d_lng
     * @param string d_device_type
     * @param string d_device_token
     * @param string d_created
     * @param string d_modified
     * @param string image_id
     *
     * @return  JSON
     *
     * @since   2016-09-04
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/driverapi/registration?
     */
    public function postRegistration($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        $driverModel=new DriverModel();
        if (false == $driverModel->validate($request_data))
        {
            throw new RestException(400, $driverModel->error);
        }
        if (( ! isset($request_data['d_email']) || ($request_data['d_email'] == '')))
        {
            throw new RestException(400, 'please enter your email');
        }
        if (isset($request_data['d_email']))
        {
            $result=$driverModel->selectWhere(array('d_email' => $request_data['d_email']));
            if ($result->num_rows)
            {
                throw new RestException(400, 'Too late! That email is being used');
            }
        }
        if (( ! isset($request_data['d_fname']) || ($request_data['d_fname'] == '')))
        {
            throw new RestException(400, 'please enter your first name');
        }
        if (( ! isset($request_data['d_password']) || ($request_data['d_password'] == '')))
        {
            throw new RestException(400, 'please enter your password');
        }
        $request_data['d_name']=$request_data['d_fname'];
        if ((isset($$request_data['d_lname']) && ($request_data['d_lname'] != '')))
        {
            $request_data['d_name']=$request_data['d_fname'] . " " . $request_data['d_lname'];
        }
        if (isset($request_data['car_id']))
        {
            $carModel=new CarModel();
            $result=$categoryModel->selectWhere(array('car_id' => $request_data['car_id']));
            if ( ! $result->num_rows)
            {
                throw new RestException(400, 'Invalid Car');
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
            if ( ! isset($request_data['driver_image']))
            {
                throw new RestException(400, 'Forgot something? All fields must be filled');
            }
            $userImage=$request_data['driver_image'];
            $imageType=$request_data['image_type'];
            $image_description=$request_data['image_description'];
            unset($request_data['driver_image']);
            unset($request_data['image_type']);
            unset($request_data['image_description']);
        }
        $imageId=NULL;
        if (($userImage != '') && ($imageType != ''))
        {
            $uploaderObj=new Uploader();
            $restul=$uploaderObj->UploadImage($userImage, 'jpg', $image_description, 'Driver Profile Images');
            if ($restul['result'])
            {
                $request_data['image_id']=$restul['image_id'];
            }
        }
        //----------
        if (isset($request_data['driver_license']))
        {
            $driverLicense=$request_data['driver_license'];
            $image_description='';
            unset($request_data['driver_license']);
            $imageId=NULL;
            if (($driverLicense != ''))
            {
                $uploaderObj=new Uploader();
                $restul=$uploaderObj->UploadImage($driverLicense, 'jpg', $image_description, 'Driver License Images');
                if ($restul['result'])
                {
                    $request_data['d_license_id']=$restul['image_id'];
                }
            }
        }
        if (isset($request_data['driver_insurance']))
        {
            $driverLicense=$request_data['driver_insurance'];
            $image_description='';
            unset($request_data['driver_insurance']);
            $imageId=NULL;
            if (($driverLicense != ''))
            {
                $uploaderObj=new Uploader();
                $restul=$uploaderObj->UploadImage($driverLicense, 'jpg', $image_description, 'Driver Insurance Images');
                if ($restul['result'])
                {
                    $request_data['d_insurance_id']=$restul['image_id'];
                }
            }
        }
        //----------
        if (isset($request_data['driver_rc']))
        {
            $driverRc=$request_data['driver_rc'];
            $image_description='';
            unset($request_data['driver_rc']);
            $imageId=NULL;
            if (($driverRc != ''))
            {
                $uploaderObj=new Uploader();
                $restul=$uploaderObj->UploadImage($driverRc, 'jpg', $image_description, 'Driver Rc Images');
                if ($restul['result'])
                {
                    $request_data['d_rc']=$restul['image_id'];
                }
            }
        }
        //----------
        $request_data['d_password']=md5($request_data['d_password']);
        $request_data['api_key']=md5($request_data['d_email'] . $request_data['d_name']);
        $request_data['d_created']=date('Y-m-d H:i:s');
        $request_data['d_modified']=date('Y-m-d H:i:s');
        //print_r($request_data);exit;
        $result=$driverModel->insert($request_data);
        if ($result)
        {
            //-------
            $apikeyModel=new ApikeyModel();
            $apikeyModel->insert(array('api_key' => $request_data['api_key']));
            //--------
            $result=$driverModel->GetDriverDetail(array('driver_id' => $driverModel->callback));
            $returnArr['status']='OK';
            $returnArr['code']=200;
            $returnArr['message']=null;
            $returnArr['response']=$result;
            return $returnArr;
        }
        throw new RestException(500);
    }

    /**
     * FUNCTION - Login the driver.
     *
     * Create new Author by passing valid name and email id. HTTP 201 Created on
     * Success.
     *
     * @param string $email
     * @param string $password
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/driverapi/login?
     */
    public function postLogin($request_data)
    {
        if ( ! isset($request_data['d_email']) || ($request_data['d_email'] == ''))
        {
            throw new RestException(400, 'Email id is  required');
        }
        if ( ! isset($request_data['d_password']) || ($request_data['d_password'] == ''))
        {
            throw new RestException(400, 'password is not required');
        }
        $model=new DriverModel();
        $result=$model->GetLogin(array('d_password' => md5($request_data['d_password']), 'd_email' => $request_data['d_email']));
        if (count($result) > 0)
        {
            $returnArr['status']='OK';
            $returnArr['code']=200;
            $returnArr['message']=$this->defaultMessageResponse;
            $returnArr['response']=$result;
            return $returnArr;
        }
        else
        {
            throw new RestException(400, 'Driver is not found');
        }
    }

    /**
     * FUNCTION - Validate Driver Via username,email,phone.
     *
     * Create new User by passing valid fields. HTTP 201 Created on
     * Success.
     *
     * @param string $d_username
     * @param string $d_email
     * @param string $d_phone
     * @param string $is_help_shown
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/driverapi/drivervalidate?
     */
    public function postDriverValidate($request_data)
    {

        if (empty($request_data))
        {
            throw new RestException(400);
        }
        if ( ! isset($request_data['d_email']) && ! isset($request_data['d_phone']))
        {
            throw new RestException(400);
        }
        $driverModel=new DriverModel();
        if (isset($request_data['d_email']))
        {
            $result=$driverModel->selectWhere(array('d_email' => $request_data['d_email']));
            if ($result->num_rows)
            {
                throw new RestException(400, 'Too late! That email is being used');
            }
        }
        $returnArr['status']=' OK';
        $returnArr['code']=200;
        $returnArr['message']=null;
        $returnArr['response']=TRUE;
        return $returnArr;
    }

    /**
     * FUNCTION - Send the new passwrod on mail.
     *
     * Change the passowrd by passing email id. HTTP 201 Created on
     * Success.
     *
     * @param string $d_email The u_email string to set
     *
     * @return  JSON
     *
     * @since   2016-06-04
     * @author  Dropbuy
     *
     * @example http://aploads.com/development/taxi/index.php/driverapi/forgetpassword?
     */
    public function postForgetPassword($request_data)
    {

        if (empty($request_data))
        {
            throw new RestException(400);
        }
        $mailObj=new SendMail();
        $driverModel=new DriverModel();
        $result=$driverModel->selectWhere(array('d_email' => $request_data['d_email']));
        if ( ! $result->num_rows)
        {
            throw new RestException(400, 'Invalid Email Id.');
        }
        $row=mysqli_fetch_assoc($result);
        $newPassword=GeneratePassword();
        $letters=ForgetPasswordLetter();

        $bkey=0;
        //-------Email Send--------
        $subject='Password reset from Xenia Taxi';
        $body = <<<BODY
    Dear  {$row['d_name']} ,
    <br /> <br/>We received your password retrieval request for your  Xenia Taxi  Account. <br/><br/>
    For security reasons, we keep our users' passwords in an encrypted file, so we're unable to retrieve your old password. Instead, we've issued you a temporary password: <br/> <br/>
        Your User ID:  {$request_data['d_email']}  <br/>
        Your Temporary Password:  {$newPassword}  
        <br/><br/><br/>Once logged into your account, please visit your profile to change your password.
        <br/> <br/> Thank you! <br/> <br/>
        Xenia Taxi 
BODY;
        //-------Email Send--------
        $email=strtolower($request_data['d_email']);
        if ( ! empty($newPassword))
        {
            $result=$driverModel->update(array('d_password' => md5($newPassword)), "d_email='{$email}'");
            if ($result)
            {
                $mailObj=new SendMail();
                $result=$mailObj->SendingMail($row['d_email'], $body, $subject, $row['d_name']);
                $returnArr['status']='OK';
                $returnArr['code']=200;
                $returnArr['message']=null;
                $returnArr['response']=true;
                return $returnArr;
            }
            throw new RestException(400);
        }
        throw new RestException(500);
    }

    /**
     * FUNCTION - Update driver profile.
     *
     * @param string api_key The api_key string to set
     * @param string d_fname
     * @param string d_lname
     * @param string d_email
     * @param string d_password
     * @param string d_full_name
     * @param string d_phone
     * @param string d_address
     * @param string d_city
     * @param string d_state
     * @param string d_country
     * @param string d_zip
     * @param string d_lat
     * @param string d_lng
     * @param string d_device_type
     * @param string d_device_token
     * @param string d_created
     * @param string d_modified
     * @param string image_id
     * @param string is_send_email
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  taxi
     *
     * @example http://aploads.com/development/taxi/index.php/driverapi/updatedriverprofile?
     */
    protected function postUpdateDriverProfile($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        //$isSendEmail = 1;
        $isSendEmail=$request_data['is_send_email'];
        unset($request_data['is_send_email']);
        if (isset($request_data['is_send_email']))
        {
            $isSendEmail=0;
            unset($request_data['is_send_email']);
        }
        if ( ! isset($request_data['driver_id']) || ($request_data['driver_id'] == ''))
        {
            throw new RestException(400, 'Driver id is  required');
        }
        //-------------------
        $driverID=$request_data['driver_id'];
        //--------------------
        $driverModel=new DriverModel();
        $query="AND driver_id !=$driverID";
        if (isset($request_data['car_reg_no'])) {
            if (isset($request_data['car_reg_no'])) {
                $carModel = new CarModel();
                $result = $carModel->selectWhere(array('car_reg_no' => $request_data['car_reg_no']));
                if ($result->num_rows) {
                    throw new RestException(400, 'This Car Already Registered');
                } else {
                    $mycar = array();
                    $mycar['car_reg_no'] = $request_data['car_reg_no'];
                    $mycar['car_make'] = $request_data['car_make'];
                    $mycar['category_id'] = $request_data['category_id'];
                    $mycar['car_model'] = $request_data['car_model'];
                    $mycar['car_name'] = $request_data['car_name'];
                    $mycar['car_created'] = date('Y-m-d H:i:s');
                    $mycar['car_modified'] = date('Y-m-d H:i:s');
                    $carModel->insert($mycar);
                    $result = $carModel->GetCarDetail(array('car_id' => $carModel->callback));
                    $last_id = $result['car_id'];
                    $data = array('car_id' => $last_id);
                    $result = $driverModel->update($data, "driver_id='{$driverID}'");
                }
            }
        }
        if (isset($request_data['d_email']))
        {
            $result=$driverModel->selectWhere(array('d_email' => $request_data['d_email']), $query);
            if ($result->num_rows)
            {
                throw new RestException(400, 'Too late! That email is being used');
            }
        }
        if (isset($request_data['d_phone']))
        {
            $result=$driverModel->selectWhere(array('d_phone' => $request_data['d_phone']), $query);
            if ($result->num_rows)
            {
                throw new RestException(400, 'Too late! That phone number is being used');
            }
        }
        if (isset($request_data['d_password']))
        {
            $request_data['d_password']=md5($request_data['d_password']);
        }
        //-------
        if (isset($request_data['d_email']))
        {
            $request_data['d_email']=strtolower($request_data['d_email']);
        }
        if (isset($request_data['d_fname']) && ! empty($request_data['d_fname']))
        {
            $request_data['d_name']=$request_data['d_fname'];
            if ((isset($$request_data['d_lname']) && ($request_data['d_lname'] != '')))
            {
                $request_data['d_name']=$request_data['d_fname'] . " " . $request_data['d_lname'];
            }
        }
        if (isset($request_data['car_id']))
        {
            $carModel=new CarModel();
            $result=$carModel->selectWhere(array('car_id' => $request_data['car_id']));
            if ( ! $result->num_rows)
            {
                throw new RestException(400, 'Invalid Car');
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
            if ( ! isset($request_data['driver_image']))
            {
                throw new RestException(400, 'Forgot something? All fields must be filled');
            }
            $userImage=$request_data['driver_image'];
            $imageType=$request_data['image_type'];
            $image_description=$request_data['image_description'];
            unset($request_data['driver_image']);
            unset($request_data['image_type']);
            unset($request_data['image_description']);
        }
        $imageId=NULL;
        if (($userImage != '') && ($imageType != ''))
        {
            $uploaderObj=new Uploader();
            $restul=$uploaderObj->UploadImage($userImage, 'jpg', $image_description, 'User Profile Images');
            if ($restul['result'])
            {
                $request_data['image_id']=$restul['image_id'];
            }
        }
        //----------
        if (isset($request_data['driver_license']))
        {
            $driverLicense=$request_data['driver_license'];
            $image_description='';
            unset($request_data['driver_license']);
            $imageId=NULL;
            if (($driverLicense != ''))
            {
                $uploaderObj=new Uploader();
                $restul=$uploaderObj->UploadImage($driverLicense, 'jpg', $image_description, 'Driver License Images');
                if ($restul['result'])
                {
                    $request_data['d_license_id']=$restul['image_id'];
                }
            }
        }
        //----
        if (isset($request_data['driver_insurance']))
        {
            $driverLicense=$request_data['driver_insurance'];
            $image_description='';
            unset($request_data['driver_insurance']);
            $imageId=NULL;
            if (($driverLicense != ''))
            {
                $uploaderObj=new Uploader();
                $restul=$uploaderObj->UploadImage($driverLicense, 'jpg', $image_description, 'Driver Insurance Images');
                if ($restul['result'])
                {
                    $request_data['d_insurance_id']=$restul['image_id'];
                }
            }
        }
        //----------
        if (isset($request_data['driver_rc']))
        {
            $driverRc=$request_data['driver_rc'];
            $image_description='';
            unset($request_data['driver_rc']);
            $imageId=NULL;
            if (($driverRc != ''))
            {
                $uploaderObj=new Uploader();
                $restul=$uploaderObj->UploadImage($driverRc, 'jpg', $image_description, 'Driver Rc Images');
                if ($restul['result'])
                {
                    $request_data['d_rc']=$restul['image_id'];
                }
            }
        }
        //----------
        $request_data['d_modified']=date('Y-m-d H:i:s');
        $userData='';
        //-----------
        $result=$driverModel->selectWhere(array('driver_id' => $driverID));
        if ($result->num_rows)
        {
            $userData=mysqli_fetch_assoc($result);
        }
        $dname = (isset($userData['d_name']) && !empty($userData['d_name']))?$userData['d_name']:'Hello User';
        //-------Email Send--------
        $subject='Profile Updated';
        $body=<<<BODY
	Dear {$dname},
	<br />Thanks for changing your settings on Taxi.<br/>If you are not aware of the changes, please login immediately to confirm your prolife information and change password.<br/><br/>Thanks,<br/>Xenia team
BODY;
        unset($request_data['api_key']);

        //-------Email Send--------
        if ( ! empty($request_data))
        {
            $result=$driverModel->update($request_data, "driver_id='{$driverID}'");
            if ($result)
            {
                if ( ! empty($userData) && $isSendEmail)
                {

                    $mailObj=new SendMail();
                    $result=$mailObj->SendingMail($userData['d_email'], $body, $subject, $userData['d_name']);
                }
                $result=$driverModel->GetDriverDetail(array('driver_id' => $driverID));
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
     * FUNCTION - Update driver password.
     *
     * @param string api_key The api_key string to set
     * @param string d_fname
     * @param string d_lname
     * @param string is_send_email
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  taxi
     *
     * @example http://aploads.com/development/taxi/index.php/driverapi/updatedriverpassword?
     */
    protected function postUpdateDriverPassword($request_data)
    {

        if (empty($request_data))
        {
            throw new RestException(400);
        }
        $isSendEmail=1;
        if (isset($request_data['is_send_email']))
        {
            $isSendEmail=0;
            unset($request_data['is_send_email']);
        }
        if ( ! isset($request_data['driver_id']) || ($request_data['driver_id'] == ''))
        {
            throw new RestException(400, 'Driver id is  required');
        }
        if ( ! isset($request_data['d_password']) || ($request_data['d_password'] == ''))
        {
            throw new RestException(400, 'Please Enter your Old Password');
        }
        //-------------------
        $driverID=$request_data['driver_id'];
        //--------------------
        $driverModel=new DriverModel();
        $query="AND driver_id =$driverID";
        if (isset($request_data['d_password']))
        {

            $result=$driverModel->selectWhere(array('d_password' => md5($request_data['d_password'])), $query);
            if ($result->num_rows)
            {
                if ( ! isset($request_data['new_password']) || ($request_data['new_password'] == ''))
                {
                    throw new RestException(400, 'New Password is required');
                }
                else
                {
                    $request_data['d_password']=md5($request_data['new_password']);
                }
            }
            else
            {

                throw new RestException(400, 'Password Not Matech');
            }
        }
        //-------
        //----------
        $request_data['d_password']=md5($request_data['new_password']);
        $request_data['d_modified']=date('Y-m-d H:i:s');
        unset($request_data['new_password']);
        $userData='';
        //-----------
        $result=$driverModel->selectWhere(array('driver_id' => $driverID));
        if ($result->num_rows)
        {
            $userData=mysqli_fetch_assoc($result);
        }
         $dname = (isset($userData['d_name']) && !empty($userData['d_name']))?$userData['d_name']:'Hello User';
        //-------Email Send--------
        $subject='Profile Updated';
        $body=<<<BODY
    Dear {$dname},
    <br />Thanks for changing your settings on Taxi.<br/>If you are not aware of the changes, please login immediately to confirm your prolife information and change password.<br/><br/>Thanks,<br/>Xenia team
BODY;
        unset($request_data['api_key']);

        //-------Email Send--------
        if ( ! empty($request_data))
        {
            //print_r($request_data); exit;
            $result=$driverModel->update($request_data, "driver_id='{$driverID}'");
            if ($result)
            {
                if ( ! empty($userData) && $isSendEmail)
                {
                    $mailObj=new SendMail();
                    $result=$mailObj->SendingMail($userData['d_email'], $body, $subject, $userData['d_name']);
                }
                $result=$driverModel->GetDriverDetail(array('driver_id' => $driverID));
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
     * FUNCTION - Get all drivers with respect
     *
     * @param string $api_key The api_key string to set
     * @param integer $driver_id The user id integer to set
     * @param integer $d_name The name string to set
     * @param integer $offset The offset integer to set
     * @param integer $limit The limit integer to set
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/driverapi/getdrivers?
     */
    protected function postGetDrivers($request_data)
    {

        $key=$request_data['api_key'];
        $driverModel=new DriverModel();
        unset($request_data['api_key']);
        $driverId=$driverModel->getDriversId($key);
        //------------------------------
        $offset=isset($request_data['offset']) ? $request_data['offset'] : 0;
        $limit=isset($request_data['limit']) ? $request_data['limit'] : 1000;
        $driverId=isset($request_data['driver_id']) ? $request_data['driver_id'] : $driverId;
        $name=isset($request_data['u_name']) ? $request_data['u_name'] : NULL;
        $driverModel=new DriverModel();
        $response=$driverModel->getDrivers($driverId, $name, $offset, $limit);
        if (( ! isset($response['result']) || ($response['result'] == '')))
        {
            throw new RestException(400, 'Invalid Driver.');
        }
        $returnArr['status']='OK';
        $returnArr['code']=200;
        $returnArr['message']=null;
        $returnArr['next_offset']=$response['next_offset'];
        $returnArr['last_offset']=$response['last_offset'];
        $returnArr['response']=$response['result'];
        return $returnArr;
    }

    /**
     * FUNCTION - Get all near by drivers with respect
     *
     * @param string $api_key The api_key string to set
     * @param integer $driver_id The user id integer to set
     * @param integer $d_name The name string to set
     * @param integer $offset The offset integer to set
     * @param integer $limit The limit integer to set
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/driverapi/getnearbydriverlists?
     */
    protected function postGetNearByDriverLists($request_data)
    {

        $key=$request_data['api_key'];
        $driverModel=new DriverModel();
        unset($request_data['api_key']);
        $driverId=$driverModel->getDriversId($key);
        //--------------------
        $offset=isset($request_data['offset']) ? $request_data['offset'] : 0;
        $limit=isset($request_data['limit']) ? $request_data['limit'] : 1000;
        $driverId=isset($request_data['driver_id']) ? $request_data['driver_id'] : $driverId;
        $lat=isset($request_data['lat']) ? $request_data['lat'] : NULL;
        $lng=isset($request_data['lng']) ? $request_data['lng'] : NULL;
        $categoryId=isset($request_data['category_id']) ? $request_data['category_id'] : NULL;
        $miles=isset($request_data['miles']) ? $request_data['miles'] : NULL;
        if (isset($request_data['user_id']))
        { 
            //------
            $userModel=new UserModel();
            $userModel->update(array('u_lat' => $lat), "user_id='{$request_data['user_id']}'");
            $userModel->update(array('u_lng' => $lng), "user_id='{$request_data['user_id']}'");
            //------
        }
        $driverModel=new DriverModel();
        $response=$driverModel->getNearByDriverList($driverId, $lat, $lng, $miles, $categoryId, $offset, $limit);

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
