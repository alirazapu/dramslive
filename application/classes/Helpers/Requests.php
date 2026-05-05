<?php

abstract class Helpers_Requests {
    /* Request againt existing person  */

    public static function get_person_information($person_id) {
        $DB = Database::instance();
        $sql = "SELECT * from person where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $person_name = !empty($results->first_name) ? $results->first_name . " " . $results->last_name : "Un-known";
        $person_f_name = !empty($results->father_name) ? $results->father_name : "Un-known";
        $person_address = !empty($results->address) ? $results->address : "Un-known";
        $person_cnic = Helpers_Person::get_person_cnic($person_id);
        $person_profile_link = Helpers_Person::get_person_link($person_id);
        ?>
        <div > <h4><b>You Are Requesting For: </b></h4> </div> 
        <div class="col-sm-6 ">
            <ul class="todo-list">
                <li>
                    <a href="<?php echo $person_profile_link; ?>">
                        <span class="text-black"><b>Name:</b><?php echo $person_name; ?></span>
                        <span class="active">(View Profile)</span>
                    </a>
                </li>
                <li >
                    <span class="text-black"> <b>Father Name: </b><?php echo $person_f_name; ?> </span>
                </li>
            </ul>
        </div>
        <div class="col-sm-6 ">
            <ul class="todo-list">
                <li>
                    <span class="text-black"> <b>CNIC: </b>  <?php echo $person_cnic; ?> </span>

                </li>
                <li >
                    <span class="text-black"> <b>Address: </b><?php echo $person_address; ?></span>
                </li>
            </ul>
        </div>
        <hr class="style14 col-md-12">
        <?php
    }

    /* Request againt new person  */

    public static function get_new_person_information() {
        $html = '<div>
                    <h4><b>You Are Requesting For New Person:</b></h4>
                    <hr class="style14 col-md-12"> 
                 </div>';
        return $html;
    }

    /* Request exception */

    public static function get_exception_message() {
        $html = '<div>
                    <h4><b>Some thing went wrong</b></h4>
                    <hr class="style14 col-md-12"> 
                 </div>';
        return $html;
    }

    /* Request exception */

    public static function get_project_region_district($region_id, $district_id) {
        try {
                    
        $region_district = 'UnKnown';        
        $DB = Database::instance();
        if ($region_id == 11) {
            $region_district = '[Head Quarters]';
        } else {
            //get region from regions
            $sql = "SELECT t1.name as region_name FROM region as t1                     
                    where t1.region_id = {$region_id} LIMIT 1";                    
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
//            echo '<pre>';
//            print_r($results);
//            exit;
            $region = !empty($results->region_name) ? '[Region-'. $results->region_name.'] ' : '';
            if ($district_id < 900 && $district_id != 100) {
                            //get district form district
            $sql = "SELECT t1.name as district_name FROM district as t1                     
                    where t1.district_id = {$district_id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $district = !empty($results->district_name) ? '[District-'.$results->district_name.']' : '';
            }elseif ($district_id == 100) {
                $district = '[District-Self]' ;
            }
            else{
            //get police station form district
            $sql = "SELECT t1.name as ps_name FROM ctd_police_station as t1                     
                    where t1.id = {$district_id} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $district = !empty($results->ps_name) ? '[PS-'.$results->ps_name.']' : '';
            }
            $region_district =  $region . $district ;
        }
        } catch (Exception $ex) {
            Model_ErrorLog::log(
                'get_project_region_district',
                'Failed to get region/district info: ' . $ex->getMessage(),
                [
                    'region_id' => $region_id,
                    'district_id' => $district_id
                ],
                $ex->getTraceAsString(),
                'database_query_error',
                'region_district_fetch',
                'error'
            );
            echo '<pre>';
            print_r($ex);
            exit;   
        }
        return $region_district;
    }
    
    /**
     * Return the rows from `files` for every CDR request placed against the
     * given SIM. Used by the Person dashboard's "Download" button to render
     * the list of available CDR archives in the modal.
     *
     * Historical bug: the previous implementation filtered with
     *   `files.data_from_date <> 'null'`
     * which compared the datetime column against the literal *string* 'null'.
     * MariaDB silently casts 'null' to a datetime, where it becomes
     * '0000-00-00 00:00:00'. Every CDR file in the system has
     * data_from_date = '0000-00-00 00:00:00' (the parser never populates it),
     * so the predicate evaluated FALSE for every row and the modal always
     * showed "No files found" — even when downloadable archives existed.
     *
     * The fix: drop that filter entirely (it never did anything useful),
     * switch the LEFT JOIN to INNER JOIN so we only return rows that
     * actually have a file row to download, and exclude soft-deleted files.
     * Per product requirement, no extra permission gating is applied — if
     * a downloadable file exists for the SIM, it shows up.
     *
     * @param string $sim         The mobile number (requested_value in user_request).
     * @param int    $type        user_request.user_request_type_id (1 = CDR by mobile).
     * @param string $classifier  Free-form tag from the caller (e.g. 'callcdr').
     *                            Accepted for forward-compat / call-site clarity;
     *                            not currently used to alter the query.
     */
    public static function get_requests_by_sim($sim, $type = 1, $classifier = '')
    {
        if (empty($sim)) {
            return [];
        }
        // We pull files.* plus the requested-data window from user_request
        // (startDate / endDate). The files table's own data_from_date /
        // data_to_date are never populated by the parser ('0000-00-00'),
        // so they're useless for display. The user_request row, in
        // contrast, records exactly the range the analyst asked for —
        // which IS the range of data inside the downloaded archive.
        // Aliased to request_start_date / request_end_date so they don't
        // collide with the files columns the JS modal already references.
        $db = Database::instance();
        $query = DB::select(
                'files.*',
                array('user_request.startDate', 'request_start_date'),
                array('user_request.endDate',   'request_end_date')
            )
            ->from('user_request')
            ->join('files', 'INNER')
            ->on('files.request_id', '=', 'user_request.request_id')
            ->where('user_request.requested_value', '=', $sim)
            ->and_where('user_request.user_request_type_id', '=', $type)
            ->and_where('files.is_deleted', '=', 0)
            ->order_by('files.id', 'DESC')
            ->execute($db)
            ->as_array();
        return $query;
    }
}
?>
