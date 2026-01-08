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
            $cnic = !empty($post['cnic']) ? $post['cnic'] : 0;
            $redirect_url = !empty($post['url']) ? $post['url'] : 'http://ctd.aiesplus.kpk/Userdashboard/dashboard';
            //  echo $msisdn;
            ?>
            <div class="box box-primary">
                <div id="headerdiv" class="box-header with-border">
                    <h3 class="box-title">Request SIM's Against CNIC </h3>
                    <a href="<?php echo $redirect_url;?>" class="btn btn-warning btn-small" style="float: right;"><i class="fa fa-backward"></i> Go Back</a>
                </div> 
                <form class="ipf-form request_net" name="requestform" id="userrequest" method="post" enctype="multipart/form-data" >
                    <div class="box-body">                                    
                        <div class="alert " id="upload" style="color: '#ff5b3c'; display: none">
                            <!--                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>-->
                            <h4><i class="icon fa fa-check"></i> 
                                <span id='parsresult'> Be Patient request in process 
                                    <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                                </span></h4>
                        </div>                                    
                        <div class="alert" id="request_permission_check_status" style="color: '#ff5b3c'; display: none">
                            <h4><i class="icon fa fa-check"></i> 
                                <span id='parsresult'> Be Patient ! Preparing Request....
                                    <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                                </span></h4>
                        </div>                                                                                                                                    
                        <div class="form-group col-md-12" id="company_inqueue0" style="display: none;min-height: 64px !important;" >
                            <div class="" id="company_inqueue" style="display: none; margin-bottom: 1px !important">                                
                                <h4 id="company_list">Requests in queue or generated within last 15 Days: </h4>
                            </div>
                        </div>                                                                        
                        <div class="col-sm-12 ">
                            <?php
                            try {
                                //$id==1 means no person existed in db, record is new where as $id=2 means person existed in db and request is for record upload 
                                if (!empty($person_id)) {
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
                        <input type="hidden" id="ChooseTemplate" name="ChooseTemplate" value="5" placeholder="value 5 for SIM's Against CNIC" />                                                
                        <div id="dataform">
                            <div class="col-sm-6" id="sub_div" style="">
                                <div class="form-group" style="">                                                            
                                    <label for="inputCNIC" class="control-label">CNIC Number</label>                                                                                                                        
                                    <input type="text" class="form-control" name="inputCNIC" id="inputCNIC" value="<?php echo ($cnic != 0) ? $cnic : '';?>" readonly>
                                </div>
                            </div>
                            <div style="display:none">
                                <?php (!empty($msisdn) && $msisdn != 0) ? $company_mnc_tel = Helpers_Utilities::search_mnc_ofmobile($msisdn) : $company_mnc_tel = ''; ?>
                            </div>    
                            <div class="col-sm-6" id="company_div" style="">
                                <div class="form-group"  >
                                    <label for="company_name_get" class="control-label"><span>Company Name </span> </label>
                                    <select class="form-control select2" multiple="multiple" name="company_name_get[]" id="company_name_get" data-placeholder="Select compnay from list"  style="">                                        
                                        <?php $comp_name_list = Helpers_Utilities::get_companies_data();
                                        foreach ($comp_name_list as $list) {
//                                            if ($list->company_id < 6 || $list->company_id==9) { // with SCOM
                                            if ($list->company_id < 6 && $list->company_id!=2) {
                                            ?>
                                            <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                            <?php }} ?>
                                    </select>
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
    var company_queue = null;
    $(document).ready(function () {        
            $('html, body').animate({
                scrollTop: $('#headerdiv').offset().top
            }, 'slow');
         
        // start initial request controller
        $('#dataform').hide();
        $('#request_permission_check_status').show();                        
        var cnic = $("#inputCNIC").val();
        var result = {cnic: cnic}
        //ajax to upload device informaiton
        $.ajax({
            url: "<?php echo URL::site("userrequest/cnicsimspermission"); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (result) {
                if (result == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }else{
                    var result = JSON.parse(result);
                    var request_permission = result.permission;
                    var request_message = result.message;
                    var request_mnc = result.mnc;                    
                    var array_mnc = request_mnc.split(',');
                    company_queue  = array_mnc;
                    $('#request_permission_check_status').hide();
                    switch(request_permission){
                        case 0:
                            $('#dataform').show();
                            break;
                        case 2:                            
                            for(var i = 0; i<array_mnc.length; i++){
                                var span = document.createElement('span');
                                span.className = 'label label-primary';
                                span.innerHTML = compnay_name(array_mnc[i]);
                                span.style.margin = '2px';
                                var company_list = document.getElementById('company_list');
                                company_list.appendChild(span);                                                                                        
                            }
                            //$("#company_inqueue").html(request_mnc);
                            if(request_mnc !=''){
                                
                            $("#company_inqueue0").show();
                            $("#company_inqueue").show();
                            $("#company_inqueue").addClass('alert');
                            $("#company_inqueue").addClass('alert-danger');
                    }else{
                        
                    }
                    
                            $('#dataform').show();
                            break;
                    }                   
                }
            }
        });
        
        //get company name from mnc
        function compnay_name(mnc){
            var data = mnc;
            var company = 'None';
                        switch(data){ 
                            case '1':
                                company =  'Mobilink';
                                break;
                            case '7':
                                company =  'Warid';
                                break;
                            case '3':
                                company =  'Ufone';
                                break;
                            case '6':
                                company =  'Telenor';
                                break;
                            case '4':
                                company =  'Zong';
                                break;
                       }
        return company;
       }
        // close intital request controller
        $("#userrequest").validate({
            rules: {
                ChooseTemplate: {
                    required: true,
                    check_list: true
                },
                "company_name_get[]": {
                    required: true,
                },
                "inputproject[]": {
                    required: true,
                    check_list: true
                },
                inputCNIC: {
                    required: true,
                    number: true,
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
             //user request form submit via ajax
             function submitrequestform(){                   
                 //var formData = $('#userrequest').serialize();
                 var formData = $("#userrequest").serializeArray();
                 //var formData = new FormData();
                 var url = $("#redirect_url").val();                 
                 if ($('#userrequest').valid())
                 {                    
                    var in_queue = in_queue_check();
                    if (in_queue == 1) {
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

    }
    //function to check company is queue lsit 
    function in_queue_check() {    
        var values = $('#company_name_get').val();          
        var match = 0;
        for(var i = 0; i<company_queue.length; i++){
            for(var j = 0; j<values.length; j++){
                if (company_queue[i] == values[j]) {
                    match = 2;
                    break;
                }
            }
        }
        if (match == 2) {
                swal("Company in queue", "Selected company requests are already in queue or generated within 15 days", "error");            
        } else {
            return 1;
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