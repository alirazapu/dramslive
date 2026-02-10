<?php
$encodings = [
  "ASCII",
  "JIS",
  "EUC-JP"
];
$encode_str= mb_detect_encoding($data['received_body'], $encodings);
$encode_str_raw= mb_detect_encoding($data['received_body_raw'], $encodings);
 if($encode_str=='ASCII'){  
      $data['received_body'] = ($data['received_body_raw']);
 }

$body_check = $data['received_body'];


/* if(empty($body_check) && (sizeof($body_check)==1))
  $body_check = imap_base64($data['received_body_raw']);
  if(empty($body_check) && (sizeof($body_check)==1))
  $body_check = $data['received_body'];
 */

$data['received_body'] = str_replace('Sent through ActiveXperts SendEMail', '', $body_check);
//$data['received_body'] = str_replace('-','',$data['received_body']);
//$data['received_body']= $body_check; 



if (strpos(strtolower($data['received_body']), 'not reachable') !== false ||
        strpos(strtolower($data['received_body']), 'unknownsubscriber') !== false ||
        strpos(strtolower($data['received_body']), 'please try again later') !== false ||
        strpos(strtolower($data['received_body']), 'cannot be retrieved') !== false ||
        strpos(strtolower($data['received_body']), '---- forwarded message ----') !== false ||
        strpos(strtolower($data['received_body']), 'loc not received') !== false ||
        strpos(strtolower($data['received_body']), 'try again later') !== false ||
        strpos(strtolower($data['received_body']), 'port out') !== false ||
        strpos(strtolower($data['received_body']), 'not related to') !== false ||
        strpos(strtolower($data['received_body']), 'we are looking into this') !== false ||
        strpos(strtolower($data['received_body']), 'purged') !== false || empty($data['received_body']) || $data['received_body']==' ' || strtolower($data['received_body'])=='na'
) {
    $reference_number = $data['request_id'];
    
    Model_ErrorLog::log(
        'parse_location_telenor',
        'No location data in response - marking as status 5 (Not Found)',
        [
            'request_id' => $reference_number,
            'company_name' => $data['company_name'] ?? 2,
            'user_request_type_id' => $data['user_request_type_id'] ?? null,
            'processing_index' => 5,
            'body_empty' => empty($data['received_body']),
            'body_sample' => substr($data['received_body'] ?? '', 0, 100)
        ],
        null,
        'not_found',
        'location_parsing','success'
    );
    
    $reference_number = Model_Email::email_status($reference_number, 2, 5);
    $not_fount = 1;
} else {

    $parse_val = array_map('trim', array_filter(explode(PHP_EOL, strip_tags($data['received_body']))));

    $parse_final = array();

    foreach ($parse_val as $key => $value) {
        if (!empty($parse_val[$key]) && strlen(trim($parse_val[$key])) > 2) {
            //echo strlen($parse_val[$key]);
            $parse_final[] = trim($parse_val[$key]);
        }
    }
   $parse_val = implode(' ', $parse_final);
   $parse_val = str_replace('>', '', $parse_val);
   $parse_val = str_replace(',', ' ', $parse_val);

 


    $check_parse_val = array_map('trim', array_filter(explode(' ', $parse_val)));




    if (strtolower($check_parse_val[0]) == 'msisdn:' && strtolower($check_parse_val[2]) == 'imei:' && strtolower($check_parse_val[4]) == 'imsi:') {
        $reference_number = Model_Email::email_status($data['request_id'], 1, 0);
        $not_fount = 1;
        // exit;
    }
    $count = 0;
    $loc_data['loclat'] = 0;
    $loc_data['loclong'] = 0;
    $loc_data['loclac'] = '';
    $loc_data['loccellid'] = '';
$temcoordinates=[];
    foreach ($check_parse_val as $parse) {
        
            $indexKey=array_search($parse, $check_parse_val);

       
          
        if (!empty($parse) && $count == 0) {

            if (ctype_digit($parse)) {

                $loc_data['locationmsisdn'] = substr(trim($parse), -10);

                if (($key = array_search($parse, $check_parse_val)) !== false) {
                    unset($check_parse_val[$key]);
                }
                
        $count = $count + 1;
                
            }
            
        }
        
        
        if (strpos(strtolower(trim(($parse))), 'lat') !== false && strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), '=') !== false ) {
          $loc_data['loclat'] = !empty($check_parse_val[$indexKey + 2]) ? $check_parse_val[$indexKey + 2] :'';
            
        }elseif (strpos(strtolower(trim(($parse))), 'lat') !== false && strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), '.') !== false && !empty(is_float($check_parse_val[$indexKey + 1]))) {
          $loc_data['loclat'] = !empty($check_parse_val[$indexKey + 1]) ? $check_parse_val[$indexKey + 1] :'';
            
        }
        
        if (strpos(strtolower(trim(($parse))), 'long') !== false && strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), '=') !== false) {
          $loc_data['loclong'] = !empty($check_parse_val[$indexKey + 2]) ? $check_parse_val[$indexKey + 2] :'';
            
        }elseif (strpos(strtolower(trim(($parse))), 'long') !== false && strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), '.') !== false && !empty(is_float($check_parse_val[$indexKey + 1]))) {
          $loc_data['loclong'] = !empty($check_parse_val[$indexKey + 1]) ? $check_parse_val[$indexKey + 1] :'';
            
        }
        
                
        if(!empty(trim($parse)) && is_numeric(trim($parse))  && trim($parse) > 23 && trim($parse) < 37){
          
           $loc_data['loclat'] = trim($parse);
        }
        if(!empty(trim($parse))  && is_numeric(trim($parse))  && trim($parse) > 61 && trim($parse) < 77){
           $loc_data['loclong'] = trim($parse);
        }
        
        if (strpos(strtolower(trim(($parse))), 'lac') !== false && strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), '=') !== false) {
            $check_parse_val[$indexKey + 2] = str_replace('.', '', $check_parse_val[$indexKey + 2]);
              $loc_data['loclac'] = !empty($check_parse_val[$indexKey + 2]) ? $check_parse_val[$indexKey + 2] :'';
            
        }
        
        if (strpos(strtolower(trim(($parse))), 'cellid') !== false && strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), '=') !== false) {
            $check_parse_val[$indexKey + 2] = str_replace('.', '', $check_parse_val[$indexKey + 2]);
              $loc_data['loccellid'] = !empty($check_parse_val[$indexKey + 2]) ? $check_parse_val[$indexKey + 2] :'';
            
        }
        
        if (strpos(strtolower(trim(($parse))), 'site:') !== false ) {
                    if (strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), '2g') !== false) {
                        $loc_data['locnetwork'] = 1;
                    } elseif (strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), '3g') !== false) {
                        $loc_data['locnetwork'] = 2;
                    } elseif ((strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), '4g') !== false) || (strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), 'lte') !== false)) {
                        $loc_data['locnetwork'] = 3;
                    } else {
                        $loc_data['locnetwork'] = 0;
                    }

                }
        
        if (strpos(strtolower(trim(($parse))), '3.') !== false && strpos(strtolower(trim(($parse))), '-7') !== false) {

            $helper = array_filter(explode('-', strip_tags($parse)));
            $loc_data['loclat'] = min($helper);
            $loc_data['loclong'] = max($helper);


            if (!empty($check_parse_val[$indexKey + 1]) && strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), 'l') !== false && strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), '-') !== false) {
                $check_parse_val[$indexKey + 1] = str_replace('L', '', $check_parse_val[$indexKey + 1]);
                $check_parse_val[$indexKey + 1] = str_replace('C', '', $check_parse_val[$indexKey + 1]);
                $helper = array_filter(explode('-', strip_tags($check_parse_val[$indexKey + 1])));
                $loc_data['loclac'] = $helper[0];
                $loc_data['loccellid'] = $helper[1];

                if (!empty($check_parse_val[$indexKey + 2]) && strpos(strtolower(trim(($check_parse_val[$indexKey + 2]))), 'site') !== false) {
                    if (strpos(strtolower(trim(($check_parse_val[$indexKey + 2]))), '2g') !== false) {
                        $loc_data['locnetwork'] = 1;
                    } elseif (strpos(strtolower(trim(($check_parse_val[$indexKey + 2]))), '3g') !== false) {
                        $loc_data['locnetwork'] = 2;
                    } elseif ((strpos(strtolower(trim(($check_parse_val[$indexKey + 2]))), '4g') !== false) || (strpos(strtolower(trim(($check_parse_val[$indexKey + 2]))), 'lte') !== false)) {
                        $loc_data['locnetwork'] = 3;
                    } else {
                        $loc_data['locnetwork'] = 0;
                    }

                    if (($key = array_search($check_parse_val[$indexKey + 2], $check_parse_val)) !== false) {
                        unset($check_parse_val[$key]);
                    }
                }




                if (($key = array_search($check_parse_val[$indexKey + 1], $check_parse_val)) !== false) {
                    unset($check_parse_val[$key]);
                }
            }


            if (($key = array_search($parse, $check_parse_val)) !== false) {
                unset($check_parse_val[$key]);
            }
        }
        
        
        if(!empty($loc_data['loclong']) && empty($loc_data['loclac']) && empty($loc_data['loccellid'])){
                  $indexKey=array_search($loc_data['loclong'], $check_parse_val);
                
 if (!empty($check_parse_val[$indexKey + 1]) && strpos(strtolower(trim(($check_parse_val[$indexKey + 1]))), 'l') !== false && strpos(strtolower(trim(($check_parse_val[$indexKey + 2]))), 'c') !== false) {
              $loc_data['loclac'] = str_replace('L', '', $check_parse_val[$indexKey + 1]);
                $loc_data['loccellid'] = str_replace('C', '', $check_parse_val[$indexKey + 2]);

                if (!empty($check_parse_val[$indexKey + 3]) && strpos(strtolower(trim(($check_parse_val[$indexKey + 3]))), 'site') !== false) {
                    if (strpos(strtolower(trim(($check_parse_val[$indexKey + 3]))), '2g') !== false) {
                        $loc_data['locnetwork'] = 1;
                    } elseif (strpos(strtolower(trim(($check_parse_val[$indexKey + 3]))), '3g') !== false) {
                        $loc_data['locnetwork'] = 2;
                    } elseif ((strpos(strtolower(trim(($check_parse_val[$indexKey + 3]))), '4g') !== false) || (strpos(strtolower(trim(($check_parse_val[$indexKey + 3]))), 'lte') !== false)) {
                        $loc_data['locnetwork'] = 3;
                    } else {
                        $loc_data['locnetwork'] = 0;
                    }

                }

            }
       
        }


    }
    if (strpos(strtolower(trim(($parse_val))), 'attached')) {
        $loc_data['locstatus'] = 1;
    } elseif (strpos(strtolower(trim(($parse_val))), 'deattach')) {
        $loc_data['locstatus'] = 0;
    } elseif (strpos(strtolower(trim(($parse_val))), 'purged')) {
        $loc_data['locstatus'] = 2;
    } else {
        $loc_data['locstatus'] = 0;
    }
    $loc_data['locaddress'] = implode(' ', $check_parse_val);

    if(!empty($temcoordinates)){
    $loc_data['loclat']=min($temcoordinates);
        $loc_data['loclong']=max($temcoordinates);
    }


    if (!empty($login_user->id) && $login_user->id == 137) {
//        echo '<pre>';
////        print_r($parse_final);
//        print_r($check_parse_val);
//        print_r($loc_data);
//        exit;
    }
}