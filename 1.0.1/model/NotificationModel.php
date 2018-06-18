<?php

class NotificationModel extends CoreModel
{

    public $tableName='notifications';

    //------
    public function __construct()
    {
        parent::__construct($this->tableName);
    }

    //------
    public function rule()
    {
        return array('type');
    }

    //------
}

?>
