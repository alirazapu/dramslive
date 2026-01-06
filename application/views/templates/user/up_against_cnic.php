<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    $post_request_id=!empty($post_data['requestid']) ? $post_data['requestid'] : '';
    $post_company_mnc=!empty($post_data['mnc']) ? $post_data['mnc'] : '';
    $post_request_type_id=!empty($post_data['requesttype']) ? $post_data['requesttype'] : '';
    $post_request_value=!empty($post_data['requestvalue']) ? $post_data['requestvalue'] : '';
    $post_recieved_file_path=!empty($post_data['receivedfilepath']) ? $post_data['receivedfilepath'] : '';
    $post_recieved_body=!empty($post_data['receivedbody']) ? $post_data['receivedbody'] : '';
    $post_recieved_body_raw=!empty($post_data['receivedbodyraw']) ? $post_data['receivedbodyraw'] : '';
    $post_country=!empty($post_data['is_foreigner']) ? $post_data['is_foreigner'] : '';
    
    
//print_r($post_data); exit;
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-upload"></i>Data Upload (CNIC#)
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Data Upload Against CNIC</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">  
                <div style="display:none;" id="custom-form"></div>
                
                <?php
                if (isset($_GET["message"]) && $_GET["message"] == 1) {
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> Congratulation! Information Successfully Added </h4>
                       <?php if (isset($_GET["pid"]) ) { ?>
                        <h4><i class="icon fa fa-check"></i> Click! <?php echo '<a style="color:red" target="blank"  href="' . URL::site('persons/dashboard/?id='. $_GET["pid"]) . '" ) >  View Person </a>'; ?></h4>
                        <?php } ?>
                    </div>
                <?php } ?>

                <div class="">
                    <!-- left column -->
                    <div class="">
                        <!-- general form elements -->
                        <div class="">
                            <div class="box box-primary">
                                
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
                                    
                                    <!-- Check  CNIC -->
                                    <div id="cnic_div" style="display: block">
                                        <!-- form start -->
                                        <form name="cnic_form" id="cnic_form" class="cnic_form" action="" method="post" enctype="multipart/form-data">
