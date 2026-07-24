<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Userdashboard extends Controller_Working {
	
	public function __Construct(Request $request, Response $response) {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }     
    public function action_categorycomparison() {
        try{
        $user_obj = Auth::instance()->get_user();
        $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);
        $permissin = Helpers_Utilities::get_user_permission($user_obj->id);
        if($permissin == 1 || $permissin == 5 || $permissin == 2 || $permissin == 3)            
            $row = Helpers_Person::get_category_comparison();
        else if($permissin == 4)
            $row = Helpers_Person::get_category_comparison($user_obj->id);        
        $compare = array();
        foreach($row as $r){
        $compare[]= $r;
        }
        echo json_encode($compare);
        } catch (Exception $ex){
            echo json_encode (2);
        }
    }
    public function action_userscomparison() {   
        try{
        $user_obj = Auth::instance()->get_user();
        $user_role = Helpers_Utilities::get_user_role_id($user_obj->id);
        
        //print_r($user_role);// exit;
        if($user_role==1)            
            $row = Helpers_Person::get_users_comparison();
        else
           $row = Helpers_Person::get_users_comparison($user_obj->id);
        echo json_encode($row);
        } catch (Exception $ex){
            echo json_encode (2);
        }
    }
    public function action_dashboard() {		
        if (Auth::instance()->logged_in()) {
            
        try {
                $user = Auth::instance()->get_user();
                $blockcount = Helpers_Profile::get_blocked_user_count($user->id);
                $approval = Helpers_Profile::get_appvoed_user_count($user->id);
            } catch (Exception $e) {
                echo 'Error! Please contact to SES'; exit;
                
            }
            
            
            try {

        
        if ($blockcount == 1) {
            Auth::instance()->logout(TRUE, TRUE);
            $this->redirect('/?message=2');
        } elseif ($approval == 1) {
            Auth::instance()->logout(TRUE, TRUE);
            $this->redirect('/?approval=0');
        }
        //working controller code end //
                
                if (Helpers_Utilities::chek_role_access($this->role_id, 1) == 1) {
                    $user_obj = Auth::instance()->get_user();
                    $userid = $user_obj->id;
                    //check cnic exist or not
                    $cnic_exist = Helpers_Utilities::check_user_cnic_exist($userid);
                    // 0  Means Passord change required
                    $password_change_required = Helpers_Utilities::password_change_required($userid);
                    $role = Helpers_Utilities::get_user_role_id($userid);
                    $persmission = Helpers_Utilities::get_user_permission($userid);
                    include 'user_functions/dashboard.inc';
                } else {
                    $this->template->content = View::factory('templates/user/access_denied');
                }
            } catch (Exception $ex) {
                
                    
                //echo 'Error! please contact to Support Section!';
                //exit;
            
                
                $this->template->content = View::factory('templates/user/some_thing_went_wrong');
            }
        } else {
            $this->redirect();
        }
    }

//new action

    public function action_cnic_number_save() {
        try {
            $user_obj = Auth::instance()->get_user();
            $userid = $user_obj->id;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $data = new Model_Userdashboard();
            $insert_result = $data->insert_cnic($_POST['cnic_number'], $userid);
            echo json_encode($insert_result);
        } catch (Exception $e) {
            echo json_encode(2);
        }
    }
//new action

    public function action_password_change_required() {
         $this->redirect('user/changepassword');
    }

    public function action_respone_check() {
       // Helpers_Upload::monthly_summary(3054937625, 293);        
        if (Auth::instance()->logged_in()) {
            try{
            $user_obj = Auth::instance()->get_user();
            //$role = Helpers_Utilities::get_user_role_id($user_obj->id);
            $userid = $user_obj->id;
            //Helpers_Email::get_email_status($userid);             
                echo '1';
                }catch (Exception $e) { }
            }
        else {
            $this->redirect();
        }    
        
        
    }


    public function action_get_request_type_counts()
        {
            try {

                if (!Auth::instance()->logged_in()) {
                    echo json_encode(array(
                        'status' => 'error',
                        'message' => 'Unauthorized access'
                    ));
                    return;
                }

                // Get filter values
                $from_date = $this->request->query('from_date');
                $to_date   = $this->request->query('to_date');

                $db = Database::instance();

                // Base query
                $query = "SELECT
                            CASE t3.label
                                WHEN 'cell_no_location' THEN 'Location Against Mobile No'
                                WHEN 'cell_no_ownership' THEN 'Ownership Against Mobile No'
                                WHEN 'cell_no_cdr' THEN 'CDR Against Mobile No'
                                WHEN 'cell_no_against_cnic' THEN 'SIMs Against CNIC'
                                WHEN 'ptcl_no_cdr' THEN 'CDR Against PTCL No'
                                WHEN 'cell_no_against_imei' THEN 'SIMs Against IMEI'
                                WHEN 'cnic_nadra_detail' THEN 'NADRA Details Against CNIC'
                                WHEN 'imsi_cdr' THEN 'CDR Against IMEI'
                                ELSE t3.label
                            END AS request_type,
                            COUNT(*) AS total_requests
                        FROM user_request t1
                        JOIN lu_user_request_type t3
                            ON t3.id = t1.user_request_type_id
                        WHERE 1=1";

                // Add date filters
                if (!empty($from_date)) {
                    $query .= " AND DATE(t1.created_at) >= :from_date";
                }

                if (!empty($to_date)) {
                    $query .= " AND DATE(t1.created_at) <= :to_date";
                }

                $query .= " GROUP BY t3.label
                            ORDER BY total_requests DESC";

                // Prepare query
                $result = DB::query(Database::SELECT, $query);

                // Bind parameters
                if (!empty($from_date)) {
                    $result->param(':from_date', $from_date);
                }

                if (!empty($to_date)) {
                    $result->param(':to_date', $to_date);
                }

                $data = $result->execute()->as_array();

                echo json_encode(array(
                    'status' => 'success',
                    'data'   => $data,
                    'total'  => array_sum(array_column($data, 'total_requests'))
                ));

            } catch (Exception $ex) {

                echo json_encode(array(
                    'status' => 'error',
                    'message' => $ex->getMessage()
                ));
            }
        }
  
} // End Userdashboard Class
