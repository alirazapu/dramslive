<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php
// Variables expected (set by Controller_Databank::_form_view()):
//   $title, $subtitle, $breadcrumb
//   $ajax_url
//   $hint    — optional line under the fields (how the search works)
//   $fields  — array of { name, label, placeholder } and optionally
//              type (text | select | checkbox) and options (select:
//              value => label)
//   $prefill — optional field name => value from the URL (e.g. links from
//              /user/search_person). When it holds a text value the
//              search runs as soon as the page loads.
//
// Renders a search hero (text fields as a large input, selects as pill
// groups, checkboxes as switches), an empty results panel below it, and
// inline JS that POSTs the field values to $ajax_url and puts the
// returned HTML fragment into the panel. Styles and result behaviour
// come from databank_assets.php (.dbk-*).
if (!isset($prefill) || !is_array($prefill)) {
    $prefill = array();
}
$dbk_pre = function ($name) use ($prefill) {
    return isset($prefill[$name]) ? (string) $prefill[$name] : '';
};
?>
<?php echo View::factory('templates/user/databank_assets')->render(); ?>

<section class="content-header">
    <h1>
        <?php echo HTML::chars($title); ?>
        <?php if (!empty($subtitle)): ?>
            <small><?php echo HTML::chars($subtitle); ?></small>
        <?php endif; ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">DRAMS Databank</a></li>
        <li class="active"><?php echo HTML::chars($breadcrumb); ?></li>
    </ol>
</section>

<section class="content dbk">
    <div class="dbk-hero">
        <div class="dbk-eyebrow"><i class="fa fa-database"></i> DRAMS Databank</div>
        <h2><?php echo HTML::chars($title); ?></h2>
        <p class="dbk-sub">One search across every connected source. Results are grouped by source.</p>

        <form id="dbk_form" onsubmit="return dbkRunSearch();" autocomplete="off">
            <?php
            $text_fields  = array();
            $other_fields = array();
            foreach ($fields as $f) {
                $type = isset($f['type']) ? $f['type'] : 'text';
                if ($type === 'text') {
                    $text_fields[] = $f;
                } else {
                    $other_fields[] = $f;
                }
            }
            ?>
            <div class="dbk-searchbar">
                <?php foreach ($text_fields as $i => $f): ?>
                    <div class="dbk-input-wrap">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        <input type="text"
                               id="dbk_<?php echo HTML::chars($f['name']); ?>"
                               name="<?php echo HTML::chars($f['name']); ?>"
                               class="dbk-input dbk-field"
                               aria-label="<?php echo HTML::chars($f['label']); ?>"
                               placeholder="<?php echo HTML::chars(isset($f['placeholder']) ? $f['placeholder'] : ''); ?>"
                               data-field="<?php echo HTML::chars($f['name']); ?>"
                               data-kind="text"
                               value="<?php echo HTML::chars($dbk_pre($f['name'])); ?>"
                               <?php echo $i === 0 ? 'autofocus' : ''; ?>>
                        <button type="button" class="dbk-input-clear" title="Clear" aria-label="Clear" onclick="dbkClear();">&times;</button>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="dbk-btn" id="dbk_submit">
                    <i class="fa fa-search"></i> Search
                </button>
            </div>

            <div class="dbk-controls">
                <?php foreach ($other_fields as $f): ?>
                    <?php $type = $f['type']; $fname = HTML::chars($f['name']); ?>
                    <?php if ($type === 'select'): ?>
                        <div role="radiogroup" aria-label="<?php echo HTML::chars($f['label']); ?>">
                            <span class="dbk-ctl-label"><?php echo HTML::chars($f['label']); ?></span>
                            <span class="dbk-pills">
                                <?php
                                $chosen = $dbk_pre($f['name']);
                                if (!array_key_exists($chosen, $f['options'])) {
                                    $chosen = (string) key($f['options']);
                                }
                                foreach ($f['options'] as $value => $text): ?>
                                    <label class="dbk-pill">
                                        <input type="radio" name="dbk_<?php echo $fname; ?>"
                                               class="dbk-field" data-field="<?php echo $fname; ?>" data-kind="choice"
                                               value="<?php echo HTML::chars($value); ?>" <?php echo (string) $value === $chosen ? 'checked' : ''; ?>>
                                        <span><?php echo HTML::chars($value === '' ? 'Auto' : $text); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </span>
                        </div>
                    <?php elseif ($type === 'checkbox'): ?>
                        <label class="dbk-switch">
                            <input type="checkbox" value="1" class="dbk-field"
                                   id="dbk_<?php echo $fname; ?>"
                                   data-field="<?php echo $fname; ?>" data-kind="flag"
                                   <?php echo $dbk_pre($f['name']) !== '' ? 'checked' : ''; ?>>
                            <span class="dbk-track" aria-hidden="true"></span>
                            <?php echo HTML::chars($f['label']); ?>
                        </label>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($hint)): ?>
                <p class="dbk-hint"><i class="fa fa-info-circle"></i><?php echo HTML::chars($hint); ?>
                    Press <span class="dbk-shortcut">/</span> to jump to the search box.</p>
            <?php endif; ?>
        </form>
    </div>

    <div id="dbk_results_box" style="display:none;">
        <div class="dbk-results-head">
            <h3>Results</h3>
            <span id="dbk_status" class="dbk-status" aria-live="polite"></span>
        </div>
        <div id="dbk_results"></div>
    </div>

