<?php
/**
 * The PromoAPI class is a sample class.
 *
 * @promo  PromoAPI
 * @license   http://localhost/vinay/taxi/index.php
 * @version   0.01
 * @since     2016-09-04
 * @author    Taxi
*/
require_once INCLUDES_DIR.'SendMail.php';
class PromoAPI 
{
    public $restler;
    public $defaultMessageResponse = array();
    /**
	* FUNCTION - Create new Promo.
	*
	* Create new User by passing valid fields. HTTP 201 Created on
        * Success.
	*
	* @param string cat_name
	* @param string cat_desc
	* @param string cat_status
	* @param string car_currency
	* @param string cat_created
	* @param string cat_modified
	*
	* @return  JSON
	*
	* @since   2016-09-04
	* @author  Taxi
	*
	* @example http://aploads.com/development/taxi/index.php/promoapi/save?
    */
    public function postSave($request_data)
    {
        if (empty ($request_data))
        {
            throw new RestException(400);
        }
        $promoModel = new PromoModel();
        if (false == $promoModel->validate($request_data))
        {
            throw new RestException(400, $promoModel->error);
        }
        if((!isset($request_data['promo_type']) || ($request_data['promo_type'] =='')))
        {
            throw new RestException(400, 'please enter promo type');
        }
        if((!isset($request_data['promo_value']) || ($request_data['promo_value'] =='')))
        {
            throw new RestException(400, 'please enter promo value');
        }
        if((!isset($request_data['promo_code']) || ($request_data['promo_code'] =='')))
        {
            throw new RestException(400, 'please enter promo code');
        }
        if(isset($request_data['promo_code']))
	{
            $result = $promoModel->selectWhere(array('promo_code'=>$request_data['promo_code']));
            if(!$result->num_rows)
            {
                throw new RestException(400, 'Promo Code Already Exists');
            }
	}
        //----------
        $request_data['promo_created'] = date('Y-m-d H:i:s');
	$request_data['promo_modified'] = date('Y-m-d H:i:s');
        $result = $promoModel->insert($request_data);
        if($result)
        {
            $result = $promoModel->GetPromoDetail(array('car_id'=>$promoModel->callback));
            $returnArr['status']='OK';
            $returnArr['code']=200;
            $returnArr['message']=null;
            $returnArr['response']=$result;
            return $returnArr;
        }
        throw new RestException(500);
    }
    /**
    * FUNCTION - Update promo profile.
    *
    * @param string cat_name
    * @param string cat_desc
    * @param string cat_status
    * @param string car_currency
    * @param string cat_created
    * @param string cat_modified
    * @param string is_send_email
    *
    * @return  JSON
    *
    * @since   2016-09-05
    * @author  taxi
    *
    * @example http://aploads.com/development/taxi/index.php/promoapi/updatepromoprofile?
    */
    protected function postUpdatePromoProfile($request_data)
    {
        if(empty($request_data))
	{
            throw new RestException(400);
	}
	$isSendEmail = 1;
        $promoModel = new PromoModel();
	if(isset($request_data['is_send_email']))
        {
            $isSendEmail = 0;
            unset($request_data['is_send_email']);
	}
        if (false == $promoModel->validate($request_data))
        {
            throw new RestException(400, $promoModel->error);
        }
        if((!isset($request_data['promo_id']) || ($request_data['promo_id'] =='')))
        {
            throw new RestException(400, 'Promo Id Missing');
        }
        if(isset($request_data['promo_code']))
	{
            if(($request_data['promo_value'] ==''))
            {
                throw new RestException(400, 'please enter promo value');
            }
            $result = $promoModel->selectWhere(array('promo_code'=>$request_data['promo_code']));
            if(!$result->num_rows)
            {
                throw new RestException(400, 'Promo Code Already Exists');
            }
	}
        if((isset($request_data['promo_type']) && ($request_data['promo_type'] =='')))
        {
            throw new RestException(400, 'please enter promo type');
        }
        if((isset($request_data['promo_value']) && ($request_data['promo_value'] =='')))
        {
            throw new RestException(400, 'please enter promo value');
        }
	//-------------------
	$promoID = $request_data['promo_id'];
	//--------------------
	$query = "AND promo_id !=$promoID";
        //----------
        $request_data['promo_modified'] = date('Y-m-d H:i:s');
        //-------
	unset($request_data['api_key']);
        //--------
        if(!empty($request_data))
        {
            $result = $promoModel->update($request_data,"promo_id='{$promoID}'");
            if($result)
            {
		$result = $promoModel->GetPromoDetail(array('promo_id'=>$promoID));
                $returnArr['status'] = 'OK';
                $returnArr['code'] = 200;
                $returnArr['message'] = null;
                $returnArr['response'] = $result;
                return $returnArr;
            }
            throw new RestException(400);
        }
	throw new RestException(500);
    }
    
