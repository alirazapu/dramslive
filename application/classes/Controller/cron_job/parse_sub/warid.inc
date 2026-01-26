<?php

if($data['received_file_path']!='na')
{
    //print_r($data['received_file_path']); $data['file_id']
    //$path = DOCROOT. 'uploads' . DIRECTORY_SEPARATOR. 'cdr' . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . $data['received_file_path'];
    $path =  !empty($data['file_id']) ? Helpers_Upload::get_request_data_path($data['file_id'],'save') . $data['received_file_path']: '';

    $cnic_data = Helpers_Upload::warid_sub_pars($path);


    $count = 1;
    foreach ($cnic_data[2] as $parse)
    {
        /*if($count==1)                            
        $active=$parse;*/
        if($count==1)                            
        $mobile_number=substr(trim($parse), -10);
        elseif($count==2)
        $name= $parse;
        elseif($count==4)
        $cnic= $parse;  
        elseif($count==5)
        {
            if(trim($parse)=='Connected')
                $status= 1;                                    
            else 
                $status= 0;

        }
        elseif($count==8)
        $date= $parse;                                         

        elseif($count==6)
        $address= $parse;  
        elseif($count==7)
        $address.= ' ' . $parse;  

        $count= $count + 1;
    }


}else{
    //$data['received_body'] = str_replace('*SUBNO*','',$data['received_body']); 
    $data['received_body'] = array_filter(explode('From:',strip_tags($data['received_body'])));
    $data['received_body'] = $data['received_body'][0];
    $parse_val = array_filter(explode(',',strip_tags($data['received_body'])));

    if (strpos(strtolower($data['received_body']), 'no data found') !== false) {
            $reference_number = $data['request_id'];
            
            Model_ErrorLog::log(
                'parse_sub_warid',
                'No data found message detected in response - marking as status 5 (Not Found)',
                [
                    'request_id' => $reference_number,
                    'company_name' => $data['company_name'] ?? 'warid',
                    'user_request_type_id' => $data['user_request_type_id'] ?? null,
                    'processing_index' => 5,
                    'body_sample' => substr($data['received_body'], 0, 200),
                    'reason' => 'Response contains "no data found" message'
                ],
                null,
                'not_found',
                'subscriber_parsing'
            );
            
            $reference_number = Model_Email::email_status($reference_number, 2, 5);
            $not_fount = 1;
         }else{
    $count = 1;
    foreach ($parse_val as $parse)
    {
        if($count==1)                            
        $active=$parse;
        elseif($count==2)                            
        $mobile_number=substr(trim($parse), -10);
        elseif($count==3)
        $name= $parse;
        elseif($count==4)
        $status= $parse;                                         
        elseif($count==5)
        $date= $parse;                                         
        elseif($count==6)
        $cnic= $parse;                                         
        elseif($count==7)
        $address= $parse;  
        elseif($count==8)
        $address.= ' ' . $parse;  

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
?>