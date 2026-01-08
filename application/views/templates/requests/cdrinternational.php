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
        <small>Tracer</small>
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
            $internationalnumber = !empty($post['internationalnumber']) ? $post['internationalnumber'] : 0;
            $redirect_url = !empty($post['url']) ? $post['url'] : 'http://ctd.aiesplus.kpk/Userdashboard/dashboard';
            //  echo $msisdn;
            ?>
            <div class="box box-primary">
                <div id="headerdiv" class="box-header with-border">
                    <h3 class="box-title">Request CDR Against International Number</h3>
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
                        <input type="hidden" id="ChooseTemplate" name="ChooseTemplate" value="9" placeholder="value 9 for CDR Against Internation number Number" />
                        <input type="hidden" id="startdate_duration" name="startdate_duration"/>
                        <input type="hidden" id="enddate_duration" name="enddate_duration"/>
                        <div id="dataform">
                            <div class="col-sm-6" id="sub_div" style="">
                                <div class="form-group" style="">                                                            
                                    <label for="inputInternationalNo" class="control-label">International Number</label>                                                                                                                        
                                    <input type="text" class="form-control" name="inputInternationalNo" id="inputInternationalNo" value="<?php echo ($internationalnumber != 0) ? $internationalnumber : '';?>" placeholder="International number" readonly>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="company_name_get" class="control-label"><span>Company Name </span></label>
                                    <select readonly class="form-control" name="company_name_get[]" id="company_name_get" style="width: 100%;">                                        
                                        <option value="12" selected>International</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6" id="quickoption_div">
                                <div class="form-group" >
                                    <label for="quickoption" class="control-label">Quick Options (for start and End Date)</label>
                                    <div class="col-md-12" id="quickoption">
                                        <button type="button" onclick="dateonemonth()" class="btn btn-primary" >Last 1 Month </button>                                            
                                        <button type="button" onclick="datetwomonths()" class="btn btn-primary" >Last 2 Months </button>                                            
                                        <button type="button" onclick="datethreemonths()" class="btn btn-primary" >Last 3 Months </button>
                                        <button type="button" onclick="datesixmonths()" class="btn btn-primary" >Last 6 Months </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3" id="datefrom_div">
                                <div class="form-group" >
                                    <label for="startDate" class="control-label">Date From (mm/dd/yyyy)</label>
                                    <input type="text"  readonly="readonly" class="form-control" name="startDate" id="startDate" value="" placeholder="mm/dd/yyyy">
                                </div>
                            </div>
                            <div class="col-sm-3" id="dateto_div">
                                <div class="form-group" >
                                    <label for="endDate" class="control-label">Date To (mm/dd/yyyy)</label>
                                    <input type="text" readonly="readonly" class="form-control" name="endDate" id="endDate" value="" placeholder="mm/dd/yyyy">
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
        var requesttype = $("#ChooseTemplate").val();                
        var inputINTNO = $("#inputInternationalNo").val();
        var result = {requesttype: requesttype, inputINTNO: inputINTNO}
        //ajax to upload device informaiton
        $.ajax({
            url: "<?php echo URL::site("userrequest/cdrintpermission"); ?>",
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
        
        $("#userrequest").validate({
            rules: {
                ChooseTemplate: {
                    required: true,
                    check_list: true
                },
                "company_name_get[]": {
                    required: true,
                    check_list: true
                },
                "inputproject[]": {
                    required: true,
                    check_list: true
                },
                inputInternationalNo: {
                    required: true,
                    number: true
                },                                
                startDate: {
                    required: true,
                    vailddate: true                    
                },
                endDate: {
                    required: true,
                    greaterThan: "#startDate"
                },
                inputreason: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 5,
                    maxlength: 500
                }
            },
            messages: {                
                inputInternationalNo: {
                },                                
                startDate: {
//                    required: "Enter date from"
                },
                endDate: {
                    //required: "Enter date to"
                },
                inputreason: {
                    required: "Enter reason for request",
                    maxlenght: "Maximum character limit is 500",
                    minlength: "Min character limit is 10"
                }
            },            
        });


        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "" || sel == 0) {
                return false;
            } else {
                return true;
            }
        }, "<span>Select One</span>");

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

        jQuery.validator.addMethod("greaterThan",
                function (value, element, params) {

                    if (!/Invalid|NaN/.test(new Date(value))) {
                        return new Date(value) > new Date($(params).val());
                    }
                    return isNaN(value) && isNaN($(params).val())
                            || (Number(value) > Number($(params).val()));
                }, 'Must be greater than ( Date From )');


        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .,/]+$/);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");
        
                   

    });
    //Date picker
    var today = new Date();
    $('#startDate').datepicker({
        endDate: "today",
        maxDate: today,
        autoclose: true
    });
    var today = new Date();
    $('#endDate').datepicker({
        endDate: "today",
        maxDate: today,
        autoclose: true
    });
    var changecount = 1;

    function dateonemonth() {
        var today = currentdate();
        var onemonthago = backdate(30);
        document.getElementById('endDate').value = today;
        document.getElementById('startDate').value = onemonthago;
    }
    function datetwomonths() {
        var today = currentdate();
        var twomonthsago = backdate(60);
        document.getElementById('endDate').value = today;
        document.getElementById('startDate').value = twomonthsago;
    }
    function datethreemonths() {
        var today = currentdate();
        var threemonthsago = backdate(86);
        document.getElementById('endDate').value = today;
        document.getElementById('startDate').value = threemonthsago;
    }
    function datesixmonths() {
        var today = currentdate();
        var sixmonthago = backdate(170);
        document.getElementById('endDate').value = today;
        document.getElementById('startDate').value = sixmonthago;
    }
    function currentdate() {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }
        today = mm + '/' + dd + '/' + yyyy;
        return today;
    }
    function backdate(value) {
        var beforedate = new Date();
        var priordate = new Date();
        priordate.setDate(beforedate.getDate() - value);
        var dd2 = priordate.getDate();
        var mm2 = priordate.getMonth() + 1;//January is 0, so always add + 1
        var yyyy2 = priordate.getFullYear();
        if (dd2 < 10) {
            dd2 = '0' + dd2
        }
        ;
        if (mm2 < 10) {
            mm2 = '0' + mm2
        }
        ;
        var datefrommonthago = mm2 + '/' + dd2 + '/' + yyyy2;

        return datefrommonthago;
    }
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
                        url: "<?php echo URL::site('email/send') ?>",
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
                        swal("System Error", "Contact AIES Support Team.", "error");
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
    //function to compare dates
    function toTimestamp(myDate) {
        myDate = myDate.split("/");
        var newDate = myDate[0] + " " + myDate[1] + " " + myDate[2];
        var result = (new Date(newDate).getTime()); //will alert 1330210800000
        return (Date.parse(newDate)); //will alert 1330210800000
    }

</script>