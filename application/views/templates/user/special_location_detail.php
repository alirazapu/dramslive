<?php
/*
 * One Special Location Request and every round with its reply
 * (Controller_Speciallocation::action_detail).
 */
$company = isset($companies[$special['company_name']]) ? $companies[$special['company_name']]->company_name : $special['company_name'];
$name = !empty($person) ? trim($person['first_name'] . ' ' . $person['last_name']) : '';
$status_class = array(0 => 'label-default', 1 => 'label-primary', 2 => 'label-success', 3 => 'label-danger', 4 => 'label-warning');
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-crosshairs"></i>
        Special Location Request #<?php echo (int) $special['id']; ?>
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('speciallocation/report'); ?>">Special Location Requests</a></li>
        <li class="active">#<?php echo (int) $special['id']; ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?php echo HTML::chars($special['msisdn']); ?> &mdash; <?php echo HTML::chars($company); ?></h3>
            <div class="pull-right">
                <button type="button" class="btn btn-default btn-sm" onclick="window.location.reload();"><i class="fa fa-refresh"></i> Refresh</button>
                <?php if ($can_cancel) { ?>
                    <button type="button" class="btn btn-danger btn-sm" id="special_cancel"><i class="fa fa-stop"></i> Cancel</button>
                <?php } ?>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <b>Profile</b><br>
                    <a href="<?php echo URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($special['person_id'], 'encrypt')); ?>">#<?php echo (int) $special['person_id']; ?></a>
                    <?php echo $name !== '' ? HTML::chars($name) : '<i>No name yet</i>'; ?>
                </div>
                <div class="col-md-3 col-sm-6">
                    <b>Schedule</b><br>
                    <?php echo (int) $special['max_requests']; ?> requests, every
                    <?php echo HTML::chars(isset(Model_Speciallocation::$intervals[$special['interval_minutes']]) ? Model_Speciallocation::$intervals[$special['interval_minutes']] : $special['interval_minutes'] . ' min'); ?><br>
                    <small>From <?php echo HTML::chars($special['created_at']); ?> until <?php echo HTML::chars($special['end_at']); ?></small>
                </div>
                <div class="col-md-3 col-sm-6">
                    <b>Requested By</b><br>
                    <?php echo HTML::chars($requested_by); ?><br>
                    <small><?php echo HTML::chars($special['reason']); ?></small>
                </div>
                <div class="col-md-3 col-sm-6">
                    <b>Status</b> (<?php echo (int) $special['sent_count']; ?> / <?php echo (int) $special['max_requests']; ?> sent)<br>
                    <?php echo $status_html; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Requests and Replies</h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Round</th>
                        <th>Request</th>
                        <th>Created</th>
                        <th>Sending Status</th>
                        <th>Response Received</th>
                        <th>Parsing Status</th>
                        <th>Location</th>
                        <th>Notes / Errors</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rounds)) { ?>
                        <tr><td colspan="8" class="text-center"><i>No request has been sent yet.</i></td></tr>
                    <?php } ?>
                    <?php foreach ($rounds as $round) {
                        $status = (int) $round['status']; ?>
                        <tr>
                            <td><?php echo (int) $round['round_no']; ?></td>
                            <td>
                                <a href="<?php echo URL::site('userrequest/request_status_detail/' . Helpers_Utilities::encrypted_key($round['request_id'], 'encrypt')); ?>"><?php echo (int) $round['request_id']; ?></a>
                                <br><small>Ref# <?php echo (int) $round['reference_id']; ?></small>
                            </td>
                            <td><?php echo HTML::chars($round['created_at']); ?></td>
                            <td>
                                <span class="label <?php echo isset($status_class[$status]) ? $status_class[$status] : 'label-default'; ?>"><?php echo HTML::chars(Helpers_Utilities::get_request_status_name($status)); ?></span>
                                <?php if (!empty($round['sending_date'])) { ?>
                                    <br><small><?php echo HTML::chars($round['sending_date']); ?></small>
                                <?php } ?>
                            </td>
                            <td><?php echo ($status == 2 && strtotime($round['received_date']) > 0) ? HTML::chars($round['received_date']) : ($status == 1 ? 'Awaiting reply' : '--'); ?></td>
                            <td><?php echo ($status == 2) ? HTML::chars(Helpers_Utilities::get_parsing_status_name((int) $round['processing_index'])) : '--'; ?></td>
                            <td><?php echo !empty($round['parsed_at']) ? Controller_Speciallocation::location_html($round) : '--'; ?></td>
                            <td>
                                <?php
                                if (isset($errors[$round['request_id']])) {
                                    $failed = $status == 3 || in_array((int) $round['processing_index'], array(1, 3));
                                    if ($failed) {
                                        echo HTML::chars($errors[$round['request_id']]['error_message']);
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
<!-- /.content -->
<?php if ($can_cancel) { ?>
<script type="text/javascript">
    $(function () {
        $('#special_cancel').on('click', function () {
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
                $.post('<?php echo URL::site('speciallocation/cancel'); ?>', {id: <?php echo json_encode($key); ?>}, function (res) {
                    if (res.status == 1) {
                        window.location.reload();
                    } else {
                        swal('Not cancelled', res.message, 'error');
                    }
                }, 'json');
            });
        });
    });
</script>
<?php } ?>
