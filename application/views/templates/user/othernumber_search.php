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
        <li class="active">PTCL / International Number</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form class="searchform_input_height" id="search_form" name="search_form" method="post" role="form" action="<?php echo URL::site('Othernumbersearch/other_number_search'); ?>" >
                    <div class="box box-default searchperson">
                        <div style="display:none;" id="custom-form"></div>
                        <div class="box-header with-border">
                            <h3 class="box-title">PTCL / International Number</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">
                            <div class="col-sm-4" >
                                <div class="form-group">                                                            
                                    <label for="number_type">Number Type</label>                                            
                                    <select class="form-control" id="number_type" name="number_type" onchange="number_type_change(1)">
                                        <option <?php echo (( isset($search_post['number_type']) && ($search_post['number_type'] == '1')) ? 'selected' : ''); ?> value="1">PTCL Number</option>
                                        <option <?php echo (( isset($search_post['number_type']) && ($search_post['number_type'] == '2')) ? 'selected' : ''); ?> value="2">International Number</option>                                                                                                               
                                    </select>                                                                                    
                                </div>
                            </div>                            
                            <div class="form-group col-md-4" id="ptclnumber_div">
                                <label for="ptclnumber">PTCL Number</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo ((!empty($search_post['ptclnumber'])) ? $search_post['ptclnumber'] : ''); ?>" id="ptclnumber" name="ptclnumber" placeholder="Search Key">

                                </div>
                            </div>
                            <div class="form-group col-md-4" id="internationalnumber_div">
                                <label for="internationalnumber">International Number </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo ((!empty($search_post['internationalnumber'])) ? $search_post['internationalnumber'] : ''); ?>" id="internationalnumber" name="internationalnumber" placeholder="Search Key">

                                </div>
                            </div>

                            <div class="col-md-4" style="margin-top: 24px">
                                <div class="form-group" >
                                    <button type="submit" class="btn btn-primary ">Search</button>
                                    <!--<input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />-->
                                    <button type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger">Clear Search</button>
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
                        <h3 class="box-title"><i class="fa fa-search"></i> Searched Numbers</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive11">
                            <table id="othernumber" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="no-sort" >Number</th>
                                        <th class="no-sort" >Company</th>
                                        <th class="no-sort" >Number Status</th>                                          
                                        <th class="no-sort" >Activation Date</th>                                          
                                        <th class="no-sort" >Affiliated Person</th>                                          
                                        <th class="no-sort" >Old Requests Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <td colspan="6">No data to display enter valid value and search to find data</td>                                
                                </tbody>
                                <tfoot style="background-color: lightblue;">
                                    <tr>
                                        <th >Number</th>
                                        <th >Company</th>
                                        <th >Number Status</th>                                          
                                        <th >Activation Date</th>
                                        <th >Affiliated Person</th>
                                        <th >Old Requests Count</th>
                                    </tr>
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
    <div class="modal modal-info fade" id="affiliate_person_othernumber">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="" name="affiliate_number" action="<?php echo url::site() . 'Othernumbersearch/affiliate_number' ?>" id="affiliate_number" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Affiliate PTCL/International Number With Person</h4>
                    </div>

                    <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>

                        <!--searching data form external sources-->
                        <div id="affiliate_person_othernumber" style="display: block;">                                                            
                            <div class="col-md-12" style="background-color: #fff;color: black">  
                                <div class="col-sm-12">
                                    <div class="form-group">                                                                                                                    
                                        <input name="number" type="hidden" class="form-control" id="number">
                                    </div>                                                                                         
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">                                                                                
                                        <label   for="cnic_number" class="control-label">Enter CNIC / Foreigner Number  </label>
                                        <input name="cnic_number" type="text" class="form-control" id="cnic_number" placeholder="Enter CNIC / Foreigner Number" >
                                    </div>                                                                                         
                                </div>
                                <div class="col-md-6" style="margin-top: 24px">
                                    <div class="form-group" >
                                        <button type="button" onclick="check_person_exist()" class="btn btn-primary">Continue</button>                                        
                                    </div>
                                </div>
                                <div class="form-group col-md-12" id="no_person" style="display: none">
                                    <label   class="control-label">Person with given CNIC / Foreigner Number does not exist in AIES  </label>
                                </div>
                                <div class="form-group col-md-12" id="Person_exist" style="display: none
                                     ">
                                    <label   class="control-label">Person with given CNIC / Foreigner Number exist in AIES  </label>
                                    <div class="form-group col-md-8">                                    
                                        <label   for="searchedperson" class="control-label">Person Name  </label>
                                        <input name="searchedperson" type="text" class="form-control" id="searchedperson" readonly>
                                    </div>
                                    <div class="col-md-4" style="margin-top: 24px">
                                        <div class="form-group" >
                                            <button type="submit" class="btn btn-primary">Save Data</button>                                        
                                        </div>
                                    </div>
                                </div>
                            </div>                                                            
                        </div>                        
                        <div class="col-md-12">
                            <hr class="style14"> 
                        </div>
                    </div>
                    <div class="modal-footer">  

                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <div class="modal modal-info fade" id="viewrequest_details">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form class="" name="affiliate_number" action="<?php echo url::site() . 'Othernumbersearch/affiliate_number' ?>" id="affiliate_number" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">View Request Details</h4>
                    </div>

                    <div class="modal-body" id="model-body" style='background-color: #00a7d0 !important; color: black !important; '>
                        <table id="othernumber" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="no-sort" >Request Type</th>
                                    <th class="no-sort" >Request Date</th>
                                    <th class="no-sort" >Date From</th>                                          
                                    <th class="no-sort" >Date To</th>                                          
                                    <th class="no-sort" >Status</th>                                          
                                    <th class="no-sort" >Action</th>
                                </tr>
                            </thead>
                            <tbody id="request_details_tablebody">                              
                            </tbody>                            
                        </table>                                           
                    </div>
                    <div class="modal-footer">  

                    </div>
                </form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->

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

        //advance search
        var numbertype = $('#number_type').val();
        var ptclnumber = $("#ptclnumber").val();
        var internationalnumber = $("#internationalnumber").val();
        var requestdata = '';
        if (numbertype == 1 && ptclnumber !== 0) {
            requestdata1 = '"Information not found, <a  href="#" onclick="requestptclsubs('+ptclnumber+')"> Click To Request Subscriber From PTCL </a> ';
            requestdata2 = ',<a  href="#" onclick="requestptclcdr('+ptclnumber+')"> Click To Request CDR From PTCL </a>", " <a  href="#" onclick="external_search_model()"> Click To Request PTCL Subscriber </a>"';
            requestdata = requestdata1.concat(requestdata2);
        } else if (numbertype == 2 && internationalnumber !== 0) {
            requestdata = '"Information not found, <a  href="#" onclick="requestinternationalcdr('+internationalnumber+')"> Click To Request CDR From MegaData </a>"';
            // // changed by shoaib
            // requestdata2 = ',<a  href="#" onclick="requestinternationalcdr('+internationalnumber+')"> Click To Request CDR From International </a>"';
            // requestdata = requestdata1.concat(requestdata2);
        }

