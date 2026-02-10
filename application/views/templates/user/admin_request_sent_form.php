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
       Single Requests
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
            $request = !empty($post['request']) ? $post['request'] : "";
            $requesttype = !empty($post['requesttype']) ? $post['requesttype'] : 0;
            $imeinumber = !empty($post['imei']) ? $post['imei'] : 0;
            $msisdn = !empty($post['msisdn']) ? $post['msisdn'] : 0;
            $cnic = !empty($post['cnic']) ? $post['cnic'] : 0;
            $redirect_url = !empty($post['url']) ? $post['url'] : URL::site('userdashboard/dashboard');

            ?>
            <div class="box box-primary">
                <div id="headerdiv" class="box-header with-border">
                    <h3 class="box-title">Request</h3>
                    <a href="<?php echo $redirect_url;?>" class="btn btn-warning btn-small" style="float: right;"><i class="fa fa-backward"></i> Go Back</a>
                </div>
                <form class="ipf-form request_net" name="requestform" id="userrequest" method="post" enctype="multipart/form-data" >
                    <div class="box-body">
                        <div class="alert " id="upload" style="color: '#ff5b3c'; display: none">
                                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i>
                                <span id='parsresult'> Be Patient request in process
                                    <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php //echo URL::base() . 'dist/img/103.gif'; ?>">
                                </span></h4>
                        </div>

                        <input type="hidden" name="person_id" value="<?php echo $person_id; ?>" />
                        <input type="hidden" name="redirect_url" id="redirect_url" value="<?php echo $redirect_url; ?>" />

                <!--<div id="dataform">-->
                    <div class="col-sm-6">
                    <div class="form-group" >
                            <label for="field">Please select request type</label>
                            <select class="form-control " name="ChooseTemplate"  id='field'  onchange="request_against(this)">
                                <option value=""> Please Select Type</option>
                                <option value="3"> Subscriber Against Mobile Number</option>
                                <option value="4"> Location Against Mobile Number</option>
                                <option value="1"> CDR Against Mobile Number</option>
                                <option value="2"> CDR Against IMEI Number </option>
                                <option value="5"> SIM's Against CNIC Number</option>
                            </select>
                        </div>
                        </div>
                            <div class="col-sm-6" id="mob_div" style="display: none">
                                <div class="form-group" >
                                    <label for="inputSubNO" class="control-label">Mobile Number</label>
                                    <input type="text" class="form-control" name="inputSubNO" id="inputSubNO" value="<?php echo ($msisdn != 0) ? $msisdn : ''; ?>" placeholder="e.g. 3001234567" >
                                </div>
                            </div>

                            <div class="col-sm-6" id="cnic_div" style="display: none">
                                <div class="form-group">
                                    <label for="inputCNIC" class="control-label">CNIC Number</label>
                                    <input type="text" class="form-control" name="inputCNIC" id="inputCNIC" value="<?php echo ($cnic != 0) ? $cnic : '';?>" >
                                </div>
                            </div>

                            <div class="col-sm-6" id="imei_div" style="display: none">
                                <div class="form-group" >
                                    <label for="inputIMEI" class="control-label">IMEI Number</label>
                                    <input type="text" class="form-control" name="inputIMEI" id="inputIMEI" value="<?php echo ($imeinumber != 0) ? $imeinumber : '';?>" >
                                </div>
                            </div>
                            <div style="display:none">
                                <?php (!empty($msisdn) && $msisdn != 0) ? $company_mnc_tel = Helpers_Utilities::search_mnc_ofmobile($msisdn) : $company_mnc_tel = ''; ?>
                            </div>
                            <div class="col-sm-12" id="company_div" style="display: none " width="50"  >
                                <div class="form-group"  style="width: 48%">
                                    <label for="company_name_get" class="control-label">Company Name </label>
                                    <select class="form-control select2" multiple="multiple" name="company_name_get[]" id="company_name_get" data-placeholder="Select company from list"  style="width: 100%">
                                        <?php $comp_name_list = Helpers_Utilities::get_companies_data();
                                        foreach ($comp_name_list as $list) {
                                            if ($list->company_id < 6 || $list->company_id==9) {
                                                ?>
                                                <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                            <?php }} ?>
                                                <option value="11">PTCL</option>
                                                <option value="12">International</option>
                                                
                                    </select>
                                </div>


                            </div>

                            <div class="col-sm-6" id="quickoption_div" style="display: none" >
                                <div class="form-group" >
                                    <label for="quickoption" class="control-label">Quick Options (For Start and End Date)</label>
                                    <div class="col-md-12" id="quickoption">
                                        <button type="button" onclick="dateonemonth()" class="btn btn-primary" >Last 30 Days </button>
                                        <button type="button" onclick="datetwomonths()" class="btn btn-primary" >Last 60 Days </button>
                                        <button type="button" onclick="datethreemonths()" class="btn btn-primary" >Last 90 Days </button>
                                        <button type="button" onclick="datesixmonths()" class="btn btn-primary" >Last 180 Days</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3" id="datefrom_div" style="display: none">
                                <div class="form-group" >
                                    <label for="startDate" class="control-label">Date From (mm/dd/yyyy)</label>
                                    <input type="text"  readonly="readonly" class="form-control" name="startDate" id="startDate" value="" placeholder="mm/dd/yyyy">
                                </div>
                            </div>
                            <div class="col-sm-3" id="dateto_div" style="display: none">
                                <div class="form-group" >
                                    <label for="endDate" class="control-label">Date To (mm/dd/yyyy)</label>
                                    <input type="text" readonly="readonly" class="form-control" name="endDate" id="endDate" value="" placeholder="mm/dd/yyyy">
                                </div>
                            </div>

                            <div class="col-sm-3" id="reason_div">
                                <div class="form-group" >
                                    <label for="inputreason" class="control-label">Requested By</label>
                                      <input type="text"   class="form-control" name="rqtbyname" id="rqtbyname" value="" placeholder="Name">
                                      <div id="rqtbynamelist"></div>
                                </div>
                            </div>
                            <div class="col-sm-3" id="reason_div">
                                <div class="form-group" >
                                    <label for="inputreason" class="control-label">Requested Attachment</label>
                                     <input type="file" accept=".jpeg,.jpg,.gif,.png" id="rqtfile" name="rqtfile" placeholder="Select Image">  
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
                        <!--</div>-->
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

    //additional changes
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
$("#rqtbyname").on("keyup", function(){
        var rqtbyname = $(this).val();
        if (rqtbyname !=="") {
          $.ajax({
            url:"<?php echo URL::site("adminrequest/autocomplete"); ?>",
            type:"POST",
            cache:false,
            data:{rqtbyname:rqtbyname},
            success:function(data){
              $("#rqtbynamelist").html(data);
              $("#rqtbynamelist").fadeIn();
            }  
          });
        }else{
          $("#rqtbynamelist").html("");  
          $("#rqtbynamelist").fadeOut();
        }
    });
