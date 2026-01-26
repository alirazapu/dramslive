<?php

$encodings = [
  "ASCII",
  "JIS",
  "EUC-JP"
];
$encode_str= mb_detect_encoding($data['received_body'], $encodings);
$encode_str_raw= mb_detect_encoding($data['received_body_raw'], $encodings);
          
//$encode_str= mb_detect_encoding(base64_decode($data['received_body']));
if($encode_str=='ASCII'){  
  
//    $data['received_body'] = base64_decode($data['received_body']);    
     $data_body = array_filter(explode('From:', strip_tags($data['received_body_raw'])));
     
                $data['received_body'] = $data_body[0];
                if (strpos(strtolower($data['received_body']), '----- forwarded message -----') !== false){
                    $data['received_body']='';
                }
}
  

if(empty($data['received_body']) && !empty($data['received_body_raw'])){
    
     $data_body = array_filter(explode('From:', strip_tags($data['received_body_raw'])));
     if(!empty($data_body[1])){
         $send_email = Model_Inneruse::get_inner_value_2(5);
     $data_body = array_filter(explode($send_email, strip_tags($data_body[1])));
         $data['received_body'] = !empty($data_body[1]) ? $data_body[1] :$data_body[0];
     }else{
         
                $data['received_body'] = $data_body[0];
     }
}


$mark_complete=0;
if($encode_str=='ASCII' && in_array($encode_str_raw,['EUC-JP','ASCII']) ){  
    $mark_complete=1;
}

$check_array=[
   'Purged', 
   'purged', 
   'Port Out', 
   'portout', 
   'Port out', 
   'not found', 
   'out of service range', 
   'switched off', 
   'Is it ZONG subscriber', 
   'ated from outside of the', 
];
foreach ($check_array as $str_check) {
    if (strpos(($data['received_body']),$str_check) !== false) {
        $mark_complete=1;
    }
}

