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
            $as->setTitle('no_record_search ');
            $as->getDefaultStyle()->getFont()->setSize(11);
            $as->getDefaultStyle()->getFont()->setName('Arial');
            $as->getDefaultStyle()->getfont()->getBold(TRUE);
            
            foreach ($as->getColumnDimensions() as $dimension) {
            $dimension->setAutoSize(true);
            }
           
                    $styleArray = array(
                     'font' => array(
                       'name' => 'Arial',
                       'size' => '11',
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
            
                    $data1 = $data->no_record_searched($post, 'false');                                                      
                            $data = array(
                                     'Users' => array(
                                             1 => array('','CTD KPK ( User\'s Record Search History)','',''),
                                             2 => array('User Name','User Type', 'Search Type','Search Key','Search Time'),
                                     ),

                             );
                            $i = 3;
                            foreach ($data1 as $item) {
                               $user_name =( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';                    
                               $user_role_name=(isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
                                $searchname =  ( isset($item['key_name']) ) ? $item['key_name'] : 'NA';
                                $searchkey =  ( isset($item['key_value']) ) ? $item['key_value'] : 'NA';
                                $time =  ( isset($item['activity_time']) ) ? $item['activity_time'] : 'NA';
                             $data['Users'][$i] = array($user_name,$user_role_name,$searchname,$searchkey,$time);   
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