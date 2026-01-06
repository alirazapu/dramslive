<?php

$mail->IsSMTP(); // enable SMTP  //for live server open and local server close
//$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only detail for 4
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
$mail->Host = "smtp.gmail.com";
$mail->Port = 465; // or 587
$mail->IsHTML(true);
//$mail->CharSet = "text/html; charset=UTF-8;"; //change for telnor
$result = Helpers_Inneruse::get_gmail_pw();
$mail->Username = $result['aies']['send']['user'];
$mail->Password = $result['aies']['send']['password'];
$mail->FromName = $result['aies']['send']['name'];
$mail->setFrom($mail->Username, $mail->FromName);
$mail->Subject = $subject;
$mail->Body = $body;
$mail->AddAddress($to, $to_name);

?>