$(document).on("click","li", function(){
        $('#rqtbyname').val($(this).text());
        $('#rqtbynamelist').fadeOut("fast");
      });


        $('#dataform').hide();
        //subscriber
        var redirect_url = $("#redirect_url").val();
        var inputSubNO = $("#inputSubNO").val();
        var result = {redirect_url: redirect_url, inputSubNO: inputSubNO};
//subscriber ajax
//        $.ajax({
//            url: "<?php echo URL::site("adminrequest/adminsubrequestpermission"); ?>",
//            type: 'POST',
//            data: result,
//            cache: false,
//            success: function (result) {
//                if (result == 2)
//                {
//                    swal("System Error", "Contact Support Team.", "error");
//                } else {
//                    var result = JSON.parse(result);
//                    var request_permission = result.permission;
//                    var request_message = result.message;
//                    $('#request_permission_check_status').hide();
//                    switch (request_permission) {
//                        case 0:
//                            $('#dataform').show();
//                            break;
//                        case 1:
//                        case 2:
//                            $("#notification_msg_divbasic1").html(request_message);
//                            $("#notification_msgbasic1").show();
//                            $("#notification_msgbasic1").addClass('alert');
//                            $("#notification_msgbasic1").addClass('alert-danger');
//                            break;
//                    }
//                }
//            }
//        });
//mob number
//        var msisdn = $("#inputmsisdn").val();
//        var result = {requesttype: requesttype, msisdn: msisdn}
        //ajax to upload device informaiton
