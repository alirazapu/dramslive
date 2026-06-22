/* DRAMS Live — System Documentation slide deck
   Embeds the 20 rendered Mermaid flow diagrams + supporting text. */
const pptxgen = require("pptxgenjs");
const fs = require("fs");
const path = require("path");

const DIR = __dirname;
const D = (n) => path.join(DIR, "diagrams", `render-${n}.png`);

// ---- aspect ratios (width/height) of each rendered diagram ----
const RATIO = {
  1:1.941, 2:4.667, 3:1.186, 4:1.970, 5:6.272, 6:8.711, 7:0.505, 8:0.498,
  9:1.980, 10:1.012, 11:1.246, 12:0.568, 13:0.682, 14:0.456, 15:1.244,
  16:0.822, 17:2.113, 18:3.980, 19:6.078, 20:10.316
};

// ---- palette ----
const NAVY = "13233B";     // dark background
const NAVY2 = "1E3A5F";
const ACCENT = "2F9BD4";   // cyan-blue
const GOLD = "E0A458";     // emphasis accent
const TEXT = "1B2A3A";
const MUTED = "6A7B8C";
const CARD = "EEF3F8";
const ICE = "CFE3F2";
const WHITE = "FFFFFF";
const LINE = "D5DEE7";

const HF = "Trebuchet MS"; // header font
const BF = "Calibri";      // body font

const pres = new pptxgen();
pres.defineLayout({ name: "WIDE", width: 13.333, height: 7.5 });
pres.layout = "WIDE";
pres.author = "DRAMS Live";
pres.title = "DRAMS Live — System Documentation";
const PW = 13.333, PH = 7.5;

const shadow = () => ({ type: "outer", color: "000000", blur: 7, offset: 3, angle: 135, opacity: 0.16 });

// fit image inside box (preserve ratio), return centered placement
function fit(ratio, boxX, boxY, boxW, boxH) {
  let w = boxW, h = w / ratio;
  if (h > boxH) { h = boxH; w = h * ratio; }
  return { x: boxX + (boxW - w) / 2, y: boxY + (boxH - h) / 2, w, h };
}

function titleBar(slide, kicker, title, dark) {
  slide.addText(kicker.toUpperCase(), {
    x: 0.55, y: 0.40, w: 11.5, h: 0.32, fontFace: HF, fontSize: 12,
    color: ACCENT, bold: true, charSpacing: 2, margin: 0
  });
  slide.addText(title, {
    x: 0.55, y: 0.70, w: 12.2, h: 0.75, fontFace: HF, fontSize: 27,
    color: dark ? WHITE : NAVY, bold: true, margin: 0
  });
}

function caption(slide, txt, x, y, w) {
  slide.addText(txt, { x, y, w, h: 0.3, fontFace: BF, fontSize: 10,
    italic: true, color: MUTED, align: "center", margin: 0 });
}

function bullets(slide, items, x, y, w, h, fs) {
  slide.addText(items.map((t, i) => ({
    text: t, options: { bullet: { code: "2022", indent: 14 }, breakLine: true,
      fontSize: fs || 13, color: TEXT, paraSpaceAfter: 7 }
  })), { x, y, w, h, fontFace: BF, valign: "top", margin: 0 });
}

function pageNum(slide, n) {
  slide.addText(String(n), { x: PW - 0.7, y: PH - 0.45, w: 0.4, h: 0.3,
    fontFace: BF, fontSize: 9, color: MUTED, align: "right", margin: 0 });
  slide.addText("DRAMS Live · System Documentation", { x: 0.55, y: PH - 0.45,
    w: 6, h: 0.3, fontFace: BF, fontSize: 9, color: MUTED, margin: 0 });
}

