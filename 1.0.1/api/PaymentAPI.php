<?php

/**
 * The PaymentAPI class is a sample class.
 *
 * @category  TripAPI
 * @license   http://localhost/vinay/taxi/index.php
 * @version   0.01
 * @since     2016-09-04
 * @author    Taxi
 */
require_once INCLUDES_DIR . 'SendMail.php';

class PaymentAPI
{

    public $restler;
    public $defaultMessageResponse=array();

    /**
     * FUNCTION - Create new Trip.
     *
     * Create new Trip by passing valid fields. HTTP 201 Created on
     * Success.
     *
     * @param string payment_id
     * @param string trip_id
     * @param string pay_date
     * @param string pay_mode
     * @param string pay_amount
     * @param string pay_status
     * @param string pay_created
     * @param string pay_modified 
     *
     * @return  JSON
     *
     * @since   2016-09-04
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/paymentapi/save?
     */
    protected function postSave($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        unset($request_data['api_key']);
        $paymentModel=new PaymentModel();
        if (false == $paymentModel->validate($request_data))
        {
            throw new RestException(400, $paymentModel->error);
        }
        if (( ! isset($request_data['trip_id'])))
        {
            throw new RestException(400, 'Trip Id Missing');
        }
        $request_data['pay_trans_id']=md5($request_data['trip_id']);
        if (isset($request_data['payment_id']))
        {
            $paymentID=$request_data['payment_id'];
            $request_data['pay_modified']=date('Y-m-d H:i:s');
            if ( ! empty($request_data))
            {
                $result=$paymentModel->update($request_data, "payment_id='{$paymentID}'");
                if ($result)
                {
                    $result=$paymentModel->GetPaymentDetail(array('payment_id' => $paymentID));
                    $returnArr['status']='OK';
                    $returnArr['code']=200;
                    $returnArr['message']=null;
                    $returnArr['response']=$result;
                    return $returnArr;
                }
                throw new RestException(400);
            }
        }
        else
        {
            $request_data['pay_created']=date('Y-m-d H:i:s');
            $request_data['pay_modified']=date('Y-m-d H:i:s');
            $result=$paymentModel->insert($request_data);
            if ($result)
            {
                $result=$paymentModel->GetPaymentDetail(array('payment_id' => $paymentModel->callback));
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
     * FUNCTION - Update category profile.
     *
     * @param string payment_id
     * @param string trip_id
     * @param string pay_date
     * @param string pay_mode
     * @param string pay_amount
     * @param string pay_status
     * @param string pay_created
     * @param string pay_modified 
     * @param string is_send_email
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  taxi
     *
     * @example http://aploads.com/development/taxi/index.php/paymentapi/updatepayment?
     */
    protected function postUpdatePayment($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        unset($request_data['api_key']);
        $isSendEmail=1;
        $paymentModel=new PaymentModel();
        if (isset($request_data['is_send_email']))
        {
            $isSendEmail=0;
            unset($request_data['is_send_email']);
        }
        if (false == $tripModel->validate($request_data))
        {
            throw new RestException(400, $paymentModel->error);
        }
        if (( ! isset($request_data['payment_id']) || ($request_data['payment_id'] == '')))
        {
            throw new RestException(400, 'Payment Id Missing');
        }
        if (( ! isset($request_data['trip_id']) || ($request_data['trip_id'] == '')))
        {
            throw new RestException(400, 'Trip Id Missing');
        }
        $request_data['pay_modified']=date('Y-m-d H:i:s');
        unset($request_data['api_key']);
        $PaymentID=$request_data['payment_id'];
        $request_data['pay_trans_id']=md5($request_data['trip_id']);
        //--------
        if ( ! empty($request_data))
        {
            $result=$paymentModel->update($request_data, "payment_id='{$PaymentID}'");
            if ($result)
            {
                $result=$paymentModel->GetPaymentDetail(array('payment_id' => $PaymentID));
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
     * FUNCTION - Get all payments with respect
     *
     * @param string $api_key The api_key string to set
     * @param integer $driver_id The driver_id integer to set
     * @param integer $user_id The user_id string to set
     * @param integer $trip_id The trip_id string to set
     * @param integer $offset The offset integer to set
     * @param integer $limit The limit integer to set
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  Taxi
     *
     * @example http://35.160.185.249/Taxi/index.php/paymentapi/getpayments?
     */
    protected function postGetPayments($request_data)
    {
        unset($request_data['api_key']);
        $offset=isset($request_data['offset']) ? $request_data['offset'] : 0;
        $limit=isset($request_data['limit']) ? $request_data['limit'] : 1000;
        $paymentId=isset($request_data['payment_id']) ? $request_data['paymentr_id'] : NULL;
        $tripId=isset($request_data['trip_id']) ? $request_data['trip_id'] : NULL;
        $paymentModel=new PaymentModel();
        $response=$paymentModel->getPayments($tripId, $paymentId, $offset, $limit);
        //-------
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