<!--                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="CnicForeignerRadio1" class="control-label">Country</label>
                                                    <div class="radio">                                            
                                                        <label>
                                                            <input type="radio" name="cnic_is_foreigner" id="CnicForeignerRadio1" value="0" checked="" >
                                                            Pakistani 
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="cnic_is_foreigner" id="CnicForeignerRadio2" value="1" >
                                                            Other
                                                        </label>
                                                    </div>
                                                </div>
                                            </div> -->
                                            <div class="form-group col-sm-6" >
                                                <label for="cnic_is_foreigner" class="control-label">Country</label>
                                                <select <?php if ($post_country==0 OR $post_country==1) { echo "readonly";    }  ?> class="form-control" name="cnic_is_foreigner" id="cnic_is_foreigner">
                                                    <option  <?php if ($post_country == 0) { echo "selected";  } ?> value="0">Pakistan</option>
                                                    <option  <?php if ($post_country == 1) {  echo "selected";   }  ?> value="1">Foreign</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <div class="row">
                                                    <div class="col-sm-10">
                                                        <label style="margin-left: -15px">Enter CNIC #</label>
                                                            <input <?php if(!empty($post_request_value)){ echo "readonly"; } ?> style="margin-left: -15px; " type="text" name="cnic_no" id="cnic_no" class="form-control">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div id="continues_btn">
                                                                <div class="form-group col-sm-12">
                                                                    <button type="submit" onclick="cnic();"  style="margin-top: 25px; margin-left: -20px"  class="cnic_submit_btn btn btn-primary pull-left"  >Continue</button>                                                                    
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


                             
                <!-- /.box-body -->
            </div>

        </div>
    </div>
    <!--/.col (left) -->
    
    <!-- Manual SIMs against CNIC Entry -->
    <div id="manualcnicsimsentry" style="display: none;">

        <div class="box box-primary">            
            <div class="box-header with-border">
                <h3 class="box-title">Manual SIMs Entry Against CNIC# </h3>
            </div>                            
            <div class="box-body">
                <form role="form" action="<?php echo url::site() . 'user/cnic_upload_post' ?>" id="manualcnicform" name="manualcnicform" method="post">
                <input type="hidden" id="requestid" name="requestid" value="<?php echo $post_request_id; ?>" />                    
                <input type="hidden" id="cnic_is_foreigner_value" name="cnic_is_foreigner_value" value="" />                    
                    <div class="row">
                        <?php if(!empty($post_data)){ ?>
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
                                        <?php } ?>
                        <div class="col-sm-6" id="cnicdetails">
                        </div>
                        <div class="col-sm-6">
                            <input name="cnicsims" type="hidden" class="form-control" id="cnicsims" placeholder="Without Dashes" readonly="">
                            
                            <div class="col-sm-12" id="sim1" style="display: block">
                                <div class="form-group" >
                                    <span><label for="inputSubNO1" class="control-label">SIM-1</label><span id="SubNO1" style="color:red"  >  </span></span>
                                    <input onchange="check_msisdn_exist(this,1)"  name="mobile_number[]" type="text" class="form-control" id="inputSubNO1" placeholder="Subscriber Number" /> 
                                </div>
                            </div>
                            <div class="col-sm-12" id="sim2" style="display: none">
                                <div class="form-group">
                                    <span> <label for="inputSubNO2" class="control-label">SIM-2</label><b id="SubNO2" style="color:red"  >  </b></span>
                                    <input onchange="check_msisdn_exist(this,2)"  name="mobile_number[]" type="text" class="form-control" id="inputSubNO2" placeholder="Subscriber Number" /> 
                                </div>
                            </div>
                            <div class="col-sm-12" id="sim3" style="display: none">
                                <div class="form-group">
                                    <span><label for="inputSubNO3" class="control-label">SIM-3</label><b id="SubNO3" style="color:red"  >  </b></span>
                                    <input onchange="check_msisdn_exist(this,3)"  name="mobile_number[]" type="text" class="form-control" id="inputSubNO3" placeholder="Subscriber Number"/> 
                                </div>
                            </div>
                            <div class="col-sm-12" id="sim4" style="display: none">
                                <div class="form-group">
                                    <span><label for="inputSubNO4" class="control-label">SIM-4</label><b id="SubNO4" style="color:red"  >  </b></span>
                                    <input onchange="check_msisdn_exist(this,4)"  name="mobile_number[]" type="text" class="form-control" id="inputSubNO4" placeholder="Subscriber Number" /> 
                                </div>
                            </div>
                            <div class="col-sm-12" id="sim5" style="display: none">
                                <div class="form-group">
                                    <span><label for="inputSubNO5" class="control-label">SIM-5</label><b id="SubNO5" style="color:red"  >  </b></span>
                                    <input onchange="check_msisdn_exist(this,5)"  name="mobile_number[]" type="text" class="form-control" id="inputSubNO5" placeholder="Subscriber Number" /> 
                                </div>
                            </div>
                            <div class="form-group col-sm-12">    
                               <div id="display_sims_against_cnic_btn_proceed" style="display:block" >
                                    <button type="button" name="simcontrol" id="simcontrol"  class="btn btn-primary" onclick="addmoresim();" style="margin-top:10px">More SIM</button>  
                                    <button  type="button" onclick="display_sims_against_cnic_btn();" class="btn btn-primary pull-right" style="margin-top:10px">Proceed</button>                                                        
                                </div>
                                <div id="display_sims_against_cnic_btn" style="display:none">                                                       
                                    <button  type="submit" onclick="cnic();" class="btn btn-primary pull-right" style="margin-top:10px">Submit</button>      
                                    <button  type="button" onclick="display_sims_against_cnic_btn();" class="btn btn-primary pull-right" style="margin-top:10px; margin-right: 5px">Edit</button>                                                   
                                </div>
                                </div>
                        </div>

                    </div>
                </form>
            </div>
        <!-- /sims against cnic record table -->
                                        <div id="cnicrecordtable" style="display: block">
                                            
                                        <hr class="style14 col-md-12"> 
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                                <div class="">
                                                    <table id="snicsimstable" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>SIM#</th>
                                                                <th title="SIM activation on">Activated At</th>
                                                                <th title="SIM last used on">Last Used At</th>
                                                                <th>Status</th>
                                                                <th >Company</th>
                                                                <th title="Current User Of This SIM">SIM User</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>

                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>SIM#</th>
                                                                <th title="SIM activation on">Activated At</th>
                                                                <th title="SIM last used on">Last Used At</th>
                                                                <th>Status</th>
                                                                <th >Company</th>
                                                                <th title="Current User Of This SIM">SIM User</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr class="style14 col-md-12"> 
    </div> 
    </div>