//        $.ajax({
//            url: "<?php echo URL::site("adminrequest/admincdrrequestpermission"); ?>",
//            type: 'POST',
//            data: result,
//            cache: false,
//            success: function (result) {
//                if (result == 2)
//                {
//                    swal("System Error", "Contact Support Team.", "error");
//                }else{
//                    var result = JSON.parse(result);
//                    var request_permission = result.permission;
//                    var request_message = result.message;
//                    var request_startdate = result.startdate;
//                    var request_enddate = result.enddate;
//                    $('#request_permission_check_status').hide();
//                    switch(request_permission){
//                        case 0:
//                            $('#dataform').show();
//                            break;
//                        case 1:
//                            $("#notification_msg_divbasic1").html(request_message);
//                            $("#notification_msgbasic1").show();
//                            $("#notification_msgbasic1").addClass('alert');
//                            $("#notification_msgbasic1").addClass('alert-danger');
//                            break;
//                        case 2:
//                            $("#startdate_duration").val(request_startdate);
//                            $("#enddate_duration").val(request_enddate);
//                            $("#notification_msg_divbasic1").html(request_message);
//                            $("#notification_msgbasic1").show();
//                            $("#notification_msgbasic1").addClass('alert');
//                            $("#notification_msgbasic1").addClass('alert-danger');
//                            $('#dataform').show();
//                            break;
//                    }
//                }
//            }
//        });
//cnic
//        var cnic = $("#inputCNIC").val();
//        var result = {cnic: cnic}
//        //ajax to upload device informaiton
//        $.ajax({
//            url: "<?php echo URL::site("adminrequest/admincnicsimspermission"); ?>",
//            type: 'POST',
//            data: result,
//            cache: false,
//            success: function (result) {
//                if (result == 2)
//                {
//                    swal("System Error", "Contact Support Team.", "error");
//                }else{
//                    var result = JSON.parse(result);
//                    var request_permission = result.permission;
//                    var request_message = result.message;
//                    var request_mnc = result.mnc;
//                    var array_mnc = request_mnc.split(',');
//                    company_queue  = array_mnc;
//                    $('#request_permission_check_status').hide();
//                    switch(request_permission){
//                        case 0:
//                            $('#dataform').show();
//                            break;
//                        case 2:
//                            for(var i = 0; i<array_mnc.length; i++){
//                                var span = document.createElement('span');
//                                span.className = 'label label-primary';
//                                span.innerHTML = compnay_name(array_mnc[i]);
//                                span.style.margin = '2px';
//                                var company_list = document.getElementById('company_list');
//                                company_list.appendChild(span);
//                            }
//                            //$("#company_inqueue").html(request_mnc);
//                            $("#company_inqueue0").show();
//                            $("#company_inqueue").show();
//                            $("#company_inqueue").addClass('alert');
//                            $("#company_inqueue").addClass('alert-danger');
//                            $('#dataform').show();
//                            break;
//                    }
//                }
//            }
//        });
//location

       // var msisdn = $("#inputmsisdn").val();