let PAGE = 0;
// content slide with image on LEFT, text on RIGHT
function slideImgText(kicker, title, fig, rightTitle, items, opts) {
  opts = opts || {};
  const slide = pres.addSlide();
  slide.background = { color: WHITE };
  titleBar(slide, kicker, title);
  // left image zone
  const boxX = 0.55, boxY = 1.65, boxW = opts.imgW || 6.0, boxH = 5.05;
  const p = fit(RATIO[fig], boxX, boxY, boxW, boxH);
  slide.addImage({ path: D(fig), x: p.x, y: p.y, w: p.w, h: p.h });
  caption(slide, opts.cap || `Figure ${fig}`, boxX, 6.78, boxW);
  // right text zone
  const rx = (opts.imgW || 6.0) + 0.95;
  const rw = PW - rx - 0.55;
  if (rightTitle) {
    slide.addText(rightTitle, { x: rx, y: 1.7, w: rw, h: 0.4, fontFace: HF,
      fontSize: 16, bold: true, color: opts.rtColor || ACCENT, margin: 0 });
  }
  bullets(slide, items, rx, rightTitle ? 2.2 : 1.7, rw, 4.8, opts.fs || 13);
  PAGE++; pageNum(slide, PAGE);
  return slide;
}

// content slide with FULL-WIDTH image (wide diagrams) + bullets below
function slideImgWide(kicker, title, fig, items, opts) {
  opts = opts || {};
  const slide = pres.addSlide();
  slide.background = { color: WHITE };
  titleBar(slide, kicker, title);
  const boxX = 0.55, boxY = 1.6, boxW = 12.23, boxH = opts.imgH || 3.2;
  const p = fit(RATIO[fig], boxX, boxY, boxW, boxH);
  slide.addImage({ path: D(fig), x: p.x, y: p.y, w: p.w, h: p.h });
  caption(slide, opts.cap || `Figure ${fig}`, boxX, boxY + p.h + 0.05, boxW);
  if (items && items.length) {
    const by = boxY + p.h + 0.45;
    bullets(slide, items, 0.7, by, 11.9, PH - by - 0.6, opts.fs || 13);
  }
  PAGE++; pageNum(slide, PAGE);
  return slide;
}

/* =================================================================== */
/* 1. TITLE                                                             */
/* =================================================================== */
(() => {
  const s = pres.addSlide();
  s.background = { color: NAVY };
  // subtle motif: thin accent ticks
  s.addShape(pres.shapes.RECTANGLE, { x: 0.9, y: 2.05, w: 1.5, h: 0.09, fill: { color: ACCENT } });
  s.addText("DRAMS LIVE", { x: 0.9, y: 2.25, w: 11.5, h: 1.2, fontFace: HF,
    fontSize: 60, bold: true, color: WHITE, charSpacing: 1, margin: 0 });
  s.addText("Complete System Documentation", { x: 0.92, y: 3.55, w: 11.5, h: 0.7,
    fontFace: HF, fontSize: 26, color: ICE, margin: 0 });
  s.addText("Architecture · Data Model · End-to-End Process Flows", {
    x: 0.92, y: 4.25, w: 11.5, h: 0.5, fontFace: BF, fontSize: 15, italic: true,
    color: ACCENT, margin: 0 });
  s.addText([
    { text: "Platform: ", options: { bold: true } },
    { text: "CTD telecom-data & person-intelligence analysis system     ", options: {} },
    { text: "Framework: ", options: { bold: true } },
    { text: "Kohana 3.x PHP (HMVC)", options: {} }
  ], { x: 0.92, y: 6.4, w: 11.6, h: 0.4, fontFace: BF, fontSize: 12, color: ICE, margin: 0 });
})();

/* =================================================================== */
/* 2. AGENDA                                                            */
/* =================================================================== */
(() => {
  const s = pres.addSlide();
  s.background = { color: WHITE };
  titleBar(s, "Overview", "What this deck covers");
  const cols = [
    ["01  Executive overview", "02  Technology stack", "03  System architecture",
     "04  Request lifecycle (HMVC)", "05  Codebase & controllers", "06  Data model (ER)"],
    ["07  Federated databases", "08  Authentication & RBAC", "09  Login & person flows",
     "10  Telecom request lifecycle", "11  CDR ingestion & identity sync", "12  Watchlist & reporting"],
    ["13  Cron / background engine", "14  External integrations", "15  Sitemap & navigation",
     "16  Roles & journeys", "17  Table inventory", "18  Glossary"]
  ];
  const colW = 3.85, gap = 0.18, startX = 0.6;
  cols.forEach((col, ci) => {
    const x = startX + ci * (colW + gap);
    col.forEach((t, ri) => {
      const y = 1.85 + ri * 0.82;
      s.addShape(pres.shapes.RECTANGLE, { x, y, w: 0.06, h: 0.62, fill: { color: ci === 0 ? ACCENT : ci === 1 ? NAVY2 : GOLD } });
      s.addText(t, { x: x + 0.2, y, w: colW - 0.25, h: 0.62, fontFace: BF, fontSize: 13,
        color: TEXT, valign: "middle", margin: 0 });
    });
  });
  PAGE++; pageNum(s, PAGE);
})();

