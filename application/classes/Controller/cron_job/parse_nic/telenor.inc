<?php
    $not_found_for_telenor = 1;
}elseif($data['received_file_path']!='na')
{
    //print_r($data['received_file_path']);                            
    //$path = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR. 'cdr' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . $data['received_file_path'];
    $path =  !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($data['file_id'], 'save') . $data['received_file_path']: '';

    $ext = pathinfo($path, PATHINFO_EXTENSION);
  
         if($ext == 'zip')
         {   
             //$file = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . $is_manual . DIRECTORY_SEPARATOR . $data['received_file_path'];
             $file_name = Helpers_Upload::unzip_file($path, $data['request_id']);                                
              //$file = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . $is_manual . DIRECTORY_SEPARATOR . $file_name;
             //echo $file =  !empty($data['request_id']) ? Helpers_Upload::get_request_data_path($data['request_id'], 'save') . DIRECTORY_SEPARATOR . $file_name: '';
              $file =  !empty($data['request_id']) ? Helpers_Upload::get_request_data_path($data['file_id'], 'save') . DIRECTORY_SEPARATOR . $file_name: '';
              $reference_number = $data['request_id'];
              $cnic_data = Helpers_Upload::telenor_cnic_sub($file, $reference_number);
              if(file_exists($file))
                unlink($file);
         }
       $loc_data['requestid'] = $data['request_id'];
       $loc_data['cnicsims'] = $cnic_data[1][0];
        for($i= 1; $i < sizeof($cnic_data); $i++)
        {
            $mobile=$cnic_data[$i][1];
            $loc_data['mobile_number'][]= substr(trim($mobile), -10);
            //if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))            
        }   
        
            if(strlen($loc_data['cnicsims'])==13)
            {   
                $sub_model = new Model_Generic();
                $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);

            }
        $not_fount = 1;
    

}else{
    

 $encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }   
$data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));

                
  //check if no data found exist
 include APPPATH . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';

                
if (!empty($not_fount)) {
       $not_fount = 1;     
}else{
    
 $temp_parse_logic=0;

    if(empty($temp_parse_logic)){
$data['received_body'] = str_replace('*','',$data['received_body'][0]); 
$data['received_body'] = str_replace('verified','',$data['received_body']); 
$data['received_body'] = str_replace('no data found','nil',  strtolower($data['received_body'])); 
$data['received_body'] = str_replace('No data has been found','nil', strtolower($data['received_body'])); 


$data['received_body'] = str_replace('-','',$data['received_body']);
$data['received_body'] = str_replace('.','',$data['received_body']);
$data['received_body'] = str_replace('Sent through ActiveXperts SendEMail','',$data['received_body']);

$parse_val = array_map('trim',array_filter(explode(PHP_EOL,strip_tags($data['received_body']))));

$name_convert = array_map("strtoupper", $parse_val);

 
                  
                
$array_loc= array_search("MSISDN", $name_convert);                        
if(!empty($array_loc))                                                
unset($parse_val[$array_loc]);
$array_loc= array_search("CNIC", $name_convert);                        
if(!empty($array_loc))                        
unset($parse_val[$array_loc]);                    
$array_loc= array_search("Name:", $name_convert);                        
if(!empty($array_loc))                        
unset($parse_val[$array_loc]);                    

$parse_final = array();



foreach ($parse_val as $key => $value) {                            
    if(!empty($parse_val[$key]) && strlen(trim($parse_val[$key]))>2){
        //echo strlen($parse_val[$key]);
        
        if (strpos(strtolower($parse_val[$key]), 'msisdn:') !== false ) {
            $parse_val[$key] = str_replace('MSISDN:','',$parse_val[$key]);
            $parse_final[]= trim($parse_val[$key]);
        }else if(empty($parse_val[$value]) && !empty($parse_val[$key]) && strlen(trim($parse_val[$key]))==11){
            $parse_final[]= trim($parse_val[$key]);
            
        }
        
    }
}     


/* updated parsing */

$parse_val=$parse_final;

  
$parse_val = array_values($parse_val);                         
$size_of_array= sizeof($parse_val);       

//$loc_data['cnicsims']=trim($data['requested_value']);

for($i=0; $i<$size_of_array;)
{
    if(strlen($parse_val[$i])==13 && ctype_digit($parse_val[$i]))
    {    
        if(!empty($parse_val[$i]))
        {    
            $loc_data['cnicsims'][] = trim($parse_val[$i]);
        }else{
            $loc_data['cnicsims'][]=trim($data['requested_value']);
        }
        $i++;

    }else{
        $loc_data['mobile_number'][]= substr(trim($parse_val[$i]), -10);
        $i++;//$i=$i+2;
    }
    /*if(!empty($parse_val[$i]))
    {   $loc_data['cnicsims']=trim($parse_val[$i]);                            
        $i=$i+1;                                
    }*/
}                            

if(empty($loc_data['cnicsims']))
    $loc_data['cnicsims'][0]=trim($data['requested_value']);

$check_cnicsim=array_unique($loc_data['cnicsims']);

    if(sizeof($check_cnicsim)>=2)    
    {
        $nic_array = $loc_data['cnicsims'];
        $mobile_array = $loc_data['mobile_number'];
        foreach($nic_array as $key => $value)
        {
            $loc_data['mobile_number']=array();
            $loc_data['cnicsims'] = $nic_array[$key];
            $loc_data['mobile_number'][] =$mobile_array[$key];

            if(strlen($loc_data['cnicsims'])==13 && ctype_digit($loc_data['cnicsims']))
            {   
                $sub_model = new Model_Generic();
                $sub_model_result = $sub_model->Manualcnicsimsinsert($loc_data);

            }
        }                            
        $not_fount = 1;  
    }else{
        $loc_data['cnicsims'] = $loc_data['cnicsims'][0];                            
    }  
    
    
    }else{
        //temp logic here
        
//        $data['received_body'] = str_replace(' ','',$data['received_body']); 
$data['received_body'] = str_replace('=','',$data['received_body']); 
$data['received_body'] = str_replace('-','',$data['received_body']); 
$data['received_body'] = str_replace('*','',$data['received_body']); 
$data['received_body'] = str_replace('_','',$data['received_body']); 

//$data['received_body'] = explode('ctdpunjab@aiesmail.com',$data['received_body'][0]); 
//$data['received_body'] = explode(PHP_EOL,$data['received_body'][0]); 
$data['received_body'] = explode(' ',$data['received_body']); 
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

        
       
    }
}

}