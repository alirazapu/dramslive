<?php
//if(Auth::instance()->get_user()->id!=419){
//echo '<pre>';
//echo 'please wait I am working on it';
//exit;

//}
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-rocket"></i>
       Advance Custom Request
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Advance Custom Request</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php
            $person_id = !empty($post['pid']) ? $post['pid'] : 0;
            $request = !empty($post['request']) ? $post['request'] : "";
            $requesttype = !empty($post['requesttype']) ? $post['requesttype'] : 0;
            $imeinumber = !empty($post['imei']) ? $post['imei'] : 0;
            $msisdn = !empty($post['msisdn']) ? $post['msisdn'] : 0;
            $cnic = !empty($post['cnic']) ? $post['cnic'] : 0;
            $redirect_url = !empty($post['url']) ? $post['url'] : URL::site('userdashboard/dashboard');
            //  echo $msisdn;
            ?>
            <div class="box box-primary">
                <div id="headerdiv" class="box-header with-border">
                    <h3 class="box-title">Request (FIR # <?php echo Helpers_Utilities::current_id("admin_reference_id"); ?>)</h3>
                    <a href="<?php echo $redirect_url;?>" class="btn btn-warning btn-small" style="float: right;"><i class="fa fa-backward"></i> Go Back</a>
                </div>
                <form class="ipf-form request_net" name="requestform" id="userrequest" method="post" enctype="multipart/form-data" >
                    <div class="box-body">
                        <div class="alert " id="upload" style="color: '#ff5b3c'; display: none">
                                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i>
                                <span id='parsresult'> Be Patient request in process
                                    <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php //echo URL::base() . 'dist/img/103.gif'; ?>">
                                </span></h4>
                        </div>
<!--                        <div class="alert" id="request_permission_check_status" style="color: '#ff5b3c'; display: none">
                            <h4><i class="icon fa fa-check"></i>
                                <span id='parsresult'> Be Patient ! Preparing Request....
                                    <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php //echo URL::base() . 'dist/img/103.gif'; ?>">
                                </span></h4>
                        </div>-->
<!--                        <div class="form-group col-md-6" id="notification_msgbasic0" style="display: none; min-height: 64px !important">
                            <div class="" id="notification_msgbasic1" style="display: none; margin-bottom: 1px !important">
                                <h4><div id="notification_msg_divbasic1"></div></h4>
                            </div>
                        </div>-->
<!--                        <div class="form-group col-md-6" id="company_inqueue0" style="display: none;min-height: 64px !important;" >
                            <div class="" id="company_inqueue" style="display: none; margin-bottom: 1px !important">
                                <h4 id="company_list">Requests in Queue </h4>
                            </div>
                        </div>-->

                        <input type="hidden" name="person_id" value="<?php echo $person_id; ?>" />
                        <input type="hidden" name="redirect_url" id="redirect_url" value="<?php echo $redirect_url; ?>" />
<!--                        <input type="hidden" id="ChooseTemplate" name="ChooseTemplate" value="1" placeholder="value 1 for cdr against mobile number" />-->
<!--                        <input type="hidden" id="ChooseTemplate" name="ChooseTemplate" value="2" placeholder="value 2 for cdr against IMEI number" />-->
<!--                        <input type="hidden" id="ChooseTemplate" name="ChooseTemplate" value="3" placeholder="3- Subscriber Against Mobile Number" />-->
<!--                        <input type="hidden" id="ChooseTemplate" name="ChooseTemplate" value="4" placeholder="value 4 for current location" />-->
<!--                        <input type="hidden" id="ChooseTemplate" name="ChooseTemplate" value="5" placeholder="value 5 for SIM's Against CNIC" />-->
                        <input type="hidden" id="startdate_duration" name="startdate_duration"/>
                        <input type="hidden" id="enddate_duration" name="enddate_duration"/>


                        

                <!-- Top row: Type → Company → Subject → Email Address.
                     Wrapped in a .row so the four col-sm-3 cells clear cleanly
                     and don't bleed into the conditional inputs that follow. -->
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="field">Please select request type</label>
                            <select class="form-control" name="ChooseTemplate" id="field">
                                <option value=""> Please Select Type</option>
                                <option value="3"> Subscriber Against Mobile Number</option>
                                <option value="4"> Location Against Mobile Number</option>
                                <option value="1"> CDR Against Mobile Number</option>
                                <option value="2"> CDR Against IMEI Number </option>
                                <option value="5"> SIM's Against CNIC Number</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3" id="company_div">
                        <div class="form-group">
                            <label for="company_name_get" class="control-label">Company Name</label>
                            <select class="form-control select2" name="company_name_get" id="company_name_get" data-placeholder="Select company from list" style="width: 100%">
                                <?php $comp_name_list = Helpers_Utilities::get_companies_data();
                                foreach ($comp_name_list as $list) {
                                    if ($list->company_id < 6 || $list->company_id == 9) { ?>
                                        <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                <?php }} ?>
                                <option value="11">PTCL</option>
                                <option value="12">International</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="esubject" class="control-label">Email Subject</label>
                            <div class="lockable-field">
                                <input type="text" class="form-control lockable-input" name="esubject" id="esubject" value="" placeholder="Email Subject" readonly>
                                <a href="javascript:void(0)" id="esubject_edit" class="lockable-toggle" title="Edit subject" onclick="toggleEditable('#esubject', '#esubject_edit')">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="emiladdress" class="control-label">Custom Email Adress</label>
                            <div class="lockable-field">
                                <input type="email" class="form-control lockable-input" name="emiladdress" id="emiladdress" value="" placeholder="Email Address" readonly>
                                <a href="javascript:void(0)" id="emiladdress_edit" class="lockable-toggle" title="Edit email address" onclick="toggleEditable('#emiladdress', '#emiladdress_edit')">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                            <!--
                                Conditional inputs — same set as the single
                                request form (Mobile / CNIC / IMEI + date range)
                                but with multi-value tag inputs so the admin can
                                lodge ONE custom request that covers many numbers.
                                Visibility is driven by request_against() in JS.

                                Each conditional field lives in its OWN .row so
                                Bootstrap 3 floats clear cleanly between
                                sections. The previous single-.row layout let
                                the chips field's bulk-upload toolbar bleed
                                visually into the Body Message label that
                                followed in the next .row, because mixed-height
                                columns inside one .row don't always clear.
                            -->

                            <!-- Mobile chips (full width when shown). -->
                            <div class="row" id="mob_row" style="display:none">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="inputSubNO" class="control-label">Mobile Number(s)</label>
                                        <select class="form-control select2-tags" name="inputSubNO[]" id="inputSubNO" multiple="multiple" data-placeholder="Type a number then press Enter (or comma)" style="width:100%"></select>
                                        <small class="text-muted">10-digit numbers starting with 3, e.g. 3001234567. Press Enter or comma to add more.</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Mobile bulk upload (separate row so icons/links never overlap). -->
                            <div class="row" id="mob_bulk_row" style="display:none">
                                <div class="col-sm-12">
                                    <div class="bulk-upload-row">
                                        <label class="btn btn-default btn-xs bulk-upload-btn">
                                            <i class="fa fa-upload"></i> Bulk Upload (CSV / XLSX)
                                            <input type="file" id="mobileBulkFile" accept=".csv,.tsv,.txt,.xlsx,.xls" style="display:none">
                                        </label>
                                        <a href="<?php echo URL::site('Adminrequest/sample_csv?type=mobile'); ?>" class="btn btn-link btn-xs" target="_blank">
                                            <i class="fa fa-download"></i> Sample format
                                        </a>
                                        <span id="mobileBulkStatus" class="text-muted bulk-upload-status"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- CNIC chips. -->
                            <div class="row" id="cnic_row" style="display:none">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="inputCNIC" class="control-label">CNIC Number(s)</label>
                                        <select class="form-control select2-tags" name="inputCNIC[]" id="inputCNIC" multiple="multiple" data-placeholder="Type a CNIC then press Enter" style="width:100%"></select>
                                        <small class="text-muted">13-digit CNIC, e.g. 1234512345671.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="cnic_bulk_row" style="display:none">
                                <div class="col-sm-12">
                                    <div class="bulk-upload-row">
                                        <label class="btn btn-default btn-xs bulk-upload-btn">
                                            <i class="fa fa-upload"></i> Bulk Upload (CSV / XLSX)
                                            <input type="file" id="cnicBulkFile" accept=".csv,.tsv,.txt,.xlsx,.xls" style="display:none">
                                        </label>
                                        <a href="<?php echo URL::site('Adminrequest/sample_csv?type=cnic'); ?>" class="btn btn-link btn-xs" target="_blank">
                                            <i class="fa fa-download"></i> Sample format
                                        </a>
                                        <span id="cnicBulkStatus" class="text-muted bulk-upload-status"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- IMEI chips. -->
                            <div class="row" id="imei_row" style="display:none">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="inputIMEI" class="control-label">IMEI Number(s)</label>
                                        <select class="form-control select2-tags" name="inputIMEI[]" id="inputIMEI" multiple="multiple" data-placeholder="Type an IMEI then press Enter" style="width:100%"></select>
                                        <small class="text-muted">15-digit IMEI.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="imei_bulk_row" style="display:none">
                                <div class="col-sm-12">
                                    <div class="bulk-upload-row">
                                        <label class="btn btn-default btn-xs bulk-upload-btn">
                                            <i class="fa fa-upload"></i> Bulk Upload (CSV / XLSX)
                                            <input type="file" id="imeiBulkFile" accept=".csv,.tsv,.txt,.xlsx,.xls" style="display:none">
                                        </label>
                                        <a href="<?php echo URL::site('Adminrequest/sample_csv?type=imei'); ?>" class="btn btn-link btn-xs" target="_blank">
                                            <i class="fa fa-download"></i> Sample format
                                        </a>
                                        <span id="imeiBulkStatus" class="text-muted bulk-upload-status"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Dates row: Date From / Date To / Quick Options inline. -->
                            <div class="row" id="dates_row" style="display:none">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="startDate" class="control-label">Date From (mm/dd/yyyy)</label>
                                        <input type="text" class="form-control" name="startDate" id="startDate" value="" placeholder="mm/dd/yyyy">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="endDate" class="control-label">Date To (mm/dd/yyyy)</label>
                                        <input type="text" class="form-control" name="endDate" id="endDate" value="" placeholder="mm/dd/yyyy">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Quick Options</label>
                                        <div>
                                            <button type="button" onclick="dateonemonth()"    class="btn btn-primary btn-sm">Last 30 Days</button>
                                            <button type="button" onclick="datetwomonths()"   class="btn btn-primary btn-sm">Last 60 Days</button>
                                            <button type="button" onclick="datethreemonths()" class="btn btn-primary btn-sm">Last 90 Days</button>
                                            <button type="button" onclick="datesixmonths()"   class="btn btn-primary btn-sm">Last 180 Days</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                            <div class="col-sm-12" id="quickoption_div"  >
                                <div class="form-group" >
                                    <label for="quickoption" class="control-label">Body Message</label>                                                                        
                                    <textarea id="body_txt" value=""  name="body" class="textarea form-control" placeholder="Please enter email body heare" style="width: 100%; height: 300px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                                </div>
                            </div>
                            </div><!-- /.row (body) -->

                            <!-- Requested By + Requested Attachment side-by-side
                                 (col-sm-6 each). Email Attachment is hidden but
                                 kept in markup so action_admincustomsend keeps
                                 reading $_FILES['emailfile'] without PHP changes —
                                 the bulk-format builder + Ufone .txt-attachment
                                 logic in send_email() handle the attachment case
                                 server-side. -->
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="rqtbyname" class="control-label">Requested By</label>
                                        <input type="text" class="form-control" name="rqtbyname" id="rqtbyname" value="" placeholder="Name">
                                        <div id="rqtbynamelist"></div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="rqtfile" class="control-label">Requested Attachment</label>
                                        <input type="file" accept=".jpeg,.jpg,.gif,.png" id="rqtfile" name="rqtfile" placeholder="Select Image">
                                    </div>
                                </div>
                                <div class="col-sm-6" style="display:none">
                                    <div class="form-group">
                                        <label for="emailfile" class="control-label">Email Attachment (auto-managed)</label>
                                        <input type="file" accept=".txt" id="emailfile" name="emailfile" placeholder="Select text file">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="inputreason" class="control-label">Reason For This Request</label>
                                        <textarea class="form-control" name="inputreason" id="inputreason" placeholder="Enter Reason For Request"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="submit_div">
                                <div class="col-sm-12">
                                    <button id="userrequestbtn" type="button" onclick="submitrequestform()" class="btn btn-primary pull-right" style="margin-top:10px"><i class="fa fa-eye"></i> Preview</button>
                                </div>
                            </div>
                        <!--</div>-->
                    </div>
                </form>
            </div>
            <!--col-md-12-->
        </div>
    </div>

</section>
<!-- /.content -->

<!--
    Confirm-Before-Send modal.

    submitrequestform() validates the form against the bulk-template rules
    (per-telco max counts, MSISDN format, IMEI digit count, date sanity)
    and, if everything looks good, populates this modal with the final
    Subject + To + Body for the admin to eyeball before the actual AJAX
    submission goes out. Hitting "Send" triggers actuallySendRequest().
-->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="previewModalLabel"><i class="fa fa-paper-plane"></i> Confirm Email Send</h4>
            </div>
            <div class="modal-body">
                <div class="preview-meta">
                    <div><strong>To:</strong> <span id="previewTo"></span></div>
                    <div><strong>Subject:</strong> <span id="previewSubject"></span></div>
                </div>
                <hr>
                <div><strong>Body:</strong></div>
                <div id="previewBody"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmSendBtn"><i class="fa fa-paper-plane"></i> Submit Request</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>
<script>
    $('#userrequest').one('submit', function () {
        $(this).find('input[type="submit"]').attr('disabled', 'disabled');
    });
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });

    /**
     * Toggle the readonly state of an input + flip the pencil/save icon.
     * Used by the "Email Subject" and "Custom Email Adress" fields so the
     * default behaviour is auto-filled-and-locked, but the admin can still
     * override either value with one click.
     */
    function toggleEditable(inputSelector, buttonSelector) {
        var $input  = $(inputSelector);
        var $button = $(buttonSelector);
        var $icon   = $button.find('i');
        if ($input.attr('readonly')) {
            $input.removeAttr('readonly').focus();
            $icon.removeClass('fa-pencil').addClass('fa-lock');
            $button.attr('title', 'Lock (revert to auto-fill on next change)');
        } else {
            $input.attr('readonly', 'readonly');
            $icon.removeClass('fa-lock').addClass('fa-pencil');
            $button.attr('title', 'Edit subject');
        }
    }

    /**
     * Mirror of admin_request_sent_form.php's request_against() — show/hide
     * the conditional input divs based on the selected request type.
     *
     * Type → fields (kept identical to the single-request form):
     *   1 = CDR by Mobile        → mobile + dates
     *   2 = CDR by IMEI          → imei   + dates
     *   3 = Subscriber by Mobile → mobile (no dates)
     *   4 = Location by Mobile   → mobile (no dates)
     *   5 = SIMs by CNIC         → cnic   (no dates)
     */
    function request_against(val) {
        var t = (val && val.value !== undefined) ? String(val.value) : String($('#field').val() || '');

        var showMobile = (t === '1' || t === '3' || t === '4');
        var showCnic   = (t === '5');
        var showImei   = (t === '2');
        var showDates  = (t === '1' || t === '2');

        // Each conditional input now lives in its own .row for clean
        // float-clearing — chip row + bulk-upload row are sibling .rows.
        $('#mob_row, #mob_bulk_row').toggle(showMobile);
        $('#cnic_row, #cnic_bulk_row').toggle(showCnic);
        $('#imei_row, #imei_bulk_row').toggle(showImei);
        $('#dates_row').toggle(showDates);

        // Clear inputs that are no longer relevant so stray values don't
        // leak into the rebuilt body.
        if (!showMobile) $('#inputSubNO').val(null).trigger('change.select2');
        if (!showCnic)   $('#inputCNIC').val(null).trigger('change.select2');
        if (!showImei)   $('#inputIMEI').val(null).trigger('change.select2');
        if (!showDates) {
            $('#startDate').val('');
            $('#endDate').val('');
        }

        // Re-render the body against the new layout.
        refreshAdminTemplateFields();
    }

    /**
     * Format a Date object as DD<sep>MM<sep>YYYY (sep ∈ '.', '/', '-') OR
     * MM/DD/YYYY when 'mdy' is requested. Mirrors the date-format set the
     * single-flow controller (action_adminsend) hands to str_replace.
     */
    function fmtDate(d, format) {
        if (!d) return '';
        var dd = ('0' + d.getDate()).slice(-2);
        var mm = ('0' + (d.getMonth() + 1)).slice(-2);
        var yyyy = d.getFullYear();
        switch (format) {
            case 'dot':       return dd + '.' + mm + '.' + yyyy;
            case 'slash':     return dd + '/' + mm + '/' + yyyy;
            case 'hyphen':    return dd + '-' + mm + '-' + yyyy;
            case 'slash_mdy': return mm + '/' + dd + '/' + yyyy;
        }
        return '';
    }

    /** Parse 'mm/dd/yyyy' into a Date, or return null. */
    function parseMdy(value) {
        if (!value) return null;
        // Anchorless match — bootstrap-datetimepicker sometimes appends a
        // time component (e.g. "04/30/2026 12:00") and the previous
        // ^...$ regex rejected anything that wasn't a bare mm/dd/yyyy.
        var m = /(\d{1,2})\/(\d{1,2})\/(\d{4})/.exec(String(value));
        if (!m) return null;
        var month = parseInt(m[1], 10);
        var day   = parseInt(m[2], 10);
        var year  = parseInt(m[3], 10);
        // Calendar sanity — JS Date silently rolls over invalid dates
        // (e.g. Feb 30 becomes Mar 2), so cross-check the components.
        var d = new Date(year, month - 1, day);
        if (d.getFullYear() !== year || d.getMonth() !== month - 1 || d.getDate() !== day) {
            return null;
        }
        return d;
    }

    /* =================================================================
        BULK-REQUEST EMAIL BODY BUILDERS

        Source-of-truth for the formats below:
          C:\Users\ali_r\Desktop\Bulk CDR sending format.docx
          C:\Users\ali_r\Desktop\Bulk IMEI sending format.docx
        plus Ufone .txt samples the user provided directly in chat.

        These telcos parse the body programmatically, so their formats
        are strict. The builders take the form's current inputs and
        produce the exact body string each LEA team expects.
       ================================================================= */

    /** company_id → human-readable name (used in validation error messages). */
    var TELCO_NAME = { '1': 'Mobilink (Jazz)', '3': 'Ufone', '4': 'Zong', '6': 'Telenor' };

    /** Normalise a Pakistani mobile number to '92xxxxxxxxxx' (12 digits). */
    function toMsisdn92(num) {
        var d = String(num).replace(/\D/g, '');
        if (d.length === 10 && d.charAt(0) === '3')   return '92' + d;
        if (d.length === 11 && d.charAt(0) === '0')   return '92' + d.substr(1);
        if (d.length === 12 && d.substr(0, 2) === '92') return d;
        if (d.length === 13 && d.substr(0, 4) === '0092') return d.substr(2);
        return d;
    }

    /** dd<sep>mm<sep>yyyy or mm/dd/yyyy depending on order='mdy'. */
    function fmtDmy(d, sep, order) {
        if (!d) return '';
        var dd = ('0' + d.getDate()).slice(-2);
        var mm = ('0' + (d.getMonth() + 1)).slice(-2);
        var yy = d.getFullYear();
        if (order === 'mdy') return mm + sep + dd + sep + yy;
        return dd + sep + mm + sep + yy;
    }

    /* ---- CDR by MSISDN ---- */

    function bulkCdrMobilink(mobiles, sd, ed) {
        // Jazz/Mobilink: HTML table with each cell formatted A;<msisdn>;dd/mm/yyyy;dd/mm/yyyy;
        var rows = mobiles.map(function (m, i) {
            return '<tr><td>' + (i + 1) + '</td>' +
                   '<td>ADM-[case_number]</td>' +
                   '<td>A;' + toMsisdn92(m) + ';' + fmtDmy(sd, '/') + ';' + fmtDmy(ed, '/') + ';</td></tr>';
        }).join('');
        return '<p>Dear Sir/Madam,</p>' +
               '<p>Please provide the requested CDR &amp; SMS log for the numbers below.</p>' +
               '<table border="1" cellpadding="6" cellspacing="0">' +
                 '<thead><tr><th>S.NO</th><th>FIR/DD NO</th><th>REQUIRED</th></tr></thead>' +
                 '<tbody>' + rows + '</tbody>' +
               '</table>' +
               '<p>FIR/DD NO ADM-[case_number] PS CTD KPK</p>';
    }

    function bulkCdrTelenor(mobiles, sd, ed) {
        // Tpn:<92xxxxxxxxxx>,<...>:dd-mm-yyyy:dd-mm-yyyy:
        // No trailing comma. Numbers must start with 92.
        return 'Tpn:' + mobiles.map(toMsisdn92).join(',') +
               ':' + fmtDmy(sd, '-') + ':' + fmtDmy(ed, '-') + ':';
    }

    function bulkCdrZong(mobiles, sd, ed) {
        var rows = mobiles.map(function (m, i) {
            return '<tr><td>' + (i + 1) + '</td>' +
                   '<td>ADM-[case_number]</td>' +
                   '<td>' + toMsisdn92(m) + '</td>' +
                   '<td>' + fmtDmy(sd, '/') + ' to ' + fmtDmy(ed, '/') + '</td></tr>';
        }).join('');
        return '<p>Dear Sir/Madam,</p>' +
               '<p>Please provide the requested CDR &amp; SMS log for the numbers below.</p>' +
               '<table border="1" cellpadding="6" cellspacing="0">' +
                 '<thead><tr><th>S.NO</th><th>FIR/DD NO</th><th>NUMBER</th><th>REQUIRED CDR &amp; SMS LOG</th></tr></thead>' +
                 '<tbody>' + rows + '</tbody>' +
               '</table>' +
               '<p>FIR/DD NO ADM-[case_number] PS CTD KPK</p>';
    }

    function bulkCdrUfone(mobiles, sd, ed) {
        // Pipe-separated, mm/dd/yyyy, MSISDNs joined by ':' (no trailing colon).
        // Sample from the user: "MSISDN|Both|05/01/2025|04/27/2026|923365350875:923275835163"
        return 'MSISDN|Both|' + fmtDmy(sd, '/', 'mdy') + '|' + fmtDmy(ed, '/', 'mdy') + '|' +
               mobiles.map(toMsisdn92).join(':');
    }

    /* ---- CDR by IMEI ---- */

    function bulkImeiMobilink(imeis, sd, ed) {
        // Jazz/Mobilink: 14-digit IMEI, table cell I;<imei14>;dd/mm/yyyy;dd/mm/yyyy;
        var rows = imeis.map(function (im, i) {
            var d = String(im).replace(/\D/g, '').substr(0, 14);
            return '<tr><td>' + (i + 1) + '</td>' +
                   '<td>ADM-[case_number]</td>' +
                   '<td>I;' + d + ';' + fmtDmy(sd, '/') + ';' + fmtDmy(ed, '/') + ';</td></tr>';
        }).join('');
        return '<p>Dear Sir/Madam,</p>' +
               '<p>Please provide the CDR for the IMEIs below.</p>' +
               '<table border="1" cellpadding="6" cellspacing="0">' +
                 '<thead><tr><th>S/NO</th><th>FIR/DD NO</th><th>Required</th></tr></thead>' +
                 '<tbody>' + rows + '</tbody>' +
               '</table>' +
               '<p>FIR/DD NO ADM-[case_number] PS CTD KPK</p>';
    }

    function bulkImeiTelenor(imeis, sd, ed) {
        // Tpi:14-digit,14-digit,...:dd-mm-yyyy:dd-mm-yyyy:
        var ids = imeis.map(function (im) { return String(im).replace(/\D/g, '').substr(0, 14); }).join(',');
        return 'Tpi:' + ids + ':' + fmtDmy(sd, '-') + ':' + fmtDmy(ed, '-') + ':';
    }

    function bulkImeiZong(imeis, sd, ed) {
        // Zong: 15-digit IMEI, with the trailing "," before the colon kept
        // verbatim per the docx sample ("353535,:10-05-2025:30-04-2026:").
        var ids = imeis.map(function (im) {
            var d = String(im).replace(/\D/g, '');
            return d.length >= 15 ? d.substr(0, 15) : d;
        }).join(',');
        return 'Tpi:' + ids + ',:' + fmtDmy(sd, '-') + ':' + fmtDmy(ed, '-') + ':';
    }

    function bulkImeiUfone(imeis, sd, ed) {
        // Pipe-separated, mm/dd/yyyy, IMEIs colon-joined.
        // Ufone requires 15-digit IMEI with last digit forced to 0.
        var ids = imeis.map(function (im) {
            var d = String(im).replace(/\D/g, '');
            if (d.length >= 15) d = d.substr(0, 15);
            if (d.length === 14) d = d + '0';
            if (d.length === 15 && d.charAt(14) !== '0') d = d.substr(0, 14) + '0';
            return d;
        }).join(':');
        return 'IMEI|Both|' + fmtDmy(sd, '/', 'mdy') + '|' + fmtDmy(ed, '/', 'mdy') + '|' + ids;
    }

    /**
     * Dispatch to the right bulk builder based on (request_type, company).
     * Returns the body string for CDR/IMEI bulk requests, or null if the
     * combination doesn't have a defined bulk format (in which case the
     * caller falls back to the standard template substitution path).
     */
    function buildBulkBody() {
        var requestType = $('#field').val();
        var companyName = $('#company_name_get').val();
        var sd = parseMdy($('#startDate').val());
        var ed = parseMdy($('#endDate').val());
        var mobiles = $('#inputSubNO').val() || [];
        var imeis   = $('#inputIMEI').val() || [];

        // Bulk formats we have are CDR-by-mobile (1) and CDR-by-IMEI (2).
        if (requestType !== '1' && requestType !== '2') return null;
        if (!sd || !ed) return null;

        if (requestType === '1') {
            if (mobiles.length === 0) return null;
            switch (companyName) {
                case '1': return bulkCdrMobilink(mobiles, sd, ed);
                case '6': return bulkCdrTelenor(mobiles, sd, ed);
                case '4': return bulkCdrZong(mobiles, sd, ed);
                case '3': return bulkCdrUfone(mobiles, sd, ed);
            }
        }
        if (requestType === '2') {
            if (imeis.length === 0) return null;
            switch (companyName) {
                case '1': return bulkImeiMobilink(imeis, sd, ed);
                case '6': return bulkImeiTelenor(imeis, sd, ed);
                case '4': return bulkImeiZong(imeis, sd, ed);
                case '3': return bulkImeiUfone(imeis, sd, ed);
            }
        }
        return null;
    }

    /**
     * Return an array of human-readable error strings for the current form
     * inputs, keyed off the bulk-template requirements (per-telco max
     * counts, MSISDN format, IMEI digit count, date sanity).
     * Empty array == ready to send.
     */
    function validateBulkRequest() {
        var requestType = $('#field').val();
        var companyName = $('#company_name_get').val();
        var mobiles = $('#inputSubNO').val() || [];
        var cnics   = $('#inputCNIC').val() || [];
        var imeis   = $('#inputIMEI').val() || [];
        var sd = parseMdy($('#startDate').val());
        var ed = parseMdy($('#endDate').val());
        var errors = [];

        if (!requestType) errors.push('Please select a request type.');
        if (!companyName) errors.push('Please select a company.');

        // CDR types need dates.
        if (requestType === '1' || requestType === '2') {
            if (!sd) errors.push('Please enter a valid Date From (mm/dd/yyyy).');
            if (!ed) errors.push('Please enter a valid Date To (mm/dd/yyyy).');
            if (sd && ed && sd > ed) errors.push('Date From must be earlier than Date To.');
            // Telcos cap CDR/IMEI windows at 180 days. Use ceil so a 181-day
            // span is rejected (still rounds to "181 days" in the message).
            if (sd && ed && sd <= ed) {
                var diffDays = Math.ceil((ed.getTime() - sd.getTime()) / 86400000) + 1;
                if (diffDays > 180) {
                    errors.push('Date range cannot exceed 180 days. Selected: ' + diffDays + ' days.');
                }
            }
        }

        // CDR by Mobile — count + format.
        if (requestType === '1') {
            var maxMobile = (companyName === '4') ? 25 : 10;  // Zong = 25, others = 10
            if (mobiles.length === 0) {
                errors.push('Please add at least one mobile number.');
            } else if (mobiles.length > maxMobile) {
                errors.push('Maximum ' + maxMobile + ' numbers allowed for ' +
                            (TELCO_NAME[companyName] || 'this telco') +
                            '. You entered ' + mobiles.length + '.');
            }
            mobiles.forEach(function (m) {
                var d = String(m).replace(/\D/g, '');
                var ok = (d.length === 10 && d.charAt(0) === '3') ||
                         (d.length === 11 && d.substr(0, 2) === '03') ||
                         (d.length === 12 && d.substr(0, 2) === '92') ||
                         (d.length === 13 && d.substr(0, 4) === '0092');
                if (!ok) errors.push('Invalid mobile number: ' + m);
            });
        }

        // CDR by IMEI — count + digit length.
        if (requestType === '2') {
            if (imeis.length === 0) {
                errors.push('Please add at least one IMEI.');
            } else if (imeis.length > 10) {
                errors.push('Maximum 10 IMEIs allowed. You entered ' + imeis.length + '.');
            }
            imeis.forEach(function (im) {
                var d = String(im).replace(/\D/g, '');
                if (d.length < 14 || d.length > 16) {
                    errors.push('Invalid IMEI: ' + im + ' (must be 14, 15, or 16 digits).');
                }
            });
        }

        // SIMs by CNIC.
        if (requestType === '5') {
            if (cnics.length === 0) {
                errors.push('Please add at least one CNIC.');
            }
            cnics.forEach(function (c) {
                var d = String(c).replace(/\D/g, '');
                if (d.length !== 13) errors.push('Invalid CNIC: ' + c + ' (must be 13 digits).');
            });
        }

        // Subscriber/Location by mobile.
        if (requestType === '3' || requestType === '4') {
            if (mobiles.length === 0) {
                errors.push('Please add at least one mobile number.');
            }
            mobiles.forEach(function (m) {
                var d = String(m).replace(/\D/g, '');
                var ok = (d.length === 10 && d.charAt(0) === '3') ||
                         (d.length === 11 && d.substr(0, 2) === '03') ||
                         (d.length === 12 && d.substr(0, 2) === '92');
                if (!ok) errors.push('Invalid mobile number: ' + m);
            });
        }

        return errors;
    }

    /**
     * Substitute every templating placeholder the standard email_templates
     * use against the values currently in the form — the same set of
     * placeholders that action_adminsend handles server-side, so what the
     * admin sees in the body editor matches what would be sent if this
     * were a single request.
     *
     * For multi-value inputs (mobile / cnic / imei) we join entries with
     * ", " — telcos accept comma-separated lists in templates that use a
     * single [mobile_number] (etc.) placeholder.
     */
    function applyTemplatePlaceholders(text) {
        if (!text) return '';

        var mobiles = $('#inputSubNO').val() || [];
        var cnics   = $('#inputCNIC').val() || [];
        var imeis   = $('#inputIMEI').val() || [];
        var startDate = parseMdy($('#startDate').val());
        var endDate   = parseMdy($('#endDate').val());

        // Multi-value fields — join with comma+space.
        if (mobiles.length) text = text.replace(/\[mobile_number\]/g, mobiles.join(', '));
        if (cnics.length)   text = text.replace(/\[cnic_number\]/g,   cnics.join(', '));
        if (imeis.length)   text = text.replace(/\[imei_number\]/g,   imeis.join(', '));

        // Date placeholders — only substitute if the date is set, so
        // an empty date doesn't replace the placeholder with nothing.
        if (startDate) {
            text = text.replace(/\[start_date_dot\]/g,       fmtDate(startDate, 'dot'));
            text = text.replace(/\[start_date_slash\]/g,     fmtDate(startDate, 'slash'));
            text = text.replace(/\[start_date_hyphen\]/g,    fmtDate(startDate, 'hyphen'));
            text = text.replace(/\[start_date_slash_mdy\]/g, fmtDate(startDate, 'slash_mdy'));
        }
        if (endDate) {
            text = text.replace(/\[end_date_dot\]/g,       fmtDate(endDate, 'dot'));
            text = text.replace(/\[end_date_slash\]/g,     fmtDate(endDate, 'slash'));
            text = text.replace(/\[end_date_hyphen\]/g,    fmtDate(endDate, 'hyphen'));
            text = text.replace(/\[end_date_slash_mdy\]/g, fmtDate(endDate, 'slash_mdy'));
        }

        // current_date — admin always wants today.
        var today = new Date();
        text = text.replace(/\[current_date\]/g, fmtDate(today, 'slash'));

        // [case_number] / ADM-[case_number] — preserve the placeholder so
        // the admin can see the ADM- prefix; the server's preg_replace
        // swaps it for ADM-<reference_id> at send time.
        text = text.replace(/(ADM-)?\[case_number\]/g, function (m, prefix) {
            return prefix ? m : 'ADM-[case_number]';
        });

        return text;
    }

    /** Cache the latest server-side validation errors so submitrequestform()
     *  can echo them when the admin tries to send. */
    var lastServerErrors = [];

    /**
     * Fetch the canonical bulk body + subject + recipient email from the
     * server (Adminrequest/build_bulk_body) for the currently-selected
     * (request_type, company, mobiles, cnics, imeis, dates) tuple. The
     * server is the source of truth: it owns the per-telco bulk format
     * specs, the per-telco max counts, and the standard template subjects
     * so admin-typed inputs can't accidentally drift from what the LEA
     * team's parser expects.
     */
    function refreshAdminTemplateFields() {
        var requestType = $('#field').val();
        var companyName = $('#company_name_get').val();

        if (!requestType || !companyName) {
            return;
        }

        $.ajax({
            url: "<?php echo URL::site('Adminrequest/build_bulk_body'); ?>",
            type: 'POST',
            dataType: 'json',
            // PHP requires the [] suffix to land array-typed POST values
            // in $_POST['mobiles'] etc. as an array.
            traditional: false,
            data: {
                request_type:  requestType,
                company_name:  companyName,
                'mobiles[]':   ($('#inputSubNO').val() || []),
                'cnics[]':     ($('#inputCNIC').val()  || []),
                'imeis[]':     ($('#inputIMEI').val()  || []),
                start_date:    $('#startDate').val() || '',
                end_date:      $('#endDate').val()   || ''
            },
            success: function (resp) {
                if (!resp) return;

                lastServerErrors = (resp.errors || []);

                // Subject — only refresh if the field is still readonly.
                // Apply local placeholder substitution as well (e.g. swap
                // [case_number] → ADM-[case_number] for display) so the
                // admin sees the prefix that drives reply matching.
                if (typeof resp.subject !== 'undefined' && $('#esubject').attr('readonly')) {
                    $('#esubject').val(applyTemplatePlaceholders(resp.subject || ''));
                }

                // Recipient email — only refresh if still readonly.
                if (typeof resp.email !== 'undefined' && $('#emiladdress').attr('readonly')) {
                    $('#emiladdress').val(resp.email || '');
                }

                // Body — server returns the canonical bulk-format string
                // for combinations that have one defined, otherwise the
                // standard template body with single-request placeholders
                // already substituted. We just push it into the editor.
                if (typeof resp.body !== 'undefined') {
                    var bodyHtml = resp.body || '';
                    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.body_txt) {
                        CKEDITOR.instances.body_txt.setData(bodyHtml);
                    } else {
                        $('#body_txt').val(bodyHtml);
                    }
                }
            },
            error: function () {
                // Non-fatal — keep whatever the user already had typed.
            }
        });
    }

    /**
     * Wire a Bulk Upload control to a select2-tags input.
     * On file pick: POSTs to Adminrequest/parse_bulk_upload, the server
     * reads the first column (CSV / TSV / TXT / XLSX / XLS), normalises
     * the values, drops invalids, and returns a deduped list. We then
     * push each value into the target select2 as a selected option.
     */
    function wireBulkUpload(fieldType, fileInputSel, targetSelectSel, statusSel) {
        $(fileInputSel).on('change', function () {
            if (!this.files || !this.files[0]) return;

            var formData = new FormData();
            formData.append('bulk_file', this.files[0]);
            formData.append('field_type', fieldType);

            var $status = $(statusSel);
            $status.text('Uploading…').removeClass('text-danger').addClass('text-muted');

            var fileInputEl = this;

            $.ajax({
                url: "<?php echo URL::site('Adminrequest/parse_bulk_upload'); ?>",
                type: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (resp) {
                    if (!resp || resp.error) {
                        var msg = (resp && resp.error) ? resp.error : 'unknown';
                        var human = {
                            'auth':           'Session expired. Please reload.',
                            'invalid_type':   'Internal error: invalid field type.',
                            'no_file':        'No file selected.',
                            'file_too_large': 'File is larger than 2 MB.',
                            'parse_failed':   'Could not parse the file. Please use the sample format.'
                        }[msg] || ('Upload failed: ' + msg);
                        swal('Bulk Upload', human, 'error');
                        $status.text('').removeClass('text-muted').addClass('text-danger');
                        fileInputEl.value = '';
                        return;
                    }

                    var values = resp.values || [];
                    var $sel   = $(targetSelectSel);
                    values.forEach(function (v) {
                        if (!$sel.find('option[value="' + v + '"]').length) {
                            $sel.append(new Option(v, v, true, true));
                        } else {
                            // Already present — make sure it's marked selected.
                            $sel.find('option[value="' + v + '"]').prop('selected', true);
                        }
                    });
                    $sel.trigger('change');

                    var summary = 'Added ' + values.length + ' value(s).';
                    if (resp.invalid_count > 0) {
                        summary += ' Skipped ' + resp.invalid_count + ' invalid';
                        if (resp.invalid_samples && resp.invalid_samples.length) {
                            summary += ' (e.g. ' + resp.invalid_samples.slice(0, 3).join(', ') + ')';
                        }
                        summary += '.';
                    }
                    $status.text(summary);

                    // Reset so the same file can be re-uploaded after editing.
                    fileInputEl.value = '';

                    // Body live-preview reflects the new entries.
                    refreshAdminTemplateFields();
                },
                error: function () {
                    swal('Bulk Upload', 'Could not reach the server.', 'error');
                    $status.text('').removeClass('text-muted').addClass('text-danger');
                    fileInputEl.value = '';
                }
            });
        });
    }

    /* =================================================================
        Input validators — block bad MSISDN / CNIC / IMEI values before
        they ever become a chip. Each returns either the cleaned digit-
        only string (chip allowed) or null (chip rejected with a brief
        flash message under the field).
       ================================================================= */

    function validateMobileInput(raw) {
        var d = String(raw).replace(/\D/g, '');
        // Accept 10-digit 3xxxxxxxxx, 11-digit 03xxxxxxxxx,
        // 12-digit 92xxxxxxxxxx, 13-digit 0092xxxxxxxxxx.
        if (d.length === 10 && d.charAt(0) === '3')                return d;
        if (d.length === 11 && d.substr(0, 2) === '03')            return d;
        if (d.length === 12 && d.substr(0, 2) === '92')            return d;
        if (d.length === 13 && d.substr(0, 4) === '0092')          return d;
        return null;
    }
    function validateCnicInput(raw) {
        var d = String(raw).replace(/\D/g, '');
        // Pakistani CNIC = exactly 13 digits (dashes / spaces tolerated).
        return (d.length === 13) ? d : null;
    }
    function validateImeiInput(raw) {
        var d = String(raw).replace(/\D/g, '');
        // IMEI = 14 (Mobilink/Telenor), 15 (Zong/Ufone) or 16 digits.
        return (d.length >= 14 && d.length <= 16) ? d : null;
    }

    /** Brief inline flash beneath the input when an entry is rejected. */
    function flashTagReject($field, message) {
        var $small = $field.parent().find('.tag-reject-flash');
        if (!$small.length) {
            $small = $('<small class="tag-reject-flash text-danger" style="display:block;margin-top:4px;"></small>');
            $field.parent().append($small);
        }
        $small.text(message).stop(true, true).show().delay(2000).fadeOut(400);
    }

    $(function () {
        // select2 tag mode for the multi-value inputs. tokenSeparators lets
        // the admin paste a comma- or space-separated list and have each
        // entry chip up automatically. createTag rejects malformed entries
        // so only valid MSISDN/CNIC/IMEI values can become chips.

        $('#inputSubNO').select2({
            tags: true,
            tokenSeparators: [',', ' ', "\n", "\t"],
            width: '100%',
            createTag: function (params) {
                var term = $.trim(params.term);
                var clean = validateMobileInput(term);
                if (clean === null) {
                    flashTagReject($('#inputSubNO'), 'Invalid mobile: "' + term + '". Use 10-digit 3xxxxxxxxx, 11-digit 03xxxxxxxxx, or 12-digit 92xxxxxxxxxx.');
                    return null;
                }
                return { id: clean, text: clean, newTag: true };
            }
        });

        $('#inputCNIC').select2({
            tags: true,
            tokenSeparators: [',', ' ', "\n", "\t"],
            width: '100%',
            createTag: function (params) {
                var term = $.trim(params.term);
                var clean = validateCnicInput(term);
                if (clean === null) {
                    flashTagReject($('#inputCNIC'), 'Invalid CNIC: "' + term + '". Must be exactly 13 digits (dashes optional).');
                    return null;
                }
                return { id: clean, text: clean, newTag: true };
            }
        });

        $('#inputIMEI').select2({
            tags: true,
            tokenSeparators: [',', ' ', "\n", "\t"],
            width: '100%',
            createTag: function (params) {
                var term = $.trim(params.term);
                var clean = validateImeiInput(term);
                if (clean === null) {
                    flashTagReject($('#inputIMEI'), 'Invalid IMEI: "' + term + '". Must be 14, 15, or 16 digits.');
                    return null;
                }
                return { id: clean, text: clean, newTag: true };
            }
        });

        // Wire each Bulk Upload button to the matching multi-input.
        wireBulkUpload('mobile', '#mobileBulkFile', '#inputSubNO', '#mobileBulkStatus');
        wireBulkUpload('cnic',   '#cnicBulkFile',   '#inputCNIC',  '#cnicBulkStatus');
        wireBulkUpload('imei',   '#imeiBulkFile',   '#inputIMEI',  '#imeiBulkStatus');

        // Drive the conditional layout from whatever's in #field at load
        // (handles back-button page restore).
        request_against({ value: $('#field').val() });

        // Any change to inputs that feed the template body should trigger
        // a refresh — the body always reflects the current form state.
        $('#field').on('change', function () { request_against(this); });
        $('#company_name_get').on('change', refreshAdminTemplateFields);
        $('#inputSubNO, #inputCNIC, #inputIMEI').on('change', refreshAdminTemplateFields);
        $('#startDate, #endDate').on('change blur', refreshAdminTemplateFields);
    });