/* =================================================================== */
/* 3. EXECUTIVE OVERVIEW — 5 capabilities                              */
/* =================================================================== */
(() => {
  const s = pres.addSlide();
  s.background = { color: WHITE };
  titleBar(s, "01 · Executive overview", "What DRAMS Live does");
  const caps = [
    ["1", "Person profiling", "A 360° record per person: identity (CNIC/NADRA, foreigner, passport), addresses, education, banks, criminal record, affiliations, training, relations, devices & SIMs."],
    ["2", "Telecom data acquisition", "Submits formal CDR / subscriber / location requests to mobile operators by email, then auto-parses the email replies back into the database."],
    ["3", "CDR analysis", "Call/SMS summaries, B-party analysis, location & cell-tower tracking, device/IMEI/SIM history, and CDR network-graph visualisation."],
    ["4", "External data federation", "Searches subscriber, ECP, KPK-CTD, DLMS & govt-employee databases plus NADRA / Verisys / Family-Tree / Travel and an AIES/CCTW REST API."],
    ["5", "Watchlists, reporting & audit", "Tag and watch persons, build user/person/admin analytics, and keep a full audit trail of every analyst action."]
  ];
  let y = 1.75;
  caps.forEach((c) => {
    s.addShape(pres.shapes.RECTANGLE, { x: 0.6, y, w: 12.15, h: 0.98, fill: { color: CARD }, line: { color: LINE, width: 1 } });
    s.addShape(pres.shapes.OVAL, { x: 0.85, y: y + 0.24, w: 0.5, h: 0.5, fill: { color: ACCENT } });
    s.addText(c[0], { x: 0.85, y: y + 0.24, w: 0.5, h: 0.5, fontFace: HF, fontSize: 20,
      bold: true, color: WHITE, align: "center", valign: "middle", margin: 0 });
    s.addText(c[1], { x: 1.55, y: y + 0.12, w: 3.3, h: 0.74, fontFace: HF, fontSize: 15,
      bold: true, color: NAVY, valign: "middle", margin: 0 });
    s.addText(c[2], { x: 4.95, y: y + 0.08, w: 7.65, h: 0.82, fontFace: BF, fontSize: 11.5,
      color: TEXT, valign: "middle", margin: 0 });
    y += 1.06;
  });
  PAGE++; pageNum(s, PAGE);
})();

/* =================================================================== */
/* 4. TECH STACK (fig 1)                                               */
/* =================================================================== */
slideImgText("02 · Technology stack", "A layered Kohana / PHP stack", 1,
  "Key components", [
   "Kohana 3.x HMVC framework on Apache + PHP, ORM + Query Builder over MySQL.",
   "Framework modules: auth, database, orm, image, mysqli, phpexcel, phpmailer.",
   "Composer libraries: php-imap (receive mail), phpspreadsheet (Excel), Google API Client.",
   "Front end: Bootstrap 3, jQuery, DataTables, Chart.js, Select2, Cytoscape graphs.",
   "Primary DB aiesplus; five federated read-only data sources behind the Databank."
  ], { imgW: 6.7, fs: 13 });

/* =================================================================== */
/* 5. ARCHITECTURE (fig 2, wide)                                       */
/* =================================================================== */
slideImgWide("03 · System architecture", "Layered HMVC architecture", 2, [
  "Public controllers (Login, Cronjob, Aiesapi, Gmailapi, Verisyssync) intentionally bypass auth.",
  "Every authenticated controller extends Controller_Working — a single before() hook enforces auth, RBAC, IP-block and audit.",
  "Domain logic lives in Helpers_* services and Model_* (ORM + query-builder) classes over the primary & federated databases."
], { imgH: 3.0 });

