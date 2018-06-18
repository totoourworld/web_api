<?php

class PaymentModel extends CoreModel
{

    public $tableName='payments';

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
    public function GetPaymentDetail($data)
    {
        $sql="SELECT * FROM {$this->tableName} p WHERE (p.payment_id='{$data['payment_id']}')";
        $result=mysqli_query($this->con, $sql);
        if ($result->num_rows)
        {
            $tripModel=new TripModel();
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['Trip']=$tripModel->GetTripDetail(array('trip_id' => $row['trip_id']));
                $return=$row;
            }
            return $return;
        }
        else
        {
            return array();
        }
    }

    public function GetLimitedPaymentDetail($data)
    {
        $sql="SELECT * FROM {$this->tableName} p WHERE (p.payment_id='{$data['payment_id']}')";
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

    public function getPayments($tripId=null, $paymentId=null, $offset=0, $rowCount=1000)
    {
        $return=array();
        $return['last_offset']=null;
        $return['next_offset']=null;

        $sql="SELECT * FROM {$this->tableName} AS p ";
        $sql .= " WHERE 1=1";

        if ( ! is_null($tripId))
        {
            $sql .= " AND (p.trip_id='{$tripId}')";
        }
        if ( ! is_null($paymentId))
        {
            $sql .= " AND (p.payment_id='{$paymentId}')";
        }

        $num_row_result=mysqli_query($this->con, $sql);
        $num_row=$num_row_result->num_rows;
        $return['last_offset']=$offset;
        $return['next_offset']=($num_row > ($offset + $rowCount)) ? $offset + $rowCount : 0;
        //--------
        $sql .= " ORDER BY p.payment_id DESC LIMIT $offset, $rowCount ";
        //echo $sql;exit;
        $result=mysqli_query($this->con, $sql);
        $revResult=array();
        if ($result->num_rows)
        {
            $i=0;
            $tripModel=new TripModel();
            while ($row=mysqli_fetch_assoc($result))
            {
                $row['Trip']=$tripModel->GetTripDetail(array('trip_id' => $row['trip_id']));
                $return['result'][]=$row;
            }
        }
        return $return;
    }

}

?>
