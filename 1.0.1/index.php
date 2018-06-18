<?php

ob_start();
ini_set('display_errors', '0');     # don't show any errors...
//error_reporting(E_ALL | E_STRICT);
// Require Contant define file
require_once 'include.php';
//require_once 'connection.php';
require_once 'global_msg.php';

array_push($classesDir, ROOT_DIR . 'api/');
/*
 * Enter description here...
 * @param unknown_type $string
 * @return Full Message String
 */
//----------------------------
require_once LIBRARY_DIR . 'restler/restler.php';
require_once 'auth/auth.php';
require_once INCLUDES_DIR . 'CommonFunction.php';
require_once INCLUDES_DIR . 'requestutils.php';

$r=new Restler();
$r->addAPIClass('UserAPI');
$r->addAPIClass('DriverAPI');
$r->addAPIClass('CarAPI');
$r->addAPIClass('CategoryAPI');
$r->addAPIClass('TripAPI');
$r->addAPIClass('NotificationAPI');
$r->addAPIClass('PaymentAPI');
$r->addAPIClass('PromoAPI');
$r->addAPIClass('MessageAPI');
$r->addAPIClass('ConstantAPI');
$r->addAPIClass('BraintreeAPI');
$r->setSupportedFormats('JsonFormat');
$r->addAuthenticationClass('UserAuth');
$r->handle();
