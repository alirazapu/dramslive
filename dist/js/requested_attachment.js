/**
 * Requested Attachment paste-zone widget for the user-side request forms
 * (CDR / Subscriber / Location / CNIC-SIMs). Wraps a plain
 * `<input type="file" name="rqtfile">` with a click-or-paste-or-drop
 * interaction so analysts can attach a screenshot via Ctrl+V instead of
 * having to save it to disk first.
 *
 * Markup contract — each view places this around its file input:
 *
 *     <div class="rqt-paste-zone" tabindex="0">
 *         <div class="rqt-hint">…hint text…</div>
 *         <input type="file" name="rqtfile" accept="…">
 *         <div class="rqt-preview"></div>
 *     </div>
 *
 * The script auto-binds to every `.rqt-paste-zone` on DOMReady. The
 * underlying `<input type="file">` keeps its `name="rqtfile"` so the
 * existing FormData submit transmits whatever ends up in `input.files`,
 * regardless of whether the analyst clicked, dropped, or pasted. No
 * server-side change is needed beyond the work already done in
 * Controller_Email::action_send.
 *
 * Browser support: relies on `DataTransfer` for assigning a synthetic
 * File to `input.files`. Supported in Chrome 73+, Edge 79+, Firefox 62+,
 * Safari 14.1+ — comfortably ahead of any browser DRAMS analysts run.
 */
