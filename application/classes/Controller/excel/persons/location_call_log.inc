<?php

if (isset($_POST['xport']) && $_POST['xport'] == 'excel') {
    // print_r($_POST); exit;
    //report stuff start
    /* Excel object */
    $XLSX = new Spreadsheet();
    $data = new Model_Persons;
    /* excel setting */
    $XLSX->set_active_sheet(0);
    //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);

    $as = $XLSX->get_active_sheet();
    $as->setTitle('Location Summary');
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
    $pname = Helpers_Person::get_person_name($pid);
    $type = $_POST['type'];
    if (!empty($type) || $type != NULL) {
        $map_data = $data->get_person_call_sms_location_export($_POST, $pid);
        $type = $_POST['type'];
        if ($type == 'call') {
            $data = array(
                'Users' => array(
                    1 => array('', '', 'Location Summary of ' . $pname, '', '', ''),
                    2 => array('Phone Number', 'Party B', 'Call AT', 'LAC ID', 'CELL ID', 'Latitude', 'Longitude', 'Address'),
                ),
            );
            $i = 3;
            foreach ($map_data as $item) {
                $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                $other_phone_number = ( isset($item['other_person_phone_number']) ) ? $item['other_person_phone_number'] : 'NA';
                $call_at = ( isset($item['call_at']) ) ? $item['call_at'] : 'NA';
                $lac_id = ( isset($item['lac_id']) ) ? $item['lac_id'] : 'NA';
                $cell_id = ( isset($item['cell_id']) ) ? $item['cell_id'] : 'NA';
                $latitude = ( isset($item['latitude']) ) ? $item['latitude'] : 'NA';
                $longitude = ( isset($item['longitude']) ) ? $item['longitude'] : 'NA';
                $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                $data['Users'][$i] = array($phone_number, $other_phone_number, $call_at, $lac_id, $cell_id, $latitude, $longitude, $address);
                $i = $i + 1;
            }
        } elseif ($type == 'sms') {
            $data = array(
                'Users' => array(
                    1 => array('', '', 'Location Summary of ' . $pname, '', '', ''),
                    2 => array('Phone Number', 'Party B', 'SMS at', 'LAC ID', 'CELL ID', 'Latitude', 'Longitude', 'Address'),
                ),
            );
            $i = 3;
            foreach ($map_data as $item) {
                $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                $other_phone_number = ( isset($item['other_person_phone_number']) ) ? $item['other_person_phone_number'] : 'NA';
                $call_at = ( isset($item['sms_at']) ) ? $item['sms_at'] : 'NA';
                $lac_id = ( isset($item['lac_id']) ) ? $item['lac_id'] : 'NA';
                $cell_id = ( isset($item['cell_id']) ) ? $item['cell_id'] : 'NA';
                $latitude = ( isset($item['latitude']) ) ? $item['latitude'] : 'NA';
                $longitude = ( isset($item['longitude']) ) ? $item['longitude'] : 'NA';
                $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                $data['Users'][$i] = array($phone_number, $other_phone_number, $call_at, $lac_id, $cell_id, $latitude, $longitude, $address);
                $i = $i + 1;
            }
        } 
        elseif ($type == 'favfive') {
            $data = array(
                'Users' => array(
                    1 => array('', '', 'Favourite Five Locations of ' . $pname, '', '', ''),
                    2 => array('Phone Number', 'Location Count', 'Latitude', 'Longitude', 'Address'),
                ),
            );
            $i = 3;
            foreach ($map_data as $item) {
                $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                $cout = ( isset($item['CNT']) ) ? $item['CNT'] : 'NA';
                $latitude = ( isset($item['latitude']) ) ? $item['latitude'] : 'NA';
                $longitude = ( isset($item['longitude']) ) ? $item['longitude'] : 'NA';
                $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                $data['Users'][$i] = array($phone_number, $cout, $latitude, $longitude, $address);
                $i = $i + 1;
            }
        }elseif ($type == 'curloc') {
           // print_r($map_data); exit;
            $data = array(
                'Users' => array(
                    1 => array('', '', 'Current Locations of  ' . $pname, '', '', ''),
                    2 => array('Phone Number', 'Location Time', 'LAC ID', 'CELL ID', 'Latitude', 'Longitude', 'Address'),
                ),
            );
            $i = 3;
            foreach ($map_data as $item) {
                $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                $location_time = ( isset($item['moved_in_at']) ) ? $item['moved_in_at'] : 'NA';
                $lac_id = ( isset($item['lac_id']) ) ? $item['lac_id'] : 'NA';
                $cell_id = ( isset($item['cell_id']) ) ? $item['cell_id'] : 'NA';
                $latitude = ( isset($item['latitude']) ) ? $item['latitude'] : 'NA';
                $longitude = ( isset($item['longitude']) ) ? $item['longitude'] : 'NA';
                $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                $data['Users'][$i] = array($phone_number, $location_time,$lac_id,$cell_id, $latitude, $longitude, $address);
                $i = $i + 1;
            }
        }else {
            $data = array(
                'Users' => array(
                    1 => array('', '', 'Call & SMS Locations of ' . $pname, '', '', ''),
                    2 => array('Phone Number', 'Party B', 'LAC ID', 'CELL ID', 'Latitude', 'Longitude', 'Address'),
                ),
            );
            $i = 3;
            foreach ($map_data as $item) {
                $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                $other_phone_number = ( isset($item['other_person_phone_number']) ) ? $item['other_person_phone_number'] : 'NA';
                $lac_id = ( isset($item['lac_id']) ) ? $item['lac_id'] : 'NA';
                $cell_id = ( isset($item['cell_id']) ) ? $item['cell_id'] : 'NA';
                $latitude = ( isset($item['latitude']) ) ? $item['latitude'] : 'NA';
                $longitude = ( isset($item['longitude']) ) ? $item['longitude'] : 'NA';
                $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                $data['Users'][$i] = array($phone_number, $other_phone_number,$lac_id, $cell_id, $latitude, $longitude, $address);
                $i = $i + 1;
            }
        }
    } else {
        $map_data = $data->get_person_last_five_calls_export($pid);
        //echo '<pre>';  print_r($map_data); exit;    
        if (isset($map_data[0]['moved_in_at'])) {  
            $data = array(
                'Users' => array(
                    1 => array('', '', 'Last Five Current Locations of ' . $pname, '', '', ''),
                    2 => array('PHONE NUMBER', 'LAC ID', 'CELL ID', 'Sector', 'LATITUDE', 'LONGITUDE', 'LOCATION TIME', 'Address'),
                ),
            );
            $i = 3;
            foreach ($map_data as $item) {
                $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';                
                $lac_id = ( isset($item['lac_id']) ) ? $item['lac_id'] : 'NA';
                $cell_id = ( isset($item['cell_id']) ) ? $item['cell_id'] : 'NA';
                $sector = ( isset($item['sector']) ) ? $item['sector'] : 'NA';
                $latitude = ( isset($item['latitude']) ) ? $item['latitude'] : 'NA';
                $longitude = ( isset($item['longitude']) ) ? $item['longitude'] : 'NA';
                $location_time = ( isset($item['moved_in_at']) ) ? $item['moved_in_at'] : 'NA';
                $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                $data['Users'][$i] = array($phone_number, $lac_id, $cell_id,$sector, $latitude, $longitude,$location_time, $address);
                $i = $i + 1;
            }
        } else {
            $data = array(
                'Users' => array(
                    1 => array('', '', 'Last Five Calls Location of ' . $pname, '', '', ''),
                    2 => array('Phone Number', 'Party B', 'Call AT', 'LAC ID', 'CELL ID', 'Latitude', 'Longitude', 'Address'),
                ),
            );
            $i = 3;
            foreach ($map_data as $item) {
                $phone_number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                $other_phone_number = ( isset($item['other_person_phone_number']) ) ? $item['other_person_phone_number'] : 'NA';
                $call_at = ( isset($item['call_at']) ) ? $item['call_at'] : 'NA';
                $lac_id = ( isset($item['lac_id']) ) ? $item['lac_id'] : 'NA';
                $cell_id = ( isset($item['cell_id']) ) ? $item['cell_id'] : 'NA';
                $latitude = ( isset($item['latitude']) ) ? $item['latitude'] : 'NA';
                $longitude = ( isset($item['longitude']) ) ? $item['longitude'] : 'NA';
                $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                $data['Users'][$i] = array($phone_number, $other_phone_number, $call_at, $lac_id, $cell_id, $latitude, $longitude, $address);
                $i = $i + 1;
            }
        }
    }
    $XLSX->setData($data, 1);
    $name = 'ExcelReport';
    $doc = 'excel'; //excel or pdf
    $file = $XLSX->save(array('path' => 'dist/files/', 'name' => $name), $doc);
    exit();
    //excel file genderated 
}
             
        //report ending