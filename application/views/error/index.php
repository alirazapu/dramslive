<section class="content-header">
    <h1>
        <i class="fa fa-exclamation-triangle text-red"></i>
        System Error Logs
        <small>Debug cron, email, parsing & uploads</small>
    </h1>
</section>

<section class="content">

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
                    <label>Company</label>
                    <?= Form::select('company_name', $company_options, Arr::get($filters,'company_name'), ['class'=>'form-control']) ?>
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
                </div>
            </form>
        </div>
    </div>

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
                        <th width="130">Source</th>
                        <th width="130">Type</th>
                        <th width="90">Request</th>
                        <th width="80">Company</th>
                        <th>Error Message</th>
                        <th width="160">Details</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></td>

                            <td>
                                <span class="label label-default">
                                    <?= HTML::chars($log['error_source']) ?>
                                </span>
                            </td>

                            <td>
                                <span class="label label-danger">
                                    <?= HTML::chars($log['error_type'] ?: 'unknown') ?>
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
                                <td colspan="7">
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
