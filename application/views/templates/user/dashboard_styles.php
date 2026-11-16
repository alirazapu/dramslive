<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php
// Styles for the user dashboard (templates/user/dashboard.php). Scoped to
// .ud / .ud-header so nothing else in the portal changes. Design only.
?>
<style>
.ud, .ud-header { --ud-ink:#1f2a3c; --ud-muted:#6b778c; --ud-line:#e3e8ef; --ud-soft:#f5f7fb; --ud-accent:#2563eb; }

/* page header */
.ud-header { display:flex; align-items:flex-end; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:18px 15px 0; }
.ud-header h1 { margin:0; font-size:24px; font-weight:600; color:var(--ud-ink); }
.ud-header h1 .fa { color:var(--ud-accent); margin-right:4px; }
.ud-greet { margin:4px 0 0; color:var(--ud-muted); font-size:13.5px; }
.ud-greet b { color:var(--ud-ink); font-weight:600; }
.content-header.ud-header > .breadcrumb { position:static; float:none; background:transparent; margin:0; padding:0 0 4px; }

/* stat cards */
.ud-stats { display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:16px; margin:6px 0 18px; }
.ud-stat { display:flex; flex-direction:column; gap:10px; background:#fff; border:1px solid var(--ud-line); border-radius:14px;
    padding:16px 18px 14px; color:var(--ud-ink); text-decoration:none !important; box-shadow:0 1px 2px rgba(16,24,40,.05);
    transition:box-shadow .15s, transform .15s, border-color .15s; position:relative; overflow:hidden; }
.ud-stat:hover, .ud-stat:focus { color:var(--ud-ink); box-shadow:0 8px 22px rgba(16,24,40,.10); transform:translateY(-2px); border-color:#cfd8e3; }
.ud-stat:before { content:""; position:absolute; left:0; top:0; bottom:0; width:4px; background:var(--ud-c); }
.ud-stat-top { display:flex; align-items:center; gap:10px; }
.ud-stat-ico { width:38px; height:38px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center;
    font-size:17px; background:var(--ud-c-soft); color:var(--ud-c); flex:none; }
.ud-stat-label { font-size:13px; font-weight:600; color:var(--ud-muted); }
.ud-stat-value { font-size:32px; font-weight:700; line-height:1; letter-spacing:-.01em; }
.ud-stat-bar { height:6px; border-radius:999px; background:var(--ud-soft); overflow:hidden; display:flex; }
.ud-stat-bar i { display:block; height:100%; background:var(--ud-c); border-radius:999px; }
.ud-stat-bar-split i { border-radius:0; }
.ud-stat-bar-split .ud-b { background:#1f2937; } .ud-stat-bar-split .ud-g { background:#94a3b8; } .ud-stat-bar-split .ud-w { background:#cbd5e1; }
.ud-stat-foot { display:flex; justify-content:space-between; font-size:12px; color:var(--ud-muted); }
.ud-stat-foot span:last-child { color:var(--ud-c); font-weight:600; }
.ud-stat:hover .ud-stat-foot .fa { transform:translateX(2px); }
.ud-stat-foot .fa { transition:transform .15s; }
.ud-black { --ud-c:#1f2937; --ud-c-soft:#e5e7eb; }
.ud-gray  { --ud-c:#64748b; --ud-c-soft:#eef2f6; }
.ud-white { --ud-c:#0f766e; --ud-c-soft:#e6f4f1; }
.ud-total { --ud-c:#2563eb; --ud-c-soft:#e8efff; }

/* boxes as clean cards */
.ud .box { border:1px solid var(--ud-line) !important; border-radius:14px; box-shadow:0 1px 2px rgba(16,24,40,.05);
    background:#fff; margin-bottom:18px; max-height:none !important; }
.ud .box > .box-header { padding:14px 18px; border-bottom:1px solid var(--ud-line); border-radius:14px 14px 0 0; background:#fff !important; color:var(--ud-ink) !important; }
.ud .box.collapsed-box > .box-header { border-bottom:0; }
.ud .box > .box-header .box-title { font-size:15px; font-weight:600; color:var(--ud-ink); }
.ud .box > .box-header .btn-box-tool { color:#64748b; }
.ud .box > .box-header .btn-box-tool:hover { color:var(--ud-ink); }
.ud .box-body { padding:16px 18px; }

/* chart legend */
.ud-legend { display:flex; gap:16px; flex-wrap:wrap; font-size:12.5px; color:var(--ud-muted); margin:-2px 0 10px; }
.ud-legend span { display:inline-flex; align-items:center; gap:6px; }
.ud-legend i { width:12px; height:12px; border-radius:3px; display:inline-block; }

/* request summary: filters + table */
.ud #fromDate, .ud #toDate { border-radius:8px; border-color:#d5dde8; box-shadow:none; height:36px; }
.ud #fromDate:focus, .ud #toDate:focus { border-color:var(--ud-accent); box-shadow:0 0 0 3px #e8efff; }
.ud .box-body label { font-size:12px; font-weight:600; color:var(--ud-muted); }
.ud .box-body .btn { border-radius:8px; height:36px; padding:0 16px; font-weight:500; }
.ud .box-body .btn-primary { background:var(--ud-accent); border-color:var(--ud-accent); }
.ud .box-body .btn-primary:hover { background:#1d4ed8; border-color:#1d4ed8; }
.ud #requestTypeTable { border:1px solid var(--ud-line); border-radius:10px; border-collapse:separate; border-spacing:0; overflow:hidden; }
.ud #requestTypeTable > thead > tr > th { background:#f8fafc; color:#475569; font-size:11.5px; font-weight:600; text-transform:uppercase;
    letter-spacing:.04em; border:0; border-bottom:1px solid var(--ud-line); padding:10px 14px; }
.ud #requestTypeTable > tbody > tr > td, .ud #requestTypeTable > tfoot > tr > td { border:0; border-bottom:1px solid #eef2f6; padding:9px 14px; vertical-align:middle; }
.ud #requestTypeTable > tbody > tr > td { background:#fff !important; }
.ud #requestTypeTable > tbody > tr:nth-of-type(even) > td { background:#fbfcfe !important; }
.ud #requestTypeTable > tbody > tr:hover > td { background:#f1f6ff !important; }
.ud #requestTypeTable > tfoot > tr { background:#f8fafc !important; }
.ud #requestTypeTable > tfoot > tr > td { border-bottom:0; }
.ud .badge.ud-count { background:#e7f6ec; color:#15803d; font-weight:700; font-size:12px; padding:4px 10px; border-radius:999px; }
.ud #totalRequests { background:var(--ud-accent) !important; font-size:12.5px; padding:4px 11px; border-radius:999px; }

@media (max-width:1199px) { .ud-stats { grid-template-columns:repeat(2, minmax(0, 1fr)); } }
@media (max-width:520px)  { .ud-stats { grid-template-columns:1fr; } .ud-stat-value { font-size:28px; } }
@media (prefers-reduced-motion:reduce) { .ud-stat, .ud-stat-foot .fa { transition:none; } .ud-stat:hover { transform:none; } }
</style>
