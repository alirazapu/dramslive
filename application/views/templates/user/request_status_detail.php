<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$file_details = !empty($results['request_id']) ? Helpers_Upload::get_file_info_with_request_id($results['request_id']) : '';

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
                <?php
                if (isset($_GET["resend"]) && $_GET["resend"] == 1) {
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <?php echo 'successfull'; ?></h4>
                    </div>
                <?php } ?>
                <div class="box box-primary">
                    <div class="row request_status_detail">
                        <div id="request_status" style="margin:15px; margin-left: 115px; margin-right: 115px;">  
                            <?php
                            $DB = Database::instance();
                            $login_user = Auth::instance()->get_user();
                            $permission = Helpers_Utilities::get_user_permission($login_user->id);
                            $current_date = date("Y-m-d H:i:s");
                            //echo $current_date . '<br>';
                            $send_date = $results['sending_date'];
                            //echo $send_date . '<br>';
                            $difference = round((strtotime($current_date) - strtotime($send_date)) / 3600, 0);
                            //echo $difference;
                            //if (($results['user_request_type_id']!=10 && $difference >= 2 && ($results['status'] == 1)) || ($results['status'] == 3 || $permission == 1 || $permission == 2)) {                            
                            //if ($difference >= 2 && ($results['status'] == 1 || $results['status'] == 3) && ($permission == 1 || $permission == 2)) {
                            if ($difference >= 2 && ($permission == 5 || $permission == 1 || $permission == 2)) {
                                 $rqtid = Helpers_Utilities::encrypted_key($results['request_id'], 'encrypt');
                                ?>
                                <a style="" target="" href="<?php echo URL::base() . '/userrequest/request_resend?request_id=' . $rqtid ?>" class="btn btn-warning pull-right" >Request Re-queue</a>
                            <?php } ?>
                            <h3 class="style14 col-md-12">Requesting User Details</h3>                            
                            <div class="form-group col-md-3">
                                <label for="user_name">User Name</label>
                                <?php
                                $userdetails = Helpers_Utilities::get_user_name($results['user_id']);
                                ?>
                                <input disabled type="text" class="form-control" id="user_name"  name="user_name" value="<?php echo $userdetails; ?>" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="user_name">User Type</label>
                                <?php
                                $usertype = (isset($results['user_id']) ) ? Helpers_Utilities::get_user_role_name($results['user_id']) : 'N/A';
                                ?>
                                <input disabled type="text" class="form-control" id="user_name"  name="user_name" value="<?php echo $usertype; ?>" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="user_name">Designation</label>
                                <?php
                                $userdesignation = ( isset($results['user_id']) ) ? Helpers_Utilities::get_user_job_title($results['user_id']) : 'NA';
                                ?>
                                <input disabled type="text" class="form-control" id="user_name"  name="user_name" value="<?php echo $userdesignation; ?>" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="user_name">Posting</label>
                                <?php
                                $userposting = ( isset($results['user_id']) ) ? Helpers_Profile::get_user_region_district($results['user_id']) : 'NA';
                                ?>
                                <input disabled type="text" class="form-control" id="user_name"  name="user_name" value="<?php echo $userposting; ?>" placeholder="">
                            </div>
                            <h3 class="style14 col-md-12">Request Related Information</h3>

                            <div class="form-group col-md-3">
                                <label for="company_name">Company Name</label>
                                <?php
                                $comp = Helpers_Utilities::get_companies_data($results['company_name']);
                                ?>
                                <input disabled type="text" class="form-control" id="company_name"  name="company_name" value="<?php echo isset($comp->company_name) ? $comp->company_name : "N/A"; ?>" placeholder="Requested Value">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="Request_type">Request Type</label>
                                <?php
                                $rqts = Helpers_Utilities::get_request_type($results['user_request_type_id']);
                                ?>
                                <input disabled type="text" class="form-control" id="Request_type"  name="Request_type" value="<?php echo $rqts; ?>" placeholder="">
                            </div> 
                            <div class="form-group col-md-2">
                                <label for="Request_sentcount">Sent(count)</label>                                
                                <input disabled type="text" class="form-control" id="Request_sentcount"  name="Request_sentcount" value="<?php echo $results['request_send_count']; ?>" placeholder="">
                            </div> 
                            <div class="form-group col-md-3">
                                <label for="requested_value">Requested Value</label>
                                <input disabled type="text" class="form-control" id="requested_value"  name="requested_value" value="<?php echo $results['requested_value']; ?>" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="requst_time">Request Time</label>
                                <input disabled type="text" class="form-control" id="requst_time"  name="requst_time" value="<?php echo $results['created_at']; ?>" placeholder="">
                            </div> 
                            <div class="form-group col-md-3">
                                <label for="requst">Request Status</label>
                                <?php
                                $display = 'block';
                                $sts ='';
                                
                                if ($results['user_request_type_id'] == 8) {
                                    $display = 'none';
                                    $sts = Helpers_Utilities::get_nadra_request_status_name($results['status']);
                                } else {
                                    $sts = Helpers_Utilities::get_request_status_name($results['status']);
                                }
                                ?>
                                <input disabled type="text" class="form-control" id="requst"  name="requst" value="<?php echo $sts ?>" placeholder="">
                            </div>

                            <div class="form-group col-md-3" style="display: <?php echo $display; ?>">
                                <label for="requested_value">E-Mail Sent Date</label>
                                <input disabled type="text" class="form-control" id="requested_value"  name="requested_value" value="<?php echo $results['sending_date']; ?>" placeholder="">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="reason">Data Parsing Status</label>
                                <?php
                                if ($results['user_request_type_id'] == 8) {
                                    $parsing_status = $sts;
                                } else {
                                    $parsing_status = Helpers_Utilities::get_parsing_status_name($results['processing_index']);
                                }
                                ?>
                                <input disabled type="text" class="form-control" id="reason"  name="reason" value="<?php echo $parsing_status; ?>" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="reason">Data Parsing Error Details</label>
                                <?php
                                if ($results['user_request_type_id'] == 8) {
                                    $parsing_status = $sts;
                                } else {
                                    $parsing_status = Helpers_Utilities::get_parsing_error_details($results['request_id']);
                                }
                                ?>
                                <input disabled type="text" class="form-control" id="reason"  name="reason" value="<?php echo $parsing_status; ?>" placeholder="">
                            </div>
                            <div class="form-group col-md-3">
                                <?php $projectslist = !empty($results['project_id']) ? Helpers_Utilities::get_projects_names($results['project_id']) : "NA"; ?>
                                <label for="project">Linked Projects</label>
                                <input disabled type="text" class="form-control" id="project"  name="project" value="<?php echo $projectslist; ?>" placeholder="">
                            </div> 
                            <div class="form-group col-md-6">
                                <label for="reason">Reason of Request</label>
                                <input disabled type="text" class="form-control" id="reason"  name="reason" value="<?php echo $results['reason']; ?>" placeholder="">
                            </div> 
                            <div style="display: <?php echo $display; ?>">
                                <div class="col-md-12">
                                    <h3 class="style14 col-md-12">E-Mail Information </h3>
                                    <hr class="style14 col-md-12">
                                    <div class="form-group col-md-12">
                                        <label for="subject">E-Mail Subject</label>
                                        <input disabled type="text" class="form-control" id="subject"  name="subject" value="<?php echo $results['message_subject']; ?>"  >
                                    </div>                             
                                    <div class="form-group col-md-12">
                                        <label for="body">E-Mail Body</label>
                                        <textarea disabled style="height: 200px; border: 1px solid;" class="form-control" id="body" name="body"><?php echo strip_tags($results['message_body']); ?></textarea>                                
                                    </div> 
                                </div>
                                <div class="col-md-12">
                                    <h3 class="style14 col-md-12">Response Information</h3>
                                    <hr class="style14 col-md-12">
                                    <div class="form-group col-md-8">
                                        <label for="subject">Received File Path</label>
                                        <input disabled type="text" class="form-control" id="subject"  name="subject" value="<?php echo $download_file_name; ?>" placeholder="File Path if any">
                                    </div>     
                                        <?php
                                        if (!empty($file_id) && (!empty($download_file_name))) {
                                                $file_id= Helpers_Utilities::encrypted_key($file_id, 'encrypt')
                                            ?>
                                            <form class="" name="download" action="<?php echo url::site() . 'personprofile/download' ?>" id="downloadfile" method="post" enctype="multipart/form-data">
                                                <input name="file" value="<?php echo $download_file_name ?>" type="hidden">
                                                <input name="fid" value="<?php echo $file_id; ?>" type="hidden">
                                                <!--<a style="margin-top: 25px" target="" href="<?php //echo URL::base() . '/personprofile/download?fid=' . base64_encode($file_id) . '&file=' . $download_file_name ?>" class="btn btn-danger pull-right" >Download File</a>-->
                                                <input style="margin-top: 25px" type="submit" value="Download" class="btn btn-primary" />
                                            </form>
                                        <!--<a style="margin-top: 25px" target="" href="<?php echo URL::base() . '/personprofile/download?fid=' . base64_encode($file_id) . '&file=' . $download_file_name ?>" class="btn btn-danger pull-right" >Download File</a>-->
                                    <?php } ?>
                                    <div class="form-group col-md-12">
                                        <div style="" id="body">
                                            <label for="body">Received Body

                                                <a style="cursor: pointer; font-size: 11px;" data-toggle="popover" title="Ecoded Body Message" 
                                                   data-content="<?php echo $results['received_body'];
                                    ?>">
                                                    Encoded Body
                                                </a>

                                            </label>
                                            <textarea id="body_txt" readonly="" value=""  name="body" class="textarea form-control" placeholder="Please enter template format heare with the help of tokens" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">
                                            <?php
                                            if (!empty($results['received_body_raw']) && $results['received_body_raw'] != 'na')
                                                echo str_replace("%20", " ", $results['received_body_raw']);
                                            elseif (!empty($results['received_body']) && $results['received_body'] != 'na')
                                                echo str_replace("%20", " ", $results['received_body']);
                                            else {
                                                echo '';
                                            }
                                            ?>
                                            </textarea>
                                            <div style="display:hidden">

                                            </div>   
                                        </div>
                                        <!--                                    <div style="background-color: wheat;border: 1px solid; padding: 10px; height: 200px !important; overflow: auto; scroll-behavior:auto">
                                        <?php //echo ($results['received_body_raw']); ?>
                                                                            </div>-->

                                        <div class="col-md-12">
                                            <form class="" name="fixerror" id="fixerror" action="<?php if ($results['user_request_type_id'] == 2) {
                                                echo url::site() . 'user/upload_against_imei';
                                            } elseif ($results['user_request_type_id'] == 5) {
                                                echo url::site() . 'user/upload_against_cnic';
                                            } else {
                                                echo url::site() . 'user/upload_against_msisdn';
                                            } ?>"  method="post"  >
                                                <input type="hidden" name="requestid" value="<?php echo $results['request_id']; ?>">
                                                <input type="hidden" name="mnc" value="<?php echo $results['company_name']; ?>">
                                                <input type="hidden" name="receivedfilepath" value="<?php echo $results['received_file_path']; ?>">
                                                <textarea hidden="" type="text" name="receivedbody"><?php echo '<br>'.'Company: '.$comp->company_name.' <br> '.',    Project:'.$projectslist.' <br> '.$results['received_body']; ?>"</textarea>
                                                <textarea hidden="" type="text" name="receivedbodyraw"><?php echo '<br>'.'Company: '.$comp->company_name.' <br> '.',    Project:'.$projectslist.' <br> '.$results['received_body_raw']; ?>"</textarea>
                                                <input type="hidden" name="requesttype" value="<?php echo $results['user_request_type_id']; ?>">
                                                <input type="hidden" name="requestvalue" value="<?php echo $results['requested_value']; ?>">
                                                <?php
                                                if ($results['user_request_type_id'] != 8 && $results['processing_index'] == 3 && (($permission == 2 || $permission == 1 || $permission == 5 || $permission == 4) || ( ($permission == 4) && ($results['user_request_type_id'] == 1 || $results['user_request_type_id'] == 2 || $results['user_request_type_id'] == 6)))) {
                                                    ?>
                                                    <button style="margin-top: 15px" type="submit" class="btn btn-danger pull-right" >Manual Upload</button>
                                                <?php } ?>
                                            </form>

                                            <form class="" name="process_complete" id="process_complete" action="<?php echo url::site() . 'user/mark_complete' ?>"  method="post"  >
                                                <?php $rqt_id = Helpers_Utilities::encrypted_key($results['request_id'], 'encrypt'); ?>
                                                <input type="hidden" name="requestid" value="<?php echo $rqt_id; ?>">
                                                <?php
                                                $login_user = Auth::instance()->get_user();
                                                $DB = Database::instance();
                                                $permission = Helpers_Utilities::get_user_permission($login_user->id);
                                                if ($results['user_request_type_id'] != 8 && $results['processing_index'] == 3 && (($permission == 2 || $permission == 5 || $permission == 1) || ( ($permission == 4) && ($results['user_request_type_id'] == 1 || $results['user_request_type_id'] == 2 || $results['user_request_type_id'] == 6)))) {
                                                    ?>
                                                    <button title="Only mark completed if data is not able to upload manually" style="margin-top: 15px; margin-right: 15px;" type="submit" class="btn btn-danger pull-right" >Mark Complete</button>
                                                <?php } ?>
                                            </form>


                                            <?php
                                            if ($results['user_request_type_id'] != 8 && $results['status'] == 2 && $results['reply'] == 0) {
                                                ?>
                                                <button id="replysent" style="margin-top: 15px; margin-right:10px;" onclick="markreplysent('<?php echo Helpers_Utilities::encrypted_key($results['request_id'], 'encrypt') ?>')" class="btn btn-warning pull-right" >Reply Sent</button>
                                            <?php } ?>
                                        </div>
                                    </div>                                        

                                </div>
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
        // Optional: If you still have any old WYSIHTML5 code commented out, you can remove it.
        // $(".textarea").wysihtml5();  // ← safe to delete if not used

        CKEDITOR.replace('body_txt', {
            versionCheck: false,  // This ensures it's disabled for this instance (extra safety)

            // If you want, you can move some/all of your toolbar/custom settings here instead of config.js
            // (but since you already have them in config.js, no need unless overriding per-instance)
            toolbarGroups: [
                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'forms' },
                { name: 'tools' },
                { name: 'document',       groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'others' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
                { name: 'styles' },
                { name: 'colors' },
                { name: 'about' }
            ],
            removeButtons: 'Underline,Subscript,Superscript',
            format_tags: 'p;h1;h2;h3;pre',
            removeDialogTabs: 'image:advanced;link:advanced'
            // Add any other per-instance overrides if needed
        });

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