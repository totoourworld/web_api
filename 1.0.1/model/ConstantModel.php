<?php

class ConstantModel extends CoreModel
{

    public $tableName='constants';

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
    public function getConstants($constantId=null, $key=null, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;

        $sql="SELECT * FROM {$this->tableName} AS c ";
        $sql .= " WHERE 1=1";

        if ( ! is_null($key))
        {
            $key=trim($key);
            $revName=explode(' ', $key);
            foreach ($revName as $key => $value)
            {
                if ( ! empty($value))
                {
                    $value=trim($value);
                    $sql .= " AND c.cat_name LIKE '%{$value}%'";
                    $searchWord .= '+' . $value . " ";
                }
            }
        }
        if ( ! is_null($constantId))
        {
            $sql .= " AND (c.constant_id='{$constantId}')";
        }
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        if ( ! is_null($key))
        {
            $sql .= " ORDER BY c.constant_id DESC LIMIT $offset, $rowCount ";
        }
        else
        {
            $sql .= " ORDER BY c.constant_id DESC LIMIT $offset, $rowCount ";
        }
        //echo $sql;exit;
        $result=mysqli_query($this->con, $sql);
        $revResult=array();
        if ($result->num_rows)
        {
            $i=0;
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['constant_key']=utf8_encode($row['constant_key']);
                if ( ! is_null($key))
                {
                    similar_text($key, $row['constant_key'], $percent);
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
