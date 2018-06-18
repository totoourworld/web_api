<?php
class UserAuth implements iAuthenticate{

    const KEY = 'rEsTlEr2';
    static  $user;
    function __isAuthenticated()
    {
        if(!isset($_REQUEST['api_key']))
            return false;
        $DoctoModel = new DoctorModel();
        $result = $DoctoModel->selectWhere(array('api_key'=>$_REQUEST['api_key']));
        if($result->num_rows)
        {
            self::$user = mysqli_fetch_assoc( $result);
            return TRUE;
        }
        else
            return FALSE;
        //return isset($_GET['key']) && $_GET['key'] == UserAuth::KEY ? TRUE : FALSE;
    }
    function key()
    {
        return UserAuth::KEY;
    }
    static  function user()
    {
        return UserAuth::$user;
    }
}