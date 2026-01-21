<?php

defined('SYSPATH') OR die('No direct script access.');
require_once 'src/Spout/Autoloader/autoload.php';

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

require_once DOCROOT . '/application/classes/Controller/excel/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Settings as SpreadsheetSettings;

/**
 *
 * @package    Upload Excel Helper
 * @category   Helpers
 */
/* working code no need to delete this....
  $file = Validation::factory($_FILES);
  $file->rules(
  'file', array(
  array(array('Upload', 'valid')),
  array(array('Upload', 'not_empty')),
  array('Upload::type', array(':value', array('xlsx', 'xls')))
  )
  );
  if ($file->check()) {
  $filename = explode('.', $_FILES['file']['name']);
  Upload::save($_FILES['file'], URL::title($filename[0], '-', true) . '.' . strtolower($filename[1]), 'uploads/cdr/manual/', 0777);
  }
 */

abstract class Helpers_Upload {

    //   use Box\Spout\Reader\ReaderFactory;
    //   use Box\Spout\Common\Type;

    public static function unziprar_file($file, $reference_number = NULL)
    {
        $path = pathinfo(realpath($file), PATHINFO_DIRNAME);
        $file_name = '';

        try {

            // PHP 7.4: Native RAR
            if (class_exists('RarArchive')) {
                $archive = RarArchive::open($file);
                $entries = $archive->getEntries();

                foreach ($entries as $entry) {
                    $target = $path . DIRECTORY_SEPARATOR . $entry->getName();
                    if (file_exists($target)) {
                        unlink($target);
                    }
                    $entry->extract($path);
                    $file_name = $entry->getName();
                    break;
                }
                $archive->close();
                return $file_name;
            }

            // PHP 8+: unrar CLI
            if (shell_exec('unrar') !== null) {
                exec(
                    'unrar x -o+ ' . escapeshellarg($file) . ' ' . escapeshellarg($path),
                    $out,
                    $status
                );

                if ($status === 0) {
                    $files = glob($path . DIRECTORY_SEPARATOR . '*');
                    return !empty($files) ? basename($files[0]) : '';
                }
            }

            return '';

        } catch (Exception $e) {
            Model_ErrorLog::log(
                'upload_unziprar_file',
                'RAR file extraction failed: ' . $e->getMessage(),
                array(
                    'request_id' => $reference_number,
                    'processing_index' => 3,
                    'file_id' => null,
                    'file_path' => $file,
                    'error_details' => $e->getMessage()
                ),
                $e->getTraceAsString(),
                'parsing_error',
                'file_upload'
            );
            Model_Email::email_status($reference_number, 2, 3);
            exit;
        }
    }


    public static function multiunziprar_file($file, $reference_number = NULL)
    {
        $path = pathinfo(realpath($file), PATHINFO_DIRNAME);
        $file_name = [];

        try {

            if (class_exists('RarArchive')) {
                $archive = RarArchive::open($file);
                $entries = $archive->getEntries();

                foreach ($entries as $entry) {
                    $target = $path . DIRECTORY_SEPARATOR . $entry->getName();
                    if (file_exists($target)) {
                        unlink($target);
                    }
                    $entry->extract($path);
                    $file_name[] = $entry->getName();
                }
                $archive->close();
                return $file_name;
            }

            if (shell_exec('unrar') !== null) {
                exec(
                    'unrar x -o+ ' . escapeshellarg($file) . ' ' . escapeshellarg($path),
                    $out,
                    $status
                );

                if ($status === 0) {
                    foreach (glob($path . DIRECTORY_SEPARATOR . '*') as $f) {
                        $file_name[] = basename($f);
                    }
                    return $file_name;
                }
            }

            return [];

        } catch (Exception $e) {
            Model_ErrorLog::log(
                'upload_multiunziprar_file',
                'Multiple RAR file extraction failed: ' . $e->getMessage(),
                array(
                    'request_id' => $reference_number,
                    'processing_index' => 3,
                    'file_id' => null,
                    'file_path' => $file,
                    'error_details' => $e->getMessage()
                ),
                $e->getTraceAsString(),
                'parsing_error',
                'file_upload'
            );
            Model_Email::email_status($reference_number, 2, 3);
            exit;
        }
    }



    public static function unzip_file($file, $reference_number = NULL)
    {
        $path = pathinfo(realpath($file), PATHINFO_DIRNAME);

        try {
            $zip = new ZipArchive;
            $res = $zip->open($file);
            $filename = $zip->getNameIndex(0);

            if ($res === TRUE) {
                try {
                    $zip->extractTo($path);
                } catch (Exception $e) {
                    Model_ErrorLog::log(
                        'upload_unzip_file',
                        'ZIP file extraction failed during extractTo: ' . $e->getMessage(),
                        array(
                            'request_id' => $reference_number,
                            'processing_index' => 3,
                            'file_id' => null,
                            'file_path' => $file,
                            'extraction_path' => $path,
                            'error_details' => $e->getMessage()
                        ),
                        $e->getTraceAsString(),
                        'parsing_error',
                        'file_upload'
                    );
                }
                $zip->close();
                return $filename;
            } else {
                // Log when zip file fails to open
                Model_ErrorLog::log(
                    'upload_unzip_file',
                    'Failed to open ZIP file. Error code: ' . $res,
                    array(
                        'request_id' => $reference_number,
                        'processing_index' => 3,
                        'file_id' => null,
                        'file_path' => $file,
                        'zip_error_code' => $res,
                        'error_details' => 'ZipArchive::open() returned error code ' . $res
                    ),
                    debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
                    'parsing_error',
                    'file_upload'
                );
                return '';
            }

        } catch (Exception $e) {
            Model_ErrorLog::log(
                'upload_unzip_file',
                'ZIP file extraction failed: ' . $e->getMessage(),
                array(
                    'request_id' => $reference_number,
                    'processing_index' => 3,
                    'file_id' => null,
                    'file_path' => $file,
                    'error_details' => $e->getMessage()
                ),
                $e->getTraceAsString(),
                'parsing_error',
                'file_upload'
            );
            Model_Email::email_status($reference_number, 2, 3);
            exit;
        }
    }



    public static function unzip_file_multiple($file, $name)
    {
        $path = pathinfo(realpath($file), PATHINFO_DIRNAME);
        $zip = new ZipArchive;
        $res = $zip->open($file);

        $imei_extr = substr($name, 0, 14);

        if ($res === TRUE) {
            if ($zip->numFiles > 1) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (strpos($filename, $imei_extr) !== false) {
                        break;
                    }
                }
            } else {
                $filename = $zip->getNameIndex(0);
            }

            try {
                $zip->extractTo($path);
            } catch (Exception $e) {
                Model_ErrorLog::log(
                    'upload_unzip_file_multiple',
                    'ZIP file extraction failed during extractTo: ' . $e->getMessage(),
                    array(
                        'file_path' => $file,
                        'extraction_path' => $path,
                        'imei_extract' => $imei_extr,
                        'error_details' => $e->getMessage()
                    ),
                    $e->getTraceAsString(),
                    'parsing_error',
                    'file_upload'
                );
            }
            $zip->close();
            return $filename;
        } else {
            // Log when zip file fails to open
            Model_ErrorLog::log(
                'upload_unzip_file_multiple',
                'Failed to open ZIP file. Error code: ' . $res,
                array(
                    'file_path' => $file,
                    'imei_extract' => $imei_extr,
                    'zip_error_code' => $res,
                    'error_details' => 'ZipArchive::open() returned error code ' . $res
                ),
                debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS),
                'parsing_error',
                'file_upload'
            );
        }

        return '';
    }


    /* CDR Data */
    public static function insert_file_record($file, $created_by, $request_type, $company_name, $requested_value, $request_id, $reason, $file_id) {
        if($request_type == 2){
            $imei = $requested_value;
            $phone = '';
        } else {
            $imei = '';
            $phone = $requested_value;
        }
        $created_on = date("Y-m-d h:i");
        $is_manual = 2;
        $description = $reason;
        $upload_status = 0;

            return $query = DB::insert('files', array('id', 'file', 'created_by', 'request_type', 'company_name', 'imei', 'phone_number' , 'request_id', 'created_on',
                    'is_manual', 'description', 'upload_status'))
                ->values(array($file_id, $file, $created_by, $request_type, $company_name, $imei, $phone, $request_id, $created_on, $is_manual, $description, $upload_status))
                ->execute();
    }

    public static function upload_file_record($file, $created_by, $request_type, $company_name, $imei, $request_id) {
        if ($request_type == "cdr") {
            $request_type = 1;
        } else {
            $request_type = 2;
        }
        $created_on = date("Y-m-d h:i");
        $is_manual = 2;
        $description = "cdr against IMEI";
        $upload_status = 1;

        return $query = DB::insert('files', array('file', 'created_by', 'request_type', 'company_name', 'imei', 'request_id', 'created_on',
                    'is_manual', 'description', 'upload_status'))
                ->values(array($file, $created_by, $request_type, $company_name, $imei, $request_id, $created_on, $is_manual, $description, $upload_status))
                ->execute();
    }

    public static function upload_file($files, $post, $user_id, $folder_name, $is_manual, $imei = NULL, $desc = Null) {

        $extension = explode(".", $files['file']['name']);
        $_POST['created_on'] = date("Y-m-d h:i");
        $_POST['is_manual'] = $is_manual;
        $_POST['created_by'] = $user_id;
        $_POST['description'] = ($desc == NULL) ? 'cdr against mobile no' : $desc;
        //getting file_id           
        $_FILES['file']['id'] = Helpers_Utilities::id_generator("file_id");
        $_POST['id'] = $_FILES['file']['id'];

        $folder_name =  !empty($_FILES['file']['id']) ? Helpers_Upload::get_request_data_path($_FILES['file']['id'],'save') : '';

        /* if ($_POST['company_name'] == 'zong')
          $_FILES['file']['file_new_name'] = $user_id . date("Ymdhi") . '.xls';
          else */
        if($imei==NULL)
            $rqt_id = 1;
        else
            $rqt_id = 2;

        //$_FILES['file']['file_new_name'] = 'rqt'. $rqt_id. 'fid' .$_POST['id'] . date("Ymdhi") . '.' . $extension[sizeof($extension) - 1];
        $_FILES['file']['file_new_name'] = 'rqt'. $rqt_id. 'fid' .$_FILES['file']['id'] .'.' . $extension[sizeof($extension) - 1];

        $_FILES['file']['file_folder_name'] = $folder_name;  //mail
        //$file = ORM::factory('File')->values(Arr::merge($_FILES, $this->request->post()));
        $file = ORM::factory('File')->values(Arr::merge($_FILES, $_POST));
        // try upload and save file and file info
        try {
            // save
            $file->save();
            //$file->save($_FILES['file'], NULL, 'uploads/cdr/manual/');
            // set user message
            Session::instance()->set('message', 'File is successfully uploaded');
        } catch (ORM_Validation_Exception $e) {
            // prepare errors
            $errors = $e->errors('upload');
            $errors = Arr::merge($errors, Arr::get($errors, '_external', array()));
            // remove external errors
            unset($errors['_external']);
            // set user errors
            Session::instance()->set('errors', $errors);
        }
//        if ($imei == NULL)
//            return 'uploads/cdr' . '/' . $folder_name . '/' . $_FILES['file']['file_new_name'];
//        else
//            return $file;
        return $folder_name;
    }



