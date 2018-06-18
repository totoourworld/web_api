<?php

class PushModel extends CoreModel
{

    public $tableName='notifications';

    function sendUserPushNotification($message=DEFAULT_PUSH_MESSAGE, $pendingNotificationCounter=1, $userId=0, $is_noti=0)
    {
        $deviceToken='';
        $deviceType='ios';
        $sql="SELECT * FROM users WHERE user_id = {$userId}";
        $result=mysqli_query($this->con, $sql);
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            $deviceToken=$row['u_device_token'];
            $deviceType=$row['u_device_type'];
        }
        if ($userId)
        {
            switch ($deviceType)
            {
                case 'android':
                    $return=$this->androidPushNotification($deviceToken, $message);
                    return $return;
                    break;
                default:
                    //-----Put your device token here (without spaces):------
                    //$deviceToken = '0f45db4d3f472320de6940da1f12bd099d708246ce746325c4307aeae2150dff';
                    if (PUSH_LIVE_FLAG)
                    {
                        //-------Put your private key's passphrase here:------
                        $passphrase='';
                        $ctx=stream_context_create();
                        stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Taxi/push/Rider.pem');
                        //stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/push/vck.pem');
                        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
                        //stream_context_set_option($ctx, 'ssl', 'cafile', '/var/www/html/Taxi/push/entrust_2048_ca.cer');
                        $fp=stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    }
                    else
                    {
                        //-------Put your private key's passphrase here:------
                        $passphrase='';
                        $ctx=stream_context_create();
                        stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Taxi/push/Rider.pem');
                        //stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/push/vck.pem');
                        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
                        //stream_context_set_option($ctx, 'ssl', 'cafile', '/var/www/html/Taxi/push/entrust_2048_ca.cer');
                        $fp=stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    }
                    if ( ! $fp)
                    {
                        exit("Failed to connect amarnew: $err $errstr" . PHP_EOL);
                    }
                    //----------
                    if ($is_noti)
                    {

                        $pendingNotificationCounter=1;
                    }
                    else
                    {
                        $pendingNotificationCounter=1;
                    }
                    //---Create the payload body----
                    $is_noti=($is_noti) ? true : false;
                    $body['aps']=array('badge' => +$pendingNotificationCounter, 'alert' => $message, 'sound' => 'default', 'is_noti' => $is_noti);
                    //---echo $deviceToken;print_r($body); die;---
                    $payload=json_encode($body);
                    //----Build the binary notification----
                    $msg=chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
                    //---Send it to the server----
                    $result=fwrite($fp, $msg, strlen($msg));
                    //----var_dump($result); die;----
                    if ( ! $result)
                    {
                        //echo 'Message not delivered' . PHP_EOL;
                        return false;
                    }
                    else
                    {
                        return true;
                        //echo 'Message successfully delivered amar'.$message. PHP_EOL;
                    }
                    //---Close the connection to the server----
                    socket_close($fp);
                    fclose($fp);
                    break;
            }
        }
    }

    function sendDriverPushNotification($message=DEFAULT_PUSH_MESSAGE, $pendingNotificationCounter=1, $driverId=0, $is_noti=0)
    {
        $deviceToken='';
        $deviceType='ios';
        $sql="SELECT * FROM drivers WHERE driver_id = {$driverId}";
        $result=mysqli_query($this->con, $sql);
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            $deviceToken=$row['d_device_token'];
            $deviceType=$row['d_device_type'];
        }
        if ($userId)
        {
            switch ($deviceType)
            {
                case 'android':
                    $return=$this->androidPushNotification($deviceToken, $message);
                    return $return;
                    break;
                default:
                    //-----Put your device token here (without spaces):------
                    //$deviceToken = '0f45db4d3f472320de6940da1f12bd099d708246ce746325c4307aeae2150dff';
                    if (PUSH_LIVE_FLAG)
                    {
                        //-------Put your private key's passphrase here:------
                        $passphrase='';
                        $ctx=stream_context_create();
                        stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Taxi/push/Driver.pem');
                        //stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/push/vck.pem');
                        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
                        //stream_context_set_option($ctx, 'ssl', 'cafile', '/var/www/html/Taxi/push/entrust_2048_ca.cer');
                        $fp=stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    }
                    else
                    {
                        //-------Put your private key's passphrase here:------
                        $passphrase='12345';
                        $ctx=stream_context_create();
                        stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Taxi/push/Driver.pem');
                        //stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/push/vck.pem');
                        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
                        //stream_context_set_option($ctx, 'ssl', 'cafile', '/var/www/html/Taxi/push/entrust_2048_ca.cer');
                        $fp=stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    }
                    if ( ! $fp)
                    {
                        exit("Failed to connect amarnew: $err $errstr" . PHP_EOL);
                    }
                    //----------
                    if ($is_noti)
                    {
                        $pendingNotificationCounter=1;
                    }
                    else
                    {
                        $pendingNotificationCounter=1;
                    }
                    //---Create the payload body----
                    $is_noti=($is_noti) ? true : false;
                    $body['aps']=array('badge' => +$pendingNotificationCounter, 'alert' => $message, 'sound' => 'default', 'is_noti' => $is_noti);
                    //---echo $deviceToken;print_r($body); die;---
                    $payload=json_encode($body);
                    //----Build the binary notification----
                    $msg=chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
                    //---Send it to the server----
                    $result=fwrite($fp, $msg, strlen($msg));
                    //----var_dump($result); die;----
                    if ( ! $result)
                    {
                        //echo 'Message not delivered' . PHP_EOL;
                        return false;
                    }
                    else
                    {
                        return true;
                        //echo 'Message successfully delivered amar'.$message. PHP_EOL;
                    }
                    //---Close the connection to the server----
                    socket_close($fp);
                    fclose($fp);
                    break;
            }
        }
    }

    function androidPushNotification($deviceToken, $message)
    {
        $registrationIds=array($deviceToken);
        $msg=array("price" => $message);
        $fields=array('registration_ids' => $registrationIds, 'data' => $msg);
        $headers=array('Authorization: key=' . API_ACCESS_KEY, 'Content-Type: application/json');
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result=curl_exec($ch);
        if ($result === FALSE)
        {
            return false;
            die('Curl failed: ' . curl_error($ch));
        }
        else
        {
            return true;
        }
    }

}

?>