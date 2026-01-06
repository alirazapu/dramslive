<?php

$encode_str= mb_detect_encoding(base64_decode($data['received_body']));
if($encode_str=='ASCII'){
    $data['received_body'] = base64_decode($data['received_body']);    
}


    
$data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));
 
 

include 'notfound.inc';

$data['received_body'] = $data['received_body'][0];

//  if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; print_r($data['received_body']);         
//          exit;
//         
//          }


if ($not_fount!=1){     
if (strpos($data['received_body'], 'Location of +') !== false) {        
$data['received_body'] = str_replace('Location of +', PHP_EOL. ' msisdn:',$data['received_body']);       
$data['received_body'] = str_replace(' at ', PHP_EOL . ' realtime location time!',$data['received_body']);       
$data['received_body'] = str_replace(' is ', PHP_EOL. ' realtime location:',$data['received_body']);       
$data['received_body'] = str_replace('CellID:',' realtime location cell_id:',$data['received_body']);       
}
$data['received_body'] = str_replace('*','',$data['received_body']);
$parse_val = array_map('trim',array_filter(explode(PHP_EOL,strip_tags($data['received_body']))));
$array_parse = array();
foreach($parse_val as $parse)
{                            
if(strlen(trim($parse))>=2)
    $array_parse[]=trim($parse);

}
$parse_val=$array_parse; 

$count = 1;

//if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; 
////         print_r(($data));         
//         print_r($parse_val);         
//          exit;
////         
//          }

if(sizeof($parse_val) < 2){
     $reference_number = $data['request_id'];
$reference_number = Model_Email::email_status($reference_number, 2, 5);
                 $not_fount = 1;  
                 
}else{
                 
foreach($parse_val as $val)
{                            
if( strpos(strtolower($val), 'msisdn' ) !== false ) {
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
    $loc_data['locationmsisdn']=!empty($exact_val[1]) ? substr(trim($exact_val[1]), -10):'';
}elseif( strpos(strtolower($val), 'person_name' ) !== false ) {
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
    $loc_data['person_name']=!empty($exact_val[1]) ? trim($exact_val[1]):'';
}elseif( strpos(strtolower($val), 'nic' ) !== false ) {
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
    $loc_data['nic']=!empty($exact_val[1]) ? trim($exact_val[1]):'';
}elseif( strpos(strtolower($val), 'imei' ) !== false ) {
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
  $loc_data['locimei']=!empty($exact_val[1]) ?  str_pad(trim($exact_val[1]), 15, '0', STR_PAD_RIGHT) :'';
   if(in_array($loc_data['locimei'],['f00000000000000'])){
     $loc_data['locimei']='';  
   }
}elseif( strpos(strtolower($val), 'imsi' ) !== false ) {
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
  $loc_data['locimsi']= !empty($exact_val[1]) ? trim($exact_val[1]):'';
}elseif( strpos(strtolower($val), 'realtime location time' ) !== false ) {                                
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));        
  if(!empty($exact_val[3]))
  {
      $exact_val = array_map('trim',array_filter(explode('!',strip_tags($val))));        
      $exact_val[1] = !empty($exact_val[1]) ?  $exact_val[1]:'';
  }
  elseif(sizeof($exact_val)==3){
    $exact_val[1] = $exact_val[1] . ":" . $exact_val[2] . ":" . $exact_val[3];
  }
  $loc_data['locdate']=!empty($exact_val[1]) ?  date("Y-m-d H:i:s", strtotime(trim($exact_val[1]))):'';  
  
  
 }elseif( strpos(strtolower($val), 'last cdr time' ) !== false ) {                                
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));        
   
  if(empty($exact_val[3]))
  {
      $exact_val = array_map('trim',array_filter(explode('!',strip_tags($val))));        
      $exact_val[1] = !empty($exact_val[1]) ?  $exact_val[1]:'';
  }
  elseif(sizeof($exact_val)==4){
    $exact_val[1] = $exact_val[1] . ":" . $exact_val[2] . ":" . $exact_val[3];
  }
  

 
  $loc_data['locdate']= !empty($exact_val[1]) ? date("Y-m-d H:i:s", strtotime(trim($exact_val[1]))):'';  
  
}elseif( strpos(strtolower($val), 'realtime location:' ) !== false ) {
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
  $loc_data['locaddress'].= !empty($exact_val[1]) ? ' Current: ' . trim($exact_val[1]):'';
}elseif( strpos(strtolower($val), 'last cdr location' ) !== false ) {
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
  $loc_data['locaddress'].= !empty($exact_val[1]) ? ' CDR: ' . trim($exact_val[1]):'';
}elseif( strpos(strtolower($val), 'realtime location cell_id' ) !== false ) {
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
  $loc_data['loccellid']= !empty($exact_val[1]) ? trim($exact_val[1]):'';
}elseif( strpos(strtolower($val), 'realtime location longitude' ) !== false ) {
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
  $loc_data['loclong']= !empty($exact_val[1]) ? trim($exact_val[1]):'';
}elseif( strpos(strtolower($val), 'realtime location latitude' ) !== false ) {
  $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));                              
  $loc_data['loclat']= !empty($exact_val[1]) ? trim($exact_val[1]):'';;
}

$count= $count + 1;
}
$lat_long[0] = $loc_data['loclong'];
$lat_long[1] = $loc_data['loclat'];

$loc_data['loclat']=min($lat_long);
$loc_data['loclong']=max($lat_long);

}

//if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; 
////         print_r(($data));         
//         print_r($loc_data);         
//          exit;
////         
//          }
}
?>