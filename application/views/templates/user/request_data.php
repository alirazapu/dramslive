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

                <div class="tab-content">                   
                    <!-- /.tab-pane -->
                    <div class="active tab-pane" id="emailmanager"> 
                        <?php
                        $person_id = !empty($post['pid']) ? $post['pid'] : 0;

                        $request = !empty($post['request']) ? $post['request'] : "na";
                        $requesttype = !empty($post['requesttype']) ? $post['requesttype'] : 0;
                        $msisdn = !empty($post['msisdn']) ? $post['msisdn'] : 0;
                        $cnic = !empty($post['cnic']) ? $post['cnic'] : 0;
                        $imsi = !empty($post['imsi']) ? $post['imsi'] : 0;
                        $imei = !empty($post['imei']) ? $post['imei'] : 0;
                        $startdate = !empty($post['startdate']) ? $post['startdate'] : 0;
                        $enddate = !empty($post['enddate']) ? $post['enddate'] : 0;
                        $reference = !empty($post['reference']) ? $post['reference'] : 0;
                        $projectid = !empty($post['projectid']) ? $post['projectid'] : 0;
                        //  echo $msisdn;
                        ?>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Request Data</h3>
                            </div> 
                            <form class="ipf-form request_net" name="requestform" action="<?php echo url::site() . 'email/send' ?>" id="userrequest" method="post" enctype="multipart/form-data" >
                                <div class="box-body">                                    
                                    <div class="alert " id="upload" style="color: '#ff5b3c'; display: none">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <h4><i class="icon fa fa-check"></i> 
                                            <span id='parsresult'> Be Patient request in process 
                                                <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                                            </span></h4>
                                    </div>                                    
                                    <div class="form-group col-md-12 " >
                                        <div class="alert-dismissible notificationclosebasic" id="notification_msgbasic" style="display: none;">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divbasic"></div></h4>
                                        </div>
                                        <input type="hidden" value="0" id="requestformcontroler" />
                                        <input type="hidden" value="0" id="requestformcontrolererror" />
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">  

                                            <label for="ChooseTemplate" class="control-label">Request Type <?php // echo $request.",".$msisdn.",".$requesttype.",".$pid ;  ?></label> 
                                                <?php try{
                                                $rqts = Helpers_Utilities::emailtemplatetype();
                                                ?>
                                            <input type="hidden" name="person_id" value="<?php echo $person_id; ?>" />
                                            <select class="form-control" id="ChooseTemplate" name="ChooseTemplate" onchange="showDiv(this, 1)">
                                                <option value="0">Please select Request Type</option>
                                                <?php
                                                foreach ($rqts as $rqt) { ?>
                                                <option value="<?php echo $rqt['id']; ?>"> <?php echo $rqt['email_type_name'];?></option>
                                                <?php } 
                                                }  catch (Exception $ex){   }?>
                                            </select>                                                                                    
                                        </div>
                                    </div>                            
                                    <hr class="col-md-12 style14"  />
                                    <div class="col-sm-6" id="sub_div" style="display: none">
                                        <div class="form-group" style="padding-top: 15px">                                                            
                                            <label for="inputSubNO" class="control-label">Subscriber No</label>                                                                                                                        
                                            <input type="text" class="form-control" name="inputSubNO" id="inputmsisdn" value="<?php if ($msisdn != 0) {
                                                    echo $msisdn;
                                                } ?>" placeholder="e.g. 3001234567"  <?php if ($msisdn != 0) {
                                                    echo "readonly";
                                                } ?>>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" id="company_div" style="display: none">
                                        <div class="form-group"  >
                                            <label for="company_name_get" class="control-label"><span>Company Name 
                                                    <img id='findcompnay_image' src="<?php echo URL::base(); ?>dist/img/102.gif" style="width: 38px;height: 33px; display: none;">
                                                    <a id="findcompanyname"  class="btn" onclick="findcompanyname();"> Search Network (Click) </a></span></label>                                    
                                            <select readonly class="form-control select2 <?php // if($requesttype==2 OR $requesttype==5){echo "select2"; }  ?>" name="company_name_get[]" id="company_name_get" style="width: 100%;" <?php // if($requesttype==2 OR $requesttype==5){echo "multiple"; }  ?>>
                                                <option value="" >Please Select Company</option>
                                                    <?php try{
                                                    $comp_name_list = Helpers_Utilities::get_companies_data();
                                                    foreach ($comp_name_list as $list) {
                                                        ?>
                                                    <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                                    <?php } 
                                                    }  catch (Exception $ex){   }?>

                                            </select>
                                        </div>


                                    </div>
                                    <div class="col-sm-6" id="ptcldiv" style="display: none">
                                        <div class="form-group" >                                                            
                                            <label for="inputPTCLNO" class="control-label">PTCL No</label>                                                                                                                        
                                            <input type="text" class="form-control" name="inputPTCLNO" id="inputPTCLNO" placeholder="e.g. 0411234567">
                                        </div>
                                    </div>
                                    <div class="col-sm-6" id="cnic_div" style="display: none">
                                        <div class="form-group" >                                                        
                                            <label for="inputCNIC" class="control-label">CNIC</label>                                                      
                                            <input type="text" class="form-control" name="inputCNIC" id="inputCNIC" value="<?php if ($cnic != 0) {
                                                echo $cnic;
                                            } ?>" placeholder="CNIC"  <?php if ($cnic != 0) {
                                                echo "readonly";
                                            } ?>>                                                        
                                        </div>
                                    </div>
                                    <div class="col-sm-6" id="imei_div" style="display: none">
                                        <div class="form-group" >                                                        
                                            <label for="inputIMEI" class="control-label">IMEI Number</label>

                                            <input type="text" class="form-control" name="inputIMEI" id="inputIMEI" value="<?php if ($imei != 0) {
                                                echo $imei;
                                            } ?>" placeholder="IMEI Number" <?php if ($imei != 0) {
                                                echo "readonly";
                                            } ?>>
                                        </div> 
                                    </div>  
                                    <div class="col-sm-6" id="imsi_div" style="display: none">
                                        <div class="form-group" >                                                        
                                            <label for="inputIMSI" class="control-label">IMSI Number</label>                                                                                                         
                                            <input type="text" class="form-control" name="inputIMSI" id="inputIMSI" value="<?php if ($imsi != 0) {
                                                    echo $imsi;
                                                } ?>" placeholder="IMSI Number" <?php if ($imsi != 0) {
                                                    echo "readonly";
                                                } ?>>                                                        
                                        </div> 
                                    </div>
                                    <div class="col-sm-6" id="quickoption_div" style="display: none ; margin-top: 15px">
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
                                    <div class="col-sm-3" id="datefrom_div" style="display: none; margin-top: 15px">
                                        <div class="form-group" >
                                            <label for="startDate" class="control-label">Date From (mm/dd/yyyy)</label>
                                            <input type="text"  readonly="readonly" class="form-control" name="startDate" id="startDate" value="<?php if ($startdate != 0) {
                                                     echo $startdate;
                                                    } ?>" placeholder="mm/dd/yyyy" <?php if ($startdate != 0) {
                                                echo "disabled";
                                            } ?>>
                                        </div>
                                    </div>
                                    <div class="col-sm-3" id="dateto_div" style="display: none; margin-top: 15px">
                                        <div class="form-group" >
                                            <label for="endDate" class="control-label">Date To (mm/dd/yyyy)</label>
                                            <input type="text" readonly="readonly" class="form-control" name="endDate" id="endDate" value="<?php if ($enddate != 0) {echo $enddate;} ?>" placeholder="mm/dd/yyyy" <?php if ($enddate != 0) { echo "disabled";} ?>>
                                        </div>
                                    </div>
                                    <div class="col-sm-12" id="reference_div" style="display: none">
                                        <div class="form-group" >                                                            
                                            <label for="inputSubject" class="control-label">Reference</label>
                                            <textarea class="form-control" name="inputreference" id="inputreference"  placeholder="Enter References.." <?php if ($reference != 0) {
                                                echo "disabled";
                                            } ?>><?php echo $reference; ?></textarea>                                                          
                                        </div>
                                    </div>
                                    <div class="col-sm-12" id="reason_div" style="display: none;margin-top: 10px">
                                        <div class="form-group" >                                                            
                                            <label for="inputreason" class="control-label">Reason For This Request</label>
                                            <textarea class="form-control" name="inputreason" id="inputreason"  placeholder="Enter Reason For Request" ></textarea>                                                          
                                        </div>
                                    </div>
                                    <div class="form-group" id="submit_div" style="display: none">
                                        <div class="col-sm-12">
                                            <button  id="userrequestbtn" type="submit" class="btn btn-primary pull-right" style="margin-top:10px" >Submit</button>
                                        </div>
                                    </div> 
                                </div>
                            </form>
                        </div>                                            
                    </div>                    
                </div>
        </div>
    </div>