/* =================================================================== */
/* 6. CONTROLLER INHERITANCE (fig 3)                                   */
/* =================================================================== */
slideImgText("03 · Controllers", "Controller inheritance & the security gate", 3,
  "How requests are gated", [
   "Controller_Working::before() runs on every authenticated page.",
   "It authenticates the user, resolves role_id, checks the IP block-list and inactive-user status, then writes an audit log entry.",
   "Raw public controllers (Login, Cron, REST API, Gmail OAuth) extend the bare Kohana controller and skip the gate by design.",
   "≈30 controllers, several with 60–70 actions, organised by domain."
  ], { imgW: 5.7, fs: 13 });

/* =================================================================== */
/* 7. REQUEST LIFECYCLE (fig 4)                                        */
/* =================================================================== */
slideImgText("04 · Request lifecycle", "How a browser request is served", 4,
  "Two response styles", [
   "index.php → Bootstrap → Router maps /controller/action/id (default controller = login).",
   "before() authorises, then the action runs business logic via Helpers/Models.",
   "Full pages render through the template wrapper (header, sidebar, footer) with .inc view bodies.",
   "AJAX endpoints set auto_render=FALSE and echo JSON — DataTables tables, Chart.js charts, Cytoscape CDR graphs."
  ], { imgW: 6.6, fs: 13 });

/* =================================================================== */
/* 8. CODEBASE STRUCTURE (fig 5, wide)                                 */
/* =================================================================== */
slideImgWide("05 · Codebase", "Module & directory structure", 5, [
  "application/classes holds Controller, Model, Helpers and the Auth ORM driver; views split into entry / templates / error.",
  "Include-file convention: each large action's view logic lives in a matching .inc (user_functions, persons_functions, watchlist_functions, excel, cron_job/<task>)."
], { imgH: 2.3 });

/* =================================================================== */
/* 9. DATA MODEL (fig 6, very wide)                                    */
/* =================================================================== */
(() => {
  const s = pres.addSlide();
  s.background = { color: WHITE };
  titleBar(s, "06 · Data model", "Entity-relationship model — centred on the person");
  const boxY = 1.6, boxH = 1.7;
  const p = fit(RATIO[6], 0.55, boxY, 12.23, boxH);
  s.addImage({ path: D(6), x: p.x, y: p.y, w: p.w, h: p.h });
  caption(s, "Figure 6 — person is the hub; identity, activity, attribute, request & access tables surround it", 0.55, boxY + p.h + 0.05, 12.23);
  // entity columns
  const groups = [
    ["Core & identity", "person_initiate · person · person_nadra_profile · person_foreigner_profile · person_detail_info"],
    ["Activity (CDR)", "person_call_log · person_sms_log · person_summary · person_location_history · person_phone_number · person_phone_device"],
    ["Attributes", "education · banks · criminal_record · affiliations · trainings · identities · relations · social_links · tags"],
    ["Requests & access", "user_request · admin_request (+nadra/familytree/travel) · email_messages · users · roles · roles_users · manu_management"]
  ];
  const colW = 2.92, gap = 0.13, sx = 0.6, gy = 4.0;
  groups.forEach((g, i) => {
    const x = sx + i * (colW + gap);
    s.addShape(pres.shapes.RECTANGLE, { x, y: gy, w: colW, h: 2.5, fill: { color: CARD }, line: { color: LINE, width: 1 } });
    s.addShape(pres.shapes.RECTANGLE, { x, y: gy, w: colW, h: 0.5, fill: { color: NAVY2 } });
    s.addText(g[0], { x: x + 0.12, y: gy, w: colW - 0.2, h: 0.5, fontFace: HF, fontSize: 13,
      bold: true, color: WHITE, valign: "middle", margin: 0 });
    s.addText(g[1], { x: x + 0.14, y: gy + 0.62, w: colW - 0.28, h: 1.8, fontFace: BF,
      fontSize: 10.5, color: TEXT, valign: "top", margin: 0 });
  });
  PAGE++; pageNum(s, PAGE);
})();

