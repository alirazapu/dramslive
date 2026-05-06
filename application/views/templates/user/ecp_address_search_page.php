<?php defined('SYSPATH') or die('No direct script access.'); ?>
<section class="content-header">
    <h1>
        ECP Address Search
        <small>Find ECP records by free-text in the address column</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">DRAMS Databank</a></li>
        <li class="active">ECP Address Search</li>
    </ol>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Search</h3>
        </div>
        <div class="box-body">
            <form id="ecp_addr_form" onsubmit="return runEcpAddrSearch();" class="form-inline">
                <div class="form-group" style="margin-right:10px; min-width:60%;">
                    <label for="ecp_addr_q" class="sr-only">Address text</label>
                    <input type="text" id="ecp_addr_q" name="q"
                           class="form-control"
                           placeholder="e.g. Karachi, Saddar, House #123…"
                           autocomplete="off"
                           style="min-width:400px;" required>
                </div>
                <div class="form-group" style="margin-right:10px;">
                    <label for="ecp_addr_limit" class="sr-only">Limit</label>
                    <select id="ecp_addr_limit" name="limit" class="form-control">
                        <option value="50">50 results</option>
                        <option value="100" selected>100 results</option>
                        <option value="250">250 results</option>
                        <option value="500">500 results</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                </button>
            </form>
            <p class="help-block" style="margin-top:10px;">
                Searches the <code>ecp_persons.address_text</code> column on the
                remote ECP database. Rows whose address has not yet been OCR'd
                from the image will not appear here &mdash; run
                <code>/cronjob/ecp_address_diagnostic</code> to check the
                backlog.
            </p>
        </div>
    </div>

    <div class="box box-default" id="ecp_addr_results_box" style="display:none;">
        <div class="box-header with-border">
            <h3 class="box-title">Results</h3>
            <div class="box-tools pull-right">
                <span id="ecp_addr_status" class="text-muted"></span>
            </div>
        </div>
        <div class="box-body" id="ecp_addr_results"></div>
    </div>
</section>

<script>
function runEcpAddrSearch() {
    var q = document.getElementById('ecp_addr_q').value.trim();
    var limit = document.getElementById('ecp_addr_limit').value;
    if (!q) { return false; }

    var box     = document.getElementById('ecp_addr_results_box');
    var panel   = document.getElementById('ecp_addr_results');
    var status  = document.getElementById('ecp_addr_status');

    box.style.display = 'block';
    panel.innerHTML   = '<div class="text-center text-muted" style="padding:20px;"><i class="fa fa-spinner fa-spin"></i> Searching…</div>';
    status.textContent = '';

    $.ajax({
        url:  "<?php echo URL::site('persons/ecp_address_search', TRUE); ?>",
        type: 'GET',
        data: { q: q, limit: limit },
        success: function (html) {
            panel.innerHTML = html;
            status.textContent = 'Done';
        },
        error: function () {
            panel.innerHTML = '<div class="alert alert-danger">Search failed. Try again or check the server logs.</div>';
            status.textContent = 'Error';
        }
    });
    return false;
}
</script>
