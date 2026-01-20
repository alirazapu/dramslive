<?php

if(!empty($phone_data['is_manual']) && $phone_data['is_manual']==1)
    $is_manual = 'manual';
else
    $is_manual = 'mail';


$ext = pathinfo($data['received_file_path'], PATHINFO_EXTENSION);

if(!empty($data['received_file_path']) && $data['received_file_path'] != 'na')
{    
    if($ext == 'zip')
         {    $file =  !empty($data['id']) ? Helpers_Upload::get_request_data_path($data['id'], 'save') . $data['received_file_path']: '';
              //$file = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . $is_manual . DIRECTORY_SEPARATOR . $data['received_file_path'];
              $file_name = Helpers_Upload::unzip_file($file, $phone_data['userrequestid']);                                
              //$file = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . $is_manual . DIRECTORY_SEPARATOR . $file_name;
              $file =  !empty($data['id']) ? Helpers_Upload::get_request_data_path($data['id'], 'save') . $file_name: '';
              $phone_data['path'] = $file;                                
              $result= Helpers_Upload::data_mapping($phone_data['path'], $phone_data['company_name'], $phone_data['phone_number'], $phone_data['userrequestid'], $data['id'] );
              unlink($file);
         }elseif($ext == 'rar')
         {
              $file =  !empty($data['id']) ? Helpers_Upload::get_request_data_path($data['id'], 'save') . $data['received_file_path']: '';
              $file_name = Helpers_Upload::unziprar_file($file, $data['request_id']);                                
              $file =  !empty($data['id']) ? Helpers_Upload::get_request_data_path($data['id'], 'save') . $file_name: '';
              $phone_data['path'] = $file;
              $result= Helpers_Upload::data_mapping($phone_data['path'], $phone_data['company_name'], $phone_data['phone_number'], $phone_data['userrequestid'], $data['id']);
              unlink($file);
         }else{
             $phone_data['path'] =  !empty($data['id']) ? Helpers_Upload::get_request_data_path($data['id'], 'save') . $data['received_file_path']: '';
             //$phone_data['path'] = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . $is_manual . DIRECTORY_SEPARATOR . $data['received_file_path'];                                             
             $result= Helpers_Upload::data_mapping($phone_data['path'], $phone_data['company_name'], $phone_data['phone_number'], $phone_data['userrequestid'], $data['id']);
         }                            
}else{
       $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));                                 

       if (strpos(strtolower($data['received_body'][0]), 'not found') !== false || 
          strpos(strtolower($data['received_body'][0]), 'no record found') !== false || 
               strpos(strtolower($data['received_body'][0]), 'error') !== false) {
            $reference_number = $data['request_id'];
                
                // Log status update to 5 (Not Found)
                Model_ErrorLog::log(
                    'parse_phone_warid',
                    'No data found in email body - marking as status 5 (Not Found)',
                    [
                        'request_id' => $reference_number,
                        'company_name' => $phone_data['company_name'] ?? 'warid',
                        'user_request_type_id' => $phone_data['user_request_type_id'] ?? 2,
                        'processing_index' => 5,
                        'reason' => 'Email body contains "not found", "no record found", or "error"',
                        'body_sample' => substr($data['received_body'][0], 0, 200)
                    ],
                    null,
                    'not_found',
                    'phone_parsing'
                );
                
                $reference_number = Model_Email::email_status($reference_number, 2, 5);
                $not_fount = 1;                
        }else{
       $reference_number = $data['request_id'];
       
       // Log status update to 3 (Error)
       Model_ErrorLog::log(
           'parse_phone_warid',
           'Invalid or unparseable phone data format - marking as status 3 (Error)',
           [
               'request_id' => $reference_number,
               'company_name' => $phone_data['company_name'] ?? 'warid',
               'user_request_type_id' => $phone_data['user_request_type_id'] ?? 2,
               'processing_index' => 3,
               'reason' => 'Email body does not contain file attachment and content is not "not found" pattern',
               'body_sample' => substr($data['received_body'][0], 0, 200)
           ],
           null,
           'parsing_error',
           'phone_parsing'
       );
       
       $reference_number = Model_Email::email_status($reference_number, 2, 3);
       $not_fount = 1; 
        }
} 