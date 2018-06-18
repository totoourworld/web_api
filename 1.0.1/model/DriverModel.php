<?php

class DriverModel extends CoreModel
{

    public $tableName='drivers';

    public function __construct()
    {
        parent::__construct($this->tableName);
    }

    //-----
    public function rule()
    {
        return array();
    }

    public function GetLogin($data)
    {
        $data['d_email']=strtolower($data['d_email']);
        $driverImageProfile=', img.img_path AS d_profile_image_path';
        $driverImageLicense=', imgL.img_path AS d_license_image_path';
        $driverImageInsurance=', imgIs.img_path AS d_insurance_image_path';
        $driverImageRC=', imgR.img_path AS d_rc_image_path';
        $driverCar=', c.*';
        //$driverTrip = ', t.*';
        $sql="SELECT d.*" . $driverImageProfile . $driverImageLicense . $driverImageInsurance . $driverImageRC . $driverCar . ", d.driver_id AS driver_id" . " FROM {$this->tableName} AS d ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = d.image_id ";
            $sql .= " LEFT OUTER JOIN images imgL ON imgL.image_id = d.d_license_id ";
            $sql .= " LEFT OUTER JOIN images imgIs ON imgIs.image_id = d.d_insurance_id ";
            $sql .= " LEFT OUTER JOIN images imgR ON imgR.image_id = d.d_rc ";
            $sql .= " LEFT OUTER JOIN cars c ON c.car_id = d.car_id ";
            //$sql .= " LEFT OUTER JOIN trips t ON t.driver_id = d.driver_id ";
        }
        $sql .= " WHERE (d.d_email='{$data['d_email']}' AND d.d_password='{$data['d_password']}')";
        $result=mysqli_query($this->con, $sql);
        ;
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            $return=$row;
            return $return;
        }
        else
        {
            return array();
        }
    }

    //----------------------
    public function GetDriverDetail($data)
    {
        $driverImageProfile=', img.img_path AS d_profile_image_path';
        $driverImageLicense=', imgL.img_path AS d_license_image_path';
        $driverImageInsurance=', imgIs.img_path AS d_insurance_image_path';
        $driverImageRC=', imgR.img_path AS d_rc_image_path';
        $driverCar=', c.*';
        //$driverTrip = ', t.*';
        $sql="SELECT d.*" . $driverImageProfile . $driverImageLicense . $driverImageInsurance . $driverImageRC . $driverCar . ", d.driver_id AS driver_id" . " FROM {$this->tableName} AS d ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = d.image_id ";
            $sql .= " LEFT OUTER JOIN images imgL ON imgL.image_id = d.d_license_id ";
            $sql .= " LEFT OUTER JOIN images imgIs ON imgIs.image_id = d.d_insurance_id ";
            $sql .= " LEFT OUTER JOIN images imgR ON imgR.image_id = d.d_rc ";
            $sql .= " LEFT OUTER JOIN cars c ON c.car_id = d.car_id ";
            //$sql .= " LEFT OUTER JOIN trips t ON t.driver_id = d.driver_id ";
        }
        $sql .= " WHERE (d.driver_id='{$data['driver_id']}')";
        $result=mysqli_query($this->con, $sql);
        if ($result->num_rows)
        {
            while ($row=mysqli_fetch_assoc($result))
            {
                $return=$row;
            }
            return $return;
        }
        else
        {
            return array();
        }
    }

    public function GetDriverDetailWithoutTrips($data)
    {
        $driverImageProfile=', img.img_path AS d_profile_image_path';
        $driverImageLicense=', imgL.img_path AS d_license_image_path';
        $driverImageInsurance=', imgIs.img_path AS d_insurance_image_path';
        $driverImageRC=', imgR.img_path AS d_rc_image_path';
        $driverCar=', c.*';
        $sql="SELECT d.*" . $driverImageProfile . $driverImageLicense . $driverImageInsurance . $driverImageRC . $driverCar . ", d.driver_id AS driver_id" . " FROM {$this->tableName} AS d ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = d.image_id ";
            $sql .= " LEFT OUTER JOIN images imgL ON imgL.image_id = d.d_license_id ";
            $sql .= " LEFT OUTER JOIN images imgIs ON imgIs.image_id = d.d_insurance_id ";
            $sql .= " LEFT OUTER JOIN images imgR ON imgR.image_id = d.d_rc ";
            $sql .= " LEFT OUTER JOIN cars c ON c.car_id = d.car_id ";
        }
        $sql .= " WHERE (d.driver_id='{$data['driver_id']}')";
        $result=mysqli_query($this->con, $sql);
        if ($result->num_rows)
        {
            while ($row=mysqli_fetch_assoc($result))
            {
                $return=$row;
            }
            return $return;
        }
        else
        {
            return array();
        }
    }

    public function getDriversId($key)
    {
        $result=$this->selectWhere(array('api_key' => $key));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            return $row['driver_id'];
        }
        return "";
    }

    public function GetLimitedDriverDetail($data)
    {
        $sql="SELECT d.api_key, d.driver_id, d.d_name, d.d_fname, d.d_lname, d.d_phone, d.d_email, d.d_city, d.d_state, d.d_country, d.d_zip, d.d_latitude, d.d_longitude, d.d_created, d.d_modified FROM {$this->tableName} d WHERE (d.driver_id='{$data['driver_id']}')";
        $result=mysqli_query($this->con, $sql);
        if ($result->num_rows)
        {
            while ($row=mysqli_fetch_assoc($result))
            {
                $return=$row;
            }
            return $return;
        }
        else
        {
            return array();
        }
    }

    //------
    public function getDriverName($driverId)
    {
        $result=$this->selectWhere(array('driver_id' => $driverId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            return $row['d_name'];
        }
        return "";
    }

    public function getMeDriverDetail($driverId)
    {
        $detail=array();
        $result=$this->selectWhere(array('driver_id' => $driverId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            $detail['d_fname']=$row['d_fname'];
            $detail['d_lname']=$row['d_lname'];
            return $detail;
        }
        return $detail;
    }

    public function getAllDriverList($driverId=null, $is_tag_list=true, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;
        $return['result']=array();
        $driverImageProfile=', img.img_path AS d_profile_image_path';
        $driverImageLicense=', imgL.img_path AS d_license_image_path';
        $driverImageInsurance=', imgIs.img_path AS d_insurance_image_path';
        $driverImageRC=', imgR.img_path AS d_rc_image_path';
        $driverCar=', c.*';
        //$driverTrip = ', t.*';
        $sql="SELECT d.*" . $driverImageProfile . $driverImageLicense . $driverImageInsurance . $driverImageRC . $driverCar . ", d.driver_id AS driver_id" . " FROM {$this->tableName} AS d ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = d.image_id ";
            $sql .= " LEFT OUTER JOIN images imgL ON imgL.image_id = d.d_license_id ";
            $sql .= " LEFT OUTER JOIN images imgIs ON imgIs.image_id = d.d_insurance_id ";
            $sql .= " LEFT OUTER JOIN images imgR ON imgR.image_id = d.d_rc ";
            $sql .= " LEFT OUTER JOIN cars c ON c.car_id = d.car_id ";
            //$sql .= " LEFT OUTER JOIN trips t ON t.driver_id = d.driver_id ";
        }
        $sql .= " WHERE 1=1";
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        $sql .= " ORDER BY d.driver_id DESC LIMIT $offset, $rowCount ";
        $result=mysqli_query($this->con, $sql);
        if ($result->num_rows)
        {
            $i=0;
            while ($row=mysqli_fetch_assoc($result))
            {
                $return['result'][]=$row;
            }
        }
        return $return;
    }

    public function getNearByDriverList($driverId=null, $latitude=null, $longitude=null, $miles=null, $categoryId=null, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;
        $return['result']=array();

        $driverImageProfile=', img.img_path AS d_profile_image_path';
        $driverImageLicense=', imgL.img_path AS d_license_image_path';
        $driverImageInsurance=', imgIs.img_path AS d_insurance_image_path';
        $driverImageRC=', imgR.img_path AS d_rc_image_path';
        $driverCar=', c.*';
        //$driverTrip = ', t.*';
        $sql="SELECT d.*" . $driverImageProfile . $driverImageLicense . $driverImageInsurance . $driverImageRC . $driverCar . ", d.driver_id AS driver_id" . " FROM {$this->tableName} AS d ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = d.image_id ";
            $sql .= " LEFT OUTER JOIN images imgL ON imgL.image_id = d.d_license_id ";
            $sql .= " LEFT OUTER JOIN images imgIs ON imgIs.image_id = d.d_insurance_id ";
            $sql .= " LEFT OUTER JOIN images imgR ON imgR.image_id = d.d_rc ";
            $sql .= " LEFT OUTER JOIN cars c ON c.car_id = d.car_id ";
            //$sql .= " LEFT OUTER JOIN trips t ON t.driver_id = d.driver_id ";
        }
        $sql .= " WHERE 1=1 AND d.d_is_available='1' ";
        if ( ! is_null($categoryId))
        {
            $sql .= " AND (c.category_id='{$categoryId}')";
        }
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        $sql .= " ORDER BY d.driver_id DESC LIMIT $offset, $rowCount ";
        //echo $sql;exit;
        $result=mysqli_query($this->con, $sql);
        if ($result->num_rows)
        {
            $i=0;
            while ($row=mysqli_fetch_assoc($result))
            {
                if ( ! is_null($latitude) && ! is_null($longitude) && ! is_null($miles) && ! is_null($row['d_lat']) && ! is_null($row['d_lng']) && ! empty($row['d_lat']) && ! empty($row['d_lng']))
                {
                    $distance=$this->distance($latitude, $longitude, $row['d_lat'], $row['d_lng']);
                    if ($distance <= $miles)
                    {
                        $row['distance']=$distance;
                        $return['result'][]=$row;
                    }
                }
            }
        }
        return $return;
    }

    public function getDriverID($mobile=NULL, $email=NULL)
    {
        if ( ! is_null($mobile))
        {
            $to=0;
            $sql="SELECT driver_id FROM drivers WHERE d_phone IN {$mobile}";
            $results=mysqli_query($this->con, $sql);
            ;
            if ($results->num_rows)
            {
                $i=0;
                while ($row=mysqli_fetch_assoc($results))
                {
                    $to=$row['driver_id'];
                }
            }
            return $to;
        }
        if ( ! is_null($email))
        {
            $to=0;
            $sql="SELECT driver_id FROM drivers WHERE d_email IN '{$email}'";
            $results=mysqli_query($this->con, $sql);
            ;
            if ($results->num_rows)
            {
                $i=0;
                while ($row=mysqli_fetch_assoc($results))
                {
                    $to=$row['driver_id'];
                }
            }
            return $to;
        }
    }

    public function getDrivers($driverId=null, $name=null, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;
        $driverImageProfile=', img.img_path AS d_profile_image_path';
        $driverImageLicense=', imgL.img_path AS d_license_image_path';
        $driverImageInsurance=', imgIs.img_path AS d_insurance_image_path';
        $driverImageRC=', imgR.img_path AS d_rc_image_path';
        $driverCar=', c.*';
        //$driverTrip = ', t.*';
        $sql="SELECT d.*" .  $driverImageProfile . $driverImageLicense . $driverImageInsurance . $driverImageRC . $driverCar . ", d.driver_id AS driver_id" . " FROM {$this->tableName} AS d ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = d.image_id ";
            $sql .= " LEFT OUTER JOIN images imgL ON imgL.image_id = d.d_license_id ";
            $sql .= " LEFT OUTER JOIN images imgIs ON imgIs.image_id = d.d_insurance_id ";
            $sql .= " LEFT OUTER JOIN images imgR ON imgR.image_id = d.d_rc ";
            $sql .= " LEFT OUTER JOIN cars c ON c.car_id = d.car_id ";
            //$sql .= " LEFT OUTER JOIN trips t ON t.driver_id = d.driver_id ";
        }
        $sql .= " WHERE 1=1";

        if ( ! is_null($name))
        {
            $name=trim($name);
            $revName=explode(' ', $name);
            foreach ($revName as $key => $value)
            {
                if ( ! empty($value))
                {
                    $value=trim($value);
                    $sql .= " AND d.d_name LIKE '%{$value}%'";
                    $searchWord .= '+' . $value . " ";
                }
            }
        }
        if ( ! is_null($driverId))
        {
            $sql .= " AND (d.driver_id='{$driverId}')";
        }
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        if ( ! is_null($name))
        {
            $sql .= " ORDER BY d.driver_id DESC LIMIT $offset, $rowCount ";
        }
        else
        {
            $sql .= " ORDER BY d.driver_id DESC LIMIT $offset, $rowCount ";
        }
        //echo $sql;exit;
        $result=mysqli_query($this->con, $sql);
        $revResult=array();
        if ($result->num_rows)
        {
            $i=0;
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['d_name']=utf8_encode($row['d_name']);
                if ( ! is_null($name))
                {
                    similar_text($name, $row['d_name'], $percent);
                    $row['percent']=$percent;
                    $revResult[$percent][]=$row;
                }
                else
                {
                    $return['result'][]=$row;
                }
            }
        }
        if ( ! empty($revResult))
        {
            krsort($revResult);
            foreach ($revResult as $key => $value)
            {
                foreach ($value as $ikey => $ivalue)
                {
                    $return['result'][]=$ivalue;
                }
            }
        }
        return $return;
    }

}

?>