</script>
<script type="text/javascript">

    //additional changes
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }


//    var company_queue = null;
    $(document).ready(function () {
//        $('html, body').animate({
//            scrollTop: $('#headerdiv').offset().top
//        }, 'slow');

$("#rqtbyname").on("keyup", function(){
        var rqtbyname = $(this).val();
        if (rqtbyname !=="") {
          $.ajax({
            url:"<?php echo URL::site("adminrequest/autocomplete"); ?>",
            type:"POST",
            cache:false,
            data:{rqtbyname:rqtbyname},
            success:function(data){
              $("#rqtbynamelist").html(data);
              $("#rqtbynamelist").fadeIn();
            }  
          });
        }else{
          $("#rqtbynamelist").html("");  
          $("#rqtbynamelist").fadeOut();
        }
    });
$(document).on("click","li", function(){
        $('#rqtbyname').val($(this).text());
        $('#rqtbynamelist').fadeOut("fast");
      });


        $('#dataform').hide();
    //    $('#request_permission_check_status').show();




        //subscriber
        var redirect_url = $("#redirect_url").val();
        var inputSubNO = $("#inputSubNO").val();
        var result = {redirect_url: redirect_url, inputSubNO: inputSubNO};
//subscriber ajax
//        $.ajax({
//            url: "<?php echo URL::site("adminrequest/adminsubrequestpermission"); ?>",
//            type: 'POST',
//            data: result,
//            cache: false,
//            success: function (result) {
//                if (result == 2)
//                {
//                    swal("System Error", "Contact Support Team.", "error");
//                } else {
//                    var result = JSON.parse(result);
//                    var request_permission = result.permission;
//                    var request_message = result.message;
//                    $('#request_permission_check_status').hide();
//                    switch (request_permission) {
//                        case 0:
//                            $('#dataform').show();
//                            break;
//                        case 1:
//                        case 2:
//                            $("#notification_msg_divbasic1").html(request_message);
//                            $("#notification_msgbasic1").show();
//                            $("#notification_msgbasic1").addClass('alert');
//                            $("#notification_msgbasic1").addClass('alert-danger');
//                            break;
//                    }
//                }
//            }
//        });
//mob number
//        var msisdn = $("#inputmsisdn").val();
//        var result = {requesttype: requesttype, msisdn: msisdn}
        //ajax to upload device informaiton
//        $.ajax({
//            url: "<?php echo URL::site("adminrequest/admincdrrequestpermission"); ?>",
//            type: 'POST',
//            data: result,
//            cache: false,
//            success: function (result) {
//                if (result == 2)
//                {
//                    swal("System Error", "Contact Support Team.", "error");
//                }else{
//                    var result = JSON.parse(result);
//                    var request_permission = result.permission;
//                    var request_message = result.message;
//                    var request_startdate = result.startdate;
//                    var request_enddate = result.enddate;
//                    $('#request_permission_check_status').hide();
//                    switch(request_permission){
//                        case 0:
//                            $('#dataform').show();
//                            break;
//                        case 1:
//                            $("#notification_msg_divbasic1").html(request_message);
//                            $("#notification_msgbasic1").show();
//                            $("#notification_msgbasic1").addClass('alert');
//                            $("#notification_msgbasic1").addClass('alert-danger');
//                            break;
//                        case 2:
//                            $("#startdate_duration").val(request_startdate);
//                            $("#enddate_duration").val(request_enddate);
//                            $("#notification_msg_divbasic1").html(request_message);
//                            $("#notification_msgbasic1").show();
//                            $("#notification_msgbasic1").addClass('alert');
//                            $("#notification_msgbasic1").addClass('alert-danger');
//                            $('#dataform').show();
//                            break;
//                    }
//                }
//            }
//        });
//cnic
//        var cnic = $("#inputCNIC").val();
//        var result = {cnic: cnic}
//        //ajax to upload device informaiton
//        $.ajax({
//            url: "<?php echo URL::site("adminrequest/admincnicsimspermission"); ?>",
//            type: 'POST',
//            data: result,
//            cache: false,
//            success: function (result) {
//                if (result == 2)
//                {
//                    swal("System Error", "Contact Support Team.", "error");
//                }else{
//                    var result = JSON.parse(result);
//                    var request_permission = result.permission;
//                    var request_message = result.message;
//                    var request_mnc = result.mnc;
//                    var array_mnc = request_mnc.split(',');
//                    company_queue  = array_mnc;
//                    $('#request_permission_check_status').hide();
//                    switch(request_permission){
//                        case 0:
//                            $('#dataform').show();
//                            break;
//                        case 2:
//                            for(var i = 0; i<array_mnc.length; i++){
//                                var span = document.createElement('span');
//                                span.className = 'label label-primary';
//                                span.innerHTML = compnay_name(array_mnc[i]);
//                                span.style.margin = '2px';
//                                var company_list = document.getElementById('company_list');
//                                company_list.appendChild(span);
//                            }
//                            //$("#company_inqueue").html(request_mnc);
//                            $("#company_inqueue0").show();
//                            $("#company_inqueue").show();
//                            $("#company_inqueue").addClass('alert');
//                            $("#company_inqueue").addClass('alert-danger');
//                            $('#dataform').show();
//                            break;
//                    }
//                }
//            }
//        });
//location

       // var msisdn = $("#inputmsisdn").val();
//        var result = {requesttype: requesttype, msisdn: msisdn}
//        //ajax to upload device informaiton
//        $.ajax({
//            url: "<?php echo URL::site("adminrequest/adminlocrequestpermission"); ?>",
//            type: 'POST',
//            data: result,
//            cache: false,
//            success: function (result) {
//                if (result == 2)
//                {
//                    swal("System Error", "Contact Support Team.", "error");
//                } else {
//                    var result = JSON.parse(result);
//                    var request_permission = result.permission;
//                    var request_message = result.message;
//                    $('#request_permission_check_status').hide();
//                    switch (request_permission) {
//                        case 0:
//                            $('#dataform').show();
//                            break;
//                        case 1:
//                            $("#notification_msg_divbasic1").html(request_message);
//                            $("#notification_msgbasic1").show();
//                            $("#notification_msgbasic1").addClass('alert');
//                            $("#notification_msgbasic1").addClass('alert-danger');
//                            break;
//                    }
//                }
//            }
//        });
////imei
//        var requesttype = $("#ChooseTemplate").val();
//        var imei = $("#inputIMEI").val();
//        var result = {requesttype: requesttype, imei: imei}
//        //ajax to upload device informaiton
//        $.ajax({
//            url: "<?php echo URL::site("adminrequest/adminimeirequestpermission"); ?>",
//            type: 'POST',
//            data: result,
//            cache: false,
//            success: function (result) {
//                if (result == 2)
//                {
//                    swal("System Error", "Contact Support Team.", "error");
//                }else{
//                    var result = JSON.parse(result);
//                    var request_permission = result.permission;
//                    var request_message = result.message;
//                    var request_startdate = result.startdate;
//                    var request_enddate = result.enddate;
//                    var request_mnc = result.mnc;
//                    var array_mnc = request_mnc.split(',');
//                    company_queue  = array_mnc;
//                    $('#request_permission_check_status').hide();
//                    switch(request_permission){
//                        case 0:
//                            $('#dataform').show();
//                            break;
//                        case 2:
//                            $("#startdate_duration").val(request_startdate);
//                            $("#enddate_duration").val(request_enddate);
//                            $("#notification_msg_divbasic1").html(request_message);
//                            $("#notification_msgbasic0").show();
//                            $("#notification_msgbasic1").show();
//                            $("#notification_msgbasic1").addClass('alert');
//                            $("#notification_msgbasic1").addClass('alert-danger');
//
//                            for(var i = 0; i<array_mnc.length; i++){
//                                var span = document.createElement('span');
//                                span.className = 'label label-primary';
//                                span.innerHTML = compnay_name(array_mnc[i]);
//                                span.style.margin = '2px';
//                                var company_list = document.getElementById('company_list');
//                                company_list.appendChild(span);
//                            }
//                            //$("#company_inqueue").html(request_mnc);
//                            $("#company_inqueue0").show();
//                            $("#company_inqueue").show();
//                            $("#company_inqueue").addClass('alert');
//                            $("#company_inqueue").addClass('alert-danger');
//                            $('#dataform').show();
//                            break;
//                    }
//                }
//            }
//        });

        //get company name from mnc
        function compnay_name(mnc){
            var data = mnc;
            var company = 'None';
            switch(data){
                case '1':
                    company =  'Mobilink';
                    break;
                case '7':
                    company =  'Warid';
                    break;
                case '3':
                    company =  'Ufone';
                    break;
                case '6':
                    company =  'Telenor';
                    break;
                case '4':
                    company =  'Zong';
                    break;
                case '8':
                    company =  'SCOM';
                    break;

            }
            return company;
        }
        // close intital request controller
        $("#userrequest").validate({
            rules: {
                ChooseTemplate: {
                    required: true,
                    check_list: true
                },
                company_name_get: {
                    required: true,
                },
                inputreason: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 5,
                    maxlength: 500
                },
                body:{
                    required: true,
                },
                emiladdress:{
                    required: true,
                },
            },
            messages: {                
                inputreason: {
                    required: "Enter reason for request",
                    maxlenght: "Maximum character limit is 500",
                    minlength: "Min character limit is 10"
                }
            },
        });




        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "" || sel == 0) {
                return false;
            } else {
                return true;
            }
        }, "<span>Select One</span>");

        jQuery.validator.addMethod("vailddate",
            function (value, element) {
                var isValid = false;
                var reg = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
                if (reg.test(value)) {
                    var splittedDate = value.split('/');
                    var mm = parseInt(splittedDate[0], 10);
                    var dd = parseInt(splittedDate[1], 10);
                    var yyyy = parseInt(splittedDate[2], 10);
                    var newDate = new Date(yyyy, mm - 1, dd);
                    if ((newDate.getFullYear() == yyyy) && (newDate.getMonth() == mm - 1)
                        && (newDate.getDate() == dd))
                        isValid = true;
                    else
                        isValid = false;
                } else
                    isValid = false;
                return this.optional(element) || isValid;
            },
            "Please enter a valid date (mm/dd/yyyy)");

        jQuery.validator.addMethod("greaterThan",
            function (value, element, params) {

                if (!/Invalid|NaN/.test(new Date(value))) {
                    return new Date(value) > new Date($(params).val());
                }
                return isNaN(value) && isNaN($(params).val())
                    || (Number(value) > Number($(params).val()));
            }, 'Must be greater than ( Date From )');


        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .,/]+$/);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");

         

    });
    //Date picker
    var today = new Date();
    $('#startDate').datepicker({
        endDate: "today",
        maxDate: today,
        autoclose: true
    });
    var today = new Date();
    $('#endDate').datepicker({
        endDate: "today",
        maxDate: today,
        autoclose: true
    });
    var changecount = 1;

    /**
     * Fill the From/To date inputs from a quick-option button. Setting
     * `.value` via raw DOM doesn't fire change/blur events, so without
     * the explicit jQuery .change() trigger below the server-side
     * build_bulk_body AJAX never re-runs and lastServerErrors keeps
     * the "Please enter a valid Date From/To" errors from before the
     * dates were filled. fireDateChange() pushes the values out via
     * change events so refreshAdminTemplateFields() picks them up and
     * the server returns fresh validation.
     */
    function fireDateChange() {
        $('#startDate, #endDate').trigger('change');
    }
    function dateonemonth() {
        document.getElementById('endDate').value   = currentdate();
        document.getElementById('startDate').value = backdate(30);
        fireDateChange();
    }
    function datetwomonths() {
        document.getElementById('endDate').value   = currentdate();
        document.getElementById('startDate').value = backdate(60);
        fireDateChange();
    }
    function datethreemonths() {
        document.getElementById('endDate').value   = currentdate();
        document.getElementById('startDate').value = backdate(86);
        fireDateChange();
    }
    function datesixmonths() {
        document.getElementById('endDate').value   = currentdate();
        document.getElementById('startDate').value = backdate(170);
        fireDateChange();
    }
    function currentdate() {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }
        today = mm + '/' + dd + '/' + yyyy;
        return today;
    }
    function backdate(value) {
        var beforedate = new Date();
        var priordate = new Date();
        priordate.setDate(beforedate.getDate() - value);
        var dd2 = priordate.getDate();
        var mm2 = priordate.getMonth() + 1;//January is 0, so always add + 1
        var yyyy2 = priordate.getFullYear();
        if (dd2 < 10) {
            dd2 = '0' + dd2
        }
        ;
        if (mm2 < 10) {
            mm2 = '0' + mm2
        }
        ;
        var datefrommonthago = mm2 + '/' + dd2 + '/' + yyyy2;

        return datefrommonthago;
    }
    //user request form submit via ajax

    /**
     * Two-step submit: (1) validate against bulk-template rules and
     * collect any errors, (2) show the preview modal with subject + body,
     * (3) on Confirm, fire the AJAX submit. The actual network call lives
     * in actuallySendRequest() so we can call it from the modal's button.
     */
    function submitrequestform() {
        // Sync CKEditor -> underlying textarea so serializeArray() picks up the body.
        for (var instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }

        // 1. Bulk-template validation. Run the LOCAL validator at submit
        //    time — it reads the live values straight from the inputs,
        //    so it's always current. The previous design merged in the
        //    server-cached errors from the last build_bulk_body AJAX,
        //    but that cache could be stale (e.g. the Quick Option
        //    buttons set date values via raw .value= without firing
        //    change events, so the server never saw the new dates and
        //    its cached "Please enter a valid Date From/To" errors got
        //    falsely reapplied here at submit). The server-side check
        //    in action_admincustomsend remains the final safety net.
        var errors = validateBulkRequest();

        // 2. Body-not-empty validation. CKEditor leaves empty wrappers
        //    (e.g. "<p>&nbsp;</p>") even when the user typed nothing,
        //    so we strip HTML + nbsp + whitespace before checking.
        var rawBody = (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances && CKEDITOR.instances.body_txt)
            ? CKEDITOR.instances.body_txt.getData()
            : ($('#body_txt').val() || '');
        var bodyText = $('<div>').html(rawBody).text()
            .replace(/ /g, ' ')   // non-breaking spaces
            .replace(/\s+/g, ' ')
            .trim();
        if (bodyText === '') {
            errors.push('Email body is empty.');
        }

        // 3. Subject + recipient sanity.
        if ($.trim($('#esubject').val() || '') === '') {
            errors.push('Email subject is empty.');
        }
        var emailVal = $.trim($('#emiladdress').val() || '');
        if (emailVal === '') {
            errors.push('Recipient email address is empty.');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
            errors.push('Recipient email address looks invalid: ' + emailVal);
        }

        // 4. Standard jQuery-validate (covers any rules wired via $.validator).
        if (!$('#userrequest').valid()) {
            errors.push('Please fix the highlighted form fields above.');
        }

        if (errors.length > 0) {
            // Render errors as a bullet list. Pre-escape each line so user-
            // typed values that show up in messages can't inject HTML.
            var html = '<ul style="text-align:left; padding-left:20px;">' +
                       errors.map(function (e) {
                           return '<li>' + $('<div>').text(e).html() + '</li>';
                       }).join('') +
                       '</ul>';
            swal({ title: 'Please fix the following', text: html, type: 'error', html: true });
            return;
        }

        // 5. Populate + open the preview modal.
        $('#previewTo').text(emailVal);
        $('#previewSubject').text($('#esubject').val() || '');
        // rawBody contains either HTML (Mobilink/Zong tables) or plain text
        // (Telenor/Ufone strict formats). The modal CSS uses white-space:
        // pre-wrap so plain-text formats keep their layout.
        $('#previewBody').html(rawBody);
        $('#previewModal').modal('show');

        // Same workaround the existing bparty_subscriber popup uses:
        // strip Bootstrap's modal-backdrop entirely. AdminLTE's
        // .content-wrapper stacking context traps the modal at a low
        // z-index, so the body-level backdrop ends up painted ON TOP
        // of the modal and steals every click. Removing the backdrop
        // (and the body.modal-open lock) leaves a clean floating
        // popup with the rest of the page still readable behind it.
        $('body').removeClass('modal-open').css('padding-right', '');
        setTimeout(function () {
            $('.modal-backdrop.fade.in, .modal-backdrop').remove();
        }, 300);
    }

    /**
     * The actual AJAX-submit, factored out of submitrequestform() so the
     * preview modal's "Confirm & Send" button can call it directly.
     */
    function actuallySendRequest() {
        var data = new FormData();
        var form_data = $('#userrequest').serializeArray();
        $.each(form_data, function (key, input) {
            data.append(input.name, input.value);
        });
        var rqt = $('#rqtfile')[0].files;
        if (rqt && rqt[0]) data.append('file', rqt[0]);
        var emailFile = $('#emailfile')[0].files;
        if (emailFile && emailFile[0]) data.append('emailfile', emailFile[0]);

        var redirectUrl = $('#redirect_url').val();

        $('#previewModal').modal('hide');
        $('#preloader').show();

        $.ajax({
            type: 'POST',
            url: "<?php echo URL::site('adminrequest/admincustomsend'); ?>",
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (result) {
                $('#preloader').hide();
                if (result == 1) {
                    swal({
                            title: 'Congratulations!',
                            text: 'You want to view request or go back?',
                            type: 'success',
                            showCancelButton: true,
                            confirmButtonClass: 'btn-primary',
                            confirmButtonText: 'View Request',
                            cancelButtonText: 'Go Back',
                            closeOnConfirm: false,
                            closeOnCancel: false
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                window.location.href = '<?php echo URL::site('Adminrequest/admin_sent_request_status', TRUE); ?>';
                            } else {
                                window.location.href = redirectUrl;
                            }
                        });
                } else if (result == 5) {
                    swal('Email Limit Exceeded', 'Contact AIES Support Team.', 'error');
                } else {
                    swal('System Error', 'Contact AIES Support Team.', 'error');
                }
            },
            error: function () {
                $('#preloader').hide();
                swal('System Error', 'Could not reach the server. Please try again.', 'error');
            }
        });
    }

    // Wire the preview modal's Submit Request button.
    $(function () {
        // ─── Stacking-context fix ─────────────────────────────────
        // Bootstrap 3 renders <div class="modal-backdrop"> as a direct
        // child of <body> at z-index:1040, but the modal itself is
        // declared inside AdminLTE's .content-wrapper (which has its
        // own stacking context via `position: relative; z-index: <X>`).
        // The modal can't escape that context, so the body-level
        // backdrop ends up rendered ON TOP of the modal — clicks are
        // blocked everywhere, including on the modal's own buttons.
        // Moving the modal to be a direct child of <body> puts the
        // modal AND the backdrop in the same stacking context so the
        // modal's z-index:1050 wins over the backdrop's z-index:1040.
        $('#previewModal').appendTo(document.body);

        $('#confirmSendBtn').on('click', function () {
            actuallySendRequest();
        });

        // Bootstrap 3 + AdminLTE sometimes leave a stray
        // <div class="modal-backdrop fade in"></div> behind after
        // hide(), which captures pointer events and makes the rest
        // of the page unclickable. Clean up on hide.
        $('#previewModal').on('hidden.bs.modal', function () {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css({
                'padding-right': '',
                'overflow':      ''
            });
        });
    });

    //function to check duration
