<?php
switch ($company_name) {
    case 1: // Mobilink
        $email_config = Helpers_CompanyEmail::get_email(1, $res['user_request_type_id'] ?? null);
        $to = $email_config['email'];
        $to_name = $email_config['name'];
        break;
    case 3: // Ufone
        $email_config = Helpers_CompanyEmail::get_email(3, $res['user_request_type_id'] ?? null);
        $to = $email_config['email'];
        $to_name = $email_config['name'];
        break;
    case 4: // Zong
        $email_config = Helpers_CompanyEmail::get_email(4, $res['user_request_type_id'] ?? null);
        $to = $email_config['email'];
        $to_name = $email_config['name'];
        break;
    case 6: // Telenor
        $email_config = Helpers_CompanyEmail::get_email(6, $res['user_request_type_id'] ?? null);
        $to = $email_config['email'];
        $to_name = $email_config['name'];
        break;
    case 7: // Warid 3 = subscriber , 4 = location ,  5, cnic
        $email_config = Helpers_CompanyEmail::get_email(7, $res['user_request_type_id'] ?? null);
        $to = $email_config['email'];
        $to_name = $email_config['name'];
        break;
    case 11:
        $email_config = Helpers_CompanyEmail::get_email(11, $res['user_request_type_id'] ?? null);
        $to = $email_config['email'];
        $to_name = $email_config['name'];
        break;
    case 12:
        $email_config = Helpers_CompanyEmail::get_email(12, $res['user_request_type_id'] ?? null);
        $to = $email_config['email'];
        $to_name = $email_config['name'];
        break;
    case 8:
        $email_config = Helpers_CompanyEmail::get_email(8, $res['user_request_type_id'] ?? null);
        $to = $email_config['email'];
        $to_name = $email_config['name'];
        break;
    case 13:
        $email_config = Helpers_CompanyEmail::get_email(13, $res['user_request_type_id'] ?? null);
        $to = $email_config['email'];
        $to_name = $email_config['name'];
        break;

}

?>