    /**
    * FUNCTION - Get all promos with respect
    *
    * @param string $api_key The api_key string to set
    * @param integer $promo_id The promo_id integer to set
    * @param integer $promo_code The name string to set
    * @param integer $offset The offset integer to set
    * @param integer $limit The limit integer to set
    *
    * @return  JSON
    *
    * @since   2016-09-05
    * @author  Taxi
    *
    * @example http://aploads.com/development/taxi/index.php/promoapi/getpromos?
    */
    protected  function postGetPromos($request_data)
    {
        unset($request_data['api_key']);
	$offset = isset($request_data['offset'])? $request_data['offset'] : 0;
	$limit = isset($request_data['limit'])? $request_data['limit'] : 1000;
	$promoId = isset($request_data['promo_id'])?$request_data['promo_id']:NULL;
	$promoCode = isset($request_data['promo_code'])?$request_data['promo_code']:NULL;
	$promoModel = new PromoModel();
	$response = $promoModel->getPromos($promoId,$promoCode,$offset,$limit);
	if((!isset($response['result']) || ($response['result'] =='')))
        {
            throw new RestException(400, 'Invalid Promo.');
        }
	$returnArr['status'] = 'OK';
	$returnArr['code'] = 200;
	$returnArr['message'] = null;
	$returnArr['next_offset'] = $response['next_offset'];
	$returnArr['last_offset'] = $response['last_offset'];
	$returnArr['response'] = $response['result'];
	return $returnArr;
    }
    
    /**
    * FUNCTION - Get all promos with respect
    *
    * @param string $api_key The api_key string to set
    * @param integer $promo_id The promo_id integer to set
    * @param integer $promo_code The name string to set
    * @param integer $offset The offset integer to set
    * @param integer $limit The limit integer to set
    *
    * @return  JSON
    *
    * @since   2016-09-05
    * @author  Taxi
    *
    * @example http://aploads.com/development/taxi/index.php/promoapi/getcategories?
    */
    protected  function postValidatePromos($request_data)
    {
        unset($request_data['api_key']);
        if((!isset($request_data['promo_code']) || ($request_data['promo_code'] =='')))
        {
            throw new RestException(400, 'Promo code Missing');
        }
	$offset = isset($request_data['offset'])? $request_data['offset'] : 0;
	$limit = isset($request_data['limit'])? $request_data['limit'] : 1000;
	$promoId = isset($request_data['promo_id'])?$request_data['promo_id']:NULL;
	$promoCode = isset($request_data['promo_code'])?$request_data['promo_code']:NULL;
	$promoModel = new PromoModel();
	$response = $promoModel->getValidatePromos($promoId,$promoCode,$offset,$limit);
	if((!isset($response['result']) || ($response['result'] =='')))
        {
            throw new RestException(400, 'Invalid Promo.');
        }
	$returnArr['status'] = 'OK';
	$returnArr['code'] = 200;
	$returnArr['message'] = null;
	$returnArr['next_offset'] = $response['next_offset'];
	$returnArr['last_offset'] = $response['last_offset'];
	$returnArr['response'] = $response['result'];
	return $returnArr;
    }
    
    function jschars($str)
    {
        $str = mb_ereg_replace("\\\\", "\\\\", $str);
	$str = mb_ereg_replace("\"", "\\\"", $str);
	//$str = mb_ereg_replace("'", "\\'", $str);
	$str = mb_ereg_replace("\r\n", "\\n", $str);
	$str = mb_ereg_replace("\r", "\\n", $str);
	$str = mb_ereg_replace("\n", "\\n", $str);
	$str = mb_ereg_replace("\t", "\\t", $str);
	$str = mb_ereg_replace("<", "\\x3C", $str); // for inclusion in HTML
	$str = mb_ereg_replace(">", "\\x3E", $str);
	return $str;
    }
}

?>
