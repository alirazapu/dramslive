<?php

defined('SYSPATH') OR die('No direct script access.');
require_once __DIR__ . '/src/Spout/Autoloader/autoload.php';
require_once DOCROOT . '/application/classes/Controller/excel/vendor/autoload.php';

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Common\Type;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Combine all CDR files of a SIM (user_request + files) into one xlsx.
 *
 * - xlsx / csv are streamed with Spout, xls is read with PhpSpreadsheet,
 *   zip archives are extracted and their xlsx / xls / csv files used.
 * - A "Source File" column tells which CDR file each row came from.
 * - Files with the same header share one header row; a file with a different
 *   header (e.g. other operator format) gets its own header row before its data.
 * - Exact duplicate rows (overlapping request date ranges) are written once.
 *
 * @package    CDR Merge Helper
 * @category   Helpers
 */
class Helpers_Cdrmerge
{
    private static $readable_ext = array('xlsx', 'xls', 'csv');

    /*
     * Merge all CDR files of $sim into $out_path (xlsx).
     * return array('files_used' => int, 'rows' => int, 'skipped' => array of messages)
     */
    public static function merge_sim_cdr_files($sim, $out_path)
    {
        $result = array('files_used' => 0, 'rows' => 0, 'skipped' => array());
        $work_dir = dirname($out_path);

        // oldest request first so the combined sheet reads in time order
        $files = array_reverse(Helpers_Requests::get_requests_by_sim($sim, 1, 'callcdr'));

        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile($out_path);
        $header_style = (new StyleBuilder())->setFontBold()->build();

        $current_header_key = NULL;
        $seen_rows = array();

        foreach ($files as $file) {
            if (empty($file['id']) || empty($file['file'])) {
                continue;
            }
            $label = $file['file'];
            $from = !empty($file['request_start_date']) ? substr($file['request_start_date'], 0, 10) : '';
            $to = !empty($file['request_end_date']) ? substr($file['request_end_date'], 0, 10) : '';
            if ($from !== '' && $to !== '' && $from !== '0000-00-00') {
                $label .= ' (' . $from . ' to ' . $to . ')';
            }

            $path = rtrim(Helpers_Upload::get_request_data_path($file['id'], 'save'), '/\\') . DIRECTORY_SEPARATOR . ltrim($file['file'], '/\\');
            if (!is_file($path)) {
                $result['skipped'][] = $file['file'] . ': file not found on server';
                continue;
            }

            $sources = self::source_paths($path, $work_dir, $result['skipped']);
            $file_had_rows = FALSE;

            foreach ($sources as $source) {
                try {
                    foreach (self::read_sheets($source) as $sheet_rows) {
                        $header = NULL;
                        foreach ($sheet_rows as $row) {
                            $row = self::clean_row($row);
                            if (empty($row)) {
                                continue;
                            }
                            // first non-empty row of a sheet is its header
                            if ($header === NULL) {
                                $header = $row;
                                $header_key = strtolower(implode('|', $header));
                                if ($header_key !== $current_header_key) {
                                    $writer->addRowWithStyle(array_merge(array('Source File'), $header), $header_style);
                                    $current_header_key = $header_key;
                                }
                                continue;
                            }
                            $hash = md5($current_header_key . '|' . implode("\x1f", $row));
                            if (isset($seen_rows[$hash])) {
                                continue;
                            }
                            $seen_rows[$hash] = TRUE;
                            $writer->addRow(array_merge(array($label), $row));
                            $result['rows']++;
                            $file_had_rows = TRUE;
                        }
                    }
                } catch (Exception $ex) {
                    $result['skipped'][] = $file['file'] . ': could not be read (' . $ex->getMessage() . ')';
                }
            }
            if ($file_had_rows) {
                $result['files_used']++;
            }
        }

        $writer->close();
        return $result;
    }

