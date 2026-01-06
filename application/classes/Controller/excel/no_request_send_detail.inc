<?php

if (isset($_POST['xport']) && $_POST['xport'] == 'excel') {
    //report stuff start
    /* Excel object */
    $XLSX = new Spreadsheet();
    $data = new Model_Userreport;
    /* excel setting */
    $XLSX->set_active_sheet(0);
    //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);

    $as = $XLSX->get_active_sheet();
    $as->setTitle('users favourite user\'s List ');
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
    $sdate = !empty($post['sdate']) ? $post['sdate'] : '';
    $edate = !empty($post['edate']) ? $post['edate'] : '';
    $userid = !empty($post['userid']) ? $post['userid'] : 0;
    $request_type = !empty($post['request_type']) ? $post['request_type'] : 0;
    $data1 = $data->no_request_send_detail($post, 'false' , $userid , $request_type);
    
    $user_name = ( isset($userid) ) ? Helpers_Utilities::get_user_name($userid) : 'NA';    
    $request_type = ( isset($request_type) ) ? Helpers_Utilities::get_request_type($request_type) : 'NA';
    
    $data = array(
        'Users' => array(
            1 => array('Request Sent Log'),
            2 => array('Request Type: '.$request_type.' Date From:'.$sdate . ' To '.$edate),
            3 => array('Requesting User', 'Requested Value', 'Request Reason', 'Person','Request Date'),
        ),
    );
    $i = 4;
    foreach ($data1 as $item) {
        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';        
        $requested_value = ( isset($item['requested_value']) ) ? $item['requested_value'] : 'NA';
        $reason = ( isset($item['reason']) ) ? $item['reason'] : 'NA';
        $concerned_person_id = ( isset($item['concerned_person_id']) ) ? $item['concerned_person_id'] : 'NA';
        if ($concerned_person_id > 0) {
            $perons_name = ( isset($item['concerned_person_id']) ) ? Helpers_Person::get_person_name($item['concerned_person_id']) : 'NA';
        } else {
            $perons_name = " ";
        }
        //now
        $created_at = ( isset($item['created_at']) ) ? $item['created_at'] : 'NA';
        $data['Users'][$i] = array($user_name, $requested_value, $reason, $perons_name,$created_at);
        $i = $i + 1;
    }
    $XLSX->setData($data, 1);
    $XLSX->get_active_sheet()->mergeCells('A1:E1');
    $XLSX->get_active_sheet()->mergeCells('A2:E2');
    $name = 'ExcelReport';
    $doc = 'excel'; //excel or pdf
    $file = $XLSX->save(array('path' => 'dist/files/', 'name' => $name), $doc);
    exit();
    //excel file genderated 
}
             
        //report ending