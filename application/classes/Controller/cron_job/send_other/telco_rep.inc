<?php

$date = date('Y-m-d H:i:s'); 
        $date_1 = date('y-m-d'); 
        $date_2 = $date_1 . ' 00:00:00'; 
        $date_3 = $date_1 . ' 23:59:59'; 
        //$query = DB::delete('request_send_today')->execute();
        //$date = date('y-m-d'); 
        //$date = $date . ' 00:00:00'; 
        /* check today limit */
        $query = "SELECT * FROM request_send_today where created_at >='" . $date_2 . "' and created_at <='" . $date_3 . "'";
                
        $sql = DB::query(Database::SELECT, $query);
        $result = $sql->execute()->as_array();
        
        if(empty($result))
        { 
            /* For Telco Report */
                /* check record exist or not */
                $date_for_telco = date('Y-m-d'); //$date;
                $tel_report = "select * from telco_request_summary where date = '{$date_for_telco}'";
                $sql_telco = DB::query(Database::SELECT, $tel_report);
                $report_telco_result = $sql_telco->execute()->as_array();
                if(empty($report_telco_result))
                {    
                    $tel_co_mnc = array(1,3,4,6,7,11,12,13);
                    $telco_array= array();
                    foreach($tel_co_mnc as $mnc)
                    {    
                        $company_mnc=$mnc;$send_high=0;$send_medium=0;$send_low=0;$total_send=0;$total_received=0; 
                        $t_date='"'. $date_for_telco . '"';                        
                        $telco_array[]='('. $t_date .', '. $company_mnc .', '. $send_high .', '. $send_medium .', '. $send_low .', '. $total_send. ')';

                    }
                    $query = 'INSERT INTO telco_request_summary (`date`, `company_mnc`, `send_high`, `send_medium`, `send_low`, `total_send`) VALUES '.implode(',', $telco_array);
                    $sql = DB::query(Database::INSERT, $query)->execute(); 
                }
                
            /* For Telco Report */
            
           $query = DB::delete('request_send_today')->execute();           
            DB::insert('request_send_today', array('created_at', 'company_name', 'request_priority', 'total'))                
            ->values(array($date, 1, 1, 0),array($date, 1, 2, 0),array($date, 1, 3, 0),
                array($date, 7, 1, 0),array($date, 7, 2, 0),array($date, 7, 3, 0),
                array($date, 3, 1, 0),array($date, 3, 2, 0),array($date, 3, 3, 0),
                array($date, 6, 1, 0),array($date, 6, 2, 0),array($date, 6, 3, 0),
                array($date, 11, 1, 0),array($date, 11, 2, 0),array($date, 11, 3, 0),
                array($date, 12, 1, 0),array($date, 12, 2, 0),array($date, 12, 3, 0),
                array($date, 13, 1, 0),array($date, 13, 2, 0),array($date, 13, 3, 0),
                array($date, 4, 1, 0),array($date, 4, 2, 0),array($date, 4, 3, 0))        
            ->execute();
            
            
        }
        
        
?>        