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
    $as->setTitle('User\'s Performance Report ');
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

    $data1 = $data->performance_report($post, 'false');
    $data = array(
        'Users' => array(
            1 => array('', '', 'CTD KPK ( User\'s Performace Report)', '', '', '', '', ''),
            2 => array('User ID','User Name','User Type','Designation', 'Total Request', 'Total Favourite person', 'Total Favourite User', 'Total White', 'Total Grey', 'Total Black', 'Total Login'),
        ),
    );
    $i = 3;
    foreach ($data1 as $item) {
        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';
        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
        $designation = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_job_title($item['user_id']) : 'NA';
        $posting = ( isset($item['user_id']) ) ? Helpers_Profile::get_user_region_district($item['user_id']) : 'NA';
        $totalrequests = ( isset($item['user_id']) ) ? Helpers_Utilities::get_request_count($item['user_id']) : 'NA';
        $totalfavperson = ( isset($item['user_id']) ) ? Helpers_Utilities::get_favourite_person_count($item['user_id']) : 'NA';
        $totalfavuser = ( isset($item['user_id']) ) ? Helpers_Utilities::get_favourite_user_count($item['user_id']) : 'NA';
        $totalwhite = ( isset($item['user_id']) ) ? Helpers_Utilities::get_users_white_person($item['user_id']) : 'NA';
        $totalgray = ( isset($item['user_id']) ) ? Helpers_Utilities::get_users_grey_person($item['user_id']) : 'NA';
        $totalblack = ( isset($item['user_id']) ) ? Helpers_Utilities::get_users_black_person($item['user_id']) : 'NA';
        $totallogin = ( isset($item['user_id']) ) ? Helpers_Utilities::get_login_count($item['user_id']) : 'NA';

        $data['Users'][$i] = array($user_id,$user_name,$user_role_name,$designation, $totalrequests, $totalfavperson, $totalfavuser, $totalwhite, $totalgray, $totalblack, $totallogin);
        $i = $i + 1;
    }
    $XLSX->setData($data, 1);
    $name = 'ExcelReport';
    $doc = 'excel'; //excel or pdf
    $file = $XLSX->save(array('path' => 'dist/files/', 'name' => $name), $doc);
    exit();
    //excel file genderated 
}
             
        //report ending