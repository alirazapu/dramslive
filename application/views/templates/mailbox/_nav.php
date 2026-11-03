<?php
/* Shared left rail + styles for the mailbox pages. Expects $folder, optional $unread. */
$folder = isset($folder) ? $folder : '';
$unread = isset($unread) ? (int) $unread : 0;
?>
<style>
    .mb-wrap { display: flex; gap: 16px; align-items: flex-start; }
    .mb-rail { width: 210px; flex-shrink: 0; }
    .mb-main { flex: 1; min-width: 0; background: #fff; border-radius: 8px; box-shadow: 0 1px 2px rgba(60,64,67,.15); }
    .mb-compose { display: inline-flex; align-items: center; gap: 10px; padding: 14px 22px; margin-bottom: 14px;
        background: #c2e7ff; color: #001d35; border-radius: 16px; font-weight: 600; font-size: 14px; }
    .mb-compose:hover, .mb-compose:focus { color: #001d35; box-shadow: 0 1px 3px rgba(60,64,67,.3); }
    .mb-folders { list-style: none; margin: 0; padding: 0; }
    .mb-folders a { display: flex; align-items: center; gap: 14px; padding: 7px 14px; border-radius: 0 16px 16px 0;
        color: #202124; font-size: 14px; }
    .mb-folders a:hover { background: #e8eaed; }
    .mb-folders a.active { background: #d3e3fd; font-weight: 700; color: #001d35; }
    .mb-folders .fa { width: 16px; text-align: center; color: #5f6368; }
    .mb-folders .mb-count { margin-left: auto; font-size: 12px; font-weight: 700; }
    .mb-toolbar { display: flex; align-items: center; gap: 10px; padding: 8px 16px; border-bottom: 1px solid #eceff1; flex-wrap: wrap; }
    .mb-search { flex: 1; min-width: 200px; display: flex; background: #eaf1fb; border-radius: 24px; padding: 0 6px 0 16px; }
    .mb-search input { flex: 1; border: 0; background: transparent; padding: 9px 0; outline: none; font-size: 14px; }
    .mb-search button { border: 0; background: transparent; color: #5f6368; padding: 0 10px; }
    .mb-pager { margin-left: auto; display: flex; align-items: center; gap: 4px; color: #5f6368; font-size: 13px; white-space: nowrap; }
    .mb-pager a, .mb-pager span.mb-off { display: inline-block; width: 32px; height: 32px; line-height: 32px; text-align: center; border-radius: 50%; color: #444; }
    .mb-pager a:hover { background: #f1f3f4; }
    .mb-pager span.mb-off { color: #ccc; }
    .mb-list { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .mb-list td { padding: 0 10px; height: 40px; border-bottom: 1px solid #f1f3f4; white-space: nowrap; overflow: hidden;
        text-overflow: ellipsis; font-size: 14px; color: #202124; cursor: pointer; }
    .mb-list tr { background: #f2f6fc; }
    .mb-list tr.mb-unread { background: #fff; }
    .mb-list tr.mb-unread td { font-weight: 700; }
    .mb-list tr:hover { box-shadow: inset 1px 0 0 #dadce0, inset -1px 0 0 #dadce0, 0 1px 2px 0 rgba(60,64,67,.3); position: relative; z-index: 1; }
    .mb-list .mb-star { width: 36px; text-align: center; color: #c4c7c5; }
    .mb-list .mb-star .fa-star { color: #f4b400; }
    .mb-list .mb-from { width: 220px; }
    .mb-list .mb-clip { width: 30px; padding: 0; text-align: center; color: #5f6368; font-size: 15px; }
    .mb-list .mb-date { width: 90px; text-align: right; font-size: 12px; }
    .mb-list tr.mb-unread .mb-date { color: #202124; }
    .mb-empty { padding: 60px 20px; text-align: center; color: #5f6368; }
    .mb-footer { padding: 12px 16px; text-align: center; }
    .mb-footer .pagination { margin: 0; }
    @media (max-width: 767px) {
        .mb-wrap { flex-direction: column; }
        .mb-rail { width: 100%; }
        .mb-list .mb-from { width: 120px; }
        .mb-list .mb-date { width: 64px; }
    }
</style>
<div class="mb-rail">
    <a class="mb-compose" href="<?php echo URL::site('mailbox/compose'); ?>"><i class="fa fa-pencil"></i> Compose</a>
    <ul class="mb-folders">
        <?php foreach (Helpers_Mailbox::$folders as $key => $info) { ?>
            <li>
                <a class="<?php echo ($key === $folder) ? 'active' : ''; ?>" href="<?php echo URL::site('mailbox') . '?folder=' . $key; ?>">
                    <i class="fa <?php echo $info['icon']; ?>"></i> <?php echo $info['name']; ?>
                    <?php if ($key === 'inbox' && $unread > 0) { ?><span class="mb-count"><?php echo number_format($unread); ?></span><?php } ?>
                </a>
            </li>
        <?php } ?>
    </ul>
</div>
