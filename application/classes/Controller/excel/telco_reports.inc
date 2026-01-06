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
    $as->setTitle('Telco Request Response');
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

    $data1 = $data->telco_request_summary($post, 'false');
    $data = array(
        'Users' => array(
            1 => array('', '', '', 'CTD Punjab (Telco Response)', '', '', ''),
            2 => array('Date', 'Company Name', 'High', 'Medium', 'Low', 'Total'),
        ),
    );
    $i = 3;
    foreach ($data1 as $item) {
                    $date = ( isset($item['date']) ) ? $item['date'] : 'NA';                 
                    $company = ( isset($item['company_name']) ) ? $item['company_name'] : 'Unknown';                 
                    $company = ($company=='NADRA')?'Family Tree':$company;
                    $sendhigh = ( isset($item['send_high']) ) ? $item['send_high'] : 0;                 
                    $sendmedium = ( isset($item['send_medium']) ) ? $item['send_medium'] : 0;                 
                    $sendlow = ( isset($item['send_low']) ) ? $item['send_low'] : 0;                 
                    $sendtotal = ( isset($item['total_send']) ) ? $item['total_send'] : 0; 
                    $rectotal = ( isset($item['total_received']) ) ? $item['total_received'] : 0; 
                    $high='Send: '.$sendhigh;
                    $medium='Send: '.$sendmedium;
                    $low='Send: '.$sendlow;
                    $total='Send: '.$sendtotal.', Received: '.$rectotal;
        $data['Users'][$i] = array($date,$company,$high,
                                   $medium,$low,$total);
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