<?php

/**
 * The UserAPI class is a sample class.
 *
 * @category  UserAPI
 * @license   http://localhost/vinay/taxi/index.php
 * @version   0.01
 * @since     2016-09-04
 * @author    Taxi
 */
require_once INCLUDES_DIR . 'SendMail.php';

class UserAPI
{

    public $restler;
    public $defaultMessageResponse=array();

    /**
     * FUNCTION - Create new User.
     *
     * Create new User by passing valid fields. HTTP 201 Created on
     * Success.
     *
     * @param string api_key The api_key string to set
     * @param string u_fname
     * @param string u_lname
     * @param string u_email
     * @param string u_password
     * @param string u_full_name
     * @param string u_phone
     * @param string u_address
     * @param string u_city
     * @param string u_state
     * @param string u_country
     * @param string u_zip
     * @param string u_lat
     * @param string u_lng
     * @param string u_device_type
     * @param string u_device_token
     * @param string u_created
     * @param string u_modified
     * @param string image_id
     *
     * @return  JSON
     *
     * @since   2016-09-04
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/userapi/registration?
     */
    public function postRegistration($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        $userModel=new UserModel();
        if (false == $userModel->validate($request_data))
        {
            throw new RestException(400, $userModel->error);
        }
        if (( ! isset($request_data['u_email']) || ($request_data['u_email'] == '')))
        {
            throw new RestException(400, 'please enter your email');
        }
        if (isset($request_data['u_email']))
        {
            $result=$userModel->selectWhere(array('u_email' => $request_data['u_email']));
            if ($result->num_rows)
            {
                throw new RestException(400, 'Too late! That email is being used');
            }
        }
        if (( ! isset($request_data['u_fname']) || ($request_data['u_fname'] == '')))
        {
            throw new RestException(400, 'please enter your first name');
        }
        if (( ! isset($request_data['u_password']) || ($request_data['u_password'] == '')))
        {
            throw new RestException(400, 'please enter your password');
        }
        $request_data['u_name']=$request_data['u_fname'];
        if ((isset($$request_data['u_lname']) && ($request_data['u_lname'] != '')))
        {
            $request_data['u_name']=$request_data['u_fname'] . " " . $request_data['u_lname'];
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
            if ( ! isset($request_data['user_image']))
            {
                throw new RestException(400, 'Forgot something? All fields must be filled');
            }
            $userImage=$request_data['user_image'];
            $imageType=$request_data['image_type'];
            $image_description=$request_data['image_description'];
            unset($request_data['user_image']);
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
        $request_data['text_password']=$request_data['u_password'];
        $request_data['u_password']=md5($request_data['u_password']);
        $request_data['api_key']=md5($request_data['u_email'] . $request_data['u_name']);
        $request_data['u_created']=date('Y-m-d H:i:s');
        $request_data['u_modified']=date('Y-m-d H:i:s');
        $result=$userModel->insert($request_data);
        if ($result)
        {
        	$lastUserId=$userModel->callback;
            //-------Strip GATEWAY------
/*			 
            require_once(LIBRARY_DIR . 'stripe/Stripe.php');
            $params = array(
                "testmode"   => "on",
				"private_live_key" => "sk_live_6xZZRrXbWzcTOXW6kfdyFVt9",
				"public_live_key"  => "pk_live_xxxxxxxxxxxxxxxxxxxxx",
				"private_test_key" => "sk_test_4bIToPRYSxPABKwoQpeF4HZk",
				"public_test_key"  => "pk_test_xxxxxxxxxxxxxxxxxxxxx"
            );
		
            if ($params['testmode'] == "on")
            {
				Stripe::setApiKey($params['private_test_key']);
				//$pubkey = $params['public_test_key'];
            }
            else
            {
				Stripe::setApiKey($params['private_live_key']);
				//$pubkey = $params['public_live_key'];
            }
            $response = array();	
            try
            {
                $createCustomerResponse =Stripe_Customer::create(array("description" => json_encode($request_data),"email" => $request_data['u_email']));
                $userModel->update(array('strip_cus_json' => json_encode($createCustomerResponse)), "user_id='{$lastUserId}'");
                $userModel->update(array('strip_cus_id' => $createCustomerResponse['id']), "user_id='{$lastUserId}'");
                
            }
            catch (Exception $e)
            {
            	$error = $e->getMessage();
            }
 */
            //-------
            //-------
            $apikeyModel=new ApikeyModel();
            $apikeyModel->insert(array('api_key' => $request_data['api_key']));
            //--------
            $result=$userModel->GetUserDetail(array('user_id' => $lastUserId));
		
            $returnArr['status']='OK';
            $returnArr['code']=200;
            $returnArr['message']=null;
            $returnArr['response']=$result;
            return $returnArr;
			
        }
        throw new RestException(500);
    }

    /**
     * FUNCTION - Login the user.
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
     * @example http://aploads.com/development/taxi/index.php/userapi/login?
     */
    public function postLogin($request_data)
    {
        if ( ! isset($request_data['u_email']) || ($request_data['u_email'] == ''))
        {
            throw new RestException(400, 'Email id is required');
        }
        if ( ! isset($request_data['u_password']) || ($request_data['u_password'] == ''))
        {
            throw new RestException(400, 'password is required');
        }
        $model=new UserModel();
        $result=$model->GetLogin(array('u_password' => md5($request_data['u_password']), 'u_email' => $request_data['u_email']));
        if (count($result) > 0)
        {
            if ($result['active'] == 0)
            {
                throw new RestException(400, 'Your account has been deactivated. Please contact your customer support.');
            }
            //Stripe Disable 2018/01/24
            /*
            if(isset($result['user_id']) && empty($result['strip_cus_id']))
            {
                require_once(LIBRARY_DIR . 'stripe/Stripe.php');
                //exit;
                $params = array(
                    "testmode"   => "on",
					"private_live_key" => "sk_live_6xZZRrXbWzcTOXW6kfdyFVt9",
					"public_live_key"  => "pk_live_xxxxxxxxxxxxxxxxxxxxx",
					"private_test_key" => "sk_test_4bIToPRYSxPABKwoQpeF4HZk",
					"public_test_key"  => "pk_test_xxxxxxxxxxxxxxxxxxxxx"
                );

                if ($params['testmode'] == "on")
                {
                    Stripe::setApiKey($params['private_test_key']);
                    //$pubkey = $params['public_test_key'];
                }
                else
                {
                    Stripe::setApiKey($params['private_live_key']);
                    //$pubkey = $params['public_live_key'];
                }
                $response = array();	
                try
                {
                	$userId = $result['user_id'];
                    $createCustomerResponse =Stripe_Customer::create(array("description" => 'Its a '.$result['u_fname'].' customer',"email" =>$result['u_email']));
                    $model->update(array('strip_cus_json' => json_encode($createCustomerResponse)), "user_id='{$userId}'");
                    $model->update(array('strip_cus_id' => $createCustomerResponse['id']), "user_id='{$userId}'");
                }
                catch (Exception $e)
                {
                    $error = $e->getMessage();
                     throw new RestException(400, $error);
                }
            }
*/
            $model->update(array('u_last_loggedin' => date('Y-m-d H:i:s')), "u_email='{$request_data['u_email']}'");
            $returnArr['status']='OK';
            $returnArr['code']=200;
            $returnArr['message']=$this->defaultMessageResponse;
            $returnArr['response']=$result;
            return $returnArr;
        }
        else
        {
            throw new RestException(400, 'User not found');
        }
    }
    
    public function getLoginLoad($request_data)
    {
        if ( ! isset($request_data['u_email']) || ($request_data['u_email'] == ''))
        {
            throw new RestException(400, 'Email id is required');
        }
        if ( ! isset($request_data['u_password']) || ($request_data['u_password'] == ''))
        {
            throw new RestException(400, 'password is required');
        }
        $model=new UserModel();
        $result=$model->GetLogin(array('u_password' => md5($request_data['u_password']), 'u_email' => $request_data['u_email']));
        if (count($result) > 0)
        {
            //print_r($result); exit;
            if ($result['active'] == 0)
            {
                throw new RestException(400, 'Your account has been deactivated. Please contact your customer support.');
            }
            $model->update(array('u_last_loggedin' => date('Y-m-d H:i:s')), "u_email='{$request_data['u_email']}'");
            $returnArr['status']='OK';
            $returnArr['code']=200;
            $returnArr['message']=$this->defaultMessageResponse;
            $returnArr['response']=$result;
            return $returnArr;
        }
        else
        {
            throw new RestException(400, 'User not found');
        }
    }

    public function postFBLogin($request_data)
    {
        if ( ! isset($request_data['u_fbid']) || ($request_data['u_fbid'] == ''))
        {
            throw new RestException(400, 'FB id is required');
        }
        $model=new UserModel();
        $result=$model->GetFBLogin(array('u_fbid' => $request_data['u_fbid']));

        if (count($result) > 0)
        {
            if ($result['active'] == 0)
            {
                throw new RestException(400, 'Your account has been deactivated. Please contact your customer support.');
            }
            
            if(isset($result['user_id']) && empty($result['strip_cus_id']))
            {
            	$userID = $result['user_id'];
                require_once(LIBRARY_DIR . 'stripe/Stripe.php');
                $params = array(
                    "testmode"   => "on",
					"private_live_key" => "sk_live_6xZZRrXbWzcTOXW6kfdyFVt9",
					"public_live_key"  => "pk_live_xxxxxxxxxxxxxxxxxxxxx",
					"private_test_key" => "sk_test_4bIToPRYSxPABKwoQpeF4HZk",
					"public_test_key"  => "pk_test_xxxxxxxxxxxxxxxxxxxxx"
                );

                if ($params['testmode'] == "on")
                {
                    Stripe::setApiKey($params['private_test_key']);
                    //$pubkey = $params['public_test_key'];
                }
                else
                {
                    Stripe::setApiKey($params['private_live_key']);
                    //$pubkey = $params['public_live_key'];
                }
                $response = array();	
                try
                {
                    $createCustomerResponse =Stripe_Customer::create(array("description" => 'Its a '.$result['u_fname'].' customer',"email" =>$result['u_email']));
                    $model->update(array('strip_cus_json' => json_encode($createCustomerResponse)), "user_id='{$userID}'");
                    $model->update(array('strip_cus_id' => $createCustomerResponse['id']), "user_id='{$userID}'");
                }
                catch (Exception $e)
                {
                    $error = $e->getMessage();
                }
            }
            $model->update(array('u_last_loggedin' => date('Y-m-d H:i:s')), "u_fbid='{$request_data['u_fbid']}'");
            $returnArr['status']='OK';
            $returnArr['code']=200;
            $returnArr['message']=$this->defaultMessageResponse;
            $returnArr['response']=$result;
            return $returnArr;
        }
        else
        {
            throw new RestException(400, 'Invalid FB ID OR FB ID not found.');
        }
    }

    public function postUpdateLoggedInTimeStamp($request_data)
    {
        if ( ! isset($request_data['user_id']) || ($request_data['user_id'] == ''))
        {
            throw new RestException(400, 'User id is  required');
        }
        //-------------------
        $userID=$request_data['user_id'];
        $userModel=new UserModel();
        $result=$userModel->selectWhere(array('user_id' => $userID));

        if (count($result) > 0)
        {
            $userModel->update(array('u_last_loggedin' => date('Y-m-d H:i:s')), "user_id='{$userID}'");
            $returnArr['status']='OK';
            $returnArr['code']=200;
            $returnArr['message']=$this->defaultMessageResponse;
            $returnArr['response']=true;
            return $returnArr;
        }
        else
        {
            throw new RestException(400, 'User is not found');
        }
    }

    /**
     * FUNCTION - Validate User Via username,email,phone.
     *
     * Create new User by passing valid fields. HTTP 201 Created on
     * Success.
     *
     * @param string $u_username
     * @param string $u_email
     * @param string $u_phone
     * @param string $is_help_shown
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/userapi/uservalidate?
     */
    public function postUserValidate($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        if ( ! isset($request_data['u_email']) && ! isset($request_data['u_phone']))
        {
            throw new RestException(400);
        }
        $userModel=new UserModel();
        if (isset($request_data['u_email']))
        {
            $result=$userModel->selectWhere(array('u_email' => $request_data['u_email']));
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
     * @param string $u_email The u_email string to set
     *
     * @return  JSON
     *
     * @since   2016-06-04
     * @author  Dropbuy
     *
     * @example http://aploads.com/development/taxi/index.php/userapi/forgetpassword?
     */
    public function postForgetPassword($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        $mailObj=new SendMail();
        $userModel=new UserModel();
        $result=$userModel->selectWhere(array('u_email' => $request_data['u_email']));
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
    Dear {$row['u_name']} ,
    <br /> <br/>We received your password retrieval request for your Xenia Account. <br/><br/>
    For security reasons, we keep our users' passwords in an encrypted file, so we're unable to retrieve your old password. Instead, we've issued you a temporary password: <br/> <br/>
        Your User ID: {$request_data['u_email']} <br/>
        Your Temporary Password: {$newPassword}  
        <br/><br/><br/>Once logged into your account, please visit your profile to change your password.
        <br/> <br/> Thank you! <br/> <br/>
         Xenia Taxi  
BODY;
        //-------Email Send--------
        $email=strtolower($request_data['u_email']);
        if ( ! empty($newPassword))
        {
            $result=$userModel->update(array('u_password' => md5($newPassword),'text_password'=>$newPassword), "u_email='{$email}'");
            if ($result)
            {
                $mailObj=new SendMail();
                $result=$mailObj->SendingMail($row['u_email'], $body, $subject, $row['u_name']);
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
     * FUNCTION - Update user profile.
     *
     * @param string api_key The api_key string to set
     * @param string u_fname
     * @param string u_lname
     * @param string u_email
     * @param string u_password
     * @param string u_full_name
     * @param string u_phone
     * @param string u_address
     * @param string u_city
     * @param string u_state
     * @param string u_country
     * @param string u_zip
     * @param string u_lat
     * @param string u_lng
     * @param string u_device_type
     * @param string u_device_token
     * @param string u_created
     * @param string u_modified
     * @param string image_id
     * @param string is_send_email
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  taxi
     *
     * @example http://aploads.com/development/taxi/index.php/userapi/updateuserprofile?
     */
    protected function postUpdateUserProfile($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        $isSendEmail=$request_data['is_send_email'];
        unset($request_data['is_send_email']);
        //$isSendEmail = 1;
        if (isset($request_data['is_send_email']))
        {
            $isSendEmail=0;
            unset($request_data['is_send_email']);
        }
        if ( ! isset($request_data['user_id']) || ($request_data['user_id'] == ''))
        {
            throw new RestException(400, 'User id is  required');
        }
        //-------------------
        $userID=$request_data['user_id'];
        //--------------------
        $userModel=new UserModel();
        $query="AND user_id !=$userID";
        if (isset($request_data['u_email']))
        {
            $result=$userModel->selectWhere(array('u_email' => $request_data['u_email']), $query);
            if ($result->num_rows)
            {
                throw new RestException(400, 'Too late! That email is being used');
            }
        }
        if (isset($request_data['u_phone']))
        {
            $result=$userModel->selectWhere(array('u_phone' => $request_data['u_phone']), $query);
            if ($result->num_rows)
            {
                throw new RestException(400, 'Too late! That phone number is being used');
            }
        }
        if (isset($request_data['u_password']))
        {
        	$request_data['text_password']=$request_data['u_password'];
            $request_data['u_password']=md5($request_data['u_password']);
        }
        //-------
        if (isset($request_data['u_email']))
        {
            $request_data['u_email']=strtolower($request_data['u_email']);
        }
        if (isset($request_data['u_fname']) && ! empty($request_data['u_fname']))
        {
            $request_data['u_name']=$request_data['u_fname'];
            if ((isset($request_data['u_lname']) && ($request_data['u_lname'] != '')))
            {
                $request_data['u_name']=$request_data['u_fname'] . " " . $request_data['u_lname'];
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
            if ( ! isset($request_data['user_image']))
            {
                throw new RestException(400, 'Forgot something? All fields must be filled');
            }
            $userImage=$request_data['user_image'];
            $imageType=$request_data['image_type'];
            $image_description=$request_data['image_description'];
            unset($request_data['user_image']);
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
        $request_data['u_modified']=date('Y-m-d H:i:s');
        $userData='';
        //-----------
        $result=$userModel->selectWhere(array('user_id' => $userID));
        if ($result->num_rows)
        {
            $userData=mysqli_fetch_assoc($result);
        }
         $uname = (isset($userData['u_name']) && !empty($userData['u_name']))?$userData['u_name']:'Hello User';
        //-------Email Send--------
        $subject='Profile Updated';
        $body=<<<BODY
	Dear {$uname},
	<br />Thanks for changing your settings on Taxi.<br/>If you are not aware of the changes, please login immediately to confirm your prolife information and change password.<br/><br/>Thanks,<br/>Xenia team
BODY;
        unset($request_data['api_key']);
        //-------Email Send--------
        if ( ! empty($request_data))
        {
            $result=$userModel->update($request_data, "user_id='{$userID}'");
            if ($result)
            {
                if ( ! empty($userData) && $isSendEmail)
                {
                    $mailObj=new SendMail();
                    $result=$mailObj->SendingMail($userData['u_email'], $body, $subject, $userData['u_name']);
                }
                //-------
        /*
            if(empty($userData['strip_cus_id']))
            {
                require_once(LIBRARY_DIR . 'stripe/Stripe.php');
                $params = array(
                    "testmode"   => "on",
					"private_live_key" => "sk_live_6xZZRrXbWzcTOXW6kfdyFVt9",
					"public_live_key"  => "pk_live_xxxxxxxxxxxxxxxxxxxxx",
					"private_test_key" => "sk_test_4bIToPRYSxPABKwoQpeF4HZk",
					"public_test_key"  => "pk_test_xxxxxxxxxxxxxxxxxxxxx"
                );

                if ($params['testmode'] == "on")
                {
                    Stripe::setApiKey($params['private_test_key']);
                    //$pubkey = $params['public_test_key'];
                }
                else
                {
                    Stripe::setApiKey($params['private_live_key']);
                    //$pubkey = $params['public_live_key'];
                }
                $response = array();	
                try
                {

                    $createCustomerResponse =Stripe_Customer::create(array("description" => 'Its a '.$userData['u_fname'].' customer',"email" =>$userData['u_email']));
                    $userModel->update(array('strip_cus_json' => json_encode($createCustomerResponse)), "user_id='{$userID}'");
                    $userModel->update(array('strip_cus_id' => $createCustomerResponse['id']), "user_id='{$userID}'");

                }
                catch (Exception $e)
                {
                    $error = $e->getMessage();
                }
            }
            */
                $result=$userModel->GetUserDetail(array('user_id' => $userID));
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
     * FUNCTION - Update user password.
     *
     * @param string api_key The api_key string to set
     * @param string u_fname
     * @param string u_lname
     * @param string is_send_email
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  taxi
     *
     * @example http://aploads.com/development/taxi/index.php/userapi/updateuserpassword?
     */
    protected function postUpdateUserPassword($request_data)
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
        if ( ! isset($request_data['user_id']) || ($request_data['user_id'] == ''))
        {
            throw new RestException(400, 'User id is required');
        }
        if ( ! isset($request_data['u_password']) || ($request_data['u_password'] == ''))
        {
            throw new RestException(400, 'Please Enter your Old Password');
        }
        //-------------------
        $userID=$request_data['user_id'];
        //--------------------
        $userModel=new UserModel();
        $query="AND user_id =$userID";
        if (isset($request_data['u_password']))
        {

            $result=$userModel->selectWhere(array('u_password' => md5($request_data['u_password'])), $query);
            if ($result->num_rows)
            {
                if ( ! isset($request_data['new_password']) || ($request_data['new_password'] == ''))
                {
                    throw new RestException(400, 'New Password is required');
                }
                else
                {
                    $request_data['u_password']=md5($request_data['new_password']);
                }
            }
            else
            {

                throw new RestException(400, 'Password Not Matech');
            }
        }
        //-------
        //----------
        $request_data['text_password']=$request_data['new_password'];
        $request_data['u_password']=md5($request_data['new_password']);
        $request_data['u_modified']=date('Y-m-d H:i:s');
        unset($request_data['new_password']);
        $userData='';
        //-----------
        $result=$userModel->selectWhere(array('user_id' => $userID));
        if ($result->num_rows)
        {
            $userData=mysqli_fetch_assoc($result);
        }
        $uname = (isset($userData['u_name']) && !empty($userData['u_name']))?$userData['u_name']:'Hello User';
        //-------Email Send--------
        $subject='Profile Updated';
        $body=<<<BODY
    Dear {$uname},
    <br />Thanks for changing your settings on Taxi.<br/>If you are not aware of the changes, please login immediately to confirm your prolife information and change password.<br/><br/>Thanks,<br/>Xenia team
BODY;
        unset($request_data['api_key']);

        //-------Email Send--------
        if ( ! empty($request_data))
        {
            //print_r($request_data); exit;
            $result=$userModel->update($request_data, "user_id='{$userID}'");
            if ($result)
            {
                if ( ! empty($userData) && $isSendEmail)
                {
                    $mailObj=new SendMail();
                    $result=$mailObj->SendingMail($userData['u_email'], $body, $subject, $userData['u_name']);
                }
                $result=$userModel->GetUserDetail(array('user_id' => $userID));
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
     * FUNCTION - Get all users with respect
     *
     * @param string $api_key The api_key string to set
     * @param integer $user_id The user id integer to set
     * @param integer $u_name The name string to set
     * @param integer $offset The offset integer to set
     * @param integer $limit The limit integer to set
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/userapi/getusers?
     */
    protected function postGetUsers($request_data)
    {
        $key=$request_data['api_key'];
        $userModel=new UserModel();
        unset($request_data['api_key']);
        $userId=$userModel->getUsersId($key);
        //--------------------
        $offset=isset($request_data['offset']) ? $request_data['offset'] : 0;
        $limit=isset($request_data['limit']) ? $request_data['limit'] : 1000;
        $userId=isset($request_data['user_id']) ? $request_data['user_id'] : $userId;
        $name=isset($request_data['u_name']) ? $request_data['u_name'] : NULL;
        $userModel=new UserModel();
        $response=$userModel->getUsers($userId, $name, $offset, $limit);
        if (( ! isset($response['result']) || ($response['result'] == '')))
        {
            throw new RestException(400, 'Invalid User.');
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
     * FUNCTION - Get all near by users with respect
     *
     * @param string $api_key The api_key string to set
     * @param integer $user_id The user id integer to set
     * @param integer $u_name The name string to set
     * @param integer $offset The offset integer to set
     * @param integer $limit The limit integer to set
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/userapi/getnearbyuserlists?
     */
    protected function postGetNearByUserLists($request_data)
    {
        $key=$request_data['api_key'];
        $usermodel=new UserModel();
        unset($request_data['api_key']);
        $userId=$usermodel->getUsersId($key);
        //-------------------------
        $offset=isset($request_data['offset']) ? $request_data['offset'] : 0;
        $limit=isset($request_data['limit']) ? $request_data['limit'] : 1000;
        $userId=isset($request_data['user_id']) ? $request_data['user_id'] : $userId;
        $lat=isset($request_data['lat']) ? $request_data['lat'] : NULL;
        $lng=isset($request_data['lng']) ? $request_data['lng'] : NULL;
        $miles=isset($request_data['miles']) ? $request_data['miles'] : 3500;
        $userModel=new UserModel();
        $response=$userModel->getNearByUserList($userId, $lat, $lng, $miles, $offset, $limit);

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
