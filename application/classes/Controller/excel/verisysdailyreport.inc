<?php

if (isset($_POST['xport']) && $_POST['xport'] == 'excel') {
    //echo 'excell'; exit;
    //report stuff start
    /* Excel object */
    $XLSX = new Spreadsheet();
    $data = new Model_Adminreports();
    /* excel setting */
    $XLSX->set_active_sheet(0);
    //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);

    $as = $XLSX->get_active_sheet();
    $as->setTitle('Verisys Report');
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

    $data1 = $data->verisys_response_summary($post, 'false');
    $data = array(
        'Users' => array(
            1 => array('', 'CTD KPK (Verisys Report)', ''),
            2 => array('Region', 'Date', 'Total Request', 'Total Response',),
        ),
    );
    $i = 3;
    foreach ($data1 as $item) {
                   $region = ( isset($item['region_id']) ) ? $item['region_id'] : 'NA';
                    $region_name = Helpers_Utilities::get_region($region);
                    $request_date = ( isset($item['request_date']) ) ? $item['request_date'] : 0;
                    $request_date_new = strtotime(date('Y-m-d', strtotime($request_date)));                    
                    $request_count = ( isset($item['request_count']) ) ? $item['request_count'] : 0;
                    $response_count = ( isset($item['pending']) ) ? $item['pending'] : 0;
                    
        $data['Users'][$i] = array($region_name,$request_date,$request_count,
                                   $response_count);
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