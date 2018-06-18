<?php

class SendMail
{


function SendingMail($mailId, $message, $subject, $mailToName){


include_once 'PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;

// $mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';                       // Specify main and backup server
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'xenia.taxi@gmail.com';                   // SMTP username
$mail->Password = 'xenia@123';               // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable encryption, 'ssl' also accepted
$mail->Port = 465;                                    //Set the SMTP port number - 587 for authenticated TLS
$mail->setFrom('xenia.taxi@gmail.com', 'Xenia.Taxi');     //Set who the message is to be sent from
//$mail->addReplyTo('@gmail.com', 'BES TAXI');  //Set an alternative reply-to address
//$mail->addAddress('@gmail.com', 'Josh Adams');  // Add a recipient
$mail->addAddress($mailId);               // Name is optional
// $mail->addCC('totoourworld@gmail.com');
//$mail->addBCC('bcc@example.com');
$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
//$mail->addAttachment('/usr/labnol/file.doc');         // Add attachments
//$mail->addAttachment('/images/image.jpg', 'new.jpg'); // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $subject;
$mail->Body    = $message;
//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('http://freecvmakers.com/'), dirname(__FILE__));

if(!$mail->send()) {
   return 'Message could not be sent.';
// echo 'Mail// er Error: ' . $mail->ErrorInfo;
//     exit;
}

return 'Success';
// echo 'Message has been sent';

}

}


?>