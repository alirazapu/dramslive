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
            $as->setTitle('Person favourite person\'s List ');
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
                    $data1 = $data->person_favourite_person($post, 'false',$pid);                                                      
                            $data = array(
                                     'Users' => array(
                                             1 => array('','CTD Punjab ( Person\'s Favourite Persons)','',''),
                                             2 => array('Party B', 'Person\'s Name', 'Father Name','Person CNIC','Total Calls','Total SMS'),
                                     ),

                             );
                            $i = 3;
                            foreach ($data1 as $item) {
                                /* Concate name full name */
                                $other_phone = ( isset($item['other_person_phone_number']) ) ? $item['other_person_phone_number'] : 'NA';
                                $phone_person_id = ( isset($item['other_id']) ) ? $item['other_id'] : NULL;

                                if (!empty($item['other_id'])) {
                                    $other_name = Helpers_Person::get_person_name($phone_person_id);
                                    $other_father = Helpers_Person::get_person_father_name($phone_person_id);
                                    $other_cnic = Helpers_Person::get_person_cnic($phone_person_id);                                    
                                } else {
                                    $other_name = 'N/A';
                                    $other_father = 'N/A';
                                    $other_cnic = 'N/A';                                   
                                    //' . URL::site('userrequest/request/' . $item['other_person_phone_number']) . '
                                }
                                $totalcalls = ( isset($item['calls']) ) ? $item['calls'] : 0;
                                $totalsms = ( isset($item['sms']) ) ? $item['sms'] : 0;
                                
                                $data['Users'][$i] = array($other_phone, $other_name, $other_father, $other_cnic, $totalcalls,$totalsms);
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