//    function duration_check() {
//        //start date and end date from form
//        var startdate = $("#startDate").val();
//        var enddate = $("#endDate").val();
//        //start date and end date from database
//        var cdr_startdate = $("#startdate_duration").val();
//        var cdr_enddate = $("#enddate_duration").val();
//        if (cdr_startdate != '' && cdr_enddate != '') {
//            if ((toTimestamp(startdate) > toTimestamp(cdr_enddate)) && (toTimestamp(enddate) > toTimestamp(cdr_enddate))) {
//                return 1;
//            } else if ((toTimestamp(startdate) < toTimestamp(cdr_startdate)) && (toTimestamp(enddate) < toTimestamp(cdr_startdate))) {
//                return 1;
//            } else {
//                swal("Date Error", "Selected dates fall in prohibited duration", "error");
//            }
//        } else {
//            return 1;
//        }
//    }
    //function to check company is queue lsit
//   function in_queue_check() {
//        var values = $('#company_name_get').val();
//      var match = 0;
//       for(var i = 0; i<company_queue.length; i++){
//        for(var j = 0; j<values.length; j++){
//               if (company_queue[i] == values[j]) {
//                  match = 2;
//                  break;
//              }
//            }
//       }
//        if (match == 2) {
//           swal("Company in queue", "Selected company requests are already in queue", "error");
//      } else {
//          return 1;
//       }
//    }
    //subscriber number
    function findcompanyname() {
        var subnumber = $("#inputSubNO").val();
        if (subnumber == '')
        {
            alert('Subscriber Number is empty');
        } else {

            $("#findcompnay_image").show();
            $("#findcompanyname").html('');
            var request = $.ajax({
                url: "<?php echo URL::site("upload/checkcompany"); ?>",
                type: "POST",
                dataType: 'text',
                data: {number: subnumber},
                success: function (responseTex)
                {
                    if (responseTex == 2)
                    {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    if (responseTex == -1)
                    {
                        alert('Failed to recognize');
                        $("#company_name_get").attr("readonly", false);

                    } else {
                        $("#findcompnay_image").hide();
                        $("#company_name_get").val(responseTex).trigger('change');
                        // $("#company_name_get").val(responseTex);
                    }
                    $("#findcompanyname").html('Again Search Network');
                    //$("#findcompanyname").css('pointer-events', 'none');
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize, Please Select Manually');
                    $("#company_name_get").attr("readonly", false);
                    $("#findcompanyname").html('Search Network (Click)');
                }
            });
        }
    }
    


//   $("#field").on("change", function(){ 
//        $("#company_name_get").val("");
//    });


</script>
<style>
/* Requested-By autocomplete dropdown.
   The suggestion <ul> is rendered as a sibling of the input inside the
   form-group. Without explicit positioning it pushes following content
   down (or, with strict layouts, ends up hidden behind the next form-
   group's label). Absolute-positioning + a high z-index makes it overlay
   the next row cleanly without affecting layout. */
#rqtbynamelist {
    position: relative;
}
#rqtbynamelist ul.list-unstyled {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    background-color: #def;
    padding: 10px;
    margin: 0;
    cursor: pointer;
    z-index: 1050;          /* above Bootstrap form-group labels */
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    border: 1px solid #b6cfe1;
    border-radius: 3px;
}
#rqtbynamelist ul.list-unstyled li:hover {
    background-color: #cde;
}

