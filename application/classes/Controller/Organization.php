<?php defined('SYSPATH') or die('No direct script access.');
/**
* Controller for email Template Functionality 
*/
 Class Controller_Organization extends Controller_Working {
    /*list of email templates */
    public function action_index() {   
        try{
        // new data
        $DB = Database::instance();
        $login_user = Auth::instance()->get_user();        
        $permission = Helpers_Utilities::get_user_permission($login_user->id);
        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
        $posting = $login_user_profile->posted;
        $result = explode('-', $posting);
        if (Helpers_Utilities::chek_role_access($this->role_id, 31) == 1) {
            /* Posted Data */
            $post = $this->request->post();            
            if (isset($_GET)) {
            $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
            if (!empty($post['message']) && $post['message'] == 1 ) {
                $message = 'Congratulation! Request Sent successfully';
            }
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('organization_list_post', $post);
            /* File Included */
            $view = View::factory('templates/user/organization_list');
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
            $post = Session::instance()->get('organization_list_post', array());            
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            
            $post = Helpers_Utilities::remove_injection($post);
            
            $data = new Model_Organization();
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
                                        $projectid=!empty($item['org_id']) ? $item['org_id'] : 0;
                                        $pname=!empty($item['org_name']) ? $item['org_name'] : "NA";                                        
                                        $pdetails=   !empty($item['org_acronym']) ? '<div class="wrap-tab">' . $item['org_acronym'] . '</div>' : "NA";
                                        $p_org_name=!empty($item['notification_no']) ? $item['notification_no'] : "NA";
                    
                    $member_name_link = '<a class="btn btn-small action" href="'.URL::base().'organization/showform/'.Helpers_Utilities::encrypted_key($projectid, 'encrypt').'"><i class="fa fa-edit"></i> Edit</a>';
                 
                    $row = array(
                        $pname,                        
                        $pdetails,
                        $p_org_name,                     
                        
                        $member_name_link
                    );

                    $output['aaData'][] = $row;
                }
            }
        }
        echo json_encode($output);
        exit();
        }  catch (Exception $ex){
            
        }
    }
        //type ahead controller for project name 
    public function action_organization_name(){
        try{
        $_POST = Helpers_Utilities::remove_injection($_POST);
        $keyword = strval($_POST['query']);
	$search_param = "{$keyword}%";
	
	//call modal function
        $object = New Model_Organization();
        
        $result = $object->get_all_organization_list($search_param);
        $organization_name = array();
	if (!empty($result)) {
            foreach ($result as $row){
		$organization_name[] = $row["org_name"];
		}
		echo json_encode($organization_name);
		exit;
	}
        }  catch (Exception $ex){
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
        if (Helpers_Utilities::chek_role_access($this->role_id, 32) == 1) {
            if (Auth::instance()->logged_in()) {
                $id_encrypted = $this->request->param('id');
                $id_encrypted = Helpers_Utilities::remove_injection($id_encrypted);
                $id=  Helpers_Utilities::encrypted_key($id_encrypted, 'decrypt');
                if (isset($id) && ($id != NULL)) {
                    $user_obj = Auth::instance()->get_user();
                    $data = new Model_Organization();
                    $data1 = $data->view($id);
                    if (isset($data1) && ($data1 != NULL)) {
                        $view = View::factory('templates/user/add_organization')
                                ->set('records', $data1);
                        $this->template->content = $view;
                    } else {                        
                        $this->redirect('user/access_denied');
                        //$this->redirect('templates/user/organization_list/?accessmessage='.$access_message);
                    }
                } else {
                    $user_obj = Auth::instance()->get_user();
                    $view = View::factory('templates/user/add_organization');
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
        if (Auth::instance()->logged_in()) {
            $user_obj = Auth::instance()->get_user();

            if ((isset($_POST)) && ($_POST != '') && ($_POST['id'] == '')) {
                try {
                    $_POST['user_id'] = $user_obj->id;
                    $_POST = Helpers_Utilities::remove_injection($_POST);
                    $content = new Model_Organization();
                    $content_id = $content->projectinsert($_POST);
                } catch (Exception $ex) {
                    $this->template->content = View::factory('templates/user/error_page');
                }
                $this->redirect('organization/showform?message=1');
            } else {
                $e_id = '';
                try {
                    $_POST['user_id'] = $user_obj->id;
                    $id = $_POST['id'];
                    $e_id = Helpers_Utilities::encrypted_key($id, 'encrypt');
                    //echo '<pre>';                print_r($_POST); exit;
                    $object = New Model_Organization();
                    $update = $object->update($_POST);
                } catch (Exception $ex) {
                    $this->template->content = View::factory('templates/user/error_page');
                }
                $this->redirect('organization/showform/' . $e_id . '?message=2');
            }
        } else {
            $this->redirect();
        }
    }

}