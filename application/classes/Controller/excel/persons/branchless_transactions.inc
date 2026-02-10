<?php

if(isset($_POST['xport']) && $_POST['xport']=='excel')
        {          
            //report stuff start
            /* Excel object */
            $XLSX = new Spreadsheet();
            $data = new Model_Persons();            
            /* excel setting */
            $XLSX->set_active_sheet(0);
            //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);
            
            $as = $XLSX->get_active_sheet();
            $as->setTitle('SMS Log Summary');
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
           // $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            $pname=Helpers_Person::get_person_name($pid);
                    $data1 = $data->branchless_transactions($post, 'false', $pid);                                                     
                            $data = array(
                                     'Users' => array(
                                             1 => array('CTD KPK (Branchless Transactions)/'.$pname),
                                             2 => array('Party A', 'Party B', 'SMS Type','Date & Time','Location'),
                                     ),

                             );
                            $i = 3;
                            foreach ($data1 as $item) {
                               $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                                $other_phone = ( isset($item['other_person_phone_number']) ) ? $item['other_person_phone_number'] : 0;
                                $result = Helpers_Utilities::get_company_branchless_transactions($other_phone);
                                $value = ' ';
                                $value .= !empty($result['name_branchless_transation']) ? $result['name_branchless_transation'] : '-' ;
                                $value .= '=>';
                                
                                $value .= !empty($result['bank_company_name']) ? $result['bank_company_name'] : '-';
                                
                                
                                $type = ( $item['is_outgoing'] == 1 ) ? "outgoing" : 'incoming';
                                //$duration= ( isset($item['duration_in_seconds']) ) ?  $item['duration_in_seconds'] : 'NA';
                                $datetime = ( isset($item['sms_at']) ) ? $item['sms_at'] : 'NA';
                                $location = ( isset($item['address']) ) ? $item['address'] : 'NA';
                             $data['Users'][$i] = array($phone_number,$value,$type,$datetime, $location);   
                             $i = $i + 1 ;
                            }
                          $XLSX->setData( $data, 1 );
                          $XLSX->get_active_sheet()->mergeCells('A1:E1');
                            $name='ExcelReport';
                            $doc = 'excel'; //excel or pdf
                            $file=$XLSX->save(array('path'=>'dist/files/','name'=>$name),$doc);
                            exit();
                      //excel file genderated 
                 
                }
             
        //report ending