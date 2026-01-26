<?php

$data['received_body'] = str_replace('--','',$data['received_body']);
$data['received_body'] = str_replace(PHP_EOL,'',$data['received_body']);
$data['received_body'] = str_replace('"','',$data['received_body']);
$data['received_body'] = str_replace('N.A','',$data['received_body']);
$data['received_body'] = str_replace('NULL','',$data['received_body']);
$data['received_body'] = str_replace('*','',$data['received_body']);
$parse_val = array_map('trim',array_filter(explode('|',strip_tags($data['received_body']))));
//          

   
          
$mark_complete=0;
$check_array=[
   'roaming powered off', 
   'powered off', 
   'terminate user', 
   'not found', 
   'no record found', 
   'number is purged', 
   'does not exist', 
   'network error', 
   'try again later', 
   'pakistan with', 
   'does not exist', 
   'roaming to', 
   'no location found', 
    'assumed idle'
];
$check_body=[
   'na', 
    'n',
    'no data',
    'nil',
    '',
    ' ',
];
foreach ($check_array as $str_check) {
    if (strpos(strtolower(trim($data['received_body'])),$str_check) !== false) {
        $mark_complete=1;
    }
}

foreach ($check_body as $str_check) {
    if (strtolower(trim($data['received_body']))==$str_check) {
        $mark_complete=1;
    }
}


        
       
  
          
if (!empty($mark_complete) || empty($data['received_body']) || $data['received_body']==null || $data['received_body']==' ') {
    
    $reference_number = $data['request_id'];
    
    Model_ErrorLog::log(
        'parse_location_scom',
        'No location data in response - marking as status 5 (Not Found)',
        [
            'request_id' => $reference_number,
            'company_name' => $data['company_name'] ?? 6,
            'user_request_type_id' => $data['user_request_type_id'] ?? null,
            'processing_index' => 5,
            'mark_complete' => $mark_complete,
            'body_empty' => empty($data['received_body'])
        ],
        null,
        'not_found',
        'location_parsing'
    );
    
        $reference_number = Model_Email::email_status($reference_number, 2, 5);
        $not_fount = 1;
      
}else{

//$array_loc= array_search("IMEI", $parse_val);
//for($i=0; $i<=$array_loc; $i++)
//{unset($parse_val[$i]); }     


//echo '<pre>';
//print_r($parse_val);
//exit;


$array_parse = array();
foreach($parse_val as $parse)
{                            
if(strlen(trim($parse))>=2)
    $array_parse[]=trim($parse);

}
$parse_val=$array_parse; 

$parse_val = array_values($parse_val); 

  
//mark complte if no array formed
if(sizeof($parse_val) < 5){   
        $reference_number = $data['request_id'];
        
    Model_ErrorLog::log(
        'parse_location_scom',
        'Insufficient location data fields - marking as status 5 (Not Found)',
        [
            'request_id' => $reference_number,
            'company_name' => $data['company_name'] ?? 6,
            'user_request_type_id' => $data['user_request_type_id'] ?? null,
            'processing_index' => 5,
            'fields_count' => sizeof($parse_val),
            'required_fields' => 5
        ],
        null,
        'not_found',
        'location_parsing'
    );
        
        $reference_number = Model_Email::email_status($reference_number, 2, 5);
        $not_fount = 1;
}

    if(!empty($login_user->id) && $login_user->id==137){
         echo '<pre>'; print_r($parse_val);         
          exit;
         
          }
          
    $loc_data['locationmsisdn']=substr(trim($parse_val[0]), -10);
      $parse_val[3]= str_replace(' PM','',$parse_val[3]);
    $parse_val[3]= str_replace(' AM','',$parse_val[3]);
    
    $date_h = $parse_val[2] . ' ' .$parse_val[3];  
    $loc_data['locdate']= date("Y-m-d H:i:s", strtotime($date_h));         
    
    if(ctype_digit(trim($parse_val[4])))
        $loc_data['loclac']=trim($parse_val[4]);
    
    if(ctype_digit(trim($parse_val[5])))
        $loc_data['loccellid']=trim($parse_val[5]);

    $key = array_search('Attached', $parse_val);
    if(empty($key))
    $key = array_search('Deattach', $parse_val);    
    if(empty($key))
    {
        $key = array_search('Purged', $parse_val);    
        $parse = $parse_val[$key];
    }else{                            
    $parse = $parse_val[6];
    }
    if( strpos(strtolower($parse), 'attached' ) !== false ) {
        $loc_data['locstatus']=1;
    }elseif( strpos( strtolower($parse), 'deattach' ) !== false ) {
        $loc_data['locstatus']=0;
    }elseif( strpos( strtolower($parse), 'purged' ) !== false ) {
        $loc_data['locstatus']=2;
    }else{
        $loc_data['locstatus']=0;
    }
    if(!empty($parse_val[8]))
        $loc_data['locimsi']=trim($parse_val[8]);                            
    if(isset($parse_val[10]))
        $loc_data['locaddress']=$parse_val[10] . ' MSC: ' . $parse_val[1] . ' Last Activity: ' . $parse_val[7] . ' Last Radio Action: ' . $parse_val[9];
    if(!empty($parse_val[11]) && !empty($parse_val[12]))
    {   $lat_long[0] = trim($parse_val[11]);
        $lat_long[1] = trim($parse_val[12]);
    }
    if(isset($lat_long))
    {    
        $loc_data['loclat']=min($lat_long);
        $loc_data['loclong']=max($lat_long);
    }else{
        $loc_data['loclat']= '';
        $loc_data['loclong']='';
    }
    if(isset($parse_val[14]) && is_integer($parse_val[14]))
    {    
        $loc_data['locimei']=str_pad(trim($parse_val[14]), 15, '0', STR_PAD_RIGHT);
    }
   
}
//echo '<pre>';
//print_r($parse_val);
//print_r($loc_data);
//exit;
    
          
//   if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; print_r($loc_data);         
//          exit;
//         
//          }
   
?>