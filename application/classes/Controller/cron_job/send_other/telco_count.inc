<?php


if ($email_staus == 1) {
    $query = DB::update('request_send_today')->set(array('total' => DB::expr('total + 1')))
        ->where('request_priority', '=', $res['request_priority'])
        ->and_where('company_name', '=', $res['company_name'])
        ->execute();  

    /* For Telco Report */
        /* check record exist or not */
        $date_for_telco = date('Y-m-d'); //$date;
        $tel_report = "select * from telco_request_summary where date = '{$date_for_telco}'";
        $sql_telco = DB::query(Database::SELECT, $tel_report);
        $report_telco_result = $sql_telco->execute()->as_array();

        if(!empty($report_telco_result))
        {    
            $tel_co_mnc = array(1,3,4,6,7,11,12,13);
            $telco_array= array();
            foreach($tel_co_mnc as $mnc)
            {    
                $company_mnc='';$send_high=0;$send_medium=0;$send_low=0;$total_send=0;$total_received=0; 
                $tel_query = "SELECT * FROM request_send_today where company_name = " . $mnc;                
                $sql = DB::query(Database::SELECT, $tel_query);
                $tel_result = $sql->execute()->as_array();

                $company_mnc=$mnc;
                foreach($tel_result as $tel)
                {
                    switch ($tel['request_priority']){
                        case 1:
                            $send_low += $tel['total'];
                            break;
                        case 2:
                            $send_medium += $tel['total'];
                            break;
                        case 3:
                            $send_high += $tel['total'];
                            break;
                     }                         

                }
                $t_date='"'. $date_for_telco . '"';
                $total_send = $send_high + $send_medium + $send_low;
                //$telco_array[]='('. $t_date .', '. $company_mnc .', '. $send_high .', '. $send_medium .', '. $send_low .', '. $total_send. ')';
                $query = DB::update('telco_request_summary')->set(array('send_high' => $send_high,
                    'send_medium'=> $send_medium, 'send_low'=>$send_low, 'total_send'=>$total_send))
                ->where('date', '=', $date_for_telco)   
                ->and_where('company_mnc', '=', $mnc)
                ->execute();     
            }
            //$query = 'INSERT INTO telco_request_summary (`date`, `company_mnc`, `send_high`, `send_medium`, `send_low`, `total_send`) VALUES '.implode(',', $telco_array);
            //sql = DB::query(Database::INSERT, $query)->execute(); 
        }
    /* For Telco Report */

    $reference_number_result = Model_Email::email_sending_status($reference_number, 1, 0);                        
} else {
    $reference_number_result = Model_Email::email_status($reference_number, 3, 1);
    //$reference_number = Model_Email::email_sended($to, $subject, $body, $reference_number, 0, 0);
}
if(isset($count))
$count = $count + 1; 

if(!empty($reference_number))
Helpers_Email::emailreadstatus($reference_number);

?>