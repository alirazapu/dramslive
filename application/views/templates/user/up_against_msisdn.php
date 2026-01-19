<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$firstName = '';
$lastName  = '';
$cnic      = '';
$address   = '';
if (
    isset($_POST['receivedbodynew']) &&
    !empty($_POST['receivedbodynew'])
) {
    $data = json_decode($_POST['receivedbodynew'], true);

    if (is_array($data)) {

        $fullName = $data['name'] ?? '';
        $cnic     = $data['cnic'] ?? '';
        $address  = $data['address'] ?? '';

        if (!empty($fullName)) {
            $nameParts = explode(' ', trim($fullName), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName  = $nameParts[1] ?? '';
        }
    }
}


    $post_request_id=!empty($post_data['requestid']) ? $post_data['requestid'] : '';
    $post_company_mnc=!empty($post_data['mnc']) ? $post_data['mnc'] : '';
    $post_request_type_id=!empty($post_data['requesttype']) ? $post_data['requesttype'] : '';
    $post_request_value=!empty($post_data['requestvalue']) ? $post_data['requestvalue'] : '';
    $post_recieved_file_path=!empty($post_data['receivedfilepath']) ? $post_data['receivedfilepath'] : '';
    $post_recieved_body=!empty($post_data['receivedbody']) ? $post_data['receivedbody'] : '';
    $post_recieved_body_raw=!empty($post_data['receivedbodyraw']) ? $post_data['receivedbodyraw'] : '';
    $post_country=!empty($post_data['is_foreigner']) ? $post_data['is_foreigner'] : '';
    
    $user_obj = Auth::instance()->get_user();
            
//print_r($post_data); exit;
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-upload"></i>Data Upload (MSISDN)
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Data Upload Against Mobile#</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div style="display:none;" id="custom-form"></div>
    <div class="row">
                   <?php
                    if (!empty($_GET['accessmessage'])) {
                        ?>
                        <div class="alert alert-success alert-dismissible" style="height: 60px; margin-top: 40px">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> <?php echo $_GET['accessmessage']; ?></h4>
                        </div>
                    <?php } ?>
        <div class="form-group col-md-12 " >
            <div class="alert-dismissible notificationclosereports" id="notification_msgreports" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divreports"></div></h4>
            </div>
        </div>
        <div class="col-md-12">
            <div class="nav-tabs-custom">  
                <div style="display:none;" id="custom-form"></div>
                <div class="alert " id="upload_full" style="color: '#ff5b3c'; display: none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> 
                            <span id='parsresult'> Be Patient parsing in process... 
                                <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                            </span></h4>
                    </div>
              <?php
                if (!empty($upload) && $upload == 1) {
                    ?>
                    <div class="alert " id="upload" style="color: '#ff5b3c'">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> 
                            <span id='parsresult_f'> Congratulation! File is Uploaded! Be Patient parsing in process 
                                <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                            </span></h4>
                    </div>
                    <input type="hidden" name="path" id="path" value="<?php echo $path; ?>">    
                    <input type="hidden" name="company_name" id="company_name" value="<?php echo $company_name; ?>">    
                    <input type="hidden" name="phone_number" id="phone_number_cdr" value="<?php echo $phone_number; ?>">    
                    <input type="hidden" name="userrequestid" id="userrequestid" value="<?php echo $userrequestid; ?>">    
                    <script type="text/javascript">
                        $(document).ready(function () {
                            parsing_start();
                        });
                        function parsing_start() {
                            var path = $('#path').val();
                            var company_name = $('#company_name').val();
                            var phone_number = $('#phone_number_cdr').val();
                            var userrequestid = $('#userrequestid').val();
                            var array_val = {path: path, company_name: company_name, phone_number:phone_number,userrequestid:userrequestid}
                            $.ajax({
                                url: "<?php echo URL::site("upload/parse_start"); ?>",
                                type: 'POST',
                                data: array_val,
                                cache: false,
                                //dataType: "text",
                                //dataType: 'html',
                                success: function (data)
                                { 
                                    if(data==3)
                                    {    
                                        $("#parsresult_f").html('A Party not mach with field input !');
                                    }else if(data==5){
                                        $("#parsresult_f").html('A Party duplication !');
                                    }else if(data==1){
                                        $("#parsresult_f").html('Parsing Completed !');
                                    }else if(data==404){
                                        $("#parsresult_f").html('Company Not Match !');
                                    }else{
                                        $("#parsresult_f").html('Parsing Error !');
                                    }
                                    
                                }
                            });
                        }
                    </script>                            
                <?php }
                ?>  
                <?php
                if (isset($_GET["message"]) && $_GET["message"] == 1) {
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> Congratulation! Information Successfully Added.</h4>
                    </div>
                <?php } ?>

                <div class="">
                    <!-- left column -->
                    <div class="">
                        <!-- general form elements -->
                        <div class="">
                            <div class="box box-primary">
<!--                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="nav-tabs-custom">
                                            <ul  class="nav nav-tabs">
                                                <li class=" "><a <?php // if(!empty($post_data)){ echo 'class="disabled"'; } ?> href="#" onclick="showDiv(1);" data-toggle="tab">Against Mobile#</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>-->
<!--                                <div class="box-header with-border">
                                    <h3 class="box-title">Upload Data</h3>
                                </div>-->
                                <!-- /.box-header -->
                                <div class="box-body"> 
                                    <div class="form-group col-md-12 " >
                                        <div class="alert-dismissible notificationcloseidentity" id="notification_msgidentity" style="display: none;">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <h4><i class="icon fa fa-check"></i> <div id="notification_msg_dividentity"></div></h4>
                                        </div>
                                    </div>    
                                    <!-- Check  MSISDN -->
                                    <div id="msisdn_div" style="display: block">
                                        <!-- form start -->
                                        <form name="msisdn_form" id="msisdn_form" class="msisdn_form upload_msisdn_form" action="" method="post" enctype="multipart/form-data">

                                            <div class="form-group col-md-6">
                                                    <div class="row">
                                                        <div class="col-sm-10">
                                                            <label style="margin-left: -15px">Enter MSISDN #</label>
                                                             <input <?php if(!empty($post_request_value)){ echo "readonly"; } ?>  style="margin-left: -15px; " type="text" name="msisdn_no" id="msisdn_no" class="form-control">
                                                            <input type="hidden" name="msisdnerrorhandle" id="msisdnerrorhandle"  >
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div id="continues_btn">
                                                                <div class="form-group col-sm-12">
                                                                    <button type="submit" onclick="msisdn();"  style="margin-top: 25px; margin-left: -20px"  class="msisdn_submit_btn btn btn-primary pull-left"  >Continue</button>                                                                    
                                                                </div>
                                                            </div>
                                                            <div id="continues_btn1" style="display: none">
                                                                <div class="form-group col-sm-12">
                                                                    <button <?php if(!empty($post_data)){ echo 'disabled'; } ?> type="button" onclick="edit_continues_btn();" style="margin-top: 25px; margin-left: -20px;"  class="btn btn-primary pull-left" >Back</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 

                                            <!-- /.box-body -->
                                        </form>
                                    </div>
                                </div>                                      
                            </div>
                        </div>
                    </div>
                </div>
                <!--/.col (left) -->


                <!-- Check  Data entry choice -->
                <div id="msisdn_div_options" style="display: none">
                    <!-- left column -->
                    <div class="">
                        <!-- general form elements -->
                        <div class="">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Manual Data Upload Against Phone#</h3>
                                </div>                                
                                <div class="box-body" style="padding: 20px;"> 
                                    <!-- /.box-header -->
                                    <div id="msisdn_div_options_sub_link" style="display: none">                                        
                                        <h4>Possible Options</h4>
                                        <p> <i class="fa fa-upload"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> onclick="openmanualsubentry(0);" class="btn manualsubentry_link">Manual Subscriber Entry</a></p>
                                        <p> <i class="fa  fa-send"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> id="sub_link_val" onclick="requestsubviaemail();"  class="btn">Subscriber Request via Email</a></p>
                                    </div>
                                    <div id="msisdn_div_options_cdr_link"  style="display: none">
                                        <div id="subscriberinfo">
                                            
                                        </div>
                                        <div class="col-md-6">
                                        <h4>MSISDN Detail</h4>
                                        <p> <i class="fa fa-upload"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> onclick="openmanualsubentry(1);" class="btn manualsubentryexist_link">Update Existing Subscriber</a></p>
                                        <p> <i class="fa  fa-send"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> onclick="openmanuallocationentry();"  class="btn">Update Current Location</a></p>                                        
                                        </div>
                                        <div class="col-md-6">
                                        <h4>CDR Detail</h4>                                        
                                        <p> <i class="fa fa-upload"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?>  onclick="openmanualupload();" class="btn">Upload CDR File</a></p>
                                        <p> <i class="fa  fa-send"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> id="cdr_link_val"  class="btn">Request New CDR</a></p>                                        
                                        
                                        </div>
                                        <div class="col-md-12" id="cdr_against_mobile_upload_status" style=' background-image: url("<?php echo URL::base(); ?>dist/img/loader.gif"); width:100%; height:11px; background-position:center; display:none'></div> 
                                        </div>    
<!--                                    <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> id="msisdn_back" onclick="msisdn_back();" class="btn btn-default"><i class="fa fa-rotate-left text-blue"></i> Back</a>-->
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/.col (left) -->
                </div>

                <!-- Manual CDR Entry -->
                <div id="manualupload" style="display:none">
                    <!-- left column -->
                    <div class="">
                        <!-- general form elements -->
                        <div class="">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Manual CDR Uploads</h3>
                                </div>
                                <!-- /.box-header -->
                                <!-- form start -->
                                <form name="manual_uuploads" id="manual_uuploads" action="<?php echo Route::url('default', array('controller' => 'upload', 'action' => 'docupload')) ?>" method="post" enctype="multipart/form-data">
                                    <div class="box-body"> 
                                        <div class="col-md-6">
                                            <div class="form-group col-md-12">
                                                <label ><u>CDR Upload Instructions!</u></label>
                                                <p><u>1.</u> Please make sure uploaded cdr has same format as in accepted list of cdr upload format document. </p>
                                                <p><u>2.</u> Uploaded cdr without matching format will generate parsing error. Error type will be shown on cdr upload status tab. <a target="NEW" href="<?php echo URL::site('User/uploaded_cdrs'); ?>" class="pull-right">CDR Status (Click)</a> </p>
                                                <p><u>3.</u> In case of error in cdr upload status please particular delete file and again upload after fixing error from file. </p>
                                                <p><u>4.</u> Acceptable cdr format. <a target="NEW" href="<?php echo URL::site('User/cdr_upload_format'); ?>" class="pull-right"> Format (Click) </a></p>
                                            </div>
                                            <hr class="style14 col-md-12"> 
                                        </div>
                                         <input type="hidden" id="userrequestid" name="userrequestid" value="<?php echo $post_request_id; ?>" /> 
                                        <?php if(!empty($post_data)){ ?>
                                        <div class="col-md-6">
                                            <div class="form-group col-md-12">
                                                <label for="subject">Received File Path</label>
                                                <input disabled type="text" class="form-control" id="subject"  name="subject" value="<?php echo $post_recieved_file_path; ?>" placeholder="File Path if any">
                                            </div>                             
                                            <div class="form-group col-md-12">
                                                <label for="body">Received Body Encoded</label>
                                                <textarea readonly style="height: 100%" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body)){ echo strip_tags($post_recieved_body); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  
                                                <label for="body">Received Body Decoded</label>
                                                <textarea readonly style="height: 100%" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body_raw)){ echo strip_tags($post_recieved_body_raw); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-6">
                                        <div class="form-group col-sm-12">            
                                            <input type="hidden" id="request_type" name="request_type" value="1">                                                
                                            <input type="hidden" id="imei" name="imei" value="">        
                                            <label for="phone_number" >Mobile Number</label>
                                            <input class="form-control" readonly="" type="text" id="phone_number" name="phone_number" value="">                       
                                        </div>                                    
                                        <div class="form-group col-sm-12">
                                            <label title="Make sure suggested network is correct for given mobile number" >Choose Network</label>
                                            <select title="Make sure suggested network is correct for given mobile number" class="form-control" name="company_name" id="company_name">
                                                <option value="">Please Select Network</option>
                                                <?php
                                                $comp_name_list = Helpers_Utilities::get_companies_data();
                                                foreach ($comp_name_list as $list) {
                                                    ?>
                                                    <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-12">
                                            <label for="exampleInputFile">File input</label>                                                
                                            <input type="file" name="file" id="file_control" class="">
                                            <p class="help-block">Upload file from here</p>
                                        </div> 
                                        <div class="form-group col-sm-12">
                                            <button type="submit" class="btn btn-primary pull-right">Submit</button>
                                        </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->

                                </form>
                            </div>

                        </div>
                    </div>
                    <!--/.col (left) -->
                </div>                
                <!-- /.box-body -->
            </div>

        </div>
    </div>
    <!--/.col (left) -->

    <!-- Manual Subscriber Entry -->
    <div id="manualsubentry" style="display: none;">

        <div class="box box-primary">
            <?php
            if (isset($_GET["message"]) && $_GET["message"] == 1) {
                ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Congratulation! Data save successfully</h4>
                </div>
            <?php } 
            if(!empty($post_data)){
            ?>
            <div class="box-header with-border">
                    <h3 class="box-title">Recieved Email Response</h3>
                </div>
                <div class="box-body"> 
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <label for="subject">Received File Path</label>
                            <input disabled type="text" class="form-control" id="subject"  name="subject" value="<?php echo $post_recieved_file_path; ?>" placeholder="File Path if any">
                        </div>                             
                        <div class="form-group col-md-12">
                            <label for="body">Received Body Encoded</label>
                            <textarea readonly style="height: 100%" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body)){ echo strip_tags($post_recieved_body); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  
                            <label for="body">Received Body Decoded</label>
                            <textarea readonly style="height: 100%" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body_raw)){ echo strip_tags($post_recieved_body_raw); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  

                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="box-header with-border">
                <h3 class="box-title">Manual Subscriber Entry</h3>
            </div>
            <form role="form" action="<?php echo url::site() . 'user/data_upload_post' ?>" id="manualsubform" method="post">
                <div class="box-body"> 
                    <div class="col-sm-6" style="display: block;margin-top: 10px;">
                        <div class="form-group">
                                                <label for="msisdn_is_foreigner" class="control-label">Country</label>
                                                <select <?php if ($post_country==0 OR $post_country==1) { echo "readonly";    }  ?> class="form-control" name="is_foreigner" id="msisdn_is_foreigner">
                                                    <option  <?php if ($post_country == 0) { echo "selected";  } ?> value="0">Pakistan</option>
                                                    <option  <?php if ($post_country == 1) {  echo "selected";   }  ?> value="1">Foreign</option>
                                                </select>
                                            </div>
                    </div>
<!--                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="ForeignerRadio1" class="control-label">Country</label>
                            <div class="radio">                                            
                                <label>
                                    <input type="radio" name="is_foreigner" id="ForeignerRadio1" value="0" checked="" >
                                    Pakistani
                                </label>
                                <label>
                                    <input type="radio" name="is_foreigner" id="ForeignerRadio1" value="1" >
                                    Other
                                </label>
                            </div>
                        </div>
                    </div> -->
                    <div class="col-sm-6" id="project_div" style="display: block;margin-top: 10px;">
                        <div class="form-group">                                                            
                                    <label for="inputproject" class="control-label">Link With Project</label> 
                                    <?php $projects_data = Helpers_Utilities::get_projects_list(); ?>
                                    <select class="form-control select2" name="inputproject[]" id="inputproject" dataplaceholder="please select project" style="width: 100%!important;">
                                            <option value="">Please select project name</option>
                                        <?php foreach ($projects_data as $project) { 
                                            $region_district = Helpers_Requests::get_project_region_district($project->region_id, $project->district_id); ?>
                                                <option  value="<?php echo $project->id; ?>"><?php echo $project->project_name . $region_district; ?></option>
                                        <?php } ?>                                                                                                                
                                    </select>                                                                                      
                                </div>
                            </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputSubNO" class="control-label">Subscriber Number</label>
                            <input name="mobile_number" type="text" class="form-control" id="inputSubNO" placeholder="Subscriber Number" readonly=""/> 
                            <input name="requestid" type="hidden" class="form-control" id="requestid" value="<?php echo $post_request_id; ?>"  readonly=""/> 
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label title="Date when sim is activated in given company" for="inputActivationDate" class="control-label">SIM Activation Date</label>
                            <input readonly="" title="Date when sim is activated in given company" name="act_date" type="text" class="form-control" id="act_date" placeholder="mm/dd/yyyy"> 
                        </div>
                    </div>                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="imsi" class=" control-label">IMSI</label>
                            <input type="text" name="imsi" class="form-control" id="imsi" placeholder="19 digists number only">
                        </div>
                    </div>                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="imei" class=" control-label">IMEI</label>
                            <input type="text" name="imei" class="form-control" id="inputimei" placeholder="15 digists number only">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputName" class="control-label">First Name<?php echo $firstName; ?></label>

                            <input name="person_name" type="text" class="form-control" id="inputName" placeholder="First Name" value="<?= htmlspecialchars($firstName) ?>">
                        
                        </div>
                    </div>                                      
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputName1" class="control-label">Last Name</label>
                            <input name="person_name1" type="text" class="form-control" id="inputName1" placeholder="Last Name" value="<?= htmlspecialchars($lastName) ?>">
                        </div>
                    </div>                                      
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label title="In case of foreigner select country as foreigner" for="inputCNIC" class="control-label">CNIC</label>
                            <input title="In case of foreigner select country as foreigner" name="cnic_number" type="text" class="form-control" id="inputCNIC" placeholder="13 digists number only without dashes" value="<?= htmlspecialchars($cnic) ?>">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputAddress" class=" control-label">Address</label>
                            <input type="text" name="address" class="form-control" id="inputAddress" placeholder="Address" value="<?= htmlspecialchars($address) ?>">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label  for="ConnectionTypeRadio1" class="control-label">Connection Type</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="ConnectionTypeRadios" id="ConnectionTypeRadio1" value="1" checked="">
                                    Pre-Paid
                                </label>                                            
                                <label>
                                    <input type="radio" name="ConnectionTypeRadios" id="ConnectionTypeRadio2" value="0">
                                    Post-Paid
                                </label>
                            </div>

                        </div>
                    </div>                                    
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="StatusRadio1" class="control-label">Status</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="StatusRadios" id="StatusRadio1" value="1" checked="">
                                    Active
                                </label>                                            
                                <label>
                                    <input type="radio" name="StatusRadios" id="StatusRadio2" value="0">
                                    In-Active
                                </label>
                            </div>
                        </div>
                    </div>                                     
                    <div class="col-sm-3">
                        <!--                                    <div id="imei_iframe_data" style="display: none">                                            
                                                                <form id="login" method="post" action="http://www.imei.info//login">
                                                                    <input name="next" value="/" type="hidden">
                                                                    <span style="display: none" class="border fleft"><input name="login" id="logini" value="Mohammad Yaser" placeholder="Login" type="text"></span>
                                                                    <span style="display: none" class="border fleft"><input name="password" value="ASADSULTAN" placeholder="Password" type="password"></span>
                                                                    <span class="border fright"><button id="blogin" class="blue" type="submit">Click Here</button></span>
                                                                </form>   
                                                            </div>-->
                        <div class="form-group">
                            <label title="Search details of phone through given imei number, if imei is not given then add default value 999999999999999" for="phone_name" class="control-label">Check Phone Name <a class="btn" target="_blank" href="http://www.imei.info/" > 
                                    Click Here                                                     
                                </a>
<!--                                            <span>
                                    <iframe id="foo" style="overflow:hidden; margin: 0; padding: 0; border: none;" scrolling="no" width="75px"  height="50px">                                                        
                                        test
                                    </iframe>
                                    </span>-->
                            </label>
                            <input title="Search details of phone through given imei number, if imei is not given then add default value 999999999999999" name="phone_name" type="text" class="form-control" id="phone_name"> 
                        </div>
                    </div>
                    <div class="col-sm-3" >
<!--                        <div class="form-group">
                            <label for="company_name_get" class="control-label">Company Name </label>                                            
                            <select class="form-control" name="company_name_get" id="company_name_get" style="margin-top: 13px">
                                <option value="">Please Select Company</option>
                                <?php
                              //  $comp_name_list = Helpers_Utilities::get_companies_data();
                              //  foreach ($comp_name_list as $list) {
                                    ?>
                                    <option value="<?php //echo $list->mnc ?>"><?php // echo $list->company_name ?></option>
                                <?php // } ?>

                            </select>
                        </div>-->
                        <div class="form-group"  >
                                            <label title="Search company name of mobile number, this option when you dont know about company name" for="company_name_get" class="control-label"><span>Company Name 
                                                <img id='findcompnay_image' src="<?php echo URL::base(); ?>dist/img/102.gif" style="width: 38px;height: 33px; display: none;">
                                                <a title="Search company name of mobile number, this option when you dont know about company name" id="findcompanyname"  class="btn" onclick="findcompanyname();"> Search (Click) </a></span></label>                                    
                                                <select  class="form-control " name="company_name_get" id="company_name_get"  >
                                                    <option  value="">Please Select Company</option>
                                                <?php                                                 
                                                $comp_name_list = Helpers_Utilities::get_companies_data();
                                                foreach($comp_name_list as $list)
                                                {     
                                                 ?>
                                                <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                                <?php } ?>

                                            </select>
                                        </div>

                    </div>
                    <div class="form-group col-sm-12">                                                                                                                
                        <button type="submit" class="btn btn-primary" style="margin-top:10px">Submit</button>                                                        
                    </div>
                </div>
            </form>
        </div>
    </div> 
    <!-- Manual Current Location Entry -->
    <div id="manuallocationentry" style="display: none;">

        <div class="box box-primary">
            <?php
            if (isset($_GET["message"]) && $_GET["message"] == 1) {
                ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Congratulation! Data save successfully</h4>
                </div>
            <?php } 
            if(!empty($post_data)){
            ?>
            <div class="box-header with-border">
                    <h3 class="box-title">Recieved Email Response</h3>
                </div>
                <div class="box-body"> 
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <label for="subject">Received File Path</label>
                            <input disabled type="text" class="form-control" id="subject"  name="subject" value="<?php  echo $post_recieved_file_path; ?>" placeholder="File Path if any">
                        </div>                             
                        <div class="form-group col-md-12">                            
                            <label for="body">Received Body Encoded</label>
                            <textarea readonly style="height: 100%" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body)){ echo strip_tags($post_recieved_body); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  
                            <label for="body">Received Body Decoded</label>
                            <textarea readonly style="height: 100%" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body_raw)){ echo strip_tags($post_recieved_body_raw); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  

                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="box-header with-border">
                <h3 class="box-title">Manual Location Entry</h3>
            </div>                            
            <div class="box-body">
                <form id="manuallocationform" name="manuallocationform" role="form" action="<?php echo url::site() . 'user/location_upload_post' ?>"  method="post" >
                  <input type="hidden" id="locationperson" name="person_id" />                    
                  <input type="hidden" id="requestid" name="requestid" value="<?php echo $post_request_id; ?>" />                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="locationmsisdn" class="control-label">Subscriber Number</label>
                            <input name="locationmsisdn" type="text" class="form-control" id="locationmsisdn" placeholder="Subscriber Number" readonly=""/> 
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="loccompany" class="control-label">Company Name</label>
                            <select  class="form-control" name="loccompany" id="loccompany">
                                <option value="">Select</option>
                                <?php
                                $comp_name_list = Helpers_Utilities::get_companies_data();
                                foreach ($comp_name_list as $list) {
                                    ?>
                                    <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                <?php } ?>

                            </select>
                        </div>
                    </div>                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="locimsi" class=" control-label">IMSI</label>
                            <input type="text" name="locimsi" class="form-control" id="locimsi" placeholder="IMSI #">
                        </div>
                    </div> 
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="locimei" class=" control-label">IMEI</label>
                            <input type="text" name="locimei" class="form-control" id="locimei" placeholder="IMEI #">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="phone_name" class="control-label">Check Phone Name <a  target="_blank" href="http://www.imei.info/" >
                                    Click Here                                                     
                                </a>
                            </label>
                            <input name="locphonename" type="text" class="form-control" id="phone_name"> 
                        </div>
                    </div>                                     
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="loclat" class="control-label">Latitude</label>
                            <input name="loclat" type="text" class="form-control" id="loclat" placeholder="Latitude">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="loclong" class="control-label">Longitude</label>
                            <input name="loclong" type="text" class="form-control" id="loclong" placeholder="Longitude">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="loccellid" class="control-label">Cell ID</label>
                            <input name="loccellid" type="text" class="form-control" id="loccellid" placeholder="Cell ID">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="loclac" class="control-label">LAC ID</label>
                            <input name="loclac" type="text" class="form-control" id="loclac" placeholder="LAC ID">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="locdate" class="control-label">Location At</label>
                                    <input name="locdate" readonly type="text" class="form-control form_datetime" id="locdate" placeholder="mm/dd/yyyy hh:mm"> 
                                  
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="locnetwork">Network</label>
                        <select class="form-control" id="locnetwork" name="locnetwork">
                            <option value="">Select</option>                                            
                            <option value="0">Unknown</option>
                            <option value="1">2G</option>
                            <option value="2">3G</option>
                            <option value="3">4g/LTE</option>
                        </select>
                    </div> 
                    <div class="form-group col-md-6">
                        <label for="locstatus">Status</label>
                        <select class="form-control" id="locstatus" name="locstatus">
                            <option value="">Select</option>
                            <option value="0">de-attached</option>
                            <option value="1">attached</option>
                            <option value="2">purged</option>
                        </select>
                    </div>                                                                         
                    <div class="form-group col-sm-12">
                        <label for="locaddress" class="control-label">Location Address</label>
                        <input name="locaddress" type="text" class="form-control" id="locaddress" placeholder="Address">
                    </div>
                    <div class="form-group col-sm-12">                                                                                                                
                        <button type="submit" class="btn btn-primary pull-right" style="margin-top:10px">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>  
</section>

<!-- /.content -->
<script type="text/javascript">
    
    
$(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
    
    
    var countdiv3view = 1;
    var objDT;
    var imei = '000000';

    
    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    jQuery.fn.dataTableExt.oApi.fnReloadAjax = function (oSettings, sNewSource, fnCallback, bStandingRedraw)
    {
        // DataTables 1.10 compatibility - if 1.10 then `versionCheck` exists.
        // 1.10's API has ajax reloading built in, so we use those abilities
        // directly.
        if (jQuery.fn.dataTable.versionCheck) {
            var api = new jQuery.fn.dataTable.Api(oSettings);

            if (sNewSource) {
                api.ajax.url(sNewSource).load(fnCallback, !bStandingRedraw);
            } else {
                api.ajax.reload(fnCallback, !bStandingRedraw);
            }
            return;
        }

        if (sNewSource !== undefined && sNewSource !== null) {
            oSettings.sAjaxSource = sNewSource;
        }

        // Server-side processing should just call fnDraw
        if (oSettings.oFeatures.bServerSide) {
            this.fnDraw();
            return;
        }

        this.oApi._fnProcessingDisplay(oSettings, true);
        var that = this;
        var iStart = oSettings._iDisplayStart;
        var aData = [];

        this.oApi._fnServerParams(oSettings, aData);

        oSettings.fnServerData.call(oSettings.oInstance, oSettings.sAjaxSource, aData, function (json) {
            /* Clear the old information from the table */
            that.oApi._fnClearTable(oSettings);

            /* Got the data - add it to the table */
            var aData = (oSettings.sAjaxDataProp !== "") ?
                    that.oApi._fnGetObjectDataFn(oSettings.sAjaxDataProp)(json) : json;

            for (var i = 0; i < aData.length; i++)
            {
                that.oApi._fnAddData(oSettings, aData[i]);
            }

            oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();

            that.fnDraw();

            if (bStandingRedraw === true)
            {
                oSettings._iDisplayStart = iStart;
                that.oApi._fnCalculateEnd(oSettings);
                that.fnDraw(false);
            }

            that.oApi._fnProcessingDisplay(oSettings, false);

            /* Callback user function - for event handlers etc */
            if (typeof fnCallback == 'function' && fnCallback !== null)
            {
                fnCallback(oSettings);
            }
        }, oSettings);
    };
    $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
        if (oSettings.oFeatures.bServerSide === false) {
            var before = oSettings._iDisplayStart;
            oSettings.oApi._fnReDraw(oSettings);
            // iDisplayStart has been reset to zero - so lets change it back
            oSettings._iDisplayStart = before;
            oSettings.oApi._fnCalculateEnd(oSettings);
        }

        // draw the 'current' page
        oSettings.oApi._fnDraw(oSettings);
    };

    $(document).ready(function () {      
  

        /*
         * 
         var iframe = document.getElementById('foo'),
         iframedoc = iframe.contentDocument || iframe.contentWindow.document;
         iframedoc.body.innerHTML = '<form id="login" method="post" action="http://www.imei.info//login">' + $("#imei_iframe_data").html() + '</form>';
         $("#imei_iframe_data").html('');
         */
        $("#manualsubform").validate({
            rules: {
                "inputproject[]":{
                    required:true
                },
                mobile_number: {
                    required: true,
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                },
                contact_home: {
                    required: true,
                    number: true,
                    maxlength: 10,
                    minlength: 10
                },
                cnic_number: {
                    required: true,
                    alphanumeric: true,
                    custominput: true,
                    minlength: 13,
                    maxlength: 13
                },
                act_date: {
                   // required: true,
                    vailddate: true,
                    future:true
                },
                person_name: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 30
                },
                person_name1: {
                   // required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 30
                },
                imei: {
                    number: true,
                    minlength: 15,
                    maxlength: 15
                },
                imsi: {
                    number: true,
                    minlength: 12
                },
                address: {
                    //required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 250
                },
                phone_name: {
                    alphanumericspecial: true
                },
                company_name_get: {
                    required: true,
                    check_list: true
                }
            },
            messages: {
                "inputproject[]":{
                    required: "Please select Project"
                },
                mobile_number: {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },
                contact_home: {
                    required: "Enter Home Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    minlength: "Minimum 10 digits"
                },
                cnic_number: {
                    required: "Enter CNIC Number",
                    custominput: "Only number without dashes",
                    alphanumeric: "Only number without dashes",
                    maxlenght: "Number should be 13 digits",
                    minlength: "Minimum 13 digits"
                },
                act_date: {
                   // required: "SIM activation date required",
                    vailddate: "mm/dd/yyyy"
                },
                person_name: {
                    required: "Enter Person Name",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
                person_name1: {
                  //  required: "Enter Person Name",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
                imei: {
                    minlength: "Minimum 15 digits required",
                    maxlenght: "Maximum 15 digits required"
                },
                imsi: {
                    minlength: "Minimum 12 digits required"
                },
                address: {
                    //required: "Enter Address",
                    maxlenght: "Maximum character limit is 250",
                    minlength: "Min character limit is 1"
                },
                phone_name: {
                    alphanumericspecial: "Only Alpha Numaric"
                },
                company_name_get: {
                    required: "Please check Phone Name"
                }
            }

        });
        
        $("#manuallocationform").validate({
            rules: {
                loccompany: {
                    required: true
                },
                locphonename: {
                    alphanumericspecial: true,
                    minlength: 3,
                    maxlength: 30
                },
                locimei: {
                    number: true,
                    minlength: 15,
                    maxlength: 15
                },
                locimsi: {
                    number: true,
                    minlength: 12
                },
                loclat: {                    
                    required: true,
                    number: true,
                    minlength: 6,
                    maxlength: 10
                },
                loclong: {
                    number: true,
                    required: true,
                    minlength: 6,
                    maxlength: 10
                },
                loccellid: {
                    number: true,
                    minlength: 4,
                    maxlength: 10
                },
                loclac: {
                    number: true,
                    minlength: 3,
                    maxlength: 10
                },
                locdate: {
                    required: true,
                    dateTime: true,
                    future:true
                },
                locnetwork: {
                    required: true
                },
                locstatus: {
                    required: true
                },
                locaddress: {
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 150
                }
                
                },
                messages:{
                    loccompany: {
                      required:"required"
                  },
                locphonename: {
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 3"
                },
                locimei: {
                    minlength: "Minimum 15 digits required",
                    maxlenght: "Maximum 15 digits required"
                },
                locimsi: {
                    minlength: "Minimum 12 digits required"
                },
                loclat: {                    
                    required:"required",
                    number:"only numbers",
                    minlength: "Minimum 6 digits required",
                    maxlenght: "Maximum 10 digits required"
                },
                loclong: {
                    required:"required",
                    minlength: "Minimum 6 digits required",
                    maxlenght: "Maximum 10 digits required"
                },
                loccellid: {
                    minlength: "Minimum 4 digits required",
                    maxlenght: "Maximum 10 digits required"
                },
                loclac: {
                    minlength: "Minimum 3 digits required",
                    maxlenght: "Maximum 10 digits required"
                },
                locdate: {
                    required: "Location date/time required",
                    dateTime: "mm/dd/yyyy hh:mm"
                },
                locnetwork: {
                    required: "required"
                },
                locstatus: {
                    required: "required"
                },
                locaddress: {
                    maxlenght: "Maximum character limit is 150",
                    minlength: "Min character limit is 1"
                }
                }
        });
        
        $("#manual_uuploads").validate({
            rules: {
                request_type: {
                    required: true,
                    check_list: true
                },
                company_name: {
                    required: true,
                    check_list: true
                },
                file: {
                    required: true,
                    accept: "xls|xlsx|csv",
                    filesize: 10048576
                }
            },
            messages: {
                request_type: {
                    required: "Please select file type",
                },

                company_name: {
                    required: "Please choose Newtork"
                },
                file: "File must be XLS, XLSX less than 2MB"
            }

        });

        // Validators for custom value number or string
        jQuery.validator.addMethod("custominput", function(value, element, params) {
  //return this.optional(element) || value == params[0] + params[1];
  if(jQuery("#msisdn_is_foreigner").val()==0)
  {      
      return ($.isNumeric($(element).val()));
  }else{
      if(jQuery.type( $(element).val() ) === "string")
          return true;
      else 
          return false;
  }
}, jQuery.validator.format("Please enter the correct value"));
        

        // Validators file size
        $.validator.addMethod('filesize', function (value, element, param) {
            // param = size (in bytes) 
            // element = element to validate (<input>)
            // value = value of the element (file name)
            return this.optional(element) || (element.files[0].size <= param)
        });
        // Validators checklist
        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "" || sel == 0) {
                return false;
            } else {
                return true;
            }
        }, "<span>Select One</span>");
        // Validators less than
        jQuery.validator.addMethod("future", function(value, element) { 
    return this.optional(element) || Date.parse(value) < new Date().getTime(); 
}, "Please enter only old dates");
        // Validators valid date
        jQuery.validator.addMethod("vailddate",
                function (value, element) {
                    var isValid = false;
                    var reg = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
                    if (reg.test(value)) {
                        var splittedDate = value.split('/');
                        var mm = parseInt(splittedDate[0], 10);
                        var dd = parseInt(splittedDate[1], 10);
                        var yyyy = parseInt(splittedDate[2], 10);
                        var newDate = new Date(yyyy, mm - 1, dd);
                        if ((newDate.getFullYear() == yyyy) && (newDate.getMonth() == mm - 1)
                                && (newDate.getDate() == dd))
                            isValid = true;
                        else
                            isValid = false;
                    } else
                        isValid = false;
                    return this.optional(element) || isValid;
                },
                "Please enter a valid date (mm/dd/yyyy)");
        // Validators alphanumericspecial
        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);
        }, "Only letters, Numbers & Space/underscore Allowed.");
        // Validators alphanumericspecial
        jQuery.validator.addMethod("alphanumeric", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9]+$/);
        }, "Only letters, Numbers & Space/underscore Allowed.");
        // Validators numberthree
        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");
        // Validator date and time
        jQuery.validator.addMethod("dateTime", function (value, element) {
        var stamp = value.split(" ");
        var validDate = !/Invalid|NaN/.test(new Date(stamp[0]).toString());
        var validTime = /^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/i.test(stamp[1]);
        return this.optional(element) || (validDate && validTime);
    }, "Please enter a valid date and time (mm/dd/yyyy hh:mm).");

    });

 

   

    //Date picker
    $('#datetime').datepicker({
        autoclose: true
    });
     $("#locdate").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
    
    
    /*$('#locdate').datepicker({
        autoclose: true
    });*/

   

    /* For msisdn */
    function msisdn() { 
       // var form1 = jQuery('.msisdn_form');        
       // form1.validate({
       jQuery("#msisdn_form").submit(function (e) {            
           // alert('form submit');
            e.preventDefault();
        }).validate({
            rules: {
                msisdn_no: {
                    required: true,
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                }
            },
            messages: {
                msisdn_no: {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                }
            },

            submitHandler: function () {
                // alert($('#email_sub').val());
                $("#continues_btn").hide();
                $("#continues_btn1").show();
                $("#msisdn_no").attr("readonly",true);
                $("#msisdn_div_options").hide();
                $("#msisdn_div_options_sub_link").hide();
                $("#msisdn_div_options_cdr_link").hide();
                var msisdn_no = $("#msisdn_no").val();  
                var request = $.ajax({
                    url: "<?php echo URL::site("upload/checkmsisdn"); ?>",
                    type: "POST",
                    dataType: 'text',
                    data: {msisdn_no: msisdn_no},
                    success: function (responseTex)
                    {
                        if(responseTex== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                        if (responseTex == -1)
                        {
                            $("#msisdn_div_options_sub_link").show();
                            $("#msisdnerrorhandle").val('-1');
                            $("#msisdn_div_options").show();                            
                           // $("#sub_link_val").attr("onclick", "<?php // echo URL::site("userrequest/request/1/3"); ?>" + "/" + msisdn_no);

                        } else {                            
                            $("#msisdnerrorhandle").val('');
                            $("#msisdn_div_options_cdr_link").show();
                            $("#msisdn_div_options").show();
                           // checkerror
                            $("#phone_number").val(responseTex);
                            $("#locationperson").val(responseTex);
                            //$("#msisdn_div").hide();
                            //Helpers_Utilities::encrypted_key($person_id,"encrypt")
                            $("#cdr_link_val").attr("href", "<?php echo URL::site("persons/dashboard"); ?>" +"/?id="+responseTex);
                       
                       //ajax call to get subscriber info
                       var msisdn_number = msisdn_no;
                var request = $.ajax({
                    url: "<?php echo URL::site("upload/checkmsisdndetail"); ?>",
                    type: "POST",
                    dataType: 'text',
                    data: {msisdn_number: msisdn_number},
                    success: function (msisdndetail)
                    {
                        
                         $("#subscriberinfo").html(msisdndetail);
                            if(msisdndetail== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                        },
                    
                });
                       
                       }

                    },
                    error: function (jqXHR, textStatus) {
                        alert("Unable to reach");
                    }
                });
            }
        });
        //alert('not validated');
        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");

        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .,/]+$/);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

    }
