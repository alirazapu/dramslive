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

    $data1 = $data->user_transferred_list($post, 'false');
    $data = array(
        'Users' => array(
            1 => array( '', '', 'CTD KPK (User List)', '', '', '', ''),
            2 => array('User ID', 'Username','Full Name', 'User Type', 'Designation', 'Posting', 'Mobile Number','Approved By'),
        ),
    );
    $i = 3;
    foreach ($data1 as $item) {
        if (!empty($item['district_id']) && $item['district_id'] != 0)
        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';
        $full_name = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
        $mobile_number = ( isset($item['mobile_number']) ) ? $item['mobile_number'] : 'NA';
        $full_name .= ' ';
        $full_name .= ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';
        $username = ( isset($item['username']) ) ? $item['username'] : '';
        $user_role_name = (isset($item['user_id']) ) ? Helpers_Utilities::get_user_role_name($item['user_id']) : 'N/A';
        $designation = ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';
        $posting = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
        $approved_by = ( isset($item['approved_by']) ) ? Helpers_Utilities::get_user_name($item['approved_by']) : 'NA';
        
        $data['Users'][$i] = array($user_id,$username,$full_name,$user_role_name,
                                   $designation,$posting,$mobile_number,$approved_by);
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