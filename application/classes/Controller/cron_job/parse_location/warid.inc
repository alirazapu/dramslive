<?php


$data['received_body'] = str_replace('Dear Team,','',$data['received_body']);
$data['received_body'] = str_replace('Kindly provide address and (LAT,','',$data['received_body']);
$data['received_body'] = str_replace('LONG) of b/m site.','',$data['received_body']);
$data['received_body'] = str_replace('and','',$data['received_body']);
$data['received_body'] = str_replace('*','',$data['received_body']);
$data['received_body'] = str_replace(')','',$data['received_body']);
$data['received_body'] = str_replace('(','',$data['received_body']);
 
//$parse_val = array_map('trim',array_filter(explode(PHP_EOL,strip_tags($data['received_body']))));
$parse_val = array_map('trim',array_filter(explode('|',strip_tags($data['received_body']))));
//$data['received_body'] = str_replace('*SUBNO*','',$data['received_body']);  

//   if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; print_r($parse_val);         
//          exit;
//         
//          }
          
$mark_complete=0;
$check_array=[
   'not found', 
   'no record found', 
   'number is purged', 
   'does not exist', 
   'network error', 
   'try again later', 
   'not exist', 
   'pakistan with', 
];
$check_body=[
   'na', 
    'n',
    'no data',
    'nil',
    'doest not exist',
    '',
    ' ',
];
foreach ($check_array as $str_check) {
    if (strpos(strtolower(trim(($data['received_body']))),$str_check) !== false) {
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
        $reference_number = Model_Email::email_status($reference_number, 2, 5);
        $not_fount = 1;
      
}else{


/*
$array_parse = array();
foreach($parse_val as $parse)
{                            
    if(strlen(trim($parse))>=2)
        $array_parse[]=trim($parse);

}
$parse_val=$array_parse; 
echo '<pre>';
print_r($parse_val);
exit;
$count = 1;
foreach($parse_val as $val)
{
    if( strpos(strtolower($val), 'location number' ) !== false ) {
      $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
        $loc_data['Location_number']=trim($exact_val[1]);
    }elseif( strpos(strtolower($val), 'mobile no' ) !== false ) {
      $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
        $loc_data['locationmsisdn']=substr(trim($exact_val[1]), -10);
    }elseif( strpos(strtolower($val), 'concern msc no' ) !== false ) {
      $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
        $loc_data['locaddress']='msc: ' . trim($exact_val[1]);
    }elseif( strpos(strtolower($val), 'subscriber have' ) !== false ) {                            
        if( strpos(strtolower($val), '2g' ) !== false ) {
            $loc_data['locnetwork']=1;
        }elseif( strpos(strtolower($val), '3g' ) !== false ) {
            $loc_data['locnetwork']=2;
        }elseif(( strpos(strtolower($val), '4g' ) !== false) || (strpos(strtolower($val), 'lte' ) !== false)) {
            $loc_data['locnetwork']=3;
        }else{
            $loc_data['locnetwork']=0;
        }
    }elseif( strpos(strtolower($val), 'de-attached' ) !== false ) {
        $loc_data['locstatus']=0;
    }elseif( (strpos(strtolower($val), 'attached' ) !== false ) || ( strpos(strtolower($val), 'idle' ) !== false )) {
        $loc_data['locstatus']=1;
    }elseif( strpos(strtolower($val), 'purged' ) !== false ) {
        $loc_data['locstatus']=2;
    }elseif( strpos(strtolower($val), 'imsi number' ) !== false ) {
      $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
      $loc_data['locimsi']= trim($exact_val[1]);
    }elseif( strpos(strtolower($val), 'imei number' ) !== false ) {
      $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
      $loc_data['locimei']= str_pad(trim($exact_val[1]), 15, '0', STR_PAD_RIGHT);
    }elseif( strpos(strtolower($val), 'lac id' ) !== false ) {
      $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
      $loc_data['loclac']= trim($exact_val[1]);
    }elseif( strpos(strtolower($val), 'site is' ) !== false ) {
      $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
      $loc_data['loccellid']= trim($exact_val[1]);
    }elseif( strpos(strtolower($val), 'address' ) !== false ) {
      $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
      if(!empty($exact_val[1]))
      $loc_data['locaddress'].= ' ' . trim($exact_val[1]);
    }elseif( strpos(strtolower($val), 'long' ) !== false ) {
      $exact_val = array_map('trim',array_filter(explode(':',strip_tags($val))));
      if(!empty($exact_val[1]))
      {    
        $exact_val = array_map('trim',array_filter(explode(',',strip_tags($exact_val[1]))));
        $loc_data['loclat']= min($exact_val);
        $loc_data['loclong']= max($exact_val);    
      }
    }
    if($count==15)
    {
        $loc_data['locaddress'].= ' ' . trim($val);
    }    
    $count= $count + 1;
}
 * 
 * 
 */


//$array_loc= array_search("IMEI", $parse_val);
//for($i=0; $i<=$array_loc; $i++)
//{unset($parse_val[$i]); }     

    
    
$array_parse = array();
foreach($parse_val as $parse)
{                            
if(strlen(trim($parse))>=2)
    $array_parse[]=trim($parse);

}
$parse_val=$array_parse; 

$parse_val = array_values($parse_val); 

         
    $loc_data['locationmsisdn']=substr(trim($parse_val[0]), -10);
    $parse_val[2]= date("Y-m-d", strtotime($parse_val[2]));         
    $parse_val[3]= str_replace(' PM','',$parse_val[3]);
    $parse_val[3]= str_replace(' AM','',$parse_val[3]);
    
    $date_h = trim($parse_val[2]) . ' ' .trim($parse_val[3]);  
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
    if(!empty($parse_val[11]))
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
    if(isset($parse_val[14]))
    {    
        $loc_data['locimei']=str_pad(trim($parse_val[14]), 15, '0', STR_PAD_RIGHT);
    }
    
}

 
   