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

$mail->Username = $result['send']['user'];
$mail->Password = $result['send']['password'];
$mail->FromName = $result['send']['name'];
$mail->setFrom($mail->Username, $mail->FromName);
$mail->Subject = $subject;
$mail->Body = $body;
// Custom Email Adress on the "Others" admin request flow allows a
// comma/semicolon separated list of addresses; every other flow only
// ever passes a single address, so this loop is a no-op for them.
$to_addresses = array_filter(array_map('trim', preg_split('/[,;]+/', (string) $to)));
if (empty($to_addresses)) {
    $to_addresses = array($to);
}
foreach ($to_addresses as $to_address) {
    $mail->AddAddress($to_address, $to_name);
}

?>