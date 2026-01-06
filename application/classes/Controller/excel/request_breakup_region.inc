<?php

if (isset($_POST['xport']) && $_POST['xport'] == 'excel') {
    
    //echo 'excell'; exit;
    //report stuff start
    /* Excel object */
    $XLSX = new Spreadsheet();
    $data = new Model_Userreport();
    /* excel setting */
    $XLSX->set_active_sheet(0);
    //$XLSX->getActiveSheet()->getColumnDimension('B2')->setAutoSize(true);

    $as = $XLSX->get_active_sheet();
    $as->setTitle('Request Breakup Report');
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

    $data1 = $data->request_breakup_region($post, 'false');
    if (!empty($post['startdate'] && $post['enddate'])) {
        $duration = $post['startdate'] . " TO " . $post['enddate'];
    }else{
        $duration = " ALL ";
    }
    
    $data = array(
        'Users' => array(
            1 => array('','Region Wise Request Breakup','',''),
            2 => array('','Duration ', $duration,''),
            3 => array('Region', 'District', 'Sub Total' , 'Total'),
        ),
    );
    $i = 4;
    foreach ($data1 as $item) {
         $region = ( isset($item['region_id']) ) ? $item['region_id'] : 0;
         $region_name = Helpers_Utilities::get_region($region);                    
         $total_request = ( isset($item['total_request']) ) ? $item['total_request'] : 0;
                    
        $data['Users'][$i] = array($region_name,'','',$total_request);
        $i = $i + 1;
        
        $district['startdate'] = $post['startdate'];
        $district['enddate'] = $post['enddate'];
        $district['region_id'] = $item['region_id'];
        $dist = new Model_Userreport();
        $district_data = $dist->request_breakup_district($district, 'false');
        foreach ($district_data as $disdata)
        {
            $region_id = ( isset($disdata['region_id']) ) ? $disdata['region_id'] : 0;
            $region_name = Helpers_Utilities::get_region($region_id);   
           $posted = ( isset($disdata['posted']) ) ? $disdata['posted'] : 0;   
           $posted_encrypted=!empty($disdata['posted']) ? Helpers_Utilities::encrypted_key($disdata['posted'],"encrypt"):'';
            $office= Helpers_Profile::get_user_posting($posted);                    
            $dtotal_request = ( isset($disdata['total_request']) ) ? $disdata['total_request'] : 0;
            $data['Users'][$i] = array('',$office,$dtotal_request,'');
            $i = $i + 1;
            
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