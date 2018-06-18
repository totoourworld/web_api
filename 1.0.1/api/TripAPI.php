<?php

/**
 * The TripAPI class is a sample class.
 *
 * @category  TripAPI
 * @license   http://localhost/vinay/taxi/index.php
 * @version   0.01
 * @since     2016-09-04
 * @author    Taxi
 */
require_once INCLUDES_DIR . 'SendMail.php';

class TripAPI
{

    public $restler;
    public $defaultMessageResponse=array();

    /**
     * FUNCTION - Create new Trip.
     *
     * Create new Trip by passing valid fields. HTTP 201 Created on
     * Success.
     *
     * @param string trip_date
     * @param string driver_id
     * @param string user_id
     * @param string trip_from_loc
     * @param string trip_to_loc
     * @param string trip_fare
     * @param string trip_wait_time
     * @param string trip_pickup_time
     * @param string trip_drop_time
     * @param string trip_reason
     * @param string trip_validity
     * @param string trip_feedback  
     * @param string trip_status
     * @param string trip_rating
     * @param string trip_created
     * @param string trip_modified  
     *
     * @return  JSON
     *
     * @since   2016-09-04
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/tripapi/save?
     */
    protected function postSave($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        unset($request_data['api_key']);
        $tripModel=new TripModel();
        if (false == $tripModel->validate($request_data))
        {
            throw new RestException(400, $tripModel->error);
        }
        // if((!isset($request_data['driver_id']) || ($request_data['driver_id'] =='')))
        // {
        //     throw new RestException(400, 'Driver Id Missing');
        // }
        if (( ! isset($request_data['user_id']) || ($request_data['user_id'] == '')))
        {
            throw new RestException(400, 'User Id Missing');
        }
        if (( ! isset($request_data['trip_date']) || ($request_data['trip_date'] == '')))
        {
            throw new RestException(400, 'Trip date Missing');
        }
        if (isset($request_data['trip_id']))
        {
            $tripID=$request_data['trip_id'];
            $request_data['trip_modified']=date('Y-m-d H:i:s');
            if ( ! empty($request_data))
            {
                $result=$tripModel->update($request_data, "trip_id='{$tripID}'");
                if ($result)
                {
                    $result=$tripModel->GetTripDetail(array('trip_id' => $tripID));
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
            $request_data['trip_created']=date('Y-m-d H:i:s');
            $request_data['trip_modified']=date('Y-m-d H:i:s');
            
            //$totalpayment = $request_data['trip_pay_amount'];
            //$promoamount = $request_data['trip_promo_amt'];
            //$request_data['trip_amount_after_promo_applied'] = $totalpayment - $promoamount;
            $result=$tripModel->insert($request_data);
            if ($result)
            {
                //------
                if (false)
                {
                    $userModel=new UserModel();
                    //$userModel->update(array('u_is_available'=>0),"user_id='{$request_data['user_id']}'");
                    $driverModel=new DriverModel();
                    //$driverModel->update(array('d_is_available'=>0),"driver_id='{$request_data['driver_id']}'");
                }
                //------
                $result=$tripModel->GetTripDetail(array('trip_id' => $tripModel->callback));
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
     * @param string trip_date
     * @param string driver_id
     * @param string user_id
     * @param string trip_from_loc
     * @param string trip_to_loc
     * @param string trip_fare
     * @param string trip_wait_time
     * @param string trip_pickup_time
     * @param string trip_drop_time
     * @param string trip_reason
     * @param string trip_validity
     * @param string trip_feedback  
     * @param string trip_status
     * @param string trip_rating
     * @param string trip_created
     * @param string trip_modified  
     * @param string is_send_email
     *
     * @return  JSON
     *
     * @since   2016-09-05
     * @author  taxi
     *
     * @example http://aploads.com/development/taxi/index.php/tripapi/updatetrip?
     */
    protected function postUpdateTrip($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        unset($request_data['api_key']);
        $isSendEmail=1;
        $tripModel=new TripModel();
        if (isset($request_data['is_send_email']))
        {
            $isSendEmail=0;
            unset($request_data['is_send_email']);
        }
        if (false == $tripModel->validate($request_data))
        {
            throw new RestException(400, $tripModel->error);
        }
        if (( ! isset($request_data['trip_id']) || ($request_data['trip_id'] == '')))
        {
            throw new RestException(400, 'Trip Id Missing');
        }
        $request_data['trip_modified']=date('Y-m-d H:i:s');
        unset($request_data['api_key']);
        $tripID=$request_data['trip_id'];
        //--------
        if ( ! empty($request_data))
        {
        	
            $result=$tripModel->update($request_data, "trip_id='{$tripID}'");
            if ($result)
            {
                if (false)
                {
                    $result=$tripModel->GetTripDetail(array('trip_id' => $tripID));
                    //------
                    if (isset($result['user_id']) && isset($result['driver_id']))
                    {
                        $userModel=new UserModel();
                        $userModel->update(array('u_is_available' => 0), "user_id='{$result['user_id']}'");
                        $driverModel=new DriverModel();
                        $driverModel->update(array('d_is_available' => 0), "driver_id='{$result['driver_id']}'");
                    }
                    //------
                }

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
     * FUNCTION - Get all categories with respect
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
     * @example http://aploads.com/development/taxi/index.php/tripapi/gettrips?
     */
    protected function postGetTrips($request_data)
    {
        unset($request_data['api_key']);
        $offset=isset($request_data['offset']) ? $request_data['offset'] : 0;
        $limit=isset($request_data['limit']) ? $request_data['limit'] : 1000;
        $driverId=isset($request_data['driver_id']) ? $request_data['driver_id'] : NULL;
        $userId=isset($request_data['user_id']) ? $request_data['user_id'] : NULL;
        $tripId=isset($request_data['trip_id']) ? $request_data['trip_id'] : NULL;
        $IsRequest=isset($request_data['is_request']) ? $request_data['is_request'] : 1;
        $tripStatus=isset($request_data['trip_status']) ? $request_data['trip_status'] : NULL;
        $tripModel=new TripModel();
        $response=$tripModel->getTrips($tripId, $userId, $driverId,$IsRequest, $offset, $limit,$tripStatus);
        //-------
        $returnArr['status']='OK';
        $returnArr['code']=200;
        $returnArr['message']=null;
        $returnArr['next_offset']=$response['next_offset'];
        $returnArr['last_offset']=$response['last_offset'];
        $returnArr['response']=$response['result'];
        return $returnArr;
    }
    
    /**
     * FUNCTION - Trip Accepted.
     *
     * Create new Trip by passing valid fields. HTTP 201 Created on
     * Success.
     *
     * @param string trip_date
     * @param string driver_id
     * @param string user_id
     * @param string trip_from_loc
     * @param string trip_to_loc
     * @param string trip_fare
     * @param string trip_wait_time
     * @param string trip_pickup_time
     * @param string trip_drop_time
     * @param string trip_reason
     * @param string trip_validity
     * @param string trip_feedback  
     * @param string trip_status
     * @param string trip_rating
     * @param string trip_created
     * @param string trip_modified  
     *
     * @return  JSON
     *
     * @since   2016-09-04
     * @author  Taxi
     *
     * @example http://aploads.com/development/taxi/index.php/tripapi/save?
     */
    protected function postTripAccept($request_data)
    {
        if (empty($request_data))
        {
            throw new RestException(400);
        }
        unset($request_data['api_key']);
        $tripModel=new TripModel();
        if (false == $tripModel->validate($request_data))
        {
            throw new RestException(400, $tripModel->error);
        }
        if (( ! isset($request_data['trip_id']) || ($request_data['trip_id'] == '')))
        {
            throw new RestException(400, 'Trip Id Missing');
        }
        if (( ! isset($request_data['trip_status']) || ($request_data['trip_status'] == '')))
        {
            throw new RestException(400, 'Trip status Missing');
        }
        $tripid=$request_data['trip_id'];
        $query=" AND trip_id = $tripid";
        if (isset($request_data['trip_status']))
        {
            $result=$tripModel->selectWhere(array('trip_status' => $request_data['trip_status']), $query);
            if ($result->num_rows)
            {
                throw new RestException(400, 'Too late! Trip assign to another driver');
            }
        }

        if (isset($request_data['trip_id']))
        {
            $tripID=$request_data['trip_id'];
            $request_data['trip_modified']=date('Y-m-d H:i:s');
            if ( ! empty($request_data))
            {
                $result=$tripModel->update($request_data, "trip_id='{$tripID}'");
                $userid=$request_data['user_id'];
                //echo $userid; exit;
                $userModel=new UserModel();
                $userModel->update(array('u_is_available' => 0), "user_id='{$userid}'");
                $driverModel=new DriverModel();
                $driverModel->update(array('d_is_available' => 0), "driver_id='{$request_data['driver_id']}'");
                if ($result)
                {
                    $result=$tripModel->GetTripDetail(array('trip_id' => $tripID));
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
            $request_data['trip_created']=date('Y-m-d H:i:s');
            $request_data['trip_modified']=date('Y-m-d H:i:s');
            $result=$tripModel->insert($request_data);
            if ($result)
            {
                //------
                if (false)
                {
                    $userModel=new UserModel();
                    $userModel->update(array('u_is_available' => 0), "user_id='{$request_data['user_id']}'");
                    $driverModel=new DriverModel();
                    $driverModel->update(array('d_is_available' => 0), "driver_id='{$request_data['driver_id']}'");
                }
                //------
                $result=$tripModel->GetTripDetail(array('trip_id' => $tripModel->callback));
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