</section>

<script>
function dbkRunSearch() {
    var inputs = document.querySelectorAll('.dbk-field');
    var data = {};
    var any = false;
    for (var i = 0; i < inputs.length; i++) {
        var el   = inputs[i];
        var kind = el.getAttribute('data-kind');
        var v;
        if (kind === 'flag')        v = el.checked ? '1' : '';
        else if (kind === 'choice') v = el.checked ? el.value : '';
        else                        v = (el.value || '').replace(/^\s+|\s+$/g, '');
        if (v !== '') {
            data[el.getAttribute('data-field')] = v;
            // A choice / flag alone is not a search.
            if (kind === 'text') any = true;
        }
    }
    var box    = document.getElementById('dbk_results_box');
    var panel  = document.getElementById('dbk_results');
    var status = document.getElementById('dbk_status');
    var btn    = document.getElementById('dbk_submit');
    box.style.display = 'block';
    if (!any) {
        panel.innerHTML = '<div class="dbk-alert dbk-alert-warning"><i class="fa fa-exclamation-circle"></i>'
            + '<div>Type a mobile number, CNIC, name or FIR number to search.</div></div>';
        status.textContent = '';
        return false;
    }
    var skel = '';
    for (var s = 0; s < 3; s++) {
        skel += '<div class="dbk-skel"><i style="width:30%"></i><i style="width:85%"></i><i style="width:60%"></i></div>';
    }
    panel.innerHTML    = skel;
    status.innerHTML   = '<i class="fa fa-spinner fa-spin"></i> Searching all sources…';
    btn.disabled       = true;
    var started        = new Date().getTime();

    $.ajax({
        url:  <?php echo json_encode($ajax_url); ?>,
        type: 'POST',
        data: data,
        success: function (html) {
            panel.innerHTML = html;
            var secs = ((new Date().getTime() - started) / 1000).toFixed(1);
            status.textContent = 'Finished in ' + secs + 's';
        },
        error: function () {
            panel.innerHTML = '<div class="dbk-alert dbk-alert-danger"><i class="fa fa-times-circle"></i>'
                + '<div>The search failed. Please try again.</div></div>';
            status.textContent = 'Error';
        },
        complete: function () { btn.disabled = false; }
    });
    return false;
}

function dbkClear() {
    var inputs = document.querySelectorAll('.dbk-field');
    for (var i = 0; i < inputs.length; i++) {
        var kind = inputs[i].getAttribute('data-kind');
        if (kind === 'flag') inputs[i].checked = false;
        else if (kind === 'choice') inputs[i].checked = (inputs[i].value === '');
        else inputs[i].value = '';
    }
    dbkSyncClear();
    document.getElementById('dbk_results_box').style.display = 'none';
    var first = document.querySelector('.dbk-input');
    if (first) first.focus();
}

function dbkSyncClear() {
    var wraps = document.querySelectorAll('.dbk-input-wrap');
    for (var i = 0; i < wraps.length; i++) {
        var inp = wraps[i].querySelector('input');
        wraps[i].querySelector('.dbk-input-clear').style.display = inp.value ? 'block' : 'none';
    }
}

(function () {
    document.addEventListener('input', function (e) {
        if (e.target.classList && e.target.classList.contains('dbk-input')) dbkSyncClear();
    });
    document.addEventListener('keydown', function (e) {
        var tag = (e.target.tagName || '').toLowerCase();
        if (e.key === '/' && tag !== 'input' && tag !== 'textarea' && tag !== 'select') {
            var first = document.querySelector('.dbk-input');
            if (first) { e.preventDefault(); first.focus(); first.select(); }
        }
    });
    // Opened with a value in the URL (e.g. from Search Person): search at once.
    dbkSyncClear();
    var first = document.querySelector('.dbk-input');
    if (first && first.value.replace(/^\s+|\s+$/g, '') !== '') {
        dbkRunSearch();
    }
})();
</script>
