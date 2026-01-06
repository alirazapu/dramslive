<?php

if(isset($_POST['xport']) && $_POST['xport']=='excel')
        {    
            //report stuff start
            /* Excel object */
            $XLSX = new Spreadsheet();
            $data = new Model_Userreport;  
            
            //get id from url
            $id = (int) $this->request->param('id');
            $id = Helpers_Utilities::remove_injection($id);
             $username = Helpers_Utilities::get_user_name($id);
            
            /* excel setting */
            $XLSX->set_active_sheet(0);
            //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);
            
            $as = $XLSX->get_active_sheet();
            $as->setTitle('User\'s List ');
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
            
                    $data1 = $data->users_favourite_agent($post, 'false' , $id);                                                      
                            $data = array(
                                     'Users' => array(
                                             1 => array('','Favourite Agents of '.$username,'','',''),
                                             2 => array('Name','User Type', 'Designation','District'),
                                     ),
                             );
                            $i = 3;
                            foreach ($data1 as $item) {
                                
                                $user_id =  ( isset($item['userid']) ) ? $item['userid'] : 'NA';
                                
                                $user_name =( isset($item['userid']) ) ? Helpers_Utilities::get_user_name($item['userid']) : 'NA';                    
                                
                                $user_role_name=(isset($user_id) ) ? Helpers_Utilities::get_user_role_name($user_id) : 'N/A';
                                
                                $user_designation =( isset($item['userid']) ) ? Helpers_Utilities::get_user_job_title($item['userid']) : 'NA';                    
                               
                                $user_district =( isset($item['userid']) ) ? Helpers_Profile::get_user_region_district($item['userid']) : 'NA';                                        
                             
                                $data['Users'][$i] = array($user_name,$user_role_name,$user_designation,$user_district);   
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