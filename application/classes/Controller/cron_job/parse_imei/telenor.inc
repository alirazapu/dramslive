<?php

if(!empty($data['is_manual']) && $data['is_manual']==1)
    $is_manual = 'manual';
else
    $is_manual = 'mail';

$ext = pathinfo($data['received_file_path'], PATHINFO_EXTENSION);

if (!empty($data['received_file_path']) && $data['received_file_path'] != 'na') {
    if ($ext == 'zip' || $ext == 'rar') {
        $file =  !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($phone_data['file_id'], 'save') . $data['received_file_path']: '';
        //$file_name = Helpers_Upload::unzip_file($file);
        $file_name = Helpers_Upload::unzip_file_multiple($file, $phone_data['phone_number']);
        $file =  !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($phone_data['file_id'], 'save') . $file_name: '';
        $path = trim($file);

        $company = $data['company_name'];
        $field_imei_no = $data['requested_value'];
        $userrequestid = $data['request_id'];
        //$file_id_index = Helpers_Upload::upload_file_record($file_name, $data['user_id'], $data['user_request_type_id'], $data['company_name'], $data['requested_value'], $data['request_id']);
        //$file_id = $file_id_index[0];
        $file_id=$data['file_id'];
        chmod($path, 0755);

        $result = Helpers_Upload::data_mapping_partially($path, $company, $field_imei_no, $file_id, $userrequestid);
        unlink($file);
    } else {
        
        $path =  !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($phone_data['file_id'], 'save') . $data['received_file_path']: '';
        $company = $data['company_name'];
        $field_imei_no = $data['requested_value'];
        $userrequestid = $data['request_id'];
        //$file_id_index = Helpers_Upload::upload_file_record($file_name, $data['user_id'], $data['user_request_type_id'], $data['company_name'], $data['requested_value'], $data['request_id']);
        //$file_id = $file_id_index[0];
        $file_id=$data['file_id'];
        $result = Helpers_Upload::data_mapping_partially($path, $company, $field_imei_no, $file_id, $userrequestid);
        
    }
} else {

    $data['received_body'] = array_filter(explode('From:', strip_tags($data['received_body'])));
    if (strpos(strtolower($data['received_body'][0]), 'na') !== false ||
            strpos(strtolower($data['received_body'][0]), 'no data found') !== false ||
            strpos(strtolower($data['received_body'][0]), 'no data') !== false ||
            strpos(strtolower($data['received_body'][0]), 'nil') !== false) {
        $reference_number = $data['request_id'];
        $reference_number = Model_Email::email_status($reference_number, 2, 5);
        $not_fount = 1;        
    } else {
        $reference_number = $data['request_id'];
        $reference_number = Model_Email::email_status($reference_number, 2, 3);
    }
}