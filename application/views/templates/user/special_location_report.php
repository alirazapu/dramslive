<?php
/*
 * Special Location Request list (Controller_Speciallocation::action_report).
 * Rows come from speciallocation/report_data: every user's requests with the
 * "Special Location Report" right, otherwise the user's own.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-crosshairs"></i>
        Special Location Requests
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Special Location Requests</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Filters <?php if (!$all_users) { ?><small>(your requests)</small><?php } ?></h3>
                <?php if ($can_send) { ?>
                    <a href="<?php echo URL::site('speciallocation/index'); ?>" class="btn btn-info btn-small" style="float: right;"><i class="fa fa-plus"></i> New Request</a>
                <?php } ?>
            </div>
            <form role="form" id="report_filters" class="ipf-form" onsubmit="return false;" autocomplete="off">
                <div class="box-body">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="from_date">Date From</label>
                            <input type="text" readonly="readonly" class="form-control report-date" id="from_date" placeholder="mm/dd/yyyy">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="to_date">Date To</label>
                            <input type="text" readonly="readonly" class="form-control report-date" id="to_date" placeholder="mm/dd/yyyy">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_status">Status</label>
                            <select class="form-control" id="filter_status">
                                <option value="">All</option>
                                <?php foreach (Model_Speciallocation::$status_names as $status_id => $status_name) { ?>
                                    <option value="<?php echo $status_id; ?>"><?php echo HTML::chars($status_name); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_company">Company</label>
                            <select class="form-control" id="filter_company">
                                <option value="">All</option>
                                <?php foreach (Model_Speciallocation::$companies as $mnc) {
                                    if (isset($companies[$mnc])) { ?>
                                        <option value="<?php echo $mnc; ?>"><?php echo HTML::chars($companies[$mnc]->company_name); ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-primary" id="apply_filters"><i class="fa fa-search"></i> Search</button>
                                <button type="button" class="btn btn-default" id="reset_filters">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="box box-primary">
            <div class="box-body table-responsive">
                <table id="speciallocation_report" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mobile # / Company</th>
                            <th class="no-sort">Profile</th>
                            <th class="no-sort">Schedule</th>
                            <th class="no-sort">Requested By</th>
                            <th>Created</th>
                            <th class="no-sort">Sent / Replies</th>
                            <th class="no-sort">Last Location</th>
                            <th class="no-sort">Status</th>
                            <th class="no-sort">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<script type="text/javascript">
    $(document).ready(function () {
        $('.report-date').datepicker({endDate: 'today', autoclose: true});

        var objDT = $('#speciallocation_report').dataTable({
            "aaSorting": [[0, "desc"]],
            "bPaginate": true,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo URL::site('speciallocation/report_data', TRUE); ?>",
            "sPaginationType": "full_numbers",
            "bFilter": true,
            "bLengthChange": true,
            "oLanguage": {
                "sProcessing": "Loading...",
                "sSearch": "Mobile #, Request # or Profile ID:"
            },
            "fnServerParams": function (aoData) {
                aoData.push({"name": "from_date", "value": $('#from_date').val()});
                aoData.push({"name": "to_date", "value": $('#to_date').val()});
                aoData.push({"name": "status", "value": $('#filter_status').val()});
                aoData.push({"name": "company_name", "value": $('#filter_company').val()});
            },
            "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false
            }]
        });

        $('#apply_filters').on('click', function () {
            objDT.fnDraw();
        });
        $('#reset_filters').on('click', function () {
            $('#report_filters')[0].reset();
            objDT.fnDraw();
        });

        $(document).on('click', '.special-cancel', function () {
            var id = $(this).data('id');
            swal({
                title: 'Cancel this request?',
                text: 'No further location requests will be sent. Requests already queued are still sent.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonClass: 'btn-danger',
                confirmButtonText: 'Yes, cancel it',
                cancelButtonText: 'No'
            }, function (isConfirm) {
                if (!isConfirm) {
                    return;
                }
                $.post('<?php echo URL::site('speciallocation/cancel'); ?>', {id: id}, function (res) {
                    if (res.status == 1) {
                        swal('Cancelled', 'The request was cancelled.', 'success');
                        objDT.fnDraw(false);
                    } else {
                        swal('Not cancelled', res.message, 'error');
                    }
                }, 'json');
            });
        });
    });
</script>
