<?php

/**
 * The NotificationAPI class is a sample class.
 *
 * @category  NotificationAPI
 * @license   http://localhost/vinay/Taxi/index.php
 * @version   0.01
 * @since     2016-03-19
 * @author    Taxi
 */
class NotificationAPI
{

    public $restler;

    public function postSendPushNotification($request_data)
    {
        $message='Vinay IOS Push Notification Testing TEST';
        $deviceToken='';
        if (false)
        {
            $deviceToken='APA91bHPLAZLC03lTjYXmPbZsa9Vpoydh-UzxRQNXYcRdIdcFkSWScpswlydXJNDK4-HLfhXDwYxs7EXLjKxidt6AgrGQKeZpthBX-fRltEm1urayZ8bTpzSnfIKPqAMEF1sHoY8tZuvOBaKKYRiOuws7hBFg8_Tqw';
            $deviceToken='5a336a0140a940c7b056763984999a94be30381eb5c2d675c07b6fb51acf55e9';
            $model=new PushModel();
            $model->androidPushNotification($deviceToken, $message);
            $returnArr['status']='OK';
            $returnArr['code']=200;
            $returnArr['message']=null;
            $returnArr['next_offset']=null;
            $returnArr['last_offset']=null;
            $returnArr['response']='Message successfully delivered amar ' . $message . ' --' . $deviceToken . '-- ' . PHP_EOL;
            return $returnArr;
        }
        else
        {
            $live=0;
            if (isset($request_data['live']))
            {
                $live=$request_data['live'];
            }
            if (isset($request_data['access_token']))
            {
                $deviceToken=$request_data['access_token'];
            }
            if (isset($request_data['message']))
            {
                $message=$request_data['message'];
            }
            if (isset($request_data['user_id']))
            {
                $userId=$request_data['user_id'];
                $model=new NotificationModel();
                $sql="SELECT * FROM user_device WHERE user_id = {$userId}";
                $result=mysqli_query($model->con, $sql);
                if ($result->num_rows)
                {
                    $row=mysqli_fetch_assoc($result);
                    $deviceToken=$row['device_token'];
                    //echo $deviceToken;exit;
                }
            }
            if (isset($request_data['username']))
            {
                $username=$request_data['username'];
                $model=new NotificationModel();
                $sql="SELECT * FROM user WHERE username = {$username}";
                $result=mysqli_query($model->con, $sql);
                if ($result->num_rows)
                {
                    $row=mysqli_fetch_assoc($result);
                    $userId=$row['id'];
                }
                $sql="SELECT * FROM user_device WHERE user_id = {$userId}";
                $result=mysqli_query($model->con, $sql);
                if ($result->num_rows)
                {
                    $row=mysqli_fetch_assoc($result);
                    $deviceToken=$row['device_token'];
                }
            }
            //-------Put your private key's passphrase here:------
            if ($live)
            {
                //-------Put your private key's passphrase here:------
                $passphrase='11111';
                $ctx=stream_context_create();
                //stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/push/ck.pem');
                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/push/ck.pem');
                stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
                stream_context_set_option($ctx, 'ssl', 'cafile', '/var/www/html/push/entrust_2048_ca.cer');
                $fp=stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            }
            else
            {
                //-------Put your private key's passphrase here:------
                $passphrase='11111';
                $ctx=stream_context_create();
                //stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/push/dev_ck.pem');
                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/push/vck.pem');
                stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
                stream_context_set_option($ctx, 'ssl', 'cafile', '/var/www/html/push/entrust_2048_ca.cer');
                $fp=stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
            }
            if ( ! $fp)
            {
                exit("Failed to connect amarnew: $err $errstr" . PHP_EOL);
            }
            //---Create the payload body----
            $is_noti=($is_noti) ? true : false;
            $body['aps']=array('badge' => +1, 'alert' => $message, 'sound' => 'default', 'is_noti' => 1);
            //---echo $deviceToken;print_r($body); die;---
            $payload=json_encode($body);
            //----Build the binary notification----
            $msg=chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
            //---Send it to the server----
            $result=fwrite($fp, $msg, strlen($msg));
            //----var_dump($result); die;----
            if ( ! $result)
            {
                $returnArr['status']='OK';
                $returnArr['code']=200;
                $returnArr['message']=null;
                $returnArr['next_offset']=$response['next_offset'];
                $returnArr['last_offset']=$response['last_offset'];
                $returnArr['response']='Message not delivered ' . ' --' . $deviceToken . '-- ' . PHP_EOL;
            }
            else
            {
                $returnArr['status']='OK';
                $returnArr['code']=200;
                $returnArr['message']=null;
                $returnArr['next_offset']=null;
                $returnArr['last_offset']=null;
                $returnArr['response']='Message successfully delivered amar ' . $message . ' --' . $deviceToken . '-- ' . PHP_EOL;
                //echo 
            }
            //---Close the connection to the server----
            socket_close($fp);
            fclose($fp);
            return $returnArr;
        }
    }

