/**
 * SCOM Email Attachments toggle for the user-side request forms.
 *
 * When the analyst selects SCOM (mnc=8) in the Company select, two
 * file inputs become mandatory: Copy of FIR and Cover Letter. A red
 * instruction label is shown above them. When SCOM is unselected (or
 * the only selection removed), the block hides and the inputs are
 * cleared so a stale file doesn't ride the next submit.
 *
 * Markup contract — the request view places this block immediately
 * before its existing "Requested Attachment" section:
 *
 *     <div id="scom_attachments_block" style="display:none">
 *         <div class="scom-instruction">…red label…</div>
 *         <input type="file" name="scom_fir" id="scom_fir">
 *         <input type="file" name="scom_cover_letter" id="scom_cover_letter">
 *     </div>
 *
 * The company dropdown is the existing `#company_name_get` (single or
 * multi-select). SCOM corresponds to option value "8" (mnc, not
 * company_id).
 */
(function () {
    'use strict';

    var SCOM_VALUE = '8';

    function injectStyles() {
        if (document.getElementById('scom-attachments-styles')) return;
        var css = [
            '#scom_attachments_block .scom-instruction {',
            '    color: #c0392b;',
            '    font-weight: 600;',
            '    margin-bottom: 8px;',
            '    padding: 8px 12px;',
            '    border: 1px solid #f5c6cb;',
            '    background: #fdecea;',
            '    border-radius: 4px;',
            '}',
            '#scom_attachments_block .scom-file-row {',
            '    margin-bottom: 10px;',
            '}',
            '#scom_attachments_block label.required-asterisk::after {',
            '    content: " *";',
            '    color: #c0392b;',
            '}'
        ].join('\n');
        var style = document.createElement('style');
        style.id = 'scom-attachments-styles';
        style.appendChild(document.createTextNode(css));
        document.head.appendChild(style);
    }

    function isScomSelected($select) {
        var val = $select.val();
        if (val === null || typeof val === 'undefined') return false;
        if (Array.isArray(val)) {
            for (var i = 0; i < val.length; i++) {
                if (String(val[i]) === SCOM_VALUE) return true;
            }
            return false;
        }
        return String(val) === SCOM_VALUE;
    }

    function setRequired(input, required) {
        if (!input) return;
        if (required) {
            input.setAttribute('required', 'required');
        } else {
            input.removeAttribute('required');
            // Clear the file when SCOM is deselected so a stale upload
            // doesn't ride the next submit.
            try { input.value = ''; } catch (e) { /* ignore */ }
        }
    }

    function applyToggle($select) {
        var block = document.getElementById('scom_attachments_block');
        if (!block) return;
        var fir = document.getElementById('scom_fir');
        var cover = document.getElementById('scom_cover_letter');
        var show = isScomSelected($select);
        block.style.display = show ? '' : 'none';
        setRequired(fir, show);
        setRequired(cover, show);
    }

    function init() {
        injectStyles();
        if (typeof window.jQuery === 'undefined') return;
        var $ = window.jQuery;
        var $select = $('#company_name_get');
        if (!$select.length) return;
        applyToggle($select);
        $select.on('change', function () {
            applyToggle($select);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