/* =================================================================== */
/* 10. FEDERATED DBs (fig 7, tall) + table                             */
/* =================================================================== */
(() => {
  const s = pres.addSlide();
  s.background = { color: WHITE };
  titleBar(s, "07 · Data sources", "Six federated database connections");
  const p = fit(RATIO[7], 0.55, 1.7, 4.0, 5.0);
  s.addImage({ path: D(7), x: p.x, y: p.y, w: p.w, h: p.h });
  caption(s, "Figure 7 — database.php connections", 0.55, 6.78, 4.0);
  const rows = [
    [{ text: "Connection", options: { bold: true, color: WHITE, fill: { color: NAVY } } },
     { text: "Database", options: { bold: true, color: WHITE, fill: { color: NAVY } } },
     { text: "Use", options: { bold: true, color: WHITE, fill: { color: NAVY } } }],
    ["default", "aiesplus / aiesdev (MySQL)", "All DRAMS person, CDR, request & user data"],
    ["mobile", "subscriber_db (MySQL)", "Operator subscriber lookups"],
    ["ecp", "ecp (MySQL)", "Electoral / address & family search"],
    ["ctd_kpk", "ctd_kpk (MySQL)", "KPK CTD person profiles"],
    ["dlms_sqlsrv", "DLMS (MS SQL Server)", "Driving licenses"],
    ["govt_emp_data", "govt_emp_data (MySQL)", "Government employees"]
  ];
  s.addTable(rows, { x: 5.0, y: 1.85, w: 7.75, colW: [1.7, 2.85, 3.2],
    border: { pt: 0.5, color: LINE }, fontFace: BF, fontSize: 12, color: TEXT,
    valign: "middle", rowH: 0.55, align: "left",
    fill: { color: WHITE } });
  s.addText("The default connection holds all DRAMS data; the other five are read-only sources searched from the Databank module (DLMS is Microsoft SQL Server, the rest MySQL).",
    { x: 5.0, y: 6.0, w: 7.75, h: 1.0, fontFace: BF, fontSize: 12, italic: true, color: MUTED, margin: 0 });
  PAGE++; pageNum(s, PAGE);
})();

/* =================================================================== */
/* 11. AUTH before() (fig 8, very tall)                                */
/* =================================================================== */
slideImgText("08 · Security", "Authentication & the before() gate", 8,
  "What every page checks", [
   "Driver: Kohana auth, ORM (Auth_Orm). Passwords SHA256-hashed; only user_id stored in session.",
   "before() runs on each authenticated controller: logged-in? → IP blocked? → user active/approved? → set role_id → menu-access check → audit log.",
   "Failures redirect to Login / Blocked or force a logout.",
   "Three login paths: username+password (IP-throttled), one-time SSO token, and password recovery."
  ], { imgW: 4.6, fs: 13.5 });

/* =================================================================== */
/* 12. RBAC (fig 9) + tier table                                       */
/* =================================================================== */
slideImgText("08 · Access control", "Role-based access — three combined checks", 9,
  "Permission tiers", [
   "Permission level (0–5): Administrator → Tech support → Executive → Field officer → Dev support.",
   "Data scope: all · region · district · own — filters every request & report query.",
   "Menu access: chek_role_access() reads the manu_management matrix to show/hide each sidebar item.",
   "Fine-grained roles gate modules (Watchlist 25, Organization 31/32, Databank 34/35); sensitive persons protected by cis_sensitive_person_acl."
  ], { imgW: 6.4, fs: 12.5 });

/* =================================================================== */
/* 13. SECTION DIVIDER — Core process flows                            */
/* =================================================================== */
(() => {
  const s = pres.addSlide();
  s.background = { color: NAVY };
  s.addShape(pres.shapes.RECTANGLE, { x: 0.9, y: 2.9, w: 1.5, h: 0.09, fill: { color: GOLD } });
  s.addText("PART TWO", { x: 0.9, y: 2.35, w: 8, h: 0.4, fontFace: HF, fontSize: 14,
    color: GOLD, bold: true, charSpacing: 3, margin: 0 });
  s.addText("Core Process Flows", { x: 0.9, y: 3.15, w: 11.5, h: 1.0, fontFace: HF,
    fontSize: 44, bold: true, color: WHITE, margin: 0 });
  s.addText("Login · Search · Telecom request lifecycle · Ingestion · Identity sync · Watchlist · Reporting · Cron · Integrations",
    { x: 0.92, y: 4.25, w: 11.4, h: 0.6, fontFace: BF, fontSize: 14, color: ICE, margin: 0 });
})();