    //http://aploads.com/development/Taxi/index.php/notificationapi/senddrivernotification? api_key=de44a68cba9605e17d8a09b217b5ac200&user_id=12&tdriver_id=12&message=hi&type=follower
    protected function postSendDriverNotification($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        if (( ! isset($request_data['driver_id']) || ($request_data['driver_id'] == '')))
        {
            throw new RestException(400, 'Driver ID Missing.');
        }
        if (( ! isset($request_data['user_id']) || ($request_data['user_id'] == '')))
        {
            throw new RestException(400, 'User ID Missing.');
        }
        if (( ! isset($request_data['message']) || ($request_data['message'] == '')))
        {
            throw new RestException(400, 'Message Missing.');
        }
        if (( ! isset($request_data['type']) || ($request_data['type'] == '')))
        {
            throw new RestException(400, 'Type Missing.');
        }
        $driverModel=new DriverModel();
        $userModel=new UserModel();
        //------------
        $fromUserId=$request_data['user_id'];
        $toUserId=$request_data['driver_id'];
        $message=$request_data['message'];
        $type=$request_data['type'];
        //-------
        $data['type']=$type;
        $data['from_user_id']=$fromUserId;
        $data['from_user_name']=$userModel->getUserName($data['from_user_id']);
        $data['to_user_id']=$toUserId;
        $data['to_user_name']=$driverModel->getDriverName($data['to_user_id']);
        $data['status']='unread';
        $data['created']=date('Y-m-d H:i:s');
        //-----------
        $fields=implode(", ", array_keys($data));
        $values="'" . implode("','", array_values($data)) . "'";
        $sql="INSERT INTO `notifications` ({$fields}) VALUES ({$values})";
        $results=mysqli_query($userModel->con, $sql);
        //------
        $PushModel=new PushModel();
        $PushModel->sendDriverPushNotification($message, 1, $toUserId, 1);
        $returnArr['status']='OK';
        $returnArr['code']=200;
        $returnArr['message']=null;
        $returnArr['next_offset']=null;
        $returnArr['last_offset']=null;
        $returnArr['response']=true;
        return $returnArr;
    }

    //http://aploads.com/development/Taxi/index.php/notificationapi/sendusernotification? api_key=de44a68cba9605e17d8a09b217b5ac200&driver_id=12&user_id=12&message=hi&type=follower
    protected function postSendUserNotification($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        if (( ! isset($request_data['driver_id']) || ($request_data['driver_id'] == '')))
        {
            throw new RestException(400, 'Driver ID Missing.');
        }
        if (( ! isset($request_data['user_id']) || ($request_data['user_id'] == '')))
        {
            throw new RestException(400, 'User ID Missing.');
        }
        if (( ! isset($request_data['message']) || ($request_data['message'] == '')))
        {
            throw new RestException(400, 'Message Missing.');
        }
        if (( ! isset($request_data['type']) || ($request_data['type'] == '')))
        {
            throw new RestException(400, 'Type Missing.');
        }
        $driverModel=new DriverModel();
        $userModel=new UserModel();
        //------------
        $fromUserId=$request_data['driver_id'];
        $toUserId=$request_data['user_id'];
        $message=$request_data['message'];
        $type=$request_data['type'];
        //-------
        $data['type']=$type;
        $data['from_user_id']=$fromUserId;
        $data['from_user_name']=$driverModel->getDriverName($data['from_user_id']);
        $data['to_user_id']=$toUserId;
        $data['to_user_name']=$userModel->getUserName($data['to_user_id']);
        $data['status']='unread';
        $data['created']=date('Y-m-d H:i:s');
        //-----------
        $fields=implode(", ", array_keys($data));
        $values="'" . implode("','", array_values($data)) . "'";
        $sql="INSERT INTO `notifications` ({$fields}) VALUES ({$values})";
        $results=mysqli_query($driverModel->con, $sql);

        //------
        if (false)
        {
            $userModel=new UserModel();
            $userModel->update(array('u_is_available' => 0), "user_id='{$request_data['user_id']}'");
            $driverModel=new DriverModel();
            $driverModel->update(array('d_is_available' => 0), "driver_id='{$request_data['driver_id']}'");
        }
        //------
        //------
        $PushModel=new PushModel();
        $PushModel->sendUserPushNotification($message, 1, $toUserId, 1);
        $returnArr['status']='OK';
        $returnArr['code']=200;
        $returnArr['message']=null;
        $returnArr['next_offset']=null;
        $returnArr['last_offset']=null;
        $returnArr['response']=true;
        return $returnArr;
    }

}

?>
