<?php

if (isset($_POST['xport']) && $_POST['xport'] == 'excel') {
    $posting = !empty($post['id']) ? Helpers_Utilities::encrypted_key($post['id'], 'decrypt') : 0;
    $office = Helpers_Profile::get_user_posting($posting);
    //report stuff start
    /* Excel object */
    $XLSX = new Spreadsheet();
    $data = new Model_Personsreports();
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

    $data1 = $data->person_list_district($post, 'false');

    $data = array(
        'Users' => array(
            1 => array( 'CTD Punjab (User List) of ' .$office),
            2 => array('Person Name', 'Father Name', 'CNIC', 'Category', 'Added By','Added Date'),
        ),
    );
    $i = 3;
    foreach ($data1 as $item) {
        $person_id = ( isset($item['person_id']) ) ? $item['person_id'] : 0;
                        $created_at = ( isset($item['pcreated']) ) ? $item['pcreated'] : 0;
                        $person_name = Helpers_Person::get_person_name($person_id);
                        $f_name = Helpers_Person::get_person_father_name($person_id);
                        $cnic = Helpers_Person::get_person_cnic($person_id);
                        $category_id = Helpers_Person::get_person_category_id($person_id);
                        $category = (isset($category_id)) ? Helpers_Utilities::get_category_name($category_id) : 'NA';
                        //$search_count = Helpers_Person::get_person_view_count($person_id);
                        $user = ( isset($item['first_name']) ) ? $item['first_name'] . ' ' . $item['last_name'] : 'Un-Known';
                        
        $data['Users'][$i] = array($person_name,$f_name,$cnic, $category,$user,$created_at);
        $i = $i + 1;
    }
    $XLSX->setData($data, 1);
    $XLSX->get_active_sheet()->mergeCells('A1:F1');
    //$XLSX->get_active_sheet()->mergeCells('A2:F2');
    $name = 'ExcelReport';
    $doc = 'excel'; //excel or pdf
    $file = $XLSX->save(array('path' => 'dist/files/', 'name' => $name), $doc);
    exit();
    //excel file genderated 
}
             
        //report ending