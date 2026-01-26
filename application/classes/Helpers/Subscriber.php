<?php defined('SYSPATH') or die('No direct script access.');

class Helpers_Subscriber
{
    public static function search($search_type, $search_value)
    {
        // Allowed columns
        $allowed = array('msisdn', 'cnic', 'imsi', 'foreigner_profile');

        if (!in_array($search_type, $allowed)) {
            return array(
                'status' => false,
                'error'  => 'Invalid search type'
            );
        }

        // Normalize phone numbers if searching msisdn
        $search_values = array($search_value);

        if ($search_type === 'msisdn') {
            $val = preg_replace('/[^0-9]/', '', $search_value); // remove non-digits
            // variants
            if (strlen($val) === 10 && substr($val, 0, 1) === '3') {
                $search_values = array(
                    $val,                    // 3100910677
                    '0' . $val,              // 03100910677
                    '92' . $val,             // 923100910677
                    '+92' . $val             // +923100910677 (optional)
                );
            } elseif (strlen($val) === 11 && substr($val, 0, 1) === '0') {
                $v = substr($val, 1);
                $search_values = array(
                    $val,                    // 03100910677
                    $v,                      // 3100910677
                    '92' . $v,               // 923100910677
                    '+92' . $v               // +923100910677
                );
            } elseif (substr($val, 0, 2) === '92') {
                $v = substr($val, 2);
                $search_values = array(
                    $val,                    // 923100910677
                    $v,                      // 3100910677
                    '0' . $v,                // 03100910677
                    '+92' . $v               // +923100910677
                );
            }
        }

        // Build query
        $query = DB::select('*')
            ->from('subscribers_main');

        if ($search_type === 'msisdn') {
            $query->where_open();
            foreach ($search_values as $val) {
                $query->or_where('msisdn', '=', $val);
            }
            $query->where_close();
        } else {
            $query->where($search_type, '=', $search_value);
        }

        // Limit 1 for now
        $query->limit(1);

        // Execute on mobile DB
        $result = $query->execute('mobile');

        $row = $result->current();

        if (!$row) {
            return array(
                'status' => false,
                'error'  => 'No subscriber found'
            );
        }

        return array(
            'status' => true,
            'data'   => $row
        );
    }


public static function searchForeignerAccount($search_type, $search_value)
{
    // Map logical search types to DB columns
    $column_map = array(
        'foreigner_profile' => 'master_acc_number',
        'foreign_cnic'      => 'foreign_cnic',
        'master_acc_number' => 'master_acc_number'
    );

    if (!isset($column_map[$search_type])) {
        return array(
            'status' => false,
            'error'  => 'Invalid search type'
        );
    }

    $column = $column_map[$search_type];

    $query = DB::select('*')
        ->from('afghan_accounts')
        ->where($column, '=', $search_value)
        ->limit(1);

    $result = $query->execute('mobile');
    $row    = $result->current();

    if (!$row) {
        return array(
            'status' => false,
            'error'  => 'No foreigner account found'
        );
    }

    return array(
        'status' => true,
        'data'   => $row
    );
}


}
