<?php

/**
 * The Braintree class is a sample class.
 *
 * @category  Braintree
 * @license   http://localhost/vinay/kunafa/index.php
 * @version   0.01
 * @since     2017-05-05
 * @author    Hotel
 */
class BraintreeAPI
{

    
    /**
     * FUNCTION - Generate EphemeralKey.
     *
     * Create new Stripe Token by passing valid fields. HTTP 201 Created on
     * Success.
     *
     * 
     * 
     * @param string api_key
     *
     * @return  JSON
     *
     * @since   2017-05-06
     * @author  Kunafa
     *
     * @example  http://localhost/dms/kunafa/index.php/braintreeapi/stripeephemeralkey?
     */
    protected function poststripeEphemeralKey($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        /*
          if ((!isset($request_data['bkey']) || ($request_data['bkey'] == '')))
          {
          throw new RestException(400, 'BKEY Missing');
          }
          if ((!isset($request_data['rkey']) || ($request_data['rkey'] == '')))
          {
          throw new RestException(400, 'RKEY Missing');
          }
         */

        if (( ! isset($request_data['api_version']) || ($request_data['api_version'] == '')))
        {
            throw new RestException(400, 'Api version Missing');
        }

        if (( ! isset($request_data['user_id']) || ($request_data['user_id'] == '')))
        {
            throw new RestException(400, 'Customer Id Missing');
        }

        require_once(LIBRARY_DIR . 'stripe/Stripe.php');

        $params=array(
            "testmode"   => "on",
			"private_live_key" => "sk_live_6xZZRrXbWzcTOXW6kfdyFVt9",
			"public_live_key"  => "pk_live_xxxxxxxxxxxxxxxxxxxxx",
			"private_test_key" => "sk_test_4bIToPRYSxPABKwoQpeF4HZk",
			"public_test_key"  => "pk_test_xxxxxxxxxxxxxxxxxxxxx"
        );

        if ($params['testmode'] == "on")
        {
            Stripe::setApiKey($params['private_test_key']);
            //$pubkey = $params['public_test_key'];
        }
        else
        {
            Stripe::setApiKey($params['private_live_key']);
            //$pubkey = $params['public_live_key'];
        }
        $response=array();

        try
        {
            Stripe::setApiVersion($request_data['api_version']);
            $key=Stripe_EphemeralKey::create(array("customer" => $request_data['user_id']), array("stripe_version" => $request_data['api_version']));
            $response=$key;
        }
        catch (Exception $e)
        {
            $error=$e->getMessage();
            $response=$error;
        }
        $returnArr['status']='OK';
        $returnArr['code']=200;
        $returnArr['message']=null;
        $returnArr['response']=$response;
        return $returnArr;


        throw new RestException(500);
    }

