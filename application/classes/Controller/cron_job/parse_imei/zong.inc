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
    $rawBody = isset($data['received_body']) ? (string) $data['received_body'] : '';
    $plainBody = strip_tags($rawBody);
    $lowerBody = strtolower($plainBody);

    if (
        strpos($lowerBody, 'not found') !== false ||
        strpos($lowerBody, 'format error') !== false ||
        strpos($lowerBody, 'no record found') !== false ||
        strpos($lowerBody, 'no data found') !== false
    ) {
        $reference_number = isset($data['request_id']) ?$data['request_id']: null;

        Model_ErrorLog::log(
            'parse_imei_zong',
            'IMEI not found in response - marking as status 5 (Not Found)',
            [
                'request_id'           => $reference_number,
                'company_name'         => isset($data['company_name'])?$data['company_name']:  null,
                'user_request_type_id' =>  isset($data['user_request_type_id'])?$data['user_request_type_id']:  null,
                'processing_index'     => 5,
                // store a small sample of the body for debugging
                'body_sample'          => substr($plainBody, 0, 500),
            ],
            null,
            'not_found',
            'imei_parsing'
        );

        // Update email status
        $reference_number = Model_Email::email_status($reference_number, 2, 5);
        $not_fount = 1; // (you may want to fix this typo to $not_found)
    } else {
        $reference_number = $data['request_id'];
        
        Model_ErrorLog::log(
            'parse_imei_zong',
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