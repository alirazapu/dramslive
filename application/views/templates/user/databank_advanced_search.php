<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php
// Variables expected (set by Controller_Databank::action_advanced_search()):
//   $api_url     — Controller_Databank::action_advanced_api (JSON proxy:
//                  POST op + payload; the browser never sees the token)
//   $export_url  — Controller_Databank::action_advanced_export (CSV)
//
// Advanced Search is a session kept by the Databank: started with
// identifiers (or investigation points + a period), its source searches
// run on the Databank, and every tab below reads that session. Response
// shapes: databank repo docs/INTEGRATION_API.md section 7.
?>
<style>
    .dbk-adv .nav-tabs-custom > .tab-content { min-height: 120px; }
    .dbk-adv table.dbk-kv > tbody > tr > th { width: 210px; font-weight: 600; color: #555; }
    .dbk-adv .dbk-rec { border: 1px solid #e3e3e3; border-radius: 3px; padding: 6px 10px; margin-bottom: 8px; background: #fcfcfc; }
    .dbk-adv .dbk-rec h5 { margin: 0 0 4px; font-weight: 600; }
    .dbk-adv img.dbk-img { max-width: 260px; max-height: 90px; border: 1px solid #ddd; }
    .dbk-adv .dbk-scroll { max-height: 460px; overflow: auto; }
    .dbk-adv #dbk_graph { height: 480px; border: 1px solid #ddd; background: #fff; }
    .dbk-adv .label-direct { background: #00a65a; }
    .dbk-adv .label-derived { background: #f39c12; }
    .dbk-adv .label-possible { background: #777; }
    .dbk-adv textarea { font-family: monospace; font-size: 12px; }
</style>

<section class="content-header">
    <h1>Advanced Search <small>Databank investigation</small></h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">DRAMS Databank</a></li>
        <li class="active">Advanced Search</li>
    </ol>
</section>

<section class="content dbk-adv">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Start an investigation</h3>
            <div class="box-tools pull-right">
                <small class="text-muted">One value per line; up to 25 per type, 40 in total.</small>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <?php foreach (array(
                    'mobile'  => 'Mobile numbers',
                    'cnic'    => 'CNICs',
                    'name'    => 'Names',
                    'fir'     => 'FIR numbers',
                    'imei'    => 'IMEIs',
                    'vehicle' => 'Vehicle registrations',
                ) as $type => $label): ?>
                    <div class="col-md-2 col-sm-4" style="margin-bottom:10px;">
                        <label class="control-label" for="dbk_id_<?php echo $type; ?>"><?php echo HTML::chars($label); ?></label>
                        <textarea class="form-control dbk-ident" rows="4" id="dbk_id_<?php echo $type; ?>" data-type="<?php echo $type; ?>"></textarea>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="row">
                <div class="col-md-2 col-sm-4">
                    <label class="control-label" for="dbk_from">From</label>
                    <input type="date" class="form-control" id="dbk_from">
                </div>
                <div class="col-md-2 col-sm-4">
                    <label class="control-label" for="dbk_to">To</label>
                    <input type="date" class="form-control" id="dbk_to">
                </div>
                <div class="col-md-8 col-sm-12">
                    <label class="control-label" for="dbk_points">Investigation points (location-first, optional)</label>
                    <textarea class="form-control" rows="2" id="dbk_points" placeholder="latitude, longitude, radius in metres, label — one point per line, e.g. 34.0151, 71.5249, 5000, Point A (needs From and To)"></textarea>
                </div>
            </div>
            <p class="help-block" style="margin:8px 0 0;">
                The period limits call and location records. With investigation points, the mobile numbers
                recorded near them in the period become the investigation's numbers. Results are leads to verify:
                derived and possible relationships do not prove identity, ownership or presence.
            </p>
            <div style="margin-top:10px;">
                <button type="button" class="btn btn-primary" id="dbk_start"><i class="fa fa-search"></i> Start</button>
                <span id="dbk_start_status" class="text-muted" style="margin-left:10px;"></span>
            </div>
        </div>
    </div>

    <div class="box box-default" id="dbk_session" style="display:none;">
        <div class="box-header with-border">
            <h3 class="box-title" id="dbk_session_title">Investigation</h3>
            <div class="box-tools pull-right">
                <span id="dbk_run_status" class="text-muted"></span>
                <button type="button" class="btn btn-xs btn-warning" id="dbk_run" style="display:none;"><i class="fa fa-play"></i> Run pending searches</button>
            </div>
        </div>
        <div class="box-body">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#dbk_t_summary" data-toggle="tab" data-tab="summary">Summary</a></li>
                    <li><a href="#dbk_t_records" data-toggle="tab" data-tab="records">Records</a></li>
                    <li><a href="#dbk_t_findings" data-toggle="tab" data-tab="findings">Findings</a></li>
                    <li><a href="#dbk_t_graph" data-toggle="tab" data-tab="graph">Graph</a></li>
                    <li><a href="#dbk_t_timeline" data-toggle="tab" data-tab="timeline">Timeline</a></li>
                    <li><a href="#dbk_t_profile" data-toggle="tab" data-tab="profile">Profile</a></li>
                    <li><a href="#dbk_t_cdr" data-toggle="tab" data-tab="cdr">CDR</a></li>
                    <li><a href="#dbk_t_location" data-toggle="tab" data-tab="location">Location</a></li>
                    <li><a href="#dbk_t_geo" data-toggle="tab" data-tab="geo">Geo</a></li>
                    <li><a href="#dbk_t_export" data-toggle="tab" data-tab="export">Export</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="dbk_t_summary"><div id="dbk_summary"></div></div>

                    <div class="tab-pane" id="dbk_t_records">
                        <button type="button" class="btn btn-xs btn-default dbk-reload" data-tab="records"><i class="fa fa-refresh"></i> Reload</button>
                        <div id="dbk_records" style="margin-top:8px;"></div>
                    </div>

                    <div class="tab-pane" id="dbk_t_findings">
                        <button type="button" class="btn btn-xs btn-default dbk-reload" data-tab="findings"><i class="fa fa-refresh"></i> Reload</button>
                        <div id="dbk_findings" style="margin-top:8px;"></div>
                    </div>

                    <div class="tab-pane" id="dbk_t_graph">
                        <div class="form-inline" style="margin-bottom:8px;">
                            <select class="form-control input-sm" id="dbk_graph_class">
                                <option value="">All relationships</option>
                                <option value="direct">Direct records only</option>
                                <option value="derived">Derived relationships only</option>
                                <option value="possible">Possible associations only</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-default dbk-reload" data-tab="graph"><i class="fa fa-refresh"></i> Draw</button>
                            <span style="margin-left:16px;">Between</span>
                            <select class="form-control input-sm dbk-node-select" id="dbk_node_a"></select>
                            <span>and</span>
                            <select class="form-control input-sm dbk-node-select" id="dbk_node_b"></select>
                            <button type="button" class="btn btn-sm btn-default" id="dbk_path">Connection path</button>
                            <button type="button" class="btn btn-sm btn-default" id="dbk_indicators">Indicators</button>
                            <label style="font-weight:normal;"><input type="checkbox" id="dbk_ind_location"> with location overlap</label>
                        </div>
                        <div class="row">
                            <div class="col-md-8"><div id="dbk_graph"></div></div>
                            <div class="col-md-4"><div id="dbk_graph_side" class="dbk-scroll"><p class="text-muted">Click a node or a relationship to see its evidence.</p></div></div>
                        </div>
                        <div id="dbk_graph_analysis" style="margin-top:8px;"></div>
                    </div>

                    <div class="tab-pane" id="dbk_t_timeline">
                        <div class="form-inline" style="margin-bottom:8px;">
                            <input type="date" class="form-control input-sm" id="dbk_tl_from">
                            <input type="date" class="form-control input-sm" id="dbk_tl_to">
                            <select class="form-control input-sm" id="dbk_tl_order"><option value="asc">Oldest first</option><option value="desc">Newest first</option></select>
                            <button type="button" class="btn btn-sm btn-default dbk-reload" data-tab="timeline"><i class="fa fa-refresh"></i> Show</button>
                        </div>
                        <div id="dbk_timeline"></div>
                    </div>

                    <div class="tab-pane" id="dbk_t_profile">
                        <div class="form-inline" style="margin-bottom:8px;">
                            <select class="form-control input-sm" id="dbk_profile_cnic"></select>
                            <button type="button" class="btn btn-sm btn-default dbk-reload" data-tab="profile"><i class="fa fa-user"></i> Show profile</button>
                        </div>
                        <div id="dbk_profile"></div>
                    </div>

                    <div class="tab-pane" id="dbk_t_cdr">
                        <div class="form-inline" style="margin-bottom:8px;">
                            <select class="form-control input-sm dbk-number-select" id="dbk_cdr_number"></select>
                            <input type="date" class="form-control input-sm" id="dbk_cdr_from">
                            <input type="date" class="form-control input-sm" id="dbk_cdr_to">
                            <button type="button" class="btn btn-sm btn-default dbk-reload" data-tab="cdr"><i class="fa fa-phone"></i> Summary &amp; activity</button>
                        </div>
                        <div id="dbk_cdr"></div>
                        <h4 style="margin-top:16px;">Call / SMS records</h4>
                        <div class="form-inline" style="margin-bottom:8px;">
                            <select class="form-control input-sm" id="dbk_cdr_kind"><option value="all">Calls and SMS</option><option value="call">Calls</option><option value="sms">SMS</option></select>
                            <select class="form-control input-sm" id="dbk_cdr_direction"><option value="all">Both directions</option><option value="outgoing">Outgoing</option><option value="incoming">Incoming</option></select>
                            <input type="text" class="form-control input-sm" id="dbk_cdr_contact" placeholder="Other party number">
                            <button type="button" class="btn btn-sm btn-default" id="dbk_cdr_records_btn">Show records</button>
                            <button type="button" class="btn btn-sm btn-default" id="dbk_cdr_next" style="display:none;">Next page</button>
                        </div>
                        <div id="dbk_cdr_records" class="dbk-scroll"></div>
                    </div>

                    <div class="tab-pane" id="dbk_t_location">
                        <div class="form-inline" style="margin-bottom:8px;">
                            <select class="form-control input-sm dbk-number-select" id="dbk_loc_number"></select>
                            <input type="date" class="form-control input-sm" id="dbk_loc_from">
                            <input type="date" class="form-control input-sm" id="dbk_loc_to">
                            <button type="button" class="btn btn-sm btn-default dbk-reload" data-tab="location"><i class="fa fa-map-marker"></i> Show</button>
                        </div>
                        <div id="dbk_location"></div>
                    </div>

                    <div class="tab-pane" id="dbk_t_geo">
                        <div class="form-inline" style="margin-bottom:8px;">
                            <textarea class="form-control" rows="2" cols="60" id="dbk_geo_points" placeholder="latitude, longitude, radius in metres, label — one point per line"></textarea>
                            <input type="date" class="form-control input-sm" id="dbk_geo_from">
                            <input type="date" class="form-control input-sm" id="dbk_geo_to">
                            <button type="button" class="btn btn-sm btn-default dbk-reload" data-tab="geo"><i class="fa fa-crosshairs"></i> Analyse</button>
                        </div>
                        <div id="dbk_geo"></div>
                    </div>

                    <div class="tab-pane" id="dbk_t_export">
                        <p class="text-muted">CSV files of this investigation, as produced by the Databank.</p>
                        <?php foreach (array(
                            'timeline'      => 'Timeline',
                            'relationships' => 'Relationships',
                            'candidates'    => 'Discovered identifiers',
                            'cdr'           => 'Call / SMS records',
                            'location'      => 'Location records',
                        ) as $kind => $label): ?>
                            <button type="button" class="btn btn-default dbk-export" data-kind="<?php echo $kind; ?>" style="margin:0 6px 6px 0;">
                                <i class="fa fa-download"></i> <?php echo HTML::chars($label); ?>
                            </button>
                        <?php endforeach; ?>
                        <div id="dbk_export_status" class="text-muted"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    var API_URL    = <?php echo json_encode($api_url); ?>;
    var EXPORT_URL = <?php echo json_encode($export_url); ?>;
    var state = { sid: '', summary: null, loaded: {}, cdrCursor: null, nodes: [] };

    /* ---------------- helpers ---------------- */

    function esc(s) {
        return String(s === null || s === undefined ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function isImage(s) {
        return typeof s === 'string' && /^data:image\/(jpeg|jpg|png|gif|webp);base64,[A-Za-z0-9+\/=]+$/.test(s);
    }
    function fmt(v) {
        if (v === null || v === undefined || v === '') return '<span class="text-muted">&mdash;</span>';
        if (v === true) return 'Yes';
        if (v === false) return 'No';
        if (isImage(v)) return '<img class="dbk-img" alt="" src="' + esc(v) + '">';
        return esc(v);
    }
    function humanize(k) {
        return String(k).replace(/_/g, ' ').replace(/([a-z])([A-Z])/g, '$1 $2').replace(/^./, function (c) { return c.toUpperCase(); });
    }
    function cls(c, label) {
        if (!c && !label) return '';
        return '<span class="label label-' + esc(c || 'possible') + '">' + esc(label || c) + '</span>';
    }
    function err(msg) { return '<div class="alert alert-danger" style="margin:6px 0;">' + esc(msg) + '</div>'; }
    function loading() { return '<div class="text-muted" style="padding:10px;"><i class="fa fa-spinner fa-spin"></i> Loading…</div>'; }
    function note(t) { return t ? '<p class="text-muted small">' + esc(t) + '</p>' : ''; }

    // Generic rendering of any API structure: objects as label / value
    // tables, lists of objects as tables, lists of values as text.
    var HIDDEN = { sortKey: 1, edgeIds: 1, source_record_id: 1, searchId: 1, requestId: 1, ok: 1, paths: 1, metadata: 1 };
    function gen(v, depth) {
        depth = depth || 0;
        if (v === null || v === undefined || v === '') return fmt(v);
        if (typeof v !== 'object') return fmt(v);
        if (Array.isArray(v)) {
            if (!v.length) return '<span class="text-muted">none</span>';
            var scalars = v.every(function (x) { return x === null || typeof x !== 'object'; });
            if (scalars) return v.map(fmt).join(', ');
            var labelled = v.every(function (x) { return x && typeof x === 'object' && 'label' in x && 'value' in x; });
            if (labelled) return fieldsTable(v);
            var objects = v.every(function (x) { return x && typeof x === 'object' && !Array.isArray(x); });
            if (objects && depth < 5) {
                var cols = [];
                v.forEach(function (o) { Object.keys(o).forEach(function (k) { if (!HIDDEN[k] && cols.indexOf(k) < 0) cols.push(k); }); });
                var h = '<div class="table-responsive"><table class="table table-condensed table-bordered table-striped"><thead><tr>';
                cols.forEach(function (k) { h += '<th>' + esc(humanize(k)) + '</th>'; });
                h += '</tr></thead><tbody>';
                v.forEach(function (o) {
                    h += '<tr>';
                    cols.forEach(function (k) { h += '<td>' + gen(o[k], depth + 1) + '</td>'; });
                    h += '</tr>';
                });
                return h + '</tbody></table></div>';
            }
            return v.map(function (x) { return gen(x, depth + 1); }).join('<hr style="margin:4px 0;">');
        }
        if (depth >= 6) return '<span class="text-muted">…</span>';
        var keys = Object.keys(v).filter(function (k) { return !HIDDEN[k]; });
        if (!keys.length) return '<span class="text-muted">none</span>';
        var t = '<table class="table table-condensed dbk-kv" style="margin-bottom:4px;"><tbody>';
        keys.forEach(function (k) { t += '<tr><th>' + esc(humanize(k)) + '</th><td>' + gen(v[k], depth + 1) + '</td></tr>'; });
        return t + '</tbody></table>';
    }
    function fieldsTable(fields) {
        var t = '<table class="table table-condensed dbk-kv" style="margin-bottom:4px;"><tbody>';
        (fields || []).forEach(function (f) {
            if (!f || typeof f !== 'object') return;
            var val = fmt(f.value);
            if (f.stateLabel && f.state !== 'available') val += ' <span class="text-muted small">(' + esc(f.stateLabel) + ')</span>';
            if (isImage(f.image)) val += '<br><img class="dbk-img" alt="" src="' + esc(f.image) + '">';
            t += '<tr><th>' + esc(f.label) + '</th><td>' + val + '</td></tr>';
        });
        return t + '</tbody></table>';
    }
    function record(r) {
        if (!r || typeof r !== 'object') return '';
        return '<div class="dbk-rec"><h5>' + esc(r.title || r.recordLabel || 'Record') +
            (r.subtitle ? ' <small>' + esc(r.subtitle) + '</small>' : '') + '</h5>' +
            '<div class="text-muted small">' + esc([r.sourceLabel, r.recordLabel, r.reference].filter(Boolean).join(' · ')) +
            (r.foundThrough ? ' · ' + esc(r.foundThrough) : '') + '</div>' + fieldsTable(r.fields) + '</div>';
    }
    function lines(v) {
        var out = [];
        String(v || '').split(/\r?\n/).forEach(function (l) { l = l.replace(/^\s+|\s+$/g, ''); if (l) out.push(l); });
        return out;
    }
    function points(text) {
        var out = [];
        lines(text).forEach(function (l) {
            var p = l.split(',');
            if (p.length < 2) return;
            var pt = { lat: parseFloat(p[0]), lng: parseFloat(p[1]) };
            if (p[2] && !isNaN(parseInt(p[2], 10))) pt.radius = parseInt(p[2], 10);
            if (p[3]) pt.label = p.slice(3).join(',').replace(/^\s+|\s+$/g, '');
            out.push(pt);
        });
        return out;
    }
    function period(fromId, toId, body) {
        var f = $('#' + fromId).val(), t = $('#' + toId).val();
        if (f) body.from = f;
        if (t) body.to = t;
        return body;
    }

    function api(op, payload) {
        payload = payload || {};
        if (op !== 'start') payload.searchId = state.sid;
        return $.ajax({ url: API_URL, type: 'POST', dataType: 'json',
                        data: { op: op, payload: JSON.stringify(payload) } })
            .then(function (r) {
                if (!r || r.ok !== true) {
                    return $.Deferred().reject((r && r.error) ? r.error + (r.requestId ? ' (Databank reference ' + r.requestId + ')' : '')
                                                              : 'Unexpected answer.').promise();
                }
                return r;
            }, function (xhr) {
                return $.Deferred().reject(xhr && xhr.status === 0 ? 'The DRAMS server could not be reached.'
                                                                    : 'The request failed (HTTP ' + (xhr ? xhr.status : '?') + ').').promise();
            });
    }
    function into(target, op, payload, render) {
        $(target).html(loading());
        return api(op, payload).then(function (r) { $(target).html(render(r)); return r; },
                                     function (msg) { $(target).html(err(msg)); });
    }

    /* ---------------- start / run / summary ---------------- */

    $('#dbk_start').on('click', function () {
        var identifiers = {}, any = false;
        $('.dbk-ident').each(function () {
            var l = lines($(this).val());
            if (l.length) { identifiers[$(this).data('type')] = l; any = true; }
        });
        var body = period('dbk_from', 'dbk_to', {});
        var pts = points($('#dbk_points').val());
        if (pts.length) body.points = pts;
        if (!any && !pts.length) { $('#dbk_start_status').html('<span class="text-warning">Enter at least one identifier or investigation point.</span>'); return; }
        body.identifiers = identifiers;
        $('#dbk_start').prop('disabled', true);
        $('#dbk_start_status').html('<i class="fa fa-spinner fa-spin"></i> Searching every source — this can take up to two minutes…');
        api('start', body).then(function (r) {
            state.sid = r.searchId; state.loaded = {}; state.cdrCursor = null;
            $('#dbk_session').show();
            var msg = (r.rejected && r.rejected.length) ? ' Not used: ' + r.rejected.map(function (x) { return x.value || x.reason || x; }).join('; ') : '';
            $('#dbk_start_status').text('Started.' + msg);
            showSummary(r.summary || {}, r.run, r.discovery);
            runPending(r.run);
        }, function (msg) {
            $('#dbk_start_status').html('<span class="text-danger">' + esc(msg) + '</span>');
        }).always(function () { $('#dbk_start').prop('disabled', false); });
    });

    function runPending(run) {
        var pending = run ? (run.pending || 0) : 0;
        if (!pending) { $('#dbk_run').hide(); $('#dbk_run_status').text(''); return; }
        $('#dbk_run').hide();
        $('#dbk_run_status').html('<i class="fa fa-spinner fa-spin"></i> ' + pending + ' source search(es) still running…');
        api('run', {}).then(function (r) {
            showSummary(r.summary || {}, r.run, null);
            runPending(r.run);
        }, function (msg) {
            $('#dbk_run_status').html('<span class="text-danger">' + esc(msg) + '</span>');
            $('#dbk_run').show();
        });
    }
    $('#dbk_run').on('click', function () { runPending({ pending: 1 }); });

    function showSummary(s, run, discovery) {
        state.summary = s;
        state.loaded = {};
        var inv = s.investigation || {};
        $('#dbk_session_title').text('Investigation ' + (s.reference || inv.reference || '') + (s.modeLabel ? ' — ' + s.modeLabel : ''));

        var h = '';
        if (discovery) h += '<h4>Location discovery</h4>' + gen(discovery);
        if (s.stats && s.stats.length) {
            h += '<div class="row">';
            s.stats.forEach(function (st) {
                h += '<div class="col-md-2 col-sm-4"><div class="small-box bg-gray" style="padding:6px 10px;margin-bottom:8px;" title="' + esc(st.note || '') + '">' +
                     '<strong style="font-size:18px;">' + esc(st.value) + '</strong><br><span class="small">' + esc(st.label) + '</span></div></div>';
            });
            h += '</div>';
        }
        if (s.relationships && s.relationships.lines) h += '<p>' + s.relationships.lines.map(esc).join('<br>') + '</p>';
        if (run) h += note('Source searches: ' + (run.ran || 0) + ' run, ' + (run.pending || 0) + ' pending' + (run.notQueried ? ', ' + run.notQueried + ' not queried (source unavailable)' : '') + '.');

        h += '<h4>Sources</h4>' + gen((s.sourceStatus || []).map(function (x) {
            return { source: x.label, status: x.statusLabel, detail: x.detail, records: x.records, truncated: x.truncated };
        }));
        h += '<h4>Searched identifiers</h4>' + gen((s.identifiers || []).map(function (x) {
            return { identifier: x.display, type: x.typeLabel, step: x.stepLabel, origin: x.originLabel, classification: x.classLabel };
        }));

        var cands = s.candidates || [];
        h += '<h4>Discovered identifiers <small>tick and expand to search them too</small></h4>';
        if (!cands.length) {
            h += '<p class="text-muted">None.</p>';
        } else {
            h += '<div class="table-responsive"><table class="table table-condensed table-bordered table-striped"><thead><tr><th></th><th>Identifier</th><th>Type</th><th>Classification</th><th>Why</th><th>Sources</th></tr></thead><tbody>';
            cands.forEach(function (c) {
                h += '<tr><td>' + (c.expandable ? '<input type="checkbox" class="dbk-cand" value="' + esc(c.id) + '">' : '<span class="text-muted" title="' + esc(c.notExpandableReason || '') + '">&ndash;</span>') + '</td>' +
                     '<td>' + esc(c.display || c.text) + '</td><td>' + esc(c.typeLabel) + '</td><td>' + cls(c['class'], c.classLabel) + '</td>' +
                     '<td>' + esc(c.why || c.connection || '') + '</td><td>' + esc((c.sources || []).join(', ')) + '</td></tr>';
            });
            h += '</tbody></table></div><button type="button" class="btn btn-sm btn-primary" id="dbk_expand"><i class="fa fa-plus"></i> Expand selected</button> <span id="dbk_expand_status" class="text-muted"></span>';
        }
        if (s.cross) {
            h += '<h4>Across the numbers <small>' + esc(s.cross.statusLabel || '') + '</small></h4>' + note(s.cross.message);
            ['interactions', 'commonContacts', 'sharedDevices', 'sharedRegistrations', 'activityOverlaps', 'clusters'].forEach(function (k) {
                if (s.cross[k] && s.cross[k].length) h += '<h5>' + esc(humanize(k)) + '</h5>' + gen(s.cross[k]);
            });
        }
        if (s.numbers && s.numbers.length) h += '<h4>Numbers</h4>' + gen(s.numbers);
        if (s.history && s.history.length) h += '<h4>History</h4>' + gen(s.history);
        if (s.limits && s.limits.depthText) h += note(s.limits.depthText);
        $('#dbk_summary').html(h);

        // Selects of the other tabs: session CNICs, numbers, graph nodes.
        var cnics = [], numbers = [];
        (s.identifiers || []).concat(cands).forEach(function (x) {
            if (x.type === 'cnic' && cnics.indexOf(x.display) < 0) cnics.push(x.display);
        });
        (s.identifiers || []).forEach(function (x) {
            if (x.type === 'mobile' && numbers.indexOf(x.display) < 0) numbers.push(x.display);
        });
        $('#dbk_profile_cnic').html(cnics.length ? cnics.map(function (c) { return '<option>' + esc(c) + '</option>'; }).join('') : '<option value="">No CNIC in this search</option>');
        $('.dbk-number-select').html('<option value="">All numbers of the search</option>' + numbers.map(function (n) { return '<option>' + esc(n) + '</option>'; }).join(''));
    }

    $('#dbk_summary').on('click', '#dbk_expand', function () {
        var ids = $('.dbk-cand:checked').map(function () { return this.value; }).get();
        if (!ids.length) { $('#dbk_expand_status').text('Tick at least one identifier.'); return; }
        $('#dbk_expand_status').html('<i class="fa fa-spinner fa-spin"></i> Searching the selected identifiers…');
        api('expand', { candidates: ids }).then(function (r) {
            var skipped = (r.skipped || []).map(function (x) { return (x.display || x.id || '') + (x.reason ? ': ' + x.reason : ''); });
            showSummary(r.summary || {}, r.run, null);
            $('#dbk_expand_status').text((r.added || []).length + ' added.' + (skipped.length ? ' Skipped: ' + skipped.join('; ') : ''));
            runPending(r.run);
        }, function (msg) { $('#dbk_expand_status').html('<span class="text-danger">' + esc(msg) + '</span>'); });
    });

    /* ---------------- tabs ---------------- */

    var loaders = {
        records: function () {
            return into('#dbk_records', 'records', {}, function (r) {
                var h = '';
                (r.results || []).forEach(function (res) {
                    var who = res.identifier ? (res.identifier.display || '') : '';
                    h += '<h5 style="margin-top:12px;"><strong>' + esc(res.sourceLabel) + '</strong> · ' + esc(who) +
                         ' <small class="text-muted">' + esc(res.statusLabel) + (res.count ? ' (' + res.count + ')' : '') + (res.truncated ? ' — truncated' : '') + '</small></h5>';
                    if (res.message) h += note(res.message);
                    (res.records || []).forEach(function (rec) { h += record(rec); });
                });
                return h || '<p class="text-muted">No records.</p>';
            });
        },
        findings: function () {
            return into('#dbk_findings', 'findings', {}, function (r) {
                var h = note(r.note);
                (r.findings || []).forEach(function (f) {
                    h += '<div class="dbk-rec">' + cls(f['class'], f.classLabel) + ' ' + (f.notable ? '<strong>' + esc(f.text) + '</strong>' : esc(f.text)) +
                         '<div class="small text-muted">' + esc([(f.sources || []).join(', '), f.period, f.reason].filter(Boolean).join(' · ')) + '</div>' +
                         (f.evidence ? '<div class="small">' + esc(f.evidence) + '</div>' : '') + '</div>';
                });
                return h || '<p class="text-muted">No findings.</p>';
            });
        },
        graph: function () {
            var c = $('#dbk_graph_class').val();
            $('#dbk_graph_analysis').html(loading());
            return api('graph', c ? { classes: [c] } : {}).then(drawGraph, function (msg) { $('#dbk_graph_analysis').html(err(msg)); });
        },
        timeline: function () {
            var body = period('dbk_tl_from', 'dbk_tl_to', { order: $('#dbk_tl_order').val() });
            return into('#dbk_timeline', 'timeline', body, function (r) {
                var h = note(r.note) + note(r.locationNote);
                if (r.categories && r.categories.length) h += '<p>' + r.categories.map(function (c) { return esc(c.label) + ': <strong>' + esc(c.count) + '</strong>'; }).join(' · ') + '</p>';
                return h + gen((r.events || []).map(function (e) {
                    return { when: e.atText, type: e.typeLabel, event: e.title, explanation: e.explanation, about: e.entityLabel, source: e.sourceLabel, reference: e.reference };
                }));
            });
        },
        profile: function () {
            var cnic = $('#dbk_profile_cnic').val();
            if (!cnic) { $('#dbk_profile').html('<p class="text-muted">This search has no CNIC to profile.</p>'); return $.Deferred().resolve(); }
            return into('#dbk_profile', 'profile', { cnic: cnic }, function (r) {
                var p = r.profile || {}, h = note(p.note);
                h += '<h4>Identity</h4>' + gen((p.identity || []).map(function (a) {
                    return { attribute: a.label, values: (a.values || []).map(function (v) { return v.value + (v.sources ? ' (' + [].concat(v.sources).map(function (s) { return typeof s === 'object' ? (s.label || s.sourceLabel || '') : s; }).join(', ') + ')' : ''); }).join(' | '), status: a.status };
                }));
                if (p.conflicts && p.conflicts.length) h += '<h4>Conflicts</h4>' + gen(p.conflicts);
                if (p.knownNumbers && p.knownNumbers.length) h += '<h4>Known numbers</h4><p>' + p.knownNumbers.map(esc).join(', ') + '</p>';
                ['mobile', 'firs', 'vehicles', 'licences', 'employment', 'bvs'].forEach(function (k) {
                    var v = p[k];
                    if (!v || (Array.isArray(v) && !v.length)) return;
                    h += '<h4>' + esc(humanize(k)) + '</h4>';
                    if (Array.isArray(v) && v.length && v[0] && v[0].record) {
                        v.forEach(function (it) { h += '<p><strong>' + esc(it.display || it.text || '') + '</strong></p>' + gen(it.details || []) + record(it.record); });
                    } else if (Array.isArray(v) && v.length && v[0] && v[0].fields) {
                        v.forEach(function (rec) { h += record(rec); });
                    } else {
                        h += gen(v);
                    }
                });
                (p.sources || []).forEach(function (s) {
                    h += '<h4>' + esc(s.label) + ' <small>' + esc(s.statusLabel) + '</small></h4>' + note(s.message);
                    (s.records || []).forEach(function (rec) { h += record(rec); });
                });
                return h;
            });
        },
        cdr: function () {
            var n = $('#dbk_cdr_number').val(), body = period('dbk_cdr_from', 'dbk_cdr_to', n ? { numbers: [n] } : {});
            $('#dbk_cdr').html(loading());
            return $.when(api('cdr', $.extend({}, body)), api('cdr_activity', $.extend({}, body))).then(function (a, b) {
                var h = note(a.note);
                (a.numbers || []).forEach(function (x) {
                    h += '<h4>' + esc(x.display) + ' <small>' + esc(x.statusLabel) + '</small></h4>' + note(x.message) +
                         gen({ calls: (x.calls ? 'out ' + x.calls.out + ', in ' + x.calls.in : ''), sms: (x.sms ? 'out ' + x.sms.out + ', in ' + x.sms.in : ''),
                               duration: x.durationText, contacts: x.contactsText, period: [x.firstText, x.lastText].filter(Boolean).join(' – '), basis: x.basisLabel, note: x.note }) +
                         '<h5>Top contacts</h5>' + gen(x.topContacts || []) +
                         ((x.coverage && x.coverage.length) ? '<h5>Coverage</h5>' + gen(x.coverage) : '') +
                         ((x.registry && x.registry.length) ? '<h5>Registered to</h5>' + gen(x.registry) : '');
                });
                if (a.cross && a.cross.status !== 'skipped') h += '<h4>Across the numbers</h4>' + gen(a.cross);
                (b.numbers || []).forEach(function (x) {
                    h += '<h4>Activity — ' + esc(x.display) + '</h4>';
                    if (x.findings && x.findings.length) h += '<ul>' + x.findings.map(function (f) { return '<li>' + esc(f) + '</li>'; }).join('') + '</ul>';
                    h += gen({ peaks: x.peaks, night: x.night, dailyAverage: x.dailyAverage, unusualDays: x.unusualDays, inactivePeriods: x.inactivePeriods, newContacts: x.newContacts });
                    h += '<div class="row"><div class="col-md-6"><h5>By hour</h5>' + gen((x.byHour || []).map(function (y) { return { hour: y.label, calls: y.calls, sms: y.sms }; })) + '</div>' +
                         '<div class="col-md-6"><h5>By weekday</h5>' + gen((x.byWeekday || []).map(function (y) { return { day: y.name || y.label, calls: y.calls, sms: y.sms }; })) + '</div></div>';
                });
                $('#dbk_cdr').html(h || '<p class="text-muted">No call records.</p>');
            }, function (msg) { $('#dbk_cdr').html(err(msg)); });
        },
        location: function () {
            var n = $('#dbk_loc_number').val(), body = period('dbk_loc_from', 'dbk_loc_to', n ? { numbers: [n] } : {});
            return into('#dbk_location', 'location', body, function (r) {
                var h = r.caveat ? '<div class="callout callout-warning" style="padding:6px 12px;">' + esc(r.caveat) + '</div>' : '';
                (r.numbers || []).forEach(function (x) {
                    var ev = x.events || [];
                    h += '<h4>' + esc(x.display) + ' <small>' + esc(x.statusLabel) + '</small></h4>' +
                         '<h5>Frequent areas</h5>' + gen((x.areas || []).map(function (a) { return { area: a.label, records: a.records, sites: a.sites, period: [a.firstText, a.lastText].filter(Boolean).join(' – '), classification: a.classLabel, explanation: a.explanation }; })) +
                         '<h5>Sites</h5><div class="dbk-scroll">' + gen((x.sites || []).map(function (s) { return { site: s.label || s.cellSite, address: s.address, records: s.count, first: s.firstText, last: s.lastText, latitude: s.lat, longitude: s.lng }; })) + '</div>' +
                         '<h5>Location records' + (ev.length > 500 ? ' (first 500 of ' + ev.length + ' — export for all)' : '') + (x.truncated ? ' — truncated by the Databank' : '') + '</h5>' +
                         '<div class="dbk-scroll">' + gen(ev.slice(0, 500).map(function (e) { return { when: e.atText, until: e.untilText, kind: e.kindLabel, site: e.cellSite, area: e.area, 'other party': e.otherParty }; })) + '</div>';
                });
                ['overlaps', 'commonSites', 'commonAreas'].forEach(function (k) {
                    if (r[k] && r[k].length) h += '<h4>' + esc(humanize(k)) + '</h4>' + gen(r[k]);
                });
                return h;
            });
        },
        geo: function () {
            var pts = points($('#dbk_geo_points').val());
            if (!pts.length) { $('#dbk_geo').html('<p class="text-warning">Enter at least one point: latitude, longitude, radius in metres, label.</p>'); return $.Deferred().resolve(); }
            var n = $('#dbk_loc_number').val(), body = period('dbk_geo_from', 'dbk_geo_to', { points: pts });
            if (n) body.numbers = [n];
            return into('#dbk_geo', 'geo', body, function (r) {
                var h = r.caveat ? '<div class="callout callout-warning" style="padding:6px 12px;">' + esc(r.caveat) + '</div>' : '';
                if (r.insights && r.insights.length) h += '<ul>' + r.insights.map(function (i) { return '<li>' + (typeof i === 'object' ? esc(i.text || JSON.stringify(i)) : esc(i)) + '</li>'; }).join('') + '</ul>';
                h += '<h4>Points</h4>' + gen(r.points || []);
                if (r.correlation) h += '<h4>Correlation</h4>' + gen(r.correlation);
                return h;
            });
        },
        summary: function () {
            return api('summary', {}).then(function (r) { showSummary(r.summary || {}, null, null); }, function (msg) { $('#dbk_summary').html(err(msg)); });
        },
        export: function () { return $.Deferred().resolve(); }
    };

    $('a[data-toggle="tab"][data-tab]').on('shown.bs.tab', function () {
        var tab = $(this).data('tab');
        // Tabs with their own inputs load when asked; the others load on first view.
        if (!state.loaded[tab] && ['records', 'findings', 'graph', 'timeline'].indexOf(tab) >= 0) {
            state.loaded[tab] = true;
            loaders[tab]();
        }
    });
    $('.dbk-reload').on('click', function () {
        var tab = $(this).data('tab');
        state.loaded[tab] = true;
        loaders[tab]();
    });

    /* ---------------- graph ---------------- */

    var COLORS = { cnic: '#3c8dbc', person: '#00a65a', mobile: '#f39c12', number: '#f39c12', imei: '#605ca8', licence: '#39cccc',
                   vehicle: '#d81b60', fir: '#dd4b39', location: '#777', name: '#999' };
    function drawGraph(r) {
        var g = r.graph || {}, nodes = g.nodes || [], edges = g.edges || [];
        state.nodes = nodes;
        var opts = nodes.map(function (n) { return '<option value="' + esc(n.id) + '">' + esc(n.label) + '</option>'; }).join('');
        $('#dbk_node_a').html(opts);
        $('#dbk_node_b').html(opts);
        if (nodes.length > 1) $('#dbk_node_b').prop('selectedIndex', 1);

        var a = g.analysis || {}, h = note(a.note);
        if (g.truncated) h += note('The graph was truncated by the Databank.');
        if (a.mostConnected && a.mostConnected.length) h += '<h5>Most connected</h5>' + gen(a.mostConnected.map(function (m) { return { item: m.label, type: m.typeLabel, relationships: m.relationships }; }));
        if (a.groups && a.groups.length) h += '<h5>Connected groups</h5>' + gen(a.groups.map(function (x) { return { group: x.text, items: x.items }; }));
        if (a.connectors && a.connectors.length) h += '<h5>Connectors</h5>' + gen(a.connectors);
        if (a.brokers && a.brokers.length) h += '<h5>Brokers</h5>' + gen(a.brokers);
        $('#dbk_graph_analysis').html(h);

        if (typeof cytoscape !== 'function') { $('#dbk_graph').html(gen(edges.map(function (e) { return { relationship: e.label, explanation: e.explanation, classification: e.classLabel }; }))); return; }
        var ids = {};
        nodes.forEach(function (n) { ids[n.id] = true; });
        var cy = cytoscape({
            container: document.getElementById('dbk_graph'),
            layout: { name: 'cose', padding: 10 },
            style: cytoscape.stylesheet()
                .selector('node').css({ 'content': 'data(name)', 'font-size': '9px', 'text-valign': 'bottom', 'color': '#333',
                                        'background-color': 'data(color)', 'width': 22, 'height': 22 })
                .selector(':selected').css({ 'border-width': 3, 'border-color': '#333' })
                .selector('edge').css({ 'curve-style': 'bezier', 'width': 2, 'line-color': 'data(color)', 'target-arrow-shape': 'triangle',
                                        'target-arrow-color': 'data(color)', 'label': 'data(name)', 'font-size': '7px', 'color': '#666' })
                .selector('edge.possible').css({ 'line-style': 'dashed' })
                .selector('edge.derived').css({ 'line-style': 'dotted' }),
            elements: {
                nodes: nodes.map(function (n) { return { data: { id: n.id, name: n.label, color: COLORS[n.type] || '#999' } }; }),
                edges: edges.filter(function (e) { return ids[e.source_node] && ids[e.target_node]; }).map(function (e) {
                    var c = e.direct_or_derived || 'direct';
                    return { data: { id: e.id, source: e.source_node, target: e.target_node, name: e.label,
                                     color: c === 'direct' ? '#00a65a' : (c === 'derived' ? '#f39c12' : '#999') }, classes: c };
                })
            }
        });
        cy.on('tap', 'node', function (evt) {
            var id = evt.cyTarget ? evt.cyTarget.id() : evt.target.id();
            var n = null;
            state.nodes.forEach(function (x) { if (x.id === id) n = x; });
            if (!n) return;
            $('#dbk_graph_side').html('<h4>' + esc(n.label) + ' <small>' + esc(n.typeLabel) + '</small></h4>' +
                '<p class="small text-muted">' + esc((n.sourceLabels || []).join(', ')) + (n.reference ? ' · ' + esc(n.reference) : '') + '</p>' +
                (n.relationshipLines ? '<p>' + n.relationshipLines.map(esc).join('<br>') + '</p>' : '') + gen(n.details || []));
        });
        cy.on('tap', 'edge', function (evt) {
            var id = evt.cyTarget ? evt.cyTarget.id() : evt.target.id();
            into('#dbk_graph_side', 'relationship', { edgeId: id }, function (r) {
                var x = r.relationship || {};
                return '<h4>' + esc(x.fromLabel) + ' → ' + esc(x.toLabel) + '</h4>' + cls(x['class'], x.classLabel) +
                       '<p>' + esc(x.explanation) + '</p>' + note(r.period) + note(r.disclaimer) + fieldsTable(x.fields) +
                       ((r.records && r.records.length) ? '<h5>' + esc(r.evidenceLabel || 'Evidence') + '</h5>' + gen(r.records) : '');
            });
        });
    }
    $('#dbk_path').on('click', function () {
        into('#dbk_graph_side', 'path', { from: $('#dbk_node_a').val(), to: $('#dbk_node_b').val() }, function (r) {
            var p = r.path || {};
            if (!p.found) return '<p class="text-muted">' + esc(p.note || 'No connection path between these two items.') + '</p>';
            return '<h4>Connection path</h4>' + cls(p['class'], p.classLabel) + note(p.classNote) +
                   '<ol>' + (p.steps || []).map(function (s) { return '<li>' + esc(s) + '</li>'; }).join('') + '</ol>' + note(p.note);
        });
    });
    $('#dbk_indicators').on('click', function () {
        into('#dbk_graph_side', 'indicators', { a: $('#dbk_node_a').val(), b: $('#dbk_node_b').val(), location: $('#dbk_ind_location').is(':checked') }, function (r) {
            return '<h4>Indicators</h4><p>' + esc(r.summary) + '</p>' + note(r.note) +
                   gen((r.indicators || []).map(function (i) { return { factor: i.factor, classification: i.classLabel, source: i.sourceLabel, when: i.when, evidence: i.evidence }; }));
        });
    });

    /* ---------------- CDR records (cursor pages) ---------------- */

    function cdrRecords(cursor) {
        var n = $('#dbk_cdr_number').val();
        var body = period('dbk_cdr_from', 'dbk_cdr_to', { kind: $('#dbk_cdr_kind').val(), direction: $('#dbk_cdr_direction').val(), perPage: 50 });
        if (n) body.numbers = [n];
        if ($('#dbk_cdr_contact').val()) body.contact = $('#dbk_cdr_contact').val();
        if (cursor) body.cursor = cursor;
        $('#dbk_cdr_next').hide();
        into('#dbk_cdr_records', 'cdr_records', body, function (r) {
            state.cdrCursor = r.nextCursor || null;
            if (state.cdrCursor) $('#dbk_cdr_next').show();
            return gen((r.records || []).map(function (x) {
                return { when: x.atText, kind: x.kindLabel, direction: x.directionLabel, from: x.fromLabel, to: x.toLabel,
                         duration: x.durationText, site: x.cellSite, IMEI: x.imei, IMSI: x.imsi, operator: x.operator };
            })) || '<p class="text-muted">No records.</p>';
        });
    }
    $('#dbk_cdr_records_btn').on('click', function () { cdrRecords(null); });
    $('#dbk_cdr_next').on('click', function () { cdrRecords(state.cdrCursor); });

    /* ---------------- export ---------------- */

    $('.dbk-export').on('click', function () {
        var kind = $(this).data('kind'), status = $('#dbk_export_status');
        var fd = new FormData();
        fd.append('searchId', state.sid);
        fd.append('kind', kind);
        var n = $('#dbk_cdr_number').val();
        if (n && (kind === 'cdr' || kind === 'location')) fd.append('numbers', n);
        status.html('<i class="fa fa-spinner fa-spin"></i> Preparing the file…');
        var xhr = new XMLHttpRequest();
        xhr.open('POST', EXPORT_URL);
        xhr.responseType = 'blob';
        xhr.onload = function () {
            if (xhr.status !== 200) {
                var reader = new FileReader();
                reader.onload = function () { status.html('<span class="text-danger">' + esc(reader.result) + '</span>'); };
                reader.readAsText(xhr.response);
                return;
            }
            var m = /filename="([^"]+)"/.exec(xhr.getResponseHeader('Content-Disposition') || '');
            var a = document.createElement('a');
            a.href = URL.createObjectURL(xhr.response);
            a.download = m ? m[1] : 'databank-' + kind + '.csv';
            document.body.appendChild(a);
            a.click();
            setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 1000);
            status.text('Downloaded.');
        };
        xhr.onerror = function () { status.html('<span class="text-danger">The export failed.</span>'); };
        xhr.send(fd);
    });
})();
</script>
