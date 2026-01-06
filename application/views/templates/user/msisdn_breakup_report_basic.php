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
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>User's Report</a></li>
        <li class="active">Audit Report</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
<!--        <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('admindatabank/msisdn_breakup_report'); ?>">
            <input id="xport" name="xport" type="hidden" value="" />
        </form>-->
        <div class="row">
            <div class="col-md-12">
                <div class="">
                    <div class="box box-primary">
                        <form role="form" name="advance_search_form" id="advance_search_form" class="ipf-form" action="<?php echo URL::site('admindatabank/msisdn_breakup_report/'); ?>" method="POST">
                            <input type="hidden" class="form-control" name="xport" id="xport" value="">                         
                            <div class="box box-default collapsed-box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Advance Search</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                        <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="startdate">Audit Start Date (mm/dd/yyyy)</label>
                                            <input type="text"  readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" id="sdate" value="<?php echo (!empty($search_post['sdate']) ? $search_post['sdate'] : ""); ?>" name="sdate">                                                                               
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="enddate">Audit End Date (mm/dd/yyyy)</label>                                        
                                            <input  readonly="readonly" type="text" placeholder="mm/dd/yyyy" class="form-control" id="edate" value="<?php echo (!empty($search_post['edate']) ? $search_post['edate'] : ''); ?>" name="edate">                                                                              
                                        </div>
                                    </div>                                
                                    <div class="col-md-3" style="width: 23%">                                    
                                        <div class="form-group pull-right" style="margin-top: 24px">                                         
                                            <!--<button type="submit" class="btn btn-primary">Search</button>-->
                                            <input type="submit" value="Search"  class="btn btn-primary" />
                                            <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                    <!-- /.row -->
                                </div>        
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i>MSISDN Breakup Report</h3>
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="auditreport" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                                                              
                                        <th class="no-sort">Region/District</th>                                        
                                        <th>Total Request</th>                                        
                                        <th class="no-sort">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>                                                                             
                                        <th>Region/District</th>                                        
                                        <th>Total Request</th>                                        
                                        <th>Action</th>
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
<!--/ .content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
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
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        $("#advance_search_form").validate({
            rules: {
                sdate: {
                    required: true,
                },
                edate: {
                    required: true,
                    check_list1: true,
                    greaterThan: "#sdate",
                }
            },
            messages: {
                sdate: {
                    //required: "Please select Call type",
                },
                edate: {
                    //required: "Please select Phone Number",
                }
            }
        });
        $.validator.addMethod("check_list1", function (sel, element) {
            if ($('#edate').val() != "" && $('#sdate').val() == "") {
                return false;
            } else {
                return true;
            }
        }, "<span>Please Select Start Date</span>");
        jQuery.validator.addMethod("greaterThan", function (value, element, params) {
            if ($('#edate').val() != "") {
                if (!/Invalid|NaN/.test(new Date(value))) {
                    return new Date(value) > new Date($(params).val());
                }
                return isNaN(value) && isNaN($(params).val())
                        || (Number(value) > Number($(params).val()));
            } else {
                return true;
            }
        }, 'Must be greater than ( Start Date )');
        var elem = $('#field').val();
        if (elem == 'def')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem == 'name')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem == 'designation')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //enable
            // $('#searchfield').removeAttr("disabled");
        } else if (elem == 'requesttype')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //enable
            // $('#searchfield').removeAttr("disabled");
        } else if (elem == 'posting')
        {
            //show
            document.getElementById('posting-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
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
        objDT = $('#auditreport').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('admindatabank/ajaxmsisdnbreakupreportbasic', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Region Name:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });
    function showDiv(elem) {
        if (elem.value == 'def')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem.value == 'name' || elem.value == 'requesttype' || elem.value == 'designation')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem.value == 'posting')
        {
            //show
            document.getElementById('posting-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
        }
    }
    function clearSearch() {
        //window.location.reload();
        window.location.href = '<?php echo URL::site('admindatabank/msisdn_breakup_report', TRUE); ?>';
    }
    function excel(id) {
        $('#xport').val('excel');
        $('#advance_search_form').submit();
        $('#xport').val('');
    }
//    $("#search_form").validate({
//        rules: {
//            sdate: {
//                required: true,
//            },
//            edate: {
//                required: true,
//            },
//        },
//        messages: {
//            sdate: {
//                required: "Please select Call type",
//            },
//            edate: {
//                required: "Please select Phone Number",
//            },
//        }
//    });
    //Date picker
    $('#sdate').datepicker({
        autoclose: true
    });
//Date picker
    $('#edate').datepicker({
        autoclose: true
    });
</script>

