<?php
//echo '<pre>';
//print_r($search_post);
//exit;
if (isset($_POST['xport']) && $_POST['xport'] == 'excel') {
    //report stuff start
    /* Excel object */
    $XLSX = new Spreadsheet();
    $data = new Model_Userreport;
    /* excel setting */
    $XLSX->set_active_sheet(0);
    //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);

    $as = $XLSX->get_active_sheet();
    $as->setTitle('Audit Report');
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

    $data1 = $data->audit_report_basic($search_post, 'false');
    $data = array(
        'Users' => array(
            1 => array( 'CTD KPK ( Audit Report )', ''),
            2 => array('Region/District' , 'Total Request'),
        ),
    );
    $i = 3;
    foreach ($data1 as $item) {
        $posting_place = '';
        $posting = ( isset($item['posted']) ) ? $item['posted'] : 'NA';
        $result = explode('-', $posting);        
        switch ($result[0]) {
            case 'h':
                $posting_place = 'Head Quarters';
                break;
            case 'r':
                $posting_place = 'Region ';
                $posting_place .= Helpers_Utilities::get_region($result[1]);
                break;
            case 'd':
                $posting_place = 'District ';
                $posting_place .= Helpers_Utilities::get_district($result[1]);                
                break;
            case 'p':
                //print_r($result[1]); exit;
                $posting_place = 'Police Station ';
                $dataps = Helpers_Utilities::get_ps_name($result[1]);
                $posting_place .= isset($dataps->name) ? $dataps->name : 'Unknown';
                //print_r($posting_place); exit;
                break;
        }
        $count = ( isset($item['count']) ) ? $item['count'] : 'NA';
        
        $data['Users'][$i] = array($posting_place,$count);
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