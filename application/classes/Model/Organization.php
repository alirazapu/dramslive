<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * module related with email template   
 */
class Model_Organization {
    
    /* single email view  */
    public static function view($id) {
        $query = "SELECT * 
                    FROM banned_organizations AS ip
                    where ip.org_id=$id";
        $sql = DB::query(Database::SELECT, $query);

        $result = $sql->execute();
        
        return $result;
    }

    

    /* view all email template  */
    public static function int_projects_list($data, $count) {       
            
        /* Sorted Data */
        $order_by_param = "t1.id";
        if (isset($data['iSortCol_0'])) {
            switch ($data['iSortCol_0']) {
                case "0":
                    $order_by_param = "t1.org_name";
                    break;
                case "1":
                    $order_by_param = "t1.org_acronym";
                    break;
                case "2":
                    $order_by_param = "t1.notification_no";
                    break;
            }
        }       
        
        /* Order By */
        $order_by_type = "asc";
        if(isset($data['sSortDir_0']) && $data['sSortDir_0'] != ""){            
            $order_by_type = $data['sSortDir_0'];
        }
        //if(!$need_for_count){t2.notification
            $order_by = " order by " . $order_by_param . " " . $order_by_type . " ";
        
        $limit= "";
        /* Starting and Ending Lenght (size) */
        if(isset($data['iDisplayStart']) && isset($data['iDisplayLength'])){
            $limit= " limit " . $data['iDisplayStart'] . ", " . $data['iDisplayLength'];
        }
        /* Search via table */
        
       if(isset($data['sSearch']) && !empty($data['sSearch'])){
                $data['sSearch'] = preg_replace('/[^A-Za-z0-9\ ]/', '', $data['sSearch']);
                $search = "and (t1.org_name like '%{$data['sSearch']}%' or t1.org_acronym like '%{$data['sSearch']}%')";
            
        }
        else {
            $search = "";
        }
        $DB = Database::instance();   
        /* For Total Record Count */
        if($count=='true')
        {   
            $sql = "SELECT Count(*) as count 
                    FROM banned_organizations AS t1       
                    where 1
                    {$search}";             
            $members = DB::query(Database::SELECT, $sql)->execute()->current();
            return $members['count'];
        }
        /*  Fetch all Records */
        else {
            $sql = "SELECT * 
                    FROM banned_organizations AS t1   
                    where 1
                    {$search}
                    {$order_by}
                    {$limit}"; 
            //print_r($sql); exit;
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
    /* get all organization data */
    public static function get_all_organization_list($data) {
        $query = "SELECT * FROM banned_organizations
                  where org_name like '%{$data}%'";
        $sql = DB::query(Database::SELECT, $query);
        $result = $sql->execute();        
        return $result;
    }
    /*template data insert*/
    public static function projectinsert($data) {        
        $query = DB::insert('banned_organizations', array('org_name','org_acronym', 'drived_from_id', 'notification_no'))               
                ->values(array($data['organizationname'],$data['org_acr'], $data['porganization'], $data['notification']))                
                ->execute();
        
                $login_user = Auth::instance()->get_user();
                $uid = $login_user->id;
                Helpers_Profile::user_activity_log($uid, 69, $data['organizationname'], $data['porganization'],$query[0]);
    }

    /*template data updated */
    public static function update($data) {
        //echo '<pre>';print_r($data); exit;
        $query = DB::update('banned_organizations')->set(array('org_name' => $data['organizationname'], 'org_acronym' => $data['org_acr'],
                    'drived_from_id' => $data['porganization'], 'notification_no' => $data['notification']))
                ->where('org_id', '=', $data['id'])
                ->execute();
        $login_user = Auth::instance()->get_user();
        $uid = $login_user->id;
        Helpers_Profile::user_activity_log($uid, 70, $data['organizationname'], $data['porganization'], $query[0]);
    }
}
