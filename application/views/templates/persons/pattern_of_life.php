<?php
/*
 * Person -> Location Summary -> Pattern of Life.
 * Variables: $person_id (int), $phone_numbers (Helpers_PatternOfLife::phone_numbers),
 * $data_url (persons/pattern_of_life_data). All data is loaded as JSON and
 * rendered with textContent, never as HTML.
 */
$enc_id = isset($_GET['id']) ? (string) $_GET['id'] : '';
?>
<section class="content-header">
    <h1>
        Person's Portal
        <small><?php echo HTML::chars(Helpers_Person::get_person_name($person_id)); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id=' . urlencode($enc_id)); ?>">Person Dashboard</a></li>
        <li class="active">Pattern of Life</li>
    </ol>
</section>

<section class="content pol">
    <!-- Filters -->
    <div class="box box-primary">
        <div class="box-body">
            <form id="pol-form" class="row" autocomplete="off">
                <div class="col-sm-4 col-md-3">
                    <label for="pol-phone">Mobile number</label>
                    <select id="pol-phone" class="form-control">
                        <option value="">All numbers</option>
                        <?php foreach ($phone_numbers as $p): ?>
                            <option value="<?php echo HTML::chars($p['phone']); ?>"><?php echo HTML::chars($p['phone']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-3 col-md-2">
                    <label for="pol-from">From</label>
                    <input type="date" id="pol-from" class="form-control">
                </div>
                <div class="col-sm-3 col-md-2">
                    <label for="pol-to">To</label>
                    <input type="date" id="pol-to" class="form-control">
                </div>
                <div class="col-sm-2 col-md-2">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block" id="pol-apply"><i class="fa fa-search"></i> Analyse</button>
                </div>
                <div class="col-sm-3 col-md-1">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-default btn-block" id="pol-report" title="Printable report of what is on screen, with an integrity code" disabled><i class="fa fa-print"></i> Report</button>
                </div>
                <div class="col-md-2 pol-hint">
                    Default: the last 90 days of records. At most 366 days.
                </div>
            </form>
        </div>
    </div>

    <div id="pol-status" class="pol-status"><i class="fa fa-spinner fa-spin"></i> Analysing call and SMS records…</div>

    <div id="pol-result" style="display:none">
        <!-- Summary tiles -->
        <div class="row pol-tiles">
            <div class="col-sm-6 col-md-3"><div class="pol-tile pol-home">
                <div class="pol-tile-label"><i class="fa fa-home"></i> Likely home (night tower)</div>
                <div class="pol-tile-value" id="pol-home">—</div>
                <div class="pol-tile-sub" id="pol-home-sub"></div>
            </div></div>
            <div class="col-sm-6 col-md-3"><div class="pol-tile pol-work">
                <div class="pol-tile-label"><i class="fa fa-briefcase"></i> Likely work (weekday day tower)</div>
                <div class="pol-tile-value" id="pol-work">—</div>
                <div class="pol-tile-sub" id="pol-work-sub"></div>
            </div></div>
            <div class="col-sm-6 col-md-3"><div class="pol-tile">
                <div class="pol-tile-label"><i class="fa fa-clock-o"></i> Most active hours</div>
                <div class="pol-tile-value" id="pol-peak">—</div>
                <div class="pol-tile-sub" id="pol-volume"></div>
            </div></div>
            <div class="col-sm-6 col-md-3"><div class="pol-tile pol-alerts">
                <div class="pol-tile-label"><i class="fa fa-exclamation-triangle"></i> Anomalies</div>
                <div class="pol-tile-value" id="pol-anom-count">0</div>
                <div class="pol-tile-sub" id="pol-anom-sub"></div>
            </div></div>
        </div>

        <!-- Incident check -->
        <div class="box box-danger pol-at">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-crosshairs"></i> Where was the person at…?</h3>
                <span class="pol-at-hint">Check the records around an incident time, and optionally their distance from the incident site.</span>
            </div>
            <div class="box-body">
                <form id="pol-at-form" class="row" autocomplete="off">
                    <div class="col-sm-3 col-md-2">
                        <label for="pol-at-date">Date</label>
                        <input type="date" id="pol-at-date" class="form-control" required>
                    </div>
                    <div class="col-sm-3 col-md-2">
                        <label for="pol-at-time">Time</label>
                        <input type="time" id="pol-at-time" class="form-control" required>
                    </div>
                    <div class="col-sm-3 col-md-2">
                        <label for="pol-at-window">Window (±)</label>
                        <select id="pol-at-window" class="form-control">
                            <option value="15">15 min</option>
                            <option value="30">30 min</option>
                            <option value="60" selected>1 hour</option>
                            <option value="180">3 hours</option>
                            <option value="360">6 hours</option>
                            <option value="720">12 hours</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label for="pol-at-lat">Incident site (optional)</label>
                        <div class="input-group">
                            <input type="text" id="pol-at-lat" class="form-control" placeholder="Latitude" inputmode="decimal">
                            <span class="input-group-btn" style="width:0"></span>
                            <input type="text" id="pol-at-lng" class="form-control" placeholder="Longitude" inputmode="decimal" style="border-left:0">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default" id="pol-at-pick" title="Click on the map to set the site"><i class="fa fa-map-pin"></i> Pick on map</button>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-3 col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-danger btn-block" id="pol-at-go"><i class="fa fa-search"></i> Check</button>
                    </div>
                </form>
                <div id="pol-at-result" style="display:none">
                    <div id="pol-at-summary" class="pol-at-summary"></div>
                    <div class="table-responsive">
                        <table class="table table-condensed pol-table">
                            <thead><tr><th>Time</th><th>Offset</th><th>Type</th><th>Other party</th><th>Number used</th><th>Tower / place</th><th>km from site</th><th>km from home</th></tr></thead>
                            <tbody id="pol-at-rows"></tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-xs btn-default" id="pol-at-clear"><i class="fa fa-times"></i> Clear check</button>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Map + playback -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-map"></i> Places and movement</h3>
                        <div class="box-tools pull-right pol-mode">
                            <button type="button" class="btn btn-xs btn-primary" data-mode="places">All places</button>
                            <button type="button" class="btn btn-xs btn-default" data-mode="day">Day by day</button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div id="pol-map" class="pol-map"></div>
                        <div id="pol-map-note" class="pol-note"></div>
                        <div id="pol-playback" class="pol-playback" style="display:none">
                            <div class="pol-play-row">
                                <button type="button" class="btn btn-default btn-sm" id="pol-prev" title="Previous day"><i class="fa fa-step-backward"></i></button>
                                <button type="button" class="btn btn-primary btn-sm" id="pol-play" title="Play"><i class="fa fa-play"></i></button>
                                <button type="button" class="btn btn-default btn-sm" id="pol-next" title="Next day"><i class="fa fa-step-forward"></i></button>
                                <input type="range" id="pol-day" min="0" max="0" value="0">
                                <strong id="pol-day-label"></strong>
                            </div>
                            <ol id="pol-stops" class="pol-stops"></ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Anomalies -->
            <div class="col-md-4">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-exclamation-triangle"></i> Anomalies</h3>
                    </div>
                    <div class="box-body pol-scroll">
                        <ul id="pol-anomalies" class="pol-anomalies"></ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Activity grid -->
            <div class="col-md-7">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-th"></i> When active (calls + SMS, hour × weekday)</h3>
                    </div>
                    <div class="box-body"><div class="pol-grid-wrap"><table id="pol-grid" class="pol-grid"></table></div></div>
                </div>
            </div>
            <!-- Daily volume -->
            <div class="col-md-5">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Daily activity</h3>
                    </div>
                    <div class="box-body">
                        <div id="pol-daily" class="pol-daily"></div>
                        <div class="pol-daily-axis"><span id="pol-daily-from"></span><span id="pol-daily-to"></span></div>
                        <div class="pol-note">Gaps are days with no calls or SMS.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Key places -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-map-marker"></i> Key places</h3>
                    </div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover table-condensed pol-table">
                            <thead><tr><th>#</th><th>Tower / place</th><th>Calls+SMS</th><th>Days</th><th>Night</th><th>Work hrs</th><th>First seen</th><th>Last seen</th></tr></thead>
                            <tbody id="pol-places"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Devices -->
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-mobile"></i> Handsets and SIMs in this period</h3>
                    </div>
                    <div class="box-body pol-devices-note" id="pol-devices-note"></div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-condensed pol-table">
                            <thead><tr><th>Type</th><th>Number</th><th>First</th><th>Last</th></tr></thead>
                            <tbody id="pol-devices"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <p class="pol-note">Places are cell towers, not exact addresses. "Home" is the tower used most between 22:00 and 06:00; "work" is the tower used most on Monday–Friday between 09:00 and 17:00. Towers whose operator sent no coordinates are listed but cannot be shown on the map.</p>
    </div>
</section>

<style>
.pol .pol-hint { color:#777; font-size:12px; padding-top:28px; }
.pol .pol-status { padding:14px 16px; background:#fff; border:1px solid #ddd; border-radius:3px; margin-bottom:15px; }
.pol .pol-status.error { color:#a94442; background:#f2dede; border-color:#ebccd1; }
.pol .pol-tile { background:#fff; border:1px solid #ddd; border-left:4px solid #3c8dbc; border-radius:3px; padding:10px 12px; margin-bottom:15px; min-height:96px; }
.pol .pol-home { border-left-color:#00a65a; }
.pol .pol-work { border-left-color:#605ca8; }
.pol .pol-alerts { border-left-color:#f39c12; }
.pol .pol-tile-label { color:#666; font-size:12px; text-transform:uppercase; letter-spacing:.3px; }
.pol .pol-tile-value { font-size:17px; font-weight:600; margin:4px 0 2px; overflow-wrap:anywhere; }
.pol .pol-tile-sub { color:#777; font-size:12px; }
.pol .pol-map { height:440px; background:#eef1f4; border-radius:3px; }
.pol .pol-note { color:#777; font-size:12px; margin-top:6px; }
.pol .pol-playback { margin-top:10px; }
.pol .pol-play-row { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.pol .pol-play-row input[type=range] { flex:1; min-width:140px; }
.pol .pol-stops { margin:10px 0 0; padding-left:22px; max-height:180px; overflow:auto; font-size:13px; }
.pol .pol-stops li { padding:2px 0; }
.pol .pol-stops .nomap { color:#999; }
.pol .pol-scroll { max-height:470px; overflow:auto; }
.pol .pol-anomalies { list-style:none; margin:0; padding:0; }
.pol .pol-anomalies li { padding:8px 4px; border-bottom:1px solid #f0f0f0; font-size:13px; display:flex; gap:8px; }
.pol .pol-anomalies li.clickable { cursor:pointer; }
.pol .pol-anomalies li.clickable:hover { background:#fafafa; }
.pol .pol-anomalies .fa { width:16px; margin-top:2px; }
.pol .a-silent .fa { color:#777; } .pol .a-spike .fa { color:#dd4b39; }
.pol .a-device .fa { color:#605ca8; } .pol .a-travel .fa { color:#f39c12; }
.pol .pol-grid-wrap { overflow-x:auto; }
.pol .pol-grid { border-collapse:separate; border-spacing:2px; font-size:11px; width:100%; }
.pol .pol-grid th { color:#777; font-weight:normal; text-align:center; padding:0 2px; }
.pol .pol-grid td { height:22px; min-width:18px; border-radius:2px; background:#f4f6f8; }
.pol .pol-daily { display:flex; align-items:flex-end; height:150px; gap:1px; border-bottom:1px solid #ddd; }
.pol .pol-daily div { flex:1; background:#3c8dbc; min-width:1px; }
.pol .pol-daily div.zero { background:transparent; }
.pol .pol-daily-axis { display:flex; justify-content:space-between; color:#777; font-size:11px; margin-top:3px; }
.pol .pol-table td, .pol .pol-table th { font-size:13px; }
.pol .pol-devices-note { color:#666; font-size:12px; padding-bottom:0; }
.pol .pol-at-hint { color:#777; font-size:12px; margin-left:8px; }
.pol .pol-at-summary { margin:12px 0 8px; padding:9px 12px; border-left:4px solid #dd4b39; background:#fdf3f3; font-size:14px; }
.pol .pol-at-summary.error { border-left-color:#a94442; background:#f2dede; color:#a94442; }
.pol .pol-at tr.window-edge td { color:#888; font-style:italic; }
.pol .pol-at tr.closest td { font-weight:600; background:#fff7e6; }
.pol .pol-map.picking { cursor:crosshair; outline:3px solid #dd4b39; }
.pol .pol-table tr.clickable { cursor:pointer; }
.pol .pol-badge { display:inline-block; padding:0 5px; border-radius:3px; font-size:11px; color:#fff; margin-left:4px; }
.pol .pol-badge.home { background:#00a65a; } .pol .pol-badge.work { background:#605ca8; }
.pol .pol-badge.nomap { background:#aaa; }
</style>

<script>
(function () {
    var DATA_URL = <?php echo json_encode($data_url); ?>;
    var AT_URL     = <?php echo json_encode($at_url); ?>;
    var REPORT_URL = <?php echo json_encode($report_url); ?>;
    var PERSON   = <?php echo json_encode($enc_id); ?>;
    var MAPS_KEY = 'AIzaSyBwL7lXZwan5Cp6GjddHiNNM3VJhZ3oYvE';
    var DAYS     = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    var data = null, map = null, mapsReady = false, overlays = [], mode = 'places';
    var trackDays = [], dayIndex = 0, playTimer = null;
    var atResult = null, atOverlays = [], picking = false, siteMarker = null;

    function $(id) { return document.getElementById(id); }
    function el(tag, text, cls) {
        var e = document.createElement(tag);
        if (text !== undefined && text !== null) e.textContent = text;
        if (cls) e.className = cls;
        return e;
    }
    function fmtDate(s) {
        if (!s) return '';
        var d = new Date(s.replace(' ', 'T'));
        return isNaN(d) ? s : d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }
    function hourLabel(h) { return (h < 10 ? '0' : '') + h + ':00'; }
    function placeName(p) {
        if (p.address) return p.address;
        return p.lat !== null ? p.lat.toFixed(5) + ', ' + p.lng.toFixed(5) : p.key;
    }

    /* ---------------- Loading ---------------- */

    function load() {
        stopPlay();
        var params = new URLSearchParams({ id: PERSON, phone: $('pol-phone').value, from: $('pol-from').value, to: $('pol-to').value });
        var status = $('pol-status');
        status.className = 'pol-status';
        status.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Analysing call and SMS records…';
        status.style.display = '';
        $('pol-apply').disabled = true;
        fetch(DATA_URL + '?' + params.toString(), { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json().catch(function () { return { ok: false, error: 'Unexpected answer from the server (HTTP ' + r.status + ').' }; }); })
            .then(function (json) {
                $('pol-apply').disabled = false;
                if (!json || !json.ok) {
                    status.className = 'pol-status error';
                    status.textContent = (json && json.error) ? json.error : 'The analysis could not be loaded.';
                    $('pol-result').style.display = 'none';
                    return;
                }
                data = json;
                $('pol-report').disabled = false;
                status.style.display = 'none';
                $('pol-result').style.display = '';
                $('pol-from').value = json.filters.from;
                $('pol-to').value = json.filters.to;
                render();
            })
            .catch(function () {
                $('pol-apply').disabled = false;
                status.className = 'pol-status error';
                status.textContent = 'The server could not be reached. Check your connection and try again.';
            });
    }

    /* ---------------- Rendering ---------------- */

    function render() {
        renderTiles();
        renderAnomalies();
        renderGrid();
        renderDaily();
        renderPlaces();
        renderDevices();
        trackDays = [];
        var seen = {};
        data.track.stops.forEach(function (s) { if (!seen[s.day]) { seen[s.day] = 1; trackDays.push(s.day); } });
        $('pol-day').max = Math.max(0, trackDays.length - 1);
        dayIndex = Math.max(0, trackDays.length - 1);
        $('pol-day').value = dayIndex;
        drawMap();
    }

    function renderTiles() {
        var t = data.totals;
        function anchor(p, key, idValue, idSub, what) {
            if (!p) {
                $(idValue).textContent = 'Not enough data';
                $(idSub).textContent = 'Needs ' + what + ' activity at one tower';
                return;
            }
            $(idValue).textContent = placeName(p);
            $(idSub).textContent = p.share + '% of ' + what + ' activity · ' + p.days + ' day(s)';
        }
        anchor(data.home, 'night', 'pol-home', 'pol-home-sub', 'night-time');
        anchor(data.work, 'work', 'pol-work', 'pol-work-sub', 'working-hours');
        $('pol-peak').textContent = data.peak.length ? data.peak.map(function (p) { return hourLabel(p.hour); }).join(', ') : '—';
        $('pol-volume').textContent = t.calls + ' calls · ' + t.sms + ' SMS · ' + t.days_active + ' active day(s)';
        var counts = {};
        data.anomalies.forEach(function (a) { counts[a.type] = (counts[a.type] || 0) + 1; });
        $('pol-anom-count').textContent = data.anomalies.length;
        var names = { silent: 'silent', spike: 'busy day', device: 'new device', travel: 'travel' };
        $('pol-anom-sub').textContent = Object.keys(counts).map(function (k) { return counts[k] + ' ' + names[k]; }).join(' · ') || 'None found';
    }

    function renderAnomalies() {
        var ul = $('pol-anomalies');
        ul.innerHTML = '';
        if (!data.anomalies.length) {
            ul.appendChild(el('li', 'No anomalies in this period.'));
            return;
        }
        var icons = { silent: 'fa-volume-off', spike: 'fa-line-chart', device: 'fa-mobile', travel: 'fa-road' };
        data.anomalies.forEach(function (a) {
            var li = el('li', null, 'a-' + a.type);
            var i = el('i', null, 'fa ' + icons[a.type]);
            li.appendChild(i);
            li.appendChild(el('span', a.text));
            if (a.lat !== undefined && a.lat !== null) {
                li.className += ' clickable';
                li.title = 'Show on map';
                li.addEventListener('click', function () { focus(a.lat, a.lng); });
            }
            ul.appendChild(li);
        });
    }

    function renderGrid() {
        var table = $('pol-grid');
        table.innerHTML = '';
        var max = 0;
        data.grid.forEach(function (row) { row.forEach(function (n) { if (n > max) max = n; }); });
        var head = el('tr');
        head.appendChild(el('th', ''));
        for (var h = 0; h < 24; h++) head.appendChild(el('th', h % 3 === 0 ? String(h) : ''));
        table.appendChild(head);
        data.grid.forEach(function (row, d) {
            var tr = el('tr');
            tr.appendChild(el('th', DAYS[d]));
            row.forEach(function (n, h) {
                var td = el('td');
                if (n > 0) {
                    var a = 0.12 + 0.88 * (n / max);
                    td.style.background = 'rgba(60,141,188,' + a.toFixed(2) + ')';
                }
                td.title = DAYS[d] + ' ' + hourLabel(h) + ' – ' + n + ' call(s)/SMS';
                tr.appendChild(td);
            });
            table.appendChild(tr);
        });
    }

    function renderDaily() {
        var box = $('pol-daily');
        box.innerHTML = '';
        var byDay = {}, max = 1;
        data.daily.forEach(function (d) { var n = d.calls + d.sms; byDay[d.day] = d; if (n > max) max = n; });
        var day = new Date(data.filters.from + 'T00:00:00'), end = new Date(data.filters.to + 'T00:00:00');
        while (day <= end) {
            var key = day.getFullYear() + '-' + String(day.getMonth() + 1).padStart(2, '0') + '-' + String(day.getDate()).padStart(2, '0');
            var d = byDay[key], n = d ? d.calls + d.sms : 0;
            var bar = el('div', null, n ? '' : 'zero');
            bar.style.height = n ? Math.max(2, Math.round(100 * n / max)) + '%' : '100%';
            bar.title = fmtDate(key) + ': ' + (d ? d.calls + ' calls, ' + d.sms + ' SMS, ' + d.places + ' place(s)' : 'no activity');
            box.appendChild(bar);
            day.setDate(day.getDate() + 1);
        }
        $('pol-daily-from').textContent = fmtDate(data.filters.from);
        $('pol-daily-to').textContent = fmtDate(data.filters.to);
    }

    function renderPlaces() {
        var tbody = $('pol-places');
        tbody.innerHTML = '';
        if (!data.places.length) {
            var tr = el('tr'), td = el('td', 'No tower information in this period.');
            td.colSpan = 8; tr.appendChild(td); tbody.appendChild(tr);
            return;
        }
        data.places.slice(0, 25).forEach(function (p, i) {
            var tr = el('tr');
            tr.appendChild(el('td', String(i + 1)));
            var name = el('td', placeName(p));
            if (data.home && p.key === data.home.key) name.appendChild(el('span', 'Home', 'pol-badge home'));
            if (data.work && p.key === data.work.key) name.appendChild(el('span', 'Work', 'pol-badge work'));
            if (p.lat === null) name.appendChild(el('span', 'no map', 'pol-badge nomap'));
            tr.appendChild(name);
            tr.appendChild(el('td', String(p.n)));
            tr.appendChild(el('td', String(p.days)));
            tr.appendChild(el('td', String(p.night)));
            tr.appendChild(el('td', String(p.work)));
            tr.appendChild(el('td', fmtDate(p.first)));
            tr.appendChild(el('td', fmtDate(p.last)));
            if (p.lat !== null) {
                tr.className = 'clickable';
                tr.title = 'Show on map';
                tr.addEventListener('click', function () { focus(p.lat, p.lng); });
            }
            tbody.appendChild(tr);
        });
    }

    function renderDevices() {
        var tbody = $('pol-devices');
        tbody.innerHTML = '';
        var rows = [];
        data.devices.imei.forEach(function (d) { rows.push(['Handset (IMEI)', d]); });
        data.devices.imsi.forEach(function (d) { rows.push(['SIM (IMSI)', d]); });
        var all = data.devices_all_time || { imei: 0, imsi: 0 };
        var note = data.devices.imei.length + ' handset(s) and ' + data.devices.imsi.length + ' SIM(s) used between '
                 + fmtDate(data.filters.from) + ' and ' + fmtDate(data.filters.to) + '. All time in the CDRs: '
                 + all.imei + ' handset(s), ' + all.imsi + ' SIM(s).';
        if (all.imei > data.devices.imei.length || all.imsi > data.devices.imsi.length) note += ' Change the dates to see the others.';
        $('pol-devices-note').textContent = note;
        if (!rows.length) {
            var tr = el('tr'), td = el('td', 'No IMEI / IMSI in this period.');
            td.colSpan = 4; tr.appendChild(td); tbody.appendChild(tr);
            return;
        }
        rows.forEach(function (r) {
            var tr = el('tr');
            tr.appendChild(el('td', r[0]));
            tr.appendChild(el('td', r[1].id));
            tr.appendChild(el('td', fmtDate(r[1].first)));
            tr.appendChild(el('td', fmtDate(r[1].last)));
            tbody.appendChild(tr);
        });
    }

    /* ---------------- Map ---------------- */

    function clearMap() {
        overlays.forEach(function (o) { o.setMap(null); });
        overlays = [];
    }

    function mapNote(text) { $('pol-map-note').textContent = text; }

    function drawMap() {
        $('pol-playback').style.display = (mode === 'day') ? '' : 'none';
        if (mode === 'day') renderStops();
        if (!data) return;
        if (!mapsReady) {
            mapNote(window.google ? '' : 'Loading map…');
            return;
        }
        clearMap();
        var located = data.places.filter(function (p) { return p.lat !== null; });
        if (!located.length) {
            mapNote('None of this person\'s towers in this period have coordinates, so nothing can be drawn on the map. See Key places below.');
            return;
        }
        var bounds = new google.maps.LatLngBounds();
        if (mode === 'places') {
            var max = located[0].n;
            located.forEach(function (p) {
                var pos = { lat: p.lat, lng: p.lng };
                var circle = new google.maps.Circle({
                    map: map, center: pos, radius: 150 + 1350 * Math.sqrt(p.n / max),
                    strokeWeight: 0, fillColor: '#dd4b39', fillOpacity: 0.15 + 0.45 * Math.sqrt(p.n / max), clickable: false
                });
                overlays.push(circle);
                bounds.extend(pos);
            });
            [['home', 'H', '#00a65a'], ['work', 'W', '#605ca8']].forEach(function (a) {
                var p = data[a[0]];
                if (p && p.lat !== null) overlays.push(marker(p, a[1], a[2], (a[0] === 'home' ? 'Likely home: ' : 'Likely work: ') + placeName(p)));
            });
            var hidden = data.places.length - located.length;
            mapNote(located.length + ' tower(s) on the map; circle size = activity.' + (hidden ? ' ' + hidden + ' tower(s) have no coordinates.' : ''));
        } else {
            var day = trackDays[dayIndex];
            var stops = data.track.stops.filter(function (s) { return s.day === day && s.lat !== null; });
            var path = [];
            stops.forEach(function (s, i) {
                var pos = { lat: s.lat, lng: s.lng };
                path.push(pos);
                bounds.extend(pos);
                overlays.push(marker(s, String(i + 1), '#3c8dbc', (i + 1) + '. ' + s.first + '–' + s.last + ' ' + placeName(s)));
            });
            if (path.length > 1) {
                overlays.push(new google.maps.Polyline({
                    map: map, path: path, strokeColor: '#3c8dbc', strokeWeight: 3, strokeOpacity: 0.8,
                    icons: [{ icon: { path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW, scale: 2.5 }, offset: '50%', repeat: '120px' }]
                }));
            }
            if (!stops.length) {
                mapNote('No towers with coordinates on this day.');
                return;
            }
            mapNote(stops.length + ' place(s) on the map for this day, in time order.');
        }
        map.fitBounds(bounds);
        if (map.getZoom() > 15) map.setZoom(15);
    }

    function marker(p, label, color, title) {
        return new google.maps.Marker({
            map: map, position: { lat: p.lat, lng: p.lng }, title: title,
            label: { text: label, color: '#fff', fontSize: '11px', fontWeight: 'bold' },
            icon: { path: google.maps.SymbolPath.CIRCLE, scale: 11, fillColor: color, fillOpacity: 1, strokeColor: '#fff', strokeWeight: 2 }
        });
    }

    function focus(lat, lng) {
        if (!mapsReady) return;
        map.panTo({ lat: lat, lng: lng });
        map.setZoom(14);
        document.getElementById('pol-map').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    /* ---------------- Day-by-day playback ---------------- */

    function renderStops() {
        var ol = $('pol-stops');
        ol.innerHTML = '';
        if (!trackDays.length) {
            $('pol-day-label').textContent = 'No tower information';
            return;
        }
        var day = trackDays[dayIndex];
        $('pol-day-label').textContent = fmtDate(day) + ' (' + (dayIndex + 1) + ' of ' + trackDays.length + ' days)';
        data.track.stops.filter(function (s) { return s.day === day; }).forEach(function (s) {
            var li = el('li', s.first + (s.last !== s.first ? '–' + s.last : '') + '  ' + placeName(s) + '  (' + s.n + ')', s.lat === null ? 'nomap' : '');
            ol.appendChild(li);
        });
        if (data.track.truncated) ol.appendChild(el('li', 'Only the first ' + data.track.stops.length + ' stops are shown; choose a shorter range.', 'nomap'));
    }

    function setDay(i) {
        if (!trackDays.length) return;
        dayIndex = Math.min(trackDays.length - 1, Math.max(0, i));
        $('pol-day').value = dayIndex;
        drawMap();
    }

    function stopPlay() {
        if (playTimer) { clearInterval(playTimer); playTimer = null; }
        $('pol-play').innerHTML = '<i class="fa fa-play"></i>';
    }

    function togglePlay() {
        if (playTimer) { stopPlay(); return; }
        if (dayIndex >= trackDays.length - 1) setDay(0);
        $('pol-play').innerHTML = '<i class="fa fa-pause"></i>';
        playTimer = setInterval(function () {
            if (dayIndex >= trackDays.length - 1) { stopPlay(); return; }
            setDay(dayIndex + 1);
        }, 1200);
    }

    /* ---------------- Incident check ---------------- */

    function kmBetween(a, b) {
        if (!a || !b || a.lat === null || b.lat === null || a.lat === undefined || b.lat === undefined) return null;
        var r = 6371, toRad = Math.PI / 180;
        var dLat = (b.lat - a.lat) * toRad, dLng = (b.lng - a.lng) * toRad;
        var h = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(a.lat * toRad) * Math.cos(b.lat * toRad) * Math.sin(dLng / 2) * Math.sin(dLng / 2);
        var km = 2 * r * Math.asin(Math.min(1, Math.sqrt(h)));
        return km < 10 ? Math.round(km * 10) / 10 : Math.round(km);
    }

    function checkAt() {
        var date = $('pol-at-date').value, time = $('pol-at-time').value;
        if (!date || !time) return;
        var params = new URLSearchParams({
            id: PERSON, phone: data ? data.filters.phone : $('pol-phone').value,
            at: date + ' ' + time, window: $('pol-at-window').value,
            lat: $('pol-at-lat').value.trim(), lng: $('pol-at-lng').value.trim()
        });
        $('pol-at-go').disabled = true;
        fetch(AT_URL + '?' + params.toString(), { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json().catch(function () { return { ok: false, error: 'Unexpected answer from the server (HTTP ' + r.status + ').' }; }); })
            .then(function (json) {
                $('pol-at-go').disabled = false;
                $('pol-at-result').style.display = '';
                var summary = $('pol-at-summary');
                if (!json || !json.ok) {
                    atResult = null;
                    summary.className = 'pol-at-summary error';
                    summary.textContent = (json && json.error) ? json.error : 'The check could not be completed.';
                    $('pol-at-rows').innerHTML = '';
                    clearAt();
                    return;
                }
                atResult = json;
                summary.className = 'pol-at-summary';
                summary.textContent = json.summary;
                renderAtRows();
                drawAt(true);
            })
            .catch(function () {
                $('pol-at-go').disabled = false;
                $('pol-at-result').style.display = '';
                $('pol-at-summary').className = 'pol-at-summary error';
                $('pol-at-summary').textContent = 'The server could not be reached.';
            });
    }

    function atRows() {
        if (!atResult) return [];
        if (atResult.records.length) return atResult.records;
        return [atResult.before, atResult.after].filter(function (r) { return r; });
    }

    function renderAtRows() {
        var tbody = $('pol-at-rows');
        tbody.innerHTML = '';
        var rows = atRows(), inWindow = atResult.records.length > 0;
        if (!rows.length) {
            var tr = el('tr'), td = el('td', 'No records for this person before or after this time.');
            td.colSpan = 8; tr.appendChild(td); tbody.appendChild(tr);
            return;
        }
        rows.forEach(function (r) {
            var tr = el('tr', null, inWindow ? '' : 'window-edge');
            if (atResult.closest && r.t === atResult.closest.t && r.kind === atResult.closest.kind) tr.className += ' closest';
            tr.appendChild(el('td', r.t.substr(0, 16)));
            tr.appendChild(el('td', (r.minutes > 0 ? '+' : '') + r.minutes + ' min'));
            tr.appendChild(el('td', (r.kind === 'call' ? 'Call' : 'SMS') + ' ' + (r.outgoing ? 'out' : 'in') + (r.kind === 'call' ? ' · ' + r.duration + 's' : '')));
            tr.appendChild(el('td', r.other || '—'));
            tr.appendChild(el('td', r.phone || '—'));
            var place = el('td', placeName(r));
            if (data && data.home && r.place === data.home.key) place.appendChild(el('span', 'Home', 'pol-badge home'));
            if (r.lat === null) place.appendChild(el('span', 'no map', 'pol-badge nomap'));
            tr.appendChild(place);
            tr.appendChild(el('td', r.km_site !== null ? String(r.km_site) : '—'));
            var kh = data ? kmBetween(r, data.home) : null;
            tr.appendChild(el('td', kh !== null ? String(kh) : '—'));
            if (r.lat !== null) {
                tr.style.cursor = 'pointer';
                tr.title = 'Show on map';
                tr.addEventListener('click', function () { focus(r.lat, r.lng); });
            }
            tbody.appendChild(tr);
        });
        if (!inWindow) {
            var note = el('tr', null, 'window-edge'), td2 = el('td', 'Nothing inside the window — showing the nearest record before and after it.');
            td2.colSpan = 8; note.appendChild(td2); tbody.appendChild(note);
        }
    }

    function clearAt() {
        atOverlays.forEach(function (o) { o.setMap(null); });
        atOverlays = [];
    }

    function drawAt(fit) {
        if (!mapsReady) return;
        clearAt();
        if (!atResult) return;
        var bounds = new google.maps.LatLngBounds(), any = false;
        var site = atResult.query.site;
        if (site) {
            atOverlays.push(new google.maps.Marker({
                map: map, position: { lat: site.lat, lng: site.lng }, title: 'Incident site', zIndex: 1000,
                label: { text: '✕', color: '#fff', fontSize: '13px', fontWeight: 'bold' },
                icon: { path: google.maps.SymbolPath.CIRCLE, scale: 13, fillColor: '#b00', fillOpacity: 1, strokeColor: '#fff', strokeWeight: 2 }
            }));
            atOverlays.push(new google.maps.Circle({ map: map, center: site, radius: 1000, strokeColor: '#b00', strokeWeight: 1, fillOpacity: 0.04, clickable: false }));
            bounds.extend(site); any = true;
            if (siteMarker) { siteMarker.setMap(null); siteMarker = null; }
        }
        atRows().forEach(function (r) {
            if (r.lat === null) return;
            var label = (r.minutes > 0 ? '+' : '') + r.minutes;
            atOverlays.push(new google.maps.Marker({
                map: map, position: { lat: r.lat, lng: r.lng }, zIndex: 900,
                title: r.t.substr(0, 16) + ' (' + label + ' min) ' + placeName(r),
                label: { text: label.length > 4 ? '•' : label, color: '#fff', fontSize: '10px', fontWeight: 'bold' },
                icon: { path: google.maps.SymbolPath.CIRCLE, scale: 12, fillColor: '#f39c12', fillOpacity: 1, strokeColor: '#fff', strokeWeight: 2 }
            }));
            bounds.extend({ lat: r.lat, lng: r.lng }); any = true;
        });
        if (fit && any) {
            map.fitBounds(bounds);
            if (map.getZoom() > 15) map.setZoom(15);
            $('pol-map').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function setPicking(on) {
        picking = on;
        $('pol-map').classList.toggle('picking', on);
        $('pol-at-pick').innerHTML = on ? '<i class="fa fa-times"></i> Cancel' : '<i class="fa fa-map-pin"></i> Pick on map';
        if (on) {
            mapNote(mapsReady ? 'Click on the map to set the incident site.' : 'The map is not loaded; type the latitude and longitude instead.');
            $('pol-map').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function openReport() {
        if (!data) return;
        var p = { id: PERSON, phone: data.filters.phone, from: data.filters.from, to: data.filters.to };
        if (atResult) {
            p.at = atResult.query.at.substr(0, 16);
            p.window = atResult.query.window;
            if (atResult.query.site) { p.lat = atResult.query.site.lat; p.lng = atResult.query.site.lng; }
        }
        window.open(REPORT_URL + '?' + new URLSearchParams(p).toString(), '_blank', 'noopener');
    }

    /* ---------------- Wiring ---------------- */

    window.polMapsReady = function () {
        mapsReady = true;
        map = new google.maps.Map($('pol-map'), { center: { lat: 34.0, lng: 71.5 }, zoom: 7, mapTypeId: 'roadmap', streetViewControl: false });
        map.addListener('click', function (e) {
            if (!picking) return;
            var lat = Math.round(e.latLng.lat() * 1e6) / 1e6, lng = Math.round(e.latLng.lng() * 1e6) / 1e6;
            $('pol-at-lat').value = lat;
            $('pol-at-lng').value = lng;
            if (siteMarker) siteMarker.setMap(null);
            siteMarker = new google.maps.Marker({ map: map, position: { lat: lat, lng: lng }, title: 'Incident site (not checked yet)' });
            setPicking(false);
            mapNote('Incident site set. Enter the date and time, then press Check.');
            $('pol-at-date').focus();
        });
        if (data) drawMap();
        drawAt(false);
    };

    document.addEventListener('DOMContentLoaded', function () {
        $('pol-form').addEventListener('submit', function (e) { e.preventDefault(); load(); });
        $('pol-phone').addEventListener('change', function () { $('pol-from').value = ''; $('pol-to').value = ''; });
        Array.prototype.forEach.call(document.querySelectorAll('.pol-mode button'), function (b) {
            b.addEventListener('click', function () {
                mode = b.getAttribute('data-mode');
                stopPlay();
                Array.prototype.forEach.call(document.querySelectorAll('.pol-mode button'), function (x) {
                    x.className = 'btn btn-xs ' + (x === b ? 'btn-primary' : 'btn-default');
                });
                drawMap();
            });
        });
        $('pol-day').addEventListener('input', function () { stopPlay(); setDay(parseInt(this.value, 10)); });
        $('pol-prev').addEventListener('click', function () { stopPlay(); setDay(dayIndex - 1); });
        $('pol-next').addEventListener('click', function () { stopPlay(); setDay(dayIndex + 1); });
        $('pol-play').addEventListener('click', togglePlay);
        $('pol-report').addEventListener('click', openReport);
        $('pol-at-form').addEventListener('submit', function (e) { e.preventDefault(); checkAt(); });
        $('pol-at-pick').addEventListener('click', function () { setPicking(!picking); });
        $('pol-at-clear').addEventListener('click', function () {
            atResult = null;
            clearAt();
            $('pol-at-result').style.display = 'none';
        });

        var s = document.createElement('script');
        s.src = 'https://maps.googleapis.com/maps/api/js?key=' + MAPS_KEY + '&callback=polMapsReady&loading=async';
        s.async = true;
        s.onerror = function () { mapNote('The map could not be loaded (no internet access to Google Maps). Everything else on this page still works.'); };
        document.head.appendChild(s);

        load();
    });
})();
</script>