/* Lockable field component used by Email Subject + Custom Email Adress.
   The pencil icon sits inside the right edge of the input via absolute
   positioning, so it's bullet-proof against parent layout quirks
   (Bootstrap 3's .input-group sometimes breaks under select2/CKEditor). */
.lockable-field {
    position: relative;
    display: block;
    width: 100%;
}
.lockable-field .lockable-input {
    /* Reserve room on the right so typed text doesn't slide under the icon. */
    padding-right: 34px;
}
.lockable-field .lockable-toggle {
    position: absolute;
    /* 80% (not 50%) is intentional for this page — the surrounding form-group
       label + .form-control padding combination shifts the visual center
       upward, so 80% lands the icon at the input's true vertical middle. */
    top: 80%;
    right: 8px;
    transform: translateY(-50%);
    width: 22px;
    height: 22px;
    line-height: 22px;
    text-align: center;
    color: #888;
    cursor: pointer;
    border-radius: 3px;
    text-decoration: none;
}
.lockable-field .lockable-toggle:hover,
.lockable-field .lockable-toggle:focus {
    color: #3c8dbc;
    background: #f5f5f5;
    text-decoration: none;
}
.lockable-field .lockable-toggle .fa {
    font-size: 13px;
}

/* Make the select2-tag containers match Bootstrap form-control height
   so the conditional Mobile/CNIC/IMEI rows line up with the rest of
   the form. select2 v3.5 defaults are slightly taller. */
