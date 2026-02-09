<?php
if (strpos(strtolower($data['received_body'][0]), 'no data against') !== false || 
         strpos(strtolower($data['received_body'][0]), 'network error') !== false
         || strpos(strtolower($data['received_body'][0]), 'service unavailable') !== false
         || strpos(strtolower($data['received_body'][0]), 'nodata') !== false ||
            strpos(strtolower($data['received_body'][0]), 'sold on') !== false ||
            strpos(strtolower($data['received_body'][0]), 'not bvs verified') !== false
         || strpos(strtolower($data['received_body'][0]), 'is ported out') !== false ||
        strpos(strtolower($data['received_body'][0]), 'no data found') !== false ||
        //strpos(strtolower($data['received_body'][0]), 'nil') !== false ||
        strpos(strtolower($data['received_body'][0]), 'no record found') !== false
     || strpos(strtolower($data['received_body'][0]), 'no record found') !== false
     || strpos(strtolower($data['received_body'][0]), 'no data found') !== false
     || strpos(strtolower($data['received_body'][0]), 'no data is found') !== false
     || strpos(strtolower($data['received_body'][0]), 'no data found.') !== false
     || strpos(strtolower($data['received_body'][0]), 'not found') !== false
     || strpos(strtolower($data['received_body'][0]), 'disowned') !== false
     || strpos(strtolower($data['received_body'][0]), 'purged') !== false
     || strpos(strtolower($data['received_body'][0]), 'nill') !== false   
     || strpos(strtolower($data['received_body'][0]), 'no location is available') !== false   
     ||   strpos(strtolower($data['received_body'][0]), 'please always provide fir/case number') !== false 
     ||   strpos(strtolower($data['received_body'][0]), 'cannot be tracked right now') !== false 
     ||   strpos(strtolower($data['received_body'][0]), 'not belong to ufone') !== false 
     ||   strpos(strtolower($data['received_body'][0]), 'on telenor network') !== false 
     ||   strpos(strtolower($data['received_body'][0]), 'on zong network') !== false 
        ||   strpos(strtolower($data['received_body'][0]), 'port out') !== false 
     || strtolower(trim($data['received_body'][0]))=='na'   
     || strtolower(trim($data['received_body'][0]))=='nil' 
     || strtolower(trim($data['received_body'][0]))==' ' 
     || empty(trim($data['received_body'][0]))   
        ) {
   
             $reference_number = $data['request_id'];
             
             Model_ErrorLog::log(
                'parse_location_notfound',
                'No location data detected - marking as status 5 (Not Found)',
                [
                    'request_id' => $reference_number,
                    'company_name' => $data['company_name'] ?? null,
                    'user_request_type_id' => $data['user_request_type_id'] ?? null,
                    'processing_index' => 5,
                    'body_sample' => isset($data['received_body'][0]) ? substr($data['received_body'][0], 0, 100) : ''
                ],
                null,
                'not_found',
                'location_parsing','success'
            );
             
                 $reference_number = Model_Email::email_status($reference_number, 2, 5);
                 $not_fount = 1;                 
}

 ?>