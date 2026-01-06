<?php
$not_fount = 0;
  
$encode_str= mb_detect_encoding(base64_decode($data['received_body']));                  
                    if($encode_str=='ASCII'){
                        $data['received_body'] = base64_decode($data['received_body']); 
                    }   
              
             
       
   $data['received_body'] = array_filter(explode('From:',($data['received_body_raw'])));
                 
       
  //check if no data found exist
// include '/var/www/html/aies/application/classes/Controller/cron_job/parse_sub/notfound.inc';

   if (strpos($data['received_body'][0], '----- Forwarded message -----') !== false) {
//        $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body_raw'])));
              
       if(!empty($data['received_body'][1]))
        $data['received_body'][0] = $data['received_body'][1];

   }
  
  
        include '/var/www/html/aies/application/classes/Controller/cron_job/parse_sub/notfound.inc';
    
 
if (empty($not_fount)){   
 
   
include APPPATH. 'classes/Controller/cron_job/parse_sub/replace.inc';

  

$data['received_body'] = (isset($data['received_body'][0])&& strlen($data['received_body'][0])>2)?$data['received_body'][0]:$data['received_body'];

if (strpos($data['received_body'], 'quotedprintable') !== false) {
    $parse_val = array_map('trim',array_filter(explode('quotedprintable',strip_tags($data['received_body']))));
    $data['received_body'] = $parse_val[1];
}
  
if (strpos(strtolower($data['received_body']), '<style>') !== false)
    $data['received_body'] = preg_replace("/.*<body[^>]*>|<\/body>.*/si", "", $data['received_body']);
#$data['received_body'] = str_replace('CNIC','',$data['received_body']); 
$data['received_body'] = str_replace('numbers','',$data['received_body']); 
$data['received_body'] = str_replace('NO','',$data['received_body']); 
#$data['received_body'] = str_replace('NAME','',$data['received_body']); 
#$data['received_body'] = str_replace('MSISDN','',$data['received_body']); 
$data['received_body'] = str_replace('see the details.','',$data['received_body']); 
$data['received_body'] = str_replace('Off for more than 24 Hours.','',$data['received_body']); 
$data['received_body'] = str_replace('`','',$data['received_body']); 
$data['received_body'] = str_replace('dear customer, following numbers are active on your cnic ','',strtolower($data['received_body'])); 
$receive_body_helper = $data['received_body'];

$data['received_body'] = str_replace('`','',$data['received_body']); 
$data['received_body'] = str_replace('`','',$data['received_body']); 
$data['received_body'] = str_replace('_','',$data['received_body']); 
$data['received_body'] = str_replace('#','',$data['received_body']); 
$data['received_body'] = str_replace('check.','',$data['received_body']); 
$data['received_body'] = str_replace($data['requested_value'],'',$data['received_body']); 
$data['received_body'] = str_replace('NUMBER','',$data['received_body']); 
$data['received_body'] = str_replace('with profound regards','', strtolower($data['received_body'])); 
$data['received_body'] = str_replace('sa bukhari','', strtolower($data['received_body'])); 
$data['received_body'] = str_replace('original','', strtolower($data['received_body'])); 
$data['received_body'] = str_replace('message','', strtolower($data['received_body'])); 
$data['received_body'] = str_replace('SABukhari','',$data['received_body']); 
$data['received_body'] = str_replace('.','',$data['received_body']); 
$data['received_body'] = str_replace(',','',$data['received_body']); 
$data['received_body'] = str_replace(' ',PHP_EOL,$data['received_body']); 
$data['received_body'] = str_replace('sent on the move powered by','',$data['received_body']); 
$data['received_body'] = str_replace('&nbsp;','',$data['received_body']); 
$nbsp = html_entity_decode("&nbsp;");
$data['received_body'] = str_replace($nbsp,'',$data['received_body']); 
//$data['received_body'] = str_replace('&','',$data['received_body']); 

$value = preg_replace('/(<[^>]*) style=("[^"]+"|\'[^\']+\')([^>]*>)/i', '$1$3', $data['received_body']);
$value = preg_replace('/(<[^>]*) lang=("[^"]+"|\'[^\']+\')([^>]*>)/i', '$1$3', $value);
$data['received_body'] = preg_replace('/(<[^>]*) class=("[^"]+"|\'[^\']+\')([^>]*>)/i', '$1$3', $value);
$parse_val = array_map('trim',array_filter(explode(PHP_EOL,trim(strip_tags($data['received_body'])))));



$parse_val = array_filter($parse_val);
$array_parse = array();
$del_val = ['sent','on', 'the', 'move', 'powered', 'by','ufone'];
foreach($del_val as $del)
{    
if (($key = array_search($del, $parse_val)) !== false) {
    unset($parse_val[$key]);
}
}
foreach($parse_val as $parse)
{                            
    if(strlen(trim($parse))>=2)
        $array_parse[]=trim($parse);
}     

$parse_val_help = $parse_val=$array_parse;    
$parse_val = array_values($parse_val); 
$size_of_array= sizeof($parse_val); 

 
       
for($i=0; $i<$size_of_array;)
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
     $loc_data['cnicsims']=$data['requested_value'];                     
$loc_data['mobile_number']=array_unique($loc_data['mobile_number']);  



}

