(function () {
    'use strict';

    // Inject our small CSS once. Doing it from JS keeps the asset story
    // one-file: each view only needs to add the script tag.
    function injectStyles() {
        if (document.getElementById('rqt-paste-zone-styles')) return;
        var css = [
            '.rqt-paste-zone {',
            '    border: 2px dashed #b0b8c1;',
            '    background: #fafbfc;',
            '    border-radius: 4px;',
            '    padding: 14px;',
            '    cursor: pointer;',
            '    transition: border-color .15s, background .15s;',
            '    outline: none;',
            '}',
            '.rqt-paste-zone:hover, .rqt-paste-zone:focus {',
            '    border-color: #3c8dbc;',
            '    background: #f3f8fc;',
            '}',
            '.rqt-paste-zone.rqt-dragover {',
            '    border-color: #00a65a;',
            '    background: #e8f7e8;',
            '}',
            '.rqt-paste-zone.rqt-has-file {',
            '    border-style: solid;',
            '    border-color: #00a65a;',
            '    background: #fff;',
            '}',
            '.rqt-paste-zone .rqt-hint {',
            '    color: #6c757d;',
            '    font-size: 13px;',
            '    line-height: 1.5;',
            '}',
            '.rqt-paste-zone .rqt-hint kbd {',
            '    background: #eee;',
            '    border: 1px solid #ccc;',
            '    border-radius: 3px;',
            '    padding: 1px 5px;',
            '    font-size: 11px;',
            '    color: #333;',
            '}',
            '.rqt-paste-zone input[type="file"] {',
            '    /* Hide the native widget — the zone itself is the click target. */',
            '    position: absolute;',
            '    width: 1px; height: 1px;',
            '    opacity: 0; pointer-events: none;',
            '}',
            '.rqt-paste-zone .rqt-preview {',
            '    margin-top: 10px;',
            '}',
            '.rqt-paste-zone .rqt-preview img {',
            '    max-width: 100%;',
            '    max-height: 140px;',
            '    border: 1px solid #ddd;',
            '    padding: 2px;',
            '    background: #fff;',
            '    border-radius: 3px;',
            '}',
            '.rqt-paste-zone .rqt-meta {',
            '    margin-top: 6px;',
            '    font-size: 12px;',
            '    color: #555;',
            '}',
            '.rqt-paste-zone .rqt-meta .rqt-clear {',
            '    margin-left: 10px;',
            '    color: #c0392b;',
            '    text-decoration: underline;',
            '    cursor: pointer;',
            '}'
        ].join('\n');
        var style = document.createElement('style');
        style.id = 'rqt-paste-zone-styles';
        style.appendChild(document.createTextNode(css));
        document.head.appendChild(style);
    }

    function escapeHTML(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return {
                '&': '&amp;', '<': '&lt;', '>': '&gt;',
                '"': '&quot;', "'": '&#39;'
            }[c];
        });
    }

    function formatSize(bytes) {
        if (!bytes && bytes !== 0) return '';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return Math.round(bytes / 1024) + ' KB';
        return (bytes / 1024 / 1024).toFixed(1) + ' MB';
    }

    /**
     * Build a synthetic name for a clipboard-pasted image. The browser
     * gives clipboard files a name like "image.png" with no timestamp,
     * which means two pastes in the same submit could clash with the
     * same on-disk filename. Add a millisecond stamp so each paste lands
     * on a distinct byte stream when the analyst submits.
     */
    function buildPasteName(mime) {
        var ext = (mime && mime.indexOf('image/') === 0)
            ? mime.substring('image/'.length).toLowerCase()
            : 'png';
        // Some browsers report 'jpeg' here, which is correct for the
        // mime type but the conventional file extension is 'jpg'.
        if (ext === 'jpeg') ext = 'jpg';
        var d = new Date();
        var pad = function (n) { return n < 10 ? '0' + n : n; };
        var stamp = d.getFullYear()
            + pad(d.getMonth() + 1)
            + pad(d.getDate())
            + '-'
            + pad(d.getHours())
            + pad(d.getMinutes())
            + pad(d.getSeconds())
            + '-'
            + d.getMilliseconds();
        return 'screenshot-' + stamp + '.' + ext;
    }

    /**
     * Assigning the pasted/dropped File to input.files requires going
     * through DataTransfer — `input.files` is read-only via direct
     * assignment of an arbitrary File. We also rebuild the File so we
     * can control the on-disk name (clipboard images come back with
     * generic names like "image.png").
     */
    function setInputFile(input, file, customName) {
        try {
            var name = customName || file.name || 'attachment';
            var dt = new DataTransfer();
            var rebuilt = new File([file], name, {
                type: file.type,
                lastModified: file.lastModified || Date.now()
            });
            dt.items.add(rebuilt);
            input.files = dt.files;
            return rebuilt;
        } catch (err) {
            // DataTransfer constructor or items.add not available
            // (vintage browsers). Fall back to letting the file survive
            // as the live File reference; the form will still pick it up
            // because we wired the underlying input's own change event.
            // eslint-disable-next-line no-console
            console.warn('[requested_attachment] DataTransfer unavailable, paste fallback engaged', err);
            return file;
        }
    }

    function bindZone(zone) {
        if (zone.dataset.rqtBound === '1') return; // idempotent
        zone.dataset.rqtBound = '1';

        var input = zone.querySelector('input[type="file"]');
        var preview = zone.querySelector('.rqt-preview');
        if (!input || !preview) return;

        if (!zone.hasAttribute('tabindex')) zone.setAttribute('tabindex', '0');

        // Click-to-pick. Avoid recursion when the click bubbled up from
        // the (hidden) input itself or from elements we render inside
        // the preview such as the "remove" link.
        zone.addEventListener('click', function (e) {
            if (e.target === input) return;
            if (e.target.closest && e.target.closest('.rqt-clear')) return;
            input.click();
        });

        // Paste handling. The paste event only fires on the focused
        // element (or document, depending on browser). Listening on the
        // zone covers the explicit "paste here" UX. We also listen on
        // document and route to the focused zone if any, so analysts who
        // hit Ctrl+V without first clicking the zone still succeed.
        zone.addEventListener('paste', function (e) {
            handlePaste(e, zone, input, preview);
        });

        // Drag-and-drop. Mirrors the paste path: extract a File, hand it
        // to setInputFile, render a preview.
        ['dragenter', 'dragover'].forEach(function (ev) {
            zone.addEventListener(ev, function (e) {
                e.preventDefault();
                e.stopPropagation();
                zone.classList.add('rqt-dragover');
            });
        });
        ['dragleave', 'dragend'].forEach(function (ev) {
            zone.addEventListener(ev, function (e) {
                e.preventDefault();
                e.stopPropagation();
                zone.classList.remove('rqt-dragover');
            });
        });
        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.remove('rqt-dragover');
            var dropped = e.dataTransfer && e.dataTransfer.files;
            if (dropped && dropped.length) {
                var f = dropped[0];
                setInputFile(input, f, f.name);
                renderPreview(zone, preview, input, f);
            }
        });

        // Standard browse picker — render preview after the user picks.
        input.addEventListener('change', function () {
            if (input.files && input.files[0]) {
                renderPreview(zone, preview, input, input.files[0]);
            } else {
                clearPreview(zone, preview);
            }
        });
    }

    function handlePaste(e, zone, input, preview) {
        var items = e.clipboardData && e.clipboardData.items;
        if (!items || !items.length) return;
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            if (item.kind === 'file' && item.type && item.type.indexOf('image/') === 0) {
                var blob = item.getAsFile();
                if (!blob) continue;
                var name = buildPasteName(blob.type);
                var saved = setInputFile(input, blob, name);
                renderPreview(zone, preview, input, saved);
                e.preventDefault();
                return;
            }
        }
        // No image item was found. Don't preventDefault — let the user
        // paste text into other fields normally if they happened to be
        // focused on the zone with text in their clipboard.
    }

    function renderPreview(zone, preview, input, file) {
        zone.classList.add('rqt-has-file');
        var name = file.name || 'attachment';
        var size = formatSize(file.size);
        var mime = file.type || '';
        var html;
        if (mime.indexOf('image/') === 0) {
            var url = URL.createObjectURL(file);
            html = '<img src="' + url + '" alt="preview">'
                 + '<div class="rqt-meta"><strong>' + escapeHTML(name) + '</strong> '
                 + escapeHTML(size)
                 + ' <a href="#" class="rqt-clear">remove</a></div>';
        } else {
            html = '<div class="rqt-meta"><i class="fa fa-paperclip"></i> '
                 + '<strong>' + escapeHTML(name) + '</strong> '
                 + escapeHTML(size)
                 + ' <a href="#" class="rqt-clear">remove</a></div>';
        }
        preview.innerHTML = html;
        var clear = preview.querySelector('.rqt-clear');
        if (clear) {
            clear.addEventListener('click', function (ev) {
                ev.preventDefault();
                ev.stopPropagation();
                input.value = '';
                clearPreview(zone, preview);
            });
        }
    }

    function clearPreview(zone, preview) {
        zone.classList.remove('rqt-has-file');
        preview.innerHTML = '';
    }

    /**
     * Document-level paste fallback. If the analyst hits Ctrl+V while
     * not focused on any input — common after taking a screenshot —
     * route the paste to the first available paste-zone on the page.
     * We skip routing when the active element is something where Ctrl+V
     * has its own meaning (textarea, text input, contenteditable).
     */
    function bindDocumentPasteFallback() {
        document.addEventListener('paste', function (e) {
            var ae = document.activeElement;
            if (ae && (
                ae.tagName === 'TEXTAREA' ||
                ae.tagName === 'INPUT' && /^(text|search|email|url|tel|password|number)$/i.test(ae.type) ||
                ae.isContentEditable
            )) {
                return;
            }
            var zone = document.querySelector('.rqt-paste-zone');
            if (!zone) return;
            var input = zone.querySelector('input[type="file"]');
            var preview = zone.querySelector('.rqt-preview');
            if (!input || !preview) return;
            handlePaste(e, zone, input, preview);
        });
    }

    function init() {
        injectStyles();
        var zones = document.querySelectorAll('.rqt-paste-zone');
        for (var i = 0; i < zones.length; i++) bindZone(zones[i]);
        bindDocumentPasteFallback();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
