<?php

if(!empty($data['is_manual']) && $data['is_manual']==1)
    $is_manual = 'manual';
else
    $is_manual = 'mail';


$ext = pathinfo($data['received_file_path'], PATHINFO_EXTENSION);
 
if (!empty($data['received_file_path']) && $data['received_file_path'] != 'na') {
    if ($ext == 'zip') {
        $file =  !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($phone_data['file_id'], 'save') . $data['received_file_path']: '';

        $file_name = Helpers_Upload::unzip_file($file, $data['request_id']);
        $file = !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($phone_data['file_id'], 'save') . $file_name: '';
        $path = $file;
        $company = $data['company_name'];
        $field_imei_no = $data['requested_value'];
        $userrequestid = $data['request_id'];
        //$file_id_index = Helpers_Upload::upload_file_record($file_name, $data['user_id'], $data['user_request_type_id'], $data['company_name'], $data['requested_value'], $data['request_id']);
        //$file_id = $file_id_index[0];
        $file_id=$data['file_id'];
                
        $result = Helpers_Upload::data_mapping_partially($path, $company, $field_imei_no, $file_id, $userrequestid);
        unlink($file);
    } elseif ($ext == 'rar') {
        $file =  !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($phone_data['file_id'], 'save') . $data['received_file_path']: '';

        $file_name = Helpers_Upload::unziprar_file($file, $data['request_id']);
        $file = !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($phone_data['file_id'], 'save') . $file_name: '';
        $path = $file;
        // echo $path;
        //echo $file_name;

        $company = $data['company_name'];
        $field_imei_no = $data['requested_value'];
        $userrequestid = $data['request_id'];
        //$file_id_index = Helpers_Upload::upload_file_record($file_name, $data['user_id'], $data['user_request_type_id'], $data['company_name'], $data['requested_value'], $data['request_id']);
        //$file_id = $file_id_index[0];
        $file_id=$data['file_id'];
        $result = Helpers_Upload::data_mapping_partially($path, $company, $field_imei_no, $file_id, $userrequestid);
        unlink($file);
        exit;
    } else {
        $company = $data['company_name'];
        $field_imei_no = $data['requested_value'];
        $userrequestid = $data['request_id'];
        //$file_id_index = Helpers_Upload::upload_file_record($file_name, $data['user_id'], $data['user_request_type_id'], $data['company_name'], $data['requested_value'], $data['request_id']);
        //$file_id = $file_id_index[0];
         $path =  !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($phone_data['file_id'], 'save') . $data['received_file_path']: '';

        $file_id=$data['file_id'];
        $result = Helpers_Upload::data_mapping_partially($path, $company, $field_imei_no, $file_id, $userrequestid);
        
    }
} else {
    $data['received_body'] = array_filter(explode('From:', strip_tags($data['received_body'])));
    if (strpos(strtolower($data['received_body'][0]), 'not found') !== false ||
            strpos(strtolower($data['received_body'][0]), 'format error') !== false
           || strpos(strtolower($data['received_body'][0]), 'no record found') !== false) {
        $reference_number = $data['request_id'];
        
        Model_ErrorLog::log(
            'parse_imei_warid',
            'IMEI not found in response - marking as status 5 (Not Found)',
            [
                'request_id' => $reference_number,
                'company_name' => $data['company_name'] ?? null,
                'user_request_type_id' => $data['user_request_type_id'] ?? null,
                'processing_index' => 5,
                'body_sample' => substr($data['received_body'][0] ?? '', 0, 200)
            ],
            null,
            'not_found',
            'imei_parsing',
            'success'
        );
        
        $reference_number = Model_Email::email_status($reference_number, 2, 5);
        $not_fount = 1;                                
    } else {
        $reference_number = $data['request_id'];
        
        Model_ErrorLog::log(
            'parse_imei_warid',
            'Unable to parse IMEI response - marking as status 3 (Error)',
            [
                'request_id' => $reference_number,
                'company_name' => $data['company_name'] ?? null,
                'user_request_type_id' => $data['user_request_type_id'] ?? null,
                'processing_index' => 3,
                'body_sample' => substr($data['received_body'][0] ?? '', 0, 200),
                'reason' => 'Unexpected response format for IMEI parsing'
            ],
            null,
            'parsing_error',
            'imei_parsing'
        );
        
        $reference_number = Model_Email::email_status($reference_number, 2, 3);
    }
}