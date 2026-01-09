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
        <i class="fa fa-search-plus"></i>
        Search Person
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Search Person</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form class="searchform_input_height" id="search_form" name="search_form" method="post" role="form" action="<?php echo URL::site('user/search_person'); ?>" >
                    <div class="box box-default searchperson">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advanced Search</h3>
                            <div style="display:none;" id="custom-form"></div>
                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">
                            <div class="form-group col-md-4">
                                <label for="number_type">Select Number Type </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-mobile-phone"></i>
                                    </div>
                                    <select class="form-control" name="number_type" id='number_type' onchange="number_type_change(1)">                                          
                                        <option <?php echo (( isset($search_post['number_type']) && ($search_post['number_type'] == '1')) ? 'selected' : ''); ?> value="1">Mobile Number</option>
                                        <option <?php echo (( isset($search_post['number_type']) && ($search_post['number_type'] == '4')) ? 'selected' : ''); ?> value="4">IMSI Number</option>
                                        <option <?php echo (( isset($search_post['number_type']) && ($search_post['number_type'] == '5')) ? 'selected' : ''); ?> value="5">IMEI Number</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-4 " id="mobile_number">
                                    <label for="phonenumber">Mobile Number </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-whatsapp"></i>
                                        </div>
                                        <input onkeypress="mobile_enter()" type="text" class="form-control" id="phonenumber" value="<?php echo ((!empty($search_post['phonenumber'])) ? $search_post['phonenumber'] : ''); ?>" name="phonenumber" placeholder="Search Key">
                                    </div>
                                </div>                            
                            <div class="form-group col-md-4" id="imsi_number">
                                    <label for="imsi">IMSI Number </label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-whatsapp"></i>
                                        </div>
                                        <input onkeypress="imsi_enter()" type="number" min="0" class="form-control" id="imsi" value="<?php echo ((!empty($search_post['imsi'])) ? $search_post['imsi'] : ''); ?>" name="imsi" placeholder="Search Key">
                                    </div>
                                </div>
                            
                            <div class="form-group col-md-4" id="imei_number">
                                <label for="imei">IMEI Number </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-whatsapp"></i>
                                    </div>
                                    <input onkeypress="imei_enter()" type="number" min="0" class="form-control" id="imei" value="<?php echo ((!empty($search_post['imei'])) ? $search_post['imei'] : ''); ?>" name="imei" placeholder="Search Key">
                                </div>
                            </div> 
                            <div class="col-md-12">
                                <hr class="style14">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="cnic">CNIC </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input onkeypress="cnic_enter()"  type="text" class="form-control" value="<?php echo ((!empty($search_post['cnic'])) ? $search_post['cnic'] : ''); ?>" id="cnic" name="cnic" placeholder="Search Key">
                                </div>
                            </div> 
                            <div class="form-group col-md-2">
                                <label for="is_foreigner">Nationality </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-map-marker"></i>
                                    </div>
                                    <select class="form-control" name="is_foreigner" id='is_foreigner' onchange="cnic_control(this)">                                          
                                         <option <?php echo (( isset($search_post['is_foreigner']) && ($search_post['is_foreigner'] == '0')) ? 'selected' : ''); ?> value="0">Pakistani</option>
                                         <option <?php echo (( isset($search_post['is_foreigner']) && ($search_post['is_foreigner'] == '1')) ? 'selected' : ''); ?> value="1">Foreigner</option>              
                                         <option <?php echo (( isset($search_post['is_foreigner']) && ($search_post['is_foreigner'] == '2')) ? 'selected' : ''); ?> value="2">Any</option>
                                     </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6" >
                                <label for="personname">Person Name </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input onkeypress="person_name_enter()" type="text" class="form-control" id="personname" value="<?php echo ((!empty($search_post['personname'])) ? $search_post['personname'] : ''); ?>" name="personname" placeholder="Search Key">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="fathername">Father/Husband Name</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <input onkeypress="person_name_enter()" type="text" class="form-control" id="fathername" value="<?php echo ((!empty($search_post['fathername'])) ? $search_post['fathername'] : ''); ?>" name="fathername" placeholder="Search Key">
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="category">Category </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <select onchange="category_enter()" class="form-control" name="category" id='category'>                                                        
                                        <option value="">Please Select</option>
                                        <option <?php echo (( isset($search_post['category']) && ($search_post['category'] == '0')) ? 'selected' : ''); ?> value="0"> White</option>
                                        <option <?php echo ((!empty($search_post['category']) && ($search_post['category'] == '1')) ? 'selected' : ''); ?>  value="1"> Gray</option>
                                        <option <?php echo ((!empty($search_post['category']) && ($search_post['category'] == '2')) ? 'selected' : ''); ?> value="2"> Black</option>                                                                                    
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-6" style="overflow:scroll">
                                <label for="organization">Organization </label>
                                <div class="input-group" >
                                    <div class="input-group-addon">
                                        <i class="fa fa-street-view"></i>
                                    </div>
                                    <select onchange="organization_enter()" class="form-control select2" placeholder="Search Key" multiple="multiple" id="organization" name="organization[]" style="width: 100%;border-radius:0px;" >

                                        <?php try{
                                        $org = Helpers_Utilities::get_banned_organizations();
                                        foreach ($org as $org1) {
                                            ?>
                                        <option  <?php echo (!empty($search_post['organization']) && in_array($org1->org_id, $search_post['organization'])) ? 'Selected' : ''; ?> value="<?php echo $org1->org_id; ?>"><?php echo $org1->org_name; ?></option>                                            

                                        <?php } 
                                        }  catch (Exception $ex){   }?>
                                    </select>                                   
                                </div>
                            </div>                             
                            <div class="col-md-12">
                                <div class="form-group pull-right">
                                    <button type="submit"  class="btn btn-primary">Search</button>
                                    <button type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger">Clear Search</button>
                                    <input type="reset" style="display: none" value="Reset">
                                </div>
                            </div>
                        </div>        
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> Searched Persons</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="searchperson" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th style="width: 15%">Person's Name</th>
                                        <th style="width: 13%">Father/Husband Name</th>
                                        <th class="no-sort" style="width: 10%">CNIC</th>                                          
                                        <th class="no-sort"  style="width: 8%">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>

                                <th>Person's Name</th>
                                <th>Father/Husband Name</th>
                                <th>CNIC</th> 
                                <th>Detail</th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>