// ... (other code remains the same) ...

public static function data_mapping_partially($path, $company, $field_imei_no, $file_id, $userrequestid) {
    ini_set('mysql.connect_timeout', 1000);
    ini_set('default_socket_timeout', 1000);
    date_default_timezone_set('GMT');

    /* Global Indexing */
    $call_type = '';
    $party_a = '';
    $party_b = '';
    $date_time = '';
    $duration = '';
    $imei = '';
    $imsi = '';
    $site = '';

    /* Global Variable for Checking */
    $person_id = '';
    $imei_exist_file = '';
    $imei_exist_db = '';
    $imei_number = '';
    $device_id = "";
    $party_a = "";
    $mnc = $company;
    $date_right = '';
    $date_right_last = '';
    $sms_table_record = array();
    $call_table_record = array();

    $flag = '';
    $telenor_cdr = ['MSISDN', 'CALL_ORIG_NUM', 'CALL_DIALED_NUM', 'IMSI', 'IMEI', 'CALL_START_DT_TM', 'CALL_END_DT_TM', 'INBOUND_OUTBOUND_IND', 'Call_Network_Volume', 'Cell_Lac_Id', 'Cell_Site_Id', 'ORIG_OPER_NAME', 'TERM_OPER_NAME', 'CALL_TYPE', 'Location'];
    $telenor_cdr_2 = ['MSISDN', 'call_org_num', 'CALL_DIALED_NUM', 'IMSI', 'IMEI', 'CALL_START_DT_TM', 'CALL_END_DT_TM', 'INBOUND_OUTBOUND_IND', 'Call_Network_Volume', 'Lac_Id','Site_Id', 'CELL_SITE_ID', 'LAT', 'LONGITUDE', 'CALL_TYPE', 'LOCATION'];
    $jazz_cdr = ['Sr #', 'IMEI', 'Date & Time', 'A-Party'];
    $ufone_cdr = ['IMEI', 'IMSI', 'Start Time', 'End Time', 'Service Provider', 'Type', 'Direction', 'Location', 'Cell Id', 'Cell Sector', 'Latitude', 'Longitude', 'Duration'];
    $zong_cdr = ['Mobile No', 'IMEI', 'LAST_ACTIVITY_DATE'];
    $warid_cdr = ['subno', 'NAME', 'imei_number', 'address', 'nic', 'connection', 'upddate', 'histfound'];
    /* Global Variable end */

    $inputfilename = $path;

    if ($company != 4) {
        // Read your Excel workbook using PhpSpreadsheet
        try {
            $inputfiletype = IOFactory::identify($inputfilename);
            $objReader = IOFactory::createReader($inputfiletype);

            // Read only data (without formatting) for memory and time performance
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($inputfilename);
        } catch (Exception $e) {
            if (!empty($userrequestid)) {
                Model_ErrorLog::log(
                    'upload_data_mapping_partially',
                    'Failed to load Excel file for partially parsing: ' . $e->getMessage(),
                    array(
                        'request_id' => $userrequestid,
                        'processing_index' => 5,
                        'file_id' => $file_id,
                        'file_path' => $path,
                        'company' => $company,
                        'error_details' => $e->getMessage()
                    ),
                    $e->getTraceAsString(),
                    'not_found',
                    'file_upload'
                );
                $reference_number = Model_Email::email_status($userrequestid, 2, 5);
            }
            if (!empty($file_id))
                $error_number = Model_Email::file_status($file_id, 0, 2);
            die('Error loading file "' . pathinfo($inputfilename, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        // Get worksheet dimensions
        ini_set('memory_limit', '4096M');
        $filePath = $inputfilename;

        if ($filePath) {
            $objPHPExcel = IOFactory::load($filePath);

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                $worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10
                $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
                $data = array();

                for ($row = 1; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $cell = $worksheet->getCellByColumnAndRow($col + 1, $row); // Note: PhpSpreadsheet uses 1-based column index
                        $val = $cell->getValue();

                        if (SpreadsheetDate::isDateTime($cell)) {
                            $val = SpreadsheetDate::excelToTimestamp($val);
                        }

                        if (isset($val) && $val)
                            $data[$row][$col] = $val;
                    }
                }

                //$excelData[$worksheetTitle] = $data;
                $row = 1;
                if (empty($data[$row])) {
                    if ($objPHPExcel->getSheetCount() > 1) {
                        continue;
                    } else {
                        if (!empty($file_id))
                            $error_number = Model_Email::file_status($file_id, 1, 3); // 1 company  format not match
                    }
                }
                $continue = '';

                include 'parse/cellmatchPartially.inc';

                if ($continue == 'continue')
                    continue;
                if (empty($flag)) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 1, 3); // 1 company  format not match

                    if (!empty($userrequestid))
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    exit;
                }

                switch ($flag) {
                    case 'warid_cdr':
                        if ($company != 7) {
                            if (!empty($file_id))
                                $error_number = Model_Email::file_status($file_id, 6, 3); // 1 company  format not match
                            if (!empty($userrequestid)) {
                                Model_ErrorLog::log(
                                    'upload_data_mapping_partially',
                                    'Company mismatch: Warid CDR format detected but company is not Warid (expected 7, got ' . $company . ')',
                                    array(
                                        'request_id' => $userrequestid,
                                        'processing_index' => 3,
                                        'file_id' => $file_id,
                                        'file_path' => $path,
                                        'company' => $company,
                                        'expected_company' => 7,
                                        'detected_format' => 'warid_cdr'
                                    ),
                                    null,
                                    'validation_error',
                                    'file_upload'
                                );
                                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                            }
                            exit;
                        }
                        try {
                            include 'parse_partially/warid_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'zong_cdr':
                        if ($company != 4) {
                            if (!empty($file_id))
                                $error_number = Model_Email::file_status($file_id, 6, 3); // 1 company  format not match
                            if (!empty($userrequestid)) {
                                Model_ErrorLog::log(
                                    'upload_data_mapping_partially',
                                    'Company mismatch: Zong CDR format detected but company is not Zong (expected 4, got ' . $company . ')',
                                    array(
                                        'request_id' => $userrequestid,
                                        'processing_index' => 3,
                                        'file_id' => $file_id,
                                        'file_path' => $path,
                                        'company' => $company,
                                        'expected_company' => 4,
                                        'detected_format' => 'zong_cdr'
                                    ),
                                    null,
                                    'validation_error',
                                    'file_upload'
                                );
                                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                            }
                            exit;
                        }
                        try {
                            include 'parse_partially/zong_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'ufone_cdr':
                        if ($company != 3) {
                            if (!empty($file_id))
                                $error_number = Model_Email::file_status($file_id, 6, 3); // 1 company  format not match
                            if (!empty($userrequestid)) {
                                Model_ErrorLog::log(
                                    'upload_data_mapping_partially',
                                    'Company mismatch: Ufone CDR format detected but company is not Ufone (expected 3, got ' . $company . ')',
                                    array(
                                        'request_id' => $userrequestid,
                                        'processing_index' => 3,
                                        'file_id' => $file_id,
                                        'file_path' => $path,
                                        'company' => $company,
                                        'expected_company' => 3,
                                        'detected_format' => 'ufone_cdr'
                                    ),
                                    null,
                                    'validation_error',
                                    'file_upload'
                                );
                                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                            }
                            exit;
                        }
                        try {
                            include 'parse_partially/ufone_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'telnor_cdr':
                        if ($company != 6) {
                            if (!empty($file_id))
                                $error_number = Model_Email::file_status($file_id, 6, 3); // 1 company  format not match
                            if (!empty($userrequestid)) {
                                Model_ErrorLog::log(
                                    'upload_data_mapping_partially',
                                    'Company mismatch: Telenor CDR format detected but company is not Telenor (expected 6, got ' . $company . ')',
                                    array(
                                        'request_id' => $userrequestid,
                                        'processing_index' => 3,
                                        'file_id' => $file_id,
                                        'file_path' => $path,
                                        'company' => $company,
                                        'expected_company' => 6,
                                        'detected_format' => 'telnor_cdr'
                                    ),
                                    null,
                                    'validation_error',
                                    'file_upload'
                                );
                                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                            }
                            exit;
                        }
                        try {
                            include 'parse_partially/telnor_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'jazz_cdr':
                        if ($company != 1 && $company != 7) {
                            if (!empty($file_id))
                                $error_number = Model_Email::file_status($file_id, 6, 3); // 1 company  format not match
                            if (!empty($userrequestid)) {
                                Model_ErrorLog::log(
                                    'upload_data_mapping_partially',
                                    'Company mismatch: Jazz CDR format detected but company is not Jazz or Warid (expected 1 or 7, got ' . $company . ')',
                                    array(
                                        'request_id' => $userrequestid,
                                        'processing_index' => 3,
                                        'file_id' => $file_id,
                                        'file_path' => $path,
                                        'company' => $company,
                                        'expected_company' => '1 or 7',
                                        'detected_format' => 'jazz_cdr'
                                    ),
                                    null,
                                    'validation_error',
                                    'file_upload'
                                );
                                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                            }
                            exit;
                        }
                        try {
                            include 'parse_partially/jazz_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                }

                /* Parsing End */
                if (!empty(Auth::instance()->get_user())) {
                    $login_user = Auth::instance()->get_user();
                    $login_id = $login_user->id;
                } else {
                    $login_id = 9999;
                }
                $uid = $login_id;
                Helpers_Profile::user_activity_log($uid, 25, NULL, NULL, $person_id);

                if (!empty($userrequestid)) {
                    Model_ErrorLog::log(
                        'upload_data_mapping_partially',
                        'File processing completed successfully - setting status to completed',
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 5,
                            'file_id' => $file_id,
                            'file_path' => $path,
                            'company' => $company,
                            'person_id' => $person_id
                        ),
                        null,
                        'not_found',
                        'file_upload'
                    );
                    $reference_number = Model_Email::email_status($userrequestid, 2, 5);
                }
                if (!empty($file_id))
                    $error_number = Model_Email::file_status($file_id, 0, 2);
                exit;
            }
        }
    } else {
        $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
        $filePath = $inputfilename;
        $reader->open($filePath);
        $help = $reader;
        $data = array();
        $total_Sheet = 0;
        foreach ($help->getSheetIterator() as $sheetIndex => $sheet) {
            $total_Sheet +=1;
        }
        $continue = '';
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $data[] = $row;
            }

            $row = 0;
            if (empty($data[$row])) {
                if ($total_Sheet > 1) {
                    continue;
                } else {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 1, 3);
                }
            }

            include 'parse/cellmatch.inc';

            if ($continue != 'continue') {
                if (empty($flag)) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 1, 3);

                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping_partially',
                            'File format not recognized (Zong/Spout path) - company format does not match',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'flag_status' => 'empty'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                break;
            }
        }
        $reader->close();

        switch ($flag) {
            case 'warid_cdr':
                if ($company != 7) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 6, 3);

                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping_partially',
                            'Company mismatch (Zong path): Warid CDR format detected but company is not Warid (expected 7, got ' . $company . ')',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'expected_company' => 7,
                                'detected_format' => 'warid_cdr'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                try {
                    include 'parse/warid_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'zong_cdr':
                if ($company != 4) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 6, 3);

                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping_partially',
                            'Company mismatch (Zong path): Zong CDR format detected but company is not Zong (expected 4, got ' . $company . ')',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'expected_company' => 4,
                                'detected_format' => 'zong_cdr'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                try {
                    include 'parse/zong_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'ufone_cdr':
                if ($company != 3) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 6, 3);

                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping_partially',
                            'Company mismatch (Zong path): Ufone CDR format detected but company is not Ufone (expected 3, got ' . $company . ')',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'expected_company' => 3,
                                'detected_format' => 'ufone_cdr'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                try {
                    include 'parse/ufone_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'telnor_cdr':
                if ($company != 6) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 6, 3);

                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping_partially',
                            'Company mismatch (Zong path): Telenor CDR format detected but company is not Telenor (expected 6, got ' . $company . ')',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'expected_company' => 6,
                                'detected_format' => 'telnor_cdr'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                try {
                    include 'parse/telnor_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'jazz_cdr':
                if ($company != 1) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 6, 3);

                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping_partially',
                            'Company mismatch (Zong path): Jazz CDR format detected but company is not Jazz (expected 1, got ' . $company . ')',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'expected_company' => 1,
                                'detected_format' => 'jazz_cdr'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                try {
                    include 'parse/jazz_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
        }

        if (!empty(Auth::instance()->get_user())) {
            $login_user = Auth::instance()->get_user();
            $login_id = $login_user->id;
        } else {
            $login_id = 9999;
        }
        $uid = $login_id;
        Helpers_Profile::user_activity_log($uid, 25, NULL, NULL, $person_id);

        if (!empty($userrequestid)) {
            Model_ErrorLog::log(
                'upload_data_mapping_partially',
                'File processing completed successfully (Zong path) - setting status to completed',
                array(
                    'request_id' => $userrequestid,
                    'processing_index' => 5,
                    'file_id' => $file_id,
                    'file_path' => $path,
                    'company' => $company,
                    'person_id' => $person_id
                ),
                null,
                'not_found',
                'file_upload'
            );
            $reference_number = Model_Email::email_status($userrequestid, 2, 5);
        }
        if (!empty($file_id))
            $error_number = Model_Email::file_status($file_id, 0, 2);
        exit;
    }
}

