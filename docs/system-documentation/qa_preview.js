/* Faithful HTML preview + screenshots of the deck for visual QA.
   Instruments pptxgenjs so the preview is generated from the SAME draw calls
   build_pptx.js issues, then screenshots each slide with the cached Chromium. */
const path = require("path");
const fs = require("fs");
const pptxgen = require("pptxgenjs");
const PUP = "C:/Users/ali_r/AppData/Local/npm-cache/_npx/668c188756b835f3/node_modules/puppeteer";
const puppeteer = require(PUP);

// ---- instrument the Slide prototype ----
const probe = new pptxgen();
const SH = probe.shapes;
const sampleSlide = probe.addSlide();
const SlideProto = Object.getPrototypeOf(sampleSlide);

const REC = new WeakMap();
const ORDER = [];
function rec(s) { if (!REC.has(s)) REC.set(s, { bg: "FFFFFF", items: [] }); return REC.get(s); }

const oText = SlideProto.addText;
SlideProto.addText = function (t, o) { rec(this).items.push({ k: "text", t, o: Object.assign({}, o) }); return oText.call(this, t, o); };
const oImage = SlideProto.addImage;
SlideProto.addImage = function (o) { rec(this).items.push({ k: "image", o: Object.assign({}, o) }); return oImage.call(this, o); };
const oShape = SlideProto.addShape;
SlideProto.addShape = function (s, o) { rec(this).items.push({ k: "shape", s, o: Object.assign({}, o) }); return oShape.call(this, s, o); };
const oTable = SlideProto.addTable;
SlideProto.addTable = function (r, o) { rec(this).items.push({ k: "table", r, o: Object.assign({}, o) }); return oTable.call(this, r, o); };

Object.defineProperty(SlideProto, "background", {
  set(v) { rec(this).bg = (v && v.color) || "FFFFFF"; this._bg = v; },
  get() { return this._bg; }, configurable: true
});

const oAdd = pptxgen.prototype.addSlide;
pptxgen.prototype.addSlide = function (...a) { const s = oAdd.apply(this, a); ORDER.push(s); return s; };

// ---- run the real deck builder (captures all calls) ----
require("./build_pptx.js");

// ---- helpers to render captured model to HTML ----
const PXIN = 96;            // px per inch
const PT = 96 / 72;         // px per pt
const esc = (s) => String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
function txt(t) {
  if (Array.isArray(t)) return t.map(r => (typeof r === "string" ? r : (r.text || ""))).join("");
  return t == null ? "" : String(t);
}
function fileURL(p) { return "file:///" + p.replace(/\\/g, "/"); }

