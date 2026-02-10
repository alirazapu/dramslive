<?php

if(isset($_POST['xport']) && $_POST['xport']=='excel')
        {        
            //report stuff start
            /* Excel object */
            $XLSX = new Spreadsheet();
            $data = new Model_Persons;            
            /* excel setting */
            $XLSX->set_active_sheet(0); //->mergeCells('A1:C1');            
            //            
            //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);
            
            $as = $XLSX->get_active_sheet();
            $as->setTitle('Person\'s Physical Location');
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
                    $data1 = $data->person_physical_location($post, 'false',$pid);                                                      
                            $data = array(
                                     'dbmatch' => array(
                                             1 => array('CTD KPK ( Person\'s Physical Location)'),
                                             2 => array('Phone Number', 'Location', 'Location Count'),
                                     ),

                             );
                            $i = 3;
                            foreach ($data1 as $item) {
                                 /* Concate name full name */                               
                                $party_a = ( isset($item['phone']) ) ? $item['phone'] : 0;
                                $address = ( isset($item['address']) ) ? $item['address'] : '--';
                                $address_count = ( isset($item['loc_count']) ) ? $item['loc_count'] : 0; 
                                
                               $data['dbmatch'][$i] = array($party_a, $address, $address_count);
                                $i = $i + 1;
                            }
                            
                            $XLSX->setData( $data, 1 );      
                            $XLSX->get_active_sheet()->mergeCells('A1:C1');                            
                            $name='ExcelReport';
                            $doc = 'excel'; //excel or pdf
                            $file=$XLSX->save(array('path'=>'dist/files/','name'=>$name),$doc);
                            exit();
                      //excel file genderated 
                 
                }
             
        //report ending