</section>
<div class="modal modal-info fade" id="external_search_model">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>

                <!--searching data form external sources-->
                <div id="externa_search_results_div" style="display: block;">                                                            
                    <div class="col-md-12" style="background-color: #fff;color: black"> 

                        <div class="col-sm-12">
                            <div class="form-group">                                                                                
                                <label   for="external_search_key" class="control-label">Mobile No:
                                </label>
                                <input name="external_search_key" type="text" class="form-control" id="external_search_key" placeholder="Search Key" readonly >
                            </div>

                            <div class="col-sm-12" id="external_search_results" style="display: block">   

                                <div style='background-image: url("<?php echo URL::base(); ?>dist/img/loader.gif"); width:100%; height:11px; background-position:center; display:block'><label    class="control-label" style="alignment-adjust: central">Searching Number Details
                                    </label></div>  
                                <hr class="style14 ">
                            </div>
                        </div>                                                            
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" style="margin-top: 10px" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>

            </div>  
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->

</div>


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
                $("#manualcnicform").validate({
            rules: {
                'mobile_number[]': {
                    required: true,
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                },
            },
            messages: {
                'mobile_number[]': {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },

            },

        });
        
        
        

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

    

    //function to add more sims
    var simscount = 1;
    var simscount1 = 1;
    function addmoresim() {
        // function to control button count  
        if (simscount1 == 1) {
            simscount = simscount + 1;
            if (simscount == 6) {
                simscount1 = 2;
            }
        }
        if (simscount1 == 2) {
            if (simscount > 1) {
                if (simscount == 6) {
                    simscount = simscount - 2;
                } else {
                    simscount = simscount - 1;
                }
            } else {
                simscount1 = 1;
                simscount = simscount + 1;
            }
        }
        if (simscount == 5) {
            $('#simcontrol').html('Less SIM');
        }
        if (simscount == 1) {
            $('#simcontrol').html('More SIM');
        }
        //code to control button
        if (simscount > 1)
        {
            $('#sim2').show();
        } else {
            $('#sim2').hide();
            $('#inputSubNO2').val('');
             $("#SubNO2").html('');  
        }

        if (simscount > 2)
        {
            $('#sim3').show();
        } else {
            $('#sim3').hide();
            $('#inputSubNO3').val('');
            $("#SubNO3").html(''); 
        }
        if (simscount > 3)
        {
            $('#sim4').show();
        } else {
            $('#sim4').hide();
            $('#inputSubNO4').val('');
            $("#SubNO4").html(''); 
        }
        if (simscount > 4)
        {
            $('#sim5').show();
        } else {
            $('#sim5').hide();
            $('#inputSubNO5').val('');
            $("#SubNO5").html(''); 
        }
    }

    

    //Date picker
    $('#datetime').datepicker({
        autoclose: true
    });
     $("#usefrom").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#usefrom1").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#usefrom2").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#usefrom3").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#usefrom4").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#useto").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#useto1").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#useto2").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#useto3").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#useto4").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
    
    /*$('#locdate').datepicker({
        autoclose: true
    });*/
jQuery.validator.addMethod("custominput", function(value, element, params) {
  //return this.optional(element) || value == params[0] + params[1];
  if(jQuery("#cnic_is_foreigner").val()==0)
  {      
      return ($.isNumeric($(element).val()));
  }else{
      if(jQuery.type( $(element).val() ) === "string")
          return true;
      else 
          return false;
  }
}, jQuery.validator.format("Please enter the correct value"));

