<?php
class ApikeyModel extends CoreModel
{
    public $tableName  = 'apikeys';
    public function  __construct()
    {
        parent::__construct($this->tableName);
    }
    //-----
    public function rule()
    {
        return array();
    }
    //----------------------
}
?>
