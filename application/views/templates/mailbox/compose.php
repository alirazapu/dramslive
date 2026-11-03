<section class="content-header">
    <h1><i class="fa fa-envelope-o"></i> Mailbox <small>New message</small></h1>
</section>

<section class="content">
    <?php if ($error) { ?>
        <div class="alert alert-danger"><i class="fa fa-ban"></i> <?php echo HTML::chars($error); ?></div>
    <?php } ?>

    <div class="mb-wrap">
        <?php echo View::factory('templates/mailbox/_nav')->set('folder', ''); ?>

        <div class="mb-main">
            <form method="post" action="<?php echo URL::site('mailbox/send'); ?>" enctype="multipart/form-data" style="padding: 16px 24px;">
                <input type="hidden" name="csrf" value="<?php echo Security::token(); ?>">
                <input type="hidden" name="in_reply_to" value="<?php echo HTML::chars($form['in_reply_to']); ?>">
                <input type="hidden" name="references" value="<?php echo HTML::chars($form['references']); ?>">

                <div class="form-group">
                    <label>To</label>
                    <input type="text" name="to" class="form-control" required value="<?php echo HTML::chars($form['to']); ?>" placeholder="name@example.com, another@example.com">
                </div>
                <div class="form-group">
                    <label>Cc</label>
                    <input type="text" name="cc" class="form-control" value="<?php echo HTML::chars($form['cc']); ?>">
                </div>
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control" required value="<?php echo HTML::chars($form['subject']); ?>">
                </div>
                <div class="form-group">
                    <textarea name="body" id="mb-body-input" class="form-control" rows="16"><?php echo HTML::chars($form['body']); ?></textarea>
                </div>
                <div class="form-group">
                    <label><i class="fa fa-paperclip"></i> Attachments <small class="text-muted">(20 MB total)</small></label>
                    <input type="file" name="attachments[]" multiple>
                </div>

                <button type="submit" class="btn btn-primary" id="mb-send"><i class="fa fa-send"></i> Send</button>
                <a href="<?php echo URL::site('mailbox'); ?>" class="btn btn-default">Discard</a>
            </form>
        </div>
    </div>
</section>
<script>
    jQuery(function () {
        var body = document.getElementById('mb-body-input');
        // Replies start with a blank line above the quote; put the cursor there
        if (body.value.indexOf('\n\n') === 0 && jQuery('input[name=to]').val() !== '') {
            body.focus();
            body.setSelectionRange(0, 0);
        }
        jQuery('#mb-send').closest('form').on('submit', function () {
            jQuery('#mb-send').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
        });
    });
</script>
