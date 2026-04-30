<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Helpers_BulkRequest
 *
 * Authoritative builder + validator for the bulk-request email body
 * formats each telco's LEA team enforces. Used by:
 *
 *  - action_build_bulk_body (AJAX preview, called from the custom request
 *    form as the admin types numbers / dates / picks a company)
 *  - action_admincustomsend (final pre-send validation)
 *
 * The format definitions come from the user-supplied templates:
 *   ~/Desktop/Bulk CDR sending format.docx
 *   ~/Desktop/Bulk IMEI sending format.docx
 *   ~/Desktop/Bulk CNIC sending format.docx
 *   ~/Desktop/MSISDN Data.txt   (Ufone .txt sample)
 *   ~/Desktop/IMEI Data.txt     (Ufone .txt sample)
 *
 * Telco company_name (mnc) ids:
 *   1 = Mobilink (Jazz), 3 = Ufone, 4 = Zong, 6 = Telenor.
 *
 * Request types (matching the single-request form's set):
 *   1 = CDR by Mobile, 2 = CDR by IMEI,
 *   3 = Subscriber by Mobile, 4 = Location by Mobile,
 *   5 = SIMs by CNIC.
 *
 * Returns from build() and validate() are arrays so callers can inspect
 * both the canonical body and the list of human-readable errors in one
 * pass.
 */
class Helpers_BulkRequest
{
    const COMPANY_MOBILINK = 1;
    const COMPANY_UFONE    = 3;
    const COMPANY_ZONG     = 4;
    const COMPANY_TELENOR  = 6;

    const TYPE_CDR_MOBILE     = 1;
    const TYPE_CDR_IMEI       = 2;
    const TYPE_SUBSCRIBER     = 3;
    const TYPE_LOCATION       = 4;
    const TYPE_SIMS_BY_CNIC   = 5;

    /** Display names used in error messages. */
    public static $TELCO_NAMES = array(
        1 => 'Mobilink (Jazz)',
        3 => 'Ufone',
        4 => 'Zong',
        6 => 'Telenor',
    );

    /**
     * Validate the form inputs against the bulk-template rules and
     * return an array of human-readable error strings. Empty array
     * means the request is ready to send.
     *
     * @param array $input  Keys: request_type, company_name, mobiles[],
     *                      cnics[], imeis[], start_date (mm/dd/yyyy),
     *                      end_date (mm/dd/yyyy).
     */
    public static function validate(array $input)
    {
        $errors = array();

        $request_type = isset($input['request_type']) ? (int) $input['request_type'] : 0;
        $company      = isset($input['company_name']) ? (int) $input['company_name'] : 0;
        $mobiles      = self::_array_of_strings($input, 'mobiles');
        $cnics        = self::_array_of_strings($input, 'cnics');
        $imeis        = self::_array_of_strings($input, 'imeis');
        $start_date   = self::_parse_mdy(isset($input['start_date']) ? $input['start_date'] : '');
        $end_date     = self::_parse_mdy(isset($input['end_date'])   ? $input['end_date']   : '');

        if ($request_type < 1) $errors[] = 'Please select a request type.';
        if ($company < 1)      $errors[] = 'Please select a company.';

        // Date sanity for CDR types (1, 2).
        if ($request_type === self::TYPE_CDR_MOBILE || $request_type === self::TYPE_CDR_IMEI) {
            if (!$start_date) $errors[] = 'Please enter a valid Date From (mm/dd/yyyy).';
            if (!$end_date)   $errors[] = 'Please enter a valid Date To (mm/dd/yyyy).';
            if ($start_date && $end_date && $start_date > $end_date) {
                $errors[] = 'Date From must be earlier than Date To.';
            }
        }

        // CDR by Mobile.
        if ($request_type === self::TYPE_CDR_MOBILE) {
            $max = ($company === self::COMPANY_ZONG) ? 25 : 10;
            if (count($mobiles) === 0) {
                $errors[] = 'Please add at least one mobile number.';
            } elseif (count($mobiles) > $max) {
                $errors[] = 'Maximum ' . $max . ' numbers allowed for '
                    . self::_telco_name($company) . '. You entered ' . count($mobiles) . '.';
            }
            foreach ($mobiles as $m) {
                if (!self::_is_valid_msisdn($m)) {
                    $errors[] = 'Invalid mobile number: ' . $m;
                }
            }
        }

        // CDR by IMEI.
        if ($request_type === self::TYPE_CDR_IMEI) {
            if (count($imeis) === 0) {
                $errors[] = 'Please add at least one IMEI.';
            } elseif (count($imeis) > 10) {
                $errors[] = 'Maximum 10 IMEIs allowed. You entered ' . count($imeis) . '.';
            }
            foreach ($imeis as $im) {
                $d = preg_replace('/\D/', '', $im);
                if (strlen($d) < 14 || strlen($d) > 16) {
                    $errors[] = 'Invalid IMEI: ' . $im . ' (must be 14, 15, or 16 digits).';
                }
            }
        }

        // SIMs by CNIC.
        if ($request_type === self::TYPE_SIMS_BY_CNIC) {
            if (count($cnics) === 0) {
                $errors[] = 'Please add at least one CNIC.';
            } elseif (count($cnics) > 10) {
                $errors[] = 'Maximum 10 CNICs allowed. You entered ' . count($cnics) . '.';
            }
            foreach ($cnics as $c) {
                $d = preg_replace('/\D/', '', $c);
                if (strlen($d) !== 13) {
                    $errors[] = 'Invalid CNIC: ' . $c . ' (must be 13 digits).';
                }
            }
        }

        // Subscriber / Location by mobile.
        if ($request_type === self::TYPE_SUBSCRIBER || $request_type === self::TYPE_LOCATION) {
            if (count($mobiles) === 0) {
                $errors[] = 'Please add at least one mobile number.';
            }
            foreach ($mobiles as $m) {
                if (!self::_is_valid_msisdn($m)) {
                    $errors[] = 'Invalid mobile number: ' . $m;
                }
            }
        }

        return $errors;
    }

    /**
     * Build the bulk email body for the given (request_type, company)
     * combination. Returns the body string, or NULL when there is no
     * defined bulk format for that combination (the caller then falls
     * back to the standard email_templates.body_txt with placeholder
     * substitution).
     *
     * The returned body still contains "ADM-[case_number]" placeholders
     * where the FIR number lives. action_admincustomsend's existing
     * preg_replace swaps those for the real ADM-<reference_id> at send
     * time.
     */
    public static function build(array $input)
    {
        $request_type = isset($input['request_type']) ? (int) $input['request_type'] : 0;
        $company      = isset($input['company_name']) ? (int) $input['company_name'] : 0;
        $mobiles      = self::_array_of_strings($input, 'mobiles');
        $cnics        = self::_array_of_strings($input, 'cnics');
        $imeis        = self::_array_of_strings($input, 'imeis');
        $start_date   = self::_parse_mdy(isset($input['start_date']) ? $input['start_date'] : '');
        $end_date     = self::_parse_mdy(isset($input['end_date'])   ? $input['end_date']   : '');

        if ($request_type === self::TYPE_CDR_MOBILE) {
            if (!$start_date || !$end_date || count($mobiles) === 0) return null;
            switch ($company) {
                case self::COMPANY_MOBILINK: return self::_cdr_mobile_mobilink($mobiles, $start_date, $end_date);
                case self::COMPANY_TELENOR:  return self::_cdr_mobile_telenor($mobiles, $start_date, $end_date);
                case self::COMPANY_ZONG:     return self::_cdr_mobile_zong($mobiles, $start_date, $end_date);
                case self::COMPANY_UFONE:    return self::_cdr_mobile_ufone($mobiles, $start_date, $end_date);
            }
        }
        if ($request_type === self::TYPE_CDR_IMEI) {
            if (!$start_date || !$end_date || count($imeis) === 0) return null;
            switch ($company) {
                case self::COMPANY_MOBILINK: return self::_cdr_imei_mobilink($imeis, $start_date, $end_date);
                case self::COMPANY_TELENOR:  return self::_cdr_imei_telenor($imeis, $start_date, $end_date);
                case self::COMPANY_ZONG:     return self::_cdr_imei_zong($imeis, $start_date, $end_date);
                case self::COMPANY_UFONE:    return self::_cdr_imei_ufone($imeis, $start_date, $end_date);
            }
        }
        if ($request_type === self::TYPE_SIMS_BY_CNIC) {
            if (count($cnics) === 0) return null;
            switch ($company) {
                case self::COMPANY_MOBILINK: return self::_cnic_mobilink($cnics);
                case self::COMPANY_TELENOR:  return self::_cnic_telenor($cnics);
                case self::COMPANY_ZONG:     return self::_cnic_zong($cnics);
                // Ufone CNIC bulk format isn't documented yet — fall through
                // to NULL so the standard template body is used instead.
            }
        }

        return null;
    }

    /* ============================================================
       CDR by Mobile builders
       ============================================================ */

    private static function _cdr_mobile_mobilink($mobiles, $sd, $ed)
    {
        $rows = '';
        foreach ($mobiles as $i => $m) {
            $idx  = $i + 1;
            $msi  = self::_to_msisdn92($m);
            $rows .= '<tr><td>' . $idx . '</td>'
                  . '<td>ADM-[case_number]</td>'
                  . '<td>A;' . $msi . ';' . self::_fmt_date($sd, '/') . ';'
                  . self::_fmt_date($ed, '/') . ';</td></tr>';
        }
        return '<p>Dear Sir/Madam,</p>'
             . '<p>Please provide the requested CDR &amp; SMS log for the numbers below.</p>'
             . '<table border="1" cellpadding="6" cellspacing="0">'
             . '<thead><tr><th>S.NO</th><th>FIR/DD NO</th><th>REQUIRED</th></tr></thead>'
             . '<tbody>' . $rows . '</tbody></table>'
             . '<p>FIR/DD NO ADM-[case_number] PS CTD KPK</p>';
    }

    private static function _cdr_mobile_telenor($mobiles, $sd, $ed)
    {
        $nums = array();
        foreach ($mobiles as $m) $nums[] = self::_to_msisdn92($m);
        return 'Tpn:' . implode(',', $nums)
             . ':' . self::_fmt_date($sd, '-')
             . ':' . self::_fmt_date($ed, '-') . ':';
    }

    private static function _cdr_mobile_zong($mobiles, $sd, $ed)
    {
        $rows = '';
        foreach ($mobiles as $i => $m) {
            $idx = $i + 1;
            $rows .= '<tr><td>' . $idx . '</td>'
                  . '<td>ADM-[case_number]</td>'
                  . '<td>' . self::_to_msisdn92($m) . '</td>'
                  . '<td>' . self::_fmt_date($sd, '/') . ' to ' . self::_fmt_date($ed, '/') . '</td></tr>';
        }
        return '<p>Dear Sir/Madam,</p>'
             . '<p>Please provide the requested CDR &amp; SMS log for the numbers below.</p>'
             . '<table border="1" cellpadding="6" cellspacing="0">'
             . '<thead><tr><th>S.NO</th><th>FIR/DD NO</th><th>NUMBER</th><th>REQUIRED CDR &amp; SMS LOG</th></tr></thead>'
             . '<tbody>' . $rows . '</tbody></table>'
             . '<p>FIR/DD NO ADM-[case_number] PS CTD KPK</p>';
    }

    private static function _cdr_mobile_ufone($mobiles, $sd, $ed)
    {
        $nums = array();
        foreach ($mobiles as $m) $nums[] = self::_to_msisdn92($m);
        return 'MSISDN|Both|' . self::_fmt_date($sd, '/', 'mdy')
             . '|'  . self::_fmt_date($ed, '/', 'mdy')
             . '|'  . implode(':', $nums);
    }

    /* ============================================================
       CDR by IMEI builders
       ============================================================ */

    private static function _cdr_imei_mobilink($imeis, $sd, $ed)
    {
        $rows = '';
        foreach ($imeis as $i => $im) {
            $idx = $i + 1;
            $d   = substr(preg_replace('/\D/', '', $im), 0, 14);
            $rows .= '<tr><td>' . $idx . '</td>'
                  . '<td>ADM-[case_number]</td>'
                  . '<td>I;' . $d . ';' . self::_fmt_date($sd, '/') . ';'
                  . self::_fmt_date($ed, '/') . ';</td></tr>';
        }
        return '<p>Dear Sir/Madam,</p>'
             . '<p>Please provide the CDR for the IMEIs below.</p>'
             . '<table border="1" cellpadding="6" cellspacing="0">'
             . '<thead><tr><th>S/NO</th><th>FIR/DD NO</th><th>Required</th></tr></thead>'
             . '<tbody>' . $rows . '</tbody></table>'
             . '<p>FIR/DD NO ADM-[case_number] PS CTD KPK</p>';
    }

    private static function _cdr_imei_telenor($imeis, $sd, $ed)
    {
        $ids = array();
        foreach ($imeis as $im) $ids[] = substr(preg_replace('/\D/', '', $im), 0, 14);
        return 'Tpi:' . implode(',', $ids)
             . ':' . self::_fmt_date($sd, '-')
             . ':' . self::_fmt_date($ed, '-') . ':';
    }

    private static function _cdr_imei_zong($imeis, $sd, $ed)
    {
        // Zong: 15-digit IMEI, with the trailing "," before the colon kept
        // verbatim per the bulk-IMEI docx sample.
        $ids = array();
        foreach ($imeis as $im) {
            $d = preg_replace('/\D/', '', $im);
            $ids[] = (strlen($d) >= 15) ? substr($d, 0, 15) : $d;
        }
        return 'Tpi:' . implode(',', $ids) . ',:'
             . self::_fmt_date($sd, '-') . ':'
             . self::_fmt_date($ed, '-') . ':';
    }

    private static function _cdr_imei_ufone($imeis, $sd, $ed)
    {
        // Ufone wants 15-digit IMEIs with last digit forced to 0.
        $ids = array();
        foreach ($imeis as $im) {
            $d = preg_replace('/\D/', '', $im);
            if (strlen($d) >= 15) $d = substr($d, 0, 15);
            if (strlen($d) === 14) $d = $d . '0';
            if (strlen($d) === 15 && $d[14] !== '0') $d = substr($d, 0, 14) . '0';
            $ids[] = $d;
        }
        return 'IMEI|Both|' . self::_fmt_date($sd, '/', 'mdy')
             . '|'  . self::_fmt_date($ed, '/', 'mdy')
             . '|'  . implode(':', $ids);
    }

    /* ============================================================
       SIMs by CNIC builders
       ============================================================ */

    private static function _cnic_mobilink($cnics)
    {
        $lines = array('FIR NO ADM-[case_number]', '');
        foreach ($cnics as $c) {
            $lines[] = preg_replace('/\D/', '', $c);
        }
        return implode("\n", $lines);
    }

    private static function _cnic_telenor($cnics)
    {
        $ids = array();
        foreach ($cnics as $c) $ids[] = preg_replace('/\D/', '', $c);
        // TPC:<cnic1>,<cnic2>,: — note the trailing ',' before the final ':'
        // per the bulk-CNIC docx sample.
        return 'TPC:' . implode(',', $ids) . ',:';
    }

    private static function _cnic_zong($cnics)
    {
        $lines = array();
        foreach ($cnics as $c) $lines[] = preg_replace('/\D/', '', $c);
        return implode("\n", $lines);
    }

    /* ============================================================
       Internal helpers
       ============================================================ */

    /** Pull a list-of-strings from POST, accepting both array and CSV string. */
    private static function _array_of_strings(array $input, $key)
    {
        if (!isset($input[$key])) return array();
        $val = $input[$key];
        if (is_array($val)) {
            $out = array();
            foreach ($val as $v) {
                $v = trim((string) $v);
                if ($v !== '') $out[] = $v;
            }
            return array_values(array_unique($out));
        }
        // CSV / colon-separated fallback for clients that flatten the array.
        $parts = preg_split('/[,;:\s]+/', (string) $val);
        $out = array();
        foreach ($parts as $v) {
            $v = trim($v);
            if ($v !== '') $out[] = $v;
        }
        return array_values(array_unique($out));
    }

    /** Parse a 'mm/dd/yyyy' string into a DateTime, or NULL. */
    private static function _parse_mdy($value)
    {
        $value = trim((string) $value);
        if ($value === '') return null;
        // Accept mm/dd/yyyy or m/d/yyyy.
        if (!preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $value, $m)) return null;
        $month = (int) $m[1]; $day = (int) $m[2]; $year = (int) $m[3];
        if (!checkdate($month, $day, $year)) return null;
        $dt = DateTime::createFromFormat('!m/d/Y', sprintf('%d/%d/%d', $month, $day, $year));
        return $dt ?: null;
    }

    /** Format a DateTime as dd<sep>mm<sep>yyyy or mm/dd/yyyy when order='mdy'. */
    private static function _fmt_date($dt, $sep, $order = 'dmy')
    {
        if (!$dt instanceof DateTime) return '';
        $dd = $dt->format('d');
        $mm = $dt->format('m');
        $yy = $dt->format('Y');
        return ($order === 'mdy')
            ? $mm . $sep . $dd . $sep . $yy
            : $dd . $sep . $mm . $sep . $yy;
    }

    /** Normalise a Pakistani mobile number to '92xxxxxxxxxx' (12 digits). */
    private static function _to_msisdn92($num)
    {
        $d = preg_replace('/\D/', '', (string) $num);
        if (strlen($d) === 10 && $d[0] === '3')               return '92' . $d;
        if (strlen($d) === 11 && $d[0] === '0')               return '92' . substr($d, 1);
        if (strlen($d) === 12 && substr($d, 0, 2) === '92')   return $d;
        if (strlen($d) === 13 && substr($d, 0, 4) === '0092') return substr($d, 2);
        return $d;
    }

    /** True if a string looks like a Pakistani mobile MSISDN. */
    private static function _is_valid_msisdn($num)
    {
        $d = preg_replace('/\D/', '', (string) $num);
        return (strlen($d) === 10 && $d[0] === '3')
            || (strlen($d) === 11 && substr($d, 0, 2) === '03')
            || (strlen($d) === 12 && substr($d, 0, 2) === '92')
            || (strlen($d) === 13 && substr($d, 0, 4) === '0092');
    }

    /** Display name for a company id, or 'this telco' as fallback. */
    private static function _telco_name($company)
    {
        return isset(self::$TELCO_NAMES[$company])
            ? self::$TELCO_NAMES[$company]
            : 'this telco';
    }
}
