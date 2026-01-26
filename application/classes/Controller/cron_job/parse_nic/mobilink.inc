<?php
 $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                 }   
        //$tempExclude='kindly provide cdr of cell';        
  //check if no data found exist
 include APPPATH . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';
                
if (!empty($not_fount)) {
       $not_fount = 1;     
}else{
    
    $temp_parse_logic=0;

    if(empty($temp_parse_logic)){
$data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'][0])));
//print_r($data['received_body']);
$data['received_body'] = str_replace(' ','',$data['received_body']); 
$data['received_body'] = str_replace('=','',$data['received_body']); 
$data['received_body'] = str_replace('-','',$data['received_body']); 
$data['received_body'] = str_replace('*','',$data['received_body']); 
$data['received_body'] = str_replace('_','',$data['received_body']); 
$send_email = Model_Inneruse::get_inner_value_2(5);
$data['received_body'] = explode((string)$send_email,$data['received_body'][0]); 
$data['received_body'] = explode(PHP_EOL,$data['received_body'][0]); 

$parse_val=$data['received_body']; 
 
                
$array_parse = array();
foreach($parse_val as $parse)
{                            
    if(strlen(trim($parse))>=2)
        $array_parse[]=trim($parse);

}                        
$parse_val=$array_parse[0]; 
$parse_val = explode(',',$parse_val); 
$parse_val = array_values($parse_val); 
$size_of_array= sizeof($parse_val); 
 
if(!empty($parse_val[0]))
$loc_data['cnicsims']=trim($parse_val[0]);

for($i=1; $i<$size_of_array;)
{   
    $parse_val[$i] = str_replace('[','',$parse_val[$i]);
    $parse_val[$i] = str_replace(']','',$parse_val[$i]);
    $phone_data = explode(':',$parse_val[$i]); 
       if(!empty($phone_data[0])){
    $loc_data['mobile_number'][]= substr(trim($phone_data[0]), -10);
    //$loc_data['mobile_number'][]= trim($phone_data[0]);    
       }
    $i++;                                                                    
}  
                          
$loc_data['mobile_number']=array_unique($loc_data['mobile_number']);   

    }else{
         
       
        $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'][0])));
//print_r($data['received_body']);
$data['received_body'] = str_replace(' ','',$data['received_body']); 
$data['received_body'] = str_replace('=','',$data['received_body']); 
$data['received_body'] = str_replace('-','',$data['received_body']); 
$data['received_body'] = str_replace('*','',$data['received_body']); 
$data['received_body'] = str_replace('_','',$data['received_body']); 
$data['received_body'] = str_replace('Active','',$data['received_body']); 
$send_email = Model_Inneruse::get_inner_value_2(5);

$data['received_body'] = explode((string)$send_email,$data['received_body'][0]); 
$data['received_body'] = explode(PHP_EOL,$data['received_body'][0]); 

$parse_val=$data['received_body']; 
        
       
$array_parse = array();
foreach($parse_val as $parse)
{                            
    if(strlen(trim($parse))>=2)
        $array_parse[]=trim($parse);

}      
 
$parse_val=$array_parse[0]; 

 

$parse_val = explode(',',$parse_val); 
$parse_val = array_values($parse_val); 
$size_of_array= sizeof($parse_val); 

if(!empty($parse_val[0]))
$loc_data['cnicsims']=trim($parse_val[0]);

for($i=1; $i<$size_of_array;)
{   
    $parse_val[$i] = str_replace('[','',$parse_val[$i]);
    $parse_val[$i] = str_replace(']','',$parse_val[$i]);
    $phone_data = explode(':',$parse_val[$i]); 
    
    if(!empty($phone_data[0])){
    $loc_data['mobile_number'][]= substr(trim($phone_data[0]), -10);
    }
    //$loc_data['mobile_number'][]= trim($phone_data[0]);    
    $i++;                                                                    
}  
                          
$loc_data['mobile_number']=array_unique($loc_data['mobile_number']); 
      

  
        
        
    }


}











  
//$data['received_body'] = str_replace(PHP_EOL,'',$data['received_body']);   
//$data['received_body'] = str_replace(',',PHP_EOL,$data['received_body']); 
//$data['received_body'] = str_replace(';',PHP_EOL,$data['received_body']); 
                   
//$data['received_body'] = str_replace('Suspended','',$data['received_body']); 
 
//$parse_val = array_map('trim',array_filter(explode(PHP_EOL,strip_tags($data['received_body']))));
         

//    $data['received_body'] = str_replace(PHP_EOL,'',$data['received_body'][0]); 
//   $data['received_body'] = explode(PHP_EOL,$data['received_body']); 
//echo '<pre>tst';
//                         print_r($data['received_body']); 
//                exit;


/*
$parse_val = array_map("strtoupper", $parse_val);
$size_of_array= sizeof($parse_val); 
for($i=0; $i<$size_of_array;$i++)
{
    $array_loc= array_search("ACTIVE", $parse_val);                          
    if(!empty($array_loc))                        
    unset($parse_val[$array_loc]);

    $array_loc= array_search("INACTIVE", $parse_val);                        
    if(!empty($array_loc))                        
    unset($parse_val[$array_loc]);                   
    $array_loc= array_search("", $parse_val);                        
    if(!empty($array_loc))                        
    unset($parse_val[$array_loc]);                   
}
*/



//for($i=1; $i<$size_of_array;)
//{   
//    $parse_val[$i] = str_replace('[','',$parse_val[$i]);
//    $parse_val[$i] = str_replace(']','',$parse_val[$i]);
//    $phone_data = explode(':',$parse_val[$i]); 
//    
//    $loc_data['mobile_number'][]= substr(trim($phone_data[0]), -10);
//    //$loc_data['mobile_number'][]= trim($phone_data[0]);
//    
//    $i++;                                                                    
//}  
