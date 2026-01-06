<?php

//echo 'hello';
if (isset($_POST['xport']) && $_POST['xport'] == 'excel') {
    //echo 'hello'; exit;
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
    $project_id = (int) Helpers_Utilities::encrypted_key($post['project_id'], 'decrypt');
    $project_name = Helpers_Utilities::get_project_names($project_id);
    $data1 = $data->project_request_type($post, 'false', $project_id);
    $data = array(
        'Users' => array(
            1 => array('Request Send Log of Project :: ' .$project_name),
            2 => array('User Name', 'Designation', 'Region', 'Posting', 'Request Type', 'Total Request'),
        ),
    );
    $i = 3;
    foreach ($data1 as $item) {
        // print_r($data);exit;
        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';
        $project_id = ( isset($item['project_id']) ) ? $item['project_id'] : 0;
        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
        $user_designation = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_job_title($item['user_id']) : 'NA';
        $user_region = (!empty($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'Head Quarters';
        $user_posting = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
        $request_type = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type_name($item['user_request_type_id']) : 'NA';
        $count = ( isset($item['count']) ) ? $item['count'] : 0;

        $data['Users'][$i] = array($user_name, $user_designation, $user_region, $user_posting, $request_type, $count);
        $i = $i + 1;
    }
    $XLSX->setData($data, 1);
    $XLSX->get_active_sheet()->mergeCells('A1:F1'); 
    $name = 'ExcelReport';
    $doc = 'excel'; //excel or pdf
    $file = $XLSX->save(array('path' => 'dist/files/', 'name' => $name), $doc);
    exit();
    //excel file genderated 
}
             
        //report ending