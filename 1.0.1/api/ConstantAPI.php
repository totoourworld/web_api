<?php

/**
 * The ConstantAPI class is a sample class.
 *
 * @category  ConstantAPI
 * @license   http://localhost/vinay/taxi/index.php
 * @version   0.01
 * @since     2016-09-04
 * @author    Taxi
 */
require_once INCLUDES_DIR . 'SendMail.php';

class ConstantAPI
{

    public $restler;
    public $defaultMessageResponse=array();

    /**
     * FUNCTION - Get all constant with respect
     *
     * @param string $api_key The api_key string to set
     * @param integer $category_id The category_id integer to set
     * @param integer $cat_name The name string to set
     * @param integer $offset The offset integer to set
     * @param integer $limit The limit integer to set
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/constantapi/getconstant?
     */
    protected function postGetConstants($request_data)
    {
        unset($request_data['api_key']);
        $offset=isset($request_data['offset']) ? $request_data['offset'] : 0;
        $limit=isset($request_data['limit']) ? $request_data['limit'] : 1000;
        $constantId=isset($request_data['constant_id']) ? $request_data['constant_id'] : NULL;
        $key=isset($request_data['constant_key']) ? $request_data['constant_key'] : NULL;
        $constantModel=new ConstantModel();
        $response=$constantModel->getConstants($constantId, $key, $offset, $limit);
        if (( ! isset($response['result']) || ($response['result'] == '')))
        {
            throw new RestException(400, 'Invalid Constant.');
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
