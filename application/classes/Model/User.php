<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Model_User extends Model_Auth_User {

    public static function search_person($data, $count) {
//          echo '<pre>';
//         print_r($data);
        //exit;
        $person_phone_number = "";
        $person_phone_device = "";
        $person_affiliations = "";
        $person_category = "";
        $person_table = "";
        $sub_query = "";
        $number_type_search = '';
        /* Posted Data */
        $result = array();
        if (empty($data['number_type'])) {
            $number_type_search = '';
        } else {
            switch ($data['number_type']) {
                case 1: //mobile number
                    if (!empty($data['phonenumber'])) {
                        $number_type_search = " and t2.phone_number = {$data['phonenumber']}";
                        $person_phone_number = "JOIN person_phone_number AS t2 ON (t1.person_id = t2.sim_owner)";
                    }
                    break;
                case 4: //imsi number
                    $number_type_search = " and t2.imsi_number ={$data['imsi']}";
                    $person_phone_number = "JOIN person_phone_number AS t2 ON (t1.person_id = t2.sim_owner)";
                    break;
                case 5: //imei number
                    $data['imei'] = Helpers_Utilities::find_imei_last_digit($data['imei']);
                    $number_type_search = " and t3.imei_number ={$data['imei']}";
                    $person_phone_device = "JOIN person_phone_device AS t3 ON (t1.person_id = t3.person_id)";
                    break;
                default :
                    $number_type_search = '';
                    break;
            }
        }

        if (!isset($data['is_foreigner'])) {
            $data['is_foreigner'] = 2;
        }
        if (!empty($data['personname'])) {

            $temp = trim($data['personname']);
            $parts = preg_split('/\s+/', ($temp));
            // print_r($parts.length); exit;
            if (!empty($data['personname'])) {
                $sub_query .= "and CONCAT(TRIM(t1.first_name), ' ', TRIM(t1.last_name)) like '%{$data['personname']}%'";
            }
        }
        if (!empty($data['fathername'])) {
            $temp = trim($data['fathername']);
            $sub_query .= "and (t1.father_name like '%{$temp}%'  )";
        }

//        if (!empty($data['phonenumber'])) {
//            $sub_query .= " and t2.phone_number = {$data['phonenumber']}";
//            $person_phone_number = "JOIN person_phone_number AS t2 ON (t1.person_id = t2.sim_owner)";
//        }
//        if (!empty($data['imei'])) {
//            $data['imei']=  Helpers_Utilities::find_imei_last_digit($data['imei']);
//            $sub_query .= " and t3.imei_number ={$data['imei']}";
//            $person_phone_device = "JOIN person_phone_device AS t3 ON (t1.person_id = t3.person_id)";
//        }
//        if (!empty($data['imsi']))
//        { 
//            $sub_query .= " and t2.imsi_number ={$data['imsi']}";
//            $person_phone_number = "JOIN person_phone_number AS t2 ON (t1.person_id = t2.sim_owner)";
//       
//            }          
        if (!empty($data['organization'])) {
            $organizations = implode(",", $data['organization']);
            $sub_query .= " and t4.organization_id in  ( {$organizations} ) and t1.person_id = t4.person_id ";
            $person_affiliations = "JOIN person_affiliations as t4 ON (t1.person_id = t4.person_id)";
        }
        if (isset($data['category']) && $data['category'] != '') {
            $person_category = "JOIN person_category as pc ON (t1.person_id = pc.person_id) ";
            $sub_query .= "  and pc.person_id = t1.person_id and pc.category_id = " . $data['category'] . ' ';
        }
        if ($data['is_foreigner'] == 1) {
            if (!empty($data['cnic'])) {
                $sub_query = '';
                $sub_query .= " and pi.cnic_number_foreigner = '{$data['cnic']}' ";
            } else {
                $sub_query .= " and pi.is_foreigner = 1 ";
            }
        } elseif ($data['is_foreigner'] == 0) {
            if (!empty($data['cnic'])) {
                $sub_query = '';
                $sub_query .= " and pi.cnic_number = {$data['cnic']} ";
            } else {
                $sub_query .= " and pi.is_foreigner = 0 ";
            }
        } else {
            $person_table = -1;
        }
        /* Sorted Data */
        if ($person_table == -1) {
            $order_by_param = "t1.firsst_name";
            if (isset($data['iSortCol_0'])) {
                switch ($data['iSortCol_0']) {
                    case "0":
                        $order_by_param = "t1.first_name";
                        break;
                    case "1":
                        $order_by_param = "t1.father_name";
                        break;
                    case "3":
                        $order_by_param = "t2.phone_number";
                        break;
                    case "4":
                        $order_by_param = "t3.imei_number";
                        break;
                }
            }
        } else {
            $order_by_param = "first_name";
            if (isset($data['iSortCol_0'])) {
                switch ($data['iSortCol_0']) {
                    case "0":
                        $order_by_param = "CONCAT(TRIM(first_name), ' ', TRIM(last_name))";
                        break;
                    case "1":
                        $order_by_param = "father_name";
                        break;
                    case "3":
                        $order_by_param = "t2.phone_number";
                        break;
                    case "4":
                        $order_by_param = "t3.imei_number";
                        break;
                }
            }
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\ ]/', '', $data['sSearch']);
            $search = "and (CONCAT(TRIM(first_name), ' ', TRIM(last_name)) like '%{$data['sSearch']}%' or father_name like '%{$data['sSearch']}%' or cnic_number like '%{$data['sSearch']}%' or cnic_number_foreigner like '%{$data['sSearch']}%')";
        } else {
            $search = "";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by t1.person_id";

        $DB = Database::instance();

        if ($person_table == -1) {
            /* For Total Record Count */
            if (!empty($search) || !empty($sub_query)) {
                    $sql = "Select count(pi.person_id) from 
                                person_initiate AS pi 
                                LEFT JOIN person as t1 USING (person_id)
                                {$person_phone_number}
                                {$person_phone_device}
                                {$person_affiliations}
                                {$person_category}
                                where 1
                                {$search}
                                {$sub_query}                            
                                {$number_type_search}                            
                                $groupby
                                {$order_by}
                                 ";

                    // print_r($sql); exit;
                    $members = $DB->query(Database::SELECT, $sql, FALSE);
                    // $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
                    $result['count']= $members->count();
                

                    $sql = "Select pi.person_id as p_id,pi.is_foreigner, Concat(t1.first_name, ' ', t1.last_name) as name, t1.father_name as father_name, pi.cnic_number as cnic_number,pi.cnic_number_foreigner
                                from 
                                person_initiate AS pi 
                                LEFT JOIN person as t1 USING (person_id)
                                {$person_phone_number}
                                {$person_phone_device}
                                {$person_affiliations}
                                {$person_category}
                                where 1
                                {$search}
                                {$sub_query}                             
                                {$number_type_search}                           
                                $groupby
                                {$order_by}                               
                                {$limit}";

                    //   print_r($sql); exit;
                    $members = $DB->query(Database::SELECT, $sql, FALSE);
                    //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();        
                    $result['result']= $members;
                    return $result;
                
            } else {                
                    $sql = "Select  COUNT(*) AS count from 
                            person_initiate AS pi 
                            LEFT JOIN person as t1 USING (person_id)";
                    $members = DB::query(Database::SELECT, $sql)->execute()->current();
                    $result['count']= $members['count'];
                
                    $sql = "Select pi.person_id as p_id,pi.is_foreigner, Concat(t1.first_name, ' ', t1.last_name) as name, t1.father_name as father_name, pi.cnic_number as cnic_number,pi.cnic_number_foreigner
                                from 
                                person_initiate AS pi 
                                LEFT JOIN person as t1 USING (person_id)
                                {$person_phone_number}
                                {$person_phone_device}
                                {$person_affiliations}
                                {$person_category}
                                where 1
                                {$search}
                                {$sub_query}                             
                                {$number_type_search}                             
                                $groupby
                                {$order_by}                               
                                {$limit}";
                    // print_r($sql); exit;
                    $members = $DB->query(Database::SELECT, $sql, FALSE);
                    //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();        
                    $result['result']= $members;
                    return $result;                
            }
        } else {

            /* For Total Record Count */
            if (!empty($search) || !empty($sub_query)) {                
                    $sql = "Select count(t1.person_id) from 
                          person_initiate AS pi 
                          LEFT JOIN person as t1 USING (person_id)
                              {$person_phone_number}
                              {$person_phone_device}
                              {$person_affiliations}
                              {$person_category}
                              where 1
                              {$search}
                              {$sub_query}                            
                              {$number_type_search}                            
                              $groupby
                              {$order_by}
                               ";

                    // print_r($sql); exit;
                    $members = $DB->query(Database::SELECT, $sql, FALSE);
                    // $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
                    $result['count']= $members->count();
                

                    $sql = "Select t1.person_id as p_id, Concat(t1.first_name, ' ', t1.last_name) as name, t1.father_name, pi.cnic_number,pi.cnic_number_foreigner,pi.is_foreigner
                      
                              from                               
                          person_initiate AS pi 
                          LEFT JOIN person as t1 USING (person_id)
                              {$person_phone_number}
                              {$person_phone_device}
                              {$person_affiliations}
                              {$person_category}
                              where 1
                              {$search}
                              {$sub_query}                             
                              {$number_type_search}                             
                              $groupby
                              {$order_by}                               
                              {$limit}";

                    //   print_r($sql); exit;
                    $members = $DB->query(Database::SELECT, $sql, FALSE);
                    //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();        
                    $result['result']= $members;
                    return $result;
                
            } else {
                
                    $sql = "Select  COUNT(*) AS count from 
                          {$person_table}";
                    $members = DB::query(Database::SELECT, $sql)->execute()->current();
                    return $members['count'];
                
                    $sql = "Select t1.person_id as p_id, Concat(t1.first_name, ' ', t1.last_name) as name, t1.father_name, pi.cnic_number,pi.cnic_number_foreigner,pi.is_foreigner
                              from 
                              {$person_table}
                              {$person_phone_number}
                              {$person_phone_device}
                              {$person_affiliations}
                              {$person_category}
                              where 1
                              {$search}
                              {$sub_query}                             
                              {$number_type_search}                             
                              $groupby
                              {$order_by}                               
                              {$limit}";
                    // print_r($sql); exit;
                    $members = $DB->query(Database::SELECT, $sql, FALSE);
                    //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();        
                 $result['result']= $members;
                    return $result;
                
            }
        }
    }

    public static function search_identity($data, $count) {

        $sub_query = "";
        $identity_search_query = "";
        $searched_key = !empty($data['identity_search']) ? $data['identity_search'] : -7;
        $identity_type_id = isset($data['search_identity_type']) ? $data['search_identity_type'] : -7;

        if ($identity_type_id == 4) {
            if (!empty($searched_key) && $searched_key != -7) {
                $sub_query .= " and (pi.cnic_number like '%{$searched_key}%') ";
            } else {
                $sub_query .= " and pi.is_foreigner=0 ";
                $searched_key = "pi.cnic_number ";
            }
        } elseif ($identity_type_id == 5) {
            if (!empty($searched_key) && $searched_key != -7) {
                $sub_query .= " and (pi.cnic_number_foreigner like '%{$searched_key}%') ";
            } else {
                $sub_query .= " and pi.is_foreigner=1 ";
            }
            $searched_key = "pi.cnic_number_foreigner ";
        } elseif ($identity_type_id != '' && $identity_type_id != -7) {
            $identity_search_query .= "JOIN person_identities as t2 USING (person_id) ";
            $sub_query .= " and t2.identity_id =  {$identity_type_id}";
            if (!empty($searched_key) && $searched_key != -7) {
                $sub_query .= " and (t2.identity_no like '%{$searched_key}%') ";
            }
            $searched_key = "t2.identity_no";
        } else {
            $sub_query .= " and pi.is_foreigner=0 or pi.is_foreigner=1 ";
        }
        /* Sorted Data */
        $order_by_param = "t1.first_name";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.first_name";
                    break;
                case "1":
                    $order_by_param = "t1.father_name";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $temp1 = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            //$temp = $data['sSearch'];
            //$temp1 = str_replace("'", "", $temp);
            $parts = preg_split('/\s+/', trim($temp1));
            //print_r($parts); exit;
            if (sizeof($parts) >= 2) {
                $search = "and (t1.first_name like '%{$parts[0]}%' or t1.last_name like '%{$parts[1]}%' or t1.father_name like '%{$data['sSearch']}%' )";
            } else {
                //$search = "and (first_name like '%{$data['sSearch']}%' or last_name like '%{$data['sSearch']}%' or father_name like '%{$data['sSearch']}%' or cnic_number like '%{$data['sSearch']}%' or t2.phone_number like '%{$data['sSearch']}%' or address like '%{$data['sSearch']}%' or T3.imei_number like '%{$data['sSearch']}%' )";
                $search = "and (t1.first_name like '%{$temp1}%' or t1.last_name like '%{$temp1}%' or t1.father_name like '%{$temp1}%' )";
            }
        } else {
            $search = "";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by pi.person_id";

        $DB = Database::instance();
        /* For Total Record Count */
        //   if (!empty($search) || !empty($sub_query)) {
        $result = array();
        //if ($count == 'true') {
            $sql = "Select count(pi.person_id) as count
                    from person_initiate AS pi 
                    LEFT JOIN person as t1 USING (person_id) 
                      {$identity_search_query}  
                            where 1
                            {$search}
                            {$sub_query} 
                             ";
            //  $members = $DB->query(Database::SELECT, $sql, FALSE);
            // $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            //return $members->count();
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            $result['count'] = $members['count'];
        //}else {
            $sql = "Select pi.person_id as p_id,pi.is_foreigner, Concat(t1.first_name, ' ', t1.last_name) as name, t1.father_name as father_name, pi.cnic_number as cnic_number,pi.cnic_number_foreigner as cnic_number_foreigner,{$identity_type_id} as identity_type_id,{$searched_key} as searched_key
                        from person_initiate AS pi 
                        LEFT JOIN person as t1 USING (person_id) 
                        {$identity_search_query}  
                            where 1
                            {$search}
                            {$sub_query}                             
                            $groupby
                            {$order_by}                               
                            {$limit}";
            //    print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();        
            $result['result']= $members;
            return $result;
        //}
        /*  } else {
          if ($count == 'true') {
          $sql = "Select  COUNT(*) AS count from person as t1
          inner join person_identities as t2 on t1.person_id=t2.person_id

          ";
          $members = DB::query(Database::SELECT, $sql)->execute()->current();
          return $members['count'];
          } else {
          $sql = "Select t2.person_id as p_id, Concat(t1.first_name, ' ', t1.last_name) as name, t1.father_name,t2.identity_id,t2.identity_no,t2.is_foreigner
          from person as t1
          inner join person_identities as t2 on t1.person_id=t2.person_id
          where 1
          $groupby
          {$order_by}
          {$limit}";
          // print_r($sql); exit;
          $members = $DB->query(Database::SELECT, $sql, FALSE);
          //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();
          return $members;
          }
          } */
    }

    //
    public static function bparty_search($data, $count) {
//          echo '<pre>';
//         print_r($data);
//        exit;
        $person_phone_number = "";
        if (!empty($data['phonenumber'])) {
            $person_phone_number = " and t1.other_person_phone_number = {$data['phonenumber']}";
        } /*else {
            $person_phone_number = " and t1.other_person_phone_number = '487898585'";
        }*/

        /* Sorted Data */
        $order_by_param = "person_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "person_id";
                    break;
//                case "1":
//                    $order_by_param = "person_id";
//                    break;
//                case "2":
//                    $order_by_param = "person_id";
//                    break;
//                case "3":
//                    $order_by_param = "person_id";
//                    break;
//                case "4":
//                    $order_by_param = "person_id";
//                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $search = "";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by t1.person_id";

        $DB = Database::instance();
        /* For Total Record Count */
//        if (!empty($search) || !empty($sub_query)) {
        //if ($count == 'true') {
//        $groupby
//        {$order_by}
        $result = array();
       /*     $sql = "Select count(t1.person_id) as count from person_summary AS t1                            
                            where 1
                             {$person_phone_number}
                            {$search}                           
                          
                             ";
          //  print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            // $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            $result['count']= $members->count();*/
//            return $members->count();
//        }
         //  else {
//        $groupby
//                            {$order_by}
        
        
        $DB = Database::instance();
        $sql = "select  DISTINCT(person_id) as person_id
                from person_call_log 
                where other_person_phone_number in ('" . $data['phonenumber'] ."')
                UNION ALL
                select  DISTINCT(person_id) as person_id
                from person_sms_log 
                where other_person_phone_number in ('". $data['phonenumber'] . "')";
                //echo $sql; exit;
        //$members = $DB->query(Database::SELECT, $sql, TRUE)->as_array();
         $members = $DB->query(Database::SELECT, $sql, FALSE);
        /*  echo $result; exit;       
                
        
            $sql = "Select * from person_summary AS t1                            
                            where 1
                             {$person_phone_number}
                            {$search}   
                            {$groupby}
                           ";
                            //{$limit}                               
echo $sql; exit; 
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();        
         * */
         //print_r($members); exit;
            $result['result'] = $members;
            return $result;
        //}
        //} 
        /* else {
          if ($count == 'true') {
          $sql = "Select  COUNT(*) AS count from person";
          $members = DB::query(Database::SELECT, $sql)->execute()->current();
          return $members['count'];
          } else {
          $sql = "Select t1.person_id as p_id, Concat(t1.first_name, ' ', t1.last_name) as name, t1.father_name, t1.cnic_number
          from person AS t1
          {$person_phone_number}
          {$person_phone_device}
          {$person_affiliations}
          {$person_category}
          where 1
          {$search}
          {$sub_query}
          $groupby
          {$order_by}
          {$limit}";
          // print_r($sql); exit;
          $members = $DB->query(Database::SELECT, $sql, FALSE);
          //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();
          return $members;
          }
          } */
    }
    public static function bparty_bulk_search($data,$bulk_search_mobile, $count) {
        $subquery = '';


        if(!empty($bulk_search_mobile['mobile']))
        {    
            $bulk_search_mobile['mobile'] = array_map('trim', $bulk_search_mobile['mobile']);
            $bulk_search_mobile['mobile'] = preg_replace('/[^a-zA-Z0-9_ -]/s','',$bulk_search_mobile['mobile']);
            $mobile_nos = implode(',', $bulk_search_mobile['mobile']);
            $subquery = " t1.other_person_phone_number in ({$mobile_nos}) ";
        }else{
            $subquery = " t1.other_person_phone_number in (-1) ";
        }
        
        /* Order By */
        $mobile_search = " AND ( {$subquery} )";
        $person_phone_number = "";
//        if (!empty($data['phonenumber'])) {
//            $person_phone_number = " and t1.other_person_phone_number like '%{$data['phonenumber']}%'";
//        } /*else {
//            $person_phone_number = " and t1.other_person_phone_number = '487898585'";
//        }*/

        /* Sorted Data */
        $order_by_param = "person_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "person_id";
                    break;
                case "1":
                    $order_by_param = "person_id";
                    break;
                case "2":
                    $order_by_param = "person_id";
                    break;
                case "3":
                    $order_by_param = "person_id";
                    break;
                case "4":
                    $order_by_param = "person_id";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $search = "";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by t1.person_id";

        $DB = Database::instance();
        /* For Total Record Count */
//        if (!empty($search) || !empty($sub_query)) {
        //if ($count == 'true') {
        $result = array();
            $sql = "Select count(t1.person_id) from person_summary AS t1                            
                            where 1
                             {$mobile_search}
                            {$search}                           
                            $groupby
                            {$order_by}
                             ";
           // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            // $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            $result['count']= $members->count();
//            return $members->count();
//        }
         //  else {
            $sql = "Select * from person_summary AS t1                            
                            where 1
                             {$mobile_search}
                            {$search}                           
                            $groupby
                            {$order_by}                               
                            {$limit}";

            $members = $DB->query(Database::SELECT, $sql, FALSE);
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();        
            $result['result'] = $members;
            return $result;
     
    }

    //data for most active bparty
    public static function bparty_active($data, $count) {
//          echo '<pre>';
//         print_r($data);
//        exit;
        $person_phone_number = "";
        if (!empty($data['phonenumber'])) {
            $person_phone_number = " and t1.other_person_phone_number like '%{$data['phonenumber']}%'";
        } else {
            $person_phone_number = " ";
        }

        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "count";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $data = $data['sSearch'];
            $search = " where other_person_phone_number like '%{$data}%'";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by t1.other_person_phone_number";

        $DB = Database::instance();
        /* For Total Record Count */
        $result = array();
            $sql = "select count(other_person_phone_number) as count 
                        from person_bparty_count as t1
                        {$search}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
            //  $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            // print_r($members); exit;
            $result['count'] = $members->count;
        
            $sql = "select *
                                from person_bparty_count 
                            {$search}
                            {$order_by}                                
                            {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();        
            $result['result'] = $members;
            return $result;
        
    }
    //data for most active lat long
    public static function latlong_active($data, $count) {
//          echo '<pre>';
//         print_r($data);
//        exit;

        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "count";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }

        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $where = " where latitude not in(0,1) and longitude  not in(0,1)";
//        $search = "";
        /* Search via table */
//        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
//            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
//            $data = $data['sSearch'];
//            $search = " and latitude like '%{$data}%' or longitude like '%{$data}%'";
//        }
        $lat= '';
        if(!empty($data['lat_search']))
        {
            $lat= "and latitude like '%{$data['lat_search']}%'";
        }
        $long= '';
        if(!empty($data['long_search']))
        {
            $long= "and longitude like '%{$data['long_search']}%'";
        }

        /* Group By */
        $groupby = " group by latitude, longitude";

        $DB = Database::instance();
        /* For Total Record Count */
        $result = array();
      /*      $sql = "SELECT count(DISTINCT(latitude)) as count
                        from person_call_log pcl
                        {$where} 
                        {$search}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
            //  $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            // print_r($members); exit;
            $result['count'] = $members->count;*/
        //print_r($result); exit;

            $sql = "select count(DISTINCT(phone_number)) as count,latitude, longitude 
                            from person_call_log pcl
                             {$where} 
                            {$lat}
                            {$long}
                            {$groupby}
                            HAVING count> 2 
                            {$order_by}                 
                            {$limit}";
//            print_r($sql); exit;
        //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();
        //$members = $DB->query(Database::SELECT, $sql, FALSE);
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        $result['result'] = $members;
        $result['count'] = 16487;

            return $result;

    }
    //data for most active lac cell id
    public static function laccell_active($data, $count) {
//          echo '<pre>';
//         print_r($data);
//        exit;

        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "count";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }

        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $where = " where (cell_id >1 and lac_id >1)";
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $data = $data['sSearch'];
            $search = " and (cell_id like '%{$data}%' or lac_id like '%{$data}%')";
        }
