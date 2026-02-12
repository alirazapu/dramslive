<?php
defined('SYSPATH') or die('No direct script access.');

class Controller_Personsreports extends Controller_Working {

    public function __Construct(Request $request, Response $response) {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }

    /*
     * Top Search Persons
     */

    public function action_find_imei_last_digit() {
        try {
            //   $imei=355516058106187 ;
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $imei = $post['imei_no'];
            $data = Helpers_Utilities::find_imei_last_digit($imei);
            echo $data;
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_top_search_persons() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 53) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                /* Posted Data */
                $post = $this->request->post();
                $post = Helpers_Utilities::remove_injection($post);
                /* Set Session for post data carrying for the  ajax call */
                Session::instance()->set('top_search_person_post', $post);
                /* Excel Export File Included */
                include 'excel/top_search_person.inc';
                /* File Included */
                include 'user_functions/top_search_person.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  User Ajax Call data for top search persons
     */

    //ajax call for data
    public function action_ajaxtopsearchpersons() {
        try {
            //echo 'Hello'; exit;
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('top_search_person_post', array());
                if (!empty($post) && sizeof($post) > 1 && !empty($_GET['iDisplayStart'])) {
                    $_GET['iDisplayStart'] = 0;
                }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personsreports;
                $rows_count = $data->top_search_persons($post, 'true');

                $profiles = $data->top_search_persons($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $full_name = ( isset($item['first_name']) ) ? $item['first_name'] : 'NA';
                        $full_name .= ' ';
                        $full_name .= ( isset($item['last_name']) ) ? $item['last_name'] : 'NA';
                        $father_name = ( isset($item['father_name']) ) ? $item['father_name'] : 'NA';
                        // $cnic_number = ( isset($item['cnic_number']) ) ? $item['cnic_number'] : 'NA';
                        $cnic_number = ( isset($item['is_foreigner']) && $item['is_foreigner'] == 0 ) ? $item['cnic_number'] : $item['cnic_number_foreigner'];
                        $address = ( isset($item['address']) ) ? $item['address'] : 'NA';
                        $category1 = ( isset($item['person_id']) ) ? Helpers_Person::get_person_category_id($item['person_id']) : "";
                        $category = (isset($category1)) ? Helpers_Utilities::get_category_name($category1) : 'NA';
                        $search_count = ( isset($item['maxtotal']) ) ? $item['maxtotal'] : 0;
                        $login_user = Auth::instance()->get_user();
                        $access = Helpers_Person::sensitive_person_acl($login_user->id, $item['person_id']);

                        if ($access == TRUE) {
                            $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Detail </a>';
                        } else {
                            $member_name_link = 'NO Access';
                        }

                        $row = array(
                            $full_name,
                            $father_name,
                            $cnic_number,
                            $address,
                            $category,
                            $search_count,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }

    /** Top Search Persons */
    public function action_person_list() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 54) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                /* Posted Data */
                $post = $this->request->post();
                /* Set Session for post data carrying for the  ajax call */
                //print_r($_GET); exit;
                if (isset($_GET)) {
                    $post_data = array_merge($post, $_GET);
                    //   $post_data1= Helpers_Utilities::encrypted_key($post_data, "decrypt");
                }
                $post_data = Helpers_Utilities::remove_injection($post_data);
                //print_r($post); exit;
                Session::instance()->set('person_list_post', $post_data);
                /* Excel Export File Included */
                //include 'excel/top_search_person.inc';
                /* File Included */
                include 'user_functions/person_list.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    /** Category wise  Search Persons */
    public function action_person_category_wise_list() {
        try {

            if (Helpers_Utilities::chek_role_access($this->role_id, 54) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                /* Posted Data */
                $post = $this->request->post();

                if (isset($_GET)) {
                    $post_data = array_merge($post, $_GET);



                    //   $post_data1= Helpers_Utilities::encrypted_key($post_data, "decrypt");
                }
                $post_data = Helpers_Utilities::remove_injection($post_data);
//
//echo '<pre>';
//print_r($post_data);
//exit;
                $this->template->content = View::factory('templates/user/person_list_category_wise')->bind('search_post',$post_data);
             //   include 'user_functions/category_wise_person_list.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }
    /** call analysis */
    public function action_person_call_analysis() {

        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 54) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);

                $post = $this->request->post();

                if (isset($_GET)) {
                    $post_data = array_merge($post, $_GET);
                }
//                $pid = Helpers_Utilities::encrypted_key(($_GET['id']), "decrypt");
//                print_r($pid); exit;
                $post_data = Helpers_Utilities::remove_injection($post_data);
                Session::instance()->set('person_list_post', $post_data);

                include 'user_functions/call_analysis.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
//                    ->bind('person_id', $pid);
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call analysis
    public function action_ajax_person_call_analysis() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );
            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('person_list_post', array());
                if (!empty($post) && sizeof($post) > 1 && !empty($_GET['iDisplayStart']) && $_GET['iDisplayStart'] < 10) {
                    $_GET['iDisplayStart'] = 0;
                }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                $data = new Model_Personsreports();
                $rows_count = $data->person_call_analysis($post, 'true');
                $profiles = $data->person_call_analysis($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $user_id = ( isset($item['id']) ) ? $item['id'] : 0;
                        $ph1 = ( isset($item['field_1']) ) ? $item['field_1'] : 0;
                        $ph2 = ( isset($item['field_2']) ) ? $item['field_2'] : 0;
                        $ph3 = ( isset($item['field_3']) ) ? $item['field_3'] : 0;
                        $ph4 = ( isset($item['field_4']) ) ? $item['field_4'] : 0;
                        $ph5 = ( isset($item['field_5']) ) ? $item['field_5'] : 0;
                        $ph6 = ( isset($item['field_6']) ) ? $item['field_6'] : 0;
                        $ph7 = ( isset($item['field_7']) ) ? $item['field_7'] : 0;
                        $ph8 = ( isset($item['field_8']) ) ? $item['field_8'] : 0;
                        $ph9 = ( isset($item['field_9']) ) ? $item['field_9'] : 0;
                        $ph10 = ( isset($item['field_10']) ) ? $item['field_10'] : 0;
                        $status = ( isset($item['status']) ) ? $item['status'] : 0;
                        $row = array(
                            $user_id,
                            $ph1,
                            $ph2,
                            $ph3,
                            $ph4,
                            $ph5,
                            $ph6,
                            $ph7,
                            $ph8,
                            $ph9,
                            $ph10,
                            $status,

                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo json_encode(99);
        }
    }
    //ajax call for data
    public function action_ajaxpersonlist()
    {
        // Disable auto-rendering (Kohana / old HMVC style)
        $this->auto_render = false;

        // Prepare DataTables response structure
        $output = [
            'sEcho'                => (int) ($_GET['sEcho'] ?? 1),
            'iTotalRecords'        => 0,
            'iTotalDisplayRecords' => 0,
            'aaData'               => []
        ];

        // Early exit if not logged in
        if (!Auth::instance()->logged_in()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($output);
            exit;
        }

        try {
            // Merge previous session filters + current GET (careful with this pattern!)
            $post = Session::instance()->get('person_list_post', []);

            // Very common anti-pattern → reset start when filters change (your logic)
            if (!empty($post) && count($post) > 1 && isset($_GET['iDisplayStart']) && (int)$_GET['iDisplayStart'] < 10) {
                $_GET['iDisplayStart'] = 0;
            }

            // Merge current request into filters
            $post = array_merge($post, $_GET ?? []);

            // Very important: remove dangerous keys
            unset($post['PHPSESSID'], $post['session'], $post['_'], $post['callback']);

            // Sanitize / escape input
            $post = Helpers_Utilities::remove_injection($post);

            $model = new Model_Personsreports();

            // Get total count (without limit)
            $total_count = $model->person_list($post, true);

            // Get paginated data
            $profiles = $model->person_list($post, false);

            $output['iTotalRecords']         = (int) $total_count;
            $output['iTotalDisplayRecords']  = (int) $total_count;

            if (empty($profiles)) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($output);
                exit;
            }

            $current_user = Auth::instance()->get_user();

            // ... (previous parts of the function remain the same)

            foreach ($profiles as $item) {
                $person_id = (int) ($item['person_id'] ?? 0);
                if ($person_id === 0) continue;

                // ────────────────────────────────────────────────
                // Person basic data
                // ────────────────────────────────────────────────
                $person_name_raw = Helpers_Person::get_person_name($person_id) ?? '—';
                $person_cnic     = Helpers_Person::get_person_cnic($person_id)   ?? '—';

                $current_user = Auth::instance()->get_user();
                $has_access   = Helpers_Person::sensitive_person_acl($current_user->id, $person_id);

                // Build display name + link
                $name_display = htmlspecialchars($person_name_raw . ' (' . $person_cnic . ')', ENT_QUOTES, 'UTF-8');

                if ($has_access) {
                    $profile_url = URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id, 'encrypt'));
                    $person_cell = '<a href="' . htmlspecialchars($profile_url, ENT_QUOTES, 'UTF-8') . '" target="_blank" title="View Profile">'
                        . $name_display
                        . ' <span style="color:#0066cc; font-weight:bold;">[ View Profile ]</span></a>';
                } else {
                    $person_cell = $name_display . ' <span class="text-danger" title="No permission">[ NO ACCESS ]</span>';
                }

                // ────────────────────────────────────────────────
                // Watchlist tags (appended after the name/link)
                // ────────────────────────────────────────────────
                $watchlist_html = '';

                $user_id     = (int) ($item['user_id'] ?? 0);
                $district_id = 0;

                if ($user_id > 0) {
                    $user_data   = Helpers_Watchlist::get_user_data($user_id);
                    $user_name   = $user_data->name         ?? '—';
                    $designation = $user_data->job_title    ?? '—';
                    $district_id = (int) ($user_data->district_id ?? 0);
                    $user_type   = Helpers_Utilities::get_user_role_name($user_id) ?? '—';
                } else {
                    $user_name   = 'NA';
                    $designation = 'NA';
                    $user_type   = 'NA';
                }

                $region_district = $user_id > 0 ? Helpers_Profile::get_user_region_district($user_id) : '—';

                if ($district_id > 0) {
                    $person_tags = Helpers_Watchlist::in_watchlist($person_id, $district_id);
                    if (!empty($person_tags)) {
                        foreach ($person_tags as $tag) {
                            $dist_name = Helpers_Utilities::get_district($tag['tag_district_id'] ?? 0);
                            $watchlist_html .= ' <span class="badge badge-dark">' . htmlspecialchars($dist_name ?? '?') . '</span>';
                        }
                    }
                }

                // Final content for first column (name + link + tags)
                $person_cell .= $watchlist_html;

                // ────────────────────────────────────────────────
                // Organizations
                // ────────────────────────────────────────────────
                $org_ids = Helpers_Person::get_person_affiliation($person_id);
                $person_org = 'NA';

                if (!empty($org_ids)) {
                    $ids = array_filter(array_column($org_ids, 'organization_id' ?? 'id'), 'is_numeric');
                    if ($ids) {
                        $person_org = Helpers_Person::get_person_organizations(implode(',', $ids)) ?? '—';
                    }
                }

                // ────────────────────────────────────────────────
                // Category
                // ────────────────────────────────────────────────
                $category_id   = (int) ($item['category_id'] ?? 0);
                $category_name = '—';
                switch ($category_id) {
                    case 0:  $category_name = 'White'; break;
                    case 1:  $category_name = 'Gray';  break;
                    case 2:  $category_name = 'Black'; break;
                }

                // ────────────────────────────────────────────────
                // Final row
                // ────────────────────────────────────────────────
                $row = [
                    $person_cell,                           // ← full link + name + CNIC + tags
                    $category_name,
                    htmlspecialchars($person_org ?? '—', ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($item['added_on'] ?? '—', ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($user_name,   ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($user_type,   ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($region_district ?? '—', ENT_QUOTES, 'UTF-8'),
                ];

                $output['aaData'][] = $row;
            }

// ... rest of the function (json_encode + exit)

        } catch (Exception $e) {
            // In production: log error, return clean error
            error_log("Person list AJAX error: " . $e->getMessage());
            $output['error'] = 'Internal server error';
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($output);
        exit;
    }

    /** Sensitive Person's List */
    public function action_sensitive_person_list() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 56) == 1) {
                //echo 'hellpo'; exit;
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                /* Posted Data */
                $post = $this->request->post();
                /* Set Session for post data carrying for the  ajax call */
                //print_r($_GET); exit;
                if (isset($_GET)) {
                    $post_data = array_merge($post, $_GET);
                }
                $post_data = Helpers_Utilities::remove_injection($post_data);
                //print_r($post); exit;
                Session::instance()->set('senstive_person_list_post', $post_data);
                /* Excel Export File Included */
                //include 'excel/top_search_person.inc';
                /* File Included */
                $this->template->content = View::factory('templates/user/sensitive_person_list')
                        ->set('search_post', $post_data);
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxsensitivepersonlist() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {

                $post = Session::instance()->get('senstive_person_list_post', array());
                if (!empty($post) && sizeof($post) > 1 && !empty($_GET['iDisplayStart'])) {
                    $_GET['iDisplayStart'] = 0;
                }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personsreports();
                $rows_count = $data->sensitve_person_list($post, 'true');
                $profiles = $data->sensitve_person_list($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);                        
                $userslist = [842, 137, 2031,1761, 2603];
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        //Person Data
                        $person_id = ( isset($item['person_id']) ) ? $item['person_id'] : 'NA';
                        $first_name = ( isset($item['first_name']) ) ? $item['first_name'] : ' ';
                        $last_name = ( isset($item['last_name']) ) ? $item['last_name'] : ' ';
                        $person_name = $first_name . " " . $last_name;
                        $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($person_id, "encrypt")) . '" > [ View Profile ] </a>';
                        $person_cnic = ( isset($item['is_foreigner']) && $item['is_foreigner'] == 0 ) ? $item['cnic_number'] : $item['cnic_number_foreigner'];
                        $added_on = ( isset($item['added_on']) ) ? $item['added_on'] : 'NA';
                        //User Data
                        $user_id = ( isset($item['adding_user']) ) ? $item['adding_user'] : 'NA';
                        $user_name = ( $user_id ) ? Helpers_Utilities::get_user_name($user_id) : 'NA';
                        $user_type = ( $user_id ) ? Helpers_Utilities::get_user_role_name($user_id) : 'NA';
                        $designation = ( $user_id ) ? Helpers_Utilities::get_user_job_title($user_id) : 'NA';                        
                        
                        $action = '';
                        if (($permission == 1) && (in_array($login_user->id, $userslist))) {
                            $action= '<a class="btn btn-small action" href="javascript:ConfirmChoice(' .  $item['adding_user'] . ',' . $item['person_id'] .')"><i class="fa fa-trash"></i> Delete</a>';
                        }
                        $row = array(
                            $person_name . $member_name_link,
                            $person_cnic,
                            $added_on,
                            $user_name,
                            $user_type,
                            $designation,
                            $action
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }
    
    
    public function action_sensitive_person_del() {
        if (Auth::instance()->logged_in()) {
            try {
                $userid = (int) $this->request->param('id');
                $userid = Helpers_Utilities::remove_injection($userid);
                $personid = (int) $this->request->param('id2');
                $personid = Helpers_Utilities::remove_injection($personid);
            } catch (Exception $e) {
                
            }
            $user_obj = Auth::instance()->get_user();
            $login_user_id = $user_obj->id;
                try {
                    $del = New Model_Personsreports;
                    $result = $del->sc_deleted($userid, $personid);
                } catch (Exception $e) {
                    echo '<pre>';
                    //print_r($e); 
                    exit;
                }
                echo $result;
           // }
        } else {
            return 0;
        }
    }
    
    /** Project Affiliate persons */
    public function action_project_persons() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 55) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                /* Posted Data */
                $post = $this->request->post();
                /* Set Session for post data carrying for the  ajax call */
                //print_r($_GET); exit;
                if (isset($_GET)) {
                    $post_data = array_merge($post, $_GET);
                }
                $post_data = Helpers_Utilities::remove_injection($post_data);
                //print_r($post); exit;
                Session::instance()->set('project_persons_post', $post_data);
                /* Excel Export File Included */
                //include 'excel/top_search_person.inc';
                /* File Included */
                include 'user_functions/project_persons.inc';
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxprojectpersons() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {

                $post = Session::instance()->get('project_persons_post', array());
                if (!empty($post) && sizeof($post) > 1 && !empty($_GET['iDisplayStart'])) {
                    $_GET['iDisplayStart'] = 0;
                }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personsreports();
                $rows_count = $data->project_persons($post, 'true');
                $profiles = $data->project_persons($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $project_id = ( isset($item['id']) ) ? $item['id'] : 'NA';
                        $project_name = ( isset($item['project_name']) ) ? $item['project_name'] : 'NA';
                        $project_region_id = ( isset($item['region_id']) ) ? $item['region_id'] : 0;
                        $project_region_name = ( isset($item['region_id']) ) ? Helpers_Utilities::get_region($project_region_id) : 'Unknown';
                        //district
                        $project_district = ( isset($item['district_id']) ) ? Helpers_Utilities::get_district($item['district_id']) : 'UnKnown';

                        $project_status = ( isset($item['project_status']) && ($item['project_status'] == 0) ) ? 'Open' : 'Close';

                        $project_org_ids= Helpers_Person::get_project_affiliation($project_id);

                        $project_org_id='';
                        if(!empty($project_org_ids)){
                            $values = array_map('array_pop', $project_org_ids);
                            $project_org_id = implode(',', $values);
                        }
                        $project_org= !empty($project_org_id)? Helpers_Person::get_project_organizations($project_org_id) :'NA';

                        $grey_persons = Helpers_Utilities::get_project_persons($project_id, 1);
                        $black_persons = Helpers_Utilities::get_project_persons($project_id, 2);
                        $total_persons = $grey_persons + $black_persons;
                        $e_project_id = Helpers_Utilities::encrypted_key($project_id, "encrypt");
                        $e_id_one = Helpers_Utilities::encrypted_key(1, "encrypt");
                        $e_id_two = Helpers_Utilities::encrypted_key(2, "encrypt");
                        if ($grey_persons > 0) {
                            $grey_persons_link = '<a href="person_list/?category=' . $e_id_one . '&project_id=' . $e_project_id . '" > [ View Persons ] </a>';
                            $grey_persons .= $grey_persons_link;
                        }
                        if ($black_persons > 0) {
                            $black_persons_link = '<a href="person_list/?category=' . $e_id_two . '&project_id=' . $e_project_id . '" > [ View Persons ] </a>';
                            $black_persons .= $black_persons_link;
                        }
                        $row = array(
                            $project_name,
                            $project_org,
                            $project_region_name,
                            $project_district,
                            $project_status,
                            $grey_persons,
                            $black_persons,
                            $total_persons,
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
                     echo '<pre>';
         print_r($ex );
         exit();
            
        }
    }

    /*
     *  User Person Devices List(person_devices)
     */

    public function action_person_devices() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            if (isset($_GET)) {
                $post_data = array_merge($post, $_GET);
            }
            $post_data = Helpers_Utilities::remove_injection($post_data);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('person_devices_post', $post_data);
            //get Person id from session
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            /* File Included */
            include 'persons_functions/person_devices.inc';
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  Person Device Details(person_devices)
     */

    public function action_ajaxdevicedetail() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('person_devices_post', array());
                //get person id from session
                // $pid = Session::instance()->get('personid');
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                if (!empty($post) && sizeof($post) > 1 && !empty($_GET['iDisplayStart'])) {
                    $_GET['iDisplayStart'] = 0;
                }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personsreports;
                $rows_count = $data->person_devices($post, 'true', $pid);
                $profiles = $data->person_devices($post, 'false', $pid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        $device = ( isset($item['phone_name']) ) ? $item['phone_name'] : 'NA';
                        $imei = ( isset($item['imei_number']) ) ? $item['imei_number'] : 'NA';
                        $prsim = ( isset($item['phonenumber']) ) ? $item['phonenumber'] : 'NA';
                        $person_id = ( isset($item['person_id']) ) ? $item['person_id'] : 'NA';
                        $usingsince = ( isset($item['in_use_since']) ) ? $item['in_use_since'] : 'NA';
                        $lastint = ( isset($item['last_interaction_at']) ) ? $item['last_interaction_at'] : 'NA';
                        $link = '<a class="custom-cursor" onclick="requestimeicdr(' . $item['imei_number'] . ',' . $item['person_id'] . ')" > <span class="label label-primary" style="margin-left:5px;margin-right:5px">Request CDR </span></a>';
                        $row = array(
                            $device,
                            $imei,
                            $prsim,
                            $usingsince,
                            $lastint,
                            $link
                        );
                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }

    /*
     *  Person Device Details(person_devices) against imei
     */

    public function action_ajaximeidetail() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                //get person id from session
                $imei = $this->request->param('id');
                $imei = Helpers_Utilities::remove_injection($imei);
                if (!empty($imei)) {
                    $post = Session::instance()->get('person_devices_post', array());
                    $post = Helpers_Utilities::remove_injection($post);
                    //  print_r($imei); exit;

                    $data = new Model_Personsreports;
                    $rows_count = $data->person_imeidevices($post, 'true', $imei);
                    $profiles = $data->person_imeidevices($post, 'false', $imei);

                    if (isset($profiles) && sizeof($profiles) <= 0) {
                        $output['iTotalRecords'] = 0;
                        $output['iTotalDisplayRecords'] = 0;
                    } else {
                        $output['iTotalRecords'] = $rows_count;
                        $output['iTotalDisplayRecords'] = $rows_count;
                    }

                    if (isset($profiles) && sizeof($profiles) > 0) {
                        foreach ($profiles as $item) {
                            //Device information
                            $device = ( isset($item['phone_name']) ) ? $item['phone_name'] : 'NA';
                            $deviceuserid = ( isset($item['device_user_id']) ) ? $item['device_user_id'] : '-1';

                            if ($deviceuserid != 0 && $deviceuserid != -1) {
                                $deviceusername = Helpers_Person::get_person_name($deviceuserid);
                            } else {
                                $deviceusername = "No User";
                            }
                            //  print_r($deviceusername); exit;  
                            // Mobile Number Information
                            $prsim = ( isset($item['phonenumber']) ) ? $item['phonenumber'] : -1;
                            $simfirstuse = ( isset($item['sim_first_use']) ) ? $item['sim_first_use'] : "NA";
                            $simlastuse = ( isset($item['sim_last_use']) ) ? $item['sim_last_use'] : "NA";
                            //sim owner links
                            $simownerid = Helpers_Person::get_mobile_ownerid_with_mobilenumber($prsim);
                            $simownername = Helpers_Person::get_person_name($simownerid);
                            if ($simownername == 'Unknown') {
                                // $mobileownerlink='<a class="custom-cursor" onclick="requestsub('. $prsim.','.Helpers_Utilities::encrypted_key($simownerid,"encrypt").')"> <span title="No Person Exist Click For Subscriber" class="label label-primary" >Request Subscriber </span></a>';
                                $mobileownerlink = '<span title="No Person Exist Request For Subscriber" class="label label-primary" >Unknown </span>';
                            } else {
                                $mobileownerlink = '<a title="Click to check person dashboard" href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($simownerid, "encrypt")) . '" >  ' . $simownername . '</a>';
                            }
                            //sim user link
                            $simuserid = Helpers_Person::get_mobile_userid_with_mobilenumber($prsim);
                            // print_r($simuserid); exit;
                            if ($simownerid == $simuserid && $simownerid != -1) {
                                $mobileuserlink = "Self";
                            } else {
                                $simusername = Helpers_Person::get_person_name($simuserid);
                                if ($simusername == 'Unknown') {
                                    //$mobileuserlink = '<a class="custom-cursor" onclick="requestsub(' . $prsim . ')"> <span class="label label-primary" >' . $simusername . ' </span></a>';
                                    $mobileuserlink = '<span title="No Person Exist Request For Subscriber" class="label label-primary" >Unknown </span>';
                                } else {
                                    $mobileuserlink = '<a title="Click to check person dashboard" href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($simuserid, "encrypt")) . '" >  ' . $simusername . '</a>';
                                }
                            }

                            //current user
                            $currentuser = "No";
                            if ($deviceuserid == $simuserid && $simuserid != -1 && $deviceuserid = -1) {
                                $currentuser = "Yes";
                            }
                            $simowneridforsub = !empty($simownerid) ? $simownerid : 0;
                            //otherlinks
                            $link = '<a class="custom-cursor" onclick="requestsub(' . $prsim . ',' . $simowneridforsub . ')"> <span class="label label-primary" >Request Subscriber </span></a>';

                            $row = array(
                                $prsim,
                                $mobileownerlink,
                                $mobileuserlink,
                                $simfirstuse,
                                $simlastuse,
                                // $currentuser,
                                $deviceusername,
                                $link
                            );
                            $output['aaData'][] = $row;
                        }
                    }
                } else {
                    $output = '';
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }

    /*
     *  Person cnic sims detail against cnic
     */

    public function action_ajaxcnicsimsdetails() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                //get person id from session
                $cnic = $this->request->param('id');
                $cnic = Helpers_Utilities::remove_injection($cnic);
                if (!empty($cnic)) {


                    $post = Session::instance()->get('person_cnicsims_post', array());
                    $post = Helpers_Utilities::remove_injection($post);
                    //  print_r($imei); exit;

                    $data = new Model_Personsreports;
                    $rows_count = $data->person_simsagainstcnic($post, 'true', $cnic);
                    $profiles = $data->person_simsagainstcnic($post, 'false', $cnic);

                    if (isset($profiles) && sizeof($profiles) <= 0) {
                        $output['iTotalRecords'] = 0;
                        $output['iTotalDisplayRecords'] = 0;
                    } else {
                        $output['iTotalRecords'] = $rows_count;
                        $output['iTotalDisplayRecords'] = $rows_count;
                    }

                    if (isset($profiles) && sizeof($profiles) > 0) {
                        foreach ($profiles as $item) {
                            //Device information
                            $personid = ( isset($item['person_id']) ) ? $item['person_id'] : 'NA';
                            $phoneno = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                            $simuserid = ( isset($item['sim_user_id']) ) ? $item['sim_user_id'] : 'NA';
                            $simlastuse = ( isset($item['sim_last_used_At']) ) ? $item['sim_last_used_At'] : 'NA';
                            $simact = ( isset($item['sim_activated_at']) ) ? $item['sim_activated_at'] : 'NA';
                            $status = ( isset($item['status']) ) ? $item['status'] : 'NA';
                            if ($status == 1) {
                                $status = 'active';
                            } else {
                                $status = 'InActive';
                            }
                            $company = ( isset($item['company_name']) ) ? $item['company_name'] : 'NA';


                            if ($personid == $simuserid && $simuserid != -1 && $simuserid != "NA" && $simuserid != "") {
                                $simusername = "Self";
                            } else {
                                $simusername = Helpers_Person::get_person_name($simuserid);
                                $simusername .= '<a title="Click to view person dashboard" href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($simuserid, "encrypt")) . '" >(Profile)</a>';
                            }

                            $row = array(
                                $phoneno,
                                $simact,
                                $simlastuse,
                                $status,
                                $company,
                                $simusername
                            );
                            $output['aaData'][] = $row;
                        }
                    }
                } else {
                    $output = '';
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }

    /*
     *  User Person SIMs List(person_sims)
     */

    public function action_person_sims() {
        try {
            /* Posted Data */
            $post = $this->request->post();
            if (isset($_GET)) {
                $post_data = array_merge($post, $_GET);
            }
            $post_data = Helpers_Utilities::remove_injection($post_data);
            /* Set Session for post data carrying for the  ajax call */
            Session::instance()->set('person_sims_post', $post_data);
            //get Person id from session
            $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            //$pid = Session::instance()->get('personid');
            /* File Included */
            include 'persons_functions/person_sims.inc';
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  Person SIMs Details(person_sims)
     */

    public function action_ajaxsimsdetail() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('person_sims_post', array());
                //get person id from session
                // $pid = Session::instance()->get('personid');
                $pid = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");


                if (!empty($post) && sizeof($post) > 1 && !empty($_GET['iDisplayStart'])) {
                    $_GET['iDisplayStart'] = 0;
                }

                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personsreports;
                $rows_count = $data->person_sims($post, 'true', $pid);
                //print_r($rows_count); exit;
                $profiles = $data->person_sims($post, 'false', $pid);

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }

                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {

                        $ownerid = ( isset($item['sim_owner']) ) ? $item['sim_owner'] : '';
                        $userid = ( isset($item['person_id']) ) ? $item['person_id'] : '';
                        // owner info
                        if ($ownerid == $pid && $ownerid != '') {
                            $ownername = "Self";
                        } else {
                            $ownername = (!empty($ownerid) ) ? Helpers_Person::get_person_name($ownerid) : 'NA';
                            if ($ownername != "NA") {
                                $ownername = '<a  href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($ownerid, "encrypt")) . '" > ' . $ownername . ' </a>';
                            }
                        }
                        //user info
                        if ($pid == $userid && $userid != '') {
                            $username = "Self";
                        } else {
                            $username = (!empty($userid) ) ? Helpers_Person::get_person_name($userid) : 'NA';
                            if ($username != "NA") {
                                $username = '<a  href="' . URL::site('persons/dashboard?id=' . Helpers_Utilities::encrypted_key($userid, "encrypt")) . '" > ' . $username . ' </a>';
                            }
                        }

                        $number = ( isset($item['phone_number']) ) ? $item['phone_number'] : 'NA';
                        // link with cyber portal

                        $data = [
                            'mob' => $number,
                            'key_' => 'SZEhiAeCdhIJgQdcbqJc2td5tWZn4Xqu',

                        ];

                       /* $cURLConnection = curl_init('http://www.ctw.ctdpunjab.com/api/ctw_exist');
                        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $data);
                        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
                        $apiResponse = curl_exec($cURLConnection);
                        curl_close($cURLConnection);
                        $jsonArrayResponse = json_decode($apiResponse);
                        $ctw_res = $jsonArrayResponse ;*/
                        $cyber_link='';
                        /*
                        if(!empty($ctw_res->url)){
                            foreach ($ctw_res->url as $i=> $res) {

                                $cyber_link .=' '. '<a  href="' . ($res) . '" target="_blank" > CTW </a>';

                               }
                        }
                        */

                        $last = ( isset($item['sim_last_used_at']) ) ? $item['sim_last_used_at'] : 'NA';
                        $contact_type = (!empty($item['contact_type']) ) ? $item['contact_type'] : '';
                        $contact_type = !empty($contact_type) ? Helpers_Utilities::get_contact_type($contact_type) : 'Unknown';
                        $company1 = ( isset($item['mnc']) ) ? Helpers_Utilities::get_companies_data($item['mnc']) : 'NA';
                        $company = isset($company1->company_name) ? $company1->company_name : "Unknown";
                        if ($company == "") {
                            $company = "Unknown";
                        }
                        $status = ( isset($item['status']) ) ? $item['status'] : 'NA';
                        $type = ( isset($item['connection_type']) ) ? $item['connection_type'] : 'NA';
                        $act = ( isset($item['sim_activated_at']) ) ? $item['sim_activated_at'] : 'NA';
                        $link1 = '<a title="Click To Search Subscriber"  href="#" onclick="external_search_model(' . $number . ')"> <span class="label label-primary" >Subscriber </span></a>';
                        $link2 = '<a class="custom-cursor" onclick="requestlocation(' . $item['phone_number'] . ', ' . $userid . ')"> <span class="label label-primary" style="margin-left:5px">Current Location </span></a>';
                        $link3 = '<a class="custom-cursor" onclick="requestcdr(' . $item['phone_number'] . ', ' . $userid . ')" > <span class="label label-primary" style="margin-left:5px;margin-right:5px">CDR </span></a>';
                        $link4 = '<a class="custom-cursor" onclick="requestsmsdetails(' . $item['phone_number'] . ', ' . $userid . ')" > <span class="label label-primary" style="margin-left:5px;margin-right:5px">SMS Detail </span></a>';


                        $cdr_status = Helpers_Person::get_person_cdr_status($userid, $number);
                        if ($cdr_status == 1) {
                            $link5 = '<a href="' . URL::site('persons/cdr_summary/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > <span class="label label-success">View CDR </span></a>';
                        } else {
                            $link5 = "";
                        }
                        if ($item['mnc'] == 3 || $item['mnc'] == 4) {
                            $link = $link1 . $link2 . $link3 . $link4;
                        } else {
                            $link = $link1 . $link2 . $link3;
                        }
                        $action = $link5;
                        if ($status == 1) {
                            $status = "Active";
                        } elseif ($status == 0) {
                            $status = "InActive";
                        } else {
                            $status = "NA";
                        }
                        if ($type == 1) {
                            $type = "Prepaid";
                        } elseif ($type == 0) {
                            $type = "Postpaid";
                        } else {
                            $type = "NA";
                        }

                        $row = array(
                            $number,
                            $ownername,
                            $username,
                            $last,
                            $company,
                            $status,
                            $type,
                            $act,
                            $contact_type,
                            $cyber_link,
                            $link,
                            $action
                        );
                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }

    /*
     *  Person SIMs Details(person_sims)
     */

    public function action_device_information() {
        try {
            $this->auto_rednder = false;
            if (($_POST['imei_no'])) {
                $_POST = Helpers_Utilities::remove_injection($_POST);
                $deviceinfo = Helpers_Utilities::get_device_information($_POST['imei_no']);
                // print_r($deviceinfo); exit;
                $device = (!empty($deviceinfo->phone_name) ) ? $deviceinfo->phone_name : 'NA';
                $usingsince = (!empty($deviceinfo->in_use_since) ) ? $deviceinfo->in_use_since : 'NA';
                $lastint = (!empty($deviceinfo->last_interaction_at) ) ? $deviceinfo->last_interaction_at : 'NA';

                $deviceuserid = (!empty($deviceinfo->person_id) ) ? $deviceinfo->person_id : '-1';
                //  print_r($deviceuserid); exit;
                $deviceusername = Helpers_Person::get_person_name($deviceuserid);
                $imei = ( isset($deviceinfo->imei_number) ) ? $deviceinfo->imei_number : $_POST['imei_no'];


                //  $devicelink=$imei."(".$deviceuserlink.")";
                ?>
                <ul class="todo-list">
                    <li>                
                        <span class="text-black"> <b>Device IMEI: </b><?php echo $imei; ?> <a class="active pull-right custom-cursor"  title="Request New CDR Against IMEI" onclick="requestimeicdr(<?php echo $imei; ?>);" ><span class="label label-primary" style="font-size: 13px;" >Request New CDR</span></a></span>
                    </li>
                    <li>
                        <span class="text-black"> <b>Device Name: </b><?php echo $device; ?><a class="active pull-right custom-cursor"  title="Update Device Name" onclick="changedevicename(1)" ><span class="label label-primary" style="font-size: 13px;" >Update</span></a> </span>
                        <div id="updatedevicename" style="display: none" class="col-lg-12">
                            <form role="form" name="imeidevicenameform" id="imeidevicenameform"  action="<?php echo URL::site("user/ajaxupdatedevicename"); ?>" method="post" enctype="multipart/form-data">
                                <div class="row" >  
                                    <hr class="style14 col-md-10"> 
                                    <a class="act-lnk pull-right custom-cursor"  onclick="changedevicename(2);" style="margin-top:10px; margin-right: 5px; color: red"><b>(Hide)</b></a>
                                    <div class="form-group col-lg-10">  
                                        <label title="Update device name"  for="phone_name" class="control-label">Search Device Name: <a title="Click here to find device name" class="btn" target="_blank" href="http://www.imei.info/" <!--onclick="findphonenumber();" --> > 
                                                                                                                                         Click Here                                                     
                                        </a>
                                    </label>
                                    <input title="Update device name, leave blank if not necessary" name="imeiphonenameupdate" type="text" class="form-control" id="imeiphonenameupdate" placeholder="Update Device Name"> 
                                </div>
                                <div class="col-lg-2">
                                    <button type="button" style="margin-top: 38px; margin-left: -20px"  class="btn btn-primary pull-left" onclick="updateimeidevicename();" >Save</button>
                                </div>
                                <hr class="style14 col-md-10"> 
                            </div>
                        </form>
                    </div>
                </li>
                <li>
                <?php if ($deviceusername == 'Unknown') { ?>
                        <a title="This device is not linked with any person" > <span class="text-black"> <b>Device User:</b> No User Exist</span></a>
                <?php } else { ?>
                        <a title="Click to check person dashboard" href="<?php echo URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($deviceuserid, "encrypt")); ?>"> <span class="text-black"> <b>Device User:</b> <?php echo $deviceusername; ?> </span><span class="active">(Profile)</span></a>
                <?php } ?>
                </li>
                <li>
                    <span class="text-black"> <b>In Use Since: </b><?php echo $usingsince; ?> </span>
                </li>
                <li>
                    <span class="text-black"> <b>Last Activity Time: </b><?php echo $lastint; ?> </span>
                </li>
                <li>
                    <span class="text-black"> <b>SIMs Against IMEI: </b><a title="Manual Add SIMs Against IMEI#" href="#" onclick="imeisimsmanualentry();"  ><span class="label label-primary pull-right simsuploadimei" style="font-size: 13px;">Manual Update SIMs</span></a> </span>
                </li>            
                </ul>   
                <?php
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    /*
     *  last updated cdr against imei# status
     */

    public function action_last_update_imei_cdr_status() {
        try {
            $this->auto_rednder = false;
            $_POST = Helpers_Utilities::remove_injection($_POST);
            if (!empty($_POST['imei_no'])) {
                $imei = $_POST['imei_no'];
                // get last update imei cdr data       
                $getstatus = Helpers_Upload::get_last_update_imei_cdr_data($imei);
                $cdrfrom = (!empty($getstatus->data_from_date)) ? $getstatus->data_from_date : "NA";
                $cdrto = (!empty($getstatus->data_to_date)) ? $getstatus->data_to_date : "NA";
                $updatetime = (!empty($getstatus->created_on)) ? $getstatus->created_on : "NA";
                $uploadstatusvalue = (isset($getstatus->upload_status)) ? $getstatus->upload_status : -1;
                $filesize = (!empty($getstatus->size)) ? $getstatus->size : 0;
                $no_records = (!empty($getstatus->no_of_record)) ? $getstatus->no_of_record : 0;
                $upload_id = (!empty($getstatus->id)) ? $getstatus->id : 0;
                $uploadtype = (isset($getstatus->is_manual)) ? $getstatus->is_manual : '';
                $uploadmnc = (!empty($getstatus->company_name)) ? $getstatus->company_name : '';
                $uploadcompany = (!empty($uploadmnc)) ? Helpers_Utilities::get_companies_data($uploadmnc) : '';
                $uploadcompany = !empty($uploadcompany) ? $uploadcompany->company_name : 'NA';
                if (!empty($uploadtype) && $uploadtype == 2) {
                    $uploadtype = 'Auto';
                } else {
                    $uploadtype = 'Manual';
                }
                $file = (!empty($getstatus->file)) ? $getstatus->file : "NA";
                $uploadby = (!empty($getstatus->created_by)) ? $getstatus->created_by : "NA";


                if ($uploadby != "NA") {
                    $userprofile = Helpers_Profile::get_user_perofile($uploadby);
                    $username = (!empty($userprofile->first_name)) ? $userprofile->first_name . " " . $userprofile->last_name : "NA";
                    $desig = (!empty($userprofile->job_title)) ? $userprofile->job_title : "NA";
                    if ($desig != "NA" || $desig != "") {
                        $uploadby = $desig . " " . $username;
                    } else {
                        $uploadby = $username;
                    }
                }
                //get uplaoded status
                //$uploadstatus = Helpers_Upload::get_last_update_imei_cdr_status($imei);
                if ($uploadstatusvalue == -1) {
                    $uploadstatus = "No CDR Uploaded";
                } else {
                    $uploadstatus = Helpers_Utilities::get_cdr_upload_status($uploadstatusvalue);
                }
                $uploadstatus = $uploadstatus . " (" . $uploadcompany . ")";
                //get upload confirmation
                $uploadconfirmation = Helpers_Upload::check_sims_subscribers_updated($imei);
                $confirm_action = "";
                if ($uploadconfirmation == 1 && $uploadstatusvalue == 1) {
                    $confirmationmsg = 'Please confirm to upload CDR';
                    $confirmationmsg = '<a title="Please confirm to upload data" id="confirm_action_link" href="#" onclick="imeicdruploadconfirm(' . $upload_id . ');" class="label label-primary pull-right" style="margin-right: 1px; font-size: 14px"><b>Confirm Upload</b></a>';
                } elseif ($uploadconfirmation == 1 && $uploadstatusvalue == 2) {
                    $confirmationmsg = "Data is updated";
                } elseif ($uploadconfirmation == 2 && $uploadstatusvalue == 1) {
                    $confirmationmsg = "Subscriber details for all sims required";
                } else {
                    $confirmationmsg = "Upload new CDR";
                }
                ?>
                <ul class="todo-list">
                    <li>
                        <span class="text-black"><b>Last Uploaded CDR: </b><?php echo $file . " (" . $filesize . " kb)"; ?> </span><span><a title="Manual Upload CDR Against IMEI#" href="#" onclick="imeicdr();" class="label label-primary pull-right cdruploadimei" style="margin-right: 1px; font-size: 14px"><b>Upload New CDR</b></a></span>
                    </li>
                    <li>
                        <span class="text-black"><b>Data Contain: </b><?php
                if ($cdrfrom == "NA") {
                    echo "Information Not Found";
                } else {
                    echo "<b>From</b> " . $cdrfrom . " <b>To</b> " . $cdrto;
                }
                ?> </span>
                    </li>
                    <li>
                        <span class="text-black"><b>Uploaded On: </b><?php echo $updatetime . " Records:" . $no_records; ?> </span>
                    </li>
                    <li>
                        <span class="text-black"><b>Uploaded By: </b><?php echo $uploadby . " (<b>" . $uploadtype . "</b>)"; ?> </span>
                    </li>
                    <li>
                        <span class="text-black"><b>Upload Status: </b><span style="font-size: 13px;color: <?php
                If ($uploadstatusvalue == 2) {
                    echo "green";
                } elseif ($uploadstatusvalue == 2) {
                    echo "blue";
                } else {
                    echo "red";
                }
                ?>; "  ><?php echo $uploadstatus; ?></span> </span>
                    </li>
                    <li>
                        <span class="text-black"><b>Action Required: </b><span style="font-size: 13px;color: <?php
                If ($uploadconfirmation == 1 && $uploadstatusvalue == 1) {
                    echo "blue";
                } elseif ($uploadconfirmation == 1 && $uploadstatusvalue == 2) {
                    echo "green";
                } else {
                    echo "red";
                }
                ?>; "  ><?php echo $confirmationmsg; ?></span><span> <?php
                                                                               If ($uploadconfirmation == 1 && $uploadstatusvalue == 1) {
                                                                                   echo $confirm_action;
                                                                               }
                                                                               ?></span> </span>     
                    </li>
                </ul>
                <?php
            }
        } catch (Exception $ex) {
            echo json_encode(2);
        }
    }

    public function action_person_breakup_report() {
        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 60) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                $access_message = 'Access denied, Contact your technical support team';
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if ($permission == 3) {
                    $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
                    $region_id = $login_user_profile->region_id;
                    $region_id_encrypted = !empty($region_id) ? Helpers_Utilities::encrypted_key($region_id, "encrypt") : '';
                    $this->redirect('userreports/request_breakup_district/?id=' . $region_id_encrypted);
                } else {
                    Session::instance()->set('person_breakup_post', $post);
//                    include 'excel/request_breakup_region.inc';
                    $this->template->content = View::factory('templates/user/adminreports/person_breakup_region')
                            ->bind('search_post', $post);
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    public function action_ajaxpersonbreakupreport() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('person_breakup_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['startdate']) && !empty($post['enddate'])) {
                    $date1 = strtotime($post['startdate']);
                    $date2 = strtotime($post['enddate']);
                }
                $data = new Model_Personsreports;
                $rows_count = $data->person_breakup_region($post, 'true');
                $profiles = $data->person_breakup_region($post, 'false');


                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $region = ( isset($item['region_id']) ) ? $item['region_id'] : 0;
                        $region_id_encrypted = !empty($item['region_id']) ? Helpers_Utilities::encrypted_key($item['region_id'], "encrypt") : '';
                        $region_name = Helpers_Utilities::get_region($region);
                        $total_person = ( isset($item['total_person']) ) ? $item['total_person'] : 0;
                        if (isset($date1) && isset($date2)) {
                            $member_name_link = '<a class="btn btn-small action" href="' . URL::site('Personsreports/person_breakup_district/?id=' . $region_id_encrypted) . '&startdate=' . $date1 . '&enddate=' . $date2 . '"><i class="fa fa-folder-open-o"></i> View Detail</a>';
                        } else {
                            $member_name_link = '<a class="btn btn-small action" href="' . URL::site('Personsreports/person_breakup_district/?id=' . $region_id_encrypted) . '"><i class="fa fa-folder-open-o"></i> View Detail</a>';
                        }
                        $row = array(
                            $region_name,
                            $total_person,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
        }
    }

    /*     * District wise break up report */

    public function action_person_breakup_district() {
        try {
            $DB = Database::instance();
            $login_user = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($login_user->id);
            $access_message = 'Access denied, Contact your technical support team';
            if ($permission == 1 || $permission == 2 || $permission == 3 || $permission == 5) {
                $post = $this->request->post();
                if (!empty($_GET['startdate']) && !empty($_GET['enddate'])) {
                    $_GET['startdate'] = date('m/d/Y', ($_GET['startdate']));
                    $_GET['enddate'] = date('m/d/Y', ($_GET['enddate']));
                }
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);

                Session::instance()->set('person_breakup_district_post', $post);
                $this->template->content = View::factory('templates/user/adminreports/person_breakup_district')
                        ->set('search_post', $post);
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    /*
     *  Region wise break up report Ajax Call data
     */

    public function action_ajaxpersonbreakupreportdist() {
        try {
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('person_breakup_district_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                if (!empty($post['startdate']) && !empty($post['enddate'])) {

                    $date1 = strtotime($post['startdate']);
                    $date2 = strtotime($post['enddate']);
                }
                try {
                    $post['region_id'] = !empty($post['id']) ? (int) Helpers_Utilities::encrypted_key($post['id'], "decrypt") : 0;
                } catch (Exception $e) {
                    $post['region_id'] = 0;
                }

                $data = new Model_Personsreports;
                $rows_count = $data->person_breakup_district($post, 'true');
                $profiles = $data->person_breakup_district($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    $user_obj = Auth::instance()->get_user();
                    $loginuser = $user_obj->id;
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $region_id = ( isset($item['region_id']) ) ? $item['region_id'] : 0;
                        $region_name = Helpers_Utilities::get_region($region_id);
                        $posted = ( isset($item['posted']) ) ? $item['posted'] : 0;
                        $posted_encrypted = !empty($item['posted']) ? Helpers_Utilities::encrypted_key($item['posted'], "encrypt") : '';
                        $office = Helpers_Profile::get_user_posting($posted);
                        $total_request = ( isset($item['total_person']) ) ? $item['total_person'] : 0;
                        if (isset($date1) && isset($date2)) {
                            $member_name_link = '<a class="btn btn-small action" href="' . URL::site('Personsreports/person_list_district/?id=' . $posted_encrypted) . '&startdate=' . $date1 . '&enddate=' . $date2 . '"><i class="fa fa-folder-open-o"></i> View Detail</a>';
                        } else {
                            $member_name_link = '<a class="btn btn-small action" href="' . URL::site('Personsreports/person_list_district/?id=' . $posted_encrypted) . '"><i class="fa fa-folder-open-o"></i> View Detail</a>';
                        }
                        $row = array(
                            $region_name,
                            $office,
                            $total_request,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            
        }
    }

    public function action_person_list_district() {

        try {
            if (Helpers_Utilities::chek_role_access($this->role_id, 60) == 1) {
                $DB = Database::instance();
                $login_user = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                $post = $this->request->post();
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                if ((!empty($post['id'])) || (!empty($post['r_category_1']))) {                    
                    /* Posted Data */
                    $post = Helpers_Utilities::remove_injection($post);
                    //export excel
                   // include 'excel/audit_report.inc';                    
                    include 'excel/persons/person_list_district.inc';                    
                    /* Set Session for post data carrying for the  ajax call */
                    Session::instance()->set('person_list_district_post', $post);
                    $this->template->content = View::factory('templates/user/adminreports/person_list_district')
                            ->set('search_post', $post);
                } else {
                    $this->template->content = View::factory('templates/user/access_denied');
                }
            } else {
                $this->template->content = View::factory('templates/user/access_denied');
            }
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
            $this->template->content = View::factory('templates/user/exception_error_page')
                    ->bind('exception', $ex);
        }
    }

    //ajax call for data
    public function action_ajaxpersonlistdistrict() {
        try {
            //echo 'Hello'; exit;
            $this->auto_rednder = false;
            /*  Output */
            $output = array(
                "sEcho" => (isset($_GET['sEcho'])) ? intval($_GET['sEcho']) : '1',
                "iTotalRecords" => "0",
                "iTotalDisplayRecords" => "0",
                "aaData" => array()
            );

            if (Auth::instance()->logged_in()) {
                $post = Session::instance()->get('person_list_district_post', array());
                if (isset($_GET)) {
                    $post = array_merge($post, $_GET);
                }
                $post = Helpers_Utilities::remove_injection($post);
                $data = new Model_Personsreports;
                $rows_count = $data->person_list_district($post, 'true');

                $profiles = $data->person_list_district($post, 'false');

                if (isset($profiles) && sizeof($profiles) <= 0) {
                    $output['iTotalRecords'] = 0;
                    $output['iTotalDisplayRecords'] = 0;
                } else {
                    $output['iTotalRecords'] = $rows_count;
                    $output['iTotalDisplayRecords'] = $rows_count;
                }
                if (isset($profiles) && sizeof($profiles) > 0) {
                    foreach ($profiles as $item) {
                        /* Concate name full name */
                        $person_id = ( isset($item['person_id']) ) ? $item['person_id'] : 0;
                        $created_at = ( isset($item['pcreated']) ) ? $item['pcreated'] : 0;
                        $person_name = Helpers_Person::get_person_name($person_id);
                        $f_name = Helpers_Person::get_person_father_name($person_id);
                        $cnic = Helpers_Person::get_person_cnic($person_id);
                        $category_id = Helpers_Person::get_person_category_id($person_id);
                        $category = (isset($category_id)) ? Helpers_Utilities::get_category_name($category_id) : 'NA';
                        //$search_count = Helpers_Person::get_person_view_count($person_id);
                        $user = ( isset($item['first_name']) ) ? $item['first_name'] . ' ' . $item['last_name'] : 'Un-Known';
                        $member_name_link = Helpers_Person::get_person_link($person_id);

                        $login_user = Auth::instance()->get_user();
                        $access = Helpers_Person::sensitive_person_acl($login_user->id, $person_id);

                        if ($access == TRUE) {
                            $member_name_link = '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($item['person_id'], "encrypt")) . '" > View Detail </a>';
                        } else {
                            $member_name_link = 'NO Access';
                        }

                        $row = array(
                            $person_name,
                            $f_name,
                            $cnic,
                            $category,
                            $created_at,
                            $user,
                            $member_name_link
                        );

                        $output['aaData'][] = $row;
                    }
                }
            }

            echo json_encode($output);
            exit();
        } catch (Exception $ex) {
            echo '<pre>';
            print_r($ex);
            exit;
        }
    }

}

// End Users Class