$check_body=[
   'na', 
    'n',
    'no data',
    'nil',
    '',
    ' ',
];
foreach ($check_body as $str_check) {
    if (strtolower(trim($data['received_body']))==$str_check) {
        $mark_complete=1;
    }
}

          
if (!empty($mark_complete) || empty($data['received_body']) || $data['received_body']==null || $data['received_body']==' ') {
    $reference_number = $data['request_id'];
    
    Model_ErrorLog::log(
        'parse_location_zong',
        'No location data in response - marking as status 5 (Not Found)',
        [
            'request_id' => $reference_number,
            'company_name' => $data['company_name'] ?? 4,
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


    
$data['received_body'] = str_replace(PHP_EOL,':',$data['received_body']);                         
$data['received_body'] = preg_replace( "/\r|\n/", PHP_EOL, $data['received_body']); 
$data['received_body'] = str_replace( ":". PHP_EOL, '', $data['received_body']); 

// echo $data['received_body'];
//$data['received_body'] = str_replace('MSISDN','',$data['received_body']); 
//$data['received_body'] = str_replace('BTS Name','',$data['received_body']); 
//$data['received_body'] = str_replace('CellAddress','',$data['received_body']); 
//$data['received_body'] = str_replace('Cell Identity','',$data['received_body']); 
//$data['received_body'] = str_replace('Location','',$data['received_body']); 
$parse_val = array_map('trim',array_filter(explode(':',strip_tags($data['received_body']))));


//mark complte if no array formed
if(sizeof($parse_val) < 5){   
 $reference_number = $data['request_id'];
 
    Model_ErrorLog::log(
        'parse_location_zong',
        'Insufficient location data fields - marking as status 5 (Not Found)',
        [
            'request_id' => $reference_number,
            'company_name' => $data['company_name'] ?? 4,
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

$loc_data['locationmsisdn']='';
$loc_data['locaddress']= '';
$loc_data['loccellid']= '';     
$loc_data['loclac']= '';     
$loc_data['locstatus']= '';


 if(!empty($login_user->id) && $login_user->id==137){
//         echo '<pre>'; 
////         print_r(($data));         
//         print_r(($parse_val));         
//          exit;
////         
          }

$msisdnKey=0;
$intersectionMsisdn=array_intersect($parse_val,['MobileNo','mobileNo','ileNo','obileNo','bileNo','eNo','No']);                  
if(!empty($intersectionMsisdn)){
    reset($intersectionMsisdn);
    $msisdnKey=key($intersectionMsisdn);
}
if((strtolower(trim($parse_val[0]))=='msisdn') && (strtolower(trim($parse_val[2]))=='cell identity'))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[1]), -10);
}elseif((strtolower(trim($parse_val[0]))=='msisdn') && (strtolower(trim($parse_val[2]))=='bts name'))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[1]), -10);
}elseif((strtolower(trim($parse_val[1]))=='msisdn') && (strtolower(trim($parse_val[3]))=='bts name'))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[2]), -10);
}elseif((strtolower(trim($parse_val[0]))=='obileno') && (strtolower(trim($parse_val[2]))=='latitude') && !empty($parse_val[8]))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[8]), -10);
}elseif((strtolower(trim($parse_val[0]))=='mobileno') && (strtolower(trim($parse_val[2]))=='latitude') && !empty($parse_val[8]))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[8]), -10);
}elseif((strtolower(trim($parse_val[0]))=='bileno') && (strtolower(trim($parse_val[2]))=='latitude') && !empty($parse_val[8]))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[8]), -10);
}elseif((strtolower(trim($parse_val[1]))=='mobileno') && (strtolower(trim($parse_val[3]))=='latitude') && !empty($parse_val[9]))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[9]), -10);
}elseif((strtolower(trim($parse_val[3]))=='mobileno') && (strtolower(trim($parse_val[5]))=='latitude') && !empty($parse_val[11]))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[11]), -10);
}elseif((strtolower(trim($parse_val[1]))=='bileno') && (strtolower(trim($parse_val[5]))=='address') && !empty($parse_val[9]))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[9]), -10);
}elseif((strtolower(trim($parse_val[1]))=='obileno') && (strtolower(trim($parse_val[5]))=='address') && !empty($parse_val[9]))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[9]), -10);
}elseif((strtolower(trim($parse_val[2]))=='ileno') && (strtolower(trim($parse_val[6]))=='address') && !empty($parse_val[10]))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[10]), -10);
}elseif((strtolower(trim($parse_val[2]))=='mobileno') && (strtolower(trim($parse_val[6]))=='address') && !empty($parse_val[10]))
{
    $loc_data['locationmsisdn']= substr(trim($parse_val[10]), -10);
}elseif(!empty($intersectionMsisdn)  && (strtolower(trim($parse_val[$msisdnKey+4]))=='address')  && !empty($parse_val[$msisdnKey+8]))
{ //common function to deal all scenarios
    $loc_data['locationmsisdn']= substr(trim($parse_val[$msisdnKey+8]), -10);
}
          

