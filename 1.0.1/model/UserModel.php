<?php

class UserModel extends CoreModel
{

    public $tableName='users';

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
    	
        $data['u_email']=strtolower($data['u_email']);
        $userImageProfile=', img.img_path AS u_profile_image_path';
        $sql="SELECT u.*" . $userImageProfile . " FROM {$this->tableName} AS u ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = u.image_id ";
        }
        if (isset($data))
        {
            $sql .= " WHERE (u.u_email='{$data['u_email']}' AND u.u_password='{$data['u_password']}')";
            $result=mysqli_query($this->con, $sql);
            if ($result->num_rows)
            {
                $row=mysqli_fetch_assoc($result);
                $return=$row;
                return $return;
                //print_r('hello'); exit;
            }
            else
            {

                return array();
            }
        }
        
    }

    public function GetFBLogin($data)
    {
        $data['u_fbid']=strtolower($data['u_fbid']);
        $userImageProfile=', img.img_path AS u_profile_image_path';
        $sql="SELECT u.*" . $userImageProfile . " FROM {$this->tableName} AS u ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = u.image_id ";
        }
        $sql .= " WHERE (u.u_fbid='{$data['u_fbid']}')";
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
    public function GetUserDetail($data)
    {
        $UserModel=new UserModel();
        $userImageProfile=', img.img_path AS u_profile_image_path';
        $sql="SELECT u.*" . $userImageProfile . " FROM {$this->tableName} AS u ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = u.image_id ";
        }
        $sql .= " WHERE (u.user_id='{$data['user_id']}')";
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

    public function getUsersId($key)
    {
        $result=$this->selectWhere(array('api_key' => $key));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            return $row['user_id'];
        }
        return "";
    }

    public function GetLimitedUserDetail($data)
    {
        $UserModel=new UserModel();
        $sql="SELECT u.api_key, u.user_id, u.u_name, u.u_fname, u.u_lname, u.u_phone, u.u_email, u.u_city, u.u_state, u.u_country, u.u_zip, u.u_latitude, u.u_longitude, u.u_created, u.u_modified FROM {$this->tableName} u WHERE (u.user_id='{$data['user_id']}')";
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
    public function getUserName($userId)
    {
        $result=$this->selectWhere(array('user_id' => $userId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            return $row['u_name'];
        }
        return "";
    }

    public function getMeUserDetail($userId)
    {
        $detail=array();
        $result=$this->selectWhere(array('user_id' => $userId));
        if ($result->num_rows)
        {
            $row=mysqli_fetch_assoc($result);
            $detail['u_fname']=$row['u_fname'];
            $detail['u_lname']=$row['u_lname'];
            return $detail;
        }
        return $detail;
    }

    public function getAllUserList($userId=null, $is_tag_list=true, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;
        $return['result']=array();
        $userImageProfile=', img.img_path AS u_profile_image_path';
        $sql="SELECT u.*" . $userImageProfile . " FROM {$this->tableName} AS u ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = u.image_id ";
        }
        $sql .= " WHERE 1=1";
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        $sql .= " ORDER BY u.user_id DESC LIMIT $offset, $rowCount ";
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

    public function getNearByUserList($userId=null, $latitude=null, $longitude=null, $miles=3500, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;
        $return['result']=array();
        $userImageProfile=', img.img_path AS u_profile_image_path';
        $sql="SELECT u.*" . $userImageProfile . " FROM {$this->tableName} AS u ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = u.image_id ";
        }
        $sql .= " WHERE 1=1";
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        $sql .= " ORDER BY u.user_id DESC LIMIT $offset, $rowCount ";

        $result=mysqli_query($this->con, $sql);
        if ($result->num_rows)
        {
            $i=0;
            while ($row=mysqli_fetch_assoc($result))
            {
                $seconds=strtotime(date('Y-m-d H:i:s')) - strtotime($row['u_last_loggedin']);
                if ($seconds <= 3600)
                {
                    if ( ! is_null($latitude) && ! is_null($longitude) && ! is_null($miles) && ! empty($row['u_lat']) && ! empty($row['u_lng']) && ! is_null($row['u_lat']) && ! is_null($row['u_lng']))
                    {
                        $distance=$this->distance($latitude, $longitude, $row['u_lat'], $row['u_lng']);

                        if ($distance <= $miles)
                        {
                            $row['distance']=$distance;
                            $return['result'][]=$row;
                        }
                    }
                }
            }
        }
        return $return;
    }

    public function getUserID($mobile=NULL, $email=NULL)
    {
        if ( ! is_null($mobile))
        {
            $to=0;
            $sql="SELECT user_id FROM users WHERE u_phone IN {$mobile}";
            $results=mysqli_query($this->con, $sql);
            ;
            if ($results->num_rows)
            {
                $i=0;
                while ($row=mysqli_fetch_assoc($results))
                {
                    $to=$row['user_id'];
                }
            }
            return $to;
        }
        if ( ! is_null($email))
        {
            $to=0;
            $sql="SELECT user_id FROM users WHERE u_email IN '{$email}'";
            $results=mysqli_query($this->con, $sql);
            ;
            if ($results->num_rows)
            {
                $i=0;
                while ($row=mysqli_fetch_assoc($results))
                {
                    $to=$row['user_id'];
                }
            }
            return $to;
        }
    }

    public function getUsers($userId=null, $name=null, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;
        $userImageProfile=', img.img_path AS u_profile_image_path';
        $sql="SELECT u.*" . $userImageProfile . " FROM {$this->tableName} AS u ";
        if (true)
        {
            $sql .= " LEFT OUTER JOIN images img ON img.image_id = u.image_id ";
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
                    $sql .= " AND u.u_name LIKE '%{$value}%'";
                    $searchWord .= '+' . $value . " ";
                }
            }
        }
        if ( ! is_null($userId))
        {
            $sql .= " AND (u.user_id='{$userId}')";
        }
        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        if ( ! is_null($name))
        {
            $sql .= " ORDER BY u.user_id DESC LIMIT $offset, $rowCount ";
        }
        else
        {
            $sql .= " ORDER BY u.user_id DESC LIMIT $offset, $rowCount ";
        }
        //echo $sql;exit;
        $result=mysqli_query($this->con, $sql);
        $revResult=array();
        if ($result->num_rows)
        {
            $i=0;
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['u_name']=utf8_encode($row['u_name']);
                if ( ! is_null($name))
                {
                    similar_text($name, $row['u_name'], $percent);
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