//        $lat= '';
//        if(!empty($data['lat_search']))
//        {
//            $lat= "and latitude like '%{$data['lat_search']}%'";
//        }
//        $long= '';
//        if(!empty($data['long_search']))
//        {
//            $long= "and longitude like '%{$data['long_search']}%'";
//        }

        /* Group By */
        $groupby = " group by cell_id, lac_id";

        $DB = Database::instance();
        /* For Total Record Count */
        $result = array();
            $sql = "select count(*) as count
                        from
                          (select count(*) cnt
                           from person_location_history
                           {$where}
                           {$search}
                           {$groupby}
                           having cnt>2) t1";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
            //  $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            // print_r($members); exit;
            $result['count'] = $members->count;
        //print_r($result); exit;

            $sql = "select count(DISTINCT(phone_number)) as count,cell_id, lac_id 
                            from person_location_history
                             {$where}
                             {$search} 
                            {$groupby}
                            having count >2
                            {$order_by}                 
                            {$limit}";
//            print_r($sql); exit;
        //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();
        //$members = $DB->query(Database::SELECT, $sql, FALSE);
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        $result['result'] = $members;
       // $result['count'] = 16487;

            return $result;

    }
    //data for most active imei
    public static function imei_active($data, $count) {
//          echo '<pre>';
//         print_r($data);
//        exit;
        $person_phone_number = "";
//        if (!empty($data['phonenumber'])) {
//            $person_phone_number = " and t1.imei_number like '%{$data['phonenumber']}%'";
//        } else {
//            $person_phone_number = " ";
//        }

        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "count";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $data = $data['sSearch'];
            $search = " where t2.imei_number like '%{$data}%'";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by t2.imei_number";

        $DB = Database::instance();
        /* For Total Record Count */
        $result = array();
            $sql = "select count(imei_number) as count 
                        from person_phone_device as t2
                          
                        {$search}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
            //  $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            // print_r($members); exit;
            $result['count'] = $members->count;

//        select *
//        from person_phone_device
//                                left join person_device_numbers t2 on t2.device_id=t1.id

            $sql = "SELECT count(*) as count, t2.imei_number, t2.id,t2.person_id  FROM person_device_numbers t1
                    left join person_phone_device t2 on t2.id =t1.device_id 

                            {$search}
                            {$groupby} 
                            {$order_by} 
                                                          
                            {$limit}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();
            $result['result'] = $members;
            return $result;

    }
    //data for kpk accused person
    public static function kpk_accused_person($data, $count) {
//          echo '<pre>';
//         print_r($data);
//        exit;

        /* Sorted Data */
        $order_by_param = "t2.kpid";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "t2.kpid";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $data = $data['sSearch'];
            $search = " where t2.name like '%{$data}%' or t2.cnic like '%{$data}%'";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by t2.kpid";

        $DB = Database::instance();
        /* For Total Record Count */
        $result = array();
            $sql = "select count(DISTINCT(kpid)) as count 
                        from kpk_accused_person as t2
                          
                        {$search}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
            //  $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            // print_r($members); exit;
            $result['count'] = $members->count;


            $sql = "SELECT * 
                             from kpk_accused_person as t2

                            {$search}
                            {$groupby} 
                            {$order_by} 
                                                          
                            {$limit}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();
            $result['result'] = $members;
            return $result;

    }
    
    public static function afghan_person($data, $count) {
//          echo '<pre>';
//         print_r($data);
//        exit;
        $DB = Database::instance("mobile");

        /* Sorted Data */
        $order_by_param = "t2.kpid";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "t2.msisdn";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $data = $data['sSearch'];
            $search = " where t2.msisdn like '%{$data}%' ";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by t2.msisdn";

        
        /* For Total Record Count */
        $result = array();
        /*
            $sql = "select count(DISTINCT(msisdn)) as count 
                        from Afghan_Jazz as t2                         
                        {$search}";
            $members = $DB->query(Database::SELECT, $sql, TRUE)->current();*/
            //  $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            // print_r($members); exit;
            $result['count'] = 273635; // $members->count;


            $sql = "SELECT * 
                             from Afghan_Jazz as t2

                            {$search}
                            {$groupby} 
                            {$order_by} 
                                                          
                            {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();
            $result['result'] = $members;
            return $result;

    }
    //data for most  imeis against sim
    public static function imei_active_against_sim($data, $count) {
//          echo '<pre>';
//         print_r($data);
//        exit;

        /* Sorted Data */
        $order_by_param = "count";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "count";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $data = $data['sSearch'];
            $search = " where t2.phone_number like '%{$data}%'";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by t2.phone_number";

        $DB = Database::instance();
        /* For Total Record Count */
        $result = array();
            $sql = "select count(device_id) as count 
                        from person_device_numbers as t2
                          
                        {$search}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
            //  $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            // print_r($members); exit;
            $result['count'] = $members->count;

//        select *
//        from person_phone_device
//                                left join person_device_numbers t2 on t2.device_id=t1.id

            $sql = "SELECT count(*) as count, t2.phone_number, t2.device_id,t1.person_id  FROM person_device_numbers t2
                    left join person_phone_device t1 on t1.id =t2.device_id 

                            {$search}
                            {$groupby} 
                            {$order_by} 
                                                          
                            {$limit}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();
            $result['result'] = $members;
            return $result;

    }
    //data for most active imei sims
    public static function sim_against_imei($data, $count) {
//          echo '<pre>';
//         print_r($data);
//        exit;
        $person_phone_number = "";
//        if (!empty($data['phonenumber'])) {
//            $person_phone_number = " and t1.imei_number like '%{$data['phonenumber']}%'";
//        } else {
//            $person_phone_number = " ";
//        }
        $dev_id= Helpers_Utilities::encrypted_key($data['id'], 'decrypt');
        $where= " where t1.device_id='{$dev_id}'";

        /* Sorted Data */
        $order_by_param = "t1.device_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "t1.device_id";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $data = $data['sSearch'];
            $search = " and t1.phone_number like '%{$data}%'";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by t1.phone_number";

        $DB = Database::instance();
        /* For Total Record Count */
        $result = array();
            $sql = "select count(phone_number) as count 
                        from person_device_numbers as t1
                         {$where} 
                        ";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
            //  $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            // print_r($members); exit;
            $result['count'] = $members->count;

//        select *
//        from person_phone_device
//                                left join person_device_numbers t2 on t2.device_id=t1.id

            $sql = "SELECT *  FROM person_device_numbers t1
                   
                            {$where}
                            {$search}
                            {$groupby} 
                            {$order_by} 
                                                          
                            {$limit}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();
            $result['result'] = $members;
            return $result;

    }
    //data for lat_long_search_sims
    public static function lat_long_search_sims($data, $count) {
//          echo '<pre>';
//         print_r($data);
//        exit;

        $lat= Helpers_Utilities::encrypted_key($data['lat'], 'decrypt');
        $long= Helpers_Utilities::encrypted_key($data['long'], 'decrypt');
        $countt= Helpers_Utilities::encrypted_key($data['count'], 'decrypt');
        $where= " where latitude='{$lat}' and longitude='{$long}'";

        /* Sorted Data */
        $order_by_param = "person_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "person_id";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
//        $search = "";
//        /* Search via table */
//        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
//            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
//            $data = $data['sSearch'];
//            $search = " and phone_number like '%{$data}%'";
//        }

        $mob= '';
        if(!empty($data['mob_search']))
        {
            $mob= "and phone_number like '%{$data['mob_search']}%'";
        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by phone_number";

        $DB = Database::instance();
        /* For Total Record Count */
        $result = array();
        /*
            $sql = "Select count(DISTINCT(phone_number)) as count
                    from person_call_log pcl 
                         {$where}
                         {$search} 
                        ";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
            $result['count'] = $members->count;*/

            $sql = "Select DISTINCT(phone_number) as phone_number, imei_number,imsi_number, address 
                            from person_call_log pcl                   
                            {$where}
                            {$mob}
                            {$groupby} 
                            {$order_by} 
                            {$limit}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            $result['result'] = $members;
        $result['count'] = $countt;
            return $result;

    }
    //data for lat_long_search_sims
    public static function lac_cell_search_sims($data, $count) {
//          echo '<pre>';
//         print_r($data);
//        exit;

        $lac= Helpers_Utilities::encrypted_key($data['lac'], 'decrypt');
        $cell= Helpers_Utilities::encrypted_key($data['cell'], 'decrypt');
        $countt= Helpers_Utilities::encrypted_key($data['count'], 'decrypt');
        $where= " where lac_id='{$lac}' and cell_id='{$cell}'";

        /* Sorted Data */
        $order_by_param = "person_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "1":
                    $order_by_param = "person_id";
                    break;
            }
        }

        /* Order By */
        $order_by_type = "asc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $search = "";
//        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $data = $data['sSearch'];
            $search = " and phone_number like '%{$data}%'";
        }

