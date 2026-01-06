<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with email template   
 */
class Model_Intprojects {
    
    /* single email view  */
    public static function view($id) {
        $query = "SELECT * 
                    FROM int_projects AS ip where ip.id=$id";
                    //inner join  banned_organizations AS bo on ip.org_id = bo.org_id where ip.id=$id order by ip.id ASC";
        $sql = DB::query(Database::SELECT, $query);

        $result = $sql->execute();
        
        return $result;
    }

    

    /* view all email template  */
    public static function int_projects_list($data, $count) {
//        echo '<pre>';
//        print_r($data);
//        exit;
            
        /* Sorted Data */
        $order_by_param = "t1.id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.project_name";
                    break;
                case "1":
                    $order_by_param = "t2.region_id";
                    break;
                case "4":
                    $order_by_param = "t1.project_status";
                    break;
            }
        }


        $where_project_name='';
        if (!empty($data['pname'])) {
            $where_project_name= " and t1.project_name like '%{$data['pname']}%' ";
        }

        $where_region_name='';
        if (!empty($data['reqbyregion'])) {
            $where_region_name= " and t1.region_id ={$data['reqbyregion']} ";

        }

        $where_district_name='';
        if (!empty($data['reqbydistrict']) && $data['reqbydistrict']==100) {
            $where_district_name= " and t1.district_id =0";
        }elseif (!empty($data['reqbydistrict'])) {
            $where_district_name= " and t1.district_id ={$data['reqbydistrict']} ";
        }



//        //dates filter
//        $serach_date = ' ';
//        //if end date is not selected then user current date as end date
//        if (empty($data['enddate'])) {
//            $data['enddate'] = date("Y-m-d");
//        }
//        if (!empty($data['startdate'])) {
//            $start_date = date("Y-m-d", strtotime($data['startdate']));
//            $end_date = date("Y-m-d", strtotime($data['enddate']));
//
//            $start_date = $start_date . ' 00:00:00';
//            $end_date = $end_date . ' 23:59:59';
//            $serach_date = " and t1.timestamp between '{$start_date}' and '{$end_date}' ";
//        }
        
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
        /* Search via table */
        
       if(isset($data['sSearch']) && !empty($data['sSearch'])){
           $data['sSearch'] = preg_replace('/[^A-Za-z0-9\-]/', '', $data['sSearch']);               
           $search = "and (t1.project_name like '%{$data['sSearch']}%' or t1.details like '%{$data['sSearch']}%')";            
        }
        else {
            $search = "";
        }
        $login_user = Auth::instance()->get_user();
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
    //    print_r($login_user_profile); exit;
        $posting_region = $login_user_profile->region_id;
        $posting = $login_user_profile->posted;
        $result = explode('-', $posting);
//        print_r($result);
//        exit;



        $where_clause = 'where t1.region_id = 0';
            if ($posting_region == 11) {
            $where_clause = " where 1 ";
        } else if ($result[0] == 'r') {
            $where_clause = " where ( t1.region_id = {$posting_region} )";
        } else {
            $where_clause = " where ( t1.region_id = {$posting_region} and t1.district_id = {$result[1]})";
        }
        $DB = Database::instance();   
        /* For Total Record Count */
        if($count=='true')
        {   
            $sql = "SELECT Count(*) as count 
                    FROM int_projects AS t1  
                    join region as t2 on t2.region_id = t1.region_id
                    {$where_clause}
                    {$where_project_name}
                    {$where_region_name}
                    {$where_district_name}
                   
                    {$search}";
            //print_r($sql); exit;
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */
        else {
            $sql = "SELECT * 
                    FROM int_projects AS t1  
                    join region as t2 on t2.region_id = t1.region_id
                    {$where_clause}
                    {$where_project_name}
                    {$where_region_name}
                    {$where_district_name}
                   
                    {$search}
                    {$order_by}
                    {$limit}";    
//            print_r($sql); exit;
            $members = $DB->query(Database::SELECT, $sql, FALSE);        
            return $members;
        }
            
            
            
    }
    /* get banned organizations list */
    public static function get_banned_org_list() {
        $query = "SELECT org_id,org_name 
                    FROM banned_organizations ";
        $sql = DB::query(Database::SELECT, $query);

        $result = $sql->execute();
        
        return $result;
    }
    /* get all project data */
    public static function get_project_list($data) {
        $query = "SELECT * FROM int_projects
                  where project_status = 0 and project_name like '%{$data}%'";
        $sql = DB::query(Database::SELECT, $query);
        $result = $sql->execute();        
        return $result;
    }

    /*template data insert*/
    public static function projectinsert($data) {
//        echo '<pre>';
//        print_r($data);
//        exit;
        //print_r($data); exit;
        $query = DB::insert('int_projects', array('project_name', 'region_id','district_id','project_status', 'details', 'created_by', 'modified_by'))               
                ->values(array($data['projectname'],$data['projectregion'],$data['projectdistrict'], $data['project_status'], $data['projectdetails'], $data['user_id'], 0))                
                ->execute();
        if (!empty($data['porganization']) ) {
            foreach ($data['porganization'] as $porg) {
                $query1 = DB::insert('int_projects_organizations', array('project_id', 'org_id'))
                        ->values(array($query[0], $porg))
                        ->execute();
            }
        }
        $login_user = Auth::instance()->get_user();
                $uid = $login_user->id;
                Helpers_Profile::user_activity_log($uid, 27, $data['projectname'],$data['projectregion'],$query[0]);
    }

    /*template data updated */
    public static function update($data) {
//        echo '<pre>';print_r($data); exit;
        $query = DB::update('int_projects')->set(array('project_name' => $data['projectname'],
                    'project_status' => $data['project_status'], 'details' => $data['projectdetails'], 'modified_by' => $data['user_id']))
                ->where('id', '=', $data['id'])
                ->execute();
        
        $query_delete_organization = DB::delete('int_projects_organizations')
                ->where('project_id', '=',  $data['id'])
                ->execute();
        
         if ( !empty($data['porganization'])) {
            foreach ($data['porganization'] as $porg) {
//                echo '<pre>';print_r($porg); exit;
                if(!empty($data['id']) && !empty($porg)){
                $query1 = DB::insert('int_projects_organizations', array('project_id', 'org_id'))
                        ->values(array($data['id'], $porg))
                        ->execute();
                }
            }
        }
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 28, $data['projectname'], $data['region'],$data['id']);
    }
}
