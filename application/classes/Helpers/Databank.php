<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Helpers_Databank — data source of the DRAMS Databank searches
 * (Controller_Databank). The records come from the Databank integration
 * API (Helpers_DatabankApi, POST integration/search); DRAMS no longer
 * queries the external databases for these pages.
 *
 * Each search_* method takes ONE identifier (the API searches by one
 * mobile number, CNIC, name or FIR number at a time), asks the API for
 * its source only, and maps every returned record onto the row keys the
 * result table (databank_advanced_results) already uses, so the table
 * keeps its columns. A value the API does not return is left ''.
 *
 * refine_rows() applies the other filled-in form fields to the returned
 * records, which keeps the form's "all filters AND together" rule.
 *
 * Errors are NOT swallowed: Helpers_DatabankApi_Exception reaches the
 * controller, which shows it instead of "no matching records".
 *
 * dlms_pdo() is unrelated to the Databank pages: the person dashboard's
 * DLMS panel (Helpers_Person::get_person_external_profile_driving_license)
 * still reads the DLMS server directly through it.
 */
class Helpers_Databank
{
    /**
     * Cached raw PDO handle for the DLMS SQL Server. We bypass Kohana's
     * Database_PDO driver for this one connection because the driver
     * runs `SET NAMES 'utf8'` after connect (MySQL-only syntax) which
     * SQL Server rejects, and surfaces other unsupported PDO attributes
     * (ATTR_TIMEOUT) to pdo_sqlsrv. A direct PDO with the verified-
     * working DSN + ATTR_ERRMODE only is more reliable.
     *
     * Set by self::dlms_pdo() on first call, reused for the request.
     */
    private static $_dlms_pdo = null;

