<?php
Class Mail {	
	public $mailobj;		                                  //phpmailer object
	public $mailSMTPDebug		= "";
	public $mailIsSMTP 		    = true;							  // telling the program to use SMTP (either true or false)	
	public $mailSMTPAuth		= true;					  //enable SMTP authentication (either true or false)
	public $mailSMTPSecure		= "false";					  // sets the prefix to the servier
	public $mailHost		    = ""; // sets SMTP server
	public $mailPort		    = "3535";						       // set the SMTP port
	public $mailUsername		= ""; // mail user name
	public $mailPassword		= "";					//mail user password
	
	/*Mail sender and receiver details*/
	public $mailFrom		    = "chandan@metadesignsolutions.in";	 //mail sender email
	public $mailFromName		= "";			 //mail sender name
	public $mailToName		    = "";	                     //mail receiver name
	public $mailSubject		    = "";
	public $mailSuccessMsg		= true;
	public $mailfailureMsg		= false;
    public	$invalidCaptcha		= "Invalid Captcha Value";
	
	public function __construct() {	
		$mail             = new PHPMailer();
		if($this->mailIsSMTP) {
			$mail->IsSMTP();
		} 
		if($this->mailSMTPDebug) {
			$mail->SMTPDebug  = $this->mailSMTPDebug;
		}

		$mail->SMTPAuth   = $this->mailSMTPAuth;	
		$mail->SMTPSecure = $this->mailSMTPSecure;
		//$mail->Host       = $this->mailHost;
		$mail->Port       = $this->mailPort;
		//$mail->Username   = $this->mailUsername;
		//$mail->Password   = $this->mailPassword;
		//$mail->SetFrom($this->mailFrom, $this->mailFromName);
		$this->mailobj = $mail;		
	}

	public function getMailObj() {
		return $this->mailobj;
	}
	public function addAddress($mailTo,$mailToName)
	{  
		$this->mailobj->AddAddress($mailTo, $mailToName);
	}	
	
	public function setBodyContent($bodyArray){
        global $mailSubject;    
		$this->mailobj->Subject = $bodyArray['msgSub'];		
		$this->mailobj->MsgHTML($bodyArray['msgBody']);
	}
	public function setMailSetting($host,$mailFrom,$mailFromName,$userName,$password){
		 $this->mailobj->Host     = $host;
		 $this->mailobj->Username = $userName;
		 $this->mailobj->Password = $password;
		 $this->mailobj->SetFrom($mailFrom, $mailFromName);
	}
	public function sendMail() {
			$responseData = $this->mailSuccessMsg;
			if(!$this->mailobj->Send()) {
			  $responseData = $this->mailfailureMsg;
			}
			return $responseData;
	}	
};
?>
