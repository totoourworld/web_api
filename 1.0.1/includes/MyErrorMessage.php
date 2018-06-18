<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class MyErrorMessage
{
    public static function CheckFileSize($file_data,$data,$extArr=array(),$img_dms_arr=array())
    {

        $action_result['error'] = FALSE;
        $file_name = ucfirst($data);
        if($file_data[$data]['error']>0)
        {
            
            if($file_data[$data]["error"] == 4)
            {
                $action_result['error'] = TRUE;
                $action_result['msg'][] = ("Please Upload $file_name File");
            }   
            else
            {
                $action_result['error'] = TRUE;
                $action_result['msg'][] = ("Error In $file_name File:".$file_data[$data]["name"]."Return Code: " . $file_data[$data]["error"]);
            }    
        }
        if($file_data[$data]['name'] != '')
        {
            if(count($extArr)>0)   
            {    
                $certifi_ftype = end(explode('/', mime_content_type($file_data[$data]['tmp_name'])));
                if (!in_array($certifi_ftype, $extArr)) {
                    $action_result['error'] = TRUE;
                    $action_result['msg'][] = ("Invalid $file_name file extension :".$file_data[$data]["name"]);
                }  
            }
            if(count($img_dms_arr)>0)
            {    
                list($width, $height, $type, $attr) = getimagesize($file_data[$data]['tmp_name']);
                if($img_dms_arr['width'] != $width && $img_dms_arr['height'] !=$height)
                {   $action_result['error'] = TRUE;
                    $action_result['msg'][] = "$file_name file dimension invalid. width :$img_dms_arr[width] and height:  $img_dms_arr[height]";
                } 
            }
        }
        return $action_result;
        
    }
}
?>
