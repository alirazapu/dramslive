<section class="content-header">
    <h1>
        <i class="fa fa-exclamation-triangle text-red"></i>
        System Error Logs
        <small>Debug cron, email, parsing & uploads</small>
    </h1>
</section>

<section class="content">

    <?php if (Session::instance()->get('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-check"></i> <?= Session::instance()->get_once('success') ?>
        </div>
    <?php endif; ?>

    <!-- FILTER BOX -->
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-filter"></i> Filters</h3>
        </div>

        <div class="box-body">
            <form method="get" class="row">

                <div class="col-sm-3">
                    <label>Error Source</label>
                    <?= Form::select('error_source', $source_options, Arr::get($filters,'error_source'), ['class'=>'form-control']) ?>
                </div>

                <div class="col-sm-2">
                    <label>Severity</label>
                    <?= Form::select('severity', $severity_options, Arr::get($filters,'severity'), ['class'=>'form-control']) ?>
                </div>

                <div class="col-sm-2">
                    <label>Company</label>
                    <?= Form::select('company_name', $company_options, Arr::get($filters,'company_name'), ['class'=>'form-control']) ?>
                </div>

                <div class="col-sm-2">
                    <label>Processing Status</label>
                    <?= Form::select('processing_index', $processing_index_options, Arr::get($filters,'processing_index'), ['class'=>'form-control']) ?>
                </div>

                <div class="col-sm-2">
                    <label>Request ID</label>
                    <input type="number" name="request_id" value="<?= Arr::get($filters,'request_id') ?>" class="form-control">
                </div>

                <div class="col-sm-2">
                    <label>From</label>
                    <input type="date" name="date_from" value="<?= Arr::get($filters,'date_from') ?>" class="form-control">
                </div>

                <div class="col-sm-2">
                    <label>To</label>
                    <input type="date" name="date_to" value="<?= Arr::get($filters,'date_to') ?>" class="form-control">
                </div>

                <div class="col-sm-4" style="margin-top:10px">
                    <label>Search</label>
                    <input type="text" name="search" value="<?= Arr::get($filters,'search') ?>"
                           placeholder="message / trace / context"
                           class="form-control">
                </div>

                <div class="col-sm-4" style="margin-top:35px">
                    <button class="btn btn-primary">
                        <i class="fa fa-search"></i> Apply
                    </button>
                    <a href="<?= URL::site('admin/errorlog') ?>" class="btn btn-default">
                        Reset
                    </a>
                    <button type="button" class="btn btn-danger pull-right" data-toggle="modal" data-target="#clearLogsModal">
                        <i class="fa fa-trash"></i> Clear Logs
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CLEAR LOGS MODAL -->
    <div class="modal fade" id="clearLogsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="post" action="<?= URL::site('errorlog/clear') ?>">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-trash"></i> Clear Error Logs</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Clear logs:</label>
                            <select name="clear_range" id="clear_range" class="form-control">
                                <option value="7days">Older than 7 days</option>
                                <option value="1month">Older than 1 month</option>
                                <option value="custom">Custom date range</option>
                                <option value="all">All logs (CAUTION!)</option>
                            </select>
                        </div>
                        <div id="custom_date_range" style="display:none;">
                            <div class="form-group">
                                <label>From Date:</label>
                                <input type="date" name="date_from" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>To Date:</label>
                                <input type="date" name="date_to" class="form-control">
                            </div>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fa fa-warning"></i> This action cannot be undone!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Clear Logs</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('clear_range').addEventListener('change', function() {
            document.getElementById('custom_date_range').style.display = 
                this.value === 'custom' ? 'block' : 'none';
        });
        
    </script>

    <!-- LOG TABLE -->
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-list"></i>
                Error Records (<?= $total_items ?>)
            </h3>
        </div>

        <div class="box-body table-responsive no-padding">

            <?php if (!$logs): ?>
                <p class="text-center text-muted" style="padding:20px">
                    No error logs found
                </p>
            <?php else: ?>

                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th width="140">Time</th>
                        <th width="80">Severity</th>
                        <th width="130">Source</th>
                        <th width="110">Type</th>
                        <th width="100">Stage</th>
                        <th width="80">Request</th>
                        <th width="70">Company</th>
                        <th>Error Message</th>
                        <th width="160">Details</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($logs as $log): 
                        // Define severity colors
                        $severity_colors = [
                            'error' => 'label-danger',
                            'warning' => 'label-warning',
                            'success' => 'label-success',
                            'info' => 'label-info'
                        ];
                        $severity = $log['severity'] ?: 'error';
                        $severity_class = isset($severity_colors[$severity]) ? $severity_colors[$severity] : 'label-default';
                    ?>
                        <tr>
                            <td><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></td>

                            <td>
                                <span class="label <?= $severity_class ?>">
                                    <?= HTML::chars(strtoupper($severity)) ?>
                                </span>
                            </td>

                            <td>
                                <span class="label label-default">
                                    <?= HTML::chars($log['error_source']) ?>
                                </span>
                            </td>

                            <td>
                                <span class="label <?= $severity === 'success' ? 'label-success' : 'label-danger' ?>">
                                    <?= HTML::chars($log['error_type'] ?: 'unknown') ?>
                                </span>
                            </td>

                            <td>
                                <span class="label label-info">
                                    <?= HTML::chars($log['process_stage'] ?: '—') ?>
                                </span>
                            </td>

                            <td>
                                <?= $log['request_id']
                                    ? '<a target="_blank" href="'.URL::site('admin/request/view/'.$log['request_id']).'">'.$log['request_id'].'</a>'
                                    : '—' ?>
                            </td>

                            <td><?= $log['company_name'] ?: '—' ?></td>

                            <td style="font-family: monospace; font-size:12px">
                                <?= nl2br(HTML::chars(mb_substr($log['error_message'],0,300))) ?>
                                <?= mb_strlen($log['error_message'])>300 ? '…' : '' ?>
                            </td>

                            <td>
                                <?php if ($log['context_data'] || $log['error_trace']): ?>
                                    <button class="btn btn-xs btn-info"
                                            data-toggle="collapse"
                                            data-target="#detail<?= $log['id'] ?>">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>

                        <?php if ($log['context_data'] || $log['error_trace']): ?>
                            <tr id="detail<?= $log['id'] ?>" class="collapse">
                                <td colspan="8">
                                    <div class="well well-sm"
                                         style="font-family: monospace; font-size:12px">

                                        <?php if ($log['context_data']): ?>
                                            <strong>Context</strong>
                                            <pre><?= HTML::chars(print_r(json_decode($log['context_data'], true), true)) ?></pre>
                                        <?php endif; ?>

                                        <?php if ($log['error_trace']): ?>
                                            <strong class="text-red">Stack Trace</strong>
                                            <pre><?= HTML::chars($log['error_trace']) ?></pre>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>

                    <?php endforeach; ?>
                    </tbody>
                </table>

            <?php endif; ?>
        </div>

        <div class="box-footer">
            <?= $pagination_html ?>
        </div>
    </div>

</section>