/* =================================================================== */
/* 14. LOGIN FLOW (fig 10)                                             */
/* =================================================================== */
slideImgText("Flow 10.1 · Login", "Login & session flow", 10,
  "Highlights", [
   "Valid URL token → expiry check → force_login() (token consumed once).",
   "Otherwise the login form posts to action_check().",
   "Per-IP throttling blocks brute-force attempts (~5 tries → IP block).",
   "On success: session created (user_id), complete_login() runs, redirect to the dashboard."
  ], { imgW: 6.3, fs: 13.5 });

/* =================================================================== */
/* 15. SEARCH → PROFILE (fig 11)                                       */
/* =================================================================== */
slideImgText("Flow 10.2 · Search", "Person search → profile", 11,
  "Highlights", [
   "Search by mobile/MSISDN, CNIC identity, advanced (IMSI/IMEI/name), or B-party / bulk / location / device.",
   "Results render in DataTables; clicking a person opens the profile with an encrypted id.",
   "The sidebar switches to person context: call/SMS, B-party, location, CDR graph, profile tabs, family tree, social links.",
   "Every view is written to user_activity_log."
  ], { imgW: 6.6, fs: 13.5 });

/* =================================================================== */
/* 16. ⭐ TELECOM REQUEST LIFECYCLE (fig 12, tall) — the core         */
/* =================================================================== */
(() => {
  const s = pres.addSlide();
  s.background = { color: WHITE };
  titleBar(s, "Flow 10.3 · CORE", "Telecom data request lifecycle");
  s.addShape(pres.shapes.OVAL, { x: 11.95, y: 0.55, w: 0.55, h: 0.55, fill: { color: GOLD } });
  s.addText("★", { x: 11.95, y: 0.53, w: 0.55, h: 0.55, fontFace: HF, fontSize: 22,
    bold: true, color: WHITE, align: "center", valign: "middle", margin: 0 });
  const p = fit(RATIO[12], 0.55, 1.6, 4.7, 5.2);
  s.addImage({ path: D(12), x: p.x, y: p.y, w: p.w, h: p.h });
  caption(s, "Figure 12 — fully asynchronous, email-driven", 0.55, 6.85, 4.7);
  const rx = 5.6, rw = 7.2;
  s.addText("Six asynchronous stages", { x: rx, y: 1.65, w: rw, h: 0.4, fontFace: HF,
    fontSize: 16, bold: true, color: GOLD, margin: 0 });
  const steps = [
    ["1  Create", "Analyst files a user_request / admin_request (CDR, subscriber, location, NADRA…); status = pending, body built from email_templates."],
    ["2  Send", "Cron action_email_send_* picks pending requests by operator + priority and emails the operator via SMTP; logs email_messages."],
    ["3  Operator", "Mobile operator (Mobilink, Ufone, Telenor, Zong, Warid, PTCL) replies by email with an attachment."],
    ["4  Receive", "Cron IMAP-polls Gmail (UNSEEN), stores the reply and matches it to the request by reference/message_id."],
    ["5  Parse", "action_email_parse_* (per type & operator) validates and writes person_call_log / SMS / subscriber / location, then sets status = complete."],
    ["6  Consume", "B-party table rebuilt; the analyst views CDR analytics, locations and graphs. Errors route to retry queues."]
  ];
  let y = 2.15;
  steps.forEach((st) => {
    s.addText(st[0], { x: rx, y, w: 1.25, h: 0.7, fontFace: HF, fontSize: 13.5,
      bold: true, color: ACCENT, valign: "top", margin: 0 });
    s.addText(st[1], { x: rx + 1.3, y, w: rw - 1.3, h: 0.78, fontFace: BF, fontSize: 11.5,
      color: TEXT, valign: "top", margin: 0 });
    y += 0.79;
  });
  PAGE++; pageNum(s, PAGE);
})();

