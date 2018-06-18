<?php

require_once 'define.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class MysqlConnection
{

    public $con;
    public $dbname = SERVER_DB_NAME;

    public function __construct()
    {
        $this->con=mysqli_connect("xeniataxidb.cuxmw7hxy6ov.us-west-2.rds.amazonaws.com", "root", "mcsamuel201*",$this->dbname);
        if (mysqli_connect_errno($this->con))
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
    }

}

abstract class CoreModel extends MysqlConnection
{

    public $fieldName=array();
    public $tableName;
    public $attributs=null;
    public $Command;
    public $callback;
    public $error=null;

    public function __construct($tableName)
    {
        parent::__construct();
        $this->tableName=$tableName;
    }

    public function rule()
    {
        return array();
    }

    public function getColoumn()
    {
        $result=mysqli_query($this->con, "SHOW COLUMNS FROM $this->tableName");
        if ( ! $result)
        {
            die('Could not run query: ' . mysql_error());
        }
        if (mysqli_num_rows($result) > 0)
        {
            while ($row=mysqli_fetch_assoc($result))
            {
                $this->fieldName[]=$row['Field'];
            }
        }
        return $this->fieldName;
    }

    public function validate($userColumnArr)
    {
        if (is_array($userColumnArr))
        {
            $userColumnArr=array_keys($userColumnArr);
            $requiredValueArr=$this->rule();
            $diff=array_diff($requiredValueArr, $userColumnArr);

            if (empty($diff))
            {
                $columnArr=$this->getColoumn();
                foreach ($userColumnArr as $userColumn)
                {
                    if ( ! in_array($userColumn, $columnArr))
                    {
                        $this->error="{$userColumn} column is not exist in {$this->tableName} table.";
                        return FALSE;
                    }
                }
                return TRUE;
            }
            else
            {
                $this->error="Required " . implode(',', $diff) . " column is not exist. ";
                return FALSE;
            }
        }
        else
        {
            $this->error="Either user column array empty or table column array ";
            return false;
        }
    }

    public function getAll()
    {
        $sql="SELECT * FROM {$this->tableName}";
        $this->command=mysqli_query($this->con, $sql);
        $this->callback=mysqli_affected_rows($this->con);
        return $this->command;
    }

    public function selectWhere(array $data, $string=null)
    {
        $fields=array_keys($data);
        $values=array_values($data);
        $count=count($fields);
        $item="";
        for ($i=0; $i < $count; $i ++)
        {
            if ($count > 1 && $count - 1 != $i)
            {
                if (is_string($values[$i]))
                {
                    $item .= $fields[$i] . " = " . "'{$values[$i]}' AND ";
                }
                else
                {
                    $item .= $fields[$i] . " = " . "'{$values[$i]}' AND ";
                }
            }
            else
            {
                if (is_string($values[$i]))
                {
                    $item .= $fields[$i] . " = " . "'{$values[$i]}'";
                }
                else
                {
                    $item .= $fields[$i] . " = " . "'{$values[$i]}'";
                }
            }
        }
        if ( ! is_null($string))
        {
            $item .= $string;
        }
        $sql="SELECT * FROM {$this->tableName} WHERE {$item}";
        $this->command=mysqli_query($this->con, $sql);
        $this->callback=mysqli_affected_rows($this->con);
        return $this->command;
    }

    public function deleteAll($table)
    {
        $sql="TRUNCATE {$table}";
        $this->command=mysql_query($sql, $this->load);
        $this->callback=mysql_affected_rows();
    }

    public function deleteWhere($where)
    {
        $sql="DELETE  FROM {$this->tableName} WHERE {$where}";
        mysqli_query($this->con, $sql);
        return mysqli_affected_rows($this->con);
    }

    public function insert(Array $data)
    {
        if (isset($data['dbname']))
        {
            unset($data['dbname']);
        }
        $fields=implode(", ", array_keys($data));
        $values="'" . implode("','", array_values($data)) . "'";
        $sql="INSERT INTO `{$this->tableName}` ({$fields}) VALUES ({$values}) ";
        $this->command=mysqli_query($this->con, $sql);
        $this->callback=mysqli_insert_id($this->con);
        return $this->command;
    }

    public function update(Array $data, $where)
    {
        if (isset($data['dbname']))
        {
            unset($data['dbname']);
        }
        foreach ($data as $x => $y)
        {
            if ($y != "" || true)
            {
                $values[]="{$x} = '{$y}'";
            }
        }
        $final=implode(", ", $values);
        $sql=" UPDATE `{$this->tableName}` SET {$final} WHERE {$where} ";
        $this->command=mysqli_query($this->con, $sql);
        $this->callback=mysqli_affected_rows($this->con);
        return $this->callback;
    }

    function distance($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $theta=$longitude1 - $longitude2;
        $miles=(sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $miles=acos($miles);
        $miles=rad2deg($miles);
        $miles=$miles * 60 * 1.1515;
        return $miles;
    }

    function debugMail($requestData, $responseData, $subject)
    {
        $body='<pre>';
        $body .= json_encode($requestData);
        $body .= '<br/><br/>';
        $body .= json_encode($responseData);
        //$body .= print_r($responseData);
        require_once INCLUDEDIR . 'SendMail.php';
        $mailObj=new SendMail();
        $mailObj->SendingMail('totoourworld@gmail.com', $body, $subject, 'Debug Email');
    }

    function GetLatitudeLongitude($address)
    {
        $returnArr=array();
        $prepAddr=str_replace(' ', '+', $address);
        @$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false');
        @$output=json_decode($geocode);
        if (@$output->status == 'OK')
        {
            $returnArr['latitude']=$output->results[0]->geometry->location->lat;
            $returnArr['longitude']=$output->results[0]->geometry->location->lng;
        }
        return $returnArr;
    }

}

?>
