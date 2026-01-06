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
            $as->setTitle('Call Summary');
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
            $pname=Helpers_Person::get_person_name($pid);
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            
               
                $phone = NULL; 
            
                $phone2 = NULL;
           
           // print_r($post); exit; 
                    $data1 = $data->call_summary($post, 'false', $pid, $phone, $phone2);
                    
                                     
                            $data = array(
                                     'Users' => array(
                                             1 => array('','CTD Punjab (Call Summary)/'.$pname,'',''),
                                             2 => array('Party A', 'Party B', 'Total Incomming','Total Outgoing','Grand Total'),
                                     ),

                             );
                            $i = 3;
                            foreach ($data1 as $item) {
                               $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                                $other_phone = ( isset($item['other_person_phone_number']) ) ? $item['other_person_phone_number'] : 'NA';
                                $incomingcall = ( isset($item['icalls']) ) ? $item['icalls'] : 0;
                                $outgoingcall = ( isset($item['ocalls']) ) ? $item['ocalls'] : 0;
                                $totalcalls = $incomingcall + $outgoingcall;
                                
                             $data['Users'][$i] = array($phone_number,$other_phone,$incomingcall,$outgoingcall, $totalcalls);   
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