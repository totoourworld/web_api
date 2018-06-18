<?php

class CarModel extends CoreModel
{

    public $tableName='cars';

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
    public function GetCarDetail($data)
    {
        $UserModel=new UserModel();
        $sql="SELECT * FROM {$this->tableName} c WHERE (c.car_id='{$data['car_id']}')";
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

    public function GetLimitedCarDetail($data)
    {
        $sql="SELECT c.car_id, c.car_name, c.car_desc, c.car_reg_no, c.car_model, c.car_fare_per_km, c.car_fare_per_min FROM {$this->tableName} c WHERE (c.car_id='{$data['car_id']}')";
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
    public function getCarName($carId)
    {
        $result=$this->selectWhere(array('car_id' => $carId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            return $row['car_name'];
        }
        return "";
    }

    public function getMeCarDetail($carId)
    {
        $detail=array();
        $result=$this->selectWhere(array('car_id' => $carId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            $detail['car_name']=$row['car_name'];
            $detail['car_desc']=$row['car_desc'];
            $detail['car_reg_no']=$row['car_reg_no'];
            $detail['car_model']=$row['car_model'];
            return $detail;
        }
        return $detail;
    }

    public function getAllCarList($carId=null, $is_tag_list=true, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;
        $return['result']=array();
        if ($is_tag_list)
        {
            $sql="SELECT * FROM {$this->tableName} c ";
        }
        else
        {
            $sql="SELECT * FROM {$this->tableName} c ";
        }
        $sql .= " WHERE 1=1";
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        $sql .= " ORDER BY c.car_id DESC LIMIT $offset, $rowCount ";
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

    public function getCars($carId=null, $name=null, $categoryId=null, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;

        $sql="SELECT * FROM {$this->tableName} AS c ";
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
                    $sql .= " AND c.car_name LIKE '%{$value}%'";
                    $searchWord .= '+' . $value . " ";
                }
            }
        }
        if ( ! is_null($carId))
        {
            $sql .= " AND (c.car_id='{$carId}')";
        }
        if ( ! is_null($categoryId))
        {
            $sql .= " AND (c.category_id='{$categoryId}')";
        }
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        if ( ! is_null($name))
        {
            $sql .= " ORDER BY c.car_id DESC LIMIT $offset, $rowCount ";
        }
        else
        {
            $sql .= " ORDER BY c.car_id DESC LIMIT $offset, $rowCount ";
        }
        //echo $sql;exit;
        $result=mysqli_query($this->con, $sql);
        $revResult=array();
        if ($result->num_rows)
        {
            $i=0;
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['car_name']=utf8_encode($row['car_name']);
                if ( ! is_null($name))
                {
                    similar_text($name, $row['car_name'], $percent);
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
