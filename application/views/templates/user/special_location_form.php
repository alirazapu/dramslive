<?php
/*
 * Special Location Request form (Controller_Speciallocation::action_index).
 * Posts to speciallocation/submit; the first request is sent at once and the
 * rest every N minutes by the special_location_tick cron.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-crosshairs"></i>
        Special Location Request
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Special Location Request</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Repeated Current Location Against Mobile Number</h3>
                    <a href="<?php echo URL::site('speciallocation/report'); ?>" class="btn btn-info btn-small" style="float: right;"><i class="fa fa-table"></i> Location Requests</a>
                </div>
                <form class="ipf-form" name="speciallocationform" id="speciallocationform" method="post" autocomplete="off">
                    <input type="hidden" name="pid" value="<?php echo HTML::chars($pid); ?>">
                    <div class="box-body">
                        <div class="col-sm-12">
                            <div class="callout callout-info" style="margin-bottom: 15px;">
                                The first location request is sent to the company right away, then again after every selected interval until the number of requests is reached or the end time passes.
                                A request is sent even if the previous one has not been replied to yet. Every reply is added to the profile's location history.
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="msisdn" class="control-label">Mobile Number</label>
                                <input type="text" class="form-control" name="msisdn" id="msisdn" value="<?php echo HTML::chars($msisdn); ?>" placeholder="3001234567" maxlength="15">
                                <p class="help-block" id="msisdn_profile"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="company_name" class="control-label">Company Name</label>
                                <select class="form-control" name="company_name" id="company_name">
                                    <option value="">Please select company</option>
                                    <?php foreach (Model_Speciallocation::$companies as $mnc) {
                                        if (isset($companies[$mnc])) { ?>
                                            <option value="<?php echo $mnc; ?>"><?php echo HTML::chars($companies[$mnc]->company_name); ?></option>
                                        <?php }
                                    } ?>
                                </select>
                                <p class="help-block">Ufone is not available: its replies are matched by mobile number, so overlapping requests would get each other's replies.</p>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="interval_minutes" class="control-label">Send Every</label>
                                <select class="form-control schedule-input" name="interval_minutes" id="interval_minutes">
                                    <option value="">Please select</option>
                                    <?php foreach (Model_Speciallocation::$intervals as $minutes => $label) { ?>
                                        <option value="<?php echo $minutes; ?>"><?php echo HTML::chars($label); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="max_requests" class="control-label">Number of Requests</label>
                                <select class="form-control schedule-input" name="max_requests" id="max_requests">
                                    <option value="">Please select</option>
                                    <?php foreach (Model_Speciallocation::$counts as $count) { ?>
                                        <option value="<?php echo $count; ?>"><?php echo $count; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="duration_hours" class="control-label">End After</label>
                                <select class="form-control schedule-input" name="duration_hours" id="duration_hours">
                                    <option value="">Please select</option>
                                    <?php foreach (Model_Speciallocation::$durations as $hours => $label) { ?>
                                        <option value="<?php echo $hours; ?>"><?php echo HTML::chars($label); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <p class="help-block" id="schedule_summary"></p>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="project_id" class="control-label">Linked Project</label>
                                <?php $projects_data = Helpers_Utilities::get_projects_list(); ?>
                                <select class="form-control select2" name="project_id" id="project_id" style="width: 100%!important;">
                                    <option value="">Please select project name</option>
                                    <?php foreach ($projects_data as $project) {
                                        $region_district = Helpers_Requests::get_project_region_district($project->region_id, $project->district_id); ?>
                                        <option value="<?php echo $project->id; ?>"><?php echo HTML::chars($project->project_name . $region_district); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="reason" class="control-label">Reason For This Request</label>
                                <textarea class="form-control" name="reason" id="reason" placeholder="Enter Reason For Request"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <button id="speciallocationbtn" type="button" class="btn btn-primary pull-right" style="margin-top:10px">Submit</button>
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
    // Same rule as Model_Multirequest::normalize_number().
    function normalizeNumber(value) {
        var d = String(value).replace(/\D/g, '');
        if (d.length === 14 && d.substr(0, 4) === '0092') {
            d = d.substr(4);
        } else if (d.length === 13 && d.substr(0, 3) === '092') {
            d = d.substr(3);
        } else if (d.length === 12 && d.substr(0, 2) === '92') {
            d = d.substr(2);
        } else if (d.length === 11 && d.charAt(0) === '0') {
            d = d.substr(1);
        }
        return /^3\d{9}$/.test(d) ? d : '';
    }

    function escapeHtml(text) {
        return $('<div>').text(String(text)).html();
    }

    // Same rule as Model_Speciallocation::rounds_that_fit().
    function roundsThatFit() {
        var interval = parseInt($('#interval_minutes').val(), 10);
        var hours = parseInt($('#duration_hours').val(), 10);
        return (interval && hours) ? Math.floor(hours * 60 / interval) + 1 : 0;
    }

    function updateScheduleSummary() {
        var interval = $('#interval_minutes option:selected');
        var count = parseInt($('#max_requests').val(), 10);
        var fit = roundsThatFit();
        if (!interval.val() || !count || !fit) {
            $('#schedule_summary').text('').css('color', '');
            return;
        }
        if (count > fit) {
            $('#schedule_summary').text('Only ' + fit + ' requests fit before the end time at one every ' + interval.text() + '.').css('color', '#dd4b39');
        } else {
            var minutes = (count - 1) * parseInt(interval.val(), 10);
            var last = minutes >= 60 ? (Math.floor(minutes / 60) + ' h ' + (minutes % 60) + ' min') : (minutes + ' min');
            $('#schedule_summary').text(count + ' requests: the first now, one every ' + interval.text() + ', the last about ' + last + ' from now.').css('color', '');
        }
    }

    // Profile lookup while typing (speciallocation/check_number). numberOk is
    // null until the typed number has been checked.
    var numberOk = null;
    var checkTimer = null;
    var checkXhr = null;
    function checkNumber() {
        var number = normalizeNumber($('#msisdn').val());
        clearTimeout(checkTimer);
        if (checkXhr) {
            checkXhr.abort();
        }
        numberOk = null;
        if (number === '') {
            $('#msisdn_profile').text('').css('color', '');
            return;
        }
        $('#msisdn_profile').html('<i class="fa fa-spinner fa-spin"></i> Checking profile...').css('color', '');
        checkTimer = setTimeout(function () {
            checkXhr = $.post('<?php echo URL::site('speciallocation/check_number'); ?>', {msisdn: number, pid: $('input[name="pid"]').val()}, function (res) {
                numberOk = res.status == 1;
                $('#msisdn_profile').html((numberOk ? '<i class="fa fa-check"></i> ' : '<i class="fa fa-times"></i> ') + escapeHtml(res.message))
                        .css('color', numberOk ? '#00a65a' : '#dd4b39');
            }, 'json');
        }, 400);
    }

    $(function () {
        $('.select2').select2();
        $('.schedule-input').on('change', updateScheduleSummary);
        $('#msisdn').on('input change', checkNumber);
        if ($('#msisdn').val() !== '') {
            checkNumber();
        }

        jQuery.validator.addMethod('alphanumericspecial', function (value, element) {
            return this.optional(element) || /^[-a-zA-Z0-9_ .,\/]+$/.test(value);
        }, 'Only letters, Numbers,Dot & Space/underscore Allowed.');
        jQuery.validator.addMethod('mobilenumber', function (value) {
            return normalizeNumber(value) !== '';
        }, 'Enter a valid mobile number (3XXXXXXXXX).');
        jQuery.validator.addMethod('roundsfit', function (value) {
            var fit = roundsThatFit();
            return !fit || !value || parseInt(value, 10) <= fit;
        }, 'Too many requests for this interval and end time.');

        $('#speciallocationform').validate({
            ignore: ':hidden:not(select)',
            rules: {
                msisdn: {required: true, mobilenumber: true},
                company_name: {required: true},
                interval_minutes: {required: true},
                max_requests: {required: true, roundsfit: true},
                duration_hours: {required: true},
                project_id: {required: true},
                reason: {required: true, alphanumericspecial: true, minlength: 5, maxlength: 500}
            },
            messages: {
                msisdn: {required: 'Enter mobile number'},
                company_name: {required: 'Select company'},
                interval_minutes: {required: 'Select interval'},
                max_requests: {required: 'Select number of requests'},
                duration_hours: {required: 'Select end time'},
                project_id: {required: 'Select linked project'},
                reason: {required: 'Enter reason for request', maxlength: 'Maximum character limit is 500'}
            }
        });

        $('#speciallocationbtn').on('click', function () {
            if (!$('#speciallocationform').valid()) {
                return;
            }
            if (numberOk === false) {
                swal('Request not sent', $('#msisdn_profile').text(), 'error');
                return;
            }
            var button = $(this);
            button.prop('disabled', true).text('Please wait...');
            $.ajax({
                url: '<?php echo URL::site('speciallocation/submit'); ?>',
                type: 'POST',
                dataType: 'json',
                data: $('#speciallocationform').serialize(),
                success: function (res) {
                    if (res.status == 1) {
                        var text = 'Special Location Request #' + escapeHtml(res.id) + ' started.'
                                + (res.request_id ? '<br>First request #' + escapeHtml(res.request_id) + ' queued for the company.' : '')
                                + (res.note ? '<br>' + escapeHtml(res.note) : '');
                        swal({
                            title: 'Request Started',
                            text: text,
                            type: 'success',
                            html: true,
                            showCancelButton: true,
                            confirmButtonText: 'View Replies',
                            cancelButtonText: 'Stay Here'
                        }, function (isConfirm) {
                            if (isConfirm) {
                                window.location.href = res.detail_url;
                            }
                        });
                    } else {
                        var lines = res.errors ? res.errors : [res.message];
                        swal({title: 'Request not sent', text: '<ul style="text-align:left; padding-left:20px;">' + lines.map(function (line) {
                                return '<li>' + escapeHtml(line) + '</li>';
                            }).join('') + '</ul>', type: 'error', html: true});
                    }
                },
                error: function (xhr) {
                    var message = 'Could not reach the server. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    swal('Request not sent', message, 'error');
                },
                complete: function () {
                    button.prop('disabled', false).text('Submit');
                }
            });
        });
    });
</script>
