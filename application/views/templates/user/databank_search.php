<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php
// Variables expected (all set by Controller_Databank::_search_view):
//   $title, $subtitle, $breadcrumb
//   $placeholder
//   $input_name             (e.g. 'q', 'search_value', 'cnic')
//   $input_select           (assoc array value=>label, empty for plain text input)
//   $input_select_name      (e.g. 'search_type'; ignored when $input_select empty)
//   $ajax_url               (full URL)
//   $ajax_method            ('GET' or 'POST')
//   $ajax_extra             (assoc array of fixed key/value pairs added to every request)
//   $help_text              (optional muted help line under the form)
//
// Renders an AdminLTE-styled search box, an empty results panel below it,
// and inline JS that wires the form to the AJAX endpoint and dumps the
// returned HTML fragment into the panel.
?>
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

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Search</h3>
        </div>
        <div class="box-body">
            <form id="dbk_form" onsubmit="return dbkRunSearch();" class="form-inline">
                <?php if (!empty($input_select)): ?>
                    <div class="form-group" style="margin-right:10px;">
                        <select id="dbk_select" class="form-control" required>
                            <?php foreach ($input_select as $val => $label): ?>
                                <option value="<?php echo HTML::chars($val); ?>"><?php echo HTML::chars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="form-group" style="margin-right:10px;">
                    <input type="text" id="dbk_input"
                           class="form-control"
                           placeholder="<?php echo HTML::chars($placeholder); ?>"
                           autocomplete="off"
                           style="min-width:400px;" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                </button>
            </form>
            <?php if (!empty($help_text)): ?>
                <p class="help-block" style="margin-top:10px;"><?php echo HTML::chars($help_text); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="box box-default" id="dbk_box" style="display:none;">
        <div class="box-header with-border">
            <h3 class="box-title">Results</h3>
            <div class="box-tools pull-right">
                <span id="dbk_status" class="text-muted"></span>
            </div>
        </div>
        <div class="box-body" id="dbk_results"></div>
    </div>
</section>

<script>
function dbkRunSearch() {
    var val = document.getElementById('dbk_input').value;
    if (val !== null) { val = val.toString().replace(/^\s+|\s+$/g, ''); }
    if (!val) { return false; }

    var data = {};
    data[<?php echo json_encode($input_name); ?>] = val;
    <?php if (!empty($input_select)): ?>
        data[<?php echo json_encode($input_select_name); ?>] = document.getElementById('dbk_select').value;
    <?php endif; ?>
    <?php if (!empty($ajax_extra)): ?>
        <?php foreach ($ajax_extra as $k => $v): ?>
            data[<?php echo json_encode($k); ?>] = <?php echo json_encode($v); ?>;
        <?php endforeach; ?>
    <?php endif; ?>

    var box    = document.getElementById('dbk_box');
    var panel  = document.getElementById('dbk_results');
    var status = document.getElementById('dbk_status');
    box.style.display  = 'block';
    panel.innerHTML    = '<div class="text-center text-muted" style="padding:20px;"><i class="fa fa-spinner fa-spin"></i> Searching…</div>';
    status.textContent = '';

    $.ajax({
        url:  <?php echo json_encode($ajax_url); ?>,
        type: <?php echo json_encode($ajax_method); ?>,
        data: data,
        success: function (html) {
            panel.innerHTML    = html;
            status.textContent = 'Done';
        },
        error: function () {
            panel.innerHTML    = '<div class="alert alert-danger">Search failed.</div>';
            status.textContent = 'Error';
        }
    });
    return false;
}
</script>
