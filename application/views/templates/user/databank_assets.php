<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php
// Shared styles + behaviour for Databank results (.dbk-*), used by
// databank_advanced_form.php (the Search All Sources page) and by the
// Databank section of the search_person "Request Subscriber" popup.
// Render once per page. Results HTML comes from
// Controller_Databank::_all_sources_html(); every handler here is
// delegated on document, so results loaded later by AJAX work as is.
?>
<style>
.dbk{--dbk-ink:#1f2a3c;--dbk-muted:#6b778c;--dbk-line:#e3e8ef;--dbk-soft:#f5f7fb;--dbk-accent:#2563eb;--dbk-accent-soft:#e8efff;
     --dbk-ok:#16a34a;--dbk-ok-soft:#e7f6ec;--dbk-warn:#d97706;--dbk-warn-soft:#fdf3e2;--dbk-bad:#dc2626;--dbk-bad-soft:#fdeaea;
     --dbk-radius:10px;color:var(--dbk-ink);}
.dbk *{box-sizing:border-box;}
.dbk-hero{position:relative;overflow:hidden;border-radius:14px;padding:26px 28px 22px;margin-bottom:18px;color:#fff;
     background:linear-gradient(120deg,#0f2747 0%,#1b4d8c 55%,#2563eb 100%);box-shadow:0 10px 30px rgba(15,39,71,.18);}
.dbk-hero:after{content:"";position:absolute;right:-60px;top:-80px;width:280px;height:280px;border-radius:50%;
     background:radial-gradient(circle,rgba(255,255,255,.14),rgba(255,255,255,0) 70%);pointer-events:none;}
.dbk-eyebrow{font-size:11px;letter-spacing:.14em;text-transform:uppercase;opacity:.75;font-weight:600;}
.dbk-hero h2{margin:4px 0 4px;font-size:24px;font-weight:600;color:#fff;}
.dbk-hero .dbk-sub{margin:0 0 18px;opacity:.85;font-size:13px;max-width:760px;}
.dbk-searchbar{position:relative;display:flex;gap:10px;align-items:stretch;}
.dbk-input-wrap{position:relative;flex:1;}
.dbk-input-wrap .fa{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px;}
.dbk-input{width:100%;height:50px;border:0;border-radius:var(--dbk-radius);padding:0 44px 0 44px;font-size:16px;color:var(--dbk-ink);
     background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.08);outline:none;transition:box-shadow .15s;}
.dbk-input:focus{box-shadow:0 0 0 3px rgba(147,197,253,.75),0 2px 6px rgba(0,0,0,.08);}
.dbk-input-clear{position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:#94a3b8;
     font-size:18px;line-height:1;padding:6px;cursor:pointer;display:none;}
.dbk-input-clear:hover{color:var(--dbk-ink);}
.dbk-btn{height:50px;border:0;border-radius:var(--dbk-radius);padding:0 26px;font-size:15px;font-weight:600;cursor:pointer;
     background:#fbbf24;color:#1f2a3c;box-shadow:0 2px 6px rgba(0,0,0,.12);transition:transform .08s,background .15s;white-space:nowrap;}
.dbk-btn:hover{background:#fcd34d;}
.dbk-btn:active{transform:translateY(1px);}
.dbk-btn[disabled]{opacity:.7;cursor:progress;}
.dbk-controls{display:flex;flex-wrap:wrap;align-items:center;gap:14px 22px;margin-top:14px;}
.dbk-ctl-label{font-size:12px;opacity:.8;margin-right:8px;font-weight:600;}
.dbk-pills{display:inline-flex;flex-wrap:wrap;gap:6px;}
.dbk-pill{position:relative;margin:0;cursor:pointer;font-weight:500;}
.dbk-pill input{position:absolute;opacity:0;pointer-events:none;}
.dbk-pill span{display:inline-block;padding:5px 13px;border-radius:999px;font-size:12.5px;border:1px solid rgba(255,255,255,.35);
     color:#fff;transition:all .12s;}
.dbk-pill:hover span{background:rgba(255,255,255,.12);}
.dbk-pill input:checked + span{background:#fff;color:#1b4d8c;border-color:#fff;font-weight:600;}
.dbk-pill input:focus-visible + span{outline:2px solid #fbbf24;outline-offset:2px;}
.dbk-switch{display:inline-flex;align-items:center;gap:9px;margin:0;cursor:pointer;font-weight:500;font-size:13px;}
.dbk-switch input{position:absolute;opacity:0;pointer-events:none;}
.dbk-switch .dbk-track{position:relative;width:38px;height:22px;border-radius:999px;background:rgba(255,255,255,.3);transition:background .15s;flex:none;}
.dbk-switch .dbk-track:after{content:"";position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background:#fff;
     transition:transform .15s;box-shadow:0 1px 2px rgba(0,0,0,.2);}
.dbk-switch input:checked + .dbk-track{background:#fbbf24;}
.dbk-switch input:checked + .dbk-track:after{transform:translateX(16px);}
.dbk-switch input:focus-visible + .dbk-track{outline:2px solid #fbbf24;outline-offset:2px;}
.dbk-hint{margin:14px 0 0;font-size:12px;opacity:.75;max-width:900px;}
.dbk-hint .fa{margin-right:5px;}
.dbk-shortcut{display:inline-block;padding:0 5px;border:1px solid rgba(255,255,255,.4);border-radius:4px;font-size:11px;font-family:monospace;}

/* results */
.dbk-results-head{display:flex;align-items:center;justify-content:space-between;margin:4px 2px 12px;}
.dbk-results-head h3{margin:0;font-size:17px;font-weight:600;}
.dbk-status{font-size:12px;color:var(--dbk-muted);}
.dbk-summary{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:14px;}
.dbk-stat{background:#fff;border:1px solid var(--dbk-line);border-radius:var(--dbk-radius);padding:12px 14px;}
.dbk-stat b{display:block;font-size:22px;font-weight:700;line-height:1.1;}
.dbk-stat small{color:var(--dbk-muted);font-size:12px;}
.dbk-chips{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;}
.dbk-chip{display:inline-flex;align-items:center;gap:7px;padding:5px 11px;border-radius:999px;background:#fff;border:1px solid var(--dbk-line);
     font-size:12.5px;color:var(--dbk-ink);text-decoration:none;transition:border-color .12s,box-shadow .12s;}
.dbk-chip:hover,.dbk-chip:focus{border-color:var(--dbk-accent);color:var(--dbk-ink);text-decoration:none;box-shadow:0 1px 4px rgba(37,99,235,.15);}
.dbk-chip em{font-style:normal;font-weight:700;padding:0 7px;border-radius:999px;background:var(--dbk-soft);font-size:11.5px;}
.dbk-dot{width:8px;height:8px;border-radius:50%;background:#cbd5e1;flex:none;}
.dbk-s-ok .dbk-dot,.dbk-dot.dbk-s-ok{background:var(--dbk-ok);}
.dbk-s-unavailable .dbk-dot{background:var(--dbk-bad);}
.dbk-s-unsupported .dbk-dot{background:var(--dbk-warn);}
.dbk-chip.dbk-s-ok em{background:var(--dbk-ok-soft);color:var(--dbk-ok);}

.dbk-card{background:#fff;border:1px solid var(--dbk-line);border-radius:12px;margin-bottom:14px;box-shadow:0 1px 3px rgba(16,24,40,.05);
     border-left:4px solid #cbd5e1;scroll-margin-top:70px;}
.dbk-card.dbk-s-ok{border-left-color:var(--dbk-ok);}
.dbk-card.dbk-s-unavailable{border-left-color:var(--dbk-bad);}
.dbk-card.dbk-s-unsupported{border-left-color:var(--dbk-warn);}
.dbk-card-head{display:flex;align-items:center;gap:12px;padding:12px 16px;}
.dbk-card.dbk-has-rows .dbk-card-head{cursor:pointer;user-select:none;}
.dbk-ico{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex:none;font-size:16px;
     background:var(--dbk-soft);color:var(--dbk-muted);}
.dbk-s-ok .dbk-ico{background:var(--dbk-accent-soft);color:var(--dbk-accent);}
.dbk-card-title{flex:1;min-width:0;}
.dbk-card-title h4{margin:0;font-size:15px;font-weight:600;}
.dbk-card-title p{margin:2px 0 0;font-size:12px;color:var(--dbk-muted);}
.dbk-badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:11.5px;font-weight:600;background:var(--dbk-soft);color:var(--dbk-muted);white-space:nowrap;}
.dbk-s-ok .dbk-badge{background:var(--dbk-ok-soft);color:var(--dbk-ok);}
.dbk-s-unavailable .dbk-badge{background:var(--dbk-bad-soft);color:var(--dbk-bad);}
.dbk-s-unsupported .dbk-badge{background:var(--dbk-warn-soft);color:var(--dbk-warn);}
.dbk-caret{color:var(--dbk-muted);transition:transform .15s;}
.dbk-card.dbk-collapsed .dbk-caret{transform:rotate(-90deg);}
.dbk-card.dbk-collapsed .dbk-card-body{display:none;}
.dbk-card-body{border-top:1px solid var(--dbk-line);padding:12px 16px 14px;}
.dbk-toolbar{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;flex-wrap:wrap;}
.dbk-toolbar .dbk-count{font-size:12px;color:var(--dbk-muted);}
.dbk-filter{position:relative;}
.dbk-filter .fa{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:12px;}
.dbk-filter input{height:30px;border:1px solid var(--dbk-line);border-radius:8px;padding:0 10px 0 28px;font-size:12.5px;width:220px;outline:none;}
.dbk-filter input:focus{border-color:var(--dbk-accent);box-shadow:0 0 0 3px var(--dbk-accent-soft);}
.dbk-table-wrap{overflow:auto;max-height:560px;border:1px solid var(--dbk-line);border-radius:8px;}
.dbk-table{width:100%;border-collapse:separate;border-spacing:0;font-size:13px;margin:0;}
.dbk-table th{position:sticky;top:0;z-index:1;background:#f8fafc;color:#475569;font-weight:600;font-size:11.5px;text-transform:uppercase;
     letter-spacing:.04em;text-align:left;padding:9px 12px;border-bottom:1px solid var(--dbk-line);white-space:nowrap;}
.dbk-table td{padding:9px 12px;border-bottom:1px solid #eef2f6;vertical-align:top;}
.dbk-table tbody tr:last-child td{border-bottom:0;}
.dbk-table tbody tr:nth-child(even) td{background:#fbfcfe;}
.dbk-table tbody tr:hover td{background:#f1f6ff;}
.dbk-table td.dbk-num{font-family:Consolas,Menlo,monospace;font-size:12.5px;white-space:nowrap;}
.dbk-table .dbk-empty-cell{color:#cbd5e1;}
.dbk-thumb{max-width:200px;max-height:64px;border-radius:6px;border:1px solid var(--dbk-line);cursor:zoom-in;background:#fff;}
.dbk-nomatch{display:none;padding:14px;text-align:center;color:var(--dbk-muted);font-size:12.5px;}
.dbk-linked{background:var(--dbk-accent-soft);border:1px solid #c7d7fe;border-radius:var(--dbk-radius);padding:10px 14px;margin-bottom:14px;font-size:12.5px;}
.dbk-linked b{display:block;margin-bottom:4px;color:#1e40af;}
.dbk-linked ul{margin:0;padding-left:18px;}
.dbk-alert{border-radius:var(--dbk-radius);padding:12px 16px;font-size:13px;display:flex;gap:10px;align-items:flex-start;border:1px solid;}
.dbk-alert .fa{margin-top:2px;}
.dbk-alert-danger{background:var(--dbk-bad-soft);border-color:#f8c9c9;color:#991b1b;}
.dbk-alert-warning{background:var(--dbk-warn-soft);border-color:#f7dcae;color:#92400e;}
.dbk-alert-info{background:var(--dbk-soft);border-color:var(--dbk-line);color:var(--dbk-muted);}
.dbk-skel{background:#fff;border:1px solid var(--dbk-line);border-radius:12px;padding:16px;margin-bottom:12px;}
.dbk-skel i{display:block;height:12px;border-radius:6px;margin:8px 0;
     background:linear-gradient(90deg,#eef2f6 25%,#f8fafc 50%,#eef2f6 75%);background-size:200% 100%;animation:dbkShimmer 1.2s infinite;}
@keyframes dbkShimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
.dbk-lightbox{position:fixed;inset:0;background:rgba(15,23,42,.8);display:none;align-items:center;justify-content:center;z-index:2000;cursor:zoom-out;padding:20px;}
.dbk-lightbox img{max-width:92vw;max-height:88vh;border-radius:8px;background:#fff;box-shadow:0 20px 50px rgba(0,0,0,.4);}
/* profile layout (person dashboard, ?view=profile) */
span.dbk-chip{cursor:default;}
span.dbk-chip:hover{border-color:var(--dbk-line);box-shadow:none;}
.dbk-chip.dbk-s-unavailable em{background:var(--dbk-bad-soft);color:var(--dbk-bad);}
.dbk-chip.dbk-s-unsupported em{background:var(--dbk-warn-soft);color:var(--dbk-warn);}
.dbk-prof-card{background:#fff;border:1px solid var(--dbk-line);border-top:3px solid var(--dbk-accent);border-radius:10px;margin-bottom:14px;scroll-margin-top:70px;}
.dbk-prof-head{display:flex;align-items:center;gap:10px;padding:10px 14px;border-bottom:1px solid var(--dbk-line);}
.dbk-prof-head .dbk-ico{background:var(--dbk-accent-soft);color:var(--dbk-accent);}
.dbk-prof-head h4{flex:1;margin:0;font-size:15px;font-weight:600;}
.dbk-prof-head .dbk-badge{background:var(--dbk-ok-soft);color:var(--dbk-ok);}
.dbk-prof-rec{padding:12px 14px;}
.dbk-prof-rec + .dbk-prof-rec{border-top:1px dashed var(--dbk-line);}
.dbk-prof-body{display:flex;gap:16px;align-items:flex-start;}
.dbk-prof-photos{display:flex;flex-direction:column;gap:8px;flex:none;max-width:220px;}
.dbk-prof-photos figure{margin:0;}
.dbk-prof-photos img{max-width:220px;max-height:90px;}
.dbk-prof-photos figcaption{font-size:10.5px;color:var(--dbk-muted);margin-top:2px;}
.dbk-prof-grid{flex:1;display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px 18px;min-width:0;}
.dbk-prof-f span{display:block;font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--dbk-muted);margin-bottom:1px;}
.dbk-prof-f b{display:block;font-size:13.5px;font-weight:500;color:var(--dbk-ink);word-break:break-word;}
.dbk-prof-bar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:12px;}
.dbk-prof-bar .dbk-status{flex:1;}
@media (max-width:767px){
  .dbk-prof-body{flex-direction:column;}
  .dbk-prof-photos{flex-direction:row;flex-wrap:wrap;max-width:none;}
}

/* simple layout (Request Subscriber popup, ?view=simple) */
.dbk-simple-source{margin-bottom:16px;}
.dbk-simple-head{display:flex;align-items:center;gap:8px;font-size:15px;font-weight:600;padding:8px 0;border-bottom:2px solid #00a7d0;margin-bottom:10px;color:#0f2747;}
.dbk-simple-head .fa{color:#00a7d0;width:18px;text-align:center;}
.dbk-simple-head span{margin-left:auto;font-size:12px;font-weight:600;color:#fff;background:#00a7d0;border-radius:999px;padding:2px 10px;}
.dbk-simple-record{border:1px solid var(--dbk-line);border-radius:8px;padding:10px 14px;margin-bottom:10px;background:#fff;}
.dbk-simple-no{font-size:11px;font-weight:700;color:#00a7d0;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;}
.dbk-simple-record dl{display:grid;grid-template-columns:minmax(120px,200px) 1fr;gap:5px 14px;margin:0;font-size:13.5px;}
.dbk-simple-record dt{font-weight:600;color:#475569;}
.dbk-simple-record dd{margin:0;color:var(--dbk-ink);word-break:break-word;}
.dbk-simple-save{margin:10px 0 0;padding-top:10px;border-top:1px dashed var(--dbk-line);}
.dbk-simple-muted{font-size:12px;color:var(--dbk-muted);font-weight:400;}
.dbk-simple-foot{font-size:13px;color:var(--dbk-muted);margin:6px 0;}
.dbk-simple-bad{color:var(--dbk-bad);}
@media (max-width:767px){
  .dbk-simple-record dl{grid-template-columns:1fr;gap:0 0;}
  .dbk-simple-record dd{margin-bottom:6px;}
}
@media (max-width:767px){
  .dbk-hero{padding:20px 16px 18px;}
  .dbk-searchbar{flex-direction:column;}
  .dbk-btn{width:100%;}
  .dbk-filter input{width:100%;}
  .dbk-filter{flex:1;}
}
@media (prefers-reduced-motion:reduce){.dbk *{transition:none!important;animation:none!important;}}
</style>

<div class="dbk-lightbox" id="dbk_lightbox" onclick="this.style.display='none';" role="dialog" aria-label="Image preview">
    <img src="" alt="">
</div>

<script>
(function () {
    if (window.dbkResultsBound) return;
    window.dbkResultsBound = true;

    function closest(el, sel) {
        while (el && el.nodeType === 1) {
            if (el.matches ? el.matches(sel) : el.msMatchesSelector(sel)) return el;
            el = el.parentNode;
        }
        return null;
    }

    // Quick filter inside one source card.
    document.addEventListener('input', function (e) {
        if (!e.target.classList || !e.target.classList.contains('dbk-filter-input')) return;
        var card  = closest(e.target, '.dbk-card');
        var q     = e.target.value.toLowerCase().replace(/^\s+|\s+$/g, '');
        var rows  = card.querySelectorAll('tbody tr');
        var shown = 0;
        for (var i = 0; i < rows.length; i++) {
            var hit = q === '' || rows[i].textContent.toLowerCase().indexOf(q) !== -1;
            rows[i].style.display = hit ? '' : 'none';
            if (hit) shown++;
        }
        var count = card.querySelector('.dbk-count');
        if (count) count.textContent = q === '' ? count.getAttribute('data-total')
                                                : shown + ' of ' + count.getAttribute('data-total');
        card.querySelector('.dbk-nomatch').style.display = shown ? 'none' : 'block';
    });

    document.addEventListener('click', function (e) {
        var img = closest(e.target, '.dbk-thumb');
        if (img) {
            var lb = document.getElementById('dbk_lightbox');
            lb.querySelector('img').src = img.src;
            lb.style.display = 'flex';
            return;
        }
        var chip = closest(e.target, 'a.dbk-chip');
        if (chip) {
            var target = document.getElementById(chip.getAttribute('href').slice(1));
            if (target) {
                e.preventDefault();
                target.classList.remove('dbk-collapsed');
                target.scrollIntoView({behavior: 'smooth', block: 'start'});
            }
            return;
        }
        var head = closest(e.target, '.dbk-has-rows .dbk-card-head');
        if (head) {
            var card = head.parentNode;
            card.classList.toggle('dbk-collapsed');
            head.setAttribute('aria-expanded', card.classList.contains('dbk-collapsed') ? 'false' : 'true');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') document.getElementById('dbk_lightbox').style.display = 'none';
        var head = closest(e.target, '.dbk-has-rows .dbk-card-head');
        if (head && (e.key === 'Enter' || e.key === ' ')) {
            e.preventDefault();
            head.click();
        }
    });
})();
</script>
