<?php

if (isset($_POST['xport']) && $_POST['xport'] == 'excel') {
    //report stuff start
    /* Excel object */
    $XLSX = new Spreadsheet();
    $data = new Model_Persons;
    /* excel setting */
    $XLSX->set_active_sheet(0);

    $as = $XLSX->get_active_sheet();
    $as->setTitle('B-Party Subscriber');
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
            'left'     => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            'right'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            'vertical' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
        ),
        'fill' => array(
            'type'       => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array('argb' => 'FFFFFFCC'),
        ),
    );

    $as->getStyle('A1:I1')->applyFromArray($styleArray);
    $as->getStyle('B2')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
    $as->getDefaultColumnDimension()->setWidth(20);

    $pname = Helpers_Person::get_person_name($pid);
    // 4th arg `true` = restrict to valid Pakistani mobile MSISDNs only
    // (drops empty / NULL / short codes / landlines), so the export matches the on-screen view.
    $data1 = $data->cdr_summary($post, 'false', $pid, true);

    $data = array(
        'Users' => array(
            1 => array('', 'CTD KPK (B-Party Subscriber)/' . $pname, '', '', '', '', '', '', ''),
            2 => array('A Party', 'B Party', 'CNIC', 'Name', 'Address', 'BVS Status', 'SMS Count', 'Call Count', 'Total'),
        ),
    );

    $i = 3;
    foreach ($data1 as $item) {
        $phone_number = isset($item['phone_number']) ? $item['phone_number'] : 'NA';
        $other_phone  = isset($item['other_person_phone_number']) ? $item['other_person_phone_number'] : 'NA';

        // Subscriber lookup: aiesplus first, then subscriber_db (mobile) — same logic as ajaxbpartysubscriber()
        $cnic = $name = $address = $bvs = '';
        $person_profile_id = Helpers_Utilities::search_pid_of_mobile($other_phone);

        if (!empty($person_profile_id)) {
            $bvs = 'DRAMS Person';
            $name = Helpers_Person::get_person_name($person_profile_id);
        } else {
            $sub = Helpers_Subscriber::search('msisdn', $other_phone);
            if (!empty($sub['status']) && !empty($sub['data'])) {
                $row     = (array) $sub['data'];
                $cnic    = !empty($row['cnic'])       ? $row['cnic']       : '';
                $name    = !empty($row['name'])       ? $row['name']       : '';
                $address = !empty($row['address'])    ? $row['address']    : '';
                $bvs     = !empty($row['bvs_status']) ? $row['bvs_status'] : '';

                // Telco data is screaming caps — render in Word case to match the on-screen view.
                if ($name !== '')    $name    = ucwords(strtolower($name),    " \t\r\n\f\v.-");
                if ($address !== '') $address = ucwords(strtolower($address), " \t\r\n\f\v.-");
            } else {
                $bvs = 'Not Found';
            }
        }

        $incomingsms = isset($item['sms_received_count']) ? $item['sms_received_count'] : 0;
        $outgoingsms = isset($item['sms_sent_count'])     ? $item['sms_sent_count']     : 0;
        $totalSMS    = $incomingsms + $outgoingsms;

        $incomingcall = isset($item['calls_received_count']) ? $item['calls_received_count'] : 0;
        $outgoingcall = isset($item['calls_made_count'])     ? $item['calls_made_count']     : 0;
        $totalcalls   = $incomingcall + $outgoingcall;

        $total = $totalSMS + $totalcalls;

        $data['Users'][$i] = array(
            $phone_number,
            $other_phone,
            $cnic,
            $name,
            $address,
            $bvs,
            $totalSMS,
            $totalcalls,
            $total,
        );
        $i++;
    }

    $XLSX->setData($data, 1);

    // Force every cell in the populated range to TEXT.
    // Why: setData() -> setCellValueByColumnAndRow() auto-types numeric strings,
    // so 13-digit CNICs (and similar) get stored as floats and Excel displays them
    // as scientific notation (e.g. 3540504663891 -> 3.54E+12). Re-binding as
    // TYPE_STRING + format '@' preserves the original digits across all columns.
    $last_row = max(2, $i - 1);
    $range    = 'A1:I' . $last_row;
    $as->getStyle($range)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    foreach ($as->getRowIterator(1, $last_row) as $rowObj) {
        $cellIterator = $rowObj->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        foreach ($cellIterator as $cell) {
            $val = $cell->getValue();
            if ($val !== null && $val !== '') {
                $cell->setValueExplicit((string) $val, PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }
    }

    $name = 'ExcelReport';
    $doc  = 'excel'; //excel or pdf
    $file = $XLSX->save(array('path' => 'dist/files/', 'name' => $name), $doc);
    exit();
    //excel file generated
}
//report ending