/* Full Parsing against IMEI */
public static function data_mapping_full($path, $company, $file_id, $imei_field = NULL, $userrequestid = Null) {
    date_default_timezone_set('GMT');

    /* Global Indexing */
    $call_type = '';
    $party_a = '';
    $party_b = '';
    $date_time = '';
    $duration = '';
    $imei = '';
    $imsi = '';
    $site = '';

    /* Global Variable for Checking */
    $person_id = '';
    $imei_exist_file = '';
    $imei_exist_db = '';
    $imei_number = '';
    $device_id = "";
    $party_a = "";
    $mnc = $company;
    $date_right = '';
    $date_right_last = '';
    $sms_table_record = array();
    $call_table_record = array();

    $flag = '';
    $telenor_cdr = ['MSISDN', 'CALL_ORIG_NUM', 'CALL_DIALED_NUM', 'IMSI', 'IMEI', 'CALL_START_DT_TM', 'CALL_END_DT_TM', 'INBOUND_OUTBOUND_IND', 'Call_Network_Volume', 'Cell_Lac_Id', 'Cell_Site_Id', 'ORIG_OPER_NAME', 'TERM_OPER_NAME', 'CALL_TYPE', 'Location'];
    $jazz_cdr = ['Sr #', 'Call Type', 'A-Party', 'B-Party', 'Date & Time', 'Duration', 'Cell ID', 'IMEI', 'IMSI', 'Site'];
    $ufone_cdr = ['IMSI', 'IMEI', 'A Number', 'B Number', 'Call Start Time', 'Call End Time', 'Call Duration', 'Call Type', 'Type', 'Service Type', 'Cell ID - A', 'Cell Sector', 'Location - A'];
    $warid_cdr = ['SUBNO', 'B_SUBNO', 'TRANSDATE', 'A_TRANSDATE', 'TRANSTIME', 'DURATION', 'CELL_ID', 'DESCRIPTION', 'IMEI_NUMBER', 'OPER'];
    $zong_cdr = ['CALL_TYPE', 'MSISDN_ID', 'STRT_TM', 'BNUMBER', 'MINS', 'SECS', 'LAC_ID', 'CELL_ID', 'IMEI', 'SITE_ADDRESS', 'LNG', 'LAT'];
    $warid_sub = ['subno', 'NAME', 'imei_number', 'address', 'nic', 'connection', 'upddate', 'histfound'];
    /* Global Variable end */

    $inputfilename = DOCROOT . $path;

    if ($company != 4) {
        // Read your Excel workbook using PhpSpreadsheet
        try {
            $inputfiletype = IOFactory::identify($inputfilename);
            $objReader = IOFactory::createReader($inputfiletype);

            /// Read only data (without formatting) for memory and time performance
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($inputfilename);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputfilename, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        ini_set('memory_limit', '9999999990024M');
        $filePath = $inputfilename;

        if ($filePath) {
            $objPHPExcel = IOFactory::load($filePath);

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                $worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
                $data = array();

                for ($row = 1; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $cell = $worksheet->getCellByColumnAndRow($col + 1, $row);
                        $val = $cell->getValue();

                        if (SpreadsheetDate::isDateTime($cell)) {
                            $val = SpreadsheetDate::excelToTimestamp($val);
                        }

                        if (isset($val) && $val)
                            $data[$row][$col] = $val;
                    }
                }

                /* Parsing Start */
                $row = 1;
                /* Telnor CDR */
                $compare_telenor_cdr = array_diff($telenor_cdr, array_map('trim', $data[$row]));
                /* Jazz CDR */
                $compare_jazz_cdr = array_diff($jazz_cdr, array_map('trim', $data[$row]));
                /* Ufone CDR */
                $compare_ufone_cdr = array_diff($ufone_cdr, array_map('trim', $data[$row]));
                /* Warid CDR */
                $compare_warid_cdr = array_diff($warid_cdr, array_map('trim', $data[$row]));
                /* Zong CDR */
                $compare_zong_cdr = ('Mobile' == $data[$row][0] && !isset($data[$row][2]) && !isset($data[$row][3])) ? 1 : 0;
                /* Warid Subscription */
                $compare_warid_sub = array_diff($warid_sub, $data[$row]);

                if (empty($compare_telenor_cdr)) {
                    $flag = 'telnor_cdr';

                    /* Index Setting */
                    $data[$row] = array_map("strtoupper", $data[$row]);
                    $call_type_index = array_search('CALL_TYPE', array_map('trim', $data[$row]));
                    $call_type_dir_index = array_search('INBOUND_OUTBOUND_IND', array_map('trim', $data[$row]));
                    $party_aindex = array_search('MSISDN', array_map('trim', $data[$row]));
                    $party_bindex = array_search('CALL_ORIG_NUM', array_map('trim', $data[$row]));
                    $party_cindex = array_search('CALL_DIALED_NUM', array_map('trim', $data[$row]));
                    $date_time_index = array_search('CALL_START_DT_TM', array_map('trim', $data[$row]));
                    $duration_index = array_search('CALL_END_DT_TM', array_map('trim', $data[$row]));
                    $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                    $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                    $site_index = array_search('LOCATION', array_map('trim', $data[$row]));
                    $cel_lac_index = array_search('CELL_LAC_ID', array_map('trim', $data[$row]));
                    $cell_site_index = array_search('CELL_SITE_ID', array_map('trim', $data[$row]));
                } elseif (empty($compare_jazz_cdr)) {
                    $flag = 'jazz_cdr';
                    /* Index Setting */
                    $data[$row] = array_map("strtoupper", $data[$row]);
                    $call_type_index = array_search('CALL TYPE', array_map('trim', $data[$row]));
                    $party_aindex = array_search('A-PARTY', array_map('trim', $data[$row]));
                    $party_bindex = array_search('B-PARTY', array_map('trim', $data[$row]));
                    $date_time_index = array_search('DATE & TIME', array_map('trim', $data[$row]));
                    $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
                    $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                    $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                    $site_index = array_search('SITE', array_map('trim', $data[$row]));
                    $cel_lac_index = array_search('LAC ID', array_map('trim', $data[$row]));
                    $cell_site_index = array_search('CELL ID', array_map('trim', $data[$row]));
                } elseif (empty($compare_ufone_cdr)) {
                    $flag = 'ufone_cdr';

                    /* Index Setting */
                    $data[$row] = array_map("strtoupper", $data[$row]);
                    $call_type_index = array_search('CALL TYPE', array_map('trim', $data[$row]));
                    $call_type_dir_index = array_search('TYPE', array_map('trim', $data[$row]));
                    $party_aindex = array_search('A NUMBER', array_map('trim', $data[$row]));
                    $party_bindex = array_search('B NUMBER', array_map('trim', $data[$row]));
                    $date_times_index = array_search('CALL START TIME', array_map('trim', $data[$row]));
                    $date_timee_index = array_search('CALL END TIME', array_map('trim', $data[$row]));
                    $duration_index = array_search('CALL DURATION', array_map('trim', $data[$row]));
                    $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
                    $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
                    $site_index = array_search('LOCATION - A', array_map('trim', $data[$row]));
                    $cell_site_index = array_search('CELL ID - A', array_map('trim', $data[$row]));
                    $cel_lac_index = array_search('LAC ID -A', array_map('trim', $data[$row]));
                } elseif (empty($compare_warid_cdr)) {
                    $flag = 'warid_cdr';
                    /* Index Setting */
                    $data[$row] = array_map("strtoupper", $data[$row]);
                    $call_type_index = array_search('OPER', array_map('trim', $data[$row]));  //OPER column in Warid
                    $party_aindex = array_search('SUBNO', array_map('trim', $data[$row]));  //SUBNO in warid
                    $party_bindex = array_search('B_SUBNO', array_map('trim', $data[$row])); //B_SUBNO in warid
                    $date_index = array_search('TRANSDATE', array_map('trim', $data[$row]));
                    $time_index = array_search('TRANSTIME', array_map('trim', $data[$row]));
                    $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
                    $imei_index = array_search('IMEI_NUMBER', array_map('trim', $data[$row]));
                    $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));   // Yet not available
                    $site_index = array_search('DESCRIPTION', array_map('trim', $data[$row]));
                    $cell_site_index = array_search('CELL_ID', array_map('trim', $data[$row]));
                    $cel_lac_index = array_search('LAC ID -A', array_map('trim', $data[$row]));
                } elseif (empty($compare_warid_sub)) {
                    $flag = 'warid_sub';
                } elseif (!empty($compare_zong_cdr) && $compare_zong_cdr == 1) {
                    $flag = 'zong_cdr';
                    $call_type_index = array_search('CALL_TYPE', array_map('trim', $data[5]));  //OPER column in Warid
                    $party_aindex = array_search('MSISDN_ID', array_map('trim', $data[5]));  //SUBNO in warid
                    $party_bindex = array_search('BNUMBER', array_map('trim', $data[5])); //B_SUBNO in warid
                    $date_index = array_search('STRT_TM', array_map('trim', $data[5]));
                    $time_index = array_search('MINS', array_map('trim', $data[5]));
                    $duration_index = array_search('SECS', array_map('trim', $data[5]));
                    $imei_index = array_search('IMEI', array_map('trim', $data[5]));
                    $imsi_index = array_search('IMSI', array_map('trim', $data[5]));   // Yet not available
                    $site_index = array_search('SITE_ADDRESS', array_map('trim', $data[5]));
                    $lng = array_search('LNG', array_map('trim', $data[5]));
                    $lat = array_search('LAT', array_map('trim', $data[5]));
                }

                switch ($flag) {
                    case 'warid_cdr':
                        try {
                            include 'parse_full/warid_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'zong_cdr':
                        try {
                            include 'parse_full/zong_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'ufone_cdr':
                        try {
                            include 'parse_full/ufone_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'telnor_cdr':
                        try {
                            include 'parse_full/telnor_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'jazz_cdr':
                        try {
                            include 'parse_full/jazz_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                }

                /* Parsing End */
                Model_Generic::update_file_status($file_id);
                if (!empty($userrequestid)) {
                    Model_ErrorLog::log(
                        'upload_data_mapping_full',
                        'Full CDR file processing completed successfully - setting status to completed',
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 5,
                            'file_id' => $file_id,
                            'file_path' => $path,
                            'imei_field' => $imei_field
                        ),
                        null,
                        'not_found',
                        'file_upload'
                    );
                    $reference_number = Model_Email::email_status($userrequestid, 2, 5);
                }
                echo '1';
                exit;
            }
        }
    } else {
        $reader = ReaderFactory::create(Type::XLSX);
        $filePath = $inputfilename;
        $reader->open($filePath);
        $data = array();
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $data[] = $row;
            }
        }
        $reader->close();

        /* Parsing Start */
        $row = 0;
        $compare_telenor_cdr = array_diff($telenor_cdr, array_map('trim', $data[$row]));
        $compare_jazz_cdr = array_diff($jazz_cdr, array_map('trim', $data[$row]));
        $compare_ufone_cdr = array_diff($ufone_cdr, array_map('trim', $data[$row]));
        $compare_warid_cdr = array_diff($warid_cdr, array_map('trim', $data[$row]));
        $compare_zong_cdr = ('Mobile' == $data[$row][0] && !isset($data[$row][2]) && !isset($data[$row][3])) ? 1 : 0;
        $compare_warid_sub = array_diff($warid_sub, $data[$row]);

        if (empty($compare_telenor_cdr)) {
            $flag = 'telnor_cdr';
            $call_type_index = array_search('CALL_TYPE', array_map('trim', $data[$row]));
            $call_type_dir_index = array_search('INBOUND_OUTBOUND_IND', array_map('trim', $data[$row]));
            $party_aindex = array_search('MSISDN', array_map('trim', $data[$row]));
            $party_bindex = array_search('CALL_ORIG_NUM', array_map('trim', $data[$row]));
            $party_cindex = array_search('CALL_DIALED_NUM', array_map('trim', $data[$row]));
            $date_time_index = array_search('CALL_START_DT_TM', array_map('trim', $data[$row]));
            $duration_index = array_search('CALL_END_DT_TM', array_map('trim', $data[$row]));
            $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
            $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
            $site_index = array_search('Location', array_map('trim', $data[$row]));
        } elseif (empty($compare_jazz_cdr)) {
            $flag = 'jazz_cdr';
            $call_type_index = array_search('Call Type', array_map('trim', $data[$row]));
            $party_aindex = array_search('A-Party', array_map('trim', $data[$row]));
            $party_bindex = array_search('B-Party', array_map('trim', $data[$row]));
            $date_time_index = array_search('Date & Time', array_map('trim', $data[$row]));
            $duration_index = array_search('Duration', array_map('trim', $data[$row]));
            $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
            $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
            $site_index = array_search('Site', array_map('trim', $data[$row]));
        } elseif (empty($compare_ufone_cdr)) {
            $flag = 'ufone_cdr';
            $call_type_index = array_search('Call Type', array_map('trim', $data[$row]));
            $call_type_dir_index = array_search('Type', array_map('trim', $data[$row]));
            $party_aindex = array_search('A Number', array_map('trim', $data[$row]));
            $party_bindex = array_search('B Number', array_map('trim', $data[$row]));
            $date_times_index = array_search('Call Start Time', array_map('trim', $data[$row]));
            $date_timee_index = array_search('Call End Time', array_map('trim', $data[$row]));
            $duration_index = array_search('Call Duration', array_map('trim', $data[$row]));
            $imei_index = array_search('IMEI', array_map('trim', $data[$row]));
            $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
            $site_index = array_search('Location - A', array_map('trim', $data[$row]));
        } elseif (empty($compare_warid_cdr)) {
            $flag = 'warid_cdr';
            $call_type_index = array_search('OPER', array_map('trim', $data[$row]));
            $party_aindex = array_search('SUBNO', array_map('trim', $data[$row]));
            $party_bindex = array_search('B_SUBNO', array_map('trim', $data[$row]));
            $date_index = array_search('TRANSDATE', array_map('trim', $data[$row]));
            $time_index = array_search('TRANSTIME', array_map('trim', $data[$row]));
            $duration_index = array_search('DURATION', array_map('trim', $data[$row]));
            $imei_index = array_search('IMEI_NUMBER', array_map('trim', $data[$row]));
            $imsi_index = array_search('IMSI', array_map('trim', $data[$row]));
            $site_index = array_search('DESCRIPTION', array_map('trim', $data[$row]));
        } elseif (empty($compare_warid_sub)) {
            $flag = 'warid_sub';
        } elseif (!empty($compare_zong_cdr) && $compare_zong_cdr == 1) {
            $flag = 'zong_cdr';
            $index_no = 3;
            $data[$index_no] = array_map("strtoupper", $data[$index_no]);
            $call_type_index = array_search('CALL_TYPE', array_map('trim', $data[$index_no]));
            if (isset($call_type_index) && $call_type_index != 0) {
                $index_no = 4;
                $data[$index_no] = array_map("strtoupper", $data[$index_no]);
                $call_type_index = array_search('CALL_TYPE', array_map('trim', $data[$index_no]));
            }
            $party_aindex = array_search('MSISDN_ID', array_map('trim', $data[$index_no]));
            $party_bindex = array_search('BNUMBER', array_map('trim', $data[$index_no]));
            $date_index = array_search('STRT_TM', array_map('trim', $data[$index_no]));
            $time_index = array_search('MINS', array_map('trim', $data[$index_no]));
            $duration_index = array_search('SECS', array_map('trim', $data[$index_no]));
            $imei_index = array_search('IMEI', array_map('trim', $data[$index_no]));
            $imsi_index = array_search('IMSI', array_map('trim', $data[$index_no]));
            $site_index = array_search('SITE_ADDRESS', array_map('trim', $data[$index_no]));
            $lng = array_search('LNG', array_map('trim', $data[$index_no]));
            $lat = array_search('LAT', array_map('trim', $data[$index_no]));
            $cel_lac_index = array_search('LAC_ID', array_map('trim', $data[$index_no]));
            $cell_site_index = array_search('CELL_ID', array_map('trim', $data[$index_no]));
        }

        switch ($flag) {
            case 'warid_cdr':
                if ($company != 7) {
                    echo 404;
                    exit;
                }
                try {
                    include 'parse/warid_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'zong_cdr':
                if ($company != 4) {
                    echo 404;
                    exit;
                }
                try {
                    include 'parse/zong_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'ufone_cdr':
                if ($company != 3) {
                    echo 404;
                    exit;
                }
                try {
                    include 'parse/ufone_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'telnor_cdr':
                if ($company != 6) {
                    echo 404;
                    exit;
                }
                try {
                    include 'parse/telnor_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'jazz_cdr':
                if ($company != 1) {
                    echo 404;
                    exit;
                }
                try {
                    include 'parse/jazz_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
        }

        if (!empty(Auth::instance()->get_user())) {
            $login_user = Auth::instance()->get_user();
            $login_id = $login_user->id;
        } else {
            $login_id = 9999;
        }
        $uid = $login_id;
        Helpers_Profile::user_activity_log($uid, 25, NULL, NULL, $person_id);
        if (!empty($userrequestid)) {
            Model_ErrorLog::log(
                'upload_data_mapping_full',
                'Full CDR file processing completed successfully (Zong path) - setting status to completed',
                array(
                    'request_id' => $userrequestid,
                    'processing_index' => 5,
                    'file_id' => $file_id,
                    'file_path' => $path,
                    'imei_field' => $imei_field,
                    'person_id' => $person_id
                ),
                null,
                'not_found',
                'file_upload'
            );
            $reference_number = Model_Email::email_status($userrequestid, 2, 5);
        }
        echo '1';
        exit;
    }
}

//public static function data_mapping($inputfilename, $company_name) {
public static function data_mapping($path, $company, $phone_number = NULL, $userrequestid = NULL, $file_id = NULL) {
    ini_set('mysql.connect_timeout', 1000);
    ini_set('default_socket_timeout', 1000);
    date_default_timezone_set('GMT');

    /* Global Indexing */
    $call_type = '';
    $party_a = '';
    $party_b = '';
    $date_time = '';
    $duration = '';
    $imei = '';
    $imsi = '';
    $site = '';

    /* Global Variable for Checking */
    $person_id = '';
    $imei_exist_file = '';
    $imei_exist_db = '';
    $imei_number = '';
    $device_id = "";
    $party_a = "";
    $mnc = $company;
    $date_right = '';
    $date_right_last = '';
    $sms_table_record = array();
    $call_table_record = array();

    $flag = '';
    $telenor_cdr_2 = ['MSISDN', 'CALL_ORIG_NUM', 'CALL_DIALED_NUM', 'IMSI', 'IMEI', 'CALL_START_DT_TM', 'CALL_END_DT_TM', 'INBOUND_OUTBOUND_IND', 'Call_Network_Volume', 'Cell_Lac_Id', 'Cell_Site_Id', 'ORIG_OPER_NAME', 'TERM_OPER_NAME', 'CALL_TYPE', 'Location'];
    $telenor_cdr = ['MSISDN','call_org_num','CALL_DIALED_NUM','IMSI','IMEI','CALL_START_DT_TM','CALL_END_DT_TM','INBOUND_OUTBOUND_IND','Call_Network_Volume','Lac_Id','Site_Id','Cell_SITE_ID','lat','longitude','CALL_TYPE','location'];
    $jazz_cdr_2 = ['Sr #', 'Call Type', 'A-Party', 'B-Party', 'Date & Time', 'Duration', 'Cell ID', 'IMEI', 'IMSI', 'Site'];
    $jazz_cdr = ['Call Type', 'A-Party', 'B-Party', 'Date And Time', 'Duration', 'Cell ID', 'IMSI', 'IMEI', 'SiteLocation', 'Longitude and Latitude', 'Node ID','IP', 'Port'];
    $jazz_cdr_3 = ['CallType', 'Aparty', 'BParty', 'Datetime', 'Duration', 'cellid', 'Imei', 'Imsi', 'SiteLocation', 'Longitude and Latitude', 'Node ID', 'IP', 'Port'];
    $warid_cdr = ['Sr #', 'Call Type', 'A-Party', 'B-Party', 'Date & Time', 'Duration', 'Cell ID', 'IMEI', 'IMSI', 'Site'];
    $ufone_cdr = ['IMEI', 'IMSI', 'A Number', 'B Number', 'Start Time', 'End Time', 'Service Provider', 'Type', 'Direction', 'Location', 'Cell Id', 'Cell Sector', 'Latitude', 'Longitude','Duration'];
    $zong_cdr = ['CALL_TYPE', 'MSISDN', 'STRT_TM', 'BNUMBER', 'MINS', 'SECS', 'LAC_ID', 'CELL_ID', 'IMEI', 'SITE_ADDRESS', 'LNG', 'LAT'];
    $warid_sub = ['subno', 'NAME', 'imei_number', 'address', 'nic', 'connection', 'upddate', 'histfound'];
    /* Global Variable end */

    $inputfilename = $path;
    if ($company != 4) {
        // Read your Excel workbook using PhpSpreadsheet
        try {
            $inputfiletype = IOFactory::identify($inputfilename);
            $objReader = IOFactory::createReader($inputfiletype);

            // Cache settings for better performance
            // Read only data (without formatting) for memory and time performance
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($inputfilename);
        } catch (Exception $e) {
            if (!empty($userrequestid)) {
                Model_ErrorLog::log(
                    'upload_data_mapping',
                    'Failed to load Excel file for data mapping: ' . $e->getMessage(),
                    array(
                        'request_id' => $userrequestid,
                        'processing_index' => 3,
                        'file_id' => $file_id,
                        'file_path' => $path,
                        'company' => $company,
                        'phone_number' => $phone_number,
                        'error_details' => $e->getMessage()
                    ),
                    $e->getTraceAsString(),
                    'parsing_error',
                    'file_upload'
                );
                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
            }
            if (!empty($file_id))
                $error_number = Model_Email::file_status($file_id, 0, 2);
            die('Error loading file "' . pathinfo($inputfilename, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }

        ini_set('memory_limit', '99999990024M');
        $filePath = $inputfilename;

        if ($filePath) {
            $objPHPExcel = IOFactory::load($filePath);

            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                $worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
                $data = array();

                for ($row = 1; $row <= $highestRow; ++$row) {
                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $cell = $worksheet->getCellByColumnAndRow($col + 1, $row);
                        $val = $cell->getValue();

                        // Note the condition: only convert dates for rows > 1
                        if (SpreadsheetDate::isDateTime($cell) && ($row != 1)) {
                            $val = SpreadsheetDate::excelToTimestamp($val);
                        }

                        if (isset($val) && $val)
                            $data[$row][$col] = $val;
                    }
                }

                /* Parsing Start */
                $row = 1;
                if (empty($data[$row])) {
                    if ($objPHPExcel->getSheetCount() > 1) {
                        continue;
                    } else {
                        if (!empty($file_id))
                            $error_number = Model_Email::file_status($file_id, 1, 3); // 1 company  format not match
                    }
                }
                $continue = '';

                include 'parse/cellmatch.inc';

                if ($continue == 'continue')
                    continue;

                if (empty($flag)) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 1, 3); // 1 company  format not match

                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping',
                            'File format not recognized - company format does not match',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'phone_number' => $phone_number,
                                'flag_status' => 'empty'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }

                switch ($flag) {
                    case 'warid_cdr':
                        if ($company != 7) {
                            if (!empty($file_id))
                                $error_number = Model_Email::file_status($file_id, 6, 3); // 1 company  format not match
                            if (!empty($userrequestid)) {
                                Model_ErrorLog::log(
                                    'upload_data_mapping',
                                    'Company mismatch: Warid CDR format detected but company is not Warid (expected 7, got ' . $company . ')',
                                    array(
                                        'request_id' => $userrequestid,
                                        'processing_index' => 3,
                                        'file_id' => $file_id,
                                        'file_path' => $path,
                                        'company' => $company,
                                        'phone_number' => $phone_number,
                                        'expected_company' => 7,
                                        'detected_format' => 'warid_cdr'
                                    ),
                                    null,
                                    'validation_error',
                                    'file_upload'
                                );
                                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                            }
                            exit;
                        }
                        try {
                            //include 'parse/warid_cdr.inc';
                            include 'parse/jazz_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e;
                        }
                        break;
                    case 'zong_cdr':
                        if ($company != 4) {
                            if (!empty($file_id))
                                $error_number = Model_Email::file_status($file_id, 6, 3); // 1 company  format not match
                            if (!empty($userrequestid)) {
                                Model_ErrorLog::log(
                                    'upload_data_mapping',
                                    'Company mismatch: Zong CDR format detected but company is not Zong (expected 4, got ' . $company . ')',
                                    array(
                                        'request_id' => $userrequestid,
                                        'processing_index' => 3,
                                        'file_id' => $file_id,
                                        'file_path' => $path,
                                        'company' => $company,
                                        'phone_number' => $phone_number,
                                        'expected_company' => 4,
                                        'detected_format' => 'zong_cdr'
                                    ),
                                    null,
                                    'validation_error',
                                    'file_upload'
                                );
                                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                            }
                            exit;
                        }
                        try {
                            include 'parse/zong_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'ufone_cdr':
                        if ($company != 3) {
                            if (!empty($file_id))
                                $error_number = Model_Email::file_status($file_id, 6, 3); // 1 company  format not match
                            if (!empty($userrequestid)) {
                                Model_ErrorLog::log(
                                    'upload_data_mapping',
                                    'Company mismatch: Ufone CDR format detected but company is not Ufone (expected 3, got ' . $company . ')',
                                    array(
                                        'request_id' => $userrequestid,
                                        'processing_index' => 3,
                                        'file_id' => $file_id,
                                        'file_path' => $path,
                                        'company' => $company,
                                        'phone_number' => $phone_number,
                                        'expected_company' => 3,
                                        'detected_format' => 'ufone_cdr'
                                    ),
                                    null,
                                    'validation_error',
                                    'file_upload'
                                );
                                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                            }
                            exit;
                        }
                        try {
                            include 'parse/ufone_cdr.inc';
                        } catch (Exception $e) {
                            if (!empty($file_id))
                                $error_number = Model_Email::file_status($file_id, 1, 3);
                            echo '<pre>';
                            print_r($e);
                            echo $e->getMessage();
                            exit;
                        }
                        break;
                    case 'telnor_cdr':
                        if ($company != 6) {
                            if (!empty($file_id))
                                $error_number = Model_Email::file_status($file_id, 6, 3); // 1 company  format not match
                            if (!empty($userrequestid)) {
                                Model_ErrorLog::log(
                                    'upload_data_mapping',
                                    'Company mismatch: Telenor CDR format detected but company is not Telenor (expected 6, got ' . $company . ')',
                                    array(
                                        'request_id' => $userrequestid,
                                        'processing_index' => 3,
                                        'file_id' => $file_id,
                                        'file_path' => $path,
                                        'company' => $company,
                                        'phone_number' => $phone_number,
                                        'expected_company' => 6,
                                        'detected_format' => 'telnor_cdr'
                                    ),
                                    null,
                                    'validation_error',
                                    'file_upload'
                                );
                                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                            }
                            exit;
                        }
                        try {
                            include 'parse/telnor_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                    case 'jazz_cdr':
                        if ($company != 1 && $company != 7) {
                            if (!empty($file_id))
                                $error_number = Model_Email::file_status($file_id, 6, 3); // 1 company  format not match
                            if (!empty($userrequestid)) {
                                Model_ErrorLog::log(
                                    'upload_data_mapping',
                                    'Company mismatch: Jazz CDR format detected but company is not Jazz or Warid (expected 1 or 7, got ' . $company . ')',
                                    array(
                                        'request_id' => $userrequestid,
                                        'processing_index' => 3,
                                        'file_id' => $file_id,
                                        'file_path' => $path,
                                        'company' => $company,
                                        'phone_number' => $phone_number,
                                        'expected_company' => '1 or 7',
                                        'detected_format' => 'jazz_cdr'
                                    ),
                                    null,
                                    'validation_error',
                                    'file_upload'
                                );
                                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                            }
                            exit;
                        }
                        try {
                            include 'parse/jazz_cdr.inc';
                        } catch (Database_Exception $e) {
                            echo $e->getMessage();
                        }
                        break;
                }

                /* Parsing End */
                if (!empty(Auth::instance()->get_user())) {
                    $login_user = Auth::instance()->get_user();
                    $login_id = $login_user->id;
                } else {
                    $login_id = 9999;
                }
                $uid = $login_id;
                Helpers_Profile::user_activity_log($uid, 25, NULL, NULL, $person_id);

                if (!empty($userrequestid)) {
                    Model_ErrorLog::log(
                        'upload_data_mapping',
                        'File processing completed successfully - setting status to completed',
                        array(
                            'request_id' => $userrequestid,
                            'processing_index' => 5,
                            'file_id' => $file_id,
                            'file_path' => $path,
                            'company' => $company,
                            'phone_number' => $phone_number,
                            'person_id' => $person_id
                        ),
                        null,
                        'not_found',
                        'file_upload'
                    );
                    $reference_number = Model_Email::email_status($userrequestid, 2, 5);
                }
                if (!empty($file_id)){
                    $error_number = Model_Email::file_status($file_id, 0, 2);
                }
            }
        }
    } else {
        $reader = ReaderFactory::create(Type::XLSX);
        $filePath = $inputfilename;
        $reader->open($filePath);
        $help = $reader;
        $data = array();
        $total_Sheet = 0;
        foreach ($help->getSheetIterator() as $sheetIndex => $sheet) {
            $total_Sheet +=1;
        }
        $continue = '';
        foreach ($reader->getSheetIterator() as $sheet) {
            $data = array();
            foreach ($sheet->getRowIterator() as $row) {
                $data[] = $row;
            }

            $row = 0;
            if (empty($data[$row])) {
                if ($total_Sheet > 1) {
                    continue;
                } else {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 1, 3);
                }
            }

            include 'parse/cellmatch.inc';

            if ($continue != 'continue') {
                if (empty($flag)) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 1, 3);

                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping',
                            'File format not recognized (Zong path) - company format does not match',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'phone_number' => $phone_number,
                                'flag_status' => 'empty'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                break;
            }
        }
        $reader->close();

        switch ($flag) {
            case 'warid_cdr':
                if ($company != 7) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 6, 3);
                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping',
                            'Company mismatch (Zong path): Warid CDR format detected but company is not Warid (expected 7, got ' . $company . ')',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'phone_number' => $phone_number,
                                'expected_company' => 7,
                                'detected_format' => 'warid_cdr'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                try {
                    include 'parse/warid_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'zong_cdr':
                if ($company != 4) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 6, 3);
                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping',
                            'Company mismatch (Zong path): Zong CDR format detected but company is not Zong (expected 4, got ' . $company . ')',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'phone_number' => $phone_number,
                                'expected_company' => 4,
                                'detected_format' => 'zong_cdr'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                try {
                    include 'parse/zong_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'ufone_cdr':
                if ($company != 3) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 6, 3);
                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping',
                            'Company mismatch (Zong path): Ufone CDR format detected but company is not Ufone (expected 3, got ' . $company . ')',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'phone_number' => $phone_number,
                                'expected_company' => 3,
                                'detected_format' => 'ufone_cdr'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                try {
                    include 'parse/ufone_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'telnor_cdr':
                if ($company != 6) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 6, 3);
                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping',
                            'Company mismatch (Zong path): Telenor CDR format detected but company is not Telenor (expected 6, got ' . $company . ')',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'phone_number' => $phone_number,
                                'expected_company' => 6,
                                'detected_format' => 'telnor_cdr'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                try {
                    include 'parse/telnor_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
            case 'jazz_cdr':
                if ($company != 1) {
                    if (!empty($file_id))
                        $error_number = Model_Email::file_status($file_id, 6, 3);
                    if (!empty($userrequestid)) {
                        Model_ErrorLog::log(
                            'upload_data_mapping',
                            'Company mismatch (Zong path): Jazz CDR format detected but company is not Jazz (expected 1, got ' . $company . ')',
                            array(
                                'request_id' => $userrequestid,
                                'processing_index' => 3,
                                'file_id' => $file_id,
                                'file_path' => $path,
                                'company' => $company,
                                'phone_number' => $phone_number,
                                'expected_company' => 1,
                                'detected_format' => 'jazz_cdr'
                            ),
                            null,
                            'validation_error',
                            'file_upload'
                        );
                        $reference_number = Model_Email::email_status($userrequestid, 2, 3);
                    }
                    exit;
                }
                try {
                    include 'parse/jazz_cdr.inc';
                } catch (Database_Exception $e) {
                    echo $e->getMessage();
                }
                break;
        }

        if (!empty(Auth::instance()->get_user())) {
            $login_user = Auth::instance()->get_user();
            $login_id = $login_user->id;
        } else {
            $login_id = 9999;
        }
        $uid = $login_id;
        Helpers_Profile::user_activity_log($uid, 25, NULL, NULL, $person_id);

        if(empty($flag)) {
            if (!empty($file_id))
                $error_number = Model_Email::file_status($file_id, 1, 3);
            if (!empty($userrequestid)) {
                Model_ErrorLog::log(
                    'upload_data_mapping',
                    'Empty flag detected after processing (Zong path) - format validation failed',
                    array(
                        'request_id' => $userrequestid,
                        'processing_index' => 3,
                        'file_id' => $file_id,
                        'file_path' => $path,
                        'company' => $company,
                        'phone_number' => $phone_number,
                        'flag_status' => 'empty'
                    ),
                    null,
                    'validation_error',
                    'file_upload'
                );
                $reference_number = Model_Email::email_status($userrequestid, 2, 3);
            }
            exit;
        } else {
            if (!empty($userrequestid)) {
                Model_ErrorLog::log(
                    'upload_data_mapping',
                    'File processing completed successfully (Zong path) - setting status to completed',
                    array(
                        'request_id' => $userrequestid,
                        'processing_index' => 5,
                        'file_id' => $file_id,
                        'file_path' => $path,
                        'company' => $company,
                        'phone_number' => $phone_number,
                        'person_id' => $person_id
                    ),
                    null,
                    'not_found',
                    'file_upload'
                );
                $reference_number = Model_Email::email_status($userrequestid, 2, 5);
            }
            if (!empty($file_id))
                $error_number = Model_Email::file_status($file_id, 0, 2);
        }
        exit;
    }
}

// ... (rest of the file remains the same) ...

    //this helper will provide folder range for data uplaod please dont change 5000 value, if required make new helper
    public static function get_folder_range($pid) {

        //default range is 5000 please dont change this value otherwise this will affect uploading
        $folder_size_limit = 5000;
        $end_limit = $folder_size_limit;
        $start = 1;
        while ($pid > 0) {
            if ($pid <= $end_limit) {
                $folders_range = $start . "-" . $end_limit;
                break;
            }
            $start = $end_limit + 1;
            $end_limit = $end_limit + $folder_size_limit;
        }
        return $folders_range;
    }

    //get file id against request type 
    public static function get_fileid_aginst_requestid($rid) {
        $DB = Database::instance();
        $sql = 'select id from files where request_id= '.$rid;
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return !empty($results)?$results->id:'';
    }

    //this helper will provide server details to upload person data
    public static function server_details_for_person_data($pid) {
        $server_details = array();
        //this is default server to save data of all persons in future with person_id range conditon another server will be allocated
        if ($pid > 0) {
             $DB = Database::instance();
        $sql = 'SELECT * 
                FROM data_server_details AS t1
                WHERE t1.data_from_id<= '.$pid.' and t1.data_to_id>= '.$pid.' and t1.upload_data_type="person_data"';
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $server_details['server_name'] = $results->server_name;
            $server_details['person_save_data_path'] = $results->save_data_path;
            $server_details['person_download_data_path'] = $results->download_data_path;
        }
        return $server_details;
    }

    //this helper will provide server details to upload cdr data
    public static function server_details_for_request_data($id) {
         $server_details = array();
        //this is default server to save data of all persons in future with person_id range conditon another server will be allocated
        if ($id > 0) {
             $DB = Database::instance();
        $sql = 'SELECT * 
                FROM data_server_details AS t1
                WHERE t1.data_from_id<= '.$id.' and t1.data_to_id>= '.$id.' and t1.upload_data_type="request_data"';
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $server_details['server_name'] = $results->server_name;
            $server_details['request_save_data_path'] = $results->save_data_path;
            $server_details['request_download_data_path'] = $results->download_data_path;
        }
        return $server_details;
    }
    //this helper will provide server details to upload finger print data
    public static function server_details_for_finger_print_data($id) {
        //$id=pid
         $server_details = array();
        //this is default server to save data of all persons in future with person_id range conditon another server will be allocated
        if ($id > 0) {
             $DB = Database::instance();
        $sql = 'SELECT * 
                FROM data_server_details AS t1
                WHERE t1.data_from_id<= '.$id.' and t1.data_to_id>= '.$id.' and t1.upload_data_type="finger_print_data"';
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $server_details['server_name'] = $results->server_name;
            $server_details['save_data_path'] = $results->save_data_path;
            $server_details['download_data_path'] = $results->download_data_path;
        }

        return $server_details;
    }

    //this helper will create person data upload direcory and return path
    public static function make_and_get_person_data_directory($pid) {

        //get folder range to uplaod data
        $folder_range = Helpers_Upload::get_folder_range($pid);
        //get server details to upload person data
        $serverdata = Helpers_Upload::server_details_for_person_data($pid);
        //check && make sub folder to uplaod person data
        $person_subfolder_path = $serverdata['person_save_data_path'] . $folder_range;


        if (!is_dir($person_subfolder_path)) {
            mkdir("{$person_subfolder_path}", 0777);
            copy($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dist/uploads/htaccess/.htaccess', $person_subfolder_path.'/.htaccess');
            copy($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dist/uploads/htaccess/index.php', $person_subfolder_path.'/index.php');
        }

        //alias for download only
        $person_download_data_path = $serverdata['person_download_data_path'] . $folder_range . '/' . $pid . '/';

        //person data folder path
        $person_save_data_path = $person_subfolder_path . '/' . $pid;

       // echo json_encode($person_subfolder_path); exit;

        //check && make folder for person data
        if (!is_dir($person_save_data_path)) {
            mkdir("{$person_save_data_path}", 0777);
            copy($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dist/uploads/htaccess/index.php', $person_save_data_path.'/index.php');
            copy($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'dist/uploads/htaccess/.htaccess', $person_save_data_path.'/.htaccess');

        }
        //checking and updating record in database table
        $chk = !empty($pid) ? Helpers_Person::check_person_assets_url_exist($pid) : '';
        if (empty($chk)) {
            $query = 'insert into person_assets_url '
                    . '(person_id, server_name, person_save_data_path,person_download_data_path) '
                    . 'VALUES (' . $pid . ', "' . $serverdata['server_name'] . '","' . $person_save_data_path ."/". '","' . $person_download_data_path . '")';
            $sql = DB::query(Database::INSERT, $query)->execute();
        } else {
            $query = 'update person_assets_url '
                    . 'SET server_name= "'. $serverdata['server_name'] .'", person_save_data_path="' . $person_save_data_path ."/". '",person_download_data_path="' . $person_download_data_path . '" Where person_id=' . $pid;
            $sql = DB::query(Database::UPDATE, $query)->execute();
        }
        return $person_save_data_path . '/';
    }
 //getting file id with user request id
    public static function get_file_info_with_request_id($requestid=NULL) {
        $DB = Database::instance();
        $sql = "SELECT * FROM 
                                files as t1 
                                where t1.request_id = $requestid limit 1";
            $members = $DB->query(Database::SELECT, $sql, FALSE)->current();
            return $members;
    }
    public static function ensure_directory_exists($path) {
        if (empty($path)) {
            return false;
        }

        if (is_dir($path)) {
            return true;
        }

        if (!mkdir($path, 0755, true)) {   // 0755 usually better for web servers
            $error = error_get_last();
            error_log(sprintf(
                "Failed to create directory '%s': %s",
                $path,
                $error['message'] ?? 'unknown error'
            ));
            return false;
        }

        return true;
    }
    //this helper will create cdr or request file data upload direcory and return path
    public static function get_request_data_path($id = null, $type = null)
    {
        $folder_range = $id ? self::get_folder_range($id) : '';

        $serverdata = $id ? self::server_details_for_request_data($id) : null;

        if (!$serverdata) {
            error_log("No serverdata found for id: " . var_export($id, true));
            return '';
        }

        // Build path parts separately – avoids slash confusion
        $base = $serverdata['request_save_data_path'];

        // Normalize to forward slashes (or use realpath later)
        $base = str_replace('\\', '/', $base);

        // Then trim and join with DS
        $base = rtrim($base, '/');
        $request_subfolder_path = $base . DS . trim($folder_range, '/\\') . DS;

        // Ensure the directory exists (recursive)
        if (!self::ensure_directory_exists($request_subfolder_path)) {
            error_log("Directory not created at: " . $request_subfolder_path);
            return '';
        }

        if ($type === 'save') {
            return $request_subfolder_path;   // already ends with DS
        }

        // ──────────────────────────────────────────────
        // Download / alias path – be careful here too
        // ──────────────────────────────────────────────
        $download_base = rtrim($serverdata['request_download_data_path'], '/\\');

        // For download we usually want URL-style (forward slashes)
        // so here it's okay to use '/' even on Windows
        return $serverdata['server_name']
            . $download_base . '/'
            . $folder_range . '/';
    }
    //to upload person documents
    public static function upload_person_documents($files, $type = NULL, $pid = NULL, $reportname = NULL) {


        $file = Validation::factory($files);
        $file->rules(
                'personfile', array(
            array(array('Upload', 'valid')),
            array(array('Upload', 'not_empty')),
           // array('Upload::type', array(':value', array('pdf', 'doc', 'docx', 'inf', 'jpg', 'jpeg', 'png', 'gif','inp')))
                )
        );

        // to get person save data path
        $person_save_data_path = !empty($pid) ? Helpers_Person::get_person_save_data_path($pid) : '';


        if ($file->check() && !empty($person_save_data_path)) {

            $date = date("YmdHis", time());
            $filename = explode('.', $_FILES['personfile']['name']);
            $filextension = sizeof($filename) - 1;
            // $filename_database = Upload::save($_FILES['personfile'], URL::title($filename[0] . $date, '-', true) . '.' . strtolower($filename[$filextension]), DOCROOT . 'dist/uploads/person/profile_assets/', 0777);
            if ($type == "person_picture" || $type == "person_verysis" || $type == "person_report" || $type == "person_income_source" || $type == "person_assets" || $type == "person_cr" || $type == "person_social_link") {
                if (!empty($reportname)) {
                    $file_name = $reportname;
                } else {
                    $file_name = $type;
                }

                $filename_database = Upload::save($_FILES['personfile'], URL::title($pid . $file_name . $date, '-', true) . '.' . strtolower($filename[$filextension]), $person_save_data_path, 0777);


                }

            $new_file_name = explode(DIRECTORY_SEPARATOR, $filename_database);
            //print_r($new_file_name[sizeof($new_file_name) - 1]); exit;
            //return $filename[0] . $date . '.' . $filename[$filextension];
            return $new_file_name[sizeof($new_file_name) - 1];
        }


//        
//        $file = Validation::factory($files);         
//        $file->rules(
//                'personfile', array(
//            array(array('Upload', 'valid')),
//           array(array('Upload', 'not_empty')),
//            array('Upload::type', array(':value', array('pdf', 'doc','docx', 'inf', 'jpg', 'jpeg', 'png', 'gif')))
//                )
//        );        
//        if ($file->check()) {
//            if ($type == "person_report") {
//                //genrate a random file name that is hexa decimal and make it same file format
//                $date = date("YmdHis", time());
//                $filename = explode('.', $_FILES['personfile']['name']);
//                $filename = $date . ".".$filename[0];
//                $directory = 'dist/uploads/person/profile_reports/';
//            }
//        }
//        if ($file = Upload::save($_FILES, NULL, $directory)) {
//            $img = file::factory($file);
//            $img->save($directory . $filename);
//            unlink($file);
//            return trim($filename);
//        }

        return FALSE;
    }

//    // upload person social links file
//    public static function upload_person_social_link($files, $type = NULL) {
//        $file = Validation::factory($files);
//        $file->rules(
//                'personfile', array(
//            array(array('Upload', 'valid')),
//            array(array('Upload', 'not_empty')),
//            array('Upload::type', array(':value', array('pdf', 'doc', 'docx', 'inf', 'jpg', 'jpeg', 'png', 'gif','xls')))
//                )
//        );
//        if ($file->check()) {
//            $date = date("YmdHis", time());
//            $filename = explode('.', $_FILES['personfile']['name']);
//            $filextension = sizeof($filename) - 1;
//            
//                $filename_database = Upload::save($_FILES['personfile'], URL::title($filename[0] . $date, '-', true) . '.' . strtolower($filename[$filextension]), DOCROOT . 'dist/uploads/person/social_links/', 0777);
//           
//            $new_file_name = explode('\\', $filename_database);
//            //print_r($new_file_name[sizeof($new_file_name) - 1]); exit;
//            //return $filename[0] . $date . '.' . $filename[$filextension];
//            return $new_file_name[sizeof($new_file_name) - 1];
//        }
//        return FALSE;
//    }
    /* Location History */
    public static function person_location_history($person_id, $phone_number, $moved_in_at, $move_out_at, $location_street, $longitude, $latitude, $mnc, $cel_lac, $cell_site) {
        $location_history = DB::insert('person_location_history', array('person_id', 'phone_number', 'moved_in_at', 'moved_out_at', 'address', 'longitude', 'latitude', 'mnc', 'lac_id', 'cell_id'));
        $location_history->values(array($person_id, $phone_number, "{$moved_in_at}", "{$move_out_at}", "{$location_street}", $longitude, $latitude, $mnc, $cel_lac, $cell_site));
        $location_history->execute();
    }

    /* Person Monthly Summary */

    public static function monthly_summary($number, $person_id) {
        $result = '';
        $query = "SELECT DATE_FORMAT(call_end_at,'%Y-%M') as month, phone_number, 
                    sum(case when is_outgoing = 1 then 1 else 0 END) as call_outgoing, 
                    sum(case when is_outgoing = 0 then 1 else 0 END) as call_incoming
                    FROM `person_call_log`
                    WHERE call_end_at >= now()-interval 4 month 
                    and phone_number = {$number}
                    and person_id = {$person_id}
                    GROUP BY MONTH(call_end_at) 
                    order by call_end_at DESC";
        $sql = DB::query(Database::SELECT, $query);
        $call_count = $sql->execute()->as_array();
        $query = "SELECT DATE_FORMAT(sms_at,'%Y-%M') as month, phone_number,  
                sum(case when is_outgoing = 1 then 1 else 0 END) as sms_outgoing, 
                sum(case when is_outgoing = 0 then 1 else 0 END) as sms_incoming
                FROM `person_sms_log`
                WHERE sms_at >= now()-interval 4 month 
                and phone_number = {$number}
                and person_id = {$person_id}
                GROUP BY MONTH(sms_at) 
                order by sms_at DESC";
        $sql = DB::query(Database::SELECT, $query);
        $sms_count = $sql->execute()->as_array();

        if (!empty($call_count)) {
            //$query = "DELETE FROM `person_monthly_summary` WHERE year(reported_month) <= ". date('Y', strtotime($call_count[0]['month']))  ." AND month(reported_month) <= ".date('m', strtotime($call_count[0]['month']))." AND year(reported_month) >= ".date('Y', strtotime($call_count[sizeof($call_count)-1]['month']))." AND month(reported_month) >= ".date('m', strtotime($call_count[sizeof($call_count)-1]['month']))." AND `person_id` =" . $person_id;
            //DB::query(Database::DELETE, $query);
            /*
              $query = DB::delete('person_monthly_summary')
              ->where('year(reported_month)', '<=', date('Y', strtotime($call_count[0]['month'])))
              ->and_where('month(reported_month)', '<=', date('m', strtotime($call_count[0]['month'])))
              ->and_where('year(reported_month)', '>=', date('Y', strtotime($call_count[sizeof($call_count)-1]['month'])))
              ->and_where('month(reported_month)', '>=', date('m', strtotime($call_count[sizeof($call_count)-1]['month'])))
              ->and_where('person_id', '=', $person_id)
              ->execute(); */


            foreach ($call_count as $record) {

                $record['month'] = date("Y-m-d", strtotime($record['month']));

                $query = DB::update('person_monthly_summary')->set(array('calls_made_count' => $record['call_outgoing'], 'calls_received_count' => $record['call_incoming']))
                        ->where('person_id', '=', $person_id)
                        ->where('reported_month', '=', $record['month'])
                        ->execute();
                if ($query == 0) {
                    $person_summary = DB::insert('person_monthly_summary', array('person_id', 'reported_month', 'calls_made_count', 'calls_received_count'));
                    $person_summary->values(array($person_id, $record['month'], $record['call_outgoing'], $record['call_incoming']));
                    $result = $person_summary->execute();
                }
            }

            if (!empty($sms_count)) {
                foreach ($sms_count as $record) {
                    $record['month'] = date("Y-m-d", strtotime($record['month']));

                    $query = DB::update('person_monthly_summary')->set(array('sms_sent_count' => $record['sms_outgoing'], 'sms_received_count' => $record['sms_incoming']))
                            ->where('person_id', '=', $person_id)
                            ->where('reported_month', '=', $record['month'])
                            ->execute();
                    if ($query == 0) {
                        $person_summary = DB::insert('person_monthly_summary', array('person_id', 'reported_month', 'sms_sent_count', 'sms_received_count'));
                        $person_summary->values(array($person_id, $record['month'], $record['sms_outgoing'], $record['sms_incoming']));
                        $result = $person_summary->execute();
                    }
                }
            }
        }

        return $result;
    }

    /* Person Summary */

    public static function person_summary($number, $person_id) {
        $query = "SELECT phone_number, other_person_phone_number, 
                    sum(case when is_outgoing = 1 then 1 else 0 END) as call_outgoing, 
                    sum(case when is_outgoing = 0 then 1 else 0 END) as call_incoming
                    FROM `person_call_log`
                    WHERE phone_number = {$number} 
                    and person_id = {$person_id} 
                    group by other_person_phone_number 
                    order by other_person_phone_number DESC";
        $sql = DB::query(Database::SELECT, $query);
        $call_count = $sql->execute()->as_array();
        $query = "SELECT phone_number, other_person_phone_number, 
                    sum(case when is_outgoing = 1 then 1 else 0 END) as sms_outgoing, 
                    sum(case when is_outgoing = 0 then 1 else 0 END) as sms_incoming
                    FROM `person_sms_log`
                    WHERE phone_number = {$number} 
                    and person_id = {$person_id} 
                    group by other_person_phone_number 
                    order by other_person_phone_number DESC";
        $sql = DB::query(Database::SELECT, $query);
        $sms_count = $sql->execute()->as_array();


        $query = DB::delete('person_summary')
                ->where('phone_number', '=', $number)
                ->and_where('person_id', '=', $person_id)
                ->execute();
        $date = date('Y-m-d H:i:s');

        if (sizeof($call_count) >= 1) {
            $person_summary = DB::insert('person_summary', array('person_id', 'phone_number', 'other_person_phone_number', 'calls_made_count', 'calls_received_count', 'sms_sent_count', 'sms_received_count', 'last_update'));
            foreach ($call_count as $record) {
                $person_summary->values(array($person_id, $number, $record['other_person_phone_number'], $record['call_outgoing'], $record['call_incoming'], 0, 0, $date));
            }
            $result = $person_summary->execute();
        }
        if (sizeof($sms_count) >= 1) {
            foreach ($sms_count as $record) {
                $query = DB::update('person_summary')->set(array('sms_sent_count' => $record['sms_outgoing'], 'sms_received_count' => $record['sms_incoming']))
                        ->where('person_id', '=', $person_id)
                        ->and_where('phone_number', '=', $number)
                        ->and_where('other_person_phone_number', '=', $record['other_person_phone_number'])
                        ->execute();
                if ($query == 0) {
                    $person_summary_1 = DB::insert('person_summary', array('person_id', 'phone_number', 'other_person_phone_number', 'calls_made_count', 'calls_received_count', 'sms_sent_count', 'sms_received_count', 'last_update'));
                    $person_summary_1->values(array($person_id, $number, $record['other_person_phone_number'], 0, 0, $record['sms_outgoing'], $record['sms_incoming'], $date));
                    $person_summary_1->execute();
                }
            }
        }
        return 1;
    }

    //    get last update imei cdr data previous table
//    public static function get_last_update_imei_cdr_data($imei) {
//       //query to get upload time
//        $DB = Database::instance();
//        $sql = "SELECT *
//                FROM  temp_cdr_upload_status AS t1
//                WHERE t1.imei_number= $imei ";
//        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();  
//        return $results;
//    }
    //    get last update imei cdr data
    public static function get_last_update_imei_cdr_data($imei) {
        //query to get upload time
        $DB = Database::instance();
        $sql = "SELECT *
                FROM  files AS t1
                WHERE t1.imei= $imei ORDER BY t1.id DESC LIMIT 1 ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    //    this helper will check record exist in file table with file name
    public static function check_record_exist_with_file_name($file_name) {
        //query to count sims against imei no
        $DB = Database::instance();
        $sql = 'SELECT COUNT(*) as count
               FROM files t1 
               WHERE t1.file="'.$file_name.'"';
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $recordcount = isset($results->count) && !empty($results->count) ? $results->count : 0;


        return $recordcount;
    }
    //    Check subscriber info is updated
    public static function check_sims_subscribers_updated($imei) {
        //query to count sims against imei no
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.imei_number) as count
               FROM person_phone_device t1 
               INNER JOIN person_device_numbers t2 ON t1.id = t2.device_id 
               INNER JOIN person_phone_number as t3 ON t2.phone_number = t3.phone_number 
               WHERE t1.imei_number=$imei ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $simcount = isset($results->count) && !empty($results->count) ? $results->count : 0;
        //query to count sims against imei no that have ownership name
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.imei_number) as count
               FROM person_phone_device t1 
               INNER JOIN person_device_numbers t2 ON t1.id = t2.device_id 
               INNER JOIN person_phone_number as t3 ON t2.phone_number = t3.phone_number 
               WHERE t1.imei_number=$imei AND t3.sim_owner!=0 AND t3.sim_owner!='' AND t3.sim_owner!=-1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $simcountwithowner = isset($results->count) && !empty($results->count) ? $results->count : 0;

        if ($simcountwithowner == $simcount && $simcountwithowner != 0 && $simcount != 0) {
            $sts = 1;
        } else {
            $sts = 2;
        }

        return $sts;
    }

    public static function warid_cnic_sub($inputfilename) {
        //$reader = ReaderFactory::create(Type::XLSX); // for XLSX files
        /* $reader = ReaderFactory::create(Type::XLS); // for XLSX files
          //$reader = ReaderFactory::create(Type::CSV); // for CSV files
          //$reader = ReaderFactory::create(Type::ODS); // for ODS files
          $filePath = $inputfilename;
          $reader->open($filePath);
          $data = array();
          foreach ($reader->getSheetIterator() as $sheet) {
          foreach ($sheet->getRowIterator() as $row) {
          $data[]=$row;

          // do stuff with the row
          }
          }
          $reader->close();
          return $data; */
        $objPHPExcel = PHPExcel_IOFactory::load($inputfilename);
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $worksheetTitle = $worksheet->getTitle();
            $highestRow = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $nrColumns = ord($highestColumn) - 64;
            $data = array();
            for ($row = 1; $row <= $highestRow; ++$row) {
                $values = array();
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
               echo     $val = $cell->getValue();
                    if (PHPExcel_Shared_Date::isDateTime($cell)) {
                        //$InvDate = date($format, PHPExcel_Shared_Date::ExcelToPHP($val)); 
                        $val = PHPExcel_Shared_Date::ExcelToPHP($cell->getValue());
                        //  echo ' ttt  ' . $val;
                        // $val= date("Y-m-d H:i:s",$val);                            
                    }

                    if (isset($val) && $val)
                        $data[$row][$col] = $val;
                }
            }
            return $data;
        }
    }


    public static function telenor_cnic_sub($filePath, $reference_number = null) {

        if (!file_exists($filePath)) {
            Model_ErrorLog::log(
                'upload_telenor_cnic_sub',
                'File not found for Telenor CNIC subscription data',
                array(
                    'request_id' => $reference_number,
                    'processing_index' => 3,
                    'file_id' => null,
                    'file_path' => $filePath,
                    'error_details' => 'File does not exist'
                ),
                null,
                'not_found',
                'file_upload'
            );
            $reference_number = Model_Email::email_status($reference_number, 2, 3);
            return;
        }
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            $data = [];
            foreach ($sheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }

        } catch (Exception $e) {
            //echo 'Error reading file: ', $e->getMessage();
            Model_ErrorLog::log(
                'upload_telenor_cnic_sub',
                'Error reading Telenor CNIC subscription file: ' . $e->getMessage(),
                array(
                    'request_id' => $reference_number,
                    'processing_index' => 3,
                    'file_id' => null,
                    'file_path' => $filePath,
                    'error_details' => $e->getMessage()
                ),
                $e->getTraceAsString(),
                'parsing_error',
                'file_upload'
            );
            $reference_number = Model_Email::email_status($reference_number, 2, 3);
            return;
        }
        return $data;
               //$date = Date::excelToDateTimeObject($value);
                    //$rowData[] = $date->format('Y-m-d');
}


    /* warid sub parsing */

    public static function warid_sub_pars($inputfilename) {
        $objPHPExcel = PHPExcel_IOFactory::load($inputfilename);
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $worksheetTitle = $worksheet->getTitle();
            $highestRow = $worksheet->getHighestRow(); // e.g. 10
            $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $nrColumns = ord($highestColumn) - 64;
            $data = array();
            for ($row = 1; $row <= $highestRow; ++$row) {
                $values = array();
                for ($col = 0; $col < $highestColumnIndex; ++$col) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    if (PHPExcel_Shared_Date::isDateTime($cell)) {
                        //$InvDate = date($format, PHPExcel_Shared_Date::ExcelToPHP($val)); 
                        $val = PHPExcel_Shared_Date::ExcelToPHP($cell->getValue());
                        //  echo ' ttt  ' . $val;
                        // $val= date("Y-m-d H:i:s",$val);                            
                    }

                    if (isset($val) && $val)
                        $data[$row][$col] = $val;
                }
            }
            return $data;
        }
    }

    /* number */

    public static function number_clean($num) {


        //remove zeros from end of number ie. 140.00000 becomes 140.
        $clean = rtrim($num, '0');
        //remove zeros from front of number ie. 0.33 becomes .33
        $clean = ltrim($clean, '0');
        //remove decimal point if an integer ie. 140. becomes 140
        $clean = rtrim($clean, '.');

        $clean = str_pad($clean, 10, '0', STR_PAD_RIGHT);

        return $clean;
    }

    /* get fileid against request id */
    public static function get_fileid_with_requestid($requestid) {
        //query to get upload time
        $DB = Database::instance();
        $sql = "SELECT id
                FROM files
                WHERE request_id = {$requestid} LIMIT 1 ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return !empty($results)?$results->id:'';
    }

}

?>
