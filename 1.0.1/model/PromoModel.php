<?php

class PromoModel extends CoreModel
{

    public $tableName='promos';

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
    public function GetPromoDetail($data)
    {
        $UserModel=new UserModel();
        $sql="SELECT * FROM {$this->tableName} c WHERE (c.promo_id='{$data['promo_id']}')";
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

    public function GetLimitedPromoDetail($data)
    {
        $sql="SELECT c.promo_id, c.promo_code FROM {$this->tableName} c WHERE (c.promo_id='{$data['promo_id']}')";
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
    public function getPromoName($promoId)
    {
        $result=$this->selectWhere(array('promo_id' => $promoId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            return $row['promo_code'];
        }
        return "";
    }

    public function getMePromoDetail($promoId)
    {
        $detail=array();
        $result=$this->selectWhere(array('promo_id' => $promoId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            $detail['promo_code']=$row['promo_code'];
            return $detail;
        }
        return $detail;
    }

    public function getAllPromoList($promoId=null, $is_tag_list=true, $offset=0, $rowCount=1000)
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
        $sql .= " ORDER BY c.promo_id DESC LIMIT $offset, $rowCount ";
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

    public function getPromos($promoId=null, $name=null, $offset=0, $rowCount=1000)
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
                    $sql .= " AND c.promo_code LIKE '%{$value}%'";
                    $searchWord .= '+' . $value . " ";
                }
            }
        }
        if ( ! is_null($promoId))
        {
            $sql .= " AND (c.promo_id='{$promoId}')";
        }
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        if ( ! is_null($name))
        {
            $sql .= " ORDER BY c.promo_id DESC LIMIT $offset, $rowCount ";
        }
        else
        {
            $sql .= " ORDER BY c.promo_id DESC LIMIT $offset, $rowCount ";
        }
        //echo $sql;exit;
        $result=mysqli_query($this->con, $sql);
        $revResult=array();
        if ($result->num_rows)
        {
            $i=0;
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['promo_code']=utf8_encode($row['promo_code']);
                if ( ! is_null($name))
                {
                    similar_text($name, $row['promo_code'], $percent);
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

    public function getValidatePromos($promoId=null, $name=null, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;

        $sql="SELECT * FROM {$this->tableName} AS c ";
        $sql .= " WHERE 1=1 AND c.promo_status = 1 ";

        if ( ! is_null($name))
        {
            $name=trim($name);
            $sql .= " AND (c.promo_code='{$name}')";
        }
        if ( ! is_null($promoId))
        {
            $sql .= " AND (c.promo_id='{$promoId}')";
        }
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        if ( ! is_null($name))
        {
            $sql .= " ORDER BY c.promo_id DESC LIMIT $offset, $rowCount ";
        }
        else
        {
            $sql .= " ORDER BY c.promo_id DESC LIMIT $offset, $rowCount ";
        }
        //echo $sql;exit;
        $result=mysqli_query($this->con, $sql);
        $revResult=array();
        if ($result->num_rows)
        {
            $i=0;
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['promo_code']=utf8_encode($row['promo_code']);
                if ( ! is_null($name))
                {
                    similar_text($name, $row['promo_code'], $percent);
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