//        var result = {requesttype: requesttype, msisdn: msisdn}
//        //ajax to upload device informaiton
//        $.ajax({
//            url: "<?php echo URL::site("adminrequest/adminlocrequestpermission"); ?>",
//            type: 'POST',
//            data: result,
//            cache: false,
//            success: function (result) {
//                if (result == 2)
//                {
//                    swal("System Error", "Contact Support Team.", "error");
//                } else {
//                    var result = JSON.parse(result);
//                    var request_permission = result.permission;
//                    var request_message = result.message;
//                    $('#request_permission_check_status').hide();
//                    switch (request_permission) {
//                        case 0:
//                            $('#dataform').show();
//                            break;
//                        case 1:
//                            $("#notification_msg_divbasic1").html(request_message);
//                            $("#notification_msgbasic1").show();
//                            $("#notification_msgbasic1").addClass('alert');
//                            $("#notification_msgbasic1").addClass('alert-danger');
//                            break;
//                    }
//                }
//            }
//        });
////imei
//        var requesttype = $("#ChooseTemplate").val();
//        var imei = $("#inputIMEI").val();
//        var result = {requesttype: requesttype, imei: imei}
//        //ajax to upload device informaiton
//        $.ajax({
//            url: "<?php echo URL::site("adminrequest/adminimeirequestpermission"); ?>",
//            type: 'POST',
//            data: result,
//            cache: false,
//            success: function (result) {
//                if (result == 2)
//                {
//                    swal("System Error", "Contact Support Team.", "error");
//                }else{
//                    var result = JSON.parse(result);
//                    var request_permission = result.permission;
//                    var request_message = result.message;
//                    var request_startdate = result.startdate;
//                    var request_enddate = result.enddate;
//                    var request_mnc = result.mnc;
//                    var array_mnc = request_mnc.split(',');
//                    company_queue  = array_mnc;
//                    $('#request_permission_check_status').hide();
//                    switch(request_permission){
//                        case 0:
//                            $('#dataform').show();
//                            break;
//                        case 2:
//                            $("#startdate_duration").val(request_startdate);
//                            $("#enddate_duration").val(request_enddate);
//                            $("#notification_msg_divbasic1").html(request_message);
//                            $("#notification_msgbasic0").show();
//                            $("#notification_msgbasic1").show();
//                            $("#notification_msgbasic1").addClass('alert');
//                            $("#notification_msgbasic1").addClass('alert-danger');
//
//                            for(var i = 0; i<array_mnc.length; i++){
//                                var span = document.createElement('span');
//                                span.className = 'label label-primary';
//                                span.innerHTML = compnay_name(array_mnc[i]);
//                                span.style.margin = '2px';
//                                var company_list = document.getElementById('company_list');
//                                company_list.appendChild(span);
//                            }
//                            //$("#company_inqueue").html(request_mnc);
//                            $("#company_inqueue0").show();
//                            $("#company_inqueue").show();
//                            $("#company_inqueue").addClass('alert');
//                            $("#company_inqueue").addClass('alert-danger');
//                            $('#dataform').show();
//                            break;
//                    }
//                }
//            }
//        });

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
                case '8':
                    company =  'SCOM';
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
                "company_name_get": {
                    required: true,
                },
                "inputproject": {
                    required: true,
                    check_list: true
                },
                inputSubNO: {
                    required: true,
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                },
                inputIMEI: {
                    required: true,
                    number: true,
                    minlength: 15,
                    maxlength: 15
                },
                inputCNIC: {
                    required: true,
                    number: true,
                    minlength: 13,
                    maxlength: 13
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
                inputSubNO: {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },

                inputIMEI: {
                },
                startDate: {
                    required: "Enter date from"
                },
                endDate: {
                    required: "Enter date to"
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
        var data = new FormData();
        var form_data = $('#userrequest').serializeArray();
        $.each(form_data, function (key, input) {
            data.append(input.name, input.value);
        });
        var files = $('#rqtfile')[0].files;
       data.append('file', files[0]);
        var url = $("#redirect_url").val();
        if ($('#userrequest').valid())
        {
                $("#preloader").show();
                $.ajax({
                    type: 'POST',
                    url: "<?php echo URL::site('adminrequest/adminsend') ?>",
                    data: data,
                     cache:false,
                    contentType: false,
                    processData: false,
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
                                        window.location.href = '<?php echo URL::site('Adminrequest/admin_sent_request_status', TRUE); ?>';
                                    } else {
                                        window.location.href = url;
                                    }
                                });
                        } else {
                            if (result == 5) {
                                swal("Email Limit Exceeded", "Contact AIES Support Team.", "error");
                            }else{
                                swal("System Error", "Contact AIES Support Team.", "error");
                                }
                          // window.location.reload(false);
                        }
                    },
                    error: function (data) {
                        console.log("error");
                        console.log(result);
                    }
                });
        }
    }

    //function to check duration
//    function duration_check() {
//        //start date and end date from form
//        var startdate = $("#startDate").val();
//        var enddate = $("#endDate").val();
//        //start date and end date from database
//        var cdr_startdate = $("#startdate_duration").val();
//        var cdr_enddate = $("#enddate_duration").val();
//        if (cdr_startdate != '' && cdr_enddate != '') {
//            if ((toTimestamp(startdate) > toTimestamp(cdr_enddate)) && (toTimestamp(enddate) > toTimestamp(cdr_enddate))) {
//                return 1;
//            } else if ((toTimestamp(startdate) < toTimestamp(cdr_startdate)) && (toTimestamp(enddate) < toTimestamp(cdr_startdate))) {
//                return 1;
//            } else {
//                swal("Date Error", "Selected dates fall in prohibited duration", "error");
//            }
//        } else {
//            return 1;
//        }
//    }
    //function to check company is queue lsit
