<?php
class Language 
{
	private static $instance;
	
	private function __construct()
	{
		if(!isset($_SESSION['language']))
		{
		    if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		    {
		    	$langcode = explode(";", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		    	$langcode = explode(",", $langcode['0']);
		    	$_SESSION['language'] = $locale = $langcode['0'];
		    }
		    else
		    {
		    	$_SESSION['language'] = 'en_US';
		    }
		}
		
		$this->initLang($_SESSION['language']);
	}
	
	private function initLang($locale)
	{
		putenv("LC_ALL=$locale");
		setlocale(LC_ALL, $locale);
		bindtextdomain('messages', "./locale");
		textdomain('messages');
	}
	
	public function setLanguage($locale)
	{
		$_SESSION['language'] = $locale;
		$this->initLang($locale);
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