</section>
<div class="modal modal-info fade" id="external_search_model">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button onclick="clearSearch1()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Search Results</h4>
            </div>

            <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>

                <!--searching data form external sources-->
                <div id="externa_search_results_div" style="display: block;">                                                            
                    <div class="col-md-12" style="background-color: #fff;color: black"> 

                        <div class="col-sm-12">
                            <div class="form-group">                                                                                
                                <label   for="external_search_key" class="control-label">Search Key:
                                </label>
                                <input name="external_search_key" type="text" class="form-control" id="external_search_key" placeholder="Search Key" readonly >
                            </div>

                            <hr class="style14 " style="margin-top: -10px; "> 
                            <div class="col-sm-12" id="external_search_results" style="display: block">   

                                <div style='background-image: url("<?php echo URL::base(); ?>dist/img/loader.gif"); width:100%; height:11px; background-position:center; display:block'><label    class="control-label" style="alignment-adjust: central">Exploring External Sources
                                    </label></div>  
                                <hr class="style14 ">
                            </div>
                        </div>                                                            
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button onclick="clearSearch1()" type="button" style="margin-top: 10px" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>

            </div>  
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->

</div>
<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>

                                        //
                                        $(function () {
                                            //Initialize Select2 Elements
                                            $(".select2").select2();
                                        });