//   function in_queue_check() {
//        var values = $('#company_name_get').val();
//      var match = 0;
//       for(var i = 0; i<company_queue.length; i++){
//        for(var j = 0; j<values.length; j++){
//               if (company_queue[i] == values[j]) {
//                  match = 2;
//                  break;
//              }
//            }
//       }
//        if (match == 2) {
//           swal("Company in queue", "Selected company requests are already in queue", "error");
//      } else {
//          return 1;
//       }
//    }
    //subscriber number
    function findcompanyname() {
        var subnumber = $("#inputSubNO").val();
        if (subnumber == '')
        {
            alert('Subscriber Number is empty');
        } else {

            $("#findcompnay_image").show();
            $("#findcompanyname").html('');
            var request = $.ajax({
                url: "<?php echo URL::site("upload/checkcompany"); ?>",
                type: "POST",
                dataType: 'text',
                data: {number: subnumber},
                success: function (responseTex)
                {
                    if (responseTex == 2)
                    {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    if (responseTex == -1)
                    {
                        alert('Failed to recognize');
                        $("#company_name_get").attr("readonly", false);

                    } else {
                        $("#findcompnay_image").hide();
                        $("#company_name_get").val(responseTex).trigger('change');
                        // $("#company_name_get").val(responseTex);
                    }
                    $("#findcompanyname").html('Again Search Network');
                    //$("#findcompanyname").css('pointer-events', 'none');
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize, Please Select Manually');
                    $("#company_name_get").attr("readonly", false);
                    $("#findcompanyname").html('Search Network (Click)');
                }
            });
        }
    }
    
    
    function request_against(val=null) {
        if (val == null || val.value == '3') {
            $('#mob_div').show();
            $('#cnic_div').hide();
            $('#imei_div').hide();
            $('#company_div').show();
            $('#quickoption_div').hide();
            $('#datefrom_div').hide();
            $('#dateto_div').hide();

        } else if (val.value == '1') {
            $('#mob_div').show();
            $('#cnic_div').hide();
            $('#imei_div').hide();
            $('#company_div').show();
            $('#quickoption_div').show();
            $('#datefrom_div').show();
            $('#dateto_div').show();

        } else if (val.value == '4') {
            $('#mob_div').show();
           $('#cnic_div').hide();
           $('#imei_div').hide();
           $('#company_div').show();
            $('#quickoption_div').hide();
            $('#datefrom_div').hide();
            $('#dateto_div').hide();

        } else if (val.value == '2') {
           $('#mob_div').hide();
           $('#cnic_div').hide();
            $('#imei_div').show();
            $('#company_div').show();
            $('#quickoption_div').show();
            $('#datefrom_div').show();
            $('#dateto_div').show();

        }
         else if(val.value=='5'){
             $('#mob_div').hide();
             $('#cnic_div').show();
             $('#imei_div').hide();
             $('#company_div').show();
            $('#quickoption_div').hide();
            $('#datefrom_div').hide();
            $('#dateto_div').hide();
         }
        else {
            $('#mob_div').hide();
            $('#cnic_div').hide();
            $('#imei_div').hide();
            $('#company_div').hide();
            $('#quickoption_div').hide();
            $('#datefrom_div').hide();
            $('#dateto_div').hide();
        }
    }

$('#field').change(function() {
    $('#inputSubNO').val('')
    $('#inputreason').val('')
    $('#inputIMEI').val('')
    $('#inputCNIC').val('')
    $('#startDate').val('')
    $('#endDate').val('')
    $('#company_name_get').val('').change();
    }); 

//   $("#field").on("change", function(){ 
//        $("#company_name_get").val("");
//    });
</script>
<style>
#rqtbynamelist ul.list-unstyled {
    background-color: #def;
    padding: 10px;
    cursor: pointer;
}
</style>


