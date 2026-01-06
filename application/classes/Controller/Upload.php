<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Upload extends Controller_Working {

    public function __Construct(Request $request, Response $response) {
        parent::__construct($request, $response);
        $this->request = $request;
        $this->response = $response;
    }


    public function action_transfer() {
        try{
         $DB = Database::instance();
           $sql = "select * from person";
           $results = $DB->query(Database::SELECT, $sql, TRUE);
           foreach($results as $r)
           {
               $sql = "select person_id from person_nadra_profile where person_id=". $r->person_id;
                $results = $DB->query(Database::SELECT, $sql, TRUE)->current();
                if(empty($results))
                {
                     $query = DB::insert('person_nadra_profile', array('person_id','cnic_number','user_id'))                
                        ->values(array($r->person_id,$r->cnic_number,$r->user_id))                                
                        ->execute();
                }    
               
           }    
           }catch (Exception $e) { }
        
    }
    public function action_parse_start() {
        try{
        $_POST = Helpers_Utilities::remove_injection($_POST);
         Helpers_Upload::data_mapping($_POST['path'], $_POST['company_name'], $_POST['phone_number'], $_POST['userrequestid']);
        echo 1;
        }catch (Exception $e) { }
    }
    /* Upload CDR Against CDR */
    public function action_docupload() {
        if (Auth::instance()->logged_in()) {
            try{
            //get logged user id
            $user_id = Auth::instance()->get_user()->id;           
            $message = 0;
            // check request method
            $_POST = Helpers_Utilities::remove_injection($_POST);
            
            if ($this->request->method() === Request::POST) {
               $path = Helpers_Upload::upload_file($_FILES, $this->request->post(), $user_id, 'manual', 1);
               $message = 1;
            }
            }catch (Exception $e) { }
            if(!empty($path))
            {
                try{
                if(!empty($_POST['userrequestid'])){
                $reference_number = Model_Email::email_status($_POST['userrequestid'], 2, 7);
                }
                }catch (Exception $e) { }
                $this->redirect('user/uploaded_cdrs?message='.$message);
                /*
                $upload = 1;
                $this->template->content = View::factory('templates/user/upload_against_msisdn')
                ->bind('path',$path)
                ->bind('upload',$upload)
                ->bind('company_name',$_POST['company_name'])
                ->bind('userrequestid',$_POST['userrequestid'])
                ->bind('phone_number',$_POST['phone_number']);
                */
            }else{            
                $this->redirect('user/upload_against_msisdn?message='.$message);
            }
           
        }
    }
    /* Upload CDR Against IMEI */
    public function action_imeidocupload() {
        if (Auth::instance()->logged_in()) {            
            //get logged user id
            $user_id = Auth::instance()->get_user()->id;           
            $message = 0;
            try{
            $post = $this->request->post();
            $post = Helpers_Utilities::remove_injection($post);
            $_POST = Helpers_Utilities::remove_injection($_POST);
            // check request method            
            $_POST['request_type'] = 2;
            if ($this->request->method() === Request::POST) {
               $file = Helpers_Upload::upload_file($_FILES, $post, $user_id, 'manual', 1, 1, 'cdr against imei no');               
               $message = 1;
            }
            }catch (Exception $e) { }
            //$file_id = $file->get("id");
            //$path = 'uploads/cdr' . '/manual/' . $file->get("file");
            
            if(!empty($file))
            {
                if(!empty($_POST['requestid'])){
                    try{
                $reference_number = Model_Email::email_status($_POST['requestid'], 2, 7);
                }catch (Exception $e) { }
                }
                if(!empty($_POST['ismanualfrm']))
                    echo $message;
                else
                   $this->redirect('user/uploaded_cdrs?message='.$message);
                        //   $result = Helpers_Upload::data_mapping_partially($path, $_POST['company_name'], $_POST['imei_no'], $file_id,$_POST['requestid']);
                //           echo $result;
            }else{            
               if(!empty($_POST['ismanualfrm']))
                    echo $message;
                else                
                    $this->redirect('user/upload_against_imei?message='.$message);
            }

        }
    }
    
    /* Full parsing function */
     /* Upload CDR Against IMEI */
    public function action_imeidocuploadfull() {
        try{
        if (Auth::instance()->logged_in()) {
            try{
            //get logged user id
            $user_id = Auth::instance()->get_user()->id;           
            $message = 0;
            // check request method    
            $_POST = Helpers_Utilities::remove_injection($_POST);
            $file_data = Model_Generic::get_file_data($_POST['imei']);
            }catch (Exception $e) { }
            if(!empty($file_data))
            {
                try{
                if(!empty($file_data["file"]) && $file_data["is_manual"]==1){
                 $path = 'uploads/cdr/manual/' . $file_data["file"];
                }elseif(!empty($file_data["file"]) && $file_data["is_manual"]==2){
                 $path = 'uploads/cdr/mail/' . $file_data["file"];
                }
                 $result = Helpers_Upload::data_mapping_full($path, $file_data["company_name"], $file_data["id"], $file_data["imei"], $file_data['request_id']);                
                }catch (Exception $e) { }
            }else{
                echo '6'; 
                exit;
            }
        }
        }  catch (Exception $ex){
            echo json_encode(2);
        }
    }

    /* Check MSISDN */
    public function action_checkmsisdn() {       
        try{
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
         $this->auto_render = FALSE;
        if (Auth::instance()->logged_in()) {
                    $user = Auth::instance()->get_user();                    
                    $updated = Model_Parse::check_msisdn($post['msisdn_no']);   
                    //print_r($updated);
                  //  print_r($updated); exit;
                    if($updated!=-1){
                    $person_id=Helpers_Utilities::encrypted_key($updated,"encrypt");
                    }else{
                        $person_id=$updated;
                    }
                    echo $person_id;
                   
                    //echo json_encode($result);                                          
        
        } else {
                $this->redirect();            
        }
        }  catch (Exception $ex){
            echo json_encode(2);
        }
    }
    /* Check MSISDN */
    public function action_checkmsisdndetail() {
        try{
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
         $this->auto_render = FALSE;
        if (Auth::instance()->logged_in()) {
                    $user = Auth::instance()->get_user();  
                   // print_r($post['msisdn_number']); exit;
                    $updated = Model_Parse::check_msisdn_detail($post['msisdn_number']);
                    // print_r($updated);                    
                    echo $updated;                   
                    //echo json_encode($result);                                                  
        } else {
                $this->redirect();            
        }
        }  catch (Exception $ex){
            echo json_encode(2);
        }
    }
    /* Check MSISDN */
    public function action_checkmobilenumberexist() {   
        try{
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
         $this->auto_render = FALSE;
        if (Auth::instance()->logged_in()) { 
                    $updated =!empty($post['msisdn_number']) ?   Helpers_Utilities::get_subscribers_info($post['msisdn_number']) :0 ;
                    $exist=!empty($updated->cnic_number) ? $updated->cnic_number : 0;
                    if(!empty($exist)){
                        $exist=1;
                    }else{
                        $exist=0;
                    }
                    
                    echo $exist;
                   
                    //echo json_encode($result);                                          
        
        } else {
            echo 0;
                $this->redirect();            
        }
        }  catch (Exception $ex){
            echo json_encode(2);
        }
    }
    /* CNIC Details */
    public function action_checkcnicdetails() {       
        try{
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
         $this->auto_render = FALSE;
        if (Auth::instance()->logged_in()) {
                    $user = Auth::instance()->get_user();  
                   // print_r($post['msisdn_number']); exit;
                    $updated = Model_Parse::check_cnic_detail($post['cnic_no']);
                    // print_r($updated);
                    
                    echo $updated;
                   
                    //echo json_encode($result);                                          
        
        } else {
                $this->redirect();            
        }
        }  catch (Exception $ex){
            echo json_encode(2);
        }
    }
    /* Check MSISDN */
    public function action_checkcnic() {  
        try{
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
         $this->auto_render = FALSE;
         }catch (Exception $e) { }
        if (Auth::instance()->logged_in()) {
            try{
                    $user = Auth::instance()->get_user();                    
                    $updated = Model_Parse::check_cnic($post['cnic_no']);                    
                    $result = (!empty($updated['person_id'])? $updated['person_id'] : '-1');
                    //print_r($updated);
                    echo $result;
                    //echo json_encode($result);                                          
        }catch (Exception $e) { }
        } else {
                $this->redirect();            
        }
    }
    /* Get subscriber detail of existing number */
    public function action_getsubinfo() { 
        try{
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
         $this->auto_render = FALSE;
        if (Auth::instance()->logged_in()) {
                    $user = Auth::instance()->get_user();                                   
                    $result = Helpers_Utilities::get_subscribers_info($post['number']);
                    //print_r($updated);
                  // print_r($result); exit;
                 echo  json_encode($result);                                          
        
        } else {
                $this->redirect();            
        }
        }  catch (Exception $ex){
            echo json_encode(2);
        }
    }
    /* Get sim last used imei number */
    public function action_getsimlastdeviceimei() {  
        try{
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
         $this->auto_render = FALSE;
        if (Auth::instance()->logged_in()) {
                    $user = Auth::instance()->get_user();                                   
                    $result = Helpers_Utilities::get_sim_last_used_imei($post['number']);
                    //print_r($updated);
                  // print_r($result); exit;
                 echo  json_encode($result);                                          
        
        } else {
                $this->redirect();            
        }
        }  catch (Exception $ex){
            echo json_encode(2);
        }
    }
    
    /* Check Company */
    /* Check Company */
    public function action_checkcompany() {
      try{
     if (Auth::instance()->logged_in()) {
        $compnay_name = -1;
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
        $this->auto_render = FALSE;
       // $post['number']= 3134276104;
        //
        $compnay_name=  Helpers_Utilities::check_mnc($post);
        if(!empty($compnay_name)){
                echo $compnay_name;
        }
        } else {
                $this->redirect();           
        }
      }  catch (Exception $ex){
          echo json_encode(2);
      }
    }
    /* Check Company */
    public function action_get_msisdn_company() {
      try{
     if (Auth::instance()->logged_in()) {
         $compnay_name = -1;
        $post = $this->request->post();
        $post = Helpers_Utilities::remove_injection($post);
        $this->auto_render = FALSE;
         $compnay_name=  Helpers_Utilities::get_company_mnc_by_mobile($post['number']);
       
         if(!empty($compnay_name && $compnay_name !==-1)){
         echo $compnay_name;
         }else{
           $compnay_name= $this->action_checkcompany(); 
         }
          
     } else {
                $this->redirect();           
        }
      }  catch (Exception $ex){
          echo json_encode(2);
      }
    }
    /*public function action_checkcompany() {   
        $compnay_name = -1;
        $post = $this->request->post();
         $this->auto_render = FALSE;
        if (Auth::instance()->logged_in()) {
            $arrContextOptions=array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );  

            $url = file_get_contents("https://www.mybecharge.com/topup/ajax?cmd=get_operators&phoneNumber=+92".$post['number']."&pinNumber=&operatorid=", false, stream_context_create($arrContextOptions));
                //$url = "https://www.mybecharge.com/topup/ajax?cmd=get_operators&phoneNumber=+92".$post['number']."&pinNumber=&operatorid=";
                //echo $url;
                $content = $url; //file_get_contents($url);
                $json = json_decode($content, true);
                
                echo '<pre>';
                print_r($json);
                exit;
                foreach($json['operator'] as $item) {                    
                    
                    if(isset($item['selected']))
                    { 
                        switch ($item['name'])
                        {
                            case 'Zong Pakistan':
                                $compnay_name = 4;
                                break;
                            case 'Jazz Pakistan':
                                $compnay_name = 1;
                                break;
                            case 'Telenor Pakistan':
                                $compnay_name = 6;
                                break;
                            case 'Ufone Pakistan':
                                $compnay_name = 3;
                                break;
                            case 'Warid Pakistan':
                                $compnay_name = 7;
                                break;

                        }
                    }  
                }
                Helpers_Utilities::increament_check_company_count($compnay_name);
                echo $compnay_name;
        } else {
                $this->redirect();            
        }
    }
    */
}

// End Persons Class