// echo '<pre>' ; 
//       print_r($loc_data);
//       exit;

/* Case # 1: 
  [0] => MSISDN
  [1] => CNIC
  [2] => NAME
*/
//
//if(sizeof($parse_val)==1 && strlen($parse_val[0])==24)
//{
//    $parse_val = array_map('trim',array_filter(explode('92',strip_tags($parse_val[0]))));    
//    foreach ($parse_val as $pval)
//    {
//        $loc_data['mobile_number'][]= substr(trim($pval), -10);
//    }    
//    $loc_data['cnicsims']=$data['requested_value'];
//    
//}elseif(sizeof($parse_val)==1 && strlen($parse_val[0])<=12)
//{
//    $loc_data['mobile_number'][]= substr(trim($parse_val[0]), -10);
//    $loc_data['cnicsims']=$data['requested_value'];
//    
//}elseif(strtoupper(trim($parse_val[0]))== 'CNIC' && strtoupper(trim($parse_val[1]))== 'MSISDN' && strtoupper(trim($parse_val[2]))== 'NAME')
//{
//    unset($parse_val[0]);
//    unset($parse_val[1]);
//    unset($parse_val[2]);
//   $parse_val = array_values($parse_val);
//   foreach($parse_val as $number_val)
//   {
//       if($number_val == $data['requested_value'])
//           continue;
//       
//       if(ctype_digit(trim($number_val)))
//       {
//           $loc_data['mobile_number'][]= substr(trim($number_val), -10);
//       }   
//       
//       
//   }    
//     $loc_data['cnicsims']=$data['requested_value'];
//    
//}elseif(strtoupper(trim($parse_val[0]))== 'MSISDN' && strtoupper(trim($parse_val[1]))== 'CNIC' && strtoupper(trim($parse_val[2]))== 'NAME')
//{
//    unset($parse_val[0]);
//    unset($parse_val[1]);
//    unset($parse_val[2]);
//   $parse_val = array_values($parse_val);
//   if(sizeof($parse_val)>3)
//   {
//       $size_of = sizeof($parse_val);
//       for($i=0; $i<$size_of;$i++)
//       {
//           $loc_data['mobile_number'][]= substr(trim($parse_val[$i]), -10);
//           $loc_data['cnicsims']= $parse_val[++$i];           
//           $i++;           
//       }
//   }else{
//       $loc_data['mobile_number'][]= substr(trim($parse_val[0]), -10);
//       $loc_data['cnicsims']= $parse_val[1];           
//   }   
//
//}else{
///* Case end */    
//if((strpos(strtoupper(trim($parse_val[0])), 'CNICMSISDN') !== false) && sizeof($parse_val)>=2)
//{
//
//   $loop_help = strlen(trim($parse_val[1]));
//   for($i=0; $i<$loop_help;)
//   {
//       if(!empty($parse_val[1])) 
//       {    
//        $helper = substr($parse_val[1], $i, 13);
//        str_replace($helper,"",$parse_val[1]);
//        $val_1[] = $helper;
//       }
//       if(!empty($parse_val[1])) 
//       {   
//        $i=$i+13;                                
//        $helper = substr($parse_val[1], $i, 12);
//        str_replace($helper,"",$parse_val[1]);
//        $val_1[] = $helper;                                                                
//        $i=$i+12;                                
//       }
//   }
//   unset($parse_val[0]);
//   unset($parse_val[1]);
//   $helper_merger = array_merge($val_1,$parse_val);
//   $parse_val=$helper_merger;
//}else{                       
//
//    $name_convert = array_map("strtoupper", $parse_val);
//
//    $array_loc= array_search("MSISDN", $name_convert);  
//    if(!empty($array_loc))
//    unset($parse_val[$array_loc]);
//
//    $array_loc= array_search("CUSTOMERNAME", $name_convert);  
//    if(empty($array_loc))
//     $array_loc= array_search("NAME", $name_convert);  
//    if(!empty($array_loc))
//    {   $name_flag = 1;   
//        unset($parse_val[$array_loc]);
//    }else{
//        $name_flag = 0;
//    }
//
//    $array_loc= array_search("CNIC", $name_convert);                                                    
//    if(!empty($array_loc) || strlen($array_loc)==1)                        
//    unset($parse_val[$array_loc]);                         
//    $array_loc= array_search("CNICNO", $name_convert);                                                    
//    if(!empty($array_loc) || strlen($array_loc)==1)                        
//    unset($parse_val[$array_loc]);                         
//
//    $parse_val = array_values($parse_val);  
//}
//
//
//$size_of_array= sizeof($parse_val); 
//
//if(sizeof($parse_val)==1 && strlen($parse_val[0]>14))
//{
//    
//    $parse_val = array_map('trim',array_filter(explode(' ',strip_tags($receive_body_helper))));
//    
//    $array_parse = array();
//    foreach($parse_val as $parse)
//    {                            
//        if(strlen(trim($parse))>=2 && ctype_digit(trim($parse)))
//            $array_parse[]=trim($parse);
//
//    }                        
//    $parse_val=$array_parse; 
//}elseif(sizeof($parse_val)==3 && strlen($parse_val[0])==12)    
//{
//    if($parse_val[0]==$data['requested_value'])
//    {
//        $loc_data['cnicsims'] = $data['requested_value'];
//        $loc_data['mobile_number'][0] = substr(trim($parse_val[1]), -10);
//        $parse_val='';
//    }else{
//        if(sizeof($parse_val)<2)
//        {    
//            $loc_data['cnicsims'] = $data['requested_value'];
//            $loc_data['mobile_number'][0] = substr(trim($parse_val[0]), -10);
//            $parse_val='';
//        }
//    }  
//        
//}   
//
//$size_of_array= sizeof($parse_val); 
//
//if(!empty($parse_val[0]))
//$loc_data['cnicsims']=trim($parse_val[0]);
//
//for($i=0; $i<$size_of_array;)
//{
//    if(ctype_digit(trim($parse_val[$i])))
//    $loc_data['mobile_number'][]= substr(trim($parse_val[$i]), -10);
//    $i++;    
//    if($name_flag == 1)
//        $i++;        
//
//}
//$loc_data['cnicsims'] = $data['requested_value'];
//}    
////echo '<pre>';
////print_r($parse_val);
////print_r($loc_data);
////print_r($loc_data['mobile_number']);
////print_r($data);
////exit;
//
//if(!empty($loc_data['mobile_number']) && (sizeof($loc_data['mobile_number'])==1) && (ctype_digit($loc_data['mobile_number'][0])==0) && (strpos((trim(trim($parse_val_help[0]))),trim($loc_data['mobile_number'][0])) !== false ))
//{
//    $loc_data['mobile_number'] = array();
//    for($i=1; $i<sizeof($parse_val_help); $i++)
//    {
//        if(ctype_digit(trim($parse_val_help[$i])))
//        $loc_data['mobile_number'][]= substr(trim($parse_val_help[$i]), -10);
//        
//    } 
//}
//
//if(!empty($loc_data['mobile_number']) && sizeof($loc_data['mobile_number'])>1)
//    $loc_data['mobile_number']=array_unique($loc_data['mobile_number']);                        
//}
//

?>                        