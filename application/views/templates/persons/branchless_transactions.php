<?php


//echo '<pre>';
//print_r($search_post);
//exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Person's Portal
        <small><?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id='.$_GET['id']); ?>">Person Dashboard </a></li>                        
        <li class="active">Branchless Transactions</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form role="form" id="search_form" name="search_form" class="sms_log_summary" method="post"  action="<?php echo URL::site('persons/branchless_transactions/?id='.$_GET['id']); ?>" >
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <select class="form-control " name="phone_number" id="phone_number" onchange="person_bparty()">                                                        
                                        <option value="">Please Select Person Number</option>
                                        <?php
                                        $sims_list = Helpers_Person::get_person_inuse_SIMs($person_id);
                                        foreach ($sims_list as $sim) {
                                            ?>
                                            <option <?php echo (!empty($search_post['phone_number']) && ($search_post['phone_number'] == $sim->phone_number)) ? 'selected' : '' ?> value=<?php echo $sim->phone_number ?> > <?php echo $sim->phone_number ?> </option>
                                        <?php }  ?>
                                    </select>
                                </div>          
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="searchfield">Branchless Transaction Company</label>
                                    <select class="form-control select2" multiple="multiple" data-placeholder="Select Company" name="branchlesstransaction[]" id="branchlesstransaction" style="width: 100%;">                                                        
                                        <?php
                                            $blt_data = Helpers_Utilities::get_lu_branchless_transactions();
                                            foreach ($blt_data as $data) { ?>
                                        <option <?php echo (!empty($search_post['branchlesstransaction']) && in_array($data->id, $search_post['branchlesstransaction'])) ? "selected" : '' ?>  value=<?php echo $data->id ?>> <?php echo $data->name_branchless_transation .' => '. $data->bank_company_name ?></option>;
                                         <?php }  ?>
                                    </select>                                            
                                </div>        
                            </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="searchfield">Start Date (mm/dd/yyyy)</label>
                                        <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="startdate" id="startdate" value="<?php echo (!empty($search_post['startdate']) ? $search_post['startdate'] : ''); ?>">                                        
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="searchfield">End Date (mm/dd/yyyy)</label>
                                        <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="enddate" id="enddate" value="<?php echo (!empty($search_post['enddate']) ? $search_post['enddate'] : ''); ?>">
                                    </div>
                                </div>                                
                            <div class="form-group pull-right">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
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
                        <h3 class="box-title"><i class="fa fa-mobile-phone"></i> BRANCHLESS TRANSACTIONS OF :  <?php echo Helpers_Person::get_person_name($person_id); ?></h3>
                   <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="smslogtable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">Party A</th>
                                        <th style="width: 12%">Party B</th>
                                        <th style="width: 10%">SMS Type</th>
                                        <th style="width: 15%">Date & Time</th>
                                        <th>LOCATION</th>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th>Party A</th>
                                        <th>Party B</th>
                                        <th>SMS Type</th>
                                        <th>Date & Time</th>
                                        <th>LOCATION</th>
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
        objDT = $('#smslogtable').dataTable(
                {"aaSorting": [[3, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('persons/ajaxblesstrans/?id='.$_GET['id'], TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Party B or Location:"
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
            field: {
                check_list: true,
            },
            phone_number: {
                check_list1: true,
            },
            key: {
                key_value: true,
            },
            startdate: {
                sdate_value: true,
            },
            enddate: {
                edate_value: true,
            },
        },
        messages: {
            field: {
                check_list: "Please select search type",
            },
            phone_number: {
                check_list1: "Please select Phone Number",
            },
            key: {
                key_value: "Enter Valid Location Name",
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
        if ($('#field').val() == "location" && $('#searchfield').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("sdate_value", function (sel, element) {
        if ($('#field').val() == "date" && $('#startdate').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("edate_value", function (sel, element) {
        if ($('#field').val() == "date" && $('#enddate').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");
//Date picker
    $('#startdate').datepicker({
      autoclose: true
    });
//Date picker
    $('#enddate').datepicker({
      autoclose: true
    });

    function clearSearch() {
        window.location.href = '<?php echo URL::site('persons/sms_log_summary/?id='.$_GET['id'], TRUE); ?>';
    }
function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
</script>