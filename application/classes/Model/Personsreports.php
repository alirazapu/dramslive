<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with email template   
 */
class Model_Personsreports {
   /* Person List Ajax Call Data */
     public function person_list($data, $count) {
         $where_posting = " ";
         $where_region = " ";
         /*Advance Search by Region*/
         if (!empty($data['reg'])) {
             $reg_id= Helpers_Utilities::encrypted_key($data['reg'], 'decrypt');
             $result1 = explode('-', $reg_id);
            // $posting = implode("','", $data['posting']);
             $where_region = "and u2.region_id ='{$result1[1]}' ";
         }
         /*Advance Search by posting*/
         if (!empty($data['posting'])) {
             $posting = implode("','", $data['posting']);
             $where_posting = "and u2.posted IN ( '{$posting}' ) ";
         }
         /* Posted Data */   
         if (isset($data['category'])) {
             $category_id= Helpers_Utilities::encrypted_key($data['category'], 'decrypt');
             $where_category = "and category_id = {$category_id}";
         } else {
             $where_category = '';
         }
         if (isset($data['project_id'])) {
             $project_id= Helpers_Utilities::encrypted_key($data['project_id'], 'decrypt');
             $where_project = "and project_id = {$project_id}";
         } else {
             $where_project = '';
         }
        /* Sorted Data */
        $order_by_param = "added_on";
        if(isset($data['iSortCol_0'])){
            switch ($data['iSortCol_0']){
                case "0":
                    $order_by_param = "added_on";
                    break;
                case "1":
                    $order_by_param = "added_on";
                    break;
                case "2":
                    $order_by_param = "added_on";
                    break; 
                case "3":
                    $order_by_param = "added_on";
                    break;  
                case "4":
                    $order_by_param = "added_on";
                    break;  
                case "5":
                    $order_by_param = "added_on";
                    break;  
                
            }
        }        
        
        /* Order By */
        $order_by_type = "desc";
        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit= "";
        /* Starting and Ending Lenght (size) */
        if(isset($data['iDisplayStart']) && isset($data['iDisplayLength'])){
            $limit= " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        
        /* Search via table */
        
        if(isset($data['sSearch']) && !empty($data['sSearch'])){
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\ ]/', '', $data['sSearch']);
            $DB = Database::instance();
            $sub_query = '';
            $sub_query_person = '';
                $sub_query .= "where ( (CONCAT(TRIM(first_name), ' ', TRIM(last_name))  like '%{$data['sSearch']}%')  )";
                $sub_query_person .= "where ( (CONCAT(TRIM(p.first_name), ' ', TRIM(p.last_name))  like '%{$data['sSearch']}%') OR (pi.cnic_number like '%{$data['sSearch']}%') OR (pi.cnic_number_foreigner like '%{$data['sSearch']}%') )";
            
            $sql = "SELECT  user_id
                                    FROM  users_profile
                                    {$sub_query}";
            $user_id = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $users_array = implode(', ', array_values(array_column($user_id, 'user_id')));
            if (!empty($users_array)){
                $search = " OR  u1.user_id in ({$users_array}))";          
            }else {
                    $search = " OR u1.user_id = null)";
                } 
                
                //search from persons
            $sql = "SELECT  person_id
                                    FROM  person as p
                                    inner join person_initiate as pi using(person_id)
                                    {$sub_query_person}";
           // print_r($sqlperson);
            $person_ids = DB::query(Database::SELECT, $sql)->execute()->as_array();
            $persons_array = implode(', ', array_values(array_column($person_ids, 'person_id')));
           // print_r($persons_array);
            if (!empty($persons_array)){
                $search_person = " and  (person_id in ({$persons_array})";          
            }else {
                    $search_person = "and (person_id = null";
                }    
                
                
             //  print_r($search_person); 
                
        }
        else {
            $search = "";
            $search_person = "";
        }
                
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = 'where 1';
        if ($permission == 3 || $permission == 2) {
            $result = explode('-', $posting);
            switch ($result[0]) {
                case 'h':
                    $where_clause = "where 1";
                    break;
                case 'r':
                    $where_clause = "where u1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.region_id = $result[1] ) ";
                    break;
                case 'd':
                    $where_clause = "where u1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='d-$result[1]' )";
                    break;
                case 'p':
                    $where_clause = "where u1.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted = 'p-$result[1]' )";
                    break;
            }
        }
        elseif ($permission == 4) {
            $where_clause = "where u1.user_id = {$login_user->id}";
        }
        
        $DB = Database::instance();   
        /* For Total Record Count */
        if($count=='true')
        {      $sql = "Select  COUNT(*) AS count
                            from person_category as u1
                            left join users_profile as u2 on u2.user_id = u1.user_id
                            {$where_clause}
                            {$where_category}
                            {$where_project}
                            {$search_person}
                            {$search}
                            {$where_posting}
                            {$where_region}";
//      print_r($sql); exit;
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        return $members['count'];
        }
        /*  Fetch all Records */
        else {
            $sql = "Select *   from person_category as u1
                    left join users_profile as u2 on u2.user_id = u1.user_id
                                {$where_clause}
                                {$where_category}
                                {$where_project}
                                {$search_person}
                                {$search}
                                {$where_posting}
                                {$where_region}                                    
                                {$order_by}    
                                {$limit}";                          
                            
           // print_r($sql); exit;
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        return $members;
        }
    }
    public function person_call_analysis($data, $count) {
        /* Sorted Data */
        $order_by_param = "added_on";
        if(isset($data['iSortCol_0'])){
            switch ($data['iSortCol_0']){
                case "0":
                    $order_by_param = "id";
                    break;
            }
        }
        /* Order By */
        $order_by_type = "asc";
        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit= "";
        /* Starting and Ending Lenght (size) */
        if(isset($data['iDisplayStart']) && isset($data['iDisplayLength'])){
            $limit= " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        $DB = Database::instance();
        /* For Total Record Count */
        if($count=='true')
        {      $sql = "Select  COUNT(*) AS count
                            from call_analysis as u1
                          ";
      //print_r($sql); exit;
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        return $members['count'];
        }
        /*  Fetch all Records */
        else {
            $sql = "Select *   
                            from call_analysis as u1
                             {$order_by}    
                             {$limit}";
           // print_r($sql); exit;
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        return $members;
        }
    }
   
    /*Project Affiliated persons data */
     public function project_persons($data, $count)
     {
         //print_r($data); exit;
         /* Posted Data */
         if (isset($data['category'])) {
             $where_category = "and category_id = {$data['category']}";
         } else {
             $where_category = '';
         }


         /* Sorted Data */
         $order_by_param = "id";
         if (isset($data['iSortCol_0'])) {
             switch ($data['iSortCol_0']) {
                 case "0":
                     $order_by_param = "project_name";
                     break;
                 case "1":
                     $order_by_param = "region_id";
                     break;
                 case "3":
                     $order_by_param = "project_status";
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
         $limit = "";
         /* Starting and Ending Lenght (size) */
         if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
             $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
         }

         /* Search via table */

         if (isset($data['sSearch']) && !empty($data['sSearch'])) {
             $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\ ]/', '', $data['sSearch']);
             $DB = Database::instance();
             $sub_query = '';
             $parts = preg_split('/\s+/', $data['sSearch']);
             if (sizeof($parts) >= 2) {
                 $search = "and (project_name like '%{$parts[0]}%' or project_name like '%{$parts[1]}%')";
             } else {
                 $search = "and (project_name like '%{$data['sSearch']}%')";
             }
         } else {
             $search = "";
         }

         $login_user = Auth::instance()->get_user();
         $DB = Database::instance();
         $permission = Helpers_Utilities::get_user_permission($login_user->id);
         $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
         $posting_region = $login_user_profile->region_id;
         $posting = $login_user_profile->posted;
         $result = explode('-', $posting);
         $where_clause = 'where 1 ';
         if ($posting_region != 11 ||  !empty($data['field'])) {
             switch ($data['field']) {
                 case "def":
                     $data['field'] = '';
                     break;


                 case "projectname":
                     $where_clause .= " and u1.project_name  like '%{$data['key']}%' ";
                     break;
                 /*case "username":
                     $data['field'] = "and u1.region_id  like '%{$data['key']}%' ";
                     break;*/

                 default:

                     $where_clause .= " and u1.region_id = {$posting_region} and u1.district_id = {$result[1]}";
                     break;
             }

         }
         /* Posted Data */

        $DB = Database::instance();   
        /* For Total Record Count */
        if($count=='true')
        {      $sql = "Select  COUNT(*) AS count
                            from int_projects as u1 
                     
                            {$where_clause}
                            {$where_category}
                            {$search}";

        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        return $members['count'];
        }
        /*  Fetch all Records */
        else {
            $sql = "Select *     from int_projects as u1

                                {$where_clause}
                                {$where_category}
                                {$search}                                    
                                {$order_by}    
                                {$limit}";
           // print_r($sql); exit;
        //   print_r($sql); exit;
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        return $members;
        }
    }
    /* Top Search persons Ajax Call Data */
     public static function top_search_persons($data, $count) {
         /* Posted Data */
         $join_query='';
         $join_query_and = '';
        if (empty($data['field']))
            $data['field'] = '';
        else
        {
            switch ($data['field']){
                case "name":
                    $data['field'] = "and ( CONCAT(u1.first_name, ' ', TRIM(u1.last_name)) like '%{$data['key']}%' )";
                    break;
                case "father_name":
                    $data['field'] = "and u1.father_name like '%{$data['key']}%' ";
                    break;
                case "cnic_number":
                    $data['field'] = "and t1.cnic_number like '%{$data['key']}%' or t1.cnic_number_foreigner like '%{$data['key']}%'";
                    break;
                case "address":
                    $data['field'] = "and u1.address like '%{$data['key']}%' ";
                    break;
                case "category":
                    switch ($data['category']) {
                        case 0:
                            $join_query = 'join person_category u2 on u1.person_id = u2.person_id';
                            $data['field'] = 'and u2.category_id = 0';
                            break;
                        case 1:
                            $join_query = 'join person_category u2 on u1.person_id = u2.person_id';
                            $data['field'] = 'and u2.category_id = 1';
                            break;
                        case 2:
                            $join_query = 'join person_category u2 on u1.person_id = u2.person_id';
                            $data['field'] = 'and u2.category_id = 2';
                            break;                        
                        default:
                            $join_query = '';
                            $data['field'] = '';
                    }
                    break;
            }
        }    
        
        /* Sorted Data */
        $order_by_param = "maxtotal";
        if(isset($data['iSortCol_0'])){
            switch ($data['iSortCol_0']){
                case "0":
                    $order_by_param = "first_name";
                    break; 
                case "5":
                    $order_by_param = "maxtotal";
                    break;
                
            }
        }        
        
        /* Order By */
        $order_by_type = "desc";
        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        
        $limit= "";
        /* Starting and Ending Lenght (size) */
        if(isset($data['iDisplayStart']) && isset($data['iDisplayLength'])){
            $limit= " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        
        /* Search via table */
        
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(u1.first_name, ' ', TRIM(u1.last_name)) like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }
        
        /* Group By */
        $groupby = "group by u1.person_id";
        //$order_by=" order by maxtotal desc";
        
        $DB = Database::instance();   
        /* For Total Record Count */
        if($count=='true')
        {      $sql = "Select  COUNT(*) AS count
                            from person as u1
                            join person_initiate as t1 on t1.person_id = u1.person_id
                            $join_query
                            where u1.is_deleted = 0                                                                                   
                            $join_query_and
                            {$search}
                            {$data['field']}
                           
                            ";
        
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        return $members['count'];
        }
        /*  Fetch all Records */
        /*
         SELECT t1.person_id AS PID, COUNT(t1.person_id) AS TOTAL
                FROM user_activity_timeline as t1 
                WHERE t1.user_activity_type_id = 3 
        */
        else {
            $sql = "Select *, u1.view_count as maxtotal
                                from person as u1   
                                join person_initiate as t1 on t1.person_id = u1.person_id
                                $join_query
                                where u1.is_deleted = 0   
                                $join_query_and
                                {$search}
                                {$data['field']}
                                {$groupby} 
                                {$order_by}    
                                {$limit}"; 
            //print_r($sql); exit;
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        return $members;
        }
    }    
    public static function update_person_status($stat) {
        //print_r($stat); exit;
        $chkcat = Helpers_Person::get_person_category_id($stat['person_id']);
        //print_r($chkcat); exit;
        $added_on = date("Y-m-d H:i:s");
        if ($chkcat == 9) {
            $query = DB::insert('person_category', array('person_id', 'category_id','project_id','reason',  'user_id','added_on'))
                    ->values(array($stat['person_id'], $stat['category'],$stat['inputproject'][0],$stat['inputreason'], $stat['user_id'], $added_on))
                    ->execute();
            //return $query;
        } else {  
            if ($chkcat != 9 && $chkcat < $stat['category'] ) {
            $query = DB::update('person_category')->set(array('category_id' => $stat['category'],'project_id' => $stat['inputproject'][0],'reason'=>$stat['inputreason'], 'added_on' => $added_on, 'user_id' => $stat['user_id']))
                    ->where('person_id', '=', $stat['person_id'])
                    ->execute();
            $login_user = Auth::instance()->get_user();
            $uid = $login_user->id;
            Helpers_Profile::user_activity_log($uid, 11 ,$chkcat , $stat['category'],$stat['person_id'] );
            //return $query;
            }
        }
        //category change history maintain
        $query2 = DB::insert('person_category_history', array('person_id','old_category_id', 'new_category_id','project_id','reason',  'user_id','added_on'))
                    ->values(array($stat['person_id'],$chkcat, $stat['category'],$stat['inputproject'][0],$stat['inputreason'], $stat['user_id'], $added_on))
                    ->execute();
    }

    public static function insert_users_feedback($uid, $pid, $feedback) {
        $added_on = date("Y-m-d H:i:s");
        $query = DB::insert('users_feedback', array('user_id','person_id', 'added_on','feedback'))                
                ->values(array($uid, $pid, $added_on, $feedback))                
                ->execute();
                return $query;
    }

  /* Person Devices */
    public static function person_devices($data, $count , $pid) {
        /* Sorted Data */
        $order_by_param = "t2.phone_number";
        if(isset($data['iSortCol_0'])){
            switch ($data['iSortCol_0']){
                case "0":
                    $order_by_param = "t1.phone_name";
                    break;
                case "1":
                    $order_by_param = "t1.imei_number";
                    break; 
                case "2":
                    $order_by_param = "phonenumber";
                    break; 
                case "3":
                    $order_by_param = "in_use_since";
                    break; 
                case "4":
                    $order_by_param = "last_interaction_at";
                    break;  
            }
        }      
        
        /* Order By */
        $order_by_type = "desc";
        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){            
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        
        /* Starting and Ending Lenght (size) */
        if(isset($data['iDisplayStart']) && isset($data['iDisplayLength'])){
            $limit= " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        
        if(isset($data['sSearch']) && !empty($data['sSearch'])){
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\ ]/', '', $data['sSearch']);
            $search = "and (t1.phone_name like '%{$data['sSearch']}%' "
                            . "or t1.imei_number like '%{$data['sSearch']}%' "
                            . "or t2.phone_number like '%{$data['sSearch']}%' )";
        }
        else {
            $search = "";
        }
        $DB = Database::instance();   
        /* For Total Record Count */
        if($count=='true')
        {   
            $sql = "SELECT COUNT(*) as count FROM (
                SELECT t1.id, t2.phone_number
                   FROM person_phone_device t1 
                   INNER JOIN person_device_numbers t2 ON t1.id = t2.device_id 
                   INNER JOIN person_phone_number as t3 ON t2.phone_number = t3.phone_number 
                   WHERE t3.person_id=$pid    
                        {$search}
                   GROUP BY t1.id, t2.phone_number
                ) as device_count";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */
        else {
            $sql = "SELECT t1.phone_name, t1.imei_number,
                        MIN(t2.first_use) as in_use_since,
                        MAX(t2.last_use) as last_interaction_at,
                        t2.phone_number as phonenumber, t1.id as device_id, t1.person_id 
                FROM person_phone_device t1 
                INNER JOIN person_device_numbers t2 ON t1.id = t2.device_id 
                INNER JOIN person_phone_number as t3 ON t2.phone_number = t3.phone_number 
                WHERE t3.person_id=$pid
                    {$search}
                GROUP BY t1.id, t2.phone_number, t1.phone_name, t1.imei_number, t1.person_id
                    {$order_by}
                    {$limit}"; 
            $members = $DB->query(Database::SELECT, $sql, FALSE);        
            return $members;
        }
    }  
  /* Person Devices against imei */
    public static function person_imeidevices($data, $count , $imei) {
        
        /* Sorted Data */
        $order_by_param = "phonenumber"; 
        if(isset($data['iSortCol_0'])){
            switch ($data['iSortCol_0']){
                case "0":
                    $order_by_param = "t1.phonenumber";
                    break;
                case "1":
                    $order_by_param = "t3.sim_owner";
                    break; 
                case "2":
                    $order_by_param = "t3.person_id";
                    break; 
                case "3":
                    $order_by_param = "t2.first_use";
                    break; 
                case "4":
                    $order_by_param = "t2.last_use";
                    break;  
                case "5":
                    $order_by_param = "t2.phonenumber";
                    break;  
                case "6":
                    $order_by_param = "t2.phonenumber";
                    break;  
            }
        }      
        
        /* Order By */
        $order_by_type = "desc";
        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){            
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        
        /* Starting and Ending Lenght (size) */
        if(isset($data['iDisplayStart']) && isset($data['iDisplayLength'])){
            $limit= " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        
        if(isset($data['sSearch']) && !empty($data['sSearch'])){
            $search = "and (t1.phone_name like '%{$data['sSearch']}%' or phonenumber like '%{$data['sSearch']}%'  )";
        }
        else {
            $search = "";
        }
        $DB = Database::instance();   
        /* For Total Record Count */
        if($count=='true')
        {   
            $sql = "SELECT COUNT(t1.imei_number) as count
               FROM person_phone_device t1 
               INNER JOIN person_device_numbers t2 ON t1.id = t2.device_id 
               INNER JOIN person_phone_number as t3 ON t2.phone_number = t3.phone_number 
               WHERE t1.imei_number=$imei   
                    {$search}";
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */
        else {
            $sql = "SELECT t1.person_id as device_user_id,t1.phone_name,t1.imei_number,t1.in_use_since,t1.last_interaction_at,t2.first_use as sim_first_use,t2.last_use as sim_last_use,t2.phone_number as phonenumber,t1.id as device_id,t3.person_id as sim_user_id,t3.sim_owner as sim_owner_id
                FROM person_phone_device t1 
                INNER JOIN person_device_numbers t2 ON t1.id = t2.device_id 
                INNER JOIN person_phone_number as t3 ON t2.phone_number = t3.phone_number 
                WHERE t1.imei_number=$imei
                    {$search}
                    {$order_by}"; 
            $members = $DB->query(Database::SELECT, $sql, FALSE);        
            return $members;
        }
    }  
  /* Person sims against cnic */
    public static function person_simsagainstcnic($data, $count , $cnic) {
        $person_id_with_cnic=  Helpers_Utilities::get_person_id_with_cnic($cnic);
       
            $sub_table_query="FROM person as t1";
      
        /* Sorted Data */
        $order_by_param = "t2.phone_number"; 
        if(isset($data['iSortCol_0'])){
            switch ($data['iSortCol_0']){
                case "0":
                    $order_by_param = "t2.phone_number";
                    break;
                case "1":
                    $order_by_param = "t2.sim_activated_at";
                    break; 
                case "2":
                    $order_by_param = "t2.sim_last_used_At";
                    break; 
                case "3":
                    $order_by_param = "t2.status";
                    break; 
                case "4":
                    $order_by_param = "t2.company_name";
                    break;  
                case "5":
                    $order_by_param = "t1.person_id";
                    break;  
            }
        }      
        
        /* Order By */
        $order_by_type = "desc";
        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){            
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        
        /* Starting and Ending Lenght (size) */
        if(isset($data['iDisplayStart']) && isset($data['iDisplayLength'])){
            $limit= " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        
        if(isset($data['sSearch']) && !empty($data['sSearch'])){
            $search = "and (t2.phone_number like '%{$data['sSearch']}%'  )";
        }
        else {
            $search = "";
        }
        $DB = Database::instance();   
        /* For Total Record Count */
        if($count=='true')
        {   
            $sql = "SELECT COUNT(t2.phone_number) as count
                   {$sub_table_query}
                    INNER JOIN person_phone_number as t2 on t1.person_id=t2.sim_owner 
                    WHERE t1.person_id=$person_id_with_cnic  AND  t1.person_id!=0
                    {$search}";
           // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */
        else {
            $sql = "SELECT t1.person_id,t2.person_id as sim_user_id,t2.phone_number,t2.sim_activated_at,t2.sim_last_used_at,t2.status,t2.mnc,(select company_name FROM mobile_companies where mnc=t2.mnc) as company_name
                    {$sub_table_query}
                    INNER JOIN person_phone_number as t2 on t1.person_id=t2.sim_owner 
                    WHERE t1.person_id=$person_id_with_cnic AND  t1.person_id!=0
                    {$search}
                    {$order_by}"; 
            $members = $DB->query(Database::SELECT, $sql, FALSE);        
            return $members;
        }
    }  
    /* Person SIMs */
    public static function person_sims($data, $count , $pid) {
        /* Sorted Data */
        $order_by_param = "t1.person_id";
        if(isset($data['iSortCol_0'])){
            switch ($data['iSortCol_0']){
                case "0":
                    $order_by_param = "person_id";
                    break;
                case "1":
                    $order_by_param = "phone_number";
                    break; 
                case "2":
                    $order_by_param = "t1.sim_last_used_at";
                    break; 
                case "3":
                    $order_by_param = "mnc";
                    break; 
                case "4":
                    $order_by_param = "status";
                    break; 
                case "5":
                    $order_by_param = "connection_type";
                    break; 
                case "6":
                    $order_by_param = "sim_activated_at";
                    break; 
            }
        }      
        
        /* Order By */
        $order_by_type = "desc";
        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){            
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
            $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
            // $limit= " limit 10";
        }
       // print_r($data['iDisplayStart']); exit;
        /* Search via table */
        
       if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $search = "and t1.phone_number like '%{$data['sSearch']}%'";
        } else {
            $search = "";
        }
        $DB = Database::instance();   
        /* For Total Record Count */
        if($count=='true')
        {   
            $sql = "SELECT Count(*) as count 
                    FROM person_phone_number as t1 
                    WHERE (t1.person_id=$pid or t1.sim_owner=$pid)    
                    {$search}
                    ";             
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */
        else {
            $sql = "SELECT *
                    FROM person_phone_number as t1 
                    WHERE (t1.person_id=$pid or t1.sim_owner=$pid)  
                    {$search}
                    {$order_by}
                     $limit";
            $members = $DB->query(Database::SELECT, $sql, FALSE);        
            return $members;
        }
    } 
    
     /* Sensitive Person's List Ajax Call Data */
     public function sensitve_person_list($data, $count) {
        //print_r($data); exit;
        /* Sorted Data */
        $order_by_param = "added_on";
        if(isset($data['iSortCol_0'])){
            switch ($data['iSortCol_0']){
                case "0":
                    $order_by_param = "added_on";
                    break;
                case "1":
                    $order_by_param = "added_on";
                    break;
                case "2":
                    $order_by_param = "added_on";
                    break; 
                case "3":
                    $order_by_param = "added_on";
                    break;  
                case "4":
                    $order_by_param = "added_on";
                    break;  
                case "5":
                    $order_by_param = "added_on";
                    break;  
                
            }
        }        
        
        /* Order By */
        $order_by_type = "desc";
        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        $limit= "";
        /* Starting and Ending Lenght (size) */
        if(isset($data['iDisplayStart']) && isset($data['iDisplayLength'])){
            $limit= " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        
        /* Search via table */
        
        if(isset($data['sSearch']) && !empty($data['sSearch'])){
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\ ]/', '', $data['sSearch']);
                $search = "and ( (CONCAT(TRIM(u3.first_name), ' ', TRIM(u3.last_name))  like '%{$data['sSearch']}%') OR (u2.cnic_number like '%{$data['sSearch']}%') OR (u2.cnic_number_foreigner like '%{$data['sSearch']}%') )";            
//            $DB = Database::instance();
//            $sub_query = '';
//            $sub_query_person = '';
//                $sub_query .= "where ( (CONCAT(TRIM(first_name), ' ', TRIM(last_name))  like '%{$data['sSearch']}%')  )";
//                $sub_query_person .= "where ( (CONCAT(TRIM(p.first_name), ' ', TRIM(p.last_name))  like '%{$data['sSearch']}%') OR (pi.cnic_number like '%{$data['sSearch']}%') OR (pi.cnic_number_foreigner like '%{$data['sSearch']}%') )";
//            
//            $sql = "SELECT  user_id
//                                    FROM  users_profile
//                                    {$sub_query}";
//            $user_id = DB::query(Database::SELECT, $sql)->execute()->as_array();
//            $users_array = implode(', ', array_values(array_column($user_id, 'user_id')));
//            if (!empty($users_array)){
//                $search = " OR  user_id in ({$users_array}))";          
//            }else {
//                    $search = " OR user_id = null)";
//                } 
//                
//                //search from persons
//            $sql = "SELECT  person_id
//                                    FROM  person as p
//                                    inner join person_initiate as pi using(person_id)
//                                    {$sub_query_person}";
//           // print_r($sqlperson);
//            $person_ids = DB::query(Database::SELECT, $sql)->execute()->as_array();
//            $persons_array = implode(', ', array_values(array_column($person_ids, 'person_id')));
//           // print_r($persons_array);
//            if (!empty($persons_array)){
//                $search_person = " and  (person_id in ({$persons_array})";          
//            }else {
//                    $search_person = "and (person_id = null";
//                }                    
        }
        else {
            $search = "";
        }
                
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;

        $where_clause = 'where 1';
        
        $DB = Database::instance();   
        /* For Total Record Count */
        if($count=='true')
        {      $sql = "Select  COUNT(*) AS count
                            from user_sensitive_person AS u1
                            join person_initiate as u2 on u2.person_id = u1.person_id
                            inner join person as u3 on u3.person_id = u1.person_id
                            {$where_clause}
                            {$search}";
     // print_r($sql); exit;
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        return $members['count'];
        }
        /*  Fetch all Records */
        else {
            $sql = "Select * , u1.user_id as adding_user   from user_sensitive_person as u1
                    join person_initiate as u2 on u2.person_id = u1.person_id
                    inner join person as u3 on u3.person_id = u1.person_id 
                    {$where_clause}
                    {$search}
                    {$order_by}    
                    {$limit}";                          
                            
           // print_r($sql); exit;
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        return $members;
        }
    }
    public static function sc_deleted($userid, $personid) {     

        $query = DB::delete('user_sensitive_person')
                ->where('user_id', '=', $userid)
                ->where('person_id', '=', $personid)
                ->execute();
        //to add activity of user
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 18);

        return $query;
    }
    public static function person_breakup_region($data, $count) {
        
        if (empty($data['startdate']) || empty($data['enddate'])) {
            $date_where_clause = "";
        } else {
            $data['startdate'] = date('Y-m-d', strtotime($data['startdate']));
            $data['enddate'] = date('Y-m-d', strtotime($data['enddate']));
            $start_date = $data['startdate'] . " 00:00:00";
            $end_date = $data['enddate'] . " 23:59:59";

            $date_where_clause = "and (t1.created_at >= '{$start_date}' AND  t1.created_at <= '{$end_date}' )";
        }
        /* Sorted Data */
        $order_by_param = "t2.region_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t2.region_id";
                    break;
            }
        }
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        $where_clause = "where 0  ";
        if ($permission == 1 || $permission == 2 || $permission == 5) {
            $where_clause = "where 1  ";
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type;
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( u2.company_name like '%{$data['sSearch']}%' or u1.total_send like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "SELECT  count(distinct t2.region_id) as count FROM person_initiate as t1
                    join users_profile as t2 on t2.user_id = t1.user_id
                         {$where_clause}
                         {$date_where_clause}";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {

            $sql = "SELECT t2.region_id, count(person_id) as total_person FROM person_initiate as t1
                    join users_profile as t2 on t2.user_id = t1.user_id
                     {$where_clause}
                     {$date_where_clause}
                     group by t2.region_id
                     {$order_by}
                        {$limit}";
            // print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    
        //breakup district
    public static function person_breakup_district($data, $count) {
        /* Posted Data */
//        echo '<pre>';
//        print_r($data); exit;
        if (empty($data['startdate']) || empty($data['enddate'])) {
            $date_where_clause = "";
        } else {
            if (DateTime::createFromFormat('Y-m-d', $data['startdate']) == FALSE) {
                $data['startdate'] = date('Y-m-d', strtotime($data['startdate']));
                $data['enddate'] = date('Y-m-d', strtotime($data['enddate']));
            } else {
                $data['startdate'] = date('Y-m-d', ($data['startdate']));
                $data['enddate'] = date('Y-m-d', ($data['enddate']));
            }
            $start_date = $data['startdate'] . " 00:00:00";
            $end_date = $data['enddate'] . " 23:59:59";

            $date_where_clause = " and (pi.created_at >= '{$start_date}' AND  pi.created_at <= '{$end_date}' ) ";
        }
        /* Sorted Data */
        $order_by_param = "up.region_id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "up.region_id";
                    break;
                case "1":
                    $order_by_param = "ur.posted";
                    break;
            }
        }
        $login_user = Auth::instance()->get_user();
        $DB = Database::instance();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        $where_clause = !empty($data['region_id']) ? "where up.region_id={$data['region_id']} " : 'where 1 ';

        //  if ($permission == 1) {
        //  $where_clause = " ";
        // }
        if ($permission == 2 || $permission == 3) {
            $result = explode('-', $posting);
            $where_clause .= ($result[0] != 'h') ? "AND up.posted = '$posting' ) " : '';
        }
        /* Order By */
        $order_by_type = "desc";
        if (isset($data['sSortDir_0']) && $data['sSortDir_0'] != "") {
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
        $order_by = " order by " . $order_by_param . " " . $order_by_type;
        $limit = '';
        /* Starting and Ending Lenght (size) */
        if (isset($data['iDisplayStart']) && isset($data['iDisplayLength'])) {
            $limit = " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */

        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\.\ ]/', '', $data['sSearch']);
            $search = "and ( u2.company_name like '%{$data['sSearch']}%' or u1.total_send like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }

        $DB = Database::instance();
        /* For Total Record Count */
        if ($count == 'true') {
            $sql = "select COUNT(DISTINCT up.posted) as count from person_initiate pi 
                    JOIN users_profile up on up.user_id = pi.user_id
                         {$where_clause}
                         {$date_where_clause}";
            // print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */ else {

            $sql = "select COUNT(up.posted) as total_person,up.region_id , up.posted from person_initiate pi 
                    JOIN users_profile up on up.user_id = pi.user_id
                   {$where_clause}
                     {$date_where_clause}
                    GROUP by up.posted
                     {$order_by}
                        {$limit}";
            //print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);
            return $members;
        }
    }
    
        /* Top Search persons Ajax Call Data */
     public static function person_list_district($data, $count) {
         $gender_where_clause = "";
         if (!empty($data['p_type'])) {
             switch ($data['p_type']){
             case 1: //male
                 $gender_where_clause = " and mod(pi.cnic_number,2) = 0";
                 break;
             case 2: //female
                 $gender_where_clause = " and mod(pi.cnic_number,2) = 1";
                 break;
             }
         }
         $login_user = Auth::instance()->get_user();
         $district_id = !empty($data['id']) ? Helpers_Utilities::encrypted_key($data['id'], 'decrypt') : 0;
        if (empty($data['startdate']) || empty($data['enddate'])) {
            $date_where_clause = "";
        } else {
                $data['startdate'] = date('Y-m-d', ($data['startdate']));
                $data['enddate'] = date('Y-m-d', ($data['enddate']));
            $start_date = $data['startdate'] . " 00:00:00";
            $end_date = $data['enddate'] . " 23:59:59";

            $date_where_clause = " and (pi.created_at >= '{$start_date}' AND  pi.created_at <= '{$end_date}' ) ";            
        }
         /* Posted Data */
         $join_query='';
         $join_query_and = '';         
         $where_clause = "where up.user_id IN (SELECT user_id FROM users_profile as u1 WHERE u1.posted ='{$district_id}'  ) ";
        
        /* Sorted Data */
        $order_by_param = "pi.person_id";
        if(isset($data['iSortCol_0'])){
            switch ($data['iSortCol_0']){
                case "0":
                    $order_by_param = "pi.person_id";
                    break; 
                case "4":
                    $order_by_param = "pi.created_at";
                    break;
                
            }
        }        
        
        /* Order By */
        $order_by_type = "desc";
        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){
            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        
        $limit= "";
        /* Starting and Ending Lenght (size) */
        if(isset($data['iDisplayStart']) && isset($data['iDisplayLength'])){
            $limit= " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        
        /* Search via table */
        
        if (isset($data['sSearch']) && !empty($data['sSearch'])) {
            $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-\ ]/', '', $data['sSearch']);
            $search = "and ( CONCAT(u1.first_name, ' ', TRIM(u1.last_name)) like '%{$data['sSearch']}%' )";
        } else {
            $search = "";
        }
        
        /* Group By */
        $groupby = "group by pi.person_id";
        //$order_by=" order by maxtotal desc";
        
        $DB = Database::instance();   
        /* For Total Record Count */
        if($count=='true')
        {      $sql = "Select  COUNT(pi.person_id) AS count from person_initiate as pi
                            JOIN users_profile up on up.user_id = pi.user_id
                            {$where_clause}
                            {$gender_where_clause}
                            {$date_where_clause}";
        
        $members = DB::query(Database::SELECT, $sql)->execute()->current();
        return $members['count'];
        }
        /*  Fetch all Records */
        /*
         SELECT t1.person_id AS PID, COUNT(t1.person_id) AS TOTAL
                FROM user_activity_timeline as t1 
                WHERE t1.user_activity_type_id = 3 
        */
        else {
            $sql = "Select * , pi.created_at as pcreated    from person_initiate as pi   
                                JOIN users_profile up on up.user_id = pi.user_id
                                {$where_clause}
                                {$gender_where_clause}
                                {$date_where_clause}
                                {$groupby} 
                                {$order_by}    
                                {$limit}"; 
        //    print_r($sql); exit;
        $members = $DB->query(Database::SELECT, $sql, FALSE);
        return $members;
        }
    }
}
