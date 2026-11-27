<section class="content-header">
    <h1><i class="fa fa-bar-chart"></i> Send Usage <small>All emails sent by DRAMS</small></h1>
</section>

<section class="content">
    <?php if ($error) { ?>
        <div class="alert alert-danger"><i class="fa fa-ban"></i> <?php echo HTML::chars($error); ?></div>
    <?php } ?>

    <div class="mb-wrap">
        <?php echo View::factory('templates/mailbox/_nav')->set('folder', 'usage'); ?>

        <div class="mb-main" style="padding:16px;">
            <?php if ($usage) {
                $percent = $usage['limit'] > 0 ? (int) floor($usage['sent'] * 100 / $usage['limit']) : 0;
                $bar = $percent >= 100 ? 'danger' : ($percent >= 80 ? 'warning' : 'success');
                ?>
                <h3 style="margin-top:0;">
                    <?php echo number_format($usage['sent']); ?> of <?php echo number_format($usage['limit']); ?>
                    <small>sent in the last 24 hours (<?php echo $percent; ?>%)</small>
                </h3>
                <div class="progress">
                    <div class="progress-bar progress-bar-<?php echo $bar; ?>" style="width:<?php echo min(100, $percent); ?>%;"></div>
                </div>

                <?php if ($usage['sent'] >= $usage['limit']) { ?>
                    <div class="alert alert-danger">
                        <i class="fa fa-pause"></i> Limit reached. Automatic request sending is on hold.
                        <?php if ($usage['resumes_at']) { ?>It can resume at about <b><?php echo date('d M g:i A', $usage['resumes_at']); ?></b>.<?php } ?>
                    </div>
                <?php } else { ?>
                    <p><b><?php echo number_format($usage['limit'] - $usage['sent']); ?></b> more can be sent before automatic sending pauses.</p>
                <?php } ?>

                <p>
                    Failed in the last 24 hours: <b><?php echo number_format($usage['failed']); ?></b>,
                    of which refused for Gmail's daily limit: <b><?php echo number_format($usage['limit_errors']); ?></b>
                    <?php if ($usage['last_limit_error']) { ?>(last at <?php echo date('d M g:i A', strtotime($usage['last_limit_error'])); ?>)<?php } ?>
                </p>

                <?php
                $week_sent = $week_failed = 0;
                foreach ($usage['daily'] as $total) {
                    $week_sent += $total['sent'];
                    $week_failed += $total['failed'];
                }
                $dates = array_keys($usage['daily']);
                ?>
                <h4>This week <small><?php echo date('d M', strtotime(end($dates))) . ' to ' . date('d M Y', strtotime(reset($dates))); ?>, midnight to midnight</small></h4>
                <table class="table table-condensed table-hover">
                    <thead><tr><th>Date</th><th class="text-right">Sent</th><th class="text-right">Failed</th></tr></thead>
                    <tbody>
                        <?php foreach ($usage['daily'] as $day => $total) { ?>
                            <tr>
                                <td><?php echo date('D, d M Y', strtotime($day)); ?></td>
                                <td class="text-right"><?php echo number_format($total['sent']); ?></td>
                                <td class="text-right <?php echo $total['failed'] ? 'text-danger' : ''; ?>"><?php echo number_format($total['failed']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total this week</th>
                            <th class="text-right"><?php echo number_format($week_sent); ?></th>
                            <th class="text-right"><?php echo number_format($week_failed); ?></th>
                        </tr>
                    </tfoot>
                </table>
                <p class="text-muted">Emails sent by hand from Gmail itself are not counted here.</p>
            <?php } ?>
        </div>
    </div>
</section>