$cellIdKey=0;
$intersectionCellId=array_intersect($parse_val,['Cell ID','cell id','cell Id','Cell id']);                  
if(!empty($intersectionCellId)){
    reset($intersectionCellId);
    $cellIdKey=key($intersectionCellId);
}
if((strtolower(trim($parse_val[2]))=='cell identity') && (strtolower(trim($parse_val[4]))=='location area identity'))
{
    $loc_data['loccellid']= trim($parse_val[3]);
}elseif((strtolower(trim($parse_val[6]))=='cell identity') && (strtolower(trim($parse_val[8]))=='location area identity'))
{
    $loc_data['loccellid']= trim($parse_val[7]);
    $loc_data['loclac']= trim($parse_val[9]); 
}elseif((strtolower(trim($parse_val[7]))=='cell identity') && (strtolower(trim($parse_val[9]))=='location area identity'))
{
    $loc_data['loccellid']= trim($parse_val[8]);
    $loc_data['loclac']= trim($parse_val[10]);

}elseif((strtolower(trim($parse_val[5]))=='cell id') && (strtolower(trim($parse_val[6]))=='lac id'))
{
    $loc_data['loccellid']= trim($parse_val[13]);
    $loc_data['loclac']= trim($parse_val[14]);
}elseif((strtolower(trim($parse_val[6]))=='cell id') && (strtolower(trim($parse_val[7]))=='lac id'))
{
    $loc_data['loccellid']= trim($parse_val[14]);
    $loc_data['loclac']= trim($parse_val[15]);
}elseif((strtolower(trim($parse_val[8]))=='cell id') && (strtolower(trim($parse_val[9]))=='lac id'))
{
    $loc_data['loccellid']=!empty($parse_val[16]) ? trim($parse_val[16]) :'';
    $loc_data['loclac']= !empty($parse_val[17]) ? trim($parse_val[17]) :'';
}elseif((strtolower(trim($parse_val[7]))=='cell id') && (strtolower(trim($parse_val[8]))=='lac id'))
{
    $loc_data['loccellid']=!empty($parse_val[15]) ? trim($parse_val[15]) :'';
    $loc_data['loclac']= !empty($parse_val[16]) ? trim($parse_val[16]) :'';

}elseif(!empty($intersectionCellId)  && (strtolower(trim($parse_val[$cellIdKey+1]))=='lac id'))
{ //common function to deal all scenarios
    $loc_data['loccellid']=!empty($parse_val[$cellIdKey+8]) ? trim($parse_val[$cellIdKey+8]) :0;
    $loc_data['loclac']= !empty($parse_val[$cellIdKey+9]) ? trim($parse_val[$cellIdKey+9]) :0;
}


if((strtolower(trim($parse_val[4]))=='location area identity') && (strtolower(trim($parse_val[6]))=='msc name'))
{
    $loc_data['loclac']= trim($parse_val[5]);
}


$addressKey=0;
$intersectionAddress=array_intersect($parse_val,['address','Address']);                  
if(!empty($intersectionAddress)){
    reset($intersectionAddress);
    $addressKey=key($intersectionAddress);
}
if((strtolower(trim($parse_val[6]))=='msc name') && (strtolower(trim($parse_val[8]))=='network type')){
    $loc_data['locaddress'] .= ' MSC Name: ' . trim($parse_val[7]);
}elseif((strtolower(trim($parse_val[2]))=='bts name') && (strtolower(trim($parse_val[4]))=='celladdress')){
    $loc_data['locaddress'] .= ' BTS Name: ' . trim($parse_val[3]) . ' Address: ' . trim($parse_val[5]);
}elseif((strtolower(trim($parse_val[3]))=='bts name') && (strtolower(trim($parse_val[5]))=='celladdress')){
    $loc_data['locaddress'] .= ' BTS Name: ' . trim($parse_val[3]) . ' Address: ' . trim($parse_val[6]);
}elseif((strtolower(trim($parse_val[4]))=='address') && (strtolower(trim($parse_val[7]))=='location age')){
    $loc_data['locaddress'] .= ' Location Age: ' . trim($parse_val[15]) . ' Address: ' . trim($parse_val[12]);
}elseif((strtolower(trim($parse_val[5]))=='address') && (strtolower(trim($parse_val[8]))=='location age')){
    $loc_data['locaddress'] .= ' Location Age: ' . trim($parse_val[16]) . ' Address: ' . trim($parse_val[13]);
}elseif((strtolower(trim($parse_val[7]))=='address') && (strtolower(trim($parse_val[10]))=='location age')){
    $loc_data['locaddress'] .= ' Location Age: ' . trim($parse_val[18]) . ' Address: ' . trim($parse_val[15]);
}elseif((strtolower(trim($parse_val[6]))=='address') && (strtolower(trim($parse_val[9]))=='location age')){
    $loc_data['locaddress'] .= ' Location Age: ' . trim($parse_val[17]) . ' Address: ' . trim($parse_val[14]);

}elseif(!empty($intersectionAddress)  && (strtolower(trim($parse_val[$addressKey+1]))=='cell id'))
{    //common function to deal all scenarios
      $loc_data['locaddress'] .= ' Location Age: ' . trim($parse_val[$addressKey+11]) . ' Address: ' . trim($parse_val[$addressKey+8]);
}          



