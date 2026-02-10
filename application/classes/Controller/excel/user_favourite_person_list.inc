<?php

if(isset($_POST['xport']) && $_POST['xport']=='excel')
        {    
            //report stuff start
            /* Excel object */
            $XLSX = new Spreadsheet();
            $data = new Model_Userreport;  
            //get id from url
            $en_id = $this->request->param('id');
            $en_id = Helpers_Utilities::remove_injection($en_id);
            $id = (int)Helpers_Utilities::encrypted_key($en_id, "decrypt");
            $username = Helpers_Utilities::get_user_name($id);
            /* excel setting */
            $XLSX->set_active_sheet(0);
            //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);
            
            $as = $XLSX->get_active_sheet();
            $as->setTitle('user favourite person list ');
            $as->getDefaultStyle()->getFont()->setSize(11);
            $as->getDefaultStyle()->getFont()->setName('Arial');
            $as->getDefaultStyle()->getfont()->getBold(TRUE);
            
            foreach ($as->getColumnDimensions() as $dimension) {
            $dimension->setAutoSize(true);
            }
           
                    $styleArray = array(
                     'font' => array(
                       'name' => 'Arial',
                       'size' => '8',
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
            
                    $data1 = $data->user_favourite_person_list($post, 'false' ,$id);  
                            $data = array(
                                     'Users' => array(
                                             1 => array('','CTD KPK (user favourite person list)','','',''),
                                             2 => array('Person Name', 'Father Name', 'CNIC','Address'),
                                     ),

                             );
                            $i = 3;
                            foreach ($data1 as $item) {
                                $personname = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
                                $personname .= ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';
                                $fathername = ( isset($item['father_name']) ) ? $item['father_name'] : 'NA';
                                $foreigner_status = (isset($item['is_foreigner'])) ? $item['is_foreigner'] : 0;
                                if ($foreigner_status == 0) {
                                 $cnic = (isset($item['cnic_number'])) ? $item['cnic_number'] : 0;   
                                }else{
                                    $cnic = (isset($item['cnic_number_foreigner'])) ? $item['cnic_number_foreigner'] : 0;
                                }
                                //$cnic = (isset($item['cnic_number'])) ? $item['cnic_number'] : 'NA';
                                $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                                
                             $data['Users'][$i] = array($personname,$fathername,$cnic,$address);   
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