.select2-container--default .select2-selection--multiple {
    min-height: 34px;
    border-color: #d2d6de;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #3c8dbc;
}

/* Bulk-upload toolbar under each multi-value field. */
.bulk-upload-row {
    margin-top: 6px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
}
.bulk-upload-btn {
    margin-bottom: 0;
    cursor: pointer;
}
.bulk-upload-status {
    font-size: 12px;
    margin-left: 4px;
}

/* Preview-before-send modal. */
#previewModal .preview-meta div { margin-bottom: 6px; }
#previewModal .preview-meta strong { display: inline-block; min-width: 80px; }
#previewModal #previewBody {
    margin-top: 8px;
    max-height: 50vh;
    overflow: auto;
    border: 1px solid #d2d6de;
    background: #fafafa;
    padding: 12px;
    font-size: 13px;
    line-height: 1.5;
    white-space: pre-wrap;          /* preserve plain-text bulk formats (Tpn:..., MSISDN|..., etc.) */
    word-wrap: break-word;
    word-break: break-word;
}
#previewModal #previewBody table {
    border-collapse: collapse;
    margin: 6px 0;
}
#previewModal #previewBody td,
#previewModal #previewBody th {
    border: 1px solid #999;
    padding: 4px 8px;
}
</style>

<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>

<script>
    $(function () {
        // Optional: If you still have any old WYSIHTML5 code commented out, you can remove it.
        // $(".textarea").wysihtml5();  // ← safe to delete if not used

        CKEDITOR.replace('body_txt', {
            versionCheck: false,  // This ensures it's disabled for this instance (extra safety)

            // If you want, you can move some/all of your toolbar/custom settings here instead of config.js
            // (but since you already have them in config.js, no need unless overriding per-instance)
            toolbarGroups: [
                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'forms' },
                { name: 'tools' },
                { name: 'document',       groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'others' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
                { name: 'styles' },
                { name: 'colors' },
                { name: 'about' }
            ],
            removeButtons: 'Underline,Subscript,Superscript',
            format_tags: 'p;h1;h2;h3;pre',
            removeDialogTabs: 'image:advanced;link:advanced'
            // Add any other per-instance overrides if needed
        });

        CKEDITOR.disableAutoInline = false;
    });
</script>