</script>
<script type="text/javascript">
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() !== "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        //nadra request
        // validate cnic request send
        number_type_change(0);       

        //advance search
        var msisdnnumber = $('#phonenumber').val();
        var cnicnumber = $("#cnic").val();
        var imeinumber = $("#imei").val();
        var imsinumber = $("#imsi").val();
        var is_foreigner = $("#is_foreigner").val();
        var requestdata = '';
        if (msisdnnumber !== '' && msisdnnumber !== 0) {
            requestdata = '"Information not found, <a  href="#" onclick="external_search_model()"> Click To Request Subscriber </a>"';
        }else if (imsinumber !== '' && imsinumber !== 0) {
            requestdata = '"Information not found, <a  href="#" onclick="external_search_model()"> Click To Request IMSI Details </a>"';
        }  else if (cnicnumber !== '' && cnicnumber !== 0) {
            requestdata1 = 'Information not found, <a  href="#" onclick="external_search_model()"> Click To Request SIMs Against CNIC </a>, '; 
            if(is_foreigner ==1){
               requestdata = '"Information not found, <a  href="#" onclick="external_search_model()"> Click To Search Foreigner Detail </a>"';
               
            }else{
            requestdata2 = '<a  href="#" onclick="requestnadraverysis('+cnicnumber+')" > Click To Request NADRA Verisis </a>';
            requestdata = requestdata1 + ' ' + requestdata2;
             }
        } else if (imeinumber !== '' && imeinumber !== 0) {
            requestdata = '"Information not found, <a  href="#" onclick="requestimeicdr('+imeinumber+')"> Click To Request CDR Against IMEI </a>"';
        }
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
        objDT = $('#searchperson').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //  //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('user/ajaxsearchperson', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": false,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        // "sSearch": "Search By Name, Father Name and CNIC:",
                        "sEmptyTable": requestdata
                        
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false
                        }]
                }
        );

        // $('.dataTables_empty').html("Information not found, <a href='<?php // echo URL::site('userrequest/request/1', TRUE);  ?>'> Please Request form Here </a>");


