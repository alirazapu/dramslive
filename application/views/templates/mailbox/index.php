<?php
$folder_name = Helpers_Mailbox::$folders[$folder]['name'];
$total_pages = max(1, (int) ceil($total / $per_page));
$first = $total ? (($page - 1) * $per_page) + 1 : 0;
$last = min($total, $page * $per_page);
$is_sent = ($folder === 'sent');

$page_url = function ($p) use ($folder, $search) {
    return URL::site('mailbox') . '?' . http_build_query(array_filter(array('folder' => $folder, 'q' => $search, 'page' => $p)));
};
$view_url = function ($uid) use ($folder, $search, $page) {
    return URL::site('mailbox/view') . '?' . http_build_query(array_filter(array('folder' => $folder, 'uid' => $uid, 'q' => $search, 'page' => $page > 1 ? $page : NULL)));
};
$success = Session::instance()->get_once('mailbox_success');
?>
<section class="content-header">
    <h1>
        <i class="fa fa-envelope-o"></i> Mailbox
        <small><?php echo isset($account) ? HTML::chars($account) : ''; ?></small>
    </h1>
</section>

<section class="content">
    <?php if ($success) { ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-check"></i> <?php echo HTML::chars($success); ?>
        </div>
    <?php } ?>
    <?php if ($error) { ?>
        <div class="alert alert-danger"><i class="fa fa-ban"></i> <?php echo HTML::chars($error); ?></div>
    <?php } ?>

    <div class="mb-wrap">
        <?php echo View::factory('templates/mailbox/_nav')->set('folder', $folder)->set('unread', $unread); ?>

        <div class="mb-main">
            <div class="mb-toolbar">
                <form class="mb-search" method="get" action="<?php echo URL::site('mailbox'); ?>">
                    <input type="hidden" name="folder" value="<?php echo $folder; ?>">
                    <input type="text" name="q" value="<?php echo HTML::chars($search); ?>" placeholder="Search in <?php echo $folder_name; ?>">
                    <?php if ($search !== '') { ?>
                        <a class="btn btn-link" href="<?php echo URL::site('mailbox') . '?folder=' . $folder; ?>" title="Clear search"><i class="fa fa-times"></i></a>
                    <?php } ?>
                    <button type="submit" title="Search"><i class="fa fa-search"></i></button>
                </form>
                <a class="btn btn-link" href="<?php echo $page_url($page > 1 ? $page : NULL); ?>" title="Refresh"><i class="fa fa-refresh"></i></a>
                <div class="mb-pager">
                    <?php echo $total ? number_format($first) . '&ndash;' . number_format($last) . ' of ' . number_format($total) : '0 emails'; ?>
                    <?php if ($page > 1) { ?>
                        <a href="<?php echo $page_url($page - 1); ?>" title="Newer"><i class="fa fa-chevron-left"></i></a>
                    <?php } else { ?><span class="mb-off"><i class="fa fa-chevron-left"></i></span><?php } ?>
                    <?php if ($page < $total_pages) { ?>
                        <a href="<?php echo $page_url($page + 1); ?>" title="Older"><i class="fa fa-chevron-right"></i></a>
                    <?php } else { ?><span class="mb-off"><i class="fa fa-chevron-right"></i></span><?php } ?>
                </div>
            </div>

            <?php if (empty($messages)) { ?>
                <div class="mb-empty">
                    <i class="fa fa-inbox fa-3x"></i>
                    <p><?php echo $search !== '' ? 'No emails match your search.' : 'No emails in ' . $folder_name . '.'; ?></p>
                </div>
            <?php } else { ?>
                <table class="mb-list">
                    <?php foreach ($messages as $m) {
                        $who = $is_sent ? 'To: ' . Helpers_Mailbox::display_name($m['to']) : Helpers_Mailbox::display_name($m['from']);
                        ?>
                        <tr class="<?php echo $m['seen'] ? '' : 'mb-unread'; ?>" data-href="<?php echo $view_url($m['uid']); ?>">
                            <td class="mb-star"><i class="fa <?php echo $m['flagged'] ? 'fa-star' : 'fa-star-o'; ?>"></i></td>
                            <td class="mb-from" title="<?php echo HTML::chars($is_sent ? $m['to'] : $m['from']); ?>"><?php echo HTML::chars($who); ?></td>
                            <td class="mb-subject"><?php echo $m['subject'] !== '' ? HTML::chars($m['subject']) : '(no subject)'; ?></td>
                            <td class="mb-clip"><?php if (!empty($m['attachment'])) { ?><i class="fa fa-paperclip" title="Has attachment"></i><?php } ?></td>
                            <td class="mb-date" title="<?php echo date('D, M j, Y g:i A', $m['date']); ?>"><?php echo Helpers_Mailbox::short_date($m['date']); ?></td>
                        </tr>
                    <?php } ?>
                </table>

                <?php if ($total_pages > 1) {
                    $start = max(1, $page - 4);
                    $end = min($total_pages, $page + 4);
                    ?>
                    <div class="mb-footer">
                        <ul class="pagination pagination-sm">
                            <?php if ($page > 1) { ?><li><a href="<?php echo $page_url(1); ?>">&laquo; Newest</a></li><?php } ?>
                            <?php for ($i = $start; $i <= $end; $i++) { ?>
                                <li class="<?php echo $i === $page ? 'active' : ''; ?>"><a href="<?php echo $page_url($i); ?>"><?php echo $i; ?></a></li>
                            <?php } ?>
                            <?php if ($page < $total_pages) { ?><li><a href="<?php echo $page_url($total_pages); ?>">Oldest &raquo;</a></li><?php } ?>
                        </ul>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</section>
<script>
    jQuery(function () {
        jQuery('.mb-list').on('click', 'tr[data-href]', function () {
            window.location = jQuery(this).data('href');
        });
    });
</script>
