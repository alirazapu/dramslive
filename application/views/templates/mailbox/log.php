<?php
$total_pages = max(1, (int) ceil($total / $per_page));
?>
<section class="content-header">
    <h1><i class="fa fa-history"></i> Sent Log <small>Emails sent from the Mailbox, by user</small></h1>
</section>

<section class="content">
    <?php if ($error) { ?>
        <div class="alert alert-danger"><i class="fa fa-ban"></i> <?php echo HTML::chars($error); ?></div>
    <?php } ?>

    <div class="mb-wrap">
        <?php echo View::factory('templates/mailbox/_nav')->set('folder', 'log'); ?>

        <div class="mb-main">
            <?php if (empty($rows)) { ?>
                <div class="mb-empty">
                    <i class="fa fa-history fa-3x"></i>
                    <p>No emails have been logged yet.</p>
                </div>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th><th>Sent by</th><th>To / Cc</th><th>Subject</th><th>Attachments</th><th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row) { ?>
                                <tr>
                                    <td style="white-space:nowrap;"><?php echo date('d M Y g:i A', strtotime($row['sent_at'])); ?></td>
                                    <td title="IP <?php echo HTML::chars($row['ip_address']); ?>"><?php echo HTML::chars($row['username']); ?></td>
                                    <td style="word-break:break-word;">
                                        <?php echo HTML::chars($row['to_emails']); ?>
                                        <?php if ($row['cc_emails'] !== '') { ?><br><small>Cc: <?php echo HTML::chars($row['cc_emails']); ?></small><?php } ?>
                                    </td>
                                    <td style="word-break:break-word;"><?php echo HTML::chars($row['subject']); ?></td>
                                    <td style="word-break:break-word;"><?php echo HTML::chars($row['attachments']); ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'sent') { ?>
                                            <span class="label label-success">Sent</span>
                                        <?php } else { ?>
                                            <span class="label label-danger" title="<?php echo HTML::chars($row['error']); ?>">Failed</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1) { ?>
                    <div class="mb-footer">
                        <?php if ($page > 1) { ?><a class="btn btn-default btn-sm" href="<?php echo URL::site('mailbox/log') . '?page=' . ($page - 1); ?>">Newer</a><?php } ?>
                        Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                        <?php if ($page < $total_pages) { ?><a class="btn btn-default btn-sm" href="<?php echo URL::site('mailbox/log') . '?page=' . ($page + 1); ?>">Older</a><?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</section>
