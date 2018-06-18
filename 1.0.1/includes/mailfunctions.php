<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class MailFunctions
{
    public static function WelcomeMail($mailToName)
    {
        $arr=array();
        $new_subj="Welcome Mail";
        $new_body = "Hello ".$mailToName." !!!</br> Welcome to Restaurant App!!! ";
        $arr['subject']=$new_subj;
        $arr['body']=$new_body;
        return $arr;
    }
}
?>