<?php if ((!empty($search_post['ptclnumber'])) || (!empty($search_post['internationalnumber']))) { ?>
            objDT = $('#othernumber').dataTable(
                    {//"aaSorting": [[2, "desc"]],
                        "bPaginate": true,
                        "bProcessing": true,
                        //  //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('Othernumbersearch/ajaxothernumber', TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": false,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading...",
                            "sEmptyTable": requestdata
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false
                            }]
                    }
            );
<?php } ?>
        // $('.dataTables_empty').html("Information not found, <a href='<?php // echo URL::site('userrequest/request/1', TRUE);      ?>'> Please Request form Here </a>");


// validation for data search
        $("#search_form").validate({
            errorElement: 'span',
            rules: {
                ptclnumber: {
                    required: true,
                    number: true,
                    startingzero: true,
                    minlength: 8,
                    maxlength: 15
                },
                internationalnumber: {
                    required: true,
                    number: true,
                    startingzero: true,
                    minlength: 8,
                    maxlength: 15
                }
            },
            submitHandler: function () {
                $("#search_form").submit();

            }
            // $('#upload').show()
        });

        // Validators numberthree
        jQuery.validator.addMethod("startingzero", function (value, element) {
            return this.optional(element) || value == value.match(/^[1-9]\d+$/);
        }, "Number can't start with zero, ignore zero");

    });

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
// request subscriber
    function external_search_model() {  
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
       var msisdn= $("#ptclnumber").val();
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

    function clearSearch() {
        window.location.href = '<?php echo URL::site('Othernumbersearch/other_number_search', TRUE); ?>';
    }
    function clearSearch1() {
       // window.location.href = '<?php //echo URL::site('user/search_person', TRUE); ?>';
       $("#search_form").trigger("reset");
       $('#search_form')[0].reset();
       $("#search_form").get(0).reset();
       $(':input').val('');
       
    }
    jQuery.fn.reset = function () {
    $(this).each (function() { this.reset(); });
  }
    // Number Type change
    function number_type_change(y) {
        if (y == 1) {
            $("#ptclnumber").val('');
            $("#internationalnumber").val('');
        }
        var x = $("#number_type").val();
        $("#ptclnumber_div").hide();
        $("#internationalnumber_div").hide();

        if (x == 1) {
            $("#ptclnumber_div").show();
        } else if (x == 2) {
            $("#internationalnumber_div").show();

        }
    }
    //request CDR ptcl //lol
    function requestptclcdr(ptclnumber) {
        var request = "existing";
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/requestcdrptcl/"); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'ptclnumber',
            'value': ptclnumber,
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
        newForm.append('<input type="submit" id="custom_form_bd_bt_cdr" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_bd_bt_cdr').trigger('click');

    }
    //function to call request page of PTCL subscriber    
    function requestptclsubs(ptclnumber) {
        var request = "existing";
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/requestsubptcl/"); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'ptclnumber',
            'value': ptclnumber,
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
        newForm.append('<input type="submit" id="custom_form_bd_bt_cdr" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_bd_bt_cdr').trigger('click');

    }
    //function to call request page of international number cdr    
    function requestinternationalcdr(number) {        
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/requestcdrinternational/"); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'internationalnumber',
            'value': number,
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
        newForm.append('<input type="submit" id="custom_form_bd_bt_cdr" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_bd_bt_cdr').trigger('click');

    }
//    //function to call data from ptcl
//    function requestinternationalcdr() {
//        var internumber = $("#internationalnumber").val();
//        var request = "new";
//        var result = {internationalnumber: internumber, pid: -1, requesttype: 9, request: request}
//        $.ajax({
//            url: "<?php /* echo URL::site('userrequest/request/1', TRUE); */ ?>",
//            type: 'POST',
//            data: result,
//            cache: false,
//            success: function (msg) {
//                var newDoc = document.open("text/html", "replace");
//                newDoc.write(msg);
//                newDoc.close();
//            }
//        });
//
//    }
    //function to call model of affiliate number with person
    function affiliateothernumber(number)
    {
        $("#affiliate_person_othernumber").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);
        $("#number").val(number);
    }
    //function to call model of request
    function viewrequestsold(number)
    {
        $("#viewrequest_details").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');
        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);
        $("#requested_number").val(number);
    }
    //function to call data from ptcl
    function viewrequests(number) {
        var requested_value = number;
        var request = "new";
        var result = {requested_value: requested_value}
        $.ajax({
            url: "<?php echo URL::site('Othernumbersearch/request_details', TRUE); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (result) {
                $("#viewrequest_details").modal("show");
                //appending modal background inside the blue div
                $('.modal-backdrop').appendTo('.blue');
                //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                $('body').removeClass("modal-open");
                $('body').css("padding-right", "");
                setTimeout(function () {
                    // Do something after 1 second     
                    $(".modal-backdrop.fade.in").remove();
                }, 300);
                $("#request_details_tablebody").html(result);
                if (result == 2){
                swal("System Error", "Contact Support Team.", "error");
                }
            }
        });

    }
    //Advance Search form validation
    $("#affiliate_number").validate({
        errorElement: 'span',
        rules: {
            cnic_number: {
                required: true,
                minlength: 13,
                maxlength: 13
            },
        },
        messages: {
            cnic_number: {
                required: "Enter CNIC Number",
                maxlenght: "Number should be 13 digits",
                minlength: "Minimum 13 digits"
            },
        }
    });

    //check person exit with given cnic
    function check_person_exist() {
        var cnicnumber = $("#cnic_number").val();
        var request = $.ajax({
            url: "<?php echo URL::site("Othernumbersearch/check_person"); ?>",
            type: "POST",
            dataType: 'text',
            data: {cnic: cnicnumber},
            success: function (data)
            {
                $("#Person_exist").hide();
                $("#no_person").hide();
                //alert(data);
                if (data == 1) {
                    $("#no_person").show();
                } else {
                    $("#Person_exist").show();
                    $("#searchedperson").val(data);
                }
                if (data == 2){
                swal("System Error", "Contact Support Team.", "error");
                }
            },
        });
//        }else{
//            $("#SubNO"+sim_serial).html('');
//         }
    }

</script>