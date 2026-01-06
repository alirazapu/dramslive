<?php

if($data['received_file_path']!='na')
{
    //print_r($data['received_file_path']);                            
    //$path = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR. 'cdr' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . $data['received_file_path'];
    $path =  !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($data['file_id'], 'save') . $data['received_file_path']: '';
        
    $cnic_data = Helpers_Upload::warid_cnic_sub($path);


    $size_of_array = sizeof($cnic_data);
    for($i=2; $i<=$size_of_array;$i++)
    {                                
            $loc_data['mobile_number'][]= substr(trim($cnic_data[$i][0]), -10);
            if(!empty($cnic_data[$i][1]))
            $loc_data['cnicsims'][]= trim($cnic_data[$i][3]);                                
    }                            
    $loc_data['cnicsims']=array_unique($loc_data['cnicsims']); 
    $loc_data['cnicsims']=$loc_data['cnicsims'][0];
    

}else{
    
    
    
    $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }   

                
  //check if no data found exist
 include '/var/www/html/aies/application/classes/Controller/cron_job/parse_sub/notfound.inc';

                
if (!empty($not_fount)) {
       $not_fount = 1;     
}else{
    

                    
    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));
    $data['received_body'] = str_replace('-','',$data['received_body'][0]); 
    $data['received_body'] = str_replace(',',PHP_EOL,$data['received_body']); 
    $data['received_body'] = str_replace(';',PHP_EOL,$data['received_body']); 
    $data['received_body'] = str_replace('*','',$data['received_body']); 
    $data['received_body'] = str_replace(':','',$data['received_body']); 
    $data['received_body'] = str_replace('Active','',$data['received_body']); 
    $data['received_body'] = str_replace('Suspended','',$data['received_body']); 
    $data['received_body'] = str_replace('Inactive','',$data['received_body']); 
    $data['received_body'] = str_replace('EXPIRED','',$data['received_body']); 
    $data['received_body'] = str_replace('-','',$data['received_body']); 
    $data['received_body'] = str_replace('Connected','',$data['received_body']); 
    $data['received_body'] = str_replace('CONNECTED','',$data['received_body']); 
    $data['received_body'] = str_replace('CONNECTED','',$data['received_body']); 
    $data['received_body'] = str_replace('[','',$data['received_body']); 
    $data['received_body'] = str_replace(']','',$data['received_body']); 
    
    
    if((empty(trim($data['received_body'])) || trim($data['received_body']) =='na') && $data['request_send_count']< 2)
    {    
        $reference_number = $data['request_id'];
        $reference_number = Model_Email::email_status($reference_number, 0, 0);
        $not_fount = 1;
    }    
    elseif (strpos(strtolower($data['received_body']), 'no data found') !== false) {
            $reference_number = $data['request_id'];
                $reference_number = Model_Email::email_status($reference_number, 2, 5);
                $not_fount = 1;                
        }else{            
     
    $parse_val = array_map('trim',array_filter(explode(PHP_EOL,strip_tags($data['received_body']))));
   
    
    $parse_val = array_filter($parse_val);                 
    $name_convert = array_map("strtoupper", $parse_val);
    $array_loc= array_search("ACTIVE", $name_convert);                        
    if(!empty($array_loc))
    unset($parse_val[$array_loc]);
    $name_convert = array_map("strtoupper", $parse_val);
    $array_loc= array_search("ACTIVE", $name_convert);                        
    if(!empty($array_loc))
    unset($parse_val[$array_loc]);
    $array_loc= array_search("INACTIVE", $name_convert);                        
    if(!empty($array_loc))                        
    unset($parse_val[$array_loc]);                        
    $parse_val = array_values($parse_val);                         
    $size_of_array= sizeof($parse_val); 
    if(!empty($parse_val[0]))
    $loc_data['cnicsims']=trim($parse_val[0]);
    for($i=1; $i<$size_of_array;)
    {
        $loc_data['mobile_number'][]= substr(trim($parse_val[$i]), -10);
        $i++;                                                                    
    }                            
    $loc_data['mobile_number']=array_unique($loc_data['mobile_number']); 
    
//     echo '<pre>';
//    print_r($parse_val);
//    print_r($loc_data);
//    exit;
    
} 

}
}
?>