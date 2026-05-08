<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Helpers_Databank — backing queries for the DRAMS Databank advanced
 * searches. One static method per external database. Each method takes
 * an associative array of field=>value filters (only non-empty filters
 * are applied, AND'd together), and returns the matching rows directly
 * from the source database. No delegation to per-person endpoints —
 * Databank is a self-contained search facility.
 *
 * Design rules:
 *   - Each method clamps $limit to a safe range to prevent runaway
 *     queries against the remote databases.
 *   - Text fields use LIKE %value%; identifier fields (CNIC, MSISDN,
 *     etc.) use exact match.
 *   - All values are escaped with $DB->escape() so the dynamic WHERE
 *     building stays parametric. We avoid the parameter-binding API
 *     because Kohana's prepared-query support is uneven across the
 *     PDO/MySQLi drivers used by these connections.
 *   - When a filter doesn't apply to a given table column, it is
 *     silently dropped rather than producing an error — the form lets
 *     users mix fields freely.
 */
class Helpers_Databank
{
    /* ------------------------------------------------------------------ */
    /*  ECP (electoral)                                                   */
    /* ------------------------------------------------------------------ */

    /**
     * Search ecp_persons by any combination of: cnic, name, father,
     * address, phone, district (uc_block_code).
     *
     * Phone is satisfied via a sub-query against ecp_person_numbers.
     * All other fields query columns on ecp_persons directly.
     *
     * @param array $filters  associative; only non-empty values are used
     * @param int   $limit
     * @return array          stdClass rows
     */
    public static function search_ecp(array $filters, $limit = 100)
    {
        $limit = self::_clamp($limit, 1, 500);
        try {
            $DB    = Database::instance('ecp');
            $where = array();

            if (!empty($filters['cnic'])) {
                // ECP stores CNIC as a 13-digit string (no dashes). Strip
                // any user-supplied dashes/spaces before exact match.
                $variants = self::_cnic_variants($filters['cnic']);
                if (!empty($variants)) {
                    $or = array();
                    foreach ($variants as $v) { $or[] = 'p.cnic = ' . $DB->escape($v); }
                    $where[] = '(' . implode(' OR ', $or) . ')';
                }
            }
            if (!empty($filters['name'])) {
                $where[] = 'p.name_text LIKE ' . $DB->escape('%' . trim($filters['name']) . '%');
            }
            if (!empty($filters['father'])) {
                $where[] = 'p.father_text LIKE ' . $DB->escape('%' . trim($filters['father']) . '%');
            }
            if (!empty($filters['address'])) {
                $where[] = 'p.address_text LIKE ' . $DB->escape('%' . trim($filters['address']) . '%');
            }
            if (!empty($filters['district'])) {
                $where[] = 'p.uc_block_code LIKE ' . $DB->escape('%' . trim($filters['district']) . '%');
            }
            if (!empty($filters['phone'])) {
                $phone = preg_replace('/\D/', '', $filters['phone']);
                if ($phone !== '') {
                    $where[] = 'p.id IN (SELECT n.ecp_person_id FROM ecp_person_numbers n WHERE n.number = '
                        . $DB->escape($phone) . ')';
                }
            }
            if (empty($where)) {
                return array();
            }

            $sql = "SELECT p.id, p.cnic, p.age, p.gender,
                        p.name_text, p.father_text, p.address_text,
                        p.code, p.family_number, p.file_name, p.folder_name, p.uc_block_code,
                        p.address_image_base64,
                        (SELECT GROUP_CONCAT(n.number ORDER BY n.number SEPARATOR ', ')
                         FROM ecp_person_numbers n
                         WHERE n.ecp_person_id = p.id) AS linked_numbers
                    FROM ecp_persons p
                    WHERE " . implode(' AND ', $where) . "
                    LIMIT {$limit}";
            return $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        } catch (Exception $e) {
            self::_log_failure('search_ecp', $e, $filters);
            return array();
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Subscriber (unified Mobile + Foreigner)                           */
    /* ------------------------------------------------------------------ */

    /**
     * Unified subscriber search across BOTH `subscribers_main` (mobile)
     * and `afghan_accounts` (foreigner). Both tables live on the same
     * `mobile` connection (subscriber_db). The system tries each source
     * for fields that apply, merges the results, and tags every row
     * with a `source` of 'mobile' or 'foreigner' so the UI can render
     * the right columns.
     *
     * Field → table mapping:
     *
     *   msisdn   → both    (subscribers_main.msisdn AND afghan_accounts.msisdn,
     *                       with PK number-format variants applied)
     *   cnic     → both    (mobile: cnic; afghan: foreign_cnic OR master_acc_number)
     *   imsi     → mobile  only
     *   name     → afghan  only (master_name LIKE)
     *   father   → afghan  only (father_name LIKE)
     *   address  → afghan  only (site_address LIKE)
     *   district → afghan  only (master_pak_district LIKE)
     *
     * Mobile-side does NOT search `imei` or `subscriber_name` — those
     * columns are not in the existing Helpers_Subscriber::search() allow-
     * list and their presence on every shard of subscribers_main is not
     * verified. Add them here once schema is confirmed.
     *
     * @return array  merged stdClass rows; each has ->source = 'mobile' | 'foreigner'
     */
    public static function search_subscriber_unified(array $filters, $limit = 100)
    {
        $limit = self::_clamp($limit, 1, 500);
        $rows  = array();

        // --- subscribers_main (mobile) — confirmed columns from existing
        // Model_Userrequest::subscriber_external_search_results():
        //   msisdn, cnic, name, address, imsi, status, bvs_status.
        // (subscribers_main has no father column — that filter is only
        // honoured on the afghan side below.)
        try {
            $DB    = Database::instance('mobile');
            $where = array();

            if (!empty($filters['msisdn'])) {
                $variants = self::_msisdn_variants($filters['msisdn']);
                $or = array();
                foreach ($variants as $v) {
                    $or[] = 'msisdn = ' . $DB->escape($v);
                }
                $where[] = '(' . implode(' OR ', $or) . ')';
            }
            if (!empty($filters['cnic'])) {
                $where[] = 'cnic = ' . $DB->escape(trim($filters['cnic']));
            }
            if (!empty($filters['imsi'])) {
                $where[] = 'imsi = ' . $DB->escape(trim($filters['imsi']));
            }
            if (!empty($filters['name'])) {
                $where[] = 'name LIKE ' . $DB->escape('%' . trim($filters['name']) . '%');
            }
            if (!empty($filters['address'])) {
                $where[] = 'address LIKE ' . $DB->escape('%' . trim($filters['address']) . '%');
            }

            if (!empty($where)) {
                $sql  = "SELECT *, 'mobile' AS source FROM subscribers_main
                         WHERE " . implode(' AND ', $where) . "
                         LIMIT {$limit}";
                $main = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
                $rows = array_merge($rows, $main);
            }
        } catch (Exception $e) {
            // Remote DB unreachable or schema mismatch — log it but
            // don't abort; the foreigner side may still produce results.
            self::_log_failure('search_subscriber_unified.subscribers_main', $e, $filters);
        }

        // --- afghan_accounts (foreigner) ---
        try {
            $DB    = Database::instance('mobile');  // same connection
            $where = array();

            if (!empty($filters['msisdn'])) {
                $variants = self::_msisdn_variants($filters['msisdn']);
                $or = array();
                foreach ($variants as $v) {
                    $or[] = 'msisdn = ' . $DB->escape($v);
                }
                $where[] = '(' . implode(' OR ', $or) . ')';
            }
            if (!empty($filters['cnic'])) {
                // CNIC field on afghan_accounts is `foreign_cnic`. Try
                // both columns so a value entered as "cnic" still hits
                // a foreigner record looked up by master_acc_number.
                $cnic_esc = $DB->escape(trim($filters['cnic']));
                $where[] = "(foreign_cnic = {$cnic_esc} OR master_acc_number = {$cnic_esc})";
            }
            if (!empty($filters['name'])) {
                $where[] = 'master_name LIKE ' . $DB->escape('%' . trim($filters['name']) . '%');
            }
            if (!empty($filters['father'])) {
                $where[] = 'father_name LIKE ' . $DB->escape('%' . trim($filters['father']) . '%');
            }
            if (!empty($filters['address'])) {
                $where[] = 'site_address LIKE ' . $DB->escape('%' . trim($filters['address']) . '%');
            }
            if (!empty($filters['district'])) {
                $d = $DB->escape('%' . trim($filters['district']) . '%');
                $where[] = "(master_pak_district LIKE {$d} OR master_pak_tehsil LIKE {$d})";
            }

            if (!empty($where)) {
                $sql      = "SELECT *, 'foreigner' AS source FROM afghan_accounts
                             WHERE " . implode(' AND ', $where) . "
                             LIMIT {$limit}";
                $foreign  = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
                $rows     = array_merge($rows, $foreign);
            }
        } catch (Exception $e) {
            self::_log_failure('search_subscriber_unified.afghan_accounts', $e, $filters);
        }

        return $rows;
    }

    /* ------------------------------------------------------------------ */
    /*  CTD KPK                                                           */
    /* ------------------------------------------------------------------ */

    public static function search_ctd_kpk(array $filters, $limit = 100)
    {
        $limit = self::_clamp($limit, 1, 500);
        try {
            $DB    = Database::instance('ctd_kpk');
            $where = array();

            if (!empty($filters['cnic'])) {
                // CTD KPK stores CNICs as both bare digits and dashed
                // (XXXXX-XXXXXXX-X). Mirror the IN(...) match used by
                // Helpers_Person::get_person_external_profile_ctd_kpk
                // so a user-supplied digit-only string still hits dashed
                // rows and vice-versa.
                $variants = self::_cnic_variants($filters['cnic']);
                if (!empty($variants)) {
                    $or = array();
                    foreach ($variants as $v) { $or[] = 'p.CNIC = ' . $DB->escape($v); }
                    $where[] = '(' . implode(' OR ', $or) . ')';
                }
            }
            if (!empty($filters['name'])) {
                $where[] = 'p.Name LIKE ' . $DB->escape('%' . trim($filters['name']) . '%');
            }
            if (!empty($filters['father'])) {
                $where[] = 'p.FatherName LIKE ' . $DB->escape('%' . trim($filters['father']) . '%');
            }
            if (!empty($filters['district'])) {
                $d = $DB->escape('%' . trim($filters['district']) . '%');
                $where[] = "(p.PermAdrDistrict LIKE {$d} OR p.CurrAdrDistrict LIKE {$d})";
            }
            if (!empty($filters['fir'])) {
                // FIR cross-table lookup: pull PersonIDs from
                // dct_person_profile_status_detail (Schedule IV) and
                // dsr_terrorism_attack (Accused) that mention this FIR
                // number, then constrain dct_person_profile to that set.
                $fir_esc = $DB->escape(trim($filters['fir']));
                $where[] = "p.PersonId IN (
                    SELECT DISTINCT spd.PersonID
                      FROM dct_person_profile_status_detail spd
                     WHERE spd.FirRefNo = {$fir_esc}
                    UNION
                    SELECT DISTINCT spd2.PersonID
                      FROM dct_person_profile_status_detail spd2
                      JOIN dsr_terrorism_attack ta ON ta.TerrorismAttackID = spd2.ActivityID
                     WHERE ta.FIRNumber = {$fir_esc}
                )";
            }
            if (empty($where)) {
                return array();
            }

            // Pull a few useful FIR fields from the status-detail table
            // alongside the person profile so the result row can show
            // an FIR badge without a second round-trip.
            $sql = "SELECT p.*,
                           (SELECT spd.FirRefNo FROM dct_person_profile_status_detail spd
                              WHERE spd.PersonID = p.PersonId AND spd.FirRefNo IS NOT NULL
                              ORDER BY spd.StatusID DESC LIMIT 1) AS LatestFirRefNo,
                           (SELECT spd.FirRefDate FROM dct_person_profile_status_detail spd
                              WHERE spd.PersonID = p.PersonId AND spd.FirRefDate IS NOT NULL
                              ORDER BY spd.StatusID DESC LIMIT 1) AS LatestFirRefDate
                    FROM dct_person_profile p
                    WHERE " . implode(' AND ', $where) . "
                    LIMIT {$limit}";
            return $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        } catch (Exception $e) {
            self::_log_failure('search_ctd_kpk', $e, $filters);
            return array();
        }
    }

    /* ------------------------------------------------------------------ */
    /*  DLMS (driving licence)                                            */
    /* ------------------------------------------------------------------ */

    public static function search_dlms(array $filters, $limit = 100)
    {
        $limit = self::_clamp($limit, 1, 500);
        try {
            $DB    = Database::instance('dlms_sqlsrv');
            $where = array();

            if (!empty($filters['cnic'])) {
                // DLMS stores CNICs in dashed form (XXXXX-XXXXXXX-X) for
                // most rows; some legacy rows are bare digits. Mirror the
                // IN(...) match used by
                // Helpers_Person::get_person_external_profile_driving_license
                // so a digit-only search still finds the dashed rows.
                // This was the bug for CNIC 1730113816259 — the row exists
                // in DLMS as "17301-1381625-9" and exact-match on digits
                // missed it.
                $variants = self::_cnic_variants($filters['cnic']);
                if (!empty($variants)) {
                    $or = array();
                    foreach ($variants as $v) { $or[] = 'p.CNIC = ' . $DB->escape($v); }
                    $where[] = '(' . implode(' OR ', $or) . ')';
                }
            }
            if (!empty($filters['name'])) {
                $name_esc = $DB->escape('%' . trim($filters['name']) . '%');
                $where[] = "(p.FirstName LIKE {$name_esc} OR p.LastName LIKE {$name_esc} OR p.MiddleName LIKE {$name_esc})";
            }
            if (!empty($filters['father'])) {
                $f = $DB->escape('%' . trim($filters['father']) . '%');
                $where[] = "(p.FatherFName LIKE {$f} OR p.FatherLName LIKE {$f})";
            }
            if (!empty($filters['license_no'])) {
                $where[] = 'd.LicenseNo = ' . $DB->escape(trim($filters['license_no']));
            }
            if (empty($where)) {
                return array();
            }

            // SQL Server uses TOP (n) instead of LIMIT n
            $sql = "SELECT TOP ({$limit}) p.PersonID, p.CNIC,
                        p.FirstName, p.MiddleName, p.LastName,
                        p.FatherFName, p.FatherMName, p.FatherLName,
                        p.DOB, p.BirthPlace, p.Gender, p.Mobile,
                        d.LicenseNo, d.EntryDate, d.ExpiryDate
                    FROM License_Person p
                    LEFT JOIN License_Details d ON d.PersonID = p.PersonID
                    WHERE " . implode(' AND ', $where);
            return $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        } catch (Exception $e) {
            self::_log_failure('search_dlms', $e, $filters);
            return array();
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Government employees                                              */
    /* ------------------------------------------------------------------ */

    public static function search_govt_emp(array $filters, $limit = 100)
    {
        $limit = self::_clamp($limit, 1, 500);
        try {
            $DB    = Database::instance('govt_emp_data');
            $where = array();

            if (!empty($filters['cnic'])) {
                // employee_data stores national_id as bare 13 digits in
                // every row we've inspected, but accept dashed input from
                // the user too — strip non-digits via _cnic_variants[0].
                $variants = self::_cnic_variants($filters['cnic']);
                if (!empty($variants)) {
                    $or = array();
                    foreach ($variants as $v) { $or[] = 'national_id = ' . $DB->escape($v); }
                    $where[] = '(' . implode(' OR ', $or) . ')';
                }
            }
            if (!empty($filters['name'])) {
                $name_esc = $DB->escape('%' . trim($filters['name']) . '%');
                $where[] = "(first_name LIKE {$name_esc} OR last_name LIKE {$name_esc})";
            }
            if (!empty($filters['father'])) {
                $where[] = 'father_husband_name LIKE ' . $DB->escape('%' . trim($filters['father']) . '%');
            }
            if (!empty($filters['pers_no'])) {
                $where[] = 'pers_no = ' . $DB->escape(trim($filters['pers_no']));
            }
            if (!empty($filters['org_unit'])) {
                $o = $DB->escape('%' . trim($filters['org_unit']) . '%');
                $where[] = "(org_unit LIKE {$o} OR org_unit_short_text LIKE {$o})";
            }
            if (!empty($filters['job'])) {
                $j = $DB->escape('%' . trim($filters['job']) . '%');
                $where[] = "(position LIKE {$j} OR job LIKE {$j} OR job_title LIKE {$j})";
            }
            if (empty($where)) {
                return array();
            }

            $sql = "SELECT * FROM employee_data
                    WHERE " . implode(' AND ', $where) . "
                    LIMIT {$limit}";
            return $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        } catch (Exception $e) {
            self::_log_failure('search_govt_emp', $e, $filters);
            return array();
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Internal helpers                                                  */
    /* ------------------------------------------------------------------ */

    /** Clamp $v into [$min, $max]. */
    private static function _clamp($v, $min, $max)
    {
        $v = (int) $v;
        if ($v < $min) return $min;
        if ($v > $max) return $max;
        return $v;
    }

    /**
     * Normalise a CNIC into the variants the external databases store.
     * Returns up to two strings — the bare 13-digit form and the
     * canonical dashed form (XXXXX-XXXXXXX-X). Use both with WHERE …
     * IN (a, b) when a database is known to store one or the other
     * (CTD KPK and DLMS both do).
     *
     * Trims any non-digit characters first so callers can pass
     * "17301-1381625-9", "17301 1381625 9", or "1730113816259" and
     * still get the same canonical pair back.
     */
    private static function _cnic_variants($raw)
    {
        $digits = preg_replace('/\D+/', '', (string) $raw);
        if ($digits === '') {
            return array();
        }
        $out = array($digits);
        if (strlen($digits) === 13) {
            $dashed = substr($digits, 0, 5) . '-' . substr($digits, 5, 7) . '-' . substr($digits, 12, 1);
            $out[] = $dashed;
        }
        return $out;
    }

    /**
     * Centralised exception logger for the Databank search helpers.
     * Mirrors the call style used by zong.inc / Cronjob.php / the
     * cdr parsers — keeps every Databank failure searchable in
     * system_error_log via the same `error_source` prefix.
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

    /**
     * Pakistani-MSISDN variants for OR matching. Mirrors the logic in
     * Helpers_Subscriber::search() so a number entered as 0312…, 312…,
     * 92312…, or +92312… all match the same row.
     *
     * @return string[]
     */
    private static function _msisdn_variants($input)
    {
        $val = preg_replace('/[^0-9]/', '', (string) $input);
        if ($val === '') {
            return array();
        }
        if (strlen($val) === 10 && substr($val, 0, 1) === '3') {
            return array($val, '0' . $val, '92' . $val, '+92' . $val);
        }
        if (strlen($val) === 11 && substr($val, 0, 1) === '0') {
            $v = substr($val, 1);
            return array($val, $v, '92' . $v, '+92' . $v);
        }
        if (substr($val, 0, 2) === '92') {
            $v = substr($val, 2);
            return array($val, $v, '0' . $v, '+92' . $v);
        }
        return array($val);
    }
}
