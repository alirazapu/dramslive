<?php
$encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  

                    if($encode_str=='ASCII'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }else{
                        $data['received_body']=$data['received_body_raw'];
                    }   
$data['received_body'] = explode('From:',$data['received_body']); 


          var_dump(DOCUMENT_ROOT);
  //check if no data found exist
 include DOCUMENT_ROOT . 'application' . DS . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';

                
if (!empty($not_fount)) {
       $not_fount = 1;     
}else{
    $data['received_body']=$data['received_body'][0];
//$data['received_body'] = str_replace('*','',$data['received_body'][0]); 
$data['received_body'] = str_replace('MSISDN','',$data['received_body']);                            
$data['received_body'] = str_replace('REG_NBR','',$data['received_body']);                            
$data['received_body'] = str_replace('CNIC.','',$data['received_body']);                            
$data['received_body'] = str_replace('CUST_NAME','',$data['received_body']);                            
$data['received_body'] = str_replace('SISDN','',$data['received_body']);                                                    


    
$parse_val = array_map('trim',array_filter(explode(PHP_EOL,strip_tags($data['received_body']))));

/*
$name_convert = array_map("strtoupper", $parse_val);
$array_loc= array_search("MSISDN", $name_convert);     
if(!empty($array_loc))                        
unset($parse_val[$array_loc]);
$array_loc= array_search("REG_NBR", $name_convert);    
if(!empty($array_loc))                                                
unset($parse_val[$array_loc]);
$array_loc= array_search("CUST_NAME", $name_convert);                        
if(!empty($array_loc))                        
unset($parse_val[$array_loc]);
$parse_val = array_values($parse_val); 
*/

                
$parse_final = array();

foreach ($parse_val as $key => $value) {                            
    if(!empty($parse_val[$key]) && strlen(trim($parse_val[$key]))>2 && ctype_digit(trim($parse_val[$key]))){
        //echo strlen($parse_val[$key]);
        $parse_final[]= trim($parse_val[$key]);
    }
}                        
$parse_val=$parse_final;                        




$size_of_array= sizeof($parse_val);                        

for($i=0; $i<$size_of_array;)
{
    $loc_data['mobile_number'][]= substr(trim($parse_val[$i]), -10);
    $i++;
    if(!empty($parse_val[$i]))
    {
        $loc_data['cnicsims']=trim($parse_val[$i]);                            
        $i++;                                
    }
}                       


 $loc_data['mobile_number']=array_unique($loc_data['mobile_number']);

//  echo '<pre>tst';
//                         print_r($loc_data); 
//                exit;
}