// validation for data search
        $("#search_form").validate({
            errorElement: 'span',
            rules: {
                personname: {
                    alphanumericspecial: true
                },
                fathername: {
                    alphanumericspecial: true
                },
                phonenumber: {
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                },                
                cnic: {
                    // number: true,
                    custominput: true,
                    minlength: 13,
                    maxlength: 13
                },
                imei: {
                    number: true,
                    minlength: 15,
                    maxlength: 15
                },
                imsi: {
                    number: true,
                    minlength: 12,
                    maxlength: 19
                }
            },
            messages: {
                personname: {
                    alphanumericspecial: "Special Characters Not Allowed"
                },
                fathername: {
                    alphanumericspecial: "Special Characters Not Allowed"
                },
                phonenumber: {
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
//                                numberthree:"Numbers must be 10 digits, starting from 3",                           
                    minlength: "Minimum 10 digits"
                },                
                cnic: {
                    // Number:"Only number without dashes",
                    custominput: "Only number without dashes (Pakistan)",
                    maxlenght: "Number should be 13 digits",
                    minlength: "Minimum 13 digits"
                },
                imei: {
                    Number: "Enter only number",
                    maxlenght: "Maximum character limit is 15",
                    minlength: "Minimum 15 digits, add zero's to complete"

                },
                imsi: {
                    Number: "Enter only number",
                    maxlenght: "Maximum character limit is 19",
                    minlength: "Minimum 12 digits"
               }
            },
            submitHandler: function () {
                $("#search_form").submit();

            }
            // $('#upload').show()
        });

        // Validators for custom value number or string
        jQuery.validator.addMethod("custominput", function (value, element) {
            //return this.optional(element) || value == params[0] + params[1];
            var cnic_no = $("#cnic").val();
            if (cnic_no != '') {
                if (jQuery("#is_foreigner").val() == 0)
                {
                    return ($.isNumeric($(element).val()));
                } else {
                    if (jQuery.type($(element).val()) === "string")
                        return true;
                    else
                        return false;
                }
            } else {
                return true;
            }
        }, jQuery.validator.format("Please enter the correct value"));


        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");

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


    function clearSearch() {
        window.location.href = '<?php echo URL::site('user/search_person', TRUE); ?>';
       
    }
    function clearSearch1() {
       // window.location.href = '<?php //echo URL::site('user/search_person', TRUE); ?>';
     //  $("#search_form").trigger("reset");
     //  $('#search_form')[0].reset();
     //  $("#search_form").get(0).reset();
      // $(':input').val('');
       
    }
    jQuery.fn.reset = function () {
    $(this).each (function() { this.reset(); });
  }
 //function to call request page for IMEI
    function requestimeicdr(imei) {    
        var request = "new";
        var url = window.location.href;        
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/requestcdrimei"); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'imei',
            'value': imei,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'requesttype',
            'value': 2,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'request',
            'value': request,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_bd_bt_cdr_imei" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_bd_bt_cdr_imei').trigger('click');

    }

    //function to call request page for sims's againt cnic new irfan
    function requestcnicsims(cnicnumber) {            
        var url = window.location.href;        
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestcnicsims"); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'cnic',
            'value': cnicnumber,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');

    }
    //function to call request page for subscriber new irfan
    function requestsub() {    
        var phonenumber = $("#phonenumber").val();
        var url = window.location.href;        
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestsubscriber"); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'msisdn',
            'value': phonenumber,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');

    }
    //function to call request page of verisys    
    function requestnadraverysis(cnicnumber) {        
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestverisys/"); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'cnicnumber',
            'value': cnicnumber,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': '-1',
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');

    }
    
    // cnic value control by nationality
    function cnic_control(elem) {
        if (elem.value == 2) {
            $("#cnic").val('');
           // $("#cnic").attr("readonly", "readonly");
        } else {
           // $("#cnic").attr("readonly", false);
        }
    }
    // person name enter remainging empty
    function person_name_enter() {
        $("#cnic").val('');
        $("#phonenumber").val('');
        $("#imei").val('');
        $("#imsi").val('');
    }
    // cnic enter remainging empty
    function cnic_enter() {
        $("#phonenumber").val('');
        $("#imei").val('');
        $("#imsi").val('');
        $("#personname").val('');
        $("#fathername").val('');
        $("#category").val('');
        $("#organization").select2().val('');
    }
    // mobile enter remainging empty
    function mobile_enter() {
        $("#cnic").val('');
        $("#is_foreigner").val('2');
        $("#personname").val('');
        $("#fathername").val('');
        $("#category").val('');
        $("#organization").select2().val('');
    }
    // imei enter remainging empty
    function imei_enter() {
        $("#cnic").val('');
        $("#is_foreigner").val('2');
        $("#personname").val('');
        $("#fathername").val('');
        $("#category").val('');
        $("#organization").select2().val('');
    }
    // imsi enter remainging empty
    function imsi_enter() {
        $("#cnic").val('');
        $("#personname").val('');
        $("#is_foreigner").val('2');
        $("#fathername").val('');
        $("#category").val('');
        $("#organization").select2().val('');
    }
    // organization enter remainging empty
    function organization_enter() {
        $("#cnic").val('');
        $("#phonenumber").val('');
        $("#imei").val('');
        $("#imsi").val('');
    }
    // category enter remainging empty
    function category_enter() {
        $("#cnic").val('');
        $("#phonenumber").val('');
        $("#imei").val('');
        $("#imsi").val('');
    }
    // request subscriber
    function external_search_model(mobile,cnic) {  
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
        var cnic=$("#cnic").val();
       var msisdn= $("#phonenumber").val();
       var imei= $("#imei").val();
       var imsi= $("#imsi").val();
       var is_foreigner = $("#is_foreigner").val();
       if(msisdn !=0 || msisdn != ''){                 
        $("#external_search_key").val(msisdn);
           search_local_subscriber_detail('msisdn',msisdn);
       }else if(cnic !=0 || cnic != ''){                 
        $("#external_search_key").val(cnic);
        if(is_foreigner==1){
            search_foreinger_detail('foreigner_profile',cnic);
        }else{
           search_local_subscriber_detail('cnic',cnic);
       }
       }else if(imsi !=0 || imsi != ''){                 
        $("#external_search_key").val(imsi);
           search_local_subscriber_detail('imsi',imsi);
       }
        
    }
    //function to search subscriber in local sources
    function search_local_subscriber_detail(search_type, search_value ) {
        var result = {search_type: search_type,search_value:search_value}
        $.ajax({
            url: "<?php echo URL::site('userreports/msisdn_data_search', TRUE); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (msg) {
                $("#external_search_results").html(msg);
            }
        });

    }
    //function to search foreigner person
    function search_foreinger_detail(search_type, search_value ) {
        var result = {search_type: search_type,search_value:search_value}
        $.ajax({
            url: "<?php echo URL::site('userreports/search_foreinger_detail', TRUE); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (msg) {
                $("#external_search_results").html(msg);
            }
        });

    }
    
        // person name enter remainging empty
    function number_type_change(y) {
            if (y == 1) {
                $("#phonenumber").val('');   
                $("#imsi").val('');  
                $("#imei").val('');  
            }
        var x = $("#number_type").val();                     
            

            $("#mobile_number").hide();
            $("#imsi_number").hide();
            $("#imei_number").hide();
            
        if (x == 1) {
            $("#mobile_number").show();
            }
            else if (x == 4) {
                $("#imsi_number").show();
    
            }
            else if (x == 5) {
                $("#imei_number").show();
    
            }
    }
</script>