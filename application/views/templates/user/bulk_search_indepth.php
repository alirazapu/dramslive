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
        Bulk Search Indepth
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Bulk Search Indepth</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form class="searchform_input_height" id="search_form" name="search_form" method="post" role="form" action="<?php echo URL::site('Othernumbersearch/bulk_search_indepth'); ?>" >
                    <div class="box box-default searchperson">
                        <div style="display:none;" id="custom-form"></div>
                        <div class="box-header with-border">
                            <h3 class="box-title">Requested B-Party Numbers</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">                                                        
                            <div class="form-group col-md-12" id="bulk_div">
                                    <label for="ptclnumber">B-Party Numbers</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <textarea 
                                            class="form-control" 
                                            id="ptclnumber" 
                                            name="bulknumber" 
                                            placeholder="Enter one or multiple numbers (in this formate '3209483709','3311456630','3211699013','3249461382','3014533008','3004006510','3069790475')" 
                                            rows="30"
                                            style="resize: vertical;"
                                        ><?php echo (!empty($search_post['bulknumber']) ? htmlspecialchars($search_post['bulknumber']) : ''); ?></textarea>
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
    </div>
  

</section>

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