    /**
     * FUNCTION - Generate Stripe Token.
     *
     * Create new Stripe Token by passing valid fields. HTTP 201 Created on
     * Success.
     *
     * 
     * 
     * @param string api_key
     *
     * @return  JSON
     *
     * @since   2017-05-06
     * @author  Kunafa
     *
     * @example  http://localhost/dms/kunafa/index.php/braintreeapi/stripetransactionsale?
     */
    protected function poststripeTransactionSale($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        
        if (( !isset($request_data['customer']) || ($request_data['customer'] == '')) && ( ! isset($request_data['source']) || ($request_data['source'] == '')))
        {
            throw new RestException(400, 'Strip Customer Id Missing OR Stripe token Missing');
        }
        
        if (( ! isset($request_data['stripeToken']) || ($request_data['stripeToken'] == '')))
        {
            //throw new RestException(400, 'Stripe token Missing');
        }

        if (( ! isset($request_data['amount']) || ($request_data['amount'] == '')))
        {
            throw new RestException(400, 'Amount Missing');
        }

        if (( ! isset($request_data['trip_id']) || ($request_data['trip_id'] == '')))
        {
            throw new RestException(400, 'Trip Id Missing');
        }
        
       

        if (( ! isset($request_data['user_id']) || ($request_data['user_id'] == '')))
        {
            throw new RestException(400, 'Customer Id Missing');
        }

        require_once(LIBRARY_DIR . 'stripe/Stripe.php');

        $params=array(
            "testmode"   => "on",
			"private_live_key" => "sk_live_6xZZRrXbWzcTOXW6kfdyFVt9",
			"public_live_key"  => "pk_live_xxxxxxxxxxxxxxxxxxxxx",
			"private_test_key" => "sk_test_4bIToPRYSxPABKwoQpeF4HZk",
			"public_test_key"  => "pk_test_xxxxxxxxxxxxxxxxxxxxx"
        );

        if ($params['testmode'] == "on")
        {
            Stripe::setApiKey($params['private_test_key']);
            //$pubkey = $params['public_test_key'];
        }
        else
        {
            Stripe::setApiKey($params['private_live_key']);
            //$pubkey = $params['public_live_key'];
        }
        $response=array();

        $amount_cents=str_replace(".", "", $request_data['amount']);  // Chargeble amount
        $invoiceid=$request_data['trip_id'];                      // Invoice ID
        $description="Invoice #" . $invoiceid . " - " . $invoiceid;
        try
        {
        	if (isset($request_data['customer']) && $request_data['customer'] != '')
        	{
            	$charge=Stripe_Charge::create(array(
                	"amount" => $amount_cents,
                	"currency" => "usd",
                	"customer" => $request_data['customer'],
                	"description" => json_encode($request_data)
                	
                	)
            	);
        	}
        	else if (isset($request_data['source']) && $request_data['source'] != '')
        	{
            	$charge=Stripe_Charge::create(array(
                	"amount" => $amount_cents,
                	"currency" => "usd",
                	"source" => $request_data['source'],
                	"description" => json_encode($request_data),
                	)
            	);
        	}
        	else
        	{
        		throw new RestException(400, 'Strip Customer Id Missing OR Stripe token Missing');
        	}
            

            if (isset($charge['card']['address_zip_check']) && $charge['card']['address_zip_check'] == "fail")
            {
                throw new RestException(400, 'zip_check_invalid');
            }
            else if (isset($charge['card']['address_line1_check']) && $charge['card']['address_line1_check']== "fail")
            {
                throw new RestException(400, 'address_check_invalid');
            }
            else if (isset($charge['card']['cvc_check']) &&  $charge['card']['cvc_check'] == "fail")
            {
                throw new RestException(400, 'cvc_check_invalid');
            }
            // Payment has succeeded, no exceptions were thrown or otherwise caught				
            if(isset($charge['id']) && !empty($charge['id']))
            {
                $tripId = $request_data['trip_id'];
                $tripModel=new TripModel();
                $tripModel->update(array('strip_sale_trans' => json_encode($charge)), "trip_id='{$tripId}'");
                $tripModel->update(array('strip_sale_trans_id' => $charge['id']), "trip_id='{$tripId}'");
            }
            $result="success";
            $response=array('result' => $charge, 'response' => 'success');
        }
        catch (Stripe_CardError $e)
        {
            $error=$e->getMessage();
            $result="declined";
            $response=array('result' => $error, 'response' => 'declined');
            if(isset($error) && !empty($error))
            {
                $tripId = $request_data['trip_id'];
                $tripModel=new TripModel();
                $tripModel->update(array('strip_sale_trans' => $error), "trip_id='{$tripId}'");
            }
        }
        catch (Stripe_InvalidRequestError $e)
        {
            $error=$e->getMessage();
            $result="declined";
            $response=array('result' => $error, 'response' => 'declined');
            if(isset($error) && !empty($error))
            {
                $tripId = $request_data['trip_id'];
                $tripModel=new TripModel();
                $tripModel->update(array('strip_sale_trans' => $error), "trip_id='{$tripId}'");
            }
        }
        catch (Stripe_AuthenticationError $e)
        {
            $error=$e->getMessage();
            $result="declined";
            $response=array('result' => $error, 'response' => 'declined');
            if(isset($error) && !empty($error))
            {
                $tripId = $request_data['trip_id'];
                $tripModel=new TripModel();
                $tripModel->update(array('strip_sale_trans' => $error), "trip_id='{$tripId}'");
            }
        }
        catch (Stripe_ApiConnectionError $e)
        {
            $error=$e->getMessage();
            $result="declined";
            $response=array('result' => $error, 'response' => 'declined');
            if(isset($error) && !empty($error))
            {
                $tripId = $request_data['trip_id'];
                $tripModel=new TripModel();
                $tripModel->update(array('strip_sale_trans' => $error), "trip_id='{$tripId}'");
            }
        }
        catch (Stripe_Error $e)
        {
            $error=$e->getMessage();
            $result="declined";
            $response=array('result' => $error, 'response' => 'declined');
            if(isset($error) && !empty($error))
            {
                $tripId = $request_data['trip_id'];
                $tripModel=new TripModel();
                $tripModel->update(array('strip_sale_trans' => $error), "trip_id='{$tripId}'");
            }
        }
        catch (Exception $e)
        {
            $error=$e->getMessage();
            if ($e->getMessage() == "zip_check_invalid")
            {
                $result="declined";
            }
            else if ($e->getMessage() == "address_check_invalid")
            {
                $result="declined";
            }
            else if ($e->getMessage() == "cvc_check_invalid")
            {
                $result="declined";
            }
            else
            {
                $result="declined";
            }
            $response=array('result' => $error, 'response' => 'declined');
            if(isset($error) && !empty($error))
            {
                $tripId = $request_data['trip_id'];
                $tripModel=new TripModel();
                $tripModel->update(array('strip_sale_trans' => $error), "trip_id='{$tripId}'");
            }
        }
        $returnArr['status']='OK';
        $returnArr['code']=200;
        $returnArr['message']=null;
        $returnArr['response']=$response;
        return $returnArr;


        throw new RestException(500);
    }

}

?>