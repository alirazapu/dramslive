<?php
/*
 * Multi Number Request batch report (Controller_Multirequest::action_report).
 * Super Admin only; rows come from multirequest/report_data.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-list-ol"></i>
        Multi Number Request Report
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Multi Number Request Report</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Filters</h3>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filter_request_type">Request Type</label>
                            <select class="form-control" id="filter_request_type">
                                <option value="">All</option>
                                <?php foreach ($request_types as $type_id => $type) { ?>
                                    <option value="<?php echo $type_id; ?>"><?php echo HTML::chars($type['label']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter_company">Company</label>
                            <select class="form-control" id="filter_company">
                                <option value="">All</option>
                                <?php foreach (array(1, 7, 3, 4, 6) as $mnc) {
                                    if (isset($companies[$mnc])) { ?>
                                        <option value="<?php echo $mnc; ?>"><?php echo HTML::chars($companies[$mnc]->company_name); ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label for="filter_batch">Batch #</label>
                            <input type="text" class="form-control" id="filter_batch">
                        </div>
                    </div>
                    <div class="col-md-2">
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
                <table id="multirequest_report" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Batch</th>
                            <th>Request</th>
                            <th class="no-sort">Mobile #</th>
                            <th class="no-sort">Request Type / Company</th>
                            <th class="no-sort">Requested By</th>
                            <th>Created</th>
                            <th class="no-sort">Sending Status</th>
                            <th class="no-sort">Response Received</th>
                            <th class="no-sort">Parsing Status</th>
                            <th class="no-sort">Profile</th>
                            <th class="no-sort">Notes / Errors</th>
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

        var objDT = $('#multirequest_report').dataTable({
            "aaSorting": [[0, "desc"]],
            "bPaginate": true,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": "<?php echo URL::site('multirequest/report_data', TRUE); ?>",
            "sPaginationType": "full_numbers",
            "bFilter": true,
            "bLengthChange": true,
            "oLanguage": {
                "sProcessing": "Loading...",
                "sSearch": "Mobile #, Batch #, Request ID or Reference No.:"
            },
            "fnServerParams": function (aoData) {
                aoData.push({"name": "from_date", "value": $('#from_date').val()});
                aoData.push({"name": "to_date", "value": $('#to_date').val()});
                aoData.push({"name": "request_type", "value": $('#filter_request_type').val()});
                aoData.push({"name": "company_name", "value": $('#filter_company').val()});
                aoData.push({"name": "batch_id", "value": $('#filter_batch').val()});
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
    });
</script>
