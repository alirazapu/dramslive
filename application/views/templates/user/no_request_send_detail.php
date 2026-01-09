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
        <i class="fa fa-user" ></i>
        User's Report
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li> </i>User's Report</a></li>
        <li class="active"></i>No. Request Send</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <?php
                $use_i = Helpers_Utilities::encrypted_key($search_post['userid'], 'decrypt');
                $user_name = ( isset($use_i) ) ? Helpers_Utilities::get_user_name($use_i) : 'NA';
                $request_typ = Helpers_Utilities::encrypted_key($search_post['request_type'], 'decrypt');
                $sdate = !empty($search_post['sdate']) ? $search_post['sdate'] : '';
                $edate = !empty($search_post['edate']) ? $search_post['edate'] : '';
                $request_type = ( isset($request_typ) ) ? Helpers_Utilities::get_request_type($request_typ) : 'NA';
                ?>  
                <form role="form" id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userreports/no_request_send_detail/'); ?>" >
                    <input type="hidden"  class="form-control" value="<?php echo $use_i?>" name="userid">
                    <input type="hidden"  class="form-control" value="<?php echo $request_typ?>" name="request_type">
                    <input type="hidden"  class="form-control" value="<?php echo $sdate; ?>" name="sdate">
                    <input type="hidden"  class="form-control" value="<?php echo $edate; ?>" name="edate">
                    <input id="xport" name="xport" type="hidden" value="" />   
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> Request Send Log of: <u> <?php echo $user_name ?></u> &nbsp; against <u><?php echo $request_type ?></u></h3>
                        <?php if (!empty($sdate) & !empty($edate)) { ?>
                        <h3 class="box-title">Date From: <u> <?php echo $sdate ?></u> TO <u><?php echo $edate ?></u></h3>
                        <?php } ?>
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="requestsend" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="no-sort">Requesting User</th>                                        
                                        <th class="no-sort">Requested Value</th> 
                                        <th class="no-sort">Request Reason</th>
                                        <th class="no-sort">Person</th>
                                        <th >Date</th>                                        
                                        <th class="no-sort">Request Status</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Requesting User</th>                                        
                                        <th>Requested Value</th>                                        
                                        <th>Request Reason</th>                                        
                                        <th>Person</th>
                                        <th>Date</th>
                                        <th>Request Status</th>
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
</section>
<!-- /.content -->
<script type="text/javascript">
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        var elem = $('#type').val();
        if (elem == 'def')
        {
            //Hide
            document.getElementById('date-div').style.display = "none";
            //show
            document.getElementById('key-div').style.display = "block";            
            //disable
            $('#key').attr("disabled","disabled");
        } else if (elem == 'reqvalue')
        {
            //Hide
            document.getElementById('date-div').style.display = "none";
            //show
            document.getElementById('key-div').style.display = "block";  
            //enable
            $('#key').removeAttr("disabled");
        } else if (elem == 'date' )
        {
            //Hide
            document.getElementById('date-div').style.display = "block";
            //show
            document.getElementById('key-div').style.display = "none";   
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
        objDT = $('#requestsend').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userreports/ajaxuserrequestsenddetail', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Requested Value:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });
    $("#search_form").validate({
        rules: {
            type: {
                check_list: true,
            },
            searchfield: {
                key_value: true,
            },
            startdate: {
                sdate_value: true,
          //      dateTime: true
            },
            enddate: {
                edate_value: true,
           //     dateTime: true
            },
        },
        messages: {
            type: {
                check_list: "Please select search type",
            },
            searchfield: {
                key_value: "Enter Valid Key Value",
            },
            startdate: {
                sdate_value: "Please select start date",
            },
            enddate: {
                edate_value: "Please select End date",
            },
        }
    });
    $.validator.addMethod("check_list1", function (sel, element) {
        if (sel == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");
    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "def") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("key_value", function (sel, element) {
        if ($('#type').val() == "location" && $('#searchfield').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("sdate_value", function (sel, element) {
        if ($('#type').val() == "date" && $('#startdate').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("edate_value", function (sel, element) {
        if ($('#type').val() == "date" && $('#enddate').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");
   function showDiv(elem) {
        if (elem.value == 'def')
        {
            //Hide
            document.getElementById('date-div').style.display = "none";
            //show
            document.getElementById('key-div').style.display = "block";  
            //disable
            $('#key').attr("disabled","disabled");
        } else if (elem.value == 'reqvalue')
        {
            //Hide
            document.getElementById('date-div').style.display = "none";
            //show
            document.getElementById('key-div').style.display = "block";
            //enable
            $('#key').removeAttr("disabled");
        } else if (elem.value == 'date' )
        {
            //Hide
            document.getElementById('date-div').style.display = "block";
            //show
            document.getElementById('key-div').style.display = "none";   
        } 
    }
    function clearSearch() {
        window.location.href = "<?php echo URL::site('userreports/no_request_send_detail/?userid=' . $use_i . '&request_type=' . $request_typ, TRUE); ?>";
    }
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
//    jQuery.validator.addMethod("dateTime", function (value, element) {
//        var stamp = value.split(" ");
//        var validDate = !/Invalid|NaN/.test(new Date(stamp[0]).toString());
//        var validTime = /^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/i.test(stamp[1]);
//        return this.optional(element) || (validDate && validTime);
//    }, "Please enter a valid date and time (mm/dd/yyyy hh:mm).");
    //Date picker
  //  $('#startdate').datepicker({
   //     autoclose: true
   // });
   // $('#enddate').datepicker({
    //    autoclose: true
   // });
     $("#startdate").datepicker({format: 'mm/dd/yyyy'});
     $("#enddate").datepicker({format: 'mm/dd/yyyy'});
</script>