    /**
     * Open (or return the cached) raw PDO connection to DLMS.
     *
     * Reads the DSN, username and password from
     * application/config/database.php (the `dlms_sqlsrv` block — same
     * source of truth as Kohana's Database::instance), but constructs
     * the PDO object directly with only PDO::ATTR_ERRMODE set and
     * skips Kohana's driver layer entirely.
     *
     * @return PDO|null  PDO on success, NULL on connect failure (logged
     *                   via Model_ErrorLog::log so callers can fail
     *                   silently without losing diagnostic info).
     */
    public static function dlms_pdo()
    {
        if (self::$_dlms_pdo instanceof PDO) {
            return self::$_dlms_pdo;
        }
        try {
            $cfg = Kohana::$config->load('database')->get('dlms_sqlsrv');
            if (!is_array($cfg) || empty($cfg['connection'])) {
                throw new Exception('dlms_sqlsrv block missing from application/config/database.php');
            }
            $conn = $cfg['connection'];
            $dsn  = isset($conn['dsn'])      ? (string) $conn['dsn']      : '';
            $user = isset($conn['username']) ? (string) $conn['username'] : '';
            $pass = isset($conn['password']) ? (string) $conn['password'] : '';

            // ATTR_ERRMODE is the ONLY attribute we set at construction.
            // pdo_sqlsrv rejects most others (ATTR_TIMEOUT,
            // ATTR_DEFAULT_FETCH_MODE) with "SQLSTATE[IMSSP]: An
            // unsupported attribute was designated on the PDO object".
            self::$_dlms_pdo = new PDO($dsn, $user, $pass, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ));
            return self::$_dlms_pdo;
        } catch (Exception $e) {
            self::_log_failure('dlms_pdo_connect', $e, array());
            return null;
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Searches (Databank integration API)                               */
    /*                                                                    */
    /*  Each returns ['rows' => mapped rows, 'limit' => records asked     */
    /*  for, 'request_id' => Databank requestId] and throws               */
    /*  Helpers_DatabankApi_Exception on any failure.                     */
    /* ------------------------------------------------------------------ */

    /** Mobile Subscription (local subscribers + foreign nationals). */
    public static function search_subscriber_unified($type, $value)
    {
        return self::_search('MOBILE', $type, $value, function (array $rec, array $f) {
            $msisdn = self::_get($f, 'Mobile number');
            if ($msisdn === '' && isset($rec['title']) && is_string($rec['title'])
                && preg_match('/^\+?\d[\d \-]{6,}$/', $rec['title'])) {
                // Mobile records carry the number as their title.
                $msisdn = $rec['title'];
            }
            $category = self::_get($f, 'Subscriber category');
            if ($category === '' && isset($rec['recordType'])) {
                $category = $rec['recordType'] === 'subscription_foreign' ? 'Foreign national' : 'Local subscriber';
            }
            return array(
                'source'              => $category,
                'msisdn'              => $msisdn,
                'cnic'                => self::_get($f, 'CNIC'),
                'name'                => self::_get($f, 'Name'),
                'address'             => self::_get($f, 'Address'),
                'master_pak_province' => self::_get($f, 'Province'),
                'master_pak_district' => self::_get($f, 'District'),
                'master_pak_tehsil'   => self::_get($f, 'Tehsil'),
                'operator'            => self::_get($f, 'Mobile operator'),
                'status'              => self::_get($f, 'Status'),
                'bvs_status'          => self::_get($f, 'Biometric verification (BVS)'),
            );
        });
    }

    /** Election Commission (ECP) electoral roll. */
    public static function search_ecp($type, $value)
    {
        return self::_search('ECP', $type, $value, function (array $rec, array $f) {
            // The record reference ("Record reference 123") carries the ECP id.
            $id = '';
            if (isset($rec['reference']) && is_string($rec['reference'])
                && preg_match('/(\d+)\s*$/', $rec['reference'], $m)) {
                $id = $m[1];
            }
            return array(
                'id'                   => $id,
                'cnic'                 => self::_get($f, 'CNIC'),
                'name_text'            => self::_get($f, 'Name'),
                'father_text'          => self::_get($f, "Father's name"),
                'address_text'         => self::_get($f, 'Address'),
                'age'                  => self::_get($f, 'Age'),
                'gender'               => self::_get($f, 'Gender'),
                'uc_block_code'        => self::_get($f, 'Electoral block code'),
                'linked_numbers'       => self::_get($f, 'Phone numbers'),
                'address_image_base64' => self::_get($f, 'Address (image)'),
            );
        });
    }

    /** CTD KPK FIR records (person profiles). */
    public static function search_ctd_kpk($type, $value)
    {
        return self::_search('FIR', $type, $value, function (array $rec, array $f) {
            return array(
                'CNIC'             => self::_get($f, 'CNIC'),
                'Name'             => self::_get($f, 'Name'),
                'FatherName'       => self::_get($f, "Father's name"),
                'Alias'            => self::_get($f, 'Alias'),
                'Tribe'            => self::_get($f, 'Tribe'),
                'BirthPlace'       => self::_get($f, 'Place of birth'),
                'LatestFirRefNo'   => self::_get($f, 'Latest FIR number'),
                'LatestFirRefDate' => self::_get($f, 'FIR date'),
            );
        });
    }

    /** DLMS driving licences. */
    public static function search_dlms($type, $value)
    {
        return self::_search('DRIVING_LICENCE', $type, $value, function (array $rec, array $f) {
            return array(
                'CNIC'       => self::_get($f, 'CNIC'),
                'Name'       => self::_get($f, 'Name'),
                'FatherName' => self::_get($f, "Father's name"),
                'DOB'        => self::_get($f, 'Date of birth'),
                'Gender'     => self::_get($f, 'Gender'),
                'LicenseNo'  => self::_get($f, 'Licence number'),
                'IssueDate'  => self::_get($f, 'Issued on'),
                'ExpiryDate' => self::_get($f, 'Expires on'),
            );
        });
    }

    /** Government employees. */
    public static function search_govt_emp($type, $value)
    {
        return self::_search('GOVT_EMPLOYEE', $type, $value, function (array $rec, array $f) {
            return array(
                'national_id'         => self::_get($f, 'CNIC'),
                'name'                => self::_get($f, 'Name'),
                'father_husband_name' => self::_get($f, "Father's name"),
                'pers_no'             => self::_get($f, 'Personnel number'),
                'job_title'           => self::_get($f, 'Job title'),
                'org_unit_short_text' => self::_get($f, 'Organisation unit'),
                'personnel_area'      => self::_get($f, 'Personnel area'),
                'description'         => self::_get($f, 'Description'),
            );
        });
    }

    /**
     * Vehicle / Excise registrations — by CNIC, or by a mobile number
     * (the Databank searches the CNIC it is registered to). Vehicle
     * records differ in their fields, so the table columns come from
     * the records (records_table()).
     *
     * @return array ['rows', 'columns', 'limit', 'request_id']
     */
    public static function search_vehicle($type, $value)
    {
        $result = Helpers_DatabankApi::search($type, $value, 'VEHICLE');
        $table  = self::records_table($result['records']);
        return array('rows' => $table['rows'], 'columns' => $table['columns'],
                     'limit' => $result['limit'], 'request_id' => $result['request_id']);
    }

    /**
     * One identifier in every source the API user may read, one block
     * per source.
     *
     * @return array ['blocks' => list of [code, label, status, statusLabel,
     *               message, rows, columns], 'linked' => list of steps
     *               followed, 'limit' => int, 'request_id' => string]
     */
    public static function search_all_sources($type, $value, $linked)
    {
        $json   = Helpers_DatabankApi::search_all($type, $value, $linked);
        $text   = function ($a, $k) { return (isset($a[$k]) && is_scalar($a[$k])) ? (string) $a[$k] : ''; };
        $blocks = array();
        foreach ($json['sources'] as $s) {
            if (!is_array($s)) {
                continue;
            }
            $records = (isset($s['records']) && is_array($s['records'])) ? array_filter($s['records'], 'is_array') : array();
            $table   = self::records_table(array_values($records));
            $blocks[] = array(
                'code'        => $text($s, 'code'),
                'label'       => $text($s, 'label'),
                'status'      => $text($s, 'status'),
                'statusLabel' => $text($s, 'statusLabel'),
                'message'     => $text($s, 'message'),
                'rows'        => $table['rows'],
                'columns'     => $table['columns'],
            );
        }
        $steps = array();
        if (isset($json['linked']['steps']) && is_array($json['linked']['steps'])) {
            foreach ($json['linked']['steps'] as $step) {
                if (is_string($step)) {
                    $steps[] = $step;
                }
            }
        }
        return array('blocks' => $blocks, 'linked' => $steps,
                     'limit' => max(1, min(50, (int) Kohana::$config->load('databank')->get('records_per_search', 25))),
                     'request_id' => isset($json['requestId']) ? preg_replace('/[^A-Za-z0-9\-]/', '', (string) $json['requestId']) : '');
    }

    /**
     * ECP household of one record (POST integration/ecp/family), mapped
     * onto the property names databank_ecp_family already renders.
     *
     * @return array ['seed' => stdClass|NULL, 'family' => list of stdClass]
     */
    public static function ecp_family($ecp_id)
    {
        $json = Helpers_DatabankApi::ecp_family($ecp_id);
        $map  = function ($rec) {
            if (!is_array($rec)) {
                return NULL;
            }
            $f  = self::_fields($rec);
            $id = '';
            if (isset($rec['reference']) && is_string($rec['reference']) && preg_match('/(\d+)\s*$/', $rec['reference'], $m)) {
                $id = $m[1];
            }
            return (object) array(
                'id'                   => $id,
                'cnic'                 => self::_get($f, 'CNIC'),
                'name_text'            => self::_get($f, 'Name'),
                'father_text'          => self::_get($f, "Father's name"),
                'address_text'         => self::_get($f, 'Address'),
                'age'                  => self::_get($f, 'Age'),
                'gender'               => self::_get($f, 'Gender'),
                'uc_block_code'        => self::_get($f, 'Electoral block code'),
                'linked_numbers'       => self::_get($f, 'Phone numbers'),
                'address_image_base64' => self::_get($f, 'Address (image)'),
            );
        };
        $family = array();
        if (isset($json['members']) && is_array($json['members'])) {
            foreach ($json['members'] as $rec) {
                $row = $map($rec);
                if ($row !== NULL) {
                    $family[] = $row;
                }
            }
        }
        return array('seed' => isset($json['record']) ? $map($json['record']) : NULL, 'family' => $family);
    }

    /**
     * Records whose fields differ as one table: a column per field label
     * (in order of first appearance; images as image columns), then
     * "Reference" and "Found through" when any record has them.
     *
     * @return array ['columns' => results-view column configs, 'rows' => rows keyed by column key]
     */
    public static function records_table(array $records)
    {
        $columns = array();
        $rows    = array();
        $extra   = array();
        foreach ($records as $rec) {
            $row = array();
            foreach (self::_fields($rec) as $label => $value) {
                $image = strpos($value, 'data:image/') === 0;
                if ($value === '' || ($image && !preg_match('#^data:image/(jpeg|png|gif|webp);base64,[A-Za-z0-9+/=]+$#', $value))) {
                    continue;
                }
                $key = 'f_' . md5($label);
                if (!isset($columns[$key])) {
                    $columns[$key] = array('key' => $key, 'label' => $label, 'formatter' => $image ? 'image_jpeg' : 'text');
                }
                $row[$key] = $value;
            }
            foreach (array('_reference' => 'reference', '_found' => 'foundThrough') as $key => $prop) {
                if (isset($rec[$prop]) && is_string($rec[$prop]) && trim($rec[$prop]) !== '') {
                    $row[$key]   = trim($rec[$prop]);
                    $extra[$key] = TRUE;
                }
            }
            $rows[] = $row;
        }
        if (isset($extra['_reference'])) {
            $columns['_reference'] = array('key' => '_reference', 'label' => 'Reference');
        }
        if (isset($extra['_found'])) {
            $columns['_found'] = array('key' => '_found', 'label' => 'Found through');
        }
        return array('columns' => array_values($columns), 'rows' => $rows);
    }

    /**
     * Keep the rows matching every condition (AND). A condition is
     *   ['field' => label for messages, 'keys' => row keys to check,
     *    'value' => user input, 'match' => 'contains' | 'exact' | 'cnic']
     * and a row matches it when any of its keys does.
     *
     * @return array ['rows' => kept rows, 'unavailable' => labels of the
     *               conditions whose keys were empty on every row, i.e.
     *               the API returned nothing to check them against]
     */
    public static function refine_rows(array $rows, array $conditions)
    {
        $unavailable = array();
        foreach ($conditions as $c) {
            if (empty($rows)) {
                break;
            }
            $kept = array();
            $seen = FALSE;
            foreach ($rows as $row) {
                foreach ($c['keys'] as $k) {
                    $v = isset($row[$k]) ? trim((string) $row[$k]) : '';
                    if ($v === '') {
                        continue;
                    }
                    $seen = TRUE;
                    if (self::_matches($v, (string) $c['value'], isset($c['match']) ? $c['match'] : 'contains')) {
                        $kept[] = $row;
                        break;
                    }
                }
            }
            if (!$seen) {
                $unavailable[] = $c['field'];
            }
            $rows = $kept;
        }
        return array('rows' => $rows, 'unavailable' => $unavailable);
    }

    /* ------------------------------------------------------------------ */
    /*  Internal helpers                                                  */
    /* ------------------------------------------------------------------ */

    /** One API search in $source, every record mapped by $map(record, fields). */
    private static function _search($source, $type, $value, $map)
    {
        $result = Helpers_DatabankApi::search($type, $value, $source);
        $rows   = array();
        foreach ($result['records'] as $rec) {
            $rows[] = $map($rec, self::_fields($rec));
        }
        return array('rows' => $rows, 'limit' => $result['limit'], 'request_id' => $result['request_id']);
    }

    /**
     * A record's fields as label => value (the first value of a label
     * wins). An ECP image is added as "<label> (image)" when it is a
     * plain base64 data URI — anything else is dropped, never rendered.
     */
    private static function _fields(array $rec)
    {
        $out    = array();
        $fields = (isset($rec['fields']) && is_array($rec['fields'])) ? $rec['fields'] : array();
        foreach ($fields as $f) {
            if (!is_array($f) || !isset($f['label']) || !is_string($f['label']) || isset($out[$f['label']])) {
                continue;
            }
            $out[$f['label']] = (isset($f['value']) && is_scalar($f['value'])) ? trim((string) $f['value']) : '';
            if (isset($f['image']) && is_string($f['image'])
                && preg_match('#^data:image/(jpeg|png|gif|webp);base64,[A-Za-z0-9+/=]+$#', $f['image'])) {
                $out[$f['label'] . ' (image)'] = $f['image'];
            }
        }
        return $out;
    }

    private static function _get(array $fields, $label)
    {
        return isset($fields[$label]) ? $fields[$label] : '';
    }

    private static function _matches($have, $want, $match)
    {
        if ($match === 'cnic') {
            return preg_replace('/\D+/', '', $have) === preg_replace('/\D+/', '', $want);
        }
        $have = mb_strtolower((string) preg_replace('/\s+/u', ' ', $have));
        $want = mb_strtolower(trim((string) preg_replace('/\s+/u', ' ', $want)));
        if ($match === 'exact') {
            return $have === $want;
        }
        return $want === '' || mb_strpos($have, $want) !== FALSE;
    }

    /**
     * Centralised exception logger for dlms_pdo(). Mirrors the call
     * style used by zong.inc / Cronjob.php / the cdr parsers — keeps
     * every failure searchable in system_error_log via the same
     * `error_source` prefix.
     */
    private static function _log_failure($helper, Exception $e, array $filters)
    {
        try {
            Model_ErrorLog::log(
                'helpers_databank_' . $helper,
                $e->getMessage(),
                array(
                    'filters' => $filters,
                    'class'   => get_class($e),
                ),
                $e->getTraceAsString(),
                'database_error',
                'databank_search',
                'error'
            );
        } catch (Exception $log_failed) {
            // Logger itself blew up — last-ditch fallback so we don't
            // double-fault the search.
            error_log('Helpers_Databank::' . $helper . ' failed; logger also failed: '
                . $e->getMessage() . ' | logger: ' . $log_failed->getMessage());
        }
    }
}
