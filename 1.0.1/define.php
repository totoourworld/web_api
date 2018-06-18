<?php
//Enable error reporting?
//error_reporting(E_ALL ^ E_NOTICE);
//error_reporting(E_ALL & E_STRICT & E_DEPRECATED);
//Set Defaul time zone
//date_default_timezone_set ("Asia/Calcutta");
date_default_timezone_set('GMT');

// Set time,session expire after this time.
define('SESSION_EXPIRE_TIME', 3000);

/** FILE SYSTEM PATHS **/ 

// Absolute file system path to the root. change all back slashes to forward slashes on Windows
define('ROOT_DIR', rtrim(realpath(dirname(__FILE__)), '/\\'). '/'); 
define('IMAGE_ROOT_DIR', rtrim(realpath(dirname(dirname(__FILE__)). "/"), '/\\'). '/'); 

//set absolute file system path for propel library files
define('LIBRARY_DIR',ROOT_DIR.'lib/');
//set absolute file system path for includes dirctory 
define('INCLUDES_DIR',ROOT_DIR.'includes/');
//set absolute file system path for controller dirctory 
define('MODEL_DIR',ROOT_DIR.'model/');
define('AUTH_DIR',ROOT_DIR.'auth/');
//set absolute file system path for controller dirctory
define('MAILHTML',ROOT_DIR.'mail_html/');

// Define path of product logo image
define('UPLOAD_IMAGE_PATH', IMAGE_ROOT_DIR.'images/originals/');
define('UPLOAD_VIDEO_PATH', IMAGE_ROOT_DIR.'images/originals/');
define('THUMBNAILS_UPLOAD_IMAGE_PATH', IMAGE_ROOT_DIR.'images/thumbnails/');
define('SMALL_THUMBNAILS_UPLOAD_IMAGE_PATH', IMAGE_ROOT_DIR.'images/small_thumbnails/');
define('RESIZE_UPLOAD_IMAGE_PATH', IMAGE_ROOT_DIR.'images/resize/');


define('IMG_MAX_SIZE', 100000);
define('ALLOW_EXT', 'jpg,jpeg,png');

define('HOSTNAME','localhost');
define('USERNAME','root');
define('PASSWORD','mcsamuel201*');
define('DATABASE','xeniataxi');

define("MAIL_HOST", 'gator3261.hostgator.com');
define('MAIL_FROM', 'admin@taxi.com');
define('MAIL_FROM_NAME', 'taxi.com');
define('MAIL_USER_NAME', 'admin@taxi.com');
define('MAIL_PASSWORD', 'admintaxi');

define('IMAGE_MERGE_PATH', LIBRARY_DIR.'image-merge'.DIRECTORY_SEPARATOR.'mergePicture.php');
define('RESIZE_IMAGE_PATH', LIBRARY_DIR.'image-merge'.DIRECTORY_SEPARATOR.'resizeImage.php');

define('PUSH_LIVE_FLAG',1);
define('API_ACCESS_KEY','AIzaSyCWhWQWAD1cgzTcj9cO_I8mLb5AbA6ZexU');

define('DEFAULT_PUSH_MESSAGE','hI');
define('SERVER_DB_NAME','xeniataxi');
?>
