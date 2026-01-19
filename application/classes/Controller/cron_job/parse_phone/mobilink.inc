<?php

if(!empty($phone_data['is_manual']) && $phone_data['is_manual']==1)
    $is_manual = 'manual';
else
    $is_manual = 'mail';

$ext = pathinfo($data['received_file_path'], PATHINFO_EXTENSION);



try{
if(!empty($data['received_file_path']) && $data['received_file_path'] != 'na')
     {
         if($ext == 'zip')
         {    $file =  !empty($data['id']) ? Helpers_Upload::get_request_data_path($data['id'], 'save') . DIRECTORY_SEPARATOR . $data['received_file_path']: '';
              //$file = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . $is_manual . DIRECTORY_SEPARATOR . $data['received_file_path'];
              $file_name = Helpers_Upload::unzip_file($file, $data['request_id']);
              //$file = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR . 'cdr' . DIRECTORY_SEPARATOR . $is_manual . DIRECTORY_SEPARATOR . $file_name;
              $file =  !empty($data['id']) ? Helpers_Upload::get_request_data_path($data['id'], 'save') . DIRECTORY_SEPARATOR . $file_name: '';
             echo $phone_data['path'] = $file;
              $result= Helpers_Upload::data_mapping($phone_data['path'], $phone_data['company_name'], $phone_data['phone_number'], $phone_data['userrequestid'], $data['id'] );
              unlink($file);
         }elseif($ext == 'rar')
         {                  
              $file =  !empty($data['id']) ? Helpers_Upload::get_request_data_path($data['id'],'save') . $data['received_file_path']: '';
              $file_name = Helpers_Upload::unziprar_file($file, $phone_data['userrequestid']);
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
            include APPPATH . 'classes' . DS . 'Controller' . DS . 'cron_job' . DS . 'parse_sub' . DS . 'notfound.inc';
     }
     
}  catch (Exception $e)
{
    $reference_number = $data['request_id'];  
    $reference_number = Model_Email::email_status($reference_number, 2, 3); 
    exit;
}