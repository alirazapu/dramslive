<?php
$back_url = URL::site('mailbox') . ($back_query !== '' ? '?' . $back_query : '');
?>
<section class="content-header">
    <h1><i class="fa fa-envelope-o"></i> Mailbox</h1>
</section>

<section class="content">
    <div class="mb-wrap">
        <?php echo View::factory('templates/mailbox/_nav')->set('folder', $folder); ?>

        <div class="mb-main">
            <div class="mb-toolbar">
                <a class="btn btn-link" href="<?php echo $back_url; ?>" title="Back to <?php echo Helpers_Mailbox::$folders[$folder]['name']; ?>"><i class="fa fa-arrow-left"></i></a>
                <?php if ($message) {
                    $compose = URL::site('mailbox/compose') . '?folder=' . $folder;
                    ?>
                    <a class="btn btn-default btn-sm" href="<?php echo $compose . '&reply=' . $message['uid']; ?>"><i class="fa fa-reply"></i> Reply</a>
                    <a class="btn btn-default btn-sm" href="<?php echo $compose . '&reply=' . $message['uid'] . '&all=1'; ?>"><i class="fa fa-reply-all"></i> Reply all</a>
                    <a class="btn btn-default btn-sm" href="<?php echo $compose . '&forward=' . $message['uid']; ?>"><i class="fa fa-share"></i> Forward</a>
                <?php } ?>
            </div>

            <?php if ($error) { ?>
                <div class="mb-empty"><i class="fa fa-exclamation-triangle fa-2x"></i><p><?php echo HTML::chars($error); ?></p></div>
            <?php } else { ?>
                <div style="padding: 16px 24px;">
                    <h3 style="margin: 0 0 16px; font-weight: 400; font-size: 22px;">
                        <?php echo $message['subject'] !== '' ? HTML::chars($message['subject']) : '(no subject)'; ?>
                    </h3>
                    <div style="display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; font-size: 13px; color: #5f6368;">
                        <div>
                            <div style="color: #202124; font-size: 14px;"><strong><?php echo HTML::chars($message['from']); ?></strong></div>
                            <div>to <?php echo HTML::chars($message['to']); ?></div>
                            <?php if ($message['cc'] !== '') { ?><div>cc <?php echo HTML::chars($message['cc']); ?></div><?php } ?>
                        </div>
                        <div><?php echo $message['date'] ? date('D, M j, Y, g:i A', $message['date']) : ''; ?></div>
                    </div>

                    <div style="margin-top: 20px;">
                        <?php if ($message['html'] !== '') { ?>
                            <!-- Sandboxed: no scripts, links open in a new tab -->
                            <iframe id="mb-body" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin"
                                    style="width: 100%; min-height: 300px; border: 0;"
                                    srcdoc="<?php echo HTML::chars('<base target="_blank"><meta charset="utf-8">' . $message['html']); ?>"></iframe>
                        <?php } else { ?>
                            <div style="white-space: pre-wrap; font-size: 14px; word-wrap: break-word;"><?php echo HTML::chars($message['text']); ?></div>
                        <?php } ?>
                    </div>

                    <?php if ($message['attachments']) { ?>
                        <hr>
                        <p><strong><?php echo count($message['attachments']); ?> attachment(s)</strong></p>
                        <?php foreach ($message['attachments'] as $file) { ?>
                            <a class="btn btn-default" style="margin: 0 8px 8px 0;"
                               href="<?php echo URL::site('mailbox/attachment') . '?' . http_build_query(array('folder' => $folder, 'uid' => $message['uid'], 'part' => $file['part'])); ?>">
                                <i class="fa fa-paperclip"></i> <?php echo HTML::chars($file['name']); ?>
                                <small class="text-muted">(<?php echo Helpers_Mailbox::format_size($file['size']); ?>)</small>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
<script>
    // Grow the email iframe to its content height (allow-same-origin lets us read it; scripts stay disabled inside)
    jQuery(function () {
        var frame = document.getElementById('mb-body');
        if (!frame) return;
        frame.onload = function () {
            try { frame.style.height = (frame.contentWindow.document.documentElement.scrollHeight + 20) + 'px'; } catch (e) {}
        };
    });
</script>