function itemHTML(it) {
  const o = it.o || {};
  const L = (o.x || 0) * PXIN, T = (o.y || 0) * PXIN, W = (o.w || 1) * PXIN, H = (o.h || 0.3) * PXIN;
  if (it.k === "image") {
    return `<img src="${fileURL(o.path)}" style="position:absolute;left:${L}px;top:${T}px;width:${W}px;height:${H}px;object-fit:fill;">`;
  }
  if (it.k === "shape") {
    const fill = (o.fill && o.fill.color) ? "#" + o.fill.color : "transparent";
    const isOval = it.s === SH.OVAL;
    const radius = isOval ? "50%" : "0";
    return `<div style="position:absolute;left:${L}px;top:${T}px;width:${W}px;height:${H}px;background:${fill};border-radius:${radius};"></div>`;
  }
  if (it.k === "text") {
    const fs = (o.fontSize || 14) * PT;
    const col = o.color ? "#" + o.color : "#000";
    const weight = o.bold ? "700" : "400";
    const style = o.italic ? "italic" : "normal";
    const align = o.align || "left";
    const valign = o.valign === "middle" ? "center" : (o.valign === "bottom" ? "flex-end" : "flex-start");
    let inner;
    if (Array.isArray(o.bullet) || (Array.isArray(it.t) && it.t.some(r => r.options && r.options.bullet))) {
      inner = it.t.map(r => `<div style="margin-bottom:${(r.options && r.options.paraSpaceAfter ? r.options.paraSpaceAfter * PT : 4)}px;">&bull;&nbsp;${esc(r.text)}</div>`).join("");
    } else if (Array.isArray(it.t)) {
      inner = it.t.map(r => `<span style="font-weight:${(r.options && r.options.bold) ? 700 : weight};">${esc(r.text || "")}</span>`).join("");
    } else {
      inner = esc(txt(it.t)).replace(/\n/g, "<br>");
    }
    const cs = o.charSpacing ? `letter-spacing:${o.charSpacing}px;` : "";
    // dashed outline so overflow is visible against the box bounds
    return `<div style="position:absolute;left:${L}px;top:${T}px;width:${W}px;height:${H}px;
      display:flex;flex-direction:column;justify-content:${valign};
      font-family:'Trebuchet MS',Calibri,Arial,sans-serif;font-size:${fs}px;color:${col};
      font-weight:${weight};font-style:${style};text-align:${align};line-height:1.2;${cs}
      outline:1px dashed rgba(255,0,0,0.28);box-sizing:border-box;overflow:visible;">${inner}</div>`;
  }
  if (it.k === "table") {
    const o2 = it.o || {};
    const L2 = (o2.x || 0) * PXIN, T2 = (o2.y || 0) * PXIN, W2 = (o2.w || 8) * PXIN;
    const colW = o2.colW || [];
    let html = `<table style="position:absolute;left:${L2}px;top:${T2}px;width:${W2}px;border-collapse:collapse;font-family:Calibri,Arial;font-size:${(o2.fontSize||12)*PT}px;">`;
    it.r.forEach(row => {
      html += "<tr>";
      row.forEach((cell, ci) => {
        const c = (cell && cell.options) ? cell : { text: cell, options: {} };
        const op = c.options || {};
        const bg = op.fill ? "#" + op.fill.color : "transparent";
        const cc = op.color ? "#" + op.color : "#1B2A3A";
        const fw = op.bold ? "700" : "400";
        const cw = colW[ci] ? `width:${colW[ci]*PXIN}px;` : "";
        html += `<td style="border:1px solid #D5DEE7;padding:4px 7px;background:${bg};color:${cc};font-weight:${fw};${cw}vertical-align:middle;">${esc(txt(c.text))}</td>`;
      });
      html += "</tr>";
    });
    html += "</table>";
    return html;
  }
  return "";
}

let body = "";
ORDER.forEach((s, i) => {
  const r = REC.get(s);
  const items = r.items.map(itemHTML).join("\n");
  body += `<div class="slide" id="s${i + 1}" style="background:#${r.bg};">${items}
    <div style="position:absolute;left:6px;top:4px;font:11px monospace;color:#888;">slide ${i + 1}</div></div>\n`;
});

const html = `<!doctype html><html><head><meta charset="utf-8"><style>
  body{margin:0;background:#444;}
  .slide{position:relative;width:1280px;height:720px;margin:0 auto 18px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.4);}
</style></head><body>${body}</body></html>`;

const htmlPath = path.join(__dirname, "preview.html");
fs.writeFileSync(htmlPath, html);
console.log("Wrote", htmlPath, "with", ORDER.length, "slides");

// ---- screenshot each slide ----
(async () => {
  const outDir = path.join(__dirname, "qa");
  if (!fs.existsSync(outDir)) fs.mkdirSync(outDir);
  const browser = await puppeteer.launch({ headless: "new", args: ["--no-sandbox"] });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 720, deviceScaleFactor: 1 });
  await page.goto(fileURL(htmlPath), { waitUntil: "networkidle0" });
  for (let i = 1; i <= ORDER.length; i++) {
    const el = await page.$("#s" + i);
    const n = String(i).padStart(2, "0");
    await el.screenshot({ path: path.join(outDir, `slide-${n}.png`) });
  }
  await browser.close();
  console.log("Screenshots in", outDir);
})();
