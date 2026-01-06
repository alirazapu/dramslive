<?php
$encode_str= mb_detect_encoding(base64_decode($data['received_body']));

if($encode_str=='ASCII')
    $data['received_body'] = base64_decode($data['received_body']);    


$data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));


// if(!empty($login_user->id) && $login_user->id==138){
//         echo '<pre>'; 
////         print_r(($data));         
//         print_r(($data['received_body']));         
//          exit;
////         
//          }
          
include 'notfound.inc';
 
 if ($not_fount!=1){

    $data['received_body'] = $data['received_body'][0];
    if (strpos(strtolower($data['received_body']), '<style>') !== false)
    $data['received_body'] = preg_replace("/.*<body[^>]*>|<\/body>.*/si", "", $data['received_body']);
    
    if (strpos($data['received_body'], 'MARGIN') !== false) {
        $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body_raw'])));
        $data['received_body'] = $data['received_body'][0];
        include 'notfound.inc';
    }
    $data['received_body']= is_array($data['received_body'])?$data['received_body'][0]:$data['received_body'];
    /* Replace String */
    include 'replace.inc';        
    $data['received_body'] = str_replace('`','',$data['received_body']);        
    $data['received_body'] = str_replace('_','',$data['received_body']);        
    $data['received_body'] = str_replace('#','',$data['received_body']);
    $data['received_body'] = str_replace('see.','',$data['received_body']);
    $data['received_body'] = str_replace('SEE THE DETAILS.','',$data['received_body']);
    $data['received_body'] = str_replace('WITH PROFOUND REGARDS','', strtoupper($data['received_body']));
    $data['received_body'] = str_replace('&NBSP;','',$data['received_body']);   
    $data['received_body'] = str_replace('SENT ON THE MOVE, POWERED BY','',$data['received_body']);   
    $data['received_body'] = str_replace('UFONE','',$data['received_body']);   
    $data['received_body'] = str_replace('SA BUKHARI','', strtoupper($data['received_body']));
    $data['received_body'] = str_replace('PFB DETAILS','', strtoupper($data['received_body']));
    $data['received_body'] = str_replace('.','', strtoupper($data['received_body']));
    
    $array_position = array_map('trim', array_filter(explode(PHP_EOL,strip_tags($data['received_body']))));
    $array_position = array_map("strtoupper", $array_position);

     $array_parse = array();
    foreach($array_position as $parse)
    {                            
        if(strlen($parse)>=2)
            $array_parse[]=$parse;

    } 
    $array_position = $array_parse;
    
        
    $array_mob = array_search("CNIC NO", $array_position);
    if(empty($array_mob))
        $array_mob = array_search("CNIC", $array_position);
    $array_nam = array_search("NAME", $array_position);

    $data['received_body'] = str_replace('Name','',$data['received_body']);   

    if( strpos( $data['received_body'], 'details.' ) !== false )
    {
        $r_body = array_filter(explode('details.',strip_tags($data['received_body'])));
        $data['received_body'] = $r_body[1];                            
    }


    $data['received_body'] = str_replace(':','',$data['received_body']);   
    $data['received_body'] = str_replace('check','',$data['received_body']);       
    $data['received_body'] = str_replace('NUMBER','',$data['received_body']);   
    $data['received_body'] = str_replace('.','',$data['received_body']);   
    $data['received_body'] = str_replace('`','',$data['received_body']);   
    $data['received_body'] = str_replace('Person_','',$data['received_body']);   
    $data['received_body'] = str_replace('*','',$data['received_body']);   
    $data['received_body'] = str_replace('*','',$data['received_body']);   
    //$data['received_body'] = str_replace(' ','',$data['received_body']);                              
    $data['received_body'] = str_replace('MSISDN','',$data['received_body']);                        
    $data['received_body'] = str_replace('NO','',$data['received_body']);                        
    $data['received_body'] = str_replace('NAME','',$data['received_body']);                        
    $data['received_body'] = str_replace('CNIC','',$data['received_body']);                        
    $data['received_body'] = str_replace('NIC','',$data['received_body']);                        
    $data['received_body'] = str_replace('CUSTOMER','',$data['received_body']);                            
    $data['received_body'] = str_replace('-','',$data['received_body']);                        
    $data['received_body'] = str_replace('–','',$data['received_body']);    
    $data['received_body'] = str_replace('With Profound Regards','',$data['received_body']);                        
    $data['received_body'] = str_replace('SA Bukhari','',$data['received_body']);   
    $parse_val = array_map('trim', array_filter(explode(PHP_EOL,strip_tags($data['received_body']))));


    $array_parse = array();
    foreach($parse_val as $parse)
    {                            
        if(strlen($parse)>=2)
            $array_parse[]=$parse;

    }    
    
    if(sizeof($array_parse)==2 && !ctype_digit($array_parse[0]) && strlen($array_parse[1])==13)
    {
        $cnic = trim($array_parse[1]);
        $mobile_number=$data['requested_value'];
    }elseif(sizeof($array_parse)==3 && !ctype_digit($array_parse[0]) && strlen($array_parse[1])==13)
    {
        
        $cnic = trim($array_parse[1]);
        $mobile_number=$data['requested_value'];
        $address = trim($array_parse[2]);
    }else{        
    
        if(sizeof($array_parse)==1 && strlen($array_parse[0])>14)
        {

            $array_parse = array_map('trim',array_filter(explode(' ',strip_tags($array_parse[0]))));
            $array_helper = array();
            $name_value = '';
            foreach($array_parse as $parse)
            {   

                if(strlen(trim($parse))>=2)
                {  if(ctype_digit(trim($parse)))
                    $array_helper[]=trim($parse);
                    else     
                    $name_value .=  ' ' .trim($parse);    
                }

            }                                                    
            $array_helper[] = trim($name_value);
            $array_parse = $array_helper;
        }


        if(empty($array_mob) && empty($array_nam))
        {
            if(!empty($array_parse[1]) && ctype_digit($array_parse[1]))
            {    $array_mob = 1;
                 $array_nam = 2;
            }else{
                $array_mob = 2;
                 $array_nam = 1;
            }

        }   

        $count = 0;          
        if(sizeof($array_parse)==2)
        {
            if(ctype_digit($array_parse[0]))
            {
                $name = $array_parse[1];
                $cnic = trim($array_parse[0]);
                
            }else{
                $name = $array_parse[0];
                $cnic = trim($array_parse[1]);                
            } 
            $mobile_number= $data['requested_value'];
            
        }else{    
        foreach ($array_parse as $parse)
            {
            if($count==0)
            $mobile_number=substr(trim($parse), -10);

            if($array_mob> $array_nam)                                
            {   if($count==1)
                $name= $parse;
                elseif($count==2)
                $cnic= $parse;                                         
            }else{
                if($count==2)
                $name= $parse;
                elseif($count==1)
                $cnic= $parse;                                         
            }

            $count= $count + 1;
        }  
        }
    }
    
        
    if(!ctype_digit(trim($cnic)) && strlen(trim($cnic))==13)
    {     
        $cnic_original = trim($cnic);     
        //$cnic = Helpers_Utilities::check_digit(trim($cnic));
        $is_foreigner= 1;     
    }
}


?>