/* =================================================================== */
/* 17. CDR / BULK INGESTION (fig 13, tall)                             */
/* =================================================================== */
slideImgText("Flow 10.4 · Ingestion", "CDR / bulk data ingestion", 13,
  "When CDR arrives as a file", [
   "Analyst uploads by MSISDN, CNIC or IMEI via Controller_Upload.",
   "Inputs validated (checkmsisdn / checkcnic), file stored under uploads/cdr/manual/ with a files row.",
   "Helpers_Upload::data_mapping parses CSV/Excel rows into person_call_log / SMS / phone_number / device.",
   "Model_Shortparse sets SIM/device first- & last-use; processing_index = 7 (uploaded).",
   "Cron rebuilds person_summary → data appears in CDR analytics."
  ], { imgW: 5.0, fs: 13 });

/* =================================================================== */
/* 18. IDENTITY SYNC (fig 14, very tall)                               */
/* =================================================================== */
slideImgText("Flow 10.5 · Identity", "NADRA / Verisys / Family-Tree / Travel sync", 14,
  "Two-step staging pattern", [
   "The identity request goes out by email or API (request types 8 / 10 / 11).",
   "Responses (images/data) land in a temp table + uploads/*_temp_images/.",
   "Controller_Verisyssync matches each by CNIC to a pending request.",
   "On match: image moved to permanent storage, person profile updated, attachment_status = 1.",
   "No match → attachment_status = 3; results then show in the person profile."
  ], { imgW: 4.5, fs: 13 });

/* =================================================================== */
/* 19. WATCHLIST (fig 15)                                              */
/* =================================================================== */
slideImgText("Flow 10.6 · Watchlist", "Watchlist management", 15,
  "Highlights", [
   "Add: choose category (black/grey/white) + district + tag, search persons, add to watchlist (in_watchlist = 1).",
   "View: area-wise (grouped by district) or per-user (scope-filtered) watchlists.",
   "Drill into details, then jump straight to a person dashboard.",
   "Removal flips in_watchlist back to 0; tags stored in person_tags + lu_tags."
  ], { imgW: 6.6, fs: 13.5 });

/* =================================================================== */
/* 20. REPORTING (fig 16, tallish)                                     */
/* =================================================================== */
slideImgText("Flow 10.7 · Reporting", "Reporting & audit", 16,
  "Three report families", [
   "User's reports: logins, requests sent, panel/URL logs, audit & performance.",
   "Person's reports: top-searched, category/district lists, sensitive list, devices/SIMs, breakups.",
   "Admin reports: identity breakups, Verisys pending/response, user breakup, blocked IPs.",
   "All scope-aware; output as on-screen DataTables or Excel exports (phpspreadsheet).",
   "Audit trail comes from user_activity_log written in before() on every action."
  ], { imgW: 5.4, fs: 12.5 });

/* =================================================================== */
/* 21. CRON PIPELINE (fig 17)                                          */
/* =================================================================== */
slideImgWide("Flow 10.8 · Background engine", "Cron / background processing pipeline", 17, [
  "Cronjob is the asynchronous engine — scheduled HTTP/CLI hits trigger SEND, RECEIVE, PARSE, AGGREGATE, OCR and HEALTH actions.",
  "Lock files & cooldowns prevent overlapping IMAP runs; every step logs to Model_ErrorLog with context and severity; failed items route to retry queues."
], { imgH: 3.4 });

/* =================================================================== */
/* 22. EXTERNAL INTEGRATIONS (fig 18, wide)                            */
/* =================================================================== */
slideImgWide("Flow 10.9 · Integrations", "External integrations", 18, [
  "AIES / CCTW: REST API (Controller_Aiesapi) — key-authenticated person lookup, 3D module, fingerprints and provisioning, returning JSON + an encrypted profile link.",
  "Google: Gmail OAuth (send/receive operator email) and Vision OCR (ECP address backfill via the cron OCR daemon)."
], { imgH: 3.0 });

