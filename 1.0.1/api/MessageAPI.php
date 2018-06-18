<?php

/**
 * The MessageAPI class is a sample class.
 *
 * @category  TripAPI
 * @license   http://localhost/vinay/taxi/index.php
 * @version   0.01
 * @since     2016-09-04
 * @author    Taxi
 */

// Require the bundled autoload file - the path may need to change
// based on where you downloaded and unzipped the SDK

//echo "dsfdsf";exit;
// Use the REST API Client to make requests to the Twilio REST API
class MessageAPI
{

    public $restler;
    public $defaultMessageResponse=array();

    /**
     * FUNCTION - Sent Message.
     *
     * Create new Send Message by passing valid fields. HTTP 201 Created on
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
    public function postSentMessage($request_data)
    {
        if (empty($request_data))
        {
            //throw new RestException(400);
        }

        
        throw new RestException(500);
    }

    

}

?>
