<?php

if(isset($_POST['xport']) && $_POST['xport']=='excel')
        {        
            //report stuff start
            /* Excel object */
            $XLSX = new Spreadsheet();
            $data = new Model_Persons;            
            /* excel setting */
            $XLSX->set_active_sheet(0);
            //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);
            
            $as = $XLSX->get_active_sheet();
            $as->setTitle('Person\'s DB Match');
            $as->getDefaultStyle()->getFont()->setSize(11);
            $as->getDefaultStyle()->getFont()->setName('Arial');
            $as->getDefaultStyle()->getfont()->getBold(TRUE);
            
            foreach ($as->getColumnDimensions() as $dimension) {
            $dimension->setAutoSize(true);
            }
           
                    $styleArray = array(
                     'font' => array(
                       'name' => 'Arial',
                       'size' => '111',
                     ),
                     'borders' => array(
                       'left' => array(
                         'style' => PHPExcel_Style_Border::BORDER_THIN,
                       ),
                       'right' => array(
                         'style' => PHPExcel_Style_Border::BORDER_THIN,
                       ),
                       'vertical' => array(
                         'style' => PHPExcel_Style_Border::BORDER_THIN,
                       ),
                     ),
                     'fill' => array(
                       'type' => PHPExcel_Style_Fill::FILL_SOLID,
                       'startcolor' => array(
                         'argb' => 'FFFFFFCC',
                       ),
                     ),
                   );
                     
                    $as->getStyle('A1:E1')->applyFromArray($styleArray);
                    $as->getStyle('B2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);                    
                    $as->getDefaultColumnDimension()->setWidth(20);                 
                    // $as->getStyleByColumnAndRow('A', '1') ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);            
                     $total = 0;              
                     $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                    //$data1 = $data->person_db_match($post, 'false',$pid);       
                      if(!empty($post['phone_number']) && $post['phone_number']==3)
                      {unset ($post['phone_number']);}
                     $result = $data->person_db_match($post, 'false', $pid);
                        $rows_count = $result['count'];
                        $data1 = $result['result'];
                    
                    
                            $data = array(
                                     'dbmatch' => array(
                                             1 => array('','CTD Punjab ( Person\'s DB Match)','',''),
                                             2 => array('Phone Number', 'B Party', 'Person\'s Name','Father Name','Person CNIC','I Calls','O Calls','I SMS','O SMS', 'Last Call/SMS'),
                                     ),

                             );
                            $i = 3;
                            foreach ($data1 as $item) {
                                 /* Concate name full name */
                                $phone_number = ( isset($item['phone']) ) ? $item['phone'] : ' ';
                                $other_phone = ( isset($item['bparty']) ) ? $item['bparty'] : '';
                                $phone_person_id = ( isset($item['other_id']) ) ? $item['other_id'] : NULL;
                                $other_name = '';
                                $other_father = '';
                                $other_cnic = '';
                                if (!empty($item['other_id'])) {
                                    $other_name = Helpers_Person::get_person_name($phone_person_id);                                    
                                    $other_father = Helpers_Person::get_person_father_name($phone_person_id);
                                    $other_cnic = Helpers_Person::get_person_cnic($phone_person_id);                     
                                }
                                if ($other_father == 'Unknown') {
                                    $other_father = '';
                                }
                                $outgoing_calls = ( isset($item['outgoing_calls']) ) ? $item['outgoing_calls'] : 'N/A';
                                $incomming_calls = ( isset($item['incoming_calls']) ) ? $item['incoming_calls'] : 'N/A';
                                $outgoing_sms = ( isset($item['outgoing_sms']) ) ? $item['outgoing_sms'] : 'N/A';
                                $incomming_sms = ( isset($item['incoming_sms']) ) ? $item['incoming_sms'] : 'N/A';
                                
                                
                                $last_activities = Helpers_Person::get_last_activity($phone_number, $other_phone, $pid);
                                $last_2_activity = '';
                                if(!empty($last_activities))
                                foreach($last_activities as $key => $activity)
                                {
                                    $last_2_activity .= 'Last Call:';
                                    $last_2_activity .= 'Duration: '. $activity->duration_in_seconds;
                                    $last_2_activity .= 'Lat: '. $activity->latitude;
                                    $last_2_activity .= 'Long: '. $activity->longitude;
                                    $last_2_activity .= 'Address:'. $activity->address;
                                    $last_2_activity .= 'Call Timing:'. $activity->call_at . ' to ' . $activity->call_end_at;

                                }    
                                $last_activities = Helpers_Person::get_last_activity_sms($phone_number, $other_phone, $pid);                        
                                if(!empty($last_activities))
                                foreach($last_activities as $key => $activity)
                                {
                                    $last_2_activity .= 'Last SMS ';                            
                                    $last_2_activity .= 'Lat: '. $activity->latitude;
                                    $last_2_activity .= 'Long: '. $activity->longitude;
                                    $last_2_activity .= 'Address: '. $activity->address;
                                    $last_2_activity .= 'Call Timing: '. $activity->sms_at;

                                }  
                                
                                
                                $data['dbmatch'][$i] = array($phone_number, $other_phone, $other_name, $other_father, $other_cnic,$incomming_calls,$outgoing_calls ,$incomming_sms,$outgoing_sms, $last_2_activity );
                                $i = $i + 1;
                            }
                            $XLSX->setData( $data, 1 );
                            $name='ExcelReport';
                            $doc = 'excel'; //excel or pdf
                            $file=$XLSX->save(array('path'=>'dist/files/','name'=>$name),$doc);
                            exit();
                      //excel file genderated 
                 
                }
             
        //report ending