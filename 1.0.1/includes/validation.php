<?php
Class Validate {
    
    public static function isEmail($request,$data)
    {
        if(!isset($request[$data]))
            return false;
        //email is not case sensitive make it lower case
        $email =  strtolower($request[$data]);
        if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email)) 
            return true;

        return false;
    }
}
?>
