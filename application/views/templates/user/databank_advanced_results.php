<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php
// Variables expected (set by Controller_Databank::action_*_results()):
//   $rows     — array of stdClass (or assoc array) source rows
//   $columns  — array of column configs:
//                 { key, label, formatter?, fallback_keys? }
//               formatter is one of:
//                 - omitted / 'text'        : HTML::chars()
//                 - 'image_jpeg'            : render base64 string as inline <img>
//                 - 'html'                  : render raw HTML (use with care)
//               fallback_keys: array of alternative column names if the
//                 primary key is missing/empty on a given row (used by
//                 the unified Subscriber search where one row may use
//                 cnic and another foreign_cnic).
//   $summary  — short text like "12 result(s)"
//   $row_actions — OPTIONAL callable. If set, an extra "Actions" column
//                  is appended; the callable is invoked once per row
//                  with the row as its argument and must return the
//                  HTML for that row's action cell (e.g. a "Family"
//                  button on ECP results).
//
// Renders a striped + bordered table.

// Default row_actions to null so isset() checks below work even when
// the action didn't pass it.
if (!isset($row_actions)) {
    $row_actions = null;
}
$has_row_actions = is_callable($row_actions);

/**
 * Read a value from $row trying $primary first, then each fallback key.
 * Returns '' if all are missing/empty.
 */
$dbk_pick = function ($row, $primary, $fallbacks = array()) {
    $candidates = array_merge(array($primary), $fallbacks);
    foreach ($candidates as $k) {
        if (is_array($row)) {
            if (isset($row[$k]) && $row[$k] !== null && $row[$k] !== '') {
                return $row[$k];
            }
        } else {
            if (isset($row->$k) && $row->$k !== null && $row->$k !== '') {
                return $row->$k;
            }
        }
    }
    return '';
};
?>
<div>
    <p class="text-muted" style="margin-bottom:8px;">
        <strong><?php echo HTML::chars($summary); ?></strong>
    </p>
    <div style="overflow-x:auto;">
        <table class="table table-striped table-bordered table-condensed">
            <thead>
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <th><?php echo HTML::chars($col['label']); ?></th>
                    <?php endforeach; ?>
                    <?php if ($has_row_actions): ?>
                        <th style="width:90px;">Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <?php
                            $key       = $col['key'];
                            $fallbacks = isset($col['fallback_keys']) ? $col['fallback_keys'] : array();
                            $formatter = isset($col['formatter'])     ? $col['formatter']     : 'text';
                            $val       = $dbk_pick($r, $key, $fallbacks);
                        ?>
                        <td>
                            <?php
                            if ($val === '' || $val === null) {
                                echo '<span class="text-muted">&mdash;</span>';
                            } elseif ($formatter === 'image_jpeg') {
                                $src = (strpos((string) $val, 'data:image') === 0)
                                    ? $val
                                    : 'data:image/jpeg;base64,' . $val;
                                echo '<img src="' . $src . '" alt="" '
                                   . 'style="max-width:240px; max-height:80px; border:1px solid #ddd;">';
                            } elseif ($formatter === 'html') {
                                echo $val;
                            } else {
                                echo HTML::chars((string) $val);
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                    <?php if ($has_row_actions): ?>
                        <td><?php echo call_user_func($row_actions, $r); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