</section>
<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>
<script>
    
$('#userrequest').one('submit', function() {
    $(this).find('input[type="submit"]').attr('disabled','disabled');
});
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();
        });
</script>
<script>
    function findcompanyname() {
        var subnumber = $("#inputmsisdn").val();
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

</script>
<script type="text/javascript">
    $(document).ready(function () {

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
                inputSubNO: {
                    required: true,
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                },
                inputPTCLNO: {
                    required: true,
                    number: true,
                    maxlength: 10,
                    minlength: 10
                },
                inputCNIC: {
                    required: true,
                    number: true,
                    minlength: 13,
                    maxlength: 13
                },
                inputIMEI: {
                    required: true,
                    number: true,
                    minlength: 15,
                    maxlength: 15
                },
                inputIMSI: {
                    required: true,
                    number: true,
                    minlength: 19,
                    maxlength: 19
                },
                startDate: {
                    required: true,
                    vailddate: true
                },
                endDate: {
                    required: true,
                    greaterThan: "#startDate"
                },
                inputreference: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 20
                },
                inputreason: {
                   // required: true,
                    alphanumericspecial: true,
                    minlength: 5,
                    maxlength: 500
                }
            },
            messages: {
                ChooseTemplate: {
                    required: "Select Request Type"
                },
                company_name_get: {
                    required: "Select atleast one company"
                },
                inputproject: {
                    required: "Select atleast one project"
                },
                inputSubNO: {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },
                inputPTCLNO: {
                    required: "Enter PTCL Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    minlength: "Minimum 10 digits"
                },
                inputCNIC: {
                    required: "Enter CNIC Number",
                    Number: "Only number without dashes",
                    maxlenght: "Number should be 13 digits",
                    minlength: "Minimum 13 digits"
                },
                inputIMEI: {
                    required: "Enter IMEI Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum character limit is 15",
                    minlength: "Minimum 15 digits"
                },
                inputIMSI: {
                    required: "Enter IMSI Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum character limit is 19",
                    minlength: "Minimum 19 digits"
                },
                startDate: {
                    required: "Enter date from"
                },
                endDate: {
                    required: "Enter date to"
                },
                inputreference: {
                    required: "Enter request reference",
                    maxlenght: "Maximum character limit is 20",
                    minlength: "Min character limit is 1"
                },
                inputreason: {
                    required: "Enter reason for request",
                    maxlenght: "Maximum character limit is 500",
                    minlength: "Min character limit is 10"
                }
            },

            submitHandler: function () {
                $('#upload').show();
               var requestformcontroler=$("#requestformcontroler").val();
               var requestformcontrolererror=$("#requestformcontrolererror").val();
               if(requestformcontroler==0){
                   
               var requesttype=$("#ChooseTemplate").val();
               var msisdn=$("#inputmsisdn").val();
               var cnic=$("#inputCNIC").val();
               var imei=$("#inputIMEI").val();
               var mnc=$("#company_name_get").val();
               var startdate=$("#startDate").val();
               var enddate=$("#endDate").val();
                var result = {requesttype: requesttype,msisdn:msisdn,cnic:cnic,imei:imei,mnc:mnc,startdate:startdate,enddate:enddate}
                    //ajax to upload device informaiton
                    $.ajax({
                        url: "<?php echo URL::site("userrequest/sendrequestpermission"); ?>",
                        type: 'POST',
                        data: result,
                        cache: false,
                        success: function (msg) {
                            $('#upload').hide();                            
                            if(msg==-1){
                             $("#notification_msg_divbasic").html('You are not permitted to send request');
                            $("#notification_msgbasic").show();
                            $("#notification_msgbasic").addClass('alert');
                            $("#notification_msgbasic").addClass('alert-danger');
                            var elem = $(".notificationclosebasic");
                            elem.slideUp(10000);
                            $("#requestformcontrolererror").val(msg);
                            }else if(msg==0){
                             $("#requestformcontroler").val("1");
                             $("#requestformcontrolererror").val(msg);
                             $('#upload').show();
                           // $("#userrequest").submit();
                             $("#userrequestbtn").trigger("click");
                            }else if(msg==1){
                            $("#notification_msg_divbasic").html('Not Permitted: Either previous request is pending or current is within restricted period');
                            $("#notification_msgbasic").show();
                            $("#notification_msgbasic").addClass('alert');
                            $("#notification_msgbasic").addClass('alert-danger');
                            var elem = $(".notificationclosebasic");
                            elem.slideUp(10000);
                            $("#requestformcontrolererror").val(msg);
                            }else if(msg==2){
                            $("#notification_msg_divbasic").html('Not Permitted: Previous request is pending within last 5 days');
                            $("#notification_msgbasic").show();
                            $("#notification_msgbasic").addClass('alert');
                            $("#notification_msgbasic").addClass('alert-danger');
                            var elem = $(".notificationclosebasic");
                            elem.slideUp(10000);
                            $("#requestformcontrolererror").val(msg);
                            }else{
                            $("#notification_msg_divbasic").html(msg);
                            $("#notification_msgbasic").show();
                            $("#notification_msgbasic").addClass('alert');
                            $("#notification_msgbasic").addClass('alert-danger');
                            var elem = $(".notificationclosebasic");
                            elem.slideUp(10000);
                            $("#requestformcontrolererror").val(1);  
                            }
                            
                           
                        }
                    }); 
                    
                 }else{             
                            if(requestformcontrolererror==-1){                                
                             $('#upload').hide();           
                             $("#notification_msg_divbasic").html('You are not permitted to send request');
                            $("#notification_msgbasic").show();
                            $("#notification_msgbasic").addClass('alert');
                            $("#notification_msgbasic").addClass('alert-danger');
                            var elem = $(".notificationclosebasic");
                            elem.slideUp(10000);
                            }else if(requestformcontrolererror==1){
                                $('#upload').hide();  
                            $("#notification_msg_divbasic").html('Not Permitted: Either previous request is pending or current is within restricted period');
                            $("#notification_msgbasic").show();
                            $("#notification_msgbasic").addClass('alert');
                            $("#notification_msgbasic").addClass('alert-danger');
                            var elem = $(".notificationclosebasic");
                            elem.slideUp(10000);
                            }else{
                             $('#upload').show();  
                             $("#userrequest").submit();
                     }
                 } // condition to control duplicate
                 
            }
               
            // $('#upload').show()
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

        $.validator.addMethod("vailddated1", function (value, element) {
            var check = false,
                    re = /^\d{1,2}\/\d{1,2}\/\d{4}$/,
                    adata, gg, mm, aaaa, xdata;
            if (re.test(value)) {
                adata = value.split("/");
                gg = parseInt(adata[0], 10);
                mm = parseInt(adata[1], 10);
                aaaa = parseInt(adata[2], 10);
                xdata = new Date(aaaa, mm - 1, gg, 12, 0, 0, 0);
                if ((xdata.getUTCFullYear() === aaaa) && (xdata.getUTCMonth() === mm - 1) && (xdata.getUTCDate() === gg)) {
                    check = true;
                } else {
                    check = false;
                }
            } else {
                check = false;
            }
            return this.optional(element) || check;
        }, "Please enter a correct date");

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
        var sixmonthago = backdate(180);
        document.getElementById('endDate').value = today;               
        document.getElementById('startDate').value = sixmonthago;
    }
    function currentdate(){
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        
        if(dd<10) {
            dd = '0'+dd
        } 
        if(mm<10) {
            mm = '0'+mm
        } 
        today = mm + '/' + dd + '/' + yyyy;
        return today;
    }
    function backdate(value){
        var beforedate = new Date();
        var priordate = new Date();
        priordate.setDate(beforedate.getDate()-value);
        var dd2 = priordate.getDate();
        var mm2 = priordate.getMonth()+1;//January is 0, so always add + 1
        var yyyy2 = priordate.getFullYear();
        if(dd2<10){dd2='0'+dd2};
        if(mm2<10){mm2='0'+mm2};
        var datefrommonthago = mm2 + '/' + dd2 + '/' + yyyy2;
        
        return datefrommonthago;
    }    
    //function to show and hide dives    
    function showDiv(elem, elem1) {
        //  alert(elem.value);
        if (elem == 1 || elem.value == 1)
        {
            //Hide      
            $('#imei_div').hide();
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#ptcldiv').hide();
            //show   
            $('#company_div').show();
            $('#findcompanyname').show();
            $('#sub_div').show();
            $('#dateto_div').show();
            $('#datefrom_div').show();
            $('#quickoption_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();
        } else if (elem == 2 || elem.value == 2)
        {
            //  alert('a');
            //Hide      
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#sub_div').hide();
            $('#ptcldiv').hide();
            $('#findcompanyname').hide();
            //show   
            $('#imei_div').show();
            $('#company_div').show();
            $('#dateto_div').show();
            $('#datefrom_div').show();
            $('#quickoption_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            //to change company for multiple values
            $("#company_name_get").attr("multiple", "multiple");
            jQuery("#company_name_get").select2().select2('val', ['-1']);
        } else if (elem == 3 || elem.value == 3)
        {
            //Hide      
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#imei_div').hide();
            $('#dateto_div').hide();
            $('#datefrom_div').hide();
            $('#quickoption_div').hide();
            $('#ptcldiv').hide();
            //show    
            $('#sub_div').show();
            $('#findcompanyname').show();
            $('#company_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();
        } else if (elem == 4 || elem.value == 4)
        {
            //Hide      
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#imei_div').hide();
            $('#dateto_div').hide();
            $('#datefrom_div').hide();
            $('#quickoption_div').hide();
            $('#ptcldiv').hide();
            //show    
            $('#sub_div').show();
            $('#company_div').show();
            $('#findcompanyname').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();
        } else if (elem == 5 || elem.value == 5)
        {
            //Hide      
            $('#imsi_div').hide();
            $('#request_div').hide();
            $('#imei_div').hide();
            $('#dateto_div').hide();
            $('#datefrom_div').hide();
            $('#quickoption_div').hide();
            $('#ptcldiv').hide();
            $('#sub_div').hide();
            $('#findcompanyname').hide();
            //show
            $('#company_div').show();
            $('#cnic_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            //to change company for multiple values
            $("#company_name_get").attr("multiple", "multiple");
            jQuery("#company_name_get").select2().select2('val', ['-1']);
        } else if (elem == 6 || elem.value == 6)
        {
            //Hide      
            $('#imei_div').hide();
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#ptcldiv').hide();
            //show   
            $('#company_div').show();
            $('#sub_div').show();
            $('#dateto_div').show();
            $('#datefrom_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();
        } else if (elem == 7 || elem.value == 7)
        {
            //Hide      
            $('#request_div').hide();
            $('#company_div').hide();
            $('#sub_div').hide();
            $('#cnic_div').hide();
            $('#imsi_div').hide();
            $('#imei_div').hide();
            $('#findcompanyname').hide();
            //show   
            $('#ptcldiv').show();
            $('#dateto_div').show();
            $('#datefrom_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();

        } else if (elem == 0 || elem.value == 0)
        {
            //Hide      
            $('#imei_div').hide();
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#company_div').hide();
            $('#sub_div').hide();
            $('#dateto_div').hide();
            $('#datefrom_div').hide();
            $('#quickoption_div').hide();
            $('#submit_div').hide();
            $('#ptcldiv').hide();
            $('#reason_div').hide();
            $('#project_div').hide();
        }
        if (elem1 == 1 || elem1.value == 1 && changecount > 1)
        {
            $('#userrequest').find("input[type=text], textarea").val("");
            $('#ChooseTemplate').val(elem.value);
            changecount = changecount + 1;
        }
    }
</script>