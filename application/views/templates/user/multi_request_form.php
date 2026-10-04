<?php
/*
 * Multi Number Request form (Controller_Multirequest::action_index).
 * Posts to multirequest/submit; every number becomes its own Telco request.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-list-ol"></i>
        Multi Number Request
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Multi Number Request</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Request Against Multiple Mobile Numbers</h3>
                    <?php if ($has_report_access) { ?>
                        <a href="<?php echo URL::site('multirequest/report'); ?>" class="btn btn-info btn-small" style="float: right;"><i class="fa fa-table"></i> Batch Report</a>
                    <?php } ?>
                </div>
                <form class="ipf-form" name="multirequestform" id="multirequestform" method="post" autocomplete="off">
                    <div class="box-body">
                        <div class="col-sm-12">
                            <div class="callout callout-info" style="margin-bottom: 15px;">
                                Every number is sent to the company as its own request and each reply is parsed into that number's profile.
                                A number without a profile gets a new profile, and a Subscriber request is queued automatically for any profile that has no Name or CNIC.
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="request_type" class="control-label">Request Type</label>
                                <select class="form-control" name="request_type" id="request_type">
                                    <option value="">Please select request type</option>
                                    <?php foreach ($request_types as $type_id => $type) { ?>
                                        <option value="<?php echo $type_id; ?>" data-companies="<?php echo implode(',', $type['companies']); ?>"><?php echo HTML::chars($type['label']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="company_name" class="control-label">Company Name</label>
                                <select class="form-control" name="company_name" id="company_name">
                                    <option value="">Please select company</option>
                                    <?php foreach (array(1, 7, 3, 4, 6) as $mnc) {
                                        if (isset($companies[$mnc])) { ?>
                                            <option value="<?php echo $mnc; ?>"><?php echo HTML::chars($companies[$mnc]->company_name); ?></option>
                                        <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div id="cdr_dates" style="display: none;">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="control-label">Quick Options (for start and End Date)</label>
                                    <div>
                                        <button type="button" class="btn btn-primary quick-range" data-days="30">Last 30 Days</button>
                                        <button type="button" class="btn btn-primary quick-range" data-days="60">Last 60 Days</button>
                                        <button type="button" class="btn btn-primary quick-range" data-days="90">Last 90 Days</button>
                                        <button type="button" class="btn btn-primary quick-range" data-days="180">Last 180 Days</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="start_date" class="control-label">Date From (mm/dd/yyyy)</label>
                                    <input type="text" readonly="readonly" class="form-control" name="start_date" id="start_date" value="" placeholder="mm/dd/yyyy">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="end_date" class="control-label">Date To (mm/dd/yyyy)</label>
                                    <input type="text" readonly="readonly" class="form-control" name="end_date" id="end_date" value="" placeholder="mm/dd/yyyy">
                                </div>
                            </div>
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
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="numbers" class="control-label">Mobile Numbers <small>(one per line, or separated by comma or space; up to <?php echo (int) $max_numbers; ?>)</small></label>
                                <textarea class="form-control" name="numbers" id="numbers" rows="8" placeholder="3001234567&#10;03001234567&#10;+923001234567"></textarea>
                                <p class="help-block" id="numbers_summary"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <button id="multirequestbtn" type="button" class="btn btn-primary pull-right" style="margin-top:10px">Submit</button>
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
    var MAX_NUMBERS = <?php echo (int) $max_numbers; ?>;
    var CDR_TYPES = ['1', '6'];

    // Same rules as Model_Multirequest::normalize_number() / parse_numbers().
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

    function parseNumbers(raw) {
        var result = {valid: [], invalid: [], duplicates: []};
        String(raw).replace(/-/g, '').split(/[\s,;]+/).forEach(function (value) {
            if (value === '') {
                return;
            }
            var number = normalizeNumber(value);
            if (number === '') {
                result.invalid.push(value);
            } else if (result.valid.indexOf(number) !== -1) {
                result.duplicates.push(value);
            } else {
                result.valid.push(number);
            }
        });
        return result;
    }

    function escapeHtml(text) {
        return $('<div>').text(String(text)).html();
    }

    function htmlList(items) {
        return '<ul style="text-align:left; padding-left:20px;">' + items.map(function (item) {
            return '<li>' + escapeHtml(item) + '</li>';
        }).join('') + '</ul>';
    }

    function formatDate(date) {
        var mm = ('0' + (date.getMonth() + 1)).slice(-2);
        var dd = ('0' + date.getDate()).slice(-2);
        return mm + '/' + dd + '/' + date.getFullYear();
    }

    function updateNumbersSummary() {
        var parsed = parseNumbers($('#numbers').val());
        var text = parsed.valid.length + ' valid number(s)';
        if (parsed.duplicates.length) {
            text += ', ' + parsed.duplicates.length + ' duplicate(s) will be removed';
        }
        if (parsed.invalid.length) {
            text += ', not valid: ' + parsed.invalid.slice(0, 10).join(', ') + (parsed.invalid.length > 10 ? ' ...' : '');
        }
        if (parsed.valid.length > MAX_NUMBERS) {
            text += ' (at most ' + MAX_NUMBERS + ' allowed)';
        }
        $('#numbers_summary').text(text).css('color', (parsed.invalid.length || parsed.valid.length > MAX_NUMBERS) ? '#dd4b39' : '');
    }

    function onRequestTypeChange() {
        var option = $('#request_type option:selected');
        var allowed = String(option.data('companies') || '').split(',');
        $('#company_name option').each(function () {
            var value = $(this).val();
            $(this).prop('disabled', value !== '' && option.val() !== '' && allowed.indexOf(value) === -1);
        });
        if ($('#company_name option:selected').prop('disabled')) {
            $('#company_name').val('');
        }
        $('#cdr_dates').toggle(CDR_TYPES.indexOf(option.val()) !== -1);
    }

    $(function () {
        $('.select2').select2();
        $('#start_date, #end_date').datepicker({
            endDate: 'today',
            autoclose: true
        });
        $('.quick-range').on('click', function () {
            var end = new Date();
            var start = new Date();
            start.setDate(start.getDate() - parseInt($(this).data('days'), 10));
            $('#start_date').val(formatDate(start));
            $('#end_date').val(formatDate(end));
        });
        $('#request_type').on('change', onRequestTypeChange);
        $('#numbers').on('input change', updateNumbersSummary);

        jQuery.validator.addMethod('alphanumericspecial', function (value, element) {
            return this.optional(element) || /^[-a-zA-Z0-9_ .,\/]+$/.test(value);
        }, 'Only letters, Numbers,Dot & Space/underscore Allowed.');
        jQuery.validator.addMethod('mobilenumbers', function (value) {
            var parsed = parseNumbers(value);
            return parsed.invalid.length === 0 && parsed.valid.length > 0 && parsed.valid.length <= MAX_NUMBERS;
        }, 'Enter 1 to ' + MAX_NUMBERS + ' valid mobile numbers.');

        $('#multirequestform').validate({
            ignore: ':hidden:not(select)',
            rules: {
                request_type: {required: true},
                company_name: {required: true},
                project_id: {required: true},
                start_date: {required: function () { return CDR_TYPES.indexOf($('#request_type').val()) !== -1; }},
                end_date: {required: function () { return CDR_TYPES.indexOf($('#request_type').val()) !== -1; }},
                reason: {required: true, alphanumericspecial: true, minlength: 5, maxlength: 500},
                numbers: {required: true, mobilenumbers: true}
            },
            messages: {
                request_type: {required: 'Select request type'},
                company_name: {required: 'Select company'},
                project_id: {required: 'Select linked project'},
                start_date: {required: 'Select start date'},
                end_date: {required: 'Select end date'},
                reason: {required: 'Enter reason for request', maxlength: 'Maximum character limit is 500'},
                numbers: {required: 'Enter mobile numbers'}
            }
        });

        $('#multirequestbtn').on('click', function () {
            if (!$('#multirequestform').valid()) {
                return;
            }
            var button = $(this);
            button.prop('disabled', true).text('Please wait...');
            $.ajax({
                url: '<?php echo URL::site('multirequest/submit'); ?>',
                type: 'POST',
                dataType: 'json',
                data: $('#multirequestform').serialize(),
                success: function (res) {
                    if (res.status == 1) {
                        var subscriber = res.created.filter(function (item) { return item.subscriber_request_id > 0; }).length;
                        var profiles = res.created.filter(function (item) { return item.profile_created; }).length;
                        var html = '<p style="text-align:left;">Batch #' + escapeHtml(res.batch_id) + ': ' + res.created.length + ' request(s) queued, one email per number.'
                                + (subscriber ? '<br>' + subscriber + ' Subscriber request(s) queued to fill Name/CNIC.' : '')
                                + (profiles ? '<br>' + profiles + ' new profile(s) created.' : '') + '</p>';
                        if (res.skipped.length) {
                            html += '<p style="text-align:left;"><b>Skipped:</b></p>' + htmlList(res.skipped.map(function (item) { return item.number + ': ' + item.reason; }));
                        }
                        if (res.notes.length) {
                            html += '<p style="text-align:left;"><b>Notes:</b></p>' + htmlList(res.notes.map(function (item) { return item.number + ': ' + item.note; }));
                        }
                        if (res.duplicates.length) {
                            html += '<p style="text-align:left;">Duplicates removed: ' + escapeHtml(res.duplicates.join(', ')) + '</p>';
                        }
                        $('#numbers').val('');
                        updateNumbersSummary();
                        swal({title: 'Requests Queued', text: html, type: 'success', html: true});
                    } else {
                        var lines = res.errors ? res.errors : [res.message];
                        if (res.skipped) {
                            lines = lines.concat(res.skipped.map(function (item) { return item.number + ': ' + item.reason; }));
                        }
                        swal({title: 'Request not sent', text: htmlList(lines), type: 'error', html: true});
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
