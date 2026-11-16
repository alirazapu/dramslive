<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php
// Variables expected (set by Controller_Databank::_all_sources_html()):
//   $rows     — array of stdClass (or assoc array) source rows
//   $columns  — array of column configs:
//                 { key, label, formatter?, fallback_keys? }
//               formatter is one of:
//                 - omitted / 'text'        : HTML::chars()
//                 - 'image_jpeg'            : render base64 string as inline <img>
//                 - 'html'                  : render raw HTML (use with care)
//               fallback_keys: array of alternative column names if the
//                 primary key is missing/empty on a given row.
//   $summary  — short text like "12 record(s)"
//   $row_actions — OPTIONAL callable. If set, an extra "Actions" column
//                  is appended; the callable is invoked once per row
//                  with the row as its argument and must return the
//                  HTML for that row's action cell.
//
// Renders a toolbar (count + quick filter) and a scrollable table with a
// sticky header. Styles live in databank_advanced_form.php (.dbk-*).

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
<div class="dbk-toolbar">
    <span class="dbk-count" data-total="<?php echo HTML::chars($summary); ?>"><?php echo HTML::chars($summary); ?></span>
    <?php if (count($rows) > 1): ?>
        <div class="dbk-filter">
            <i class="fa fa-filter" aria-hidden="true"></i>
            <input type="text" class="dbk-filter-input" placeholder="Filter these records" aria-label="Filter these records">
        </div>
    <?php endif; ?>
</div>
<div class="dbk-table-wrap">
    <table class="dbk-table">
        <thead>
            <tr>
                <?php foreach ($columns as $col): ?>
                    <th scope="col"><?php echo HTML::chars($col['label']); ?></th>
                <?php endforeach; ?>
                <?php if ($has_row_actions): ?>
                    <th scope="col" style="width:90px;">Actions</th>
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
                        // Identifiers (CNIC, mobile, FIR no.) read better in a fixed-width font.
                        $is_num    = $formatter === 'text' && is_scalar($val)
                                     && preg_match('/^[0-9+\-\/ ]{5,}$/', (string) $val);
                    ?>
                    <td<?php echo $is_num ? ' class="dbk-num"' : ''; ?>>
                        <?php
                        if ($val === '' || $val === null) {
                            echo '<span class="dbk-empty-cell">&mdash;</span>';
                        } elseif ($formatter === 'image_jpeg') {
                            $src = (strpos((string) $val, 'data:image') === 0)
                                ? $val
                                : 'data:image/jpeg;base64,' . $val;
                            echo '<img src="' . HTML::chars($src) . '" alt="' . HTML::chars($col['label']) . '" '
                               . 'class="dbk-thumb" title="Click to enlarge">';
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
    <div class="dbk-nomatch">No records match the filter.</div>
</div>