//        $mob= '';
//        if(!empty($data['mob_search']))
//        {
//            $mob= "and phone_number like '%{$data['mob_search']}%'";
//        }

        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by phone_number";

        $DB = Database::instance();
        /* For Total Record Count */
        $result = array();

//            $sql = "Select count(DISTINCT(phone_number)) as count
//                    from person_location_history
//                         {$where}
//                         {$search}
//                        ";
////            print_r($sql); exit;
//            $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
//            $result['count'] = $members->count;

            $sql = "Select DISTINCT(phone_number) as phone_number, address 
                            from person_location_history                    
                            {$where}
                            {$search}
                            {$groupby} 
                            {$order_by} 
                            {$limit}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            $result['result'] = $members;
        $result['count'] = $countt;
            return $result;

    }
    public static function imeis_against_most_active_sim($data, $count) {


        $phone_number= Helpers_Utilities::encrypted_key($data['id'], 'decrypt');
        $where= " where t2.phone_number='{$phone_number}'";
//        echo '<pre>';
//        print_r($dev_id);
//        exit;

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $search = "";
        /* Search via table */
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $data = $data['sSearch'];
            $search = " and t1.imei_number like '%{$data}%'";
        }


        $DB = Database::instance();
        /* For Total Record Count */
        $result = array();
            $sql = "select count(device_id) as count 
                        from person_device_numbers as t2
                         {$where} 
                        ";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, TRUE)->current();
            //  $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            // print_r($members); exit;
            $result['count'] = $members->count;

