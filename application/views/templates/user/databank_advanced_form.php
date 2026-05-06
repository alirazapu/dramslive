<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php
// Variables expected (set by Controller_Databank::_form_view()):
//   $title, $subtitle, $breadcrumb
//   $ajax_url
//   $fields  — array of { name, label, placeholder }
//
// Renders an AdminLTE-styled multi-field filter form, an empty results
// panel below it, and inline JS that POSTs the form data as JSON to
// $ajax_url and dumps the returned HTML fragment into the panel.
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
            <h3 class="box-title">Filters</h3>
            <div class="box-tools pull-right">
                <small class="text-muted">All filters AND together. Leave a field blank to ignore it.</small>
            </div>
        </div>
        <div class="box-body">
            <form id="dbk_form" onsubmit="return dbkRunSearch();" autocomplete="off">
                <div class="row">
                    <?php foreach ($fields as $f): ?>
                        <div class="col-md-4 col-sm-6" style="margin-bottom:10px;">
                            <label for="dbk_<?php echo HTML::chars($f['name']); ?>" class="control-label">
                                <?php echo HTML::chars($f['label']); ?>
                            </label>
                            <input type="text"
                                   id="dbk_<?php echo HTML::chars($f['name']); ?>"
                                   name="<?php echo HTML::chars($f['name']); ?>"
                                   class="form-control dbk-field"
                                   placeholder="<?php echo HTML::chars(isset($f['placeholder']) ? $f['placeholder'] : ''); ?>"
                                   data-field="<?php echo HTML::chars($f['name']); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top:10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> Search
                    </button>
                    <button type="button" class="btn btn-default" onclick="dbkClear();">
                        <i class="fa fa-eraser"></i> Clear
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="box box-default" id="dbk_results_box" style="display:none;">
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
    var inputs = document.querySelectorAll('.dbk-field');
    var data = {};
    var any = false;
    for (var i = 0; i < inputs.length; i++) {
        var el = inputs[i];
        var v  = (el.value || '').replace(/^\s+|\s+$/g, '');
        if (v !== '') {
            data[el.dataset.field] = v;
            any = true;
        }
    }
    var box    = document.getElementById('dbk_results_box');
    var panel  = document.getElementById('dbk_results');
    var status = document.getElementById('dbk_status');
    box.style.display = 'block';
    if (!any) {
        panel.innerHTML = '<div class="text-warning" style="padding:8px;">'
            + 'Please fill in at least one field.</div>';
        status.textContent = '';
        return false;
    }
    panel.innerHTML    = '<div class="text-center text-muted" style="padding:20px;">'
        + '<i class="fa fa-spinner fa-spin"></i> Searching…</div>';
    status.textContent = '';

    $.ajax({
        url:  <?php echo json_encode($ajax_url); ?>,
        type: 'POST',
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

function dbkClear() {
    var inputs = document.querySelectorAll('.dbk-field');
    for (var i = 0; i < inputs.length; i++) inputs[i].value = '';
    document.getElementById('dbk_results_box').style.display = 'none';
}
</script>
