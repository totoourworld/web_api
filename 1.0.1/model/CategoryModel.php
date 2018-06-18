<?php

class CategoryModel extends CoreModel
{

    public $tableName='categories';

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
    public function GetCategoryDetail($data)
    {
        $UserModel=new UserModel();
        $sql="SELECT * FROM {$this->tableName} c WHERE (c.category_id='{$data['category_id']}')";
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

    public function GetLimitedCategoryDetail($data)
    {
        $sql="SELECT c.category_id, c.cat_name FROM {$this->tableName} c WHERE (c.category_id='{$data['category_id']}')";
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
    public function getCategoryName($categoryId)
    {
        $result=$this->selectWhere(array('category_id' => $categoryId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            return $row['cat_name'];
        }
        return "";
    }

    public function getMeCategoryDetail($categoryId)
    {
        $detail=array();
        $result=$this->selectWhere(array('category_id' => $categoryId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            $detail['cat_name']=$row['cat_name'];
            return $detail;
        }
        return $detail;
    }

    public function getAllCategoryList($categoryId=null, $is_tag_list=true, $offset=0, $rowCount=1000)
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
        $sql .= " ORDER BY c.category_id DESC LIMIT $offset, $rowCount ";
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

    public function getCategories($categoryId=null, $name=null, $offset=0, $rowCount=1000)
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
                    $sql .= " AND c.cat_name LIKE '%{$value}%'";
                    $searchWord .= '+' . $value . " ";
                }
            }
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
            $sql .= " ORDER BY c.category_id DESC LIMIT $offset, $rowCount ";
        }
        else
        {
            $sql .= " ORDER BY c.category_id DESC LIMIT $offset, $rowCount ";
        }
        //echo $sql;exit;
        $result=mysqli_query($this->con, $sql);
        $revResult=array();
        if ($result->num_rows)
        {
            $i=0;
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['cat_name']=utf8_encode($row['cat_name']);
                if ( ! is_null($name))
                {
                    similar_text($name, $row['cat_name'], $percent);
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
