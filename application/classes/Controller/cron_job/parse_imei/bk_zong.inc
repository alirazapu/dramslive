<?php

$encode_str= mb_detect_encoding(base64_decode($data['received_body']));
if($encode_str=='ASCII')
    $data['received_body'] = base64_decode($data['received_body']);    

$data['received_body'] = array_filter(explode('From:', strip_tags($data['received_body'])));

if (strpos(strtolower($data['received_body'][0]), 'na') !== false ||
        strpos(strtolower($data['received_body'][0]), 'no data found ') !== false 
        || strpos(strtolower($data['received_body'][0]), 'no record found') !== false) {
    if(!empty($data['request_id']))
    {    $reference_number = $data['request_id'];
         $reference_number = Model_Email::email_status($reference_number, 2, 5);
    }   
    if(!empty($file_id))
           $error_number = Model_Email::file_status($file_id, 0, 1);
    
    $not_fount = 1;    
}else{

$data['received_body'] = str_replace(' ', '', $data['received_body'][0]);
$data['received_body'] = str_replace('MobileNo', '', $data['received_body']);
$data['received_body'] = str_replace('ile', '', $data['received_body']);
$data['received_body'] = str_replace('obNo', '', $data['received_body']);
$data['received_body'] = str_replace('bNo', '', $data['received_body']);
$data['received_body'] = str_replace('IMEI', '', $data['received_body']);
$data['received_body'] = str_replace('Last_Activity_Date', '', $data['received_body']);
//$data['received_body'] = str_replace(PHP_EOL,' ',$data['received_body']);
$parse_val = array_values(array_filter(explode(PHP_EOL, strip_tags($data['received_body']))));

$data_imei['user_id'] = $data['user_id'];
$data_imei['imeicompany'] = $data['company_name'];
$data_imei['requestid'] = $data['request_id'];
$data_imei['imeisims'] = Helpers_Utilities::find_imei_last_digit(str_pad($data['requested_value'], 15, '0', STR_PAD_RIGHT));

$parse_final = array();

foreach ($parse_val as $key => $value) {
    if (!empty($parse_val[$key]) && strlen(trim($parse_val[$key])) > 2) {
        //echo strlen($parse_val[$key]);
        $parse_final[] = trim($parse_val[$key]);
    }
}
$parse_val = $parse_final;



$parse_val = array_values($parse_val);
$size_of = sizeof($parse_val);


if(strlen(trim($data['received_body']))==36)
{   
    
    $requested_result[]=substr(trim($data['received_body']), 0,12);
    $requested_result[]=Helpers_Utilities::find_imei_last_digit(substr(trim($data['received_body']), 12,15));
    $requested_result[]=substr(trim($data['received_body']), 26,10);
    
    $data_imei['imei_mobile_number'][]= substr(trim($requested_result[0]), -10);
    $data_imei['usefrom'][]= date("Y-m-d H:i:s", strtotime(trim($requested_result[2])));
    $data_imei['imeisims']= trim($requested_result[1]);

}else{
//    echo '<pre>';
//print_r($parse_val);
//exit;
for ($i = 0; $i < $size_of;) {
    $data_imei['imei_mobile_number'][] = substr(trim($parse_val[$i]), -10);
    $i = $i + 1;
    $i = $i + 1;
    $data_imei['usefrom'][] = date("Y-m-d H:i:s", strtotime(trim($parse_val[$i])));
    $i = $i + 1;
}
}

//echo '<pre>';
//print_r($parse_val);
//print_r($data_imei);
//echo strlen(trim($data['received_body']));
//exit;
//$parse_val = array_filter(explode(PHP_EOL,strip_tags($data['received_body'])));                        
//$parse_val_2 = array_filter(explode('-',strip_tags($parse_val[0])));

$data_imei['mnc_imei']=4;
$sub_model = new Model_Generic();
$sub_model_result = $sub_model->Manualimeisimsinsert($data_imei);

/*
  $ext = pathinfo($data['received_file_path'], PATHINFO_EXTENSION);
  if(!empty($data['received_file_path']))
  {
  if($ext == 'zip')
  {
  $file = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . $data['received_file_path'];
  $file_name = Helpers_Upload::unzip_file($file);
  $file = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . $file_name;
  $phone_data['path'] = $file;
  $result= Helpers_Upload::data_mapping_partially($phone_data['path'], $phone_data['company_name'], $phone_data['phone_number'], $phone_data['userrequestid']);
  }elseif($ext == 'rar')
  {
  $file = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . $data['received_file_path'];
  $file_name = Helpers_Upload::unziprar_file($file);
  $file = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . $file_name;
  $phone_data['path'] = $file;

  $result= Helpers_Upload::data_mapping_partially($phone_data['path'], $phone_data['company_name'], $phone_data['phone_number'], $phone_data['userrequestid']);

  }else{
  $phone_data['path'] = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . $data['received_file_path'];
  echo $phone_data['path'];
  $result= Helpers_Upload::data_mapping_partially($phone_data['path'], $phone_data['company_name'], $phone_data['phone_number'], $phone_data['userrequestid']);
  }
  } */

}