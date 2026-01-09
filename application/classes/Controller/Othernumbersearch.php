<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Othernumbersearch extends Controller_Working {
	
	public function __Construct(Request $request, Response $response) {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }
    
    public function action_error() {
         $this->template->content = View::factory('templates/user/access_denied');
    }        
    

        /*
    *      search person list
    */
    public function action_access_denied()
    {
        $this->template->content = View::factory('templates/user/access_denied');
                         
    }
    public function action_mark_complete()
    {
        try{
        $_POST = Helpers_Utilities::remove_injection($_POST);
        // this is mark completed status, updated in case of inpropriate email
        $reference_number = $_POST['requestid'];
        $reference_number = Model_Email::email_status($reference_number, 2, 7);
        $this->redirect('Userrequest/request_status_detail/' . $reference_number);
        
        } catch (Exception $e){ }
    }
    public function action_other_number_search() {
        try{
        if (Auth::instance()->logged_in()) {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            $access_search_person = Helpers_Profile::get_user_access_permission($login_user_id, 6);
            if ((Helpers_Utilities::chek_role_access($this->role_id, 6) == 1) && $access_search_person == 1) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('othernumber_search_post', $post);
                
                $this->template->content = View::factory('templates/user/othernumber_search')
                         ->set('search_post', $post);
                
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } else {
            $this->redirect();
        }
         } catch (Exception $ex){
                $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
            }
    }
    public function action_bulk_search_indepth() {
        try{
        if (Auth::instance()->logged_in()) {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $login_user_id = $login_user->id;
            $access_message = 'Access denied, Contact your technical support team';
            $access_search_person = Helpers_Profile::get_user_access_permission($login_user_id, 6);
            if ((Helpers_Utilities::chek_role_access($this->role_id, 6) == 1) && $access_search_person == 1) {
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('bulk_search_indepth', $post);
if($this->request->post())                
{    
$clean = stripslashes($post['bulknumber']);
$clean = str_replace("\\n", "", $clean);   
$clean = str_replace("\\'", "'", $clean); 
$data = new Model_Othernumber();            
$results = $data->bulk_search($clean);
 // 3️⃣ If no records found
    if (empty($results)) {
       echo 'No records found for the given numbers.';
       echo '<a href="'.URL::site().'Othernumbersearch/bulk_search_indepth">Back</a>';
       exit;
    }

    // 4️⃣ Prepare CSV headers
    $filename = 'bulk_search_' . date('Y-m-d_H-i-s') . '.csv';

    // Set headers for download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // 5️⃣ Open output buffer
    $output = fopen('php://output', 'w');

    // 6️⃣ Write column headers
    fputcsv($output, [
        'Aparty',
        'Bparty',
        'Person Name',
        'Father Name',
        'CNIC',
        'Category',
        'Tags',
        'Call Made',
        'Call Received',
        'SMS Made',
        'SMS Received',
        'Total Logs',
        'Last Call',
        'SMS Call'
    ]);

    // 7️⃣ Write data rows
    foreach ($results as $row) {
        fputcsv($output, [
            $row['Aparty'],
            $row['Bparty'],
            $row['PersonName'],
            $row['FatherName'],
            $row['PersonCNIC'],
            $row['Category'],
            $row['Tags'],
            $row['CallMade'],
            $row['CallReceived'],
            $row['SMSMade'],
            $row['SmsReceived'],
            $row['TotalLogs'],
            $row['LastCall'],
            $row['SmsCall']
        ]);
    }

    fclose($output);
    exit; // 🚨 Important: stop further output
}            
            
                
                $this->template->content = View::factory('templates/user/bulk_search_indepth')
                         ->set('search_post', $post);
                
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } else {
            $this->redirect();
        }
         } catch (Exception $ex){
                $this->template->content = View::factory('templates/user/exception_error_page')
                ->bind('exception', $ex);
            }
    }

    //ajax call for data
    public function action_ajaxothernumber() {
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
            $post = Session::instance()->get('othernumber_search_post', array());
            
            if (isset($_GET)) {
                $post = array_merge($post, $_GET);
            }
            $post = Helpers_Utilities::remove_injection($post);
            $data = new Model_Othernumber();            
            $rows_count = $data->othernumber_search($post, 'true');
            $profiles = $data->othernumber_search($post, 'false');
           
           if (isset($profiles) && sizeof($profiles) <= 0) {
                $output['iTotalRecords'] = 0;
                $output['iTotalDisplayRecords'] = 0;
            }else{
                $output['iTotalRecords'] = $rows_count;
                $output['iTotalDisplayRecords'] = $rows_count;
            }             
            if (isset($profiles) && sizeof($profiles) > 0) {                
                foreach ($profiles as $item) {
                    $number = (!empty($item['phone_number']) ) ? $item['phone_number'] : '';
                    $company_name = (isset($item['mnc']) && $item['mnc'] == 11) ? 'PTCL' : 'International';
                    $active_status = (isset($item['status']) && $item['status'] == 1) ? 'Active': '' ;
                                     ((isset($item['status']) && $item['status'] == 0) ? 'In-Active' :
                                         '');
                    $activation_date = (isset($item['sim_activated_at'])) ? $item['sim_activated_at'] : '';
                    $person_id = (isset($item['person_id']) ) ? $item['person_id'] : '';
                    $person_name = '';
                    $member_name_link = '';
                    $request_detail_link = '';
                    if (isset($person_id) && !empty($person_id)) {
                        $person_name = ( isset($person_id) ) ? Helpers_Person::get_person_name($person_id) : 'NA';
                        $login_user = Auth::instance()->get_user();
                        $access = Helpers_Person::sensitive_person_acl($login_user->id, $person_id);
                        if ($access == TRUE) {
                            $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id,"encrypt")) . '" > [ View Profile ] </a>';
                        } else {
                            $member_name_link = 'NO Access';
                        }
                    }else{
                        $member_name_link = '<a href="javascript:affiliateothernumber('.$number.')" > [ Affiliate with Person ] </a>';
                    }
                    $requst_count = Helpers_Othernumbers::check_request_against_number($number);
                    if ($requst_count > 0) {
                        $request_detail_link = '<a href="javascript:viewrequests('.$number.')" > [ View Requests ] </a>';
                    }

                    $row = array(
                        $number,
                        $company_name,
                        $active_status,
                        $activation_date,
                        $person_name . ' ' . $member_name_link,
                        $requst_count. ' ' . $request_detail_link
                    );

                    $output['aaData'][] = $row;
                }
            } 
        }

        echo json_encode($output);
        exit();
        } catch (Exception $ex){
            
        }
    }
    
    public function action_affiliate_number() {
        try{
        $_POST = Helpers_Utilities::remove_injection($_POST);        
        $number = isset($_POST['number']) ? $_POST['number'] : 0;
        $cnic_number = isset($_POST['cnic_number']) ? $_POST['cnic_number'] : 0;
        //get person ID from CNIC
        $person_id = Helpers_Person::check_person_id_with_cnic($cnic_number);
        Model_Othernumber::affiliate_number($number, $person_id); 
        
        $post = Session::instance()->get('othernumber_search_post', array());
        $this->template->content = View::factory('templates/user/othernumber_search')
                         ->set('search_post', $post);
        } catch (Exception $e){ }
    }
    public function action_check_person() {
        try{
        $_POST = Helpers_Utilities::remove_injection($_POST);
        //print_r($_POST); exit;
        if (!empty($_POST['cnic'])) {
            $person_id = Helpers_Utilities::get_person_id_with_cnic($_POST['cnic']);
            if (!empty($person_id)) {
                $person_Name = Helpers_Person::get_person_name($person_id);
                echo $person_Name;
            }else{
            echo 1;
            }
        } else {
            echo 1;
        }
        }  catch (Exception $ex){
            echo json_encode(2);
        }
    }
    public function action_request_details() {
        try{
        $_POST = Helpers_Utilities::remove_injection($_POST);
        $requested_value = !empty($_POST['requested_value']) ? $_POST['requested_value'] : 0;        
        $request_data = Helpers_Othernumbers::request_details($requested_value);        
        $html = '';
        foreach ($request_data as $item) { 
            $request_type = ( isset($item['user_request_type_id']) ) ? Helpers_Utilities::get_request_type($item['user_request_type_id']) : 'NA';
            $request_date = ( isset($item['created_at']) ) ? ($item['created_at']) : 'NA';
            $start_date = ( isset($item['startDate']) ) ? ($item['startDate']) : '-';
            $end_date = ( isset($item['endDate']) ) ? ($item['endDate']) : '-';
            $request_status = ( isset($item['status']) ) ? ($item['status']) : '-';
            $enc_request_id= trim(Helpers_Utilities::encrypted_key($item['request_id'], 'encrypt'));
            $status_flag = '';
            switch ($request_status) {
                case 0:
                    $status_flag = '<span class="label label-info">Request In Queue</span>';
                    break;
                case 1:
                    $status_flag = '<span class="label label-success">Request Send</span>';
                    break;
                case 2:
                    $status_flag = '<span class="label label-success">Request Completed</span>';
                    break;
                case 3:
                    $status_flag = '<span class="label label-danger">Request Error</span>';
                    break;
            }
            $view_detail_link = '<a href="' . URL::site('userrequest/request_status_detail/' . $enc_request_id) . '" > View Detail </a>';
            $html .= '<tr>
                    <td>'.$request_type.'</td>
                    <td>'.$request_date.'</td>
                    <td>'.$start_date.'</td>
                    <td>'.$end_date.'</td>
                    <td>'.$status_flag.'</td>
                    <td>'.$view_detail_link.'</td>
                </tr>';
        }
        $post = Session::instance()->get('othernumber_search_post', array());
        $number_type = $post['number_type'];
        $request_link = '';
        if ($number_type == 1) {
         $request_link = 'Above request data is avaiable if more data required, <a  href="#" onclick="requestptclcdr('.$requested_value.')"> Click To Request CDR From PTCL </a>';   
        }  else {
         $request_link = 'Above request data is avaiable if more data required, <a  href="#" onclick="requestinternationalcdr('.$requested_value.')"> Click To Request CDR From MegaData </a>';
        }        
        $html .= '<tr>
                    <td colspan="6">'.$request_link.'</td>
                </tr>';
        echo $html;
        }  catch (Exception $ex){
            echo json_encode(2);
        }
    }
    //add other number against person
    public function action_add_other_number() {

        try {
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $number = isset($_POST['othernumber']) ? $_POST['othernumber'] : 0;
            $p_id = isset($_POST['person_id']) ? $_POST['person_id'] : 0;
            $mnc = (isset($_POST['number_type']) && ($_POST['number_type'] == 1)) ? 11 : 12;
            $result = Helpers_Person::check_person_other_number_exist($number);
            if ($result > 0) {
                echo ('-2');
                exit;
            } else {
                $query_result = Model_Othernumber::add_other_number($number, $p_id, $mnc);
                echo $query_result;
                exit;
            }
        } catch (Exception $ex) {
            echo ('-3');
        }

        
    }

} // End Users Class
