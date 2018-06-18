<?php

class TripModel extends CoreModel
{

    public $tableName='trips';

    public function __construct()
    {
        parent::__construct($this->tableName);
    }

    //-----
    public function rule()
    {
        return array();
    }

    //----------------------
    public function GetTripDetail($data)
    {
        $sql="SELECT * FROM {$this->tableName} t WHERE (t.trip_id='{$data['trip_id']}')";
        $result=mysqli_query($this->con, $sql);
        if ($result->num_rows)
        {
            $driverModel=new DriverModel();
            $userModel=new UserModel();
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['Driver']=$driverModel->GetDriverDetailWithoutTrips(array('driver_id' => $row['driver_id']));
                $row['User']=$userModel->GetUserDetail(array('user_id' => $row['user_id']));
                $return=$row;
            }
            return $return;
        }
        else
        {
            return array();
        }
    }

    public function GetLimitedTripDetail($data)
    {
        $sql="SELECT t.trip_id, t.trip_date FROM {$this->tableName} t WHERE (t.trip_id='{$data['trip_id']}')";
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
    public function getTripDriverId($tripId)
    {
        $result=$this->selectWhere(array('trip_id' => $tripId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            return $row['driver_id'];
        }
        return "";
    }

    public function getTripUserId($tripId)
    {
        $detail=array();
        $result=$this->selectWhere(array('trip_id' => $tripId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            $detail['user_id']=$row['user_id'];
            return $detail;
        }
        return $detail;
    }

    public function getAllTripList($tripId=null, $is_tag_list=true, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;
        $return['result']=array();
        if ($is_tag_list)
        {
            $sql="SELECT * FROM {$this->tableName} t ";
        }
        else
        {
            $sql="SELECT * FROM {$this->tableName} t ";
        }
        $sql .= " WHERE 1=1";
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        $sql .= " ORDER BY t.trip_id DESC LIMIT $offset, $rowCount ";
        $result=mysqli_query($this->con, $sql);
        if ($result->num_rows)
        {
            $i=0;
            $driverModel=new DriverModel();
            $userModel=new UserModel();
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['Driver']=$driverModel->GetDriverDetailWithoutTrips(array('driver_id' => $row['driver_id']));
                $row['User']=$userModel->GetUserDetail(array('user_id' => $row['user_id']));
                $return['result'][]=$row;
            }
        }
        return $return;
    }

     public function getTrips($tripId=null, $userId=null, $driverId=null,$IsRequest=1, $offset=0, $rowCount=1000,$tripStatus=null)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;

        $sql="SELECT * FROM {$this->tableName} AS t ";
        $sql .= " WHERE 1=1 ";
		if ($IsRequest == 0)
        {
            $sql .= " AND trip_status NOT LIKE 'request' ";
        }
        if ( ! is_null($tripStatus))
        {
            $sql .= " AND t.trip_status LIKE '{$tripStatus}'";
        }
        else
        {
        	$sql .= " AND t.trip_status NOT LIKE 'expired'";
        }
        if ( ! is_null($tripId))
        {
            $sql .= " AND t.trip_id='{$tripId}'";
        }
        if ( ! is_null($userId))
        {
            $sql .= " AND t.user_id='{$userId}'";
        }
        if ( ! is_null($driverId))
        {
            $sql .= " AND t.driver_id='{$driverId}'";
        }
        
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        $sql .= " ORDER BY t.trip_id DESC LIMIT $offset, $rowCount ";
        //echo $sql;exit;
        $result=mysqli_query($this->con, $sql);
        $revResult=array();
        if ($result->num_rows)
        {
            $i=0;
            $driverModel=new DriverModel();
            $userModel=new UserModel();
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['Driver']=$driverModel->GetDriverDetailWithoutTrips(array('driver_id' => $row['driver_id']));
                $row['User']=$userModel->GetUserDetail(array('user_id' => $row['user_id']));
                $return['result'][]=$row;
            }
        }
        return $return;
    }

}

?>
