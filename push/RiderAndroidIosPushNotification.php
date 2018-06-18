<?php
if(isset($_REQUEST['message']))
{
    $message = $_REQUEST['message'];
}
else
{
    $message = 'This is default message';
}
if(isset($_REQUEST['trip_id']))
{
    $tripID = $_REQUEST['trip_id'];

}
else
{
    $tripID = '0';
    
}
if(isset($_REQUEST['trip_status']))
{
    $status = $_REQUEST['trip_status'];
}
else
{
    $status = 'accept';
    
}
if(isset($_REQUEST['ios']))
{
    $deviceTokenIOS = $_REQUEST['ios'];
    $deviceTokenIOS = explode(',',$deviceTokenIOS);
    ios($deviceTokenIOS,$message,$message,$tripID,$status);
}
if(isset($_REQUEST['android']))
{
    $deviceTokenAndroid = $_REQUEST['android'];
    $deviceTokenAndroid = explode(',',$deviceTokenAndroid);
    android($deviceTokenAndroid,$message,$message,$tripID,$status);
}

function ios($deviceToken,$message,$message,$tripID,$status)
{
	if(!empty($deviceToken) && is_array($deviceToken))
    {
		$ctx = stream_context_create();


		stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/xeniaAPI/push/HiremeRider.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', '');

		$fp = stream_socket_client('ssl://gateway.push.apple.com:2195',
	    $err,
	    $errstr,
	    60,
	    STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT,
	    $ctx);

	    if (!$fp)
	        {
	            exit("Failed to connect amarnew: $err $errstr" . PHP_EOL);
	        }
	    	foreach ($deviceToken as $key => $token)
	        {
	            if(isset($token))
	                $token = $token;
	            else
	                    $token = 'a42a5cfdf87145be126ed4b044ffb83994c231008b943c9eab95cc6d9b1c7230';
	            if(isset($message))
	                    $message = $message;
	            else
	                    $message = 'This is default message';


				    $body['aps'] = array(
				    'badge' => +1,
				    'alert' => $message,
				    'sound' => 'default',
				    'trip_id' => $tripID,
                	'trip_status' => $status,
                	'content-available' => 1
				);
				//echo $deviceToken;print_r($body); die;
				$payload = json_encode($body);

				// Build the binary notification
				$msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;

				// Send it to the server
				$result = fwrite($fp, $msg, strlen($msg));
				//var_dump($result); die;
				if (!$result)
				    echo 'Message not delivered' . PHP_EOL;
				else
				    echo 'Message successfully delivered amar'.$message. PHP_EOL;
			}
				// Close the connection to the server
				fclose($fp);
	}
}

function android($deviceToken,$message,$message,$tripID,$status)
{
	if(!empty($deviceToken) && is_array($deviceToken))
    {
    	define('API_ACCESS_KEY', 'AIzaSyCWhWQWAD1cgzTcj9cO_I8mLb5AbA6ZexU');
        if(isset($deviceToken))
        {
            $deviceToken = $deviceToken;
        }
        else
		{
		    $deviceToken = 'e5vHKrrwH7A:APA91bE4UTSjBKMZCBr5pwmCPhCEmczdDbYVMMdfZ0SbfYZRCLO43QihdVYozuFOEJDs2HcJXOlBpcxjsesoAD61DR9Avo1WYpQw9lyFSUkEu9RYM5aTXLussdXbie6i634qhT4hZELa';
		}
		if(isset($_REQUEST['message']))
		{
		    $message = $_REQUEST['message'];
		}
		else
		{
		    $message = 'This is default message';
		}

    	$registrationIds = $deviceToken;
        //$registrationIds = array($deviceToken);
        $msg = array("price"=>$message,"trip_id"=>$tripID, "trip_status"=>$status);
        //print_r($msg);exit;
        $fields = array('registration_ids' => $registrationIds,'data' => $msg);
        $headers = array('Authorization: key=' . API_ACCESS_KEY,'Content-Type: application/json');
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, true );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        if($result === FALSE)
        {
            //echo 'Message not delivered';
           // die('Curl failed : ' . curl_error($ch));
        }
        else
        {
            //echo 'Message successfully delivered : '.$message;
            if(false)
            {
                echo "<pre>";
                print_r($result);
                echo "</pre>";
            }
        }

    }

}
