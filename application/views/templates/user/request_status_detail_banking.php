<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$file_details = !empty($request_data['request_id']) ? Helpers_Upload::get_file_info_with_request_id($request_data['request_id']) : '';

$file_id = !empty($file_details['id']) ? $file_details['id'] : '';
$download_file_name = !empty($file_details['file']) ? $file_details['file'] : '';

$login_user = Auth::instance()->get_user();
$permission = Helpers_Utilities::get_user_permission($login_user->id);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-files-o"></i>
        Request Status
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('userrequest/request_status'); ?>">Request Status</a></li>
        <li class="active">Request Status Detail</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="">
                <div class="box box-primary">
                    <div class="row request_status_detail">
                        <div id="request_status" style="margin:15px; margin-left: 115px; margin-right: 115px;">                             
                            <h3 class="style14 col-md-12">Requesting User Details</h3>                            
                            <div class="form-group col-md-3">
                                <label for="user_name">User Name</label>
                                <?php
                                $userdetails = Helpers_Utilities::get_user_name($request_data['user_id']);
                                ?>
                                <input disabled type="text" class="form-control" id="user_name"  name="user_name" value="<?php echo $userdetails; ?>" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="user_name">User Type</label>
                                <?php
                                $usertype = (isset($request_data['user_id']) ) ? Helpers_Utilities::get_user_role_name($request_data['user_id']) : 'N/A';
                                ?>
                                <input disabled type="text" class="form-control" id="user_name"  name="user_name" value="<?php echo $usertype; ?>" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="user_name">Designation</label>
                                <?php
                                $userdesignation = ( isset($request_data['user_id']) ) ? Helpers_Utilities::get_user_job_title($request_data['user_id']) : 'NA';
                                ?>
                                <input disabled type="text" class="form-control" id="user_name"  name="user_name" value="<?php echo $userdesignation; ?>" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="user_name">Posting</label>
                                <?php
                                $userposting = ( isset($request_data['user_id']) ) ? Helpers_Profile::get_user_region_district($request_data['user_id']) : 'NA';
                                ?>
                                <input disabled type="text" class="form-control" id="user_name"  name="user_name" value="<?php echo $userposting; ?>" placeholder="">
                            </div>
                            <h3 class="style14 col-md-12">Request Related Information</h3>

                            <div class="form-group col-md-3">
                                <label for="company_name">Company Name</label>
                                <?php
                                $comp = Helpers_Utilities::get_banks_list($request_data['bank_id']);
                                ?>
                                <input disabled type="text" class="form-control" id="company_name"  name="company_name" value="<?php echo isset($comp->name) ? $comp->name : "N/A"; ?>" placeholder="Requested Value">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="Request_type">Request Type</label>
                                <?php
                                $rqts = Helpers_Utilities::get_request_type($request_data['user_request_type_id']);
                                ?>
                                <input disabled type="text" class="form-control" id="Request_type"  name="Request_type" value="<?php echo $rqts; ?>" placeholder="">
                            </div> 
                            <div class="form-group col-md-3">
                                <label for="requested_value">Requested Value</label>
                                <input disabled type="text" class="form-control" id="requested_value"  name="requested_value" value="<?php echo $request_data['requested_value']; ?>" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="requst_time">Request Time</label>
                                <input disabled type="text" class="form-control" id="requst_time"  name="requst_time" value="<?php echo $request_data['created_at']; ?>" placeholder="">
                            </div> 
                            <div class="form-group col-md-3">
                                <label for="requst">Request Status</label>
                                <?php
                                $display = 'block';                                
                                $sts = !empty($request_data['request_status']) ?  Helpers_Utilities::get_request_status_name_ctfu($request_data['request_status']) : '';
                                ?>
                                <input disabled type="text" class="form-control" id="requst"  name="requst" value="<?php echo $sts ?>" placeholder="">
                            </div>

                            <div class="form-group col-md-3" style="display: <?php echo $display; ?>">
                                <label for="dispatch_date">Request Dispath Date</label>
                                <input disabled type="text" class="form-control" id="dispatch_date"  name="dispatch_date" value="<?php echo $request_data['dispatch_date']; ?>" placeholder="">
                            </div>
                            
                            <div class="form-group col-md-3" style="display: <?php echo $display; ?>">
                                <label for="receive_date">Request Receive Date</label>
                                <input disabled type="text" class="form-control" id="receive_date"  name="receive_date" value="<?php echo $request_data['receive_date']; ?>" placeholder="">
                            </div>                            
                            <div class="form-group col-md-3">
                                <?php $projectslist = !empty($request_data['project_id']) ? Helpers_Utilities::get_projects_names($request_data['project_id']) : "NA"; ?>
                                <label for="project">Linked Projects</label>
                                <input disabled type="text" class="form-control" id="project"  name="project" value="<?php echo $projectslist; ?>" placeholder="">
                            </div> 
                            <div class="form-group col-md-9">
                                <label for="reason">Reason of Request</label>
                                <input disabled type="text" class="form-control" id="reason"  name="reason" value="<?php echo $request_data['reason']; ?>" placeholder="">
                            </div>                
                            <?php
                            $bank_id     = !empty($request_data['bank_id']) ? $request_data['bank_id'] : 0;
                            $dispatch_id = !empty($request_data['dispatch_id']) ? $request_data['dispatch_id'] : 0;                            
                            $file_data = Helpers_Utilities::get_request_file_info($bank_id, $dispatch_id);
                            $file_id = !empty($file_data['record_id']) ?  $file_data['record_id'] : 0;                           
                            $file_name = !empty($file_data['received_file_path']) ?  $file_data['received_file_path'] : '';                           
                            ?>                            
                            <h3 class="style14 col-md-12">Response Information</h3>
                                <div class="form-group col-md-8">
                                    <label for="subject">Received File Path</label>
                                    <input disabled type="text" class="form-control" id="subject"  name="subject" value="<?php echo $file_name; ?>" placeholder="File Path if any">
                                </div> 
                                <div class="form-group col-md-4">
                                    <?php
                                    if (!empty($file_id)) {
                                        $file_id = Helpers_Utilities::encrypted_key($file_id, 'encrypt')
                                        ?>
                                    <a style="margin-top: 23px;" href="<?php echo URL::site('/userrequest/download_request_file/?record_id=' . $file_id); ?>" class="btn btn-primary">Download file</a>
                                    <?php } ?>   
                                </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
</section>
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<script>
                                                    $(function () {
                                                        //bootstrap WYSIHTML5 - text editor
                                                        //$(".textarea").wysihtml5();
                                                        CKEDITOR.replace('body_txt');
                                                        CKEDITOR.disableAutoInline = false;
                                                    });
</script>

<script type="text/javascript">
    function markreplysent(request_id) {
        $.ajax({url: '<?php echo URL::base() . "Userrequest/reply_sent?request_id="; ?>' + request_id,
            success: function (result) {
                if (result == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#replysent").hide();
            }});
    }
    function addText() {
        var content = CKEDITOR.instances.body_txt.getData()
        content += ' ' + $('#token_list option:selected').val();
        CKEDITOR.instances.body_txt.setData(content)
        console.log(content);
    }
</script>

<style>
    #cke_1_top {
        display: none;
    }
</style>

<script>
    $(document).ready(function () {
        $('[data-toggle="popover"]').popover();
    });
</script>