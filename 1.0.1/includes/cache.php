<?php
/*******************************************************
 Cache class
*******************************************************/
class Cache
{
	private static $instance;
	private $cache;
	
	//Constructor
	private function __construct()
	{     
		$frontendOptions = array('caching' => true,'lifetime' => 600,'automatic_serialization' => true);
		$backendOptions = array('servers' =>array(array('host' => 'trakode.com','port' => 11211)),
    					  'compression' => false);
		$this->cache = Zend_Cache::factory('Core', 'Memcached', $frontendOptions, $backendOptions);

		if(!$this->cache->load('MailSettings'))
			$this->initMailSettings();

		if(!$this->cache->load('Barcode1d'))
			$this->initBarcodes1D();

		if(!$this->cache->load('Barcode2d'))
			$this->initBarcodes2D();
	}
	
	private function initMailSettings()
	{
		$data                = array();
        $data['adminEmail']  = SettingsQuery::create()->findOneByKey('admin_email')->getValue();
       	$data['smtp']        = SettingsQuery::create()->findOneByKey('smtp')->getValue();
        $data['mailUsrName'] = SettingsQuery::create()->findOneByKey('mail_username')->getValue();
        $data['mailPassword']= SettingsQuery::create()->findOneByKey('mail_password')->getValue();
        $data['mailPort']    = SettingsQuery::create()->findOneByKey('mail_port')->getValue();
        $data['mailFromName']= SettingsQuery::create()->findOneByKey('mail_from_name')->getValue(); 
        
        $this->cache->save($data, 'MailSettings');
	}
		
	private function initBarcodes1D()
	{
    	$this->cache->save(Barcodes1dQuery::create()->select(array('Name','Code','Id'))->orderById()->find(), 'Barcode1d');
    	$this->cache->save(Barcodes1dQuery::create()->select('Code')->orderById()->find(), 'Barcode1dCode');
	}

	private function initBarcodes2D()
	{
    	$this->cache->save(Barcodes2dQuery::create()->select(array('Name','Id'))->orderById()->find(), 'Barcode2d');
	}

	public function getCache()
	{
		return $this->cache;
	}

	public static function singleton()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }	
}
?>