//edit msisdn no
    function edit_continues_btn() {
        $("#continues_btn").show();
        $("#continues_btn1").hide();
        $("#msisdn_no").attr("readonly", false);
        $("#msisdn_div_options").hide();
        $("#manuallocationentry").hide();
        $("#manualsubentry").hide();
        $("#manualupload").hide();
    }
    /* For msisdn back */
    function msisdn_back() {
        $("#msisdn_div").show();
        $("#cnic_div").hide();
        $("#imei_div").hide()
        $("#msisdn_div_options").hide();
        $("#msisdn_div_options_cdr_link").hide();
        $("#msisdn_div_options_sub_link").hide();
    }

  

    /* For to open manual location entry */
    function openmanuallocationentry() {
        $("#locationmsisdn").val($("#msisdn_no").val());
        $("#msisdn_div_options").hide();
        //$("#msisdn_div").hide();
        $("#manuallocationentry").show();
        $("#manualsubentry").hide();        
        $("#manualupload").hide();
    }

 

    /* For to open manual cdr upload */
    function openmanualupload() {        
        $("#cdr_against_mobile_upload_status").show();
        var subnumber = $("#msisdn_no").val();        
        var request = $.ajax({
            url: "<?php echo URL::site("upload/get_msisdn_company"); ?>",
            type: "POST",
            dataType: 'text',
            data: {number: subnumber},
            success: function (responseTex)
            {
                if (responseTex == -1)
                {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);

                } else {
                   // alert(subnumber);
                    //$("#msisdn_div_options").hide();
                   // $("#msisdn_div").hide();
                    $("#manual_uuploads #phone_number").val(subnumber);
                    $("#manualupload").show();
                    $("#company_name").val(responseTex);                    
                      $("#cdr_against_mobile_upload_status").hide();
                }
                
                      if(responseTex== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
            },
            error: function (jqXHR, textStatus) {
                alert('Failed to recognize, Please Select Manually');
                $("#company_name_get").attr("readonly", false);
            }
        });


    }



    /* For find msisdn */
    function findphonenumber() {

        $("#modal-default").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);

    }

    /* For to find company name */
    function findcompanyname() {
        $("#findcompnay_image").show();
        $("#findcompanyname").html('');
        var subnumber = $("#inputSubNO").val();
        if (subnumber == '')
        {
            alert('Subscriber Number is empty');
        } else {
            var request = $.ajax({
                url: "<?php echo URL::site("upload/checkcompany"); ?>",
                type: "POST",
                dataType: 'text',
                data: {number: subnumber},
                success: function (responseTex)
                {
                    if (responseTex == -1)
                    {
                        alert('Failed to recognize');
                        $("#company_name_get").attr("readonly", false);
                        $("#findcompanyname").html('Search (Click)');

                    } else {
                        $("#findcompnay_image").hide();
                        $("#company_name_get").val(responseTex);
                        $("#findcompanyname").html('Search (Click)');
                    }
                      if(responseTex== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize, Please Select Manually');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }


  //Date picker
    $('#act_date').datepicker({
      autoclose: true
    });
     /* For to open manual subscriber entry */
    function openmanualsubentry(type) {
    //alert(type);
        if(type==1) {
            
        $("#manualupload").hide();
        $("#inputSubNO").val($("#msisdn_no").val());
        $("#msisdn_div_options").hide();
       // $("#msisdn_div").hide();
        $("#manuallocationentry").hide();
        $("#manualsubentry").show();
        // ajax call to upadte existing sub info
       var subnumber = $("#inputSubNO").val();
        var request = $.ajax({
            url: "<?php echo URL::site("upload/getsubinfo"); ?>",
            type: "POST",
           dataType: 'json',
            data: {number: subnumber},
            success: function (sub)
            {
               
              //  console.log(sub.mnc);
              //  alert(sub.mnc);              
              if(sub.actdate==0)
                sub.actdate='';
              $("#act_date").val(sub.actdate);  
              if(sub.imsi_number==0)
                sub.imsi_number='';
              $("#imsi").val(sub.imsi_number);    
            //   $("#inputName").val(sub.first_name);  
            //   $("#inputName1").val(sub.last_name);  
            //   $("#inputCNIC").val(sub.cnic_number);
            //   $("#inputCNIC").attr("readonly",true);
            if (sub) {
                // Normalize first name
                let firstName = sub.first_name ? sub.first_name.replace(/\s+/g, '').toLowerCase() : '';
                if (firstName === '' || firstName === 'unknown') {
                    $("#inputFirstName").val(sub.first_name);
                }

                // Normalize last name
                let lastName = sub.last_name ? sub.last_name.replace(/\s+/g, '').toLowerCase() : '';
                if (lastName === '' || lastName === 'unknown') {
                    $("#inputLastName").val(sub.last_name);
                }

                // Normalize CNIC
                let cnic = sub.cnic_number ? sub.cnic_number.replace(/\s+/g, '').toLowerCase() : '';
                if (cnic === '' || cnic === 'unknown') {
                    $("#inputCNIC").val(sub.cnic_number).prop("readonly", true);
                }
            }

            //  $("inputCNIC").prop('disabled', true);
              $("#inputAddress").val(sub.address);  
              $("#phone_name").val(sub.phone_name);  
              $("#company_name_get").val(sub.mnc); 
              $("#msisdn_is_foreigner").val(sub.is_foreigner); 
//              if(sub.cnic_number != '' || sub.cnic_number != 0){
//              $("#inputproject").attr('disabled',true); 
//                }
//              var is_foreigner=sub.is_foreigner;
//                if (parseInt(is_foreigner) == 0) {
//                        $('input[name="is_foreigner"][value="0"]').not(':checked').prop("checked", true); 
//                 } else {
//                        $('input[name="is_foreigner"][value="1"]').not(':checked').prop("checked", true);
//                }
              var sts=sub.status;
                if (parseInt(sts) == 0) {
                        $('input[name="StatusRadios"][value="0"]').not(':checked').prop("checked", true);  
                 } else {
                        $('input[name="StatusRadios"][value="1"]').not(':checked').prop("checked", true);
                }
              var ctype=sub.connection_type;
                if (parseInt(ctype) == 0) {
                        $('input[name="ConnectionTypeRadios"][value="0"]').not(':checked').prop("checked", true);  
                 } else {
                        $('input[name="ConnectionTypeRadios"][value="1"]').not(':checked').prop("checked", true);
                }
                if(sub== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
            },
            error: function (jqXHR, textStatus) {
              //  alert('Failed to get existing details, Please Reload Page');
            }
        }); 
        
        //second ajax call to update imei
        var request = $.ajax({
            url: "<?php echo URL::site("upload/getsimlastdeviceimei"); ?>",
            type: "POST",
           dataType: 'json',
            data: {number: subnumber},
            success: function (imei)
            {
              //  console.log(sub.mnc);
              //  alert(imei.imei_number); 
              //  alert(imei.phone_name); 
              if(imei.imei_number!=-1){
              $("#inputimei").val(imei.imei_number);
              $("#phone_name").val(imei.phone_name);
          }
          if(imei== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
            }
        }); 
    }else{
        $("#msisdn_div_options").hide();
        $("#manuallocationentry").hide();
        $("#manualsubentry").show();
        // empty form
        document.getElementById("manualsubform").reset();        
        $("#inputSubNO").val($("#msisdn_no").val());
        $("#inputproject").attr('disabled',false); 
        $("#inputCNIC").attr("readonly",false);
        } 
    }
        
//function to call request page for subscriber
    function requestsub(sim,simownerid) { 
        var request = "existing";
        var newForm = jQuery('<form name="custom_form_sub" id="custom_form_bd_sub" action="<?php echo URL::site("userrequest/request/1"); ?>" method="POST">');
            newForm.append(jQuery('<input>', {
                'name': 'msisdn',
                'value': sim,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'requesttype',
                'value': 3,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'pid',
                'value': simownerid,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'request',
                'value': request,
                'type': 'text'
            }));
            newForm.append('<input type="submit" id="custom_form_bd_bt_sub" name="submit" value="true"/>');
            $('#custom-form').append(newForm);
            //$("#custom_form_bd").submit();
            $('#custom_form_bd_bt_sub').trigger('click');
        
       
    }
//function to call request page for subscriber
    function requestsubviaemail() {        
        var sim = $("#msisdn_no").val(); 
        var request = "existing";
        var newForm = jQuery('<form name="custom_form_sub" id="custom_form_bd_sub" action="<?php echo URL::site("userrequest/request/1"); ?>" method="POST">');
            newForm.append(jQuery('<input>', {
                'name': 'msisdn',
                'value': sim,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'requesttype',
                'value': 3,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'pid',
                'value': -1,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'request',
                'value': request,
                'type': 'text'
            }));
            newForm.append('<input type="submit" id="custom_form_bd_bt_sub" name="submit" value="true"/>');
            $('#custom-form').append(newForm);
            //$("#custom_form_bd").submit();
            $('#custom_form_bd_bt_sub').trigger('click');        
       
    }
    
    function showDiv(elem) {
        // alert(elem);
        $('#tabmessage').hide();
        if (elem == 1 || elem.value == 1)
        {
            //Hide      
            $("#msisdn_div_options").hide();
            $("#msisdn_div_options_cdr_link").hide();
            $("#msisdn_div_options_sub_link").hide();
            $("#manualsubentry").hide();
            $("#manuallocationentry").hide();
            //  $("#manualimeisimsentry").hide();
            $("#manualupload").hide();
            //show   
            $('#msisdn_div').show();
        } else
        {
            //Hide      
            $('#msisdn_div').show();
            $("#msisdn_div_options").hide();
            $("#msisdn_div_options_cdr_link").hide();
            $("#msisdn_div_options_sub_link").hide();
            $("#manualsubentry").hide();
            $("#manuallocationentry").hide();
            //  $("#manualimeisimsentry").hide();
            $("#manualupload").hide();
        }

    }
    
</script>
<?php
if(!empty($post_request_type_id) && $post_request_type_id==3 && !empty($post_request_value))
    {    
?>
<script>
 $(document).ready(function () {  
     showDiv(1);
     $("#msisdn_no").val(<?php echo $post_request_value; ?>);
        jQuery(".msisdn_submit_btn").trigger("click");  
        // msisdn();                                    
        var fixsub=$("#msisdnerrorhandle").val();
        //alert(fixsub);
        if(fixsub===-1){
           // jQuery(".manualsubentry_link").trigger("click"); 
          // alert('new');
        openmanualsubentry(0);        
    }else{
       // alert('update')
       // jQuery(".manualsubentryexist_link").trigger("click");  
        openmanualsubentry(1);
    }    
    });
</script>
<?php
    }elseif(!empty($post_request_type_id) && $post_request_type_id==4 && !empty($post_request_value)){
    ?>
<script>
 $(document).ready(function () {  
     showDiv(1);
     $("#msisdn_no").val(<?php echo $post_request_value; ?>);
        jQuery(".msisdn_submit_btn").trigger("click");  
        // msisdn();                                    
        var fixsub=$("#msisdnerrorhandle").val();
        //alert(fixsub);
        if(fixsub===-1){
           // jQuery(".manualsubentry_link").trigger("click"); 
          // alert('new');
       // openmanualsubentry(0);        
    }else{
       // alert('update')
       // jQuery(".manualsubentryexist_link").trigger("click");  
        openmanuallocationentry();
    }    
    });
</script>

    <?php 
    }elseif(!empty($post_request_type_id) && $post_request_type_id==1 && !empty($post_request_value)){
    ?>
<script>
 $(document).ready(function () {  
     showDiv(1);
     $("#msisdn_no").val(<?php echo $post_request_value; ?>);
        jQuery(".msisdn_submit_btn").trigger("click");  
        var fixsub=$("#msisdnerrorhandle").val();
        if(fixsub===-1){     
    }else{ 
        openmanualupload();
    }    
    });
</script>

    <?php
    }
    ?>