<?php

if(isset($_POST['xport']) && $_POST['xport']=='excel')
        {
    
            //report stuff start
            /* Excel object */
            $XLSX = new Spreadsheet();
            $data = new Model_Personsreports;            
            /* excel setting */
            $XLSX->set_active_sheet(0);
            //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);
            
            $as = $XLSX->get_active_sheet();
            $as->setTitle('Top Searched Person ');
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
            
                    $data1 = $data->top_search_persons($post, 'false');                                                      
                            $data = array(
                                     'Users' => array(
                                             1 => array('','','CTD KPK ( Top Searched Persons)','',''),
                                             2 => array('Person Name', 'Father Name','CNIC','Address','Category','Search Count'),
                                     ),

                             );
                            $i = 3;
                            foreach ($data1 as $item) {
                               $full_name = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
                                $full_name .= ' ';
                                $full_name .= ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';                    
                                $father_name= ( isset($item['father_name']) ) ? $item['father_name'] : 'NA';
                                $cnic_number= ( isset($item['cnic_number']) ) ? $item['cnic_number'] : 'NA';
                                $address = ( isset($item['address']) ) ?  $item['address'] : 'NA';
                                $category1 = ( isset($item['person_id']) ) ? Helpers_Person::get_person_category_id($item['person_id']) : "";                    
                                $category = (isset($category1)) ? Helpers_Utilities::get_category_name($category1) : 'NA';
                               
                                $search_count = ( isset($item['maxtotal']) ) ?  $item['maxtotal'] : 0;
                                
                             $data['Users'][$i] = array($full_name,$father_name,$cnic_number,$address,$category,$search_count);   
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