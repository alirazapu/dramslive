<?php

$encode_str= mb_detect_encoding(base64_decode($data['received_body']));
if($encode_str=='ASCII')
    $data['received_body'] = base64_decode($data['received_body']);    



if (empty($data['received_body'])) {
    $reference_number = $data['request_id'];
    
    Model_ErrorLog::log(
        'parse_sub_zong',
        'Empty response body received - marking as status 5 (Not Found)',
        [
            'request_id' => $reference_number,
            'company_name' => $data['company_name'] ?? 'zong',
            'user_request_type_id' => $data['user_request_type_id'] ?? null,
            'processing_index' => 5,
            'reason' => 'received_body is empty after decoding'
        ],
        null,
        'not_found',
        'subscriber_parsing'
    );
    
    $reference_number = Model_Email::email_status($reference_number, 2, 5);
    $not_fount = 1;
}else{

$first_string = $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));
  //check if no data found exist

 include APPPATH . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';
}        
if (!empty($not_fount)) {
       $not_fount = 1; 
}else{
$data['received_body'] = str_replace('CNIC','',$data['received_body'][0]);                        
$data['received_body'] = str_replace('-','',$data['received_body']);                        
$data['received_body'] = str_replace('MSISDN','',$data['received_body']);                        
$data['received_body'] = str_replace('REG_NBR','',$data['received_body']);                        
$data['received_body'] = str_replace('CUST_NAME','',$data['received_body']);                        
$parse_val = array_filter(explode(PHP_EOL,strip_tags($data['received_body'])));



$array_parse = array();
foreach($parse_val as $parse)
{                            
    if(strlen(trim($parse))>=2)
        $array_parse[]=trim($parse);

}
$parse_val=$array_parse; 



$test_string = str_replace('-','', $first_string[0]);
if((trim($test_string)== trim($parse_val[0])) && sizeof($parse_val)==1 )
{
    $parse_val = explode('CTD KPK',strip_tags($first_string[1]));
    $parse_val = str_replace('CNIC','',  strtoupper($parse_val[1]));
    $parse_val = str_replace('*','',  strtoupper($parse_val));
    $parse_val = array_filter(explode(PHP_EOL,strip_tags($parse_val)));
    $array_parse = array();
    foreach($parse_val as $parse)
    {                            
        if(strlen(trim($parse))>=2)
            $array_parse[]=trim($parse);

    }
    $parse_val=$array_parse; 
    
}        


if (strpos(strtolower(trim(($parse_val[0]))), 'subscriber detail') !== false) {
        array_splice($parse_val,0,1);
    }
    

  
$count = 0;
foreach ($parse_val as $parse)
{
    if($count==0)
    $mobile_number=substr(trim($parse), -10);
    elseif($count==1)
    {
        if(!ctype_digit(trim($parse)) && strlen(trim($parse)==13))
            {
                $cnic_original = trim($parse);     
                //$cnic = Helpers_Utilities::check_digit(trim($parse));
                $is_foreigner= 1;    
            }else{
                   $cnic= $parse;  
            }
    }
    elseif($count==2)
    $name= $parse;

    $count= $count + 1;
}   
}

 //echo '<br>' ;                        
//  print_r($mobile_number);
//  print_r($cnic);
//  print_r($cnic_original);
//  print_r($name);
 // print_r($data['received_body']);
 // exit;
?>