$latKey=0;
$intersectionLat=array_intersect($parse_val,['latitude','Latitude']);                  
if(!empty($intersectionLat)){
    reset($intersectionLat);
    $latKey=key($intersectionLat);
}
if(!empty($parse_val[10]) && !empty($parse_val[11]) && (strtolower(trim($parse_val[2]))=='latitude'))
    {   $lat_long[0] = trim($parse_val[10]);
        $lat_long[1] = trim($parse_val[11]);
    }elseif(!empty($parse_val[11]) && !empty($parse_val[12]) && (strtolower(trim($parse_val[3]))=='latitude')){
        $lat_long[0] = trim($parse_val[11]);
        $lat_long[1] = trim($parse_val[12]); 
    }elseif(!empty($parse_val[13]) && !empty($parse_val[14]) && (strtolower(trim($parse_val[5]))=='latitude')){
       $lat_long[0] = trim($parse_val[13]);
        $lat_long[1] = trim($parse_val[14]); 
    }elseif(!empty($parse_val[12]) && !empty($parse_val[13]) && (strtolower(trim($parse_val[4]))=='latitude')){
       $lat_long[0] = trim($parse_val[12]);
        $lat_long[1] = trim($parse_val[13]); 
    }elseif(!empty($intersectionLat)  && (strtolower(trim($parse_val[$latKey+1]))=='longitude') && !empty($parse_val[$latKey+8]))
    { //common function to deal all scenarios
        $lat_long[0] = trim($parse_val[$latKey+8]);
        $lat_long[1] = trim($parse_val[$latKey+9]);
    }
    
    
    
    if(!empty($lat_long))
    {    
        $loc_data['loclat']=min($lat_long);
        $loc_data['loclong']=max($lat_long);
    }else{
        $loc_data['loclat']= '';
        $loc_data['loclong']='';
    }

    

if(strtolower((trim($parse_val[8]))=='network type') && (strtolower(trim($parse_val[10]))=='msisdn purged?'))
{
if( strpos( $parse_val[9], '2G' ) !== false ) {
    $loc_data['locnetwork']=1;
}elseif( strpos( $parse_val[9], '3G' ) !== false ) {
    $loc_data['locnetwork']=2;
}elseif(( strpos( $parse_val[9], '4G' ) !== false) || (strpos( $parse_val[9], 'LTE' ) !== false)) {
    $loc_data['locnetwork']=3;
}else{
    $loc_data['locnetwork']=0;
} 



/*
if( strpos(strtolower(trim($parse_val[10])), 'attached' ) !== false ) {
    $loc_data['locstatus']=1;
}elseif( strpos(strtolower(trim($parse_val[10])), 'deattach' ) !== false ) {
    $loc_data['locstatus']=0;
}else*/
if( strpos(strtolower(trim($parse_val[11])), 'yes' ) !== false ) {
    $loc_data['locstatus']=2;
}else{
    $loc_data['locstatus']=0;
}
}else{
/* if( strpos(strtolower(trim($parse_val[10])), 'attached' ) !== false ) {
    $loc_data['locstatus']=1;
}elseif( strpos(strtolower(trim($parse_val[10])), 'deattach' ) !== false ) {
    $loc_data['locstatus']=0;
}else*/
if(!empty($parse_val[10]) && strpos(strtolower(trim($parse_val[10])), 'yes' ) !== false ) {
    $loc_data['locstatus']=2;
}else{
    $loc_data['locstatus']=0;
}


}


}


//       if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; print_r($loc_data);         
//          exit;
//         
//          }