// Validators alphanumericspecial
        jQuery.validator.addMethod("alphanumeric", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9]+$/);
        }, "Only letters, Numbers & Space/underscore Allowed.");

    /* For cnic */
    function cnic() {
        var form1 = $('.cnic_form');
        form1.validate({
            rules: {
                cnic_no: {
                    required: true,
                    custominput: true,                    
                    alphanumeric: true,
                    maxlength: 13,
                    minlength: 13
                },
            },
            messages: {
                cnic_no: {
                    required: "Enter CNIC Number",
                    custominput: "Enter valid input",
                    alphanumeric: "Only Alphanumeric",
                    maxlenght: "Maximum 13 digits",
                    minlength: "Minimum 13 digits"
                },
            },

            submitHandler: function () {
                $("#continues_btn").hide();
                $("#continues_btn1").show();
                $("#cnic_no").attr("readonly",true);
                $("#cnic_is_foreigner").attr("readonly",true);
                // alert($('#email_sub').val());
                var cnic_no = $("#cnic_no").val();
                var cnic_is_foreigner = $("#cnic_is_foreigner").val();
               // var cnic_is_foreigner=document.querySelector('input[name="cnic_is_foreigner"]:checked').value;
              
                $("#manualcnicsimsentry").show();
                $("#cnicsims").val($("#cnic_no").val());
                $("#cnic_is_foreigner_value").val(cnic_is_foreigner);
                //ajax call to get cnic details
                var request = $.ajax({
                    url: "<?php echo URL::site("upload/checkcnicdetails"); ?>",
                    type: "POST",
                    dataType: 'text',
                    data: {cnic_no: cnic_no,cnic_is_foreigner:cnic_is_foreigner},
                    success: function (cnicdetails)
                    { 
                      if(cnicdetails== 2){
              swal("System Error", "Contact Support Team.", "error");
          }        
                         $("#cnicdetails").html(cnicdetails);
                        },
                    
                });
               cnicsimstable(); 
            }
        });


    }

    /* For CNIC SIMS Details */
    function cnicsimstable() {
      var cnicno = $("#cnic_no").val();  
      if (typeof objDT != 'undefined')
                {  //objDT.destroy(); 

                    var newUrl = "<?php echo URL::site('personsreports/ajaxcnicsimsdetails/', TRUE); ?>" + "/" + cnicno;

                    objDT.fnReloadAjax(newUrl);

                    //refreshGrid();
                } else {

                    objDT = $('#snicsimstable').dataTable(
                            {//"aaSorting": [[2, "desc"]],
                                "bPaginate": true,
                                "bProcessing": true,
                                //"bStateSave": true,
                                "bServerSide": true,
                                "sAjaxSource": "<?php echo URL::site('personsreports/ajaxcnicsimsdetails/', TRUE); ?>" + "/" + cnicno,
                                "sPaginationType": "full_numbers",
                                "bFilter": true,
                                "bLengthChange": true,
                                "oLanguage": {
                                    "sProcessing": "Loading..."
                                },
                                retrieve: true,
                                "columnDefs": [{
                                        "targets": 'no-sort',
                                        "orderable": false,
                                    }]
                            }
                    );

                    $('.dataTables_empty').html("Information not found");
                }  
    }
    

    /* For to open manual cnic entry */
    function openmanualcnicsimsentry() {
        $("#cnicsims").val($("#cnic_no").val());
        $("#cnic_div").hide();
        $("#manualcnicsimsentry").show();
    }

  

    //edit msisdn no
    function edit_continues_btn() {
        $("#continues_btn").show();
        $("#continues_btn1").hide();
        $("#cnic_no").attr("readonly", false);
        $("#cnic_is_foreigner").attr("readonly", false);
        $("#manualcnicsimsentry").hide();
    }
   function showDiv(elem) {
        // alert(elem);
        $('#tabmessage').hide();
        if (elem == 2 || elem.value == 2)
        {
            $("#manualcnicsimsentry").hide();
            $('#cnic_div').show();
        } else
        {
            //Hide      
            $('#cnic_div').show();
            $("#manualcnicsimsentry").hide();
        }

    } 
    function check_msisdn_exist(number,sim_serial) { 
        var num=number.value;
         var n = num.length;
         if(num==''){
             $("#SubNO"+sim_serial).html('');
        }
         if(n==10){
        // alert(num);
        var msisdn_number = num;
                var request = $.ajax({
                    url: "<?php echo URL::site("upload/checkmobilenumberexist"); ?>",
                    type: "POST",
                    dataType: 'text',
                    data: {msisdn_number: msisdn_number},
                    success: function (exist)
                    {
                              if(exist== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                        if(exist==1){            
                            $("#SubNO"+sim_serial).html(' Number Already Exist <a title="Click to check number details" class="btn btn-primary " style="padding:0px !important" href="#" onclick="external_search_model('+sim_serial+')" > Detail</a> <a  onclick="discardaddingnumber('+sim_serial+')" title="Click to discard this record otherwise record will be updated with new cnic number" class="btn btn-danger" style="padding:0px !important"  href="#" >Discard</a>');  
                        }
                        
                        },
                    
                });
        }else{
            $("#SubNO"+sim_serial).html('');
         }
     }
    // request subscriber details
    function external_search_model(sim_serial) { 
        $("#external_search_model").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);
        var msisdn=0;
        if(sim_serial==1){            
            msisdn=$("#inputSubNO1").val();
        }else if(sim_serial==2) {            
            msisdn=$("#inputSubNO2").val();
        }else if(sim_serial==3) {            
            msisdn=$("#inputSubNO3").val();
        }else if(sim_serial==4) {            
            msisdn=$("#inputSubNO4").val();
        }else if(sim_serial==5) {
            msisdn=$("#inputSubNO5").val();  
        }
        $("#external_search_key").val(msisdn);
                   
        if(msisdn !=0){
             checkmsisdndetail(msisdn) ;
        }
    }
    
    // get mobile details
     function discardaddingnumber(sim_serial) {
        
         $("#inputSubNO"+sim_serial).val('');
         $("#SubNO"+sim_serial).html('');
         $('#sim'+sim_serial).hide();
         
     }
    // get mobile details
     function checkmsisdndetail(msisdn) {
    //ajax call to get subscriber info
                var msisdn_number = msisdn;
                var request = $.ajax({
                    url: "<?php echo URL::site("upload/checkmsisdndetail"); ?>",
                    type: "POST",
                    dataType: 'text',
                    data: {msisdn_number: msisdn_number},
                    success: function (msisdndetail)
                    {
                        if(msisdndetail== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                         $("#externa_search_results_div").html(msisdndetail);
                        },
                    
                }); 
        }
   
    //  button to displa submist button    
     var countsubmit=1;
     function display_sims_against_cnic_btn() {
     if(countsubmit==1){
             $("#display_sims_against_cnic_btn").show(); 
             $("#display_sims_against_cnic_btn_proceed").hide(); 
             countsubmit=0;
             $("#inputSubNO1").attr("readonly",true);
             $("#inputSubNO2").attr("readonly",true);
             $("#inputSubNO3").attr("readonly",true);
             $("#inputSubNO4").attr("readonly",true);
             $("#inputSubNO5").attr("readonly",true);
         }else{
             $("#display_sims_against_cnic_btn").hide(); 
             $("#display_sims_against_cnic_btn_proceed").show(); 
             countsubmit=1;
             $("#inputSubNO1").attr("readonly",false);
             $("#inputSubNO2").attr("readonly",false);
             $("#inputSubNO3").attr("readonly",false);
             $("#inputSubNO4").attr("readonly",false);
             $("#inputSubNO5").attr("readonly",false);
         }
        }
</script>
<?php
if(!empty($post_request_type_id) && $post_request_type_id==5 && !empty($post_request_value)){
    ?>
<script>
 $(document).ready(function () {  
     showDiv(2);
     $("#cnic_no").val(<?php echo $post_request_value; ?>);
        jQuery(".cnic_submit_btn").trigger("click");    
           
    });
</script>
    <?php
     } ?>


         
         