    /* readable spreadsheet paths for one CDR file (zip archives are extracted) */
    private static function source_paths($path, $work_dir, &$skipped)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, self::$readable_ext)) {
            return array($path);
        }
        if ($ext == 'zip') {
            $zip = new ZipArchive();
            if ($zip->open($path) !== TRUE) {
                $skipped[] = basename($path) . ': zip could not be opened';
                return array();
            }
            $extract_dir = $work_dir . DIRECTORY_SEPARATOR . 'zip_' . uniqid();
            mkdir($extract_dir);
            $paths = array();
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                $entry_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($entry_ext, self::$readable_ext)) {
                    continue;
                }
                // flat, safe file name (no paths from inside the archive)
                $target = $extract_dir . DIRECTORY_SEPARATOR . $i . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($name));
                $stream = $zip->getStream($name);
                if ($stream) {
                    file_put_contents($target, $stream);
                    fclose($stream);
                    $paths[] = $target;
                }
            }
            $zip->close();
            if (empty($paths)) {
                $skipped[] = basename($path) . ': no xlsx/xls/csv file inside zip';
            }
            return $paths;
        }
        $skipped[] = basename($path) . ': .' . $ext . ' files are not supported for combining';
        return array();
    }

    /* yields one row iterator per sheet */
    private static function read_sheets($path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext == 'xls') {
            $reader = IOFactory::createReader('Xls');
            $reader->setReadDataOnly(FALSE);
            $spreadsheet = $reader->load($path);
            foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                yield self::xls_sheet_rows($worksheet);
            }
            $spreadsheet->disconnectWorksheets();
            return;
        }

        $reader = ReaderFactory::create($ext == 'csv' ? Type::CSV : Type::XLSX);
        $reader->setShouldFormatDates(TRUE);
        if ($ext == 'csv') {
            $reader->setFieldDelimiter(self::detect_csv_delimiter($path));
        }
        $reader->open($path);
        foreach ($reader->getSheetIterator() as $sheet) {
            yield self::sheet_rows($sheet);
        }
        $reader->close();
    }

    private static function sheet_rows($sheet)
    {
        foreach ($sheet->getRowIterator() as $row) {
            yield $row;
        }
    }

    /* xls rows with raw values (formatted values turn IMEI into 3.58E+14), date cells as DateTime */
    private static function xls_sheet_rows($worksheet)
    {
        foreach ($worksheet->getRowIterator() as $row) {
            $values = array();
            $cells = $row->getCellIterator();
            $cells->setIterateOnlyExistingCells(FALSE);
            foreach ($cells as $cell) {
                $value = $cell->getCalculatedValue();
                if (is_numeric($value) && \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)) {
                    $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                }
                $values[] = $value;
            }
            yield $values;
        }
    }

    /* values to plain strings (keeps long numbers like IMEI / MSISDN intact), trailing empty cells removed */
    private static function clean_row($row)
    {
        $clean = array();
        foreach ($row as $value) {
            if ($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (is_float($value) && floor($value) == $value && abs($value) < 1e16) {
                $value = sprintf('%.0f', $value);
            } elseif (is_bool($value)) {
                $value = $value ? 'TRUE' : 'FALSE';
            }
            $clean[] = trim((string) $value);
        }
        while (!empty($clean) && end($clean) === '') {
            array_pop($clean);
        }
        return $clean;
    }

    private static function detect_csv_delimiter($path)
    {
        $line = '';
        $handle = fopen($path, 'r');
        if ($handle) {
            $line = (string) fgets($handle);
            fclose($handle);
        }
        $best = ',';
        $best_count = 0;
        foreach (array(',', ';', "\t", '|') as $delimiter) {
            $count = substr_count($line, $delimiter);
            if ($count > $best_count) {
                $best = $delimiter;
                $best_count = $count;
            }
        }
        return $best;
    }

    /* remove a temp folder created for a merge */
    public static function remove_dir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            $item_path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($item_path) ? self::remove_dir($item_path) : @unlink($item_path);
        }
        @rmdir($dir);
    }
}
