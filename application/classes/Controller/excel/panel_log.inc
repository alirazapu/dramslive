<?php

if(isset($_POST['xport']) && $_POST['xport']=='excel')
        {    
            //report stuff start
            /* Excel object */
            $XLSX = new Spreadsheet();
            $data = new Model_Userreport;            
            /* excel setting */
            $XLSX->set_active_sheet(0);
            //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);
            
            $as = $XLSX->get_active_sheet();
            $as->setTitle('User\'s URL Hits Log-History ');
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
                    $limit = '';
                    $data1 = $data->user_panel_log($post, 'false');                                                      
                            $data = array(
                                     'Users' => array(
                                             1 => array('','','CTD Punjab ( User\'s Panel Log/History)','','','','',''),
                                             2 => array('User Name', 'Designation', 'User Type','Posting','Region','Activity Type','Activity Time'),
                                     ),

                             );
                            $i = 3;
                            foreach ($data1 as $item) {
//                                echo '<pre>';
//                                print_r($item);
//                                exit;
                               $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA'; 
                                $designation = ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';

//                                $user_ip=$_SERVER['REMOTE_ADDR'];
//                                $user_agent=$_SERVER['HTTP_HOST'];
                               // $accessed_url=$_SERVER['REQUEST_URI'];
//                                $accessed_url= explode("?",$_SERVER['REQUEST_URI']);



                                $user_role_name=(isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
                                $posting = ( isset($item['user_id']) ) ? Helpers_Profile::get_user_region_district($item['user_id'])   : 'NA';
                                $region = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_region_name($item['user_id'])   : 'NA';
                                $activity_type = ( isset($item['user_activity_type_id']) ) ? Helpers_Utilities::get_user_activity_name($item['user_activity_type_id'])   : 'NA';
                              $datetime = ( isset($item['activity_time']) ) ? $item['activity_time'] : 'NA';
//                    $user_agent = ( isset($item['user_agent']) ) ? $item['user_agent'] : 'NA';
//                    $accessed_url = ( isset($item['accessed_url']) ) ? $item['accessed_url'] : 'NA';
//                    $user_ip = ( isset($item['user_ip']) ) ? $item['user_ip'] : 'NA';
//                    $accessed_url_status_code = ( isset($item['accessed_url_status_code']) ) ? $item['accessed_url_status_code'] : 'NA';
                   
                             $data['Users'][$i] = array($user_name,$designation,$user_role_name,$posting,$region,$activity_type,$datetime);
                             $i = $i + 1 ;
                            }
                          $XLSX->setData( $data, 1 );
                            $name='ExcelReport';
                            $doc = 'excel'; //excel or pdf
                            $file=$XLSX->save(array('path'=>'dist/files/','name'=>$name),$doc);
                            exit();
                      //excel file genderated 
                 
                }
             
        //report ending