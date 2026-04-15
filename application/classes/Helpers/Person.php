<?php

defined('SYSPATH') OR die('No direct script access.');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class Helpers_Person
{
    /*
     * CDR Merge
     */

    public static function cdr_graph_merge($array_1 = NULL, $array_2 = NULL, $array_3 = NULL, $array_4 = NULL, $array_5 = NULL)
    {
        if (in_array($array_1->phone_number, array_column($array_2, 'phone_number')) && in_array($array_1->other_person_phone_number, array_column($array_2, 'other_person_phone_number'))) {

        }

        /*
          if ((in_array($array_1->phone_number, array_column($array_2, 'phone_number')) && in_array($r->other_person_phone_number, array_column($second_record, 'other_person_phone_number'))) || in_array($r1->phone_number, array_column($second_record, 'phone_number')) && in_array($r1->other_person_phone_number, array_column($second_record, 'other_person_phone_number'))) {

          echo $r->phone_number;
          echo '<br>';
          //array_search('phone_number'=>$r->phone_number, $link)
          echo $r->other_person_phone_number;
          exit;
          } else {
          return $r1;
          } */
    }

    /*
     *  Get the person name of the given id
     *
     * @param int $user_ud
     * 
     * return self
     */

    public static function get_person_name($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT CONCAT_WS(' ',T1.first_name, T1.middle_name, T1.last_name) as name
                         from person AS T1";

        if (!empty($person_id)) {
            $sql .= " WHERE T1.person_id= $person_id";
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        $name = isset($results->name) && !empty($results->name) ? $results->name : "Unknown";
        //echo $name; exit;
        return $name;
    }
    public static function get_last_activity($a_number, $b_number, $person_id)
    {
        $DB = Database::instance();
        $sql = "select duration_in_seconds, latitude, longitude, address, call_at, call_end_at from person_call_log pcl
        where person_id = {$person_id}
        and phone_number = {$a_number}
        and other_person_phone_number = {$b_number}
        ORDER by call_at  DESC 
        LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }
    public static function get_last_activity_sms($a_number, $b_number, $person_id)
    {
        $DB = Database::instance();
        $sql = "select latitude, longitude, address, sms_at 
            from person_sms_log pcl
        where person_id = {$person_id}
        and phone_number = {$a_number}
        and other_person_phone_number = {$b_number}
        ORDER by sms_at DESC 
        LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }

    /* get person father name by person id */

    public static function get_person_father_name($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT father_name as fname
                         from person AS T1";
        if (!empty($person_id)) {
            $sql .= " WHERE T1.person_id= $person_id";
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $fname = isset($results->fname) && !empty($results->fname) ? $results->fname : "Unknown";
        return $fname;
    }

    /* get number's short code detail */

    public static function get_short_code_name($ph_num = NULL)
    {

        $DB = Database::instance();
        $sql = "SELECT company_name as c_name
                         from telco_short_code AS T1";
//        echo '<pre>';
//        print_r(is_string($ph_num));
//        exit();


        if (!empty($ph_num)) {
            $sql .= " WHERE T1.code= $ph_num";
         }

        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        $c_name= isset($results->c_name) && !empty($results->c_name) ? $results->c_name :'';

        return $c_name;
    }

    public static function get_person_view_count($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT view_count as view_count
                         from person AS T1";
        if (!empty($person_id)) {
            $sql .= " WHERE T1.person_id= $person_id";
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $view_count = isset($results->view_count) && !empty($results->view_count) ? $results->view_count : 0;
        return $view_count;
    }

    /* get person cnic by person id */

    public static function get_person_cnic($person_id = NULL)
    {
        $cnic = '';
        $DB = Database::instance();
        $sql = "SELECT cnic_number,cnic_number_foreigner 
                         from person_initiate AS T1";
        if (!empty($person_id)) {
            $sql .= " WHERE T1.person_id= $person_id";
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $cnic_local = isset($results->cnic_number) && !empty($results->cnic_number) ? $results->cnic_number : "";
        $cnic_foreigner = !empty($results->cnic_number_foreigner) ? $results->cnic_number_foreigner : "";
        if (!empty($cnic_local)) {
            $cnic = $cnic_local;
        } else {
            $cnic = $cnic_foreigner;
        }
        return $cnic;
    }

    private static function normalize_cnic_for_external_sources($cnic = '')
    {
        return preg_replace('/\D+/', '', (string)$cnic);
    }

    private static function format_cnic_with_dashes($cnic = '')
    {
        $cnic = self::normalize_cnic_for_external_sources($cnic);
        if (strlen($cnic) !== 13) {
            return $cnic;
        }
        return substr($cnic, 0, 5) . '-' . substr($cnic, 5, 7) . '-' . substr($cnic, 12);
    }

    public static function get_person_external_profile_ctd_kpk($cnic = '')
    {
        try {
            $DB = Database::instance('ctd_kpk');
            $cnic_digits = self::normalize_cnic_for_external_sources($cnic);
            if (empty($cnic_digits)) {
                return NULL;
            }

            $cnic_dash = self::format_cnic_with_dashes($cnic_digits);
            $cnic_digits_esc = $DB->escape($cnic_digits);
            $cnic_dash_esc = $DB->escape($cnic_dash);

            $sql = "SELECT dct_person_profile.*,
                    sb_district.DistrictName, sb_tehsil.TehsilName,
                    sb_religion.ReligionTitle AS ReligionName, sects_details.SectTitle AS SectName,
                    caste.Caste AS CasteName,
                    perm_province.ProvinceName AS PermAdrProvinceName,
                    perm_country.CountryName AS PermAdrCountryName,
                    perm_city.CityName AS PermAdrCityName,
                    perm_ps.PoliceStationName AS AdrPoliceStationName,
                    curr_district.DistrictName AS CurrAdrDistrictName,
                    curr_tehsil.TehsilName AS CurrAdrTehsilName,
                    curr_province.ProvinceName AS CurrAdrProvinceName,
                    curr_country.CountryName AS CurrAdrCountryName,
                    curr_city.CityName AS CurrAdrCityName,
                    curr_ps.PoliceStationName AS CurrAdrPoliceStationName
                FROM dct_person_profile
                LEFT JOIN sb_district ON sb_district.DistrictId = dct_person_profile.PermAdrDistrict
                LEFT JOIN sb_tehsil ON sb_tehsil.TehsilId = dct_person_profile.PermAdrTehsil
                LEFT JOIN sb_religion ON sb_religion.ReligionID = dct_person_profile.Religion
                LEFT JOIN sects_details ON sects_details.SectID = dct_person_profile.Sect
                LEFT JOIN caste ON caste.CasteID = dct_person_profile.Caste
                LEFT JOIN sb_province AS perm_province ON perm_province.ProvinceId = dct_person_profile.PermAdrProvince
                LEFT JOIN sb_country AS perm_country ON perm_country.CountryID = dct_person_profile.PermAdrCountry
                LEFT JOIN sb_city AS perm_city ON perm_city.CityId = dct_person_profile.PermAdrCity
                LEFT JOIN police_stations AS perm_ps ON perm_ps.PoliceStationId = dct_person_profile.AdrPoliceStation
                LEFT JOIN sb_district AS curr_district ON curr_district.DistrictId = dct_person_profile.CurrAdrDistrict
                LEFT JOIN sb_tehsil AS curr_tehsil ON curr_tehsil.TehsilId = dct_person_profile.CurrAdrTehsil
                LEFT JOIN sb_province AS curr_province ON curr_province.ProvinceId = dct_person_profile.CurrAdrProvince
                LEFT JOIN sb_country AS curr_country ON curr_country.CountryID = dct_person_profile.CurrAdrCountry
                LEFT JOIN sb_city AS curr_city ON curr_city.CityId = dct_person_profile.CurrAdrCity
                LEFT JOIN police_stations AS curr_ps ON curr_ps.PoliceStationId = dct_person_profile.CurrAdrPoliceStation
                WHERE dct_person_profile.CNIC = {$cnic_digits_esc}
                   OR dct_person_profile.CNIC = {$cnic_dash_esc}
                LIMIT 1";

            return $DB->query(Database::SELECT, $sql, TRUE)->current();
        } catch (Exception $e) {
            return NULL;
        }
    }

    public static function get_person_external_profile_driving_license($cnic = '')
    {
        try {
            $DB = Database::instance('dlms_sqlsrv');
            $cnic_digits = self::normalize_cnic_for_external_sources($cnic);
            if (empty($cnic_digits)) {
                return NULL;
            }

            $cnic_dash = self::format_cnic_with_dashes($cnic_digits);
            $cnic_digits_esc = $DB->escape($cnic_digits);
            $cnic_dash_esc = $DB->escape($cnic_dash);

            $sql = "SELECT TOP (1)
                    p.PersonID,
                    p.FirstName,
                    p.MiddleName,
                    p.LastName,
                    p.FatherFName AS FatherName,
                    p.FatherMName,
                    p.FatherLName,
                    p.DOB,
                    p.BirthPlace,
                    p.Gender,
                    p.Mobile,
                    p.EntryDate,
                    i.imgObject,
                    ld.LicenseNo,
                    ld.EntryDate AS LicenseEntryDate,
                    ld.ExpiryDate AS LicenseExpiryDate
                FROM License_Person AS p
                LEFT JOIN License_Details AS ld
                    ON ld.PersonID = p.PersonID
                OUTER APPLY (
                    SELECT TOP 1 imgObject
                    FROM License_Person_Images
                    WHERE PersonID = p.PersonID
                      AND Category = 'Photograph'
                ) AS i
                WHERE p.CNIC = {$cnic_digits_esc} OR p.CNIC = {$cnic_dash_esc}
                ORDER BY ld.EntryDate DESC, p.EntryDate DESC";

            return $DB->query(Database::SELECT, $sql, TRUE)->current();
        } catch (Exception $e) {
            return NULL;
        }
    }

    public static function get_person_external_profile_ecp($cnic = '')
    {
        try {
            $DB = Database::instance('ecp');
            $cnic_digits = self::normalize_cnic_for_external_sources($cnic);
            if (empty($cnic_digits)) {
                return NULL;
            }

            $cnic_esc = $DB->escape($cnic_digits);
            $sql = "SELECT p.id, p.cnic, p.age, p.gender, p.father_text, p.name_text, p.address_text, p.code,
                        p.family_number, p.file_name, p.folder_name, p.uc_block_code, p.address_image_base64,
                        p.father_image_base64, p.name_image_base64,
                        (SELECT GROUP_CONCAT(n.number ORDER BY n.number SEPARATOR ', ')
                         FROM ecp_person_numbers n
                         WHERE n.ecp_person_id = p.id) AS linked_numbers
                    FROM ecp_persons p
                    WHERE p.cnic = {$cnic_esc}
                    LIMIT 1";

            return $DB->query(Database::SELECT, $sql, TRUE)->current();
        } catch (Exception $e) {
            return NULL;
        }
    }

    public static function get_person_external_profile_employee($cnic = '')
    {
        try {
            $DB = Database::instance('govt_emp_data');
            $cnic_digits = self::normalize_cnic_for_external_sources($cnic);
            if (empty($cnic_digits)) {
                return array();
            }
            $cnic_esc = $DB->escape($cnic_digits);
            $sql = "SELECT pers_no, first_name, last_name, father_husband_name, position, job, job_title,
                        cost_ctr, description, national_id, org_unit, org_unit_short_text,
                        personnel_area, employee_group, employee_subgroup
                    FROM employee_data
                    WHERE national_id = {$cnic_esc}
                    LIMIT 10";

            return $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        } catch (Exception $e) {
            return array();
        }
    }

    /* get person url link by person id */
    public static function get_person_link($person_id)
    {

        return URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id, "encrypt"));
    }

    /* get person cnic by person id */

    //  public static function get_person_id_against_cnic($cnic = NULL) {
//        $DB = Database::instance();
//        $sql = "SELECT person_id as id
//                         from person AS T1";
//        if (!empty($cnic)) {
//            $sql .= " WHERE T1.cnic_number= $cnic";
//        }
//        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
//        $id = isset($results->id) && !empty($results->id) ? $results->id : '';
//        return $id;
    // }

    /*
     *  Get Person Profile Detail 
     */

    public static function get_person_perofile($person_id)
    {
        $DB = Database::instance();
        $query_result = '';
        $sql = "SELECT * from person where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $query_result = $results;
        $cnic_number = Helpers_Person::get_person_cnic($person_id);
        $query_result->cnic_number = Helpers_Person::get_person_cnic($person_id);
        $sql = "SELECT person_photo_url as image_url from person_nadra_profile
                             where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        $query_result->image_url = !empty($results) ? $results->image_url : '';
        return $query_result;
    }
public static function get_person_for_dashboard_perofile($person_id)
    {
        $DB = Database::instance();
        $query_result = '';
        $sql = "SELECT * from person where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, false)->current();
        $query_result = $results;
        $cnic_number = Helpers_Person::get_person_cnic($person_id);
        $query_result['cnic_number'] = $cnic_number;
        $sql = "SELECT person_photo_url as image_url from person_nadra_profile
                             where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        $query_result['image_url'] = !empty($results) ? $results->image_url : '';
        return $query_result;
    }

    /*
     *  Get Person Nadra Profile Detail 
     */

    public static function get_person_nadra_perofile($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT *
                             from person_nadra_profile
                             where person_id = {$person_id}";

        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }
    /*
     *  Get Person short code Detail
     */

    public static function shortcode_count($code,$pid)
    {

        $DB = Database::instance();
        $sql = "SELECT * , (sms_received_count + sms_sent_count) as tsms,
                          (calls_received_count + calls_made_count) as tcalls
                         FROM person_summary
                         WHERE person_id= {$pid} and other_person_phone_number= {$code} limit 1";
//echo '<pre>';
//print_r($sql);
//exit();
        $results = $DB->query(Database::SELECT, $sql, true)->as_array();


        return $results;
    }

    /*
     *  Get Person Foreginer Profile Detail 
     */

    public static function get_person_foreigner_perofile($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT *
                             from person_foreigner_profile
                             where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    /*
     *  Get Person Detail Info
     */

    public static function get_person_detail_info($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT *
                             from person_detail_info
                             where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    /*
     *  Get Person Profile Detail with cnic 
     */

    public static function get_person_perofile_with_cnic($cnic)
    {
        $DB = Database::instance();
        $sql = "SELECT *
                             from person as t1
                             join person_initiate as pi on t1.person_id=pi.person_id
                             where pi.cnic_number = {$cnic} OR pi.cnic_number_foreigner = '{$cnic}'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    /*
     *  Get Person assets url data
     */

    public static function get_person_assets_url_data($pid)
    {
        $DB = Database::instance();
        $sql = "SELECT *
                             from person_assets_url as t1
                             where t1.person_id = {$pid} LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    /*
     *  Get Person assets save data path
     */

    public static function get_person_save_data_path($pid)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.person_save_data_path
                             from person_assets_url as t1
                             where t1.person_id = {$pid} LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $person_folder_path = !empty($results->person_save_data_path) ? $results->person_save_data_path : '';

        if (!is_dir($person_folder_path)) {
            $person_folder_path = !empty($pid) ? Helpers_Upload::make_and_get_person_data_directory($pid) : '';
        }
        return $person_folder_path;
    }

    /*
     *  Get Person assets download path
     */

    public static function get_person_download_data_path($pid)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.person_download_data_path,t1.server_name
                             from person_assets_url as t1
                             where t1.person_id = {$pid} LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $data_path = !empty($results->person_download_data_path) ? $results->person_download_data_path : '';
        $server = !empty($results->server_name) ? $results->server_name : '';

        return $server . $data_path;
    }

    /*
     *  Get Person id with mobile 
     */

    public static function get_mobile_userid_with_mobilenumber($msisdn)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.person_id as sim_user_id
                             from person_phone_number as t1
                             where t1.phone_number = {$msisdn} LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $simuserid = isset($results->sim_user_id) && !empty($results->sim_user_id) ? $results->sim_user_id : -1;
        return $simuserid;
    }

    /*
     *  Get Person id with mobile 
     */

    public static function get_mobile_ownerid_with_mobilenumber($msisdn)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.sim_owner as sim_owner_id
                             from person_phone_number as t1
                             where t1.phone_number = {$msisdn} LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $simownerid = isset($results->sim_owner_id) && !empty($results->sim_owner_id) ? $results->sim_owner_id : -1;
        return $simownerid;
    }

    /* NADRA COUNTER */

    public static function update_nadra_api_count($region_id, $date)
    {
        if ($region_id == 0)
            $region_id = 11;
        $query = "select id, count
                FROM `nadra_profile_stats` 
                where region_id = {$region_id} and date = '{$date}'";
        $sql = DB::query(Database::SELECT, $query);
        $result = $sql->execute()->current();
        if (!empty($result['id'])) {
            //$query = DB::update('nadra_profile_stats')->set(array('count' => DB::expr('count' + 1)))            
            $query = DB::update('nadra_profile_stats')->set(array('count' => $result['count'] + 1))
                ->where('id', '=', $result['id'])
                //->where('region_id', '=', $region_id) // ->and_where('date', '=', $date)
                ->execute();
        } else {
            $query = DB::insert('nadra_profile_stats', array('region_id', 'date', 'count'))
                ->values(array($region_id, $date, 1))
                ->execute();
        }
    }

    /*    get  Person identity type */

    public static function get_person_identity_type($identityid = -7)
    {
        $DB = Database::instance();
        $sql = "SELECT * FROM  lu_identity";
        if (!empty($identityid) && $identityid != -7) {
            $sql .= " WHERE id = {$identityid} LIMIT 1";
            $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $data = !empty($results) ? $results->identity : 'Unknown';
        } else {
            $data = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        }
        return $data;
    }

    /*    get case accused postion type */

    public static function get_case_accused_position($id = NULL)
    {
        $array[1] = 'Under Investigation';
        $array[2] = 'Under Trial';
        $array[3] = 'Convicted';
        $array[4] = 'Discharged';
        $array[5] = 'Acquitted';
        $array[6] = 'PO';
        $array[7] = 'On Bail';
        if (!empty($id)) {
            return $array[$id];
        } else {
            return $array;
        }
    }

    /*    get  Most Searched Person records */

    public static function get_most_searched_person()
    {
        $DB = Database::instance();
        $sql = "SELECT person_id AS PID FROM person ORDER BY view_count DESC LIMIT 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    //Increment Person Profile view Count
    public static function increament_person_view_count($person_id)
    {
        $DB = Database::instance();
        //$result = DB::update("person")->set(array('view_count' =>'view_count' + 1))->where('person_id', '=', $person_id)->execute();

        $sql = "UPDATE person set view_count = view_count + 1
                where person_id  = $person_id";

        $result = $DB->query(Database::UPDATE, $sql, TRUE);

        return $result;
    }

//    Get the person name of the given id
    public static function get_person_address($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT address
                         from person AS T1";
        if (!empty($person_id)) {
            $sql .= " WHERE T1.person_id= $person_id";
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $address = isset($results->address) && !empty($results->address) ? $results->address : "Unknown";
        return $address;
    }

    //    Get the person relation type with relation id
    public static function get_person_relation_type($relation_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT *
                         from lu_relation_type AS T1";
        if (!empty($relation_id)) {
            $sql .= " WHERE T1.id= $relation_id";
            $relation = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $relation = !empty($relation->relation_name) ? $relation->relation_name : 'Unknown';

            return $relation;
        }
        $relation = $DB->query(Database::SELECT, $sql, TRUE);
        return $relation;
    }

//    Get the Total SIMs of person against the given id
    public static function get_person_SIMs($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.phone_number) AS SIMS
                FROM person_phone_number AS t1";
        if (!empty($person_id)) {
            $sql .= " WHERE t1.sim_owner= $person_id OR t1.person_id=$person_id ";
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $SIMS = isset($results->SIMS) && !empty($results->SIMS) ? $results->SIMS : 0;
        return $SIMS;
    }

//    Get person affiliations
    public static function get_person_affiliations($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.organization_id as org_id1
                FROM person_affiliations AS t1 ";
        if (!empty($person_id)) {
            $sql .= " where t1.person_id=$person_id order by t1.id DESC";
        }
        $results = DB::query(Database::SELECT, $sql)->execute()->as_array();
        return $results;
    }

        //    Get person affiliation
        public static function get_person_affiliation($person_id = NULL)
    {
        $results='';
        if (!empty($person_id)) {
            $DB = Database::instance();
            $sql = "SELECT t1.organization_id as org_id1
                FROM person_affiliations AS t1 ";

            $sql .= " where t1.person_id=$person_id order by t1.id DESC";


            $results = DB::query(Database::SELECT, $sql)->execute()->as_array();
        }

        return $results;

    }
        //    Get person affiliation
        public static function get_project_affiliation($project_id = NULL)
    {
        $results='';
        if (!empty($project_id)) {
            $DB = Database::instance();
            $sql = "SELECT t1.org_id as org_id1
                FROM int_projects_organizations AS t1 ";

            $sql .= " where t1.project_id=$project_id order by t1.project_id DESC";


            $results = DB::query(Database::SELECT, $sql)->execute()->as_array();
        }

        return $results;

    }

//    Get person social links
    public static function get_person_social_links($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.person_sw_id,t1.phone_number,t2.website_name
                FROM person_social_links AS t1 
                inner join social_websites AS t2 on t1.sw_type_id=t2.id";
        if (!empty($person_id)) {
            $sql .= " where t1.person_id=$person_id and is_deleted = 0 order by t1.sw_type_id DESC";
        }
        $results = DB::query(Database::SELECT, $sql)->execute()->as_array();
        return $results;
    }

    //    Check person mobile number exist or not
    public static function check_person_mobile_number_exist($number)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.phone_number) AS SIMS
                FROM person_phone_number AS t1
                WHERE t1.phone_number= $number";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        $SIMS = !empty($results->SIMS) ? $results->SIMS : 0;
        return $SIMS;
    }

    //    Check person mobile number exist or not
    public static function check_person_other_number_exist($number)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.phone_number) AS othernumber
                FROM other_numbers AS t1
                WHERE t1.phone_number= $number";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $othernumbers = isset($results->othernumber) && !empty($results->othernumber) ? $results->othernumber : 0;
        return $othernumbers;
    }

    //    Check person mobile number exist or not
    public static function check_person_assets_url_exist($pid)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS cnt
                FROM person_assets_url AS t1
                WHERE t1.person_id= $pid";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $count = isset($results->cnt) && !empty($results->cnt) ? $results->cnt : 0;
        return $count;
    }

    //    Check person mobile number exist or not
    public static function check_person_detail_exist($pid = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS SIMS
                FROM person_detail_info AS t1
                WHERE t1.person_id= $pid";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $SIMS = isset($results->SIMS) && !empty($results->SIMS) ? $results->SIMS : 0;
        return $SIMS;
    }

    //    Check person nadra profile exist
    public static function check_person_nadra_profile_exist($pid = NULL)
    {
        $is_foreigner = Helpers_Utilities::check_is_foreigner($pid);
        if (empty($is_foreigner)) {
            $sub_query = "FROM person_nadra_profile AS t1";
        } else {
            $sub_query = "FROM person_foreigner_profile AS t1";
        }
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS SIMS
        {$sub_query}
                WHERE t1.person_id= $pid";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $SIMS = isset($results->SIMS) && !empty($results->SIMS) ? $results->SIMS : 0;
        return $SIMS;
    }

    //    Check person cnic exist or not against CNIC
    public static function check_person_id_with_cnic($cnic)
    {
        $cnic = ltrim($cnic);
        $cnic = rtrim($cnic);
        if (ctype_digit($cnic)) {
            $subquery = "WHERE cnic_number= {$cnic} Limit 1";
        } else {
            $subquery = "WHERE cnic_number_foreigner= '{$cnic}' Limit 1";
        }
        $DB = Database::instance();
        $sql = "SELECT person_id
                FROM person_initiate
                {$subquery}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->person_id) && !empty($results->person_id) ? $results->person_id : 0;
        return $chk;
    }

    //    Check person relation exist or not against CNIC
    public static function check_person_relation_exist($relwith, $pid)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.relation_with) AS chk
                FROM person_relations AS t1
                WHERE t1.relation_with= $relwith AND t1.person_id=$pid";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    //    Check person identity exist or not against person_id and identity no
    public static function check_person_identity_exist($idnname, $pid)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS chk
                FROM person_identities AS t1
                WHERE t1.person_id= $pid AND t1.identity_no='{$idnname}'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    //    Check person education exist or not against person_id and degree name
    public static function check_person_education_exist($degname, $pid)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS chk
                FROM person_education AS t1
                WHERE t1.person_id= $pid AND t1.degree_name='$degname'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    //    Check person education exist or not against person_id and degree name
    public static function check_person_picture_exist($pic_type, $pid)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS chk
                FROM person_pictures AS t1
                WHERE t1.person_id= $pid AND t1.picture_type={$pic_type}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    //    Check person education exist or not against person_id and degree name
    public static function check_person_account_exist($account, $pid)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS chk
                FROM person_banks AS t1
                WHERE t1.person_id= $pid AND t1.account_number='$account'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    //    Check person education exist or not against person_id and degree name
    public static function check_person_criminal_record_exist($fir, $policest, $firdate, $pid)
    {

        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS chk
                FROM person_criminal_record AS t1
                WHERE t1.person_id= $pid AND t1.fir_number=$fir AND t1.police_station_id=$policest AND t1.fir_date='$firdate'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    //    Check person affiliation record exist
    public static function check_person_affiliations_record_exist($org, $pid)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS chk
                FROM person_affiliations AS t1
                WHERE t1.person_id= $pid AND t1.organization_id='$org'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    //    Check person affiliation count
    public static function check_person_affiliations($pid)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS count
                FROM person_affiliations AS t1
                WHERE t1.person_id= {$pid}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        //print_r($results); exit;
        $chk = (isset($results->count) && $results->count > 0) ? $results->count : 0;
        return $chk;
    }
    //    get person organizations
    public static function get_person_organizations($org_ids)
    {
        $DB = Database::instance();
        $sql = "SELECT org_name
                FROM banned_organizations AS t1
                WHERE t1.org_id in ($org_ids)";

        $results = $DB->query(Database::SELECT, $sql, false)->as_array();
        if(!empty($results)){
            $values = array_map('array_pop', $results);
            $chk = implode(',', $values);
        }
//        $chk = (isset($results->org_name)) ? $results->org_name : 'NA';
        return $chk;
    }
    //    get person organizations
    public static function get_project_organizations($project_org_ids)
    {
        $chk = '';
        $DB = Database::instance();
        $sql = "SELECT org_name
                FROM banned_organizations AS t1
                WHERE t1.org_id in ($project_org_ids)";

        $results = $DB->query(Database::SELECT, $sql, false)->as_array();
        if(!empty($results)){
            $values = array_map('array_pop', $results);
            $chk = implode(',', $values);
        }
//        $chk = (isset($results->org_name)) ? $results->org_name : 'NA';
        return $chk;
    }

    //    Check person report exist or not against person_id and report type
    public static function check_person_reports_record_exist($rep, $no, $pid)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS chk
                FROM person_reports AS t1
                WHERE t1.person_id= $pid AND t1.report_type='$rep' AND t1.report_reference_no='$no'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    //    Check person income source exist or not against person_id and income source name type
    public static function check_person_income_source_record_exist($sname, $pid)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS chk
                FROM person_income_sources AS t1
                WHERE t1.person_id= $pid AND t1.income_source_name='$sname' ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    //    Check person income source exist or not against person_id and income source name type
    public static function check_person_assets_record_exist($sname, $pid)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.person_id) AS chk
                FROM person_assets AS t1
                WHERE t1.person_id= $pid AND t1.asset_name='$sname' ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $chk = isset($results->chk) && $results->chk ? $results->chk : 0;
        return $chk;
    }

    //    Get the Total SIMs of person against the given id
    public static function get_person_total_SIMs($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT *
                FROM person_phone_number AS t1";
        if (!empty($person_id)) {
            $sql .= " WHERE t1.person_id= $person_id OR t1.sim_owner=$person_id ";
            //OR t1.sim_owner=$person_id 
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    //    Get the Total other numbers of person
    public static function get_person_total_ptcl_numbers($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT * FROM other_numbers AS t1 where mnc = 11 and t1.person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    //    Get the Total other numbers of person
    public static function get_person_total_international_numbers($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT * FROM other_numbers AS t1 where mnc = 12 and t1.person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    //    Get the Total SIMs in personal user of person against the given id
    public static function get_person_inuse_SIMs($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT *
                FROM person_phone_number AS t1";
        if (!empty($person_id)) {
            $sql .= " WHERE t1.person_id= $person_id ";
            //OR t1.sim_owner=$person_id 
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    //    Get the Total Other person Phone number  of person against the given id and phone number
    public static function get_person_total_bparty($person_id, $phone_number = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.other_person_phone_number as ophone
                FROM person_summary AS t1
                Where t1.person_id = $person_id";
        if (!empty($phone_number)) {
            $sql .= " AND t1.phone_number= {$phone_number}";
        }
        $results = DB::query(Database::SELECT, $sql)->as_object()->execute();
        //$results = $DB->query(Database::SELECT, $sql, True)->execute();      
        return $results;
    }

    //    Get the Total Calls to other person with given phone number and phone number
    public static function get_person_total_calls_with_number($person_id, $phone_number)
    {
        $DB = Database::instance();
        $sql = "SELECT sum(p1.calls_made_count + p1.calls_received_count) as calls
                FROM person_summary AS p1
                Where p1.person_id = $person_id
                and p1.other_person_phone_number like '%$phone_number%'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $calls = isset($results->calls) && !empty($results->calls) ? $results->calls : 0;
        //$results = $DB->query(Database::SELECT, $sql, True)->execute();      
        return $calls;
    }

    //    Get the Total sms to other person with given phone number and phone number
    public static function get_person_total_sms_with_number($person_id, $phone_number)
    {
        $DB = Database::instance();
        $sql = "SELECT sum(p1.sms_sent_count + p1.sms_received_count) as sms
                FROM person_summary AS p1
                Where p1.person_id = $person_id
                and p1.other_person_phone_number like '%$phone_number%'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $sms = isset($results->sms) && !empty($results->sms) ? $results->sms : 0;
        //$results = $DB->query(Database::SELECT, $sql, True)->execute();      
        return $sms;
    }

    //    Get the Total Devices of person against the given id
    public static function get_person_devices($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.phone_name, t1.imei_number,
                    MIN(t2.first_use) as in_use_since,
                    MAX(t2.last_use) as last_interaction_at,
                    t2.phone_number, t1.id as device_id, t1.person_id 
                FROM person_phone_device t1 
                INNER JOIN person_device_numbers t2 ON t1.id = t2.device_id 
                INNER JOIN person_phone_number as t3 ON t2.phone_number = t3.phone_number 
                WHERE t3.person_id=$person_id
                GROUP BY t1.id, t2.phone_number, t1.phone_name, t1.imei_number, t1.person_id";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    //    Get the Total Devices of person against the given id
    public static function get_link_with_project_last_five($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.user_id,t1.request_time,t2.email_type_name,t1.project_id,requested_value
                    FROM person_linked_projects as t1 
                    inner join email_templates_type as t2 on t1.request_type_id=t2.id
                    WHERE t1.person_id={$person_id}
                    order by t1.request_time desc
                    limit 5";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }
    //    Get the Total projects links of person against the given id
    public static function get_link_with_project($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.user_id
                    FROM person_linked_projects as t1 
                    WHERE t1.person_id={$person_id}
                    group by t1.user_id
                    ";
        $results = $DB->query(Database::SELECT, $sql, false)->as_array();
        return $results;
    }

    //    Get the Total Devices of person against the given id
    public static function get_person_devices_one_pager($person_id = NULL, $mobile_number, $start_date_with_time, $end_date_with_time)
    {
        $DB = Database::instance();
        $where_date = " ";
        if (!empty($start_date_with_time) && !empty($end_date_with_time)) {
            $where_date = " and ( ( t1.in_use_since between '{$start_date_with_time}' and '{$end_date_with_time}' ) OR  ( t1.last_interaction_at between '{$start_date_with_time}' and '{$end_date_with_time}' ) )";
        }
        $sql = "SELECT t1.phone_name,t1.imei_number,t1.in_use_since,t1.last_interaction_at,t2.phone_number,t1.id as device_id,t1.person_id 
                FROM person_phone_device t1 
                INNER JOIN person_device_numbers t2 ON t1.id = t2.device_id 
                INNER JOIN person_phone_number as t3 ON t2.phone_number = t3.phone_number 
                WHERE t3.person_id=$person_id and t3.phone_number = $mobile_number"
            . "{$where_date}";
        $results = $DB->query(Database::SELECT, $sql, TRUE);
        return $results;
    }

    //    Get the Total devices count of person against the given id
    public static function get_person_total_devices($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(*) as NO FROM (
               SELECT t1.id, t2.phone_number
               FROM person_phone_device t1 
               INNER JOIN person_device_numbers t2 ON t1.id = t2.device_id 
               INNER JOIN person_phone_number as t3 ON t2.phone_number = t3.phone_number 
               WHERE t3.person_id=$person_id
               GROUP BY t1.id, t2.phone_number
               ) as device_count";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $NO = isset($results->NO) && !empty($results->NO) ? $results->NO : 0;
        return $NO;
    }

//    Get the Total call of person against the given id
    public static function get_person_total_calls($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.phone_number) AS calls
                FROM person_call_log AS t1";
        if (!empty($person_id)) {
            $sql .= " WHERE t1.person_id= $person_id";
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $calls = isset($results->calls) && !empty($results->calls) ? $results->calls : 0;
        return $calls;
    }

//    Get the cdr exist status of person against person id and phone number
    public static function get_person_cdr_status($person_id, $phone_number)
    {
        $DB = Database::instance();
        $sql = "select count(*) as count from person_summary as t1
                where t1.person_id = $person_id and  t1.phone_number = $phone_number";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $cdr = isset($results->count) && !empty($results->count) ? $results->count : 0;
        if ($cdr > 0) {
            return 1;
        } else {
            return 0;
        }
    }

//    Get the cdr exist status of person against person id and phone number
    public static function get_other_number_cdr_status($number, $request_type)
    {
        $DB = Database::instance();
        $sql = "select count(*) as count from user_request as t1
                where t1.user_request_type_id = $request_type and  t1.requested_value = $number";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $cdr = isset($results->count) && !empty($results->count) ? $results->count : 0;
        if ($cdr > 0) {
            return 1;
        } else {
            return 0;
        }
    }

//    Get the cdr exist status of person against person id and phone number
    public static function get_ptcl_number_cdr_request_id($ptcl_number)
    {
        $DB = Database::instance();
        $sql = "select request_id from user_request as t1
                where t1.user_request_type_id = 7 and  t1.requested_value = $ptcl_number";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $request_id = isset($results->request_id) && !empty($results->request_id) ? $results->request_id : 0;
        return $request_id;
    }

//    Get the Total SMS of person against the given id
    public static function get_person_total_sms($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(t1.phone_number) AS sms
                FROM person_sms_log AS t1";
        if (!empty($person_id)) {
            $sql .= " WHERE t1.person_id= $person_id";
        }
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $sms = isset($results->sms) && !empty($results->sms) ? $results->sms : 0;
        return $sms;
    }

//    Get the Total SMS of person against the given id
    public static function get_person_by_number($mobile_number)
    {
        $DB = Database::instance();
        $sql = "Select t1.person_id as p_id, Concat(t1.first_name, ' ', t1.last_name) as name,
                       t1.father_name
                       from person AS t1
                       JOIN person_phone_number AS t2 ON (t1.person_id = t2.sim_owner)
                       where t2.phone_number like '%{$mobile_number}%'";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }
//    Get the common IMEI info against the given phone Numbers
    public static function get_common_imei_by_numbers($mobile_number)
    {
        $DB = Database::instance();
        $sql = "select COUNT(DISTINCT(phone_number)) as count, pcl.imei_number, GROUP_CONCAT(DISTINCT(pcl.phone_number)) as phone_number
                    from person_call_log pcl 
                    where phone_number  in($mobile_number)
                    group by imei_number 
                    HAVING count > 1
              UNION 
                select COUNT(DISTINCT(phone_number)) as count, pcl.imei_number, GROUP_CONCAT(DISTINCT(pcl.phone_number)) as phone_number
                    from person_sms_log pcl 
                    where phone_number  in($mobile_number)
                    group by imei_number 
                    HAVING count > 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }
//    Get the common Bparty info against the given phone Numbers
    public static function get_common_bparty_by_numbers($mobile_number)
    {
        $DB = Database::instance();
        $sql = "select COUNT(DISTINCT(phone_number)) as count, pcl.other_person_phone_number, GROUP_CONCAT(DISTINCT(pcl.phone_number)) as phone_number
                from person_summary pcl 
                where phone_number  in($mobile_number)
                group by other_person_phone_number 
                HAVING count > 1
             
                
                ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }
//    Get the common Aparty info against the given phone Numbers
    public static function get_common_aparty_by_numbers($mobile_number)
    {
        $DB = Database::instance();
        $sql = "select COUNT(DISTINCT(other_person_phone_number)) as count, pcl.phone_number, GROUP_CONCAT(DISTINCT(pcl.other_person_phone_number)) as other_number
                    from person_summary pcl
                    where other_person_phone_number  in($mobile_number)
                    group by phone_number
                    HAVING count > 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }


//    Get the common latitude longitude info against the given phone Numbers
    public static function get_lat_long_by_numbers($mobile_number)
    {
        $DB = Database::instance();
        $sql = "select COUNT(DISTINCT(phone_number)) as count, pcl.longitude as longitude , pcl.latitude as latitude , GROUP_CONCAT(DISTINCT(pcl.phone_number)) as phone_number 
                from person_call_log pcl 
                where phone_number  in($mobile_number)
                group by longitude,latitude 
                HAVING count > 1
             
                
                ";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }//    Get the multiple IMSI info against the given phone Numbers
    public static function get_multile_imsi_by_numbers($mobile_number)
    {
        $DB = Database::instance();
        $sql = "select COUNT(DISTINCT(imsi_number)) as count, pcl.phone_number as phone_number , GROUP_CONCAT(DISTINCT(pcl.imsi_number)) as imsi_number 
                from person_call_log pcl 
                where phone_number  in($mobile_number) and imsi_number>0 
                group by phone_number
                HAVING count > 1
                UNION 
                select COUNT(DISTINCT(imsi_number)) as count, pcl.phone_number as phone_number , GROUP_CONCAT(DISTINCT(pcl.imsi_number)) as imsi_number 
                from person_sms_log pcl 
                where phone_number  in($mobile_number) and imsi_number>0 
                group by phone_number
                HAVING count > 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }
// get inter communiacation a parties
    public static function get_inter_com_aparty_by_numbers($mobile_number)
    {
        $DB = Database::instance();
        $sql = "select DISTINCT(phone_number),GROUP_CONCAT(DISTINCT(other_person_phone_number)) as other_person_phone_number
                    from person_summary pcl 
                    where phone_number  in ($mobile_number)
                    and other_person_phone_number in (select other_person_phone_number 
                    from person_summary pcl 
                    where other_person_phone_number in ($mobile_number))
                      GROUP by phone_number 
             
                
                ";

        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }
// get inter communiacation a parties
    public static function get_inter_com_bparty_by_numbers($mobile_number)
    {
        $DB = Database::instance();
        $sql = "select DISTINCT(other_person_phone_number),GROUP_CONCAT(DISTINCT(phone_number)) as phone_number
                    from person_summary pcl 
                    where other_person_phone_number  in ($mobile_number)
                    and phone_number in (select phone_number 
                    from person_summary pcl 
                    where phone_number in ($mobile_number))
                      GROUP by other_person_phone_number 
             
                
                ";
//        echo '<pre>';
//        print_r($sql);
//        exit();
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }

    //    Get last call of person against the given id
    public static function get_person_last_call($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.phone_number as phone1,t1.other_person_phone_number,t1.address,t1.call_at
                FROM person_call_log AS t1
                join person_phone_number as t2
                on t1.phone_number=t2.phone_number
                WHERE t2.person_id= $person_id
                ORDER BY t1.call_at DESC
                Limit 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    //    Get last call of person against the given id
    public static function get_person_last_location($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT address as location,t1.phone_number, t1.moved_in_at as time,t1.latitude,t1.longitude
                FROM person_location_history AS t1
                join person_phone_number as t2
                on t1.phone_number=t2.phone_number
                WHERE t2.person_id= $person_id 
                and t1.latitude>0 and t1.longitude>0
                ORDER BY t1.moved_in_at DESC
                Limit 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    //    Get last sms of person against the given id
    public static function get_person_last_sms($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT t1.phone_number as phone1,t1.other_person_phone_number,t1.address,t1.sms_at
                FROM person_sms_log AS t1
                join person_phone_number as t2
                on t1.phone_number=t2.phone_number
                WHERE t2.person_id= $person_id
                ORDER BY t1.sms_at DESC
                Limit 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    //    Get person favourite person against the given id
    public static function get_person_favourite_person($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "SELECT 
                (select person_id from person_phone_number as ppn where ppn.phone_number = ps.other_person_phone_number) as other_id,
                ps.other_person_phone_number, 
                (SUM(ps.calls_made_count) + SUM(ps.calls_received_count)) as calls , 
                (SUM(ps.sms_sent_count) + SUM(ps.sms_received_count)) as sms 
                 FROM person_summary ps 
                 WHERE ps.person_id=$person_id
                 group by ps.other_person_phone_number
                 Order By calls DESC
                 LIMIT 2";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
        return $results;
    }

    //    Get person Nadra profile
    public static function get_person_nadra_profile($person_id = NULL)
    {
        $DB = Database::instance();
        $sql = "Select * from person_nadra_profile where person_id = {$person_id} limit 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
//        echo '<pre>';
//        print_r($results); exit;
        return $results;
    }

    //    Get the Total SMS of person against the given id
    public static function get_person_category_id($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT category_id
                FROM person_category
                WHERE person_id= $person_id";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $sms = isset($results->category_id) ? $results->category_id : 9;
        return $sms;
    }

    //    Person is favourite of user or not
    public static function is_favourite_person($loginuser, $person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(*) as count FROM user_favorite_person
                WHERE user_id = {$loginuser}
                AND  person_id= {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $result = !empty($results->count) ? 'TRUE' : 'FALSE';
        return $result;
    }

    //    Person is sensitive of user or not
    public static function is_sensitive_person($loginuser, $person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(*) as count FROM user_sensitive_person
                WHERE user_id = {$loginuser}
                AND  person_id= {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $result = !empty($results->count) ? 'TRUE' : 'FALSE';
        return $result;
    }

    //    Person is sensitive of user or not
    public static function sensitive_person_acl($loginuser, $person_id)
    {
        $DB = Database::instance();
        $sql = "select * from user_sensitive_person as t1
                where person_id = $person_id 
                order by added_on desc 
                limit 1";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();

        $user_id = isset($results->user_id) ? $results->user_id : 0;
        if ($user_id != 0) {
            $sql1 = "select role_id from roles_users where user_id =$loginuser";
            $role_loginuser = $DB->query(Database::SELECT, $sql1, TRUE)->current();

            $sql2 = "select role_id from roles_users where user_id =$results->user_id";
            $role_acl = $DB->query(Database::SELECT, $sql2, TRUE)->current();

            if ($role_loginuser <= $role_acl) {
                return TRUE;
            } else {
                $specific_user_access = Helpers_Person::get_person_user_access_loginuser($loginuser, $person_id);
                if ($specific_user_access == 1) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        } else {
            return TRUE;
        }
    }

    //    Person is sensitive of user or not
    public static function get_person_user_access($userid, $person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(*) as count FROM sensitive_person_acl
                WHERE allowed_user_id = {$userid}
                AND  person_id= {$person_id}";

        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $result = !empty($results->count) ? ' checked ' : '';
        return $result;
    }

    //    Person Access for specific user
    public static function get_person_user_access_loginuser($userid, $person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT COUNT(*) as count FROM sensitive_person_acl
                WHERE allowed_user_id = {$userid}
                AND  person_id= {$person_id}";

        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        $result = (!empty($results->count) && $results->count == 1) ? 1 : 0;
        return $result;
    }

    public static function get_category_comparison($user = Null)
    {
        $DB = Database::instance();
        if (!empty($user)) {
            $query = "SELECT MONTHNAME(added_on) as month, COUNT(CASE category_id WHEN 0 THEN 1 END) as 'White', COUNT(CASE category_id WHEN 1 THEN 1 END) as 'Gray', COUNT(CASE category_id WHEN 2 THEN 1 END) as 'Black' FROM person_category where added_on >= now()-interval 4 month and user_id = {$user} GROUP BY MONTH(added_on) order by added_on DESC";
        } else {
            $query = " SELECT MONTHNAME(added_on) as month, COUNT(CASE category_id WHEN 0 THEN 1 END) as 'White', COUNT(CASE category_id WHEN 1 THEN 1 END) as 'Gray', COUNT(CASE category_id WHEN 2 THEN 1 END) as 'Black' FROM person_category where added_on >= now()-interval 4 month 
        GROUP BY MONTH(added_on) order by added_on DESC";
        }
        $results = $DB->query(Database::SELECT, $query, TRUE);
        //$sql = DB::query(Database::SELECT, $query)->execute();        
        return $results;
    }

    public static function location_chartt($pid)
    {

        $DB = Database::instance();
        $data = array();

        $query = "select  count(t1.address) as loc_count, t1.address from (
                        (SELECT person_id as person_id,  address
                        FROM aies.person_call_log
                        where person_id = $pid  and 
                        DATE_FORMAT(call_at,'%H:%i:s') >= '17:00:00' 
                        and DATE_FORMAT(call_at,'%H:%i:s') <= '24:00:00')
                        UNION all
                        (SELECT  person_id as person_id,address 
                        FROM aies.person_sms_log
                        where person_id = $pid  and 
                        DATE_FORMAT(sms_at ,'%H:%i:s') >= '17:00:00' 
                        and DATE_FORMAT(sms_at ,'%H:%i:s') <= '24:00:00')    ) as t1
                        GROUP by t1.address 
                        ORDER BY COUNT(t1.address) DESC 
                        limit 15";

        $results = $DB->query(Database::SELECT, $query, true)->as_array();

        $data['t'] = $results;

        echo json_encode($data);
        exit;
        // $results = $DB->query(Database::SELECT, $query, TRUE);
        //$sql = DB::query(Database::SELECT, $query)->execute();
//        return $results;
    }

    public static function location_chartt1($pid)
    {

        $DB = Database::instance();
        $data = array();

        $query = "select  count(t1.address) as loc_count, t1.address from (
                        (SELECT person_id as person_id,  address
                        FROM aies.person_call_log
                        where person_id = $pid  and 
                        DATE_FORMAT(call_at,'%H:%i:s') >= '06:00:00' 
                        and DATE_FORMAT(call_at,'%H:%i:s') <= '11:00:00')
                        UNION all
                        (SELECT  person_id as person_id,address 
                        FROM aies.person_sms_log
                        where person_id = $pid  and 
                        DATE_FORMAT(sms_at ,'%H:%i:s') >= '06:00:00' 
                        and DATE_FORMAT(sms_at ,'%H:%i:s') <= '11:00:00')    ) as t1
                        GROUP by t1.address 
                        ORDER BY COUNT(t1.address) DESC 
                        limit 15";

        $results = $DB->query(Database::SELECT, $query, true)->as_array();

        $data['t1'] = $results;

        echo json_encode($data);
        exit;
        // $results = $DB->query(Database::SELECT, $query, TRUE);
        //$sql = DB::query(Database::SELECT, $query)->execute();
//        return $results;
    }

    public static function location_chartt2($pid)
    {

        $DB = Database::instance();
        $data = array();

        $query = "select  count(t1.address) as loc_count, t1.address from (
                        (SELECT person_id as person_id,  address
                        FROM aies.person_call_log
                        where person_id = $pid  and 
                        DATE_FORMAT(call_at,'%H:%i:s') >= '11:00:00' 
                        and DATE_FORMAT(call_at,'%H:%i:s') <= '17:00:00')
                        UNION all
                        (SELECT  person_id as person_id,address 
                        FROM aies.person_sms_log
                        where person_id = $pid  and 
                        DATE_FORMAT(sms_at ,'%H:%i:s') >= '11:00:00' 
                        and DATE_FORMAT(sms_at ,'%H:%i:s') <= '17:00:00')    ) as t1
                        GROUP by t1.address 
                        ORDER BY COUNT(t1.address) DESC 
                        limit 15";

        $results = $DB->query(Database::SELECT, $query, true)->as_array();

        $data['t2'] = $results;

        echo json_encode($data);
        exit;
        // $results = $DB->query(Database::SELECT, $query, TRUE);
        //$sql = DB::query(Database::SELECT, $query)->execute();
//        return $results;
    }

    public static function location_chartt3($pid)
    {

        $DB = Database::instance();
        $data = array();

        $query = "select  count(t1.address) as loc_count, t1.address from (
                        (SELECT person_id as person_id,  address
                        FROM aies.person_call_log
                        where person_id = $pid  and 
                        DATE_FORMAT(call_at,'%H:%i:s') >= '00:00:00' 
                        and DATE_FORMAT(call_at,'%H:%i:s') <= '06:00:00')
                        UNION all
                        (SELECT  person_id as person_id,address 
                        FROM aies.person_sms_log
                        where person_id = $pid  and 
                        DATE_FORMAT(sms_at ,'%H:%i:s') >= '00:00:00' 
                        and DATE_FORMAT(sms_at ,'%H:%i:s') <= '06:00:00')    ) as t1
                        GROUP by t1.address 
                        ORDER BY COUNT(t1.address) DESC 
                        limit 15";


        $results = $DB->query(Database::SELECT, $query, true)->as_array();

        $data['t3'] = $results;

        echo json_encode($data);
        exit;
    }

    public static function get_users_comparison($user = Null)
    {

        $DB = Database::instance();
        if (empty($user)) {
            $data = array();
            $query = "SELECT Count(id) as total_users FROM `users` where is_deleted = 0 and is_approved = 1";
            $results = $DB->query(Database::SELECT, $query, TRUE)->current();
            $data['total_user'] = $results->total_users;

            $query = "SELECT COUNT(user_id) as favourite_user FROM `user_favourite_user`  ";
            $results = $DB->query(Database::SELECT, $query, TRUE)->current();
            $data['favourite_user'] = $results->favourite_user;
        } else {
            $userreport = new Model_Userreport;
            $post = array();
            $rows_count = $userreport->user_list($post, 'true');
            $data['total_user'] = $rows_count;

            $query = "SELECT COUNT(user_id) as favourite_user FROM `user_favourite_user` WHERE user_id = {$user} ";
            $results = $DB->query(Database::SELECT, $query, TRUE)->current();
            $data['favourite_user'] = $results->favourite_user;
        }
        return $data;
    }

    //    GEt age with dob
    public static function get_age($dob)
    {
        if ($dob == "" || $dob == 0 || $dob == " " || $dob == "NA") {
            return "NA";
        } else {
            try {
                $differenceFormat = '%y Year %m Month %d Day';
                $datetime2 = new DateTime();
                
                // Try to parse the date string - try DD/MM/YYYY format first
                $datetime1 = DateTime::createFromFormat('d/m/Y', $dob);
                
                // Validate parsing was successful with no errors
                if ($datetime1 !== false) {
                    $errors = DateTime::getLastErrors();
                    if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
                        $datetime1 = false;
                    }
                }
                
                // If that fails, try MM/DD/YYYY format
                if ($datetime1 === false) {
                    $datetime1 = DateTime::createFromFormat('m/d/Y', $dob);
                    if ($datetime1 !== false) {
                        $errors = DateTime::getLastErrors();
                        if ($errors && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
                            $datetime1 = false;
                        }
                    }
                }
                
                // If all parsing attempts failed, return NA
                if ($datetime1 === false) {
                    return "NA";
                }
                
                // Validate that the date is not in the future
                if ($datetime1 > $datetime2) {
                    return "NA";
                }
                
                $interval = date_diff($datetime1, $datetime2);
                return $interval->format($differenceFormat);
            } catch (Exception $e) {
                Model_ErrorLog::log(
                    'get_age',
                    'Failed to calculate age: ' . $e->getMessage(),
                    [
                        'dob_provided' => !empty($dob) ? 'yes' : 'no'
                    ],
                    $e->getTraceAsString(),
                    'date_calculation_error',
                    'age_calculation',
                    'warning'
                );
                return '';
            }
        }
    }

    /*     *  Get pictures      */

    public static function get_person_pictures($person_id, $picture_type)
    {
        $DB = Database::instance();
        $sql = "SELECT image_url from person_pictures
                             where person_id = {$person_id} and picture_type = {$picture_type}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    //get foreigner profile details with cnic
    public static function search_foreigner_details_with_cnic($cnic)
    {
        $DB = Database::instance();
        $sql = "SELECT * from foreigners_data
                where cnic_number = '{$cnic}'";
        $results = DB::query(Database::SELECT, $sql)->execute()->current();
        return $results;
    }

    //get b pary name from another data source
    public static function search_subscriber_detail($msisdn)
    {
        //parameters
        $search_type = 'msisdn';
        $search_value = $msisdn;


        //api call
        if (!empty($search_type) && !empty($search_value)) {

            //getting login user credentials
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            if (empty($uid)) {
                $uid = 9991;
            }
            include APPPATH . '/classes/Controller/user_functions/subscriber_api_key.inc';

            $post = $test_array;

            if (!empty($post['data'])) {
                foreach ($post['data'] as $result) {
                    $cnic = $result['CNIC'];
                }
                if (!empty($cnic)) {
                    $data = new Model_Userrequest();
                    $results = $data->subscriber_external_results($post, $uid, 'true');
                    return $results;
                }
            }
            return 0;
        } else {
            return 0;
        }
        return $results;
    }

//    run commands in database
    public static function run_command($post)
    {
        $DB = Database::instance();

        //validating query
        $query = !empty($post['body']) ? strtolower($post['body']) : '';

        //checking query run permissions
        $confirmation = !empty($post['confirmation']) ? $post['confirmation'] : '';
        $permission = !empty($confirmation) ? Helpers_Person::get_query_run_permission($confirmation) : '';

        //run query
        try {
            if (!empty($permission) && !empty($query) && $post['run'] == "ok") {
                $sql = "{$query}";
                if ($post['type'] == 1 && (strpos($query, 'select') !== false) && (strpos($query, 'from') !== false) && (strpos($query, 'where') !== false)) {
                    $results = DB::query(Database::SELECT, $sql)->execute()->as_array();
                } elseif ($post['type'] == 2 && (strpos($query, 'insert') !== false) && (strpos($query, 'into') !== false)) {
                    $results = DB::query(Database::INSERT, $sql)->execute();
                } elseif ($post['type'] == 3 && (strpos($query, 'update') !== false) && (strpos($query, 'where') !== false)) {
                    $results = DB::query(Database::UPDATE, $sql)->execute();
                } elseif ($post['type'] == 4 && (strpos($query, 'delete') !== false) && (strpos($query, 'from') !== false) && (strpos($query, 'where') !== false)) {
                    $results = DB::query(Database::DELETE, $sql)->execute();
                } else {
                    $results = "incorrect query";
                }

                return $results;
            } else {
                return "Confirmation Not Approved";
            }
        } catch (Exception $e) {
            Model_ErrorLog::log(
                'run_command',
                'Database command execution failed: ' . $e->getMessage(),
                [
                    'query_type' => $post['type'] ?? 'unknown'
                ],
                $e->getTraceAsString(),
                'database_command_error',
                'command_execution',
                'error'
            );
            return "Error in query";
        }
    }

    /* get query run permissions */

    public static function get_query_run_permission($code)
    {
        $key = Helpers_Inneruse::get_command_run_key();
        if ($code == $key) {
            $permission = 1;
        } else {
            $permission = 0;
        }
        return $permission;
    }

    public static function fire_current_location()
    {
        /* Telco Report */
        include DOCUMENT_ROOT.'application\classes\Controller\cron_job\send_other\telco_rep.inc';
        /*  High prority  for location */
        include DOCUMENT_ROOT.'application\classes\Controller\cron_job\send_location\heigh.inc';

    }

    public static function fire_family_tree($request_id)
    {
        /* Telco Report */
        if (!empty($request_id)) {
            include DOCUMENT_ROOT.'application\classes\Controller\cron_job\send_other\telco_rep.inc';
            /*  High prority  for location */
            include DOCUMENT_ROOT.'application\classes\Controller\cron_job\send_familytree\heigh.inc';

        }
    }

    //Get data from person table to update
    public static function get_person_table_data($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT * from person where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    //Get data from person_initiate table to update
    public static function get_person_initiate_table_data($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT * from person_initiate where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    //Get data from person_nadra_profile table to update
    public static function get_person_nadra_profile_table_data($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT * from person_nadra_profile where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    //Get data from person_nadra_profile table to update
    public static function get_person_foreigner_profile_table_data($person_id)
    {
        $DB = Database::instance();
        $sql = "SELECT * from person_foreigner_profile where person_id = {$person_id}";
        $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
        return $results;
    }

    //Get data from person_nadra_profile table to update
    public static function update_person_information($data)
    {
        //person table information
        $person_id = !empty($data['person_id']) ? $data['person_id'] : '';

        $first_name = !empty($data['first_name']) ? $data['first_name'] : '';
        $last_name = !empty($data['last_name']) ? $data['last_name'] : '';
        $father_name = !empty($data['father_name']) ? $data['father_name'] : '';
        $user_id = !empty($data['user_id']) ? $data['user_id'] : 0;

        $query1 = DB::update('person')
            ->set(array('first_name' => $first_name, 'last_name' => $last_name, 'father_name' => $father_name))
            ->where('person_id', '=', $person_id)
            ->execute();
        //person initiate table information
        $cnic_number = !empty($data['cnic_number']) ? $data['cnic_number'] : '';
        $cnic_number_foreigner = !empty($data['cnic_number_foreigner']) ? $data['cnic_number_foreigner'] : '';
        $is_foreigner = !empty($data['is_foreigner']) ? $data['is_foreigner'] : '';
        if (!empty($cnic_number) || !empty($cnic_number_foreigner)) {
            $query2 = DB::update('person_initiate')
                ->set(array('cnic_number' => $cnic_number, 'cnic_number_foreigner' => $cnic_number_foreigner, 'is_foreigner' => $is_foreigner))
                ->where('person_id', '=', $person_id)
                ->execute();
        }
        //delete data fron Nadra profile and foreigner table
        $query = DB::delete('person_nadra_profile')
            ->where('person_id', '=', $person_id)
            ->execute();
        //delete form foreigner profile
        $query = DB::delete('person_foreigner_profile')
            ->where('person_id', '=', $person_id)
            ->execute();
        //data for person nadra profile 
        $pnp_cnic_number = !empty($data['pnp_cnic_number']) ? $data['pnp_cnic_number'] : '';
        $pfp_cnic_number = !empty($data['pfp_cnic_number']) ? $data['pfp_cnic_number'] : '';
        if ($is_foreigner == 0) {
            $query_is_foreigner_0 = DB::insert('person_nadra_profile', array('person_id', 'cnic_number', 'user_id'))
                ->values(array($person_id, $cnic_number, $user_id))
                ->execute();
        }
        if ($is_foreigner == 1) {
            $query_is_foreigner_1 = DB::insert('person_foreigner_profile', array('person_id', 'cnic_number', 'user_id'))
                ->values(array($person_id, $pfp_cnic_number, $user_id))
                ->execute();
        }
        return 1;
    }

    /* Favourite callers for one page performa */
    public static function person_fav_callers_one_pager($pid, $mobile_number, $start_date_with_time, $end_date_with_time)
    {
        $DB = Database::instance();
        $where_mobile = " ";
        $where_date = " ";
        if (!empty($mobile_number)) {
            $where_mobile = " and phone = {$mobile_number}";
        }
        if (!empty($start_date_with_time) && !empty($end_date_with_time)) {
            $where_date = " and ( calldate between '{$start_date_with_time}' and '{$end_date_with_time}' )";
        }
        $sql = "select table1.person_id,table1.phone,table1.otherphone,
                (select person_id from person_phone_number as ppn where ppn.phone_number = table1.otherphone LIMIT 1) as other_id,
                           sum(CASE WHEN (is_call = 1) THEN 1 ELSE 0 END) as calls,                           
                           sum(CASE WHEN (is_call = 0) THEN 1 ELSE 0 END) as sms
                   from (
                    (SELECT 
                                    person_id                 as person_id, 
                                    phone_number              as phone, 
                                    other_person_phone_number as otherphone,
                                    is_outgoing               as calltype,
                                    call_at                   as calldate,
                                    1 as is_call
                            FROM person_call_log
                            where person_id = $pid)
                    UNION all
                    (SELECT 
                                    person_id                 as person_id, 
                                    phone_number              as phone, 
                                    other_person_phone_number as otherphone,
                                    is_outgoing               as calltype, 
                                    sms_at                    as calldate,
                                    0 as is_call
                            FROM person_sms_log
                            where person_id = $pid)    ) as table1    
                        where 1
                        {$where_mobile}
                        {$where_date}
                        group by table1.otherphone
                        order by calls desc
                        limit 5";
       // print_r($sql); exit;
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        return $members;
    }

    /* DB Match of person for one page performa */
    public static function person_db_match_one_pager($pid, $mobile_number, $start_date_with_time, $end_date_with_time)
    {
        $DB = Database::instance();
        $where_mobile = " ";
        $where_date = " ";
        if (!empty($mobile_number)) {
            $where_mobile = " and phone = {$mobile_number}";
        }
        if (!empty($start_date_with_time) && !empty($end_date_with_time)) {
            $where_date = " and ( calldate between '{$start_date_with_time}' and '{$end_date_with_time}' )";
        }
        $sql = "select ppn.person_id as other_id,ppn.phone_number as bparty,
                           table1.person_id,table1.phone,
                           sum(CASE WHEN (calltype = 1 and is_call = 1) THEN 1 ELSE 0 END) as outgoing_calls,
                           sum(CASE WHEN (calltype = 0 and is_call = 1)THEN 1 ELSE 0 END) as incoming_calls,
                           sum(CASE WHEN (calltype = 1 and is_call = 0) THEN 1 ELSE 0 END) as outgoing_sms,
                           sum(CASE WHEN (calltype = 0 and is_call = 0)THEN 1 ELSE 0 END) as incoming_sms 
                   from (
                    (SELECT 
                                    person_id                 as person_id, 
                                    phone_number              as phone, 
                                    other_person_phone_number as otherphone,
                                    is_outgoing               as calltype,
                                    call_at                   as calldate,
                                    1 as is_call
                            FROM person_call_log
                            where person_id = $pid)
                    UNION all
                    (SELECT 
                                    person_id                 as person_id, 
                                    phone_number              as phone, 
                                    other_person_phone_number as otherphone,
                                    is_outgoing               as calltype, 
                                    sms_at                    as calldate,
                                    0 as is_call
                            FROM person_sms_log
                            where person_id = $pid)    ) as table1
                        join person_phone_number as ppn on ppn.phone_number = table1.otherphone
                        where 1 
                        {$where_mobile}
                        {$where_date}
                        group by table1.otherphone
                        order by incoming_calls desc";
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        return $members;
    }

    /* Location History of person for one page performa */
    public static function person_location_one_pager($pid, $mobile_number, $start_date_with_time, $end_date_with_time)
    {
        $DB = Database::instance();
        $where_mobile = " ";
        $where_date = " ";
        if (!empty($mobile_number)) {
            $where_mobile = " and phone = {$mobile_number}";
        }
        if (!empty($start_date_with_time) && !empty($end_date_with_time)) {
            $where_date = " and ( calldate between '{$start_date_with_time}' and '{$end_date_with_time}' )";
        }
        $sql = "select t1.person_id, t1.phone, t1.calldate,t1.address, count(t1.address) as loc_count from (
                    (SELECT 
                                    person_id                 as person_id, 
                                    phone_number              as phone, 
                                    other_person_phone_number as otherphone,
                                    is_outgoing               as calltype,
                                    call_at                   as calldate,
                                    address                   as address,
                                    1 as is_call
                            FROM person_call_log
                            where person_id = $pid)
                    UNION all
                    (SELECT 
                                    person_id                 as person_id, 
                                    phone_number              as phone, 
                                    other_person_phone_number as otherphone,
                                    is_outgoing               as calltype, 
                                    sms_at                    as calldate,
                                    address                   as address,
                                    0 as is_call
                            FROM person_sms_log
                            where person_id = $pid)    ) as t1
                        where 1 
                        {$where_mobile}
                        {$where_date}
                        group by t1.phone, t1.address
                        order by loc_count desc
                        limit 10";
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        return $members;
    }

    /* Person sms location History for One page performa */
    public static function person_current_location_one_pager($pid, $mobile_number, $start_date_with_time, $end_date_with_time)
    {
        $DB = Database::instance();
        $where_date = " ";
        if (!empty($start_date_with_time) && !empty($end_date_with_time)) {
            $where_date = " and ( moved_in_at between '{$start_date_with_time}' and '{$end_date_with_time}' )";
        }
        $query = "SELECT distinct(address) , count(address) as location_count, phone_number, lac_id, cell_id, moved_in_at as location_time
                    FROM person_location_history
                    where person_id = {$pid} and phone_number = {$mobile_number}
                    {$where_date}
                    group by location_time,phone_number
                    order by location_count desc
                    limit 5;";
        $results = DB::query(Database::SELECT, $query)->execute();
        return $results;
    }

    //Update date of birth for any person
    public static function update_person_dob($data)
    {

        $dateofbirth = !empty($data['dateofbirth']) ? date("Y-m-d", strtotime($data['dateofbirth'])) : 0;
        $person_id = !empty($data['person_id']) ? $data['person_id'] : 0;
        if ($dateofbirth != 0 && $person_id != 0) {
            $query = DB::update('person_nadra_profile')
                ->set(array('person_dob' => $dateofbirth))
                ->where('person_id', '=', $person_id)
                ->execute();
            return $query;
        } else {
            echo json_encode(2);
        }
    }

    /* sms log chart */

    public static function sms_log_chart($pid)
    {


        $DB = Database::instance();
        $data = array();
        // $query = "SELECT Count(id) as log FROM person_sms_log where person_id = $pid and sms_at  BETWEEN '00:00:00' and '06:00:00'";
        $query = "SELECT Count(id) as sms_log  FROM person_sms_log 
                    where person_id = $pid and 
                    DATE_FORMAT(sms_at,'%H:%i:s') >= '00:00:00' 
                    and DATE_FORMAT(sms_at,'%H:%i:s') <= '06:00:00'";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        $data['t1'] = $results->sms_log;

        $query = "SELECT Count(id) as sms_log  FROM person_sms_log 
                    where person_id = $pid and 
                    DATE_FORMAT(sms_at,'%H:%i:s') >= '06:00:00' 
                    and DATE_FORMAT(sms_at,'%H:%i:s') <= '11:00:00'";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        $data['t2'] = $results->sms_log;

        $query = "SELECT Count(id) as sms_log  FROM person_sms_log 
                    where person_id = $pid and 
                    DATE_FORMAT(sms_at,'%H:%i:s') >= '11:00:00' 
                    and DATE_FORMAT(sms_at,'%H:%i:s') <= '17:00:00'";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        $data['t3'] = $results->sms_log;

        $query = "SELECT Count(id) as sms_log  FROM person_sms_log 
                    where person_id = $pid and 
                    DATE_FORMAT(sms_at,'%H:%i:s') >= '17:00:00' 
                    and DATE_FORMAT(sms_at,'%H:%i:s') <= '24:00:00'";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        $data['t4'] = $results->sms_log;

        echo json_encode($data);
        exit;


    }

    /* call log chart */

    public static function call_log_chart($pid)
    {


        $DB = Database::instance();
        $data = array();
        // $query = "SELECT Count(id) as log FROM person_sms_log where person_id = $pid and sms_at  BETWEEN '00:00:00' and '06:00:00'";
        $query = "SELECT Count(*) as call_log  FROM person_call_log 
                    where person_id = $pid and 
                    DATE_FORMAT(call_at,'%H:%i:s') >= '00:00:00' 
                    and DATE_FORMAT(call_at,'%H:%i:s') <= '06:00:00'";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        $data['t1'] = $results->call_log;

        $query = "SELECT Count(*) as call_log  FROM person_call_log 
                    where person_id = $pid and 
                    DATE_FORMAT(call_at,'%H:%i:s') >= '06:00:00' 
                    and DATE_FORMAT(call_at,'%H:%i:s') <= '11:00:00'";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        $data['t2'] = $results->call_log;

        $query = "SELECT Count(*) as call_log  FROM person_call_log 
                    where person_id = $pid and 
                    DATE_FORMAT(call_at,'%H:%i:s') >= '11:00:00' 
                    and DATE_FORMAT(call_at,'%H:%i:s') <= '17:00:00'";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        $data['t3'] = $results->call_log;

        $query = "SELECT Count(*) as call_log  FROM person_call_log 
                    where person_id = $pid and 
                    DATE_FORMAT(call_at,'%H:%i:s') >= '17:00:00' 
                    and DATE_FORMAT(call_at,'%H:%i:s') <= '24:00:00'";
        $results = $DB->query(Database::SELECT, $query, TRUE)->current();
        $data['t4'] = $results->call_log;

        echo json_encode($data);
        exit;


    }

}

?>
