<?php defined('SYSPATH') or die('No direct script access.');
/**
* Controller for email Template Functionality 
*/
 Class Controller_Intprojects extends Controller_Working {    

     
    
    /*list of email templates */
    public function action_index() {

        try{
        // new data
        $DB = Database::instance();
        $login_user = Auth::instance()->get_user();                
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        $result = explode('-', $posting);
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        
        if (Helpers_Utilities::chek_role_access($this->role_id, 29) == 1) {
            /* Posted Data */
            $post = $this->request->post();
            
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart'])) {            
//                $_GET['iDisplayStart']=0;                
//                
//            } 
                        
            if (isset($_GET)) {
            $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
//            echo '<pre>';
//            print_r($post);
//            exit;
            if (!empty($post['message']) && $post['message'] == 1 ) {
                $message = 'Congratulation! Request Sent successfully';
            }
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('project_list_post', $post);
            /* File Included */
            $view = View::factory('templates/user/int_projects_list')
            ->bind('post', $post);
                $this->template->content = $view;
        }
        else {
            $this->template->content = View::factory('templates/user/access_denied');
        }
        
        } catch (Exception $ex){
                $this->template->content = View::factory('templates/user/exception_error_page')
                        ->bind('exception', $ex);
            }
        
    }

//ajax call for data
    public function action_ajaxprojectslist() {
        try{
        $this->auto_rednder = false;
        /*  Output */
        $output = array(
            "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
            "iTotalRecords" => "0",
            "iTotalDisplayRecords" => "0",
            "aaData" => array()
        );
        if (Auth::instance()->logged_in()) {
            $post = Session::instance()->get('project_list_post', array());
            
//            if (!empty($post) && sizeof($post)>1 && !empty($_GET['iDisplayStart']))
//            {
//                $_GET['iDisplayStart']=0;                
//                
//            } 
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }            
            $post = Helpers_Utilities::remove_injection($post);
//            echo '<pre>';
//            print_r($post);
//            exit;
            $enc_sdate= !empty($post['startdate'])? Helpers_Utilities::encrypted_key($post['startdate'], "encrypt"):'';
            $enc_edate= !empty($post['enddate'])? Helpers_Utilities::encrypted_key($post['enddate'], "encrypt"):'';
//            $enc_sdate= !empty($post['startdate'])? $post['startdate']:'';
//            $enc_edate= !empty($post['enddate'])? $post['enddate']:'';
            $data = new Model_Intprojects;
            
            $rows_count = $data->int_projects_list($post, 'true');
            $profiles = $data->int_projects_list($post, 'false');
            

            if (isset($profiles) && sizeof($profiles) <= 0) {
                $output['iTotalRecords'] = 0;
                $output['iTotalDisplayRecords'] = 0;
            } else {
                $output['iTotalRecords'] = $rows_count;
                $output['iTotalDisplayRecords'] = $rows_count;
            }
            if (isset($profiles) && sizeof($profiles) > 0) {
                foreach ($profiles as $item) {
//                    echo '<pre>';
//                    print_r($item);
//                    exit;
                    $projectid = !empty($item['id']) ? $item['id'] : 0;
                    $pname=!empty($item['project_name']) ? $item['project_name'] : "NA";                                        
                    $region_name=!empty($item['name']) ? $item['name'] : "NA"; 
                    if (isset($item['district_id']) && $item['district_id'] == 100) {
                        $district_name = 'Self';
                    }else{                        
                        if($item['district_id']<900)
                        {    
                            $district_name=(isset($item['district_id']) && $item['district_id'] !== '0' ) ? Helpers_Utilities::get_district($item['district_id']) : "Unknown";   
                        }else
                        {    
                            $poice_station = Helpers_Utilities::get_ps_name($item['district_id']);
                            if(!empty($poice_station->name))
                                $district_name= 'PS '. $poice_station->name;
                            else
                                $district_name= "Unknown";   
                        }   
                    }
                    $organiztions_data = Helpers_Utilities::get_project_organizations($projectid);
                    $organiztions = '';
                    if (!empty($organiztions_data)) {
                        foreach($organiztions_data as $r){
                            $organiztions .= $r['org_name']; 
                            $organiztions .= ', '; 
                     }  
                    }
                    $request_count = 'Total = <b>';
                    $request_count .= Helpers_Utilities::project_request_count($projectid,$post);
                    $request_count .= '</b> </br>';
                    $request_count .= ' <a href="' . URL::site('Userreports/project_request_type/?project_id=' . Helpers_Utilities::encrypted_key($projectid, "encrypt").'&sdate='.$enc_sdate.'&edate='.$enc_edate) . '" > View Details</a>';
                    //$request_count .= ']';
                    $status= (isset($item['project_status']) && $item['project_status']== 0) ? 'Open' : 'Close';                                        
                    $pdetails=   !empty($item['details']) ? '<div class="wrap-tab">' . $item['details'] . '</div>' : "NA";
                    $p_org_name=!empty($item['org_name']) ? $item['org_name'] : "NA"; 
                    $projectid_encrypted = Helpers_Utilities::encrypted_key($projectid,"encrypt");
                    if ($status == 'Open') {
                    $member_name_link = '<a class="btn btn-small action" href="'.URL::base().'intprojects/showform/'.$projectid_encrypted.'"><i class="fa fa-edit"></i> Edit</a>';
                    } else {
                    $member_name_link = 'Project Closed';   
                    }
                    $row = array(
                        $pname,       
                        $region_name,
                        $district_name,
                        $organiztions, 
                        $status,
                        $pdetails,
                        $request_count,
                        $member_name_link
                    );

                    $output['aaData'][] = $row;
                }
            }
        }
        echo json_encode($output);
        exit();
        }  catch (Exception $ex){
            echo '<pre>';
            print_r($ex);
            exit;
        }
    }
    //type ahead controller for project name 
    public function action_project_name(){
        try{
        $_POST = Helpers_Utilities::remove_injection($_POST);
        $keyword = strval($_POST['query']);
	$search_param = "{$keyword}%";
	
	//call modal function
        $object = New Model_Intprojects();
        
        $result = $object->get_project_list($search_param);
        $project_name = array();
	if (!empty($result)) {
            foreach ($result as $row){
		$project_name[] = $row["project_name"];
		}
		echo json_encode($project_name);
		exit;
	}
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }
// single email template 
    public function action_showform() {
        try{
        $DB = Database::instance();
        $login_user = Auth::instance()->get_user();
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_id = $login_user->id;
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        $result = explode('-', $posting);
       // $access_email_edit = Helpers_Profile::get_user_access_permission($login_user_id, 15);
        //$access_email_add = Helpers_Profile::get_user_access_permission($login_user_id, 16);
        $access_message = 'Access denied, Contact your technical support team';
       // print_r($access_email_add); exit;
        if (Helpers_Utilities::chek_role_access($this->role_id, 30) == 1) {
            if (Auth::instance()->logged_in()) {
                $id = $this->request->param('id');
                $id = Helpers_Utilities::encrypted_key($id, 'decrypt');
                $id = Helpers_Utilities::remove_injection($id);
                if (isset($id) && ($id != NULL)) {
                    $user_obj = Auth::instance()->get_user();
                    try{
                    $data = new Model_Intprojects();
                    $data1 = $data->view($id);
                    } catch (Exception $ex){
             $this->redirect('Userdashboard/dashboard'); 
        }
                    if (isset($data1) && ($data1 != NULL)) {
                        $view = View::factory('templates/user/add_int_project')
                                ->set('records', $data1);
                        $this->template->content = $view;
                    } else {                        
                        $this->redirect('user/access_denied');
                        //$this->redirect('templates/user/int_projects_list/?accessmessage='.$access_message);
                    }
                } else {
                    $user_obj = Auth::instance()->get_user();
                    $view = View::factory('templates/user/add_int_project');
                    $this->template->content = $view;
                }
            } else {
                $this->redirect();
            }
        } else {
            $this->template->content = View::factory('templates/user/access_denied');
        }
        } catch (Exception $ex){
                $this->template->content = View::factory('templates/user/exception_error_page')
                        ->bind('exception', $ex);
            }
    }

    
    /* Add / update email template post */

    public function action_post() {
        //try {
            if (Auth::instance()->logged_in()) {
                $user_obj = Auth::instance()->get_user();
                $_POST = Helpers_Utilities::remove_injection($_POST);
                if ((isset($_POST)) && ($_POST != '') && ($_POST['id'] == '')) {
                    // echo '<pre>';                print_r($_POST); exit;
                    $_POST['user_id'] = $user_obj->id;
                    try {
                        $content = new Model_Intprojects();
                        $content_id = $content->projectinsert($_POST);
                    } catch (Exception $ex) {
                        $this->redirect('user/error_page');
                    }
                    $this->redirect('intprojects/showform?message=1');
                } else {
                    $_POST['user_id'] = $user_obj->id;
                    $id = $_POST['id'];
                    //echo '<pre>';                print_r($_POST); exit;
                    try {
                        $object = New Model_Intprojects();
                        $update = $object->update($_POST);
                    } catch (Exception $ex) {
                        $this->redirect('user/error_page');
                    }
                    $this->redirect('intprojects/showform?message=2');
                }
            } else {
                $this->redirect();
            }
//        } catch (Exception $ex) {
//            echo '<pre>';
//            print_r($ex);
//            exit;
//        }
    }

    public function action_region_district() {
        try{
        $_POST = Helpers_Utilities::remove_injection($_POST);
        $ctd_polic = Helpers_Utilities::get_region_police($_POST['region']);
        $results = Helpers_Utilities::get_region_district($_POST['region']);
        //print_r($_POST); exit;
        $data = '';
        if (!empty($results)) {
            $data .= '<option hidden >Please Select District Name</option>';            
            $data .= '<option '.((!empty($_POST['district']) && ($_POST['district'] == 100)) ? "selected" : '') .' value="100" >Self</option>';
            
            $data .= '<optgroup label="District">';
            
            foreach ($results as $row) {
                $data .= '<option '. ((!empty($_POST['district']) && ($_POST['district'] == $row['district_id'])) ? "selected" : '') .'  value="' . $row['district_id'] . '">' . $row['name'] . '</option>';
            }
            $data .= '</optgroup>';
            
        } 
        if (!empty($ctd_polic)) {    
            $data .= '<optgroup label="Police Station">';
            
            foreach ($ctd_polic as $row) {
                $data .= '<option '. ((!empty($_POST['district']) && ($_POST['district'] == $row['id'])) ? "selected" : '') .'  value="' . $row['id'] . '">' . $row['name'] . '</option>';
            }
            $data .= '</optgroup>';
        }
        
        if (empty($ctd_polic) && empty($results)) {
            $data .= '<option hidden >Please Select region Name</option>';
        }
        echo $data;
        }  catch (Exception $ex){
            echo json_encode(2);
        }
    }



}