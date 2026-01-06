<?php
//print_r($post); exit;
if (isset($_POST['xport']) && $_POST['xport'] == 'excel') {
    //report stuff start
    /* Excel object */
    $XLSX = new Spreadsheet();
    $data = new Model_Userreport;
    /* excel setting */
    $XLSX->set_active_sheet(0);
    //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);

    $as = $XLSX->get_active_sheet();
    $as->setTitle('Breakup Report');
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
    
    $post['posted']=  Helpers_Utilities::encrypted_key($post['posted'], 'decrypt');    
    $posting_place = !empty($post['posted']) ? Helpers_Profile::get_user_posting($post['posted']) : 'Un-Known';   
    $sdate = !empty($post['sdate']) ? $post['sdate'] : '';
    $edate = !empty($post['edate']) ? $post['edate'] : '';
    $data1 = $data->audit_report($post, 'false', $post['posted']);
    
    $data = array(
        'Users' => array(
            1 => array('Audit Report'),
            2 => array('Office: '.$posting_place .' Date: ' . $sdate . ' TO '.$edate),
            3 => array( 'User Name', 'Designation', 'Posting', 'Region', 'Request count'),
        ),
    );
    $i = 4;
    foreach ($data1 as $item) {
        
        $user_id = ( isset($item['user_id']) ) ? $item['user_id'] : 'NA';
        $user_name = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_name($item['user_id']) : 'NA';
        $designation = ( isset($item['job_title']) ) ? $item['job_title'] : 'NA';
        $posting = ( isset($item['posted']) ) ? Helpers_Profile::get_user_posting($item['posted']) : 'NA';
        // $district = ( isset($item['user_id']) ) ? Helpers_Utilities::get_user_district_name($item['user_id']) : 'NA';
        $reg_id = $item['region_id'];
        if ($reg_id == 0) {
            $region = "Head Quaters";
        } else {
            $region = ( isset($item['region_id']) ) ? Helpers_Utilities::get_region($item['region_id']) : 'NA';
        }
        $requestcount = ( isset($item['count']) ) ? $item['count'] : 0;
        
        $data['Users'][$i] = array($user_name, $designation, $posting, $region, $requestcount);
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