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
        <i class="fa fa-user"></i>
        User's Report
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li> User's Report</a></li>
        <li class="active">Request Send Log</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?php
        $use_i = Helpers_Utilities::encrypted_key($search_post['userid'], 'decrypt');
        ?>
        <form role="form" id="search_form" name="search_form" class="ipf-form" method="POST"
              action="<?php echo URL::site('admindatabank/no_send_type_reports/'); ?>">
            <input type="hidden" placeholder="mm/dd/yyyy" class="form-control"
                   value="<?php echo(!empty($search_post['userid']) ? $use_i : ""); ?>" name="userid">
            <input type="hidden" placeholder="mm/dd/yyyy" class="form-control"
                   value="<?php echo(!empty($search_post['sdate']) ? $search_post['sdate'] : ""); ?>" name="sdate">
            <input type="hidden" placeholder="mm/dd/yyyy" class="form-control"
                   value="<?php echo(!empty($search_post['edate']) ? $search_post['edate'] : ''); ?>" name="edate">
            <input id="xport" name="xport" type="hidden" value=""/>
        </form>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <?php
                        $sdate = !empty($search_post['sdate']) ? $search_post['sdate'] : '';
                        $edate = !empty($search_post['edate']) ? $search_post['edate'] : '';
                        ?>
                        <h3 class="box-title"><i class="fa fa-search"></i>Request Send Log of User
                            <u> <?php echo isset($use_i) ? Helpers_Utilities::get_user_name($use_i) : "UnKnown" ?></u>
                        </h3>
                        <?php if (!empty($sdate) && !empty($edate)) { ?>
                            <h2 class="box-title"> Date From: <?php echo $sdate; ?> TO <?php echo $edate; ?></h2>
                        <?php } ?>
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small"
                           style="float: right;"><i class="fa fa-file-excel-o"></i> Export</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="requestsend" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="no-sort">User Name</th>
                                    <th class="no-sort">Designation</th>
                                    <th class="no-sort">Region</th>
                                    <th class="no-sort">Posting</th>
                                    <th>Request Type</th>
                                    <th>Request Count</th>
                                    <th class="no-sort">View Detail</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>User Name</th>
                                    <th>Designation</th>
                                    <th>region</th>
                                    <th>Posting</th>
                                    <th>Request Type</th>
                                    <th>Request Count</th>
                                    <th>View Detail</th>
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
            {
                "aaSorting": [[5, "desc"]],
                "bPaginate": true,
                "bProcessing": true,
                //"bStateSave": true,
                "bServerSide": true,
                "sAjaxSource": "<?php echo URL::site('admindatabank/ajaxuserrequestsendreports', TRUE); ?>",
                "sPaginationType": "full_numbers",
                "bFilter": false,
                "bLengthChange": true,
                "oLanguage": {
                    "sProcessing": "Loading..."
                },
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }]
            }
        );
        $('.dataTables_empty').html("Information not found");
        $.fn.dataTable.ext.errMode = 'none';
        $('#userfavouritepersonlist').on('error.dt', function (e, settings, techNote, message) {
            swal("System Error", "Contact Technical Support Team.", "error");
        })

    });
    $("#search_form").validate({
        rules: {
            field: {
                check_list: true,
            },
            key: {
                key_value: true,
                required: true,
            },
        },
        messages: {
            field: {
                check_list: "Please select search type",
            },
            key: {
                key_value: "Enter Valid Search Value",
            },
        }
    });
    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "def") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("key_value", function (sel, element) {
        if ($('#searchfield').val() !== "") {
            return true;
        } else {
            return false;
        }
    }, "<span>Select One</span>");

    function clearSearch() {
        window.location.href = '<?php echo URL::site('userreports/no_request_send_type', TRUE); ?>';
    }

    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }

    //Date picker
    $('#sdate').datepicker({
        autoclose: true
    });
    //Date picker
    $('#edate').datepicker({
        autoclose: true
    });
</script>

