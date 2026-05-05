<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-whatsapp"></i>
        Request  
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Request</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php
            $person_id = !empty($post['pid']) ? $post['pid'] : 0;
            $request = !empty($post['request']) ? $post['request'] : "na";
            $cnicnumber = !empty($post['cnicnumber']) ? $post['cnicnumber'] : 0;
            $redirect_url = !empty($post['url']) ? $post['url'] : URL::site('userdashboard/dashboard');
            //  echo $msisdn;
            ?>
            <div class="box box-primary">
                <div id="headerdiv" class="box-header with-border">
                    <h3 class="box-title">Request Family Tree</h3>
                    <a href="<?php echo $redirect_url;?>" class="btn btn-warning btn-small" style="float: right;"><i class="fa fa-backward"></i> Go Back</a>
                </div> 
                <form class="ipf-form request_net" name="requestform" id="userrequest" method="post" enctype="multipart/form-data" >
                    <div class="box-body">
                        <div class="alert" id="request_permission_check_status" style="color: '#ff5b3c'; display: none">
                            <h4><i class="icon fa fa-check"></i> 
                                <span id='parsresult'> Be Patient ! Preparing Request....
                                    <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                                </span></h4>
                        </div>
                        <div class="form-group col-md-12 " >
                            <div class="" id="notification_msgbasic1" style="display: none; margin-bottom: 1px !important">
                                <h4><div id="notification_msg_divbasic1"></div></h4>
                            </div>
                        </div>                                                                        
                        <div class="col-sm-12 ">
                            <?php
                            try {
                                //$id==1 means no person existed in db, record is new where as $id=2 means person existed in db and request is for record upload 
                                if (!empty($post['pid'] && $post['pid'] != -1)) {
                                    Helpers_Requests::get_person_information($post['pid']);
                                    //$id==1 means no person existed in db, record is new where as $id=2 means person existed in db and request is for record upload 
                                } else {
                                    echo Helpers_Requests::get_new_person_information();
                                }
                            } catch (Exception $ex) {
                                echo Helpers_Requests::get_exception_message();
                                }
                            ?>
                        </div>  
                        <input type="hidden" name="person_id" value="<?php echo $person_id; ?>" />
                        <input type="hidden" name="redirect_url" id="redirect_url" value="<?php echo $redirect_url; ?>" />                        
                        <div id="dataform">
                            <div class="col-sm-6" id="sub_div" style="">
                                <div class="form-group" style="">                                                            
                                    <label for="cnic_number" class="control-label">CNIC Number</label>                                                                                                                        
                                    <input type="text" class="form-control" name="cnic_number" id="cnic_number" value="<?php echo ($cnicnumber != 0) ? $cnicnumber : '';?>" readonly>
                                </div>
                            </div>                                                        
                            <div class="col-sm-12" id="project_div" style="margin-top: 10px; ">
                                <div class="form-group">                                                            
                                    <label for="inputproject" class="control-label">Linked Project</label> 
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
                            <div class="col-sm-12" id="reason_div">
                                <div class="form-group" >                                                            
                                    <label for="inputreason" class="control-label">Reason For This Request</label>
                                    <textarea class="form-control" name="inputreason" id="inputreason"  placeholder="Enter Reason For Request" ></textarea>                                                          
                                </div>
                            </div>
                            <div class="form-group" id="submit_div">
                                <div class="col-sm-12">
                                    <button  id="userrequestbtn" type="button" onclick="submitrequestform()" class="btn btn-primary pull-right" style="margin-top:10px" >Submit</button>
                                </div>
                            </div>                                                         
                        </div>
                    </div>
                </form>
            </div>                                            
            <!--col-md-12-->
        </div> 
    </div>

</section>
<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>
<script>
        $('#userrequest').one('submit', function () {
            $(this).find('input[type="submit"]').attr('disabled', 'disabled');
        });
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();
        });
</script>
<script type="text/javascript">
    $(document).ready(function () {        
            $('html, body').animate({
                scrollTop: $('#headerdiv').offset().top
            }, 'slow');
        
        // start request permission check
        $('#dataform').hide();
        $('#request_permission_check_status').show();                      
        var cnic_number = $("#cnic_number").val();
        var result = {cnic_number: cnic_number}
        //ajax to upload device informaiton
        $.ajax({
            url: "<?php echo URL::site("userrequest/familytreepermission"); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (result) {
                if (result == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                } else {
                    var result = JSON.parse(result);
                    var request_permission = result.permission;
                    var request_message = result.message;
                    $('#request_permission_check_status').hide();
                    switch (request_permission) {
                        case 0:
                            $('#dataform').show();
                            break;
                        case 1:
                            $("#notification_msg_divbasic1").html(request_message);
                            $("#notification_msgbasic1").show();
                            $("#notification_msgbasic1").addClass('alert');
                            $("#notification_msgbasic1").addClass('alert-danger');
                            break;
                    }
                }
            }
        });
        // End request permission check
            
        //request form validation
        $("#userrequest").validate({
            rules: {               
                "inputproject[]": {
                    required: true,
                    check_list: true
                },
                cnic_number: {
                    required: true,
                    minlength: 13,
                    maxlength: 13
                },
                inputreason: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 5,
                    maxlength: 500
                }
            },
            messages: {
                inputreason: {
                    required: "Enter reason for request",
                    maxlenght: "Maximum character limit is 500",
                    minlength: "Min character limit is 10"
                }
            },            
        });
        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .,/]+$/);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "" || sel == 0) {
                return false;
            } else {
                return true;
            }
        }, "<span>Select One</span>");            
    });
             //user request form submit via ajax
             function submitrequestform(){   
                 //var formData = $('#userrequest').serialize();
                 var formData = $("#userrequest").serializeArray();
                 //var formData = new FormData();
                 var url = $("#redirect_url").val();
               if ($('#userrequest').valid())
                {
                $("#preloader").show();
                $.ajax({
                        type: 'POST',
                        url: "<?php echo URL::site('email/requestfamilytree') ?>",
                        data: formData,
                        success: function (result) {
                            $("#preloader").hide();
                            if (result == 1) {
                                swal({
                                        title: "Congratulations!",
                                        text: "You want to view request or go back?",
                                        type: "success",
                                        showCancelButton: true,
                                        confirmButtonClass: "btn-primary",
                                        confirmButtonText: "View Request",
                                        cancelButtonText: "Go Back",
                                        closeOnConfirm: false,
                                        closeOnCancel: false
                            },
                                function (isConfirm) {
                                    if (isConfirm) {
                                        window.location.href = '<?php echo URL::site('Userrequest/request_status', TRUE); ?>';
                                    } else {
                                        window.location.href = url;
                                    }
                                });
                    } else {
                        swal("System Error", "Contact DRAMS Support Team.", "error");
                        window.location.reload(false);
                    }

                },
                error: function (data) {
                    console.log("error");
                    console.log(result);
                }
            });
        }

    }

</script>