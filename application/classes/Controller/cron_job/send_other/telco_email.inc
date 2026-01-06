<?php

switch ($company_name) {
case 1: // Mobilink 
    $to = 'leasupportteam@jazz.com.pk';
    $to_name = '';
    break;
case 3: // Ufone 
    if(($res['user_request_type_id']==6 || $res['user_request_type_id']==1 || $res['user_request_type_id']==2))
    {     
        $to = 'cdr.requests@ptclgroup.com';
//        $to = 'cdr.requests@ufone.com';
          $to_name = ''; //ufone auto for cdr msisdn/imei
    }elseif(($res['user_request_type_id']==4 || $res['user_request_type_id']==3))
    {     // $to = 'ufone.location@ufone.com';
            $to = 'ufone.location@ptclgroup.com';
          $to_name = ''; //ufone auto for cdr msisdn/imei
    }else {
        $to = 'racentral@ufone.com';
        $to_name = '';
    }     
    break;
case 4: // Zong 
    $to = 'reg@zong.com.pk';
    $to_name = '';
    break;
case 6: // Telenor 
    $is_second= Helpers_Utilities::get_telenor_flag();
    //if(($is_second==1) && ($res['user_request_type_id']==1 || $res['user_request_type_id']==2))
    if(($res['user_request_type_id']==3 || $res['user_request_type_id']==5 || $res['user_request_type_id']==1 || $res['user_request_type_id']==2))
    {     
        //$to = 'lea2@newsystem123.com';
          $to = 'lea.2@telenor.com.pk';
          $to_name = ''; //Law Enforcement Agency
    }elseif($res['user_request_type_id']==4  ){
     //   $to = 'lea1@newsystem123.com';    
        $to = 'lea.1@telenor.com.pk';    
        $to_name = '';
    }else {
        $to = 'lea@newsystem123.com';    
        $to_name = '';
    } 
      //  $to = 'lea@newsystem123.com';    
      //  $to_name = '';
    break;
case 7: // Warid 3 = subscriber , 4 = location ,  5, cnic
    if(($res['user_request_type_id']==5 || $res['user_request_type_id']==3 || $res['user_request_type_id']==4))
    {
        $to = 'leasupportteam@jazz.com.pk';
        $to_name = '';
    }else{
       // $to = 'waridlea@jazz.com.pk';
        $to = 'leasupportteam@jazz.com.pk';
        $to_name = '';
    }
    break;
case 11:
    $to = 'mega.radata@ptcl.net.pk';
    $to_name = 'MegaRAdata/PTCL';
    break;
case 12:
    $to = 'mega.radata@ptcl.net.pk';
    $to_name = 'MegaRAdata/PTCL';
    break;
case 8:
    $to = 'info.lea@sco.gov.pk';
    $to_name = 'scom';
    break;
case 13:
    $to = 'naumana.manzoor@nadra.gov.pk';
    $to_name = 'Nadra';
    break;

}

?>