/* =================================================================== */
/* 23. SITEMAP (fig 19, wide)                                          */
/* =================================================================== */
slideImgWide("Navigation", "Sitemap & navigation", 19, [
  "The left sidebar (sidebar_user.php) is role-filtered; opening a person swaps it to a person-context sidebar (sidebar_person.php).",
  "Top-level areas: Search · Profile · Data Upload · User Requests · Watch List · DRAMS Plus · Databank · Reports · Administration · System Monitoring."
], { imgH: 2.6 });

/* =================================================================== */
/* 24. ROLES & JOURNEY (fig 20, very wide) + table                     */
/* =================================================================== */
(() => {
  const s = pres.addSlide();
  s.background = { color: WHITE };
  titleBar(s, "Users", "Roles & a representative analyst journey");
  const rows = [
    [{ text: "User type", options: { bold: true, color: WHITE, fill: { color: NAVY } } },
     { text: "Roles", options: { bold: true, color: WHITE, fill: { color: NAVY } } },
     { text: "Primary journeys", options: { bold: true, color: WHITE, fill: { color: NAVY } } }],
    ["Super Admin", "1", "User registration, menu management, ACL, all reports & DRAMS Plus"],
    ["Analyst / Operator", "2–8", "Search persons, analyse CDR, submit requests, build reports"],
    ["Request manager", "14+", "Manage request queue, scheduling, projects"],
    ["Data-upload operator", "9–12", "Bulk-upload CDR files, monitor parsing"],
    ["Watchlist / Databank", "25 / 34–35", "Tag & monitor persons; federated identity searches"]
  ];
  s.addTable(rows, { x: 0.6, y: 1.75, w: 12.1, colW: [2.6, 1.9, 7.6],
    border: { pt: 0.5, color: LINE }, fontFace: BF, fontSize: 12.5, color: TEXT,
    valign: "middle", rowH: 0.5, fill: { color: WHITE } });
  s.addText("Representative analyst journey", { x: 0.6, y: 5.0, w: 12, h: 0.4,
    fontFace: HF, fontSize: 15, bold: true, color: ACCENT, margin: 0 });
  const p = fit(RATIO[20], 0.55, 5.45, 12.23, 1.35);
  s.addImage({ path: D(20), x: p.x, y: p.y, w: p.w, h: p.h });
  caption(s, "Figure 20 — log in → search → (request if missing) → analyse → watchlist / report", 0.55, p.y + p.h + 0.02, 12.23);
  PAGE++; pageNum(s, PAGE);
})();

/* =================================================================== */
/* 25. CLOSING                                                          */
/* =================================================================== */
(() => {
  const s = pres.addSlide();
  s.background = { color: NAVY };
  s.addShape(pres.shapes.RECTANGLE, { x: 0.9, y: 2.55, w: 1.5, h: 0.09, fill: { color: ACCENT } });
  s.addText("Summary", { x: 0.9, y: 2.05, w: 8, h: 0.4, fontFace: HF, fontSize: 14,
    color: ACCENT, bold: true, charSpacing: 3, margin: 0 });
  s.addText("One platform, end-to-end", { x: 0.9, y: 2.8, w: 11.5, h: 0.9, fontFace: HF,
    fontSize: 38, bold: true, color: WHITE, margin: 0 });
  s.addText([
    { text: "Acquire → Parse → Profile → Analyse → Report.  ", options: { bold: true, color: WHITE } },
    { text: "DRAMS Live turns operator email replies and federated databases into a searchable, audited, role-governed intelligence picture of persons and their communications.", options: { color: ICE } }
  ], { x: 0.92, y: 3.9, w: 11.3, h: 1.4, fontFace: BF, fontSize: 16, color: ICE, margin: 0, lineSpacingMultiple: 1.15 });
  s.addText("Companion files:  DRAMS_Live_System_Documentation.docx  ·  .md  ·  20 diagram PNGs",
    { x: 0.92, y: 6.4, w: 11.4, h: 0.4, fontFace: BF, fontSize: 12, italic: true, color: ACCENT, margin: 0 });
})();

const out = path.join(DIR, "DRAMS_Live_System_Presentation.pptx");
pres.writeFile({ fileName: out }).then(() => console.log("Saved:", out));