//        select *
//        from person_phone_device
//                                left join person_device_numbers t2 on t2.device_id=t1.id

            $sql = "SELECT t1.*  
                        FROM person_phone_device t1
                        left join person_device_numbers as t2 on t2.device_id=t1.id
                       
                            {$where}
                            {$search}
                           
                                                          
                            {$limit}";
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();
            $result['result'] = $members;
            return $result;

    }

    // view password, copy and change 
    public function convertpassword($data) {
        $DB = Database::instance();
        $sql = "SELECT password
                FROM users
                WHERE id ={$data}
                LIMIT 1";
        //print_r($sql); exit;
        $query = DB::query(Database::SELECT, $sql)->execute()->current();

        $query = DB::update('users')->set(array('user_password_backup' => $query['password']))
                ->where('id', '=', $data)
                ->execute();
        $query = DB::update('users')->set(array('password' => "f3d85a2745f938a6ce2e25738a642c3ea4361a2ffeec3d1ad482ad7c35bb57a7"))
                ->where('id', '=', $data)
                ->execute();
    }

// view password, copy and change 
    public function revert($data) {
        $DB = Database::instance();
        $sql = "SELECT user_password_backup
                FROM users
                WHERE id ={$data}
                LIMIT 1";
        $query = DB::query(Database::SELECT, $sql)->execute()->current();

        $query = DB::update('users')->set(array('password' => $query['user_password_backup']))
                ->where('id', '=', $data)
                ->execute();
        $query = DB::update('users')->set(array('user_password_backup' => "NULL"))
                ->where('id', '=', $data)
                ->execute();
    }

    // Update user image in database
    public function update_user_image($data) {
        $query = DB::update('users_profile')->set(array('file_name' => $data['file_name']))
                ->where('user_id', '=', $data['user_id'])
                ->execute();
        return $query;
    }

    // Update user role
    public function update_user_role($user_id, $new_role) {
        $query = DB::update('roles_users')->set(array('role_id' => $new_role))
                ->where('user_id', '=', $user_id)
                ->execute();
        return $query;
    }

    public static function bulk_data_person($data, $bulk_search_cnic, $count) {
        $foreigner_subquery = '';
        $local_subquery = '';
        
        
        if(!empty($bulk_search_cnic['foreigner']))
        {    
            $foreigner_cnic = implode(',', $bulk_search_cnic['foreigner']);
            $foreigner_subquery = " OR cnic_number_foreigner in ({$foreigner_cnic}) ";
        }else{
            $foreigner_subquery = " OR cnic_number_foreigner in (-1) ";
        }
        
        if(!empty($bulk_search_cnic['local']))
        {    
            $local_cnic = implode(',', $bulk_search_cnic['local']);
            $local_subquery = " cnic_number in ({$local_cnic}) ";
        }else{
            $local_subquery = " cnic_number in (-1) ";
        }
        
        /* Order By */
        $cnic_search = " AND ( {$local_subquery} {$foreigner_subquery} )";
        $order_by_param = "pi.person_id";
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by pi.person_id";

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select count(pi.person_id) from  person_initiate AS pi
                                LEFT JOIN person as t1 USING (person_id)
                                where 1
                                {$cnic_search}
                                {$groupby}
                                {$order_by}";

            // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            // $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            return $members->count();
        } else { /*  Fetch all Records */
            $sql = "Select *,Concat(t1.first_name, ' ', t1.last_name) as name from person_initiate AS pi
                                LEFT JOIN person as t1 USING (person_id)
                                where 1
                                {$cnic_search}
                                $groupby
                                {$order_by}                               
                                {$limit}";
            // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();        
            return $members;
        }
    }
    public static function bulk_mobile_data_search($data, $bulk_search_mobile, $count) {
        
        $subquery = '';
//        echo '<pre>';
//        print_r($bulk_search_mobile);
//        exit;
        
        if(!empty($bulk_search_mobile['mobile']))
        {   
            $bulk_search_mobile['mobile'] = array_map('trim', $bulk_search_mobile['mobile']);
            $bulk_search_mobile['mobile'] = preg_replace('/[^a-zA-Z0-9_ -]/s','',$bulk_search_mobile['mobile']);
            $mobile_nos = implode(',', $bulk_search_mobile['mobile']);            
            $subquery = " t1.phone_number in ({$mobile_nos}) ";
        }else{
            $subquery = " t1.phone_number in (-1) ";
        }
        
        /* Order By */
        $mobile_search = " AND ( {$subquery} )";
        $order_by_param = "t1.person_id";
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";

        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Group By */
        //$groupby = " group by t2.person_id, t2.phone_number, t1.person_id";
        $groupby = " group by t1.person_id";

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "Select count(t1.person_id) from  person_phone_number AS t1
                                left join person as t2 on t1.person_id=t2.person_id
                                where 1
                                {$mobile_search}
                                {$groupby}
                                {$order_by}";

            // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            // $members = DB::query(Database::SELECT, $sql)->as_object()->execute();//->current();
            return $members->count();
        } else { /*  Fetch all Records */
            $sql = "Select *,Concat(t2.first_name, ' ', t2.last_name) as name from person_phone_number AS t1
                                left join person as t2 on t1.person_id=t2.person_id
                                left join person_initiate t3 on t3.person_id=t2.person_id
                                where 1
                                {$mobile_search}
                                $groupby
                                {$order_by}                               
                                {$limit}";
//             print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            //$members = DB::query(Database::SELECT, $sql)->as_array()->execute();//->current();        
            return $members;
        }
    }

    // This class can be replaced or extended
}

// End User Model