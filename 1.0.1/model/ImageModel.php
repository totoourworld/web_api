<?php

class ImageModel extends CoreModel
{

    public $tableName='images';

    public function __construct()
    {
        parent::__construct($this->tableName);
    }

    //-----
    public function rule()
    {
        return array();
    }

    public function GetImages($imageId)
    {
        $sql="SELECT * FROM {$this->tableName} img";
        $sql .= " WHERE (img.image_id='{$imageId}')";
        $result=mysqli_query($this->con, $sql);
        ;
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

    public function GetImage($imageId)
    {
        $sql="SELECT * FROM {$this->tableName} img";
        $sql .= " WHERE (img.image_id='{$imageId}')";
        $result=mysqli_query($this->con, $sql);
        ;
        if ($result->num_rows)
        {
            while ($row=mysqli_fetch_assoc($result))
            {
                $return=$row['image_path'];
            }
            return $return;
        }
        else
        {
            return array();
        }
    }

    public function GetVideoImage($imageId)
    {
        $sql="SELECT * FROM {$this->tableName} img";
        $sql .= " WHERE (img.image_id='{$imageId}')";
        $result=mysqli_query($this->con, $sql);
        ;
        if ($result->num_rows)
        {
            while ($row=mysqli_fetch_assoc($result))
            {
                $return=$row['video_path'];
            }
            return $return;
        }
        else
        {
            return array();
        }
    }

}

?>
