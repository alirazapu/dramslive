<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * DRAMS Databank — one search of every external source through the
 * Databank integration API (Helpers_DatabankApi, config/databank.php).
 * DRAMS does not query those databases for this page; the Databank
 * decides what the DRAMS API user may read and see.
 *
 *   /databank/all_sources          — form
 *   /databank/all_sources_results  — AJAX result fragment (one table per source)
 *
 * The API searches by ONE identifier per request: mobile number, CNIC,
 * name or FIR number, optionally following the CNICs / numbers found
 * ("linked", mobile number or CNIC only).
 *
 * Visibility: the per-user "DRAMS Databank" right (Helpers_Databank::
 * has_access, granted in Access Control List -> Manage Rights) — the same
 * check as the sidebar's DRAMS Databank menu. _require_databank_access()
 * is called from every action so URLs cannot be hit directly by users
 * without the right.
 */
class Controller_Databank extends Controller_Working
{

    public function __Construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->request  = $request;
        $this->response = $response;
    }

    public function action_index()
    {
        $this->redirect('databank/all_sources');
    }

    public function action_all_sources()
    {
        $this->_require_databank_access();
        // ?value=&type=&linked= pre-fill the form and run the search on
        // load (used by the "Search in Databank" links on /user/search_person).
        $prefill = $this->_collect_filters(array('value', 'type', 'linked'));
        if (isset($prefill['type']) && !in_array($prefill['type'], Helpers_DatabankApi::TYPES, TRUE)) {
            unset($prefill['type']);
        }
        $this->template->content = $this->_form_view(array(
            'prefill'    => $prefill,
            'title'      => 'Search All Sources',
            'breadcrumb' => 'All Sources',
            'ajax_url'   => URL::site('databank/all_sources_results', TRUE),
            'hint'       => 'Searches one value in every source the Databank allows DRAMS to read (at most '
                          . $this->_records_per_search() . ' records per source). "Follow linked" also searches the CNICs and '
                          . 'numbers found on the records (mobile number or CNIC only).',
            'fields'     => array(
                array('name' => 'value', 'label' => 'Search value', 'placeholder' => 'Mobile number, CNIC, name or FIR number'),
                array('name' => 'type', 'label' => 'Search by', 'type' => 'select', 'options' => array(
                    ''       => 'Detect automatically',
                    'mobile' => 'Mobile number',
                    'cnic'   => 'CNIC',
                    'name'   => 'Name',
                    'fir'    => 'FIR number',
                )),
                array('name' => 'linked', 'label' => 'Follow linked CNICs / numbers', 'type' => 'checkbox'),
            ),
        ));
    }

    public function action_all_sources_results()
    {
        $this->auto_render = FALSE;
        $this->_require_databank_access();
        try {
            $filters = $this->_collect_filters(array('value', 'type', 'linked', 'view'));
            if (!isset($filters['value'])) {
                $this->response->body($this->_no_filters());
                return;
            }
            $type = isset($filters['type']) ? $filters['type'] : self::_detect_type($filters['value']);
            if (!in_array($type, Helpers_DatabankApi::TYPES, TRUE)) {
                $this->response->body($this->_notice('warning', 'Choose what the value is: mobile number, CNIC, name or FIR number.'));
                return;
            }
            $result = Helpers_Databank::search_all_sources($type, $filters['value'], !empty($filters['linked']));
            $this->_audit('all_sources ' . $type, $result['request_id']);
            $view = isset($filters['view']) ? $filters['view'] : '';
            if ($view === 'simple') {
                $html = $this->_all_sources_simple_html($result, $type, $filters['value']);
            } elseif ($view === 'profile') {
                $html = $this->_all_sources_profile_html($result);
            } else {
                $html = $this->_all_sources_html($result);
            }
            $this->response->body($html);
        } catch (Helpers_DatabankApi_Exception $e) {
            $this->response->body($this->_api_failed($e));
        } catch (Exception $e) {
            $this->response->body($this->_search_failed());
        }
    }

    /* ================================================================== */
    /*  Internal helpers                                                  */
    /* ================================================================== */

    /**
     * Summary tiles, a chip per source (jump link), then one card per
     * source — sources with records first, in API order — each holding its
     * databank_advanced_results table. Styles: databank_advanced_form.php.
     */
    private function _all_sources_html(array $result)
    {
        $blocks = $result['blocks'];
        if (!$blocks) {
            return '<div class="dbk-alert dbk-alert-info"><i class="fa fa-info-circle"></i>'
                 . '<div>The Databank returned no sources for this search.</div></div>';
        }

        $with_rows = array();
        $without   = array();
        $total     = 0;
        $down      = 0;
        foreach ($blocks as $i => $b) {
            $b['anchor'] = 'dbk-src-' . $i;
            $b['css']    = 'dbk-s-' . (in_array($b['status'], array('ok', 'empty', 'unavailable', 'unsupported'), TRUE) ? $b['status'] : 'empty');
            $total      += count($b['rows']);
            if ($b['status'] === 'unavailable') {
                $down++;
            }
            if ($b['rows']) {
                $with_rows[] = $b;
            } else {
                $without[] = $b;
            }
        }
        $ordered = array_merge($with_rows, $without);

        $tile = function ($value, $label) {
            return '<div class="dbk-stat"><b>' . (int) $value . '</b><small>' . HTML::chars($label) . '</small></div>';
        };
        $html = '<div class="dbk-summary">'
              . $tile($total, $total === 1 ? 'record found' : 'records found')
              . $tile(count($with_rows), count($with_rows) === 1 ? 'source with matches' : 'sources with matches')
              . $tile(count($blocks), 'sources searched')
              . ($down ? $tile($down, 'unavailable') : '')
              . '</div>';

        if ($result['linked']) {
            $html .= '<div class="dbk-linked"><b><i class="fa fa-link"></i> Linked identifiers followed</b><ul>';
            foreach ($result['linked'] as $step) {
                $html .= '<li>' . HTML::chars($step) . '</li>';
            }
            $html .= '</ul></div>';
        }

        $html .= '<div class="dbk-chips">';
        foreach ($ordered as $b) {
            $html .= '<a class="dbk-chip ' . $b['css'] . '" href="#' . $b['anchor'] . '"><span class="dbk-dot"></span>'
                   . HTML::chars($b['label'] !== '' ? $b['label'] : $b['code'])
                   . '<em>' . count($b['rows']) . '</em></a>';
        }
        $html .= '</div>';

        foreach ($ordered as $b) {
            $has   = (bool) $b['rows'];
            $badge = $has ? count($b['rows']) . (count($b['rows']) === 1 ? ' record' : ' records') : ($b['statusLabel'] !== '' ? $b['statusLabel'] : 'No records');
            $note  = $has ? $b['statusLabel'] : ($b['message'] !== '' ? $b['message'] : '');
            $html .= '<div class="dbk-card ' . $b['css'] . ($has ? ' dbk-has-rows' : '') . '" id="' . $b['anchor'] . '">'
                   . '<div class="dbk-card-head"' . ($has ? ' role="button" tabindex="0" aria-expanded="true"' : '') . '>'
                   . '<div class="dbk-ico"><i class="fa ' . self::_source_icon($b) . '"></i></div>'
                   . '<div class="dbk-card-title"><h4>' . HTML::chars($b['label'] !== '' ? $b['label'] : $b['code']) . '</h4>'
                   . ($note !== '' ? '<p>' . HTML::chars($note) . '</p>' : '') . '</div>'
                   . '<span class="dbk-badge">' . HTML::chars($badge) . '</span>'
                   . ($has ? '<i class="fa fa-chevron-down dbk-caret" aria-hidden="true"></i>' : '')
                   . '</div>';
            if ($has) {
                $html .= '<div class="dbk-card-body">'
                       . View::factory('templates/user/databank_advanced_results')
                           ->set('rows', $b['rows'])
                           ->set('columns', $b['columns'])
                           ->set('summary', count($b['rows']) . (count($b['rows']) === 1 ? ' record' : ' records'))
                           ->render()
                       . '</div>';
            }
            $html .= '</div>';
        }
        return $html;
    }

    /**
     * The compact layout used by the Request Subscriber popup on
     * /user/search_person (?view=simple): per source, each record as a
     * label / value list with a "Save Subscriber" button (same hand-off as
     * the local subscriber result: POST to user/upload_against_msisdn in a
     * new tab, see Model_Userrequest::subscriber_external_search_results).
     * Sources without records are listed in one line at the end.
     */
    private function _all_sources_simple_html(array $result, $type, $value)
    {
        $can_save = FALSE;
        try {
            $can_save = Helpers_Utilities::chek_role_access($this->role_id, 9) == 1;
        } catch (Exception $e) {
            $can_save = FALSE;
        }
        // The searched mobile number (10 digits, the format the save page
        // expects) — used when a record carries no mobile number of its own.
        $searched_mobile = '';
        if ($type === 'mobile') {
            try {
                $searched_mobile = substr(Helpers_DatabankApi::normalize_identifier('mobile', $value), 1);
            } catch (Helpers_DatabankApi_Exception $e) {
                $searched_mobile = '';
            }
        }

        $html    = '';
        $empty   = array();
        $down    = array();
        $form_no = 0;
        foreach ($result['blocks'] as $b) {
            $label = $b['label'] !== '' ? $b['label'] : $b['code'];
            if (!$b['rows']) {
                if ($b['status'] === 'unavailable' || $b['status'] === 'unsupported') {
                    $down[] = HTML::chars($label) . ($b['message'] !== '' ? ' <span class="dbk-simple-muted">(' . HTML::chars($b['message']) . ')</span>' : '');
                } else {
                    $empty[] = HTML::chars($label);
                }
                continue;
            }
            $count = count($b['rows']);
            $html .= '<div class="dbk-simple-source">'
                   . '<div class="dbk-simple-head"><i class="fa ' . self::_source_icon($b) . '"></i> '
                   . HTML::chars($label) . '<span>' . $count . ($count === 1 ? ' record' : ' records') . '</span></div>';
            foreach ($b['rows'] as $n => $row) {
                $fields = self::_row_fields($row, $b['columns']);
                $html  .= '<div class="dbk-simple-record"><div class="dbk-simple-no">Record ' . ($n + 1) . '</div><dl>';
                foreach ($fields as $f) {
                    $html .= '<dt>' . HTML::chars($f['label']) . '</dt><dd>';
                    if ($f['image']) {
                        $html .= '<img class="dbk-thumb" src="' . HTML::chars($f['value']) . '" alt="' . HTML::chars($f['label']) . '" title="Click to enlarge">';
                    } else {
                        $html .= HTML::chars($f['value']);
                    }
                    $html .= '</dd>';
                }
                $html .= '</dl>';
                if ($can_save) {
                    $html .= self::_save_subscriber_form($label, $fields, $searched_mobile, ++$form_no);
                }
                $html .= '</div>';
            }
            $html .= '</div>';
        }

        if ($html === '') {
            $html = '<div class="dbk-alert dbk-alert-info"><i class="fa fa-info-circle"></i><div>No records in any Databank source.</div></div>';
        }
        if ($empty) {
            $html .= '<p class="dbk-simple-foot"><i class="fa fa-minus-circle"></i> <b>No records:</b> ' . implode(', ', $empty) . '</p>';
        }
        if ($down) {
            $html .= '<p class="dbk-simple-foot dbk-simple-bad"><i class="fa fa-exclamation-triangle"></i> <b>Not available:</b> ' . implode(', ', $down) . '</p>';
        }
        return $html;
    }

    /**
     * The layout used by the person dashboard's "Databank" panel
     * (?view=profile): a status chip per source, then per source with
     * records a card where each record is a fact sheet — photos on the
     * left, the fields as a label / value grid.
     */
    private function _all_sources_profile_html(array $result)
    {
        $chips = '';
        $cards = '';
        $i     = 0;
        foreach ($result['blocks'] as $b) {
            $label = $b['label'] !== '' ? $b['label'] : $b['code'];
            $css   = 'dbk-s-' . (in_array($b['status'], array('ok', 'empty', 'unavailable', 'unsupported'), TRUE) ? $b['status'] : 'empty');
            $count = count($b['rows']);
            if (!$count) {
                $state  = $b['statusLabel'] !== '' ? $b['statusLabel'] : 'No records';
                $chips .= '<span class="dbk-chip ' . $css . '" title="' . HTML::chars($b['message']) . '"><span class="dbk-dot"></span>'
                        . HTML::chars($label) . '<em>' . HTML::chars($state) . '</em></span>';
                continue;
            }
            $anchor = 'dbk-prof-' . $i++;
            $chips .= '<a class="dbk-chip ' . $css . '" href="#' . $anchor . '"><span class="dbk-dot"></span>'
                    . HTML::chars($label) . '<em>' . $count . '</em></a>';

            $cards .= '<div class="dbk-prof-card" id="' . $anchor . '">'
                    . '<div class="dbk-prof-head"><div class="dbk-ico"><i class="fa ' . self::_source_icon($b) . '"></i></div>'
                    . '<h4>' . HTML::chars($label) . '</h4>'
                    . '<span class="dbk-badge">' . $count . ($count === 1 ? ' record' : ' records') . '</span></div>';
            foreach ($b['rows'] as $n => $row) {
                $photos = '';
                $grid   = '';
                foreach (self::_row_fields($row, $b['columns']) as $f) {
                    if ($f['image']) {
                        $photos .= '<figure><img class="dbk-thumb" src="' . HTML::chars($f['value']) . '" alt="' . HTML::chars($f['label']) . '" title="Click to enlarge">'
                                 . '<figcaption>' . HTML::chars(preg_replace('/\s*\(image\)$/', '', $f['label'])) . '</figcaption></figure>';
                    } else {
                        $grid .= '<div class="dbk-prof-f"><span>' . HTML::chars($f['label']) . '</span><b>' . HTML::chars($f['value']) . '</b></div>';
                    }
                }
                $cards .= '<div class="dbk-prof-rec">'
                        . ($count > 1 ? '<div class="dbk-simple-no">Record ' . ($n + 1) . '</div>' : '')
                        . '<div class="dbk-prof-body">'
                        . ($photos !== '' ? '<div class="dbk-prof-photos">' . $photos . '</div>' : '')
                        . '<div class="dbk-prof-grid">' . $grid . '</div>'
                        . '</div></div>';
            }
            $cards .= '</div>';
        }

        $html = '<div class="dbk-chips">' . $chips . '</div>';
        if ($result['linked']) {
            $html .= '<div class="dbk-linked"><b><i class="fa fa-link"></i> Linked identifiers followed</b><ul>';
            foreach ($result['linked'] as $step) {
                $html .= '<li>' . HTML::chars($step) . '</li>';
            }
            $html .= '</ul></div>';
        }
        $html .= $cards !== ''
            ? $cards
            : '<div class="dbk-alert dbk-alert-info"><i class="fa fa-info-circle"></i><div>No records for this person in any Databank source.</div></div>';
        return $html;
    }

    /** A results row as a list of [label, value, image], in column order, empty values left out. */
    private static function _row_fields(array $row, array $columns)
    {
        $out = array();
        foreach ($columns as $col) {
            if (isset($row[$col['key']]) && $row[$col['key']] !== '') {
                $out[] = array(
                    'label' => $col['label'],
                    'value' => (string) $row[$col['key']],
                    'image' => isset($col['formatter']) && $col['formatter'] === 'image_jpeg',
                );
            }
        }
        return $out;
    }

    /**
     * Hidden form + "Save Subscriber" button for one Databank record. The
     * fields match the local subscriber result's form so
     * user/upload_against_msisdn pre-fills name, CNIC, address and number.
     * requestvalue is digits only (the save page writes it into JS as is);
     * left empty when the number is not known, so the analyst types it.
     */
    private static function _save_subscriber_form($source, array $fields, $searched_mobile, $form_no)
    {
        $name = $cnic = $address = $mobile = '';
        $body = array('SOURCE => ' . $source);
        foreach ($fields as $f) {
            if ($f['image']) {
                continue;
            }
            $l = strtolower($f['label']);
            $v = $f['value'];
            $body[] = strtoupper($f['label']) . ' => ' . $v;
            if ($name === '' && strpos($l, 'name') !== FALSE
                && !preg_match('/father|husband|mother|guardian|spouse|file|folder|district|tehsil|city|station|org|caste|sect/', $l)) {
                $name = $v;
            } elseif ($cnic === '' && preg_match('/cnic|\bnic\b|national|id card/', $l)
                && preg_match('/^\d{5}-?\d{7}-?\d$/', trim($v))) {
                $cnic = preg_replace('/\D/', '', $v);
            } elseif ($address === '' && strpos($l, 'address') !== FALSE) {
                $address = $v;
            } elseif ($mobile === '' && preg_match('/mobile|msisdn|phone|cell|contact/', $l)) {
                try {
                    $mobile = substr(Helpers_DatabankApi::normalize_identifier('mobile', $v), 1);
                } catch (Helpers_DatabankApi_Exception $e) {
                    $mobile = '';  // several numbers or not a mobile: let the analyst type it
                }
            }
        }
        if ($searched_mobile !== '') {
            $mobile = $searched_mobile;
        }
        $json = json_encode(array(
            'name' => $name, 'cnic' => $cnic, 'address' => $address,
            'mobile' => $mobile, 'status' => '', 'bvs_status' => '',
        ));
        $hidden = array(
            'requestid'        => '0',
            'receivedfilepath' => 'No Attached File',
            'receivedbody'     => implode(' , ', $body),
            'requesttype'      => '3',
            'requestvalue'     => preg_replace('/\D/', '', $mobile),
            'receivedbodynew'  => $json,
        );
        $html = '<form class="dbk-simple-save" target="_blank" method="post" id="dbk-save-' . (int) $form_no . '" action="'
              . HTML::chars(URL::base() . 'user/upload_against_msisdn') . '">';
        foreach ($hidden as $k => $v) {
            $html .= '<input type="hidden" name="' . $k . '" value="' . HTML::chars($v) . '">';
        }
        $html .= '<button type="submit" class="btn btn-success btn-sm" title="Save in AIES">'
               . '<i class="fa fa-save"></i> Save Subscriber</button>'
               . ($mobile === '' ? ' <span class="dbk-simple-muted">Mobile number not in this record. Enter it on the save page.</span>' : '')
               . '</form>';
        return $html;
    }

    /** A Font Awesome 4.5 icon that suggests what a source holds. */
    private static function _source_icon(array $b)
    {
        $text = strtolower($b['code'] . ' ' . $b['label']);
        $map  = array(
            'ecp'      => 'fa-users',      'election' => 'fa-users',
            'ctd'      => 'fa-shield',     'fir'      => 'fa-shield',
            'dlms'     => 'fa-car',        'driving'  => 'fa-car',
            'employee' => 'fa-briefcase',  'govt'     => 'fa-briefcase',
            'subscri'  => 'fa-mobile',     'mobile'   => 'fa-mobile',  'sim' => 'fa-mobile',
            'nadra'    => 'fa-user',       'travel'   => 'fa-plane',
        );
        foreach ($map as $needle => $icon) {
            if (strpos($text, $needle) !== FALSE) {
                return $icon;
            }
        }
        return 'fa-database';
    }

    /**
     * Build the search form view from $opts.
     *
     * Expected keys: title, subtitle, breadcrumb, ajax_url, hint, fields[].
     * Each field is { name, label, placeholder } and optionally
     * type (text | select | checkbox) and options (select).
     */
    private function _form_view(array $opts)
    {
        $defaults = array(
            'title'      => 'Advanced Search',
            'subtitle'   => '',
            'breadcrumb' => 'Search',
            'ajax_url'   => '#',
            'hint'       => '',
            'fields'     => array(),
            'prefill'    => array(),
        );
        $opts = array_merge($defaults, $opts);

        $view = View::factory('templates/user/databank_advanced_form');
        foreach ($opts as $k => $v) {
            $view->set($k, $v);
        }
        return $view;
    }

    /**
     * Pull a whitelisted set of POST/GET fields, sanitise them through
     * remove_injection, and return only the non-empty ones.
     */
    private function _collect_filters(array $names)
    {
        $src = array_merge((array) $this->request->post(), (array) $_GET);
        $src = Helpers_Utilities::remove_injection($src);
        $out = array();
        foreach ($names as $n) {
            if (isset($src[$n]) && is_scalar($src[$n]) && trim((string) $src[$n]) !== '') {
                $out[$n] = trim((string) $src[$n]);
            }
        }
        return $out;
    }

    /** The identifier type a free-text value looks like. */
    private static function _detect_type($value)
    {
        foreach (array('cnic', 'mobile', 'name', 'fir') as $type) {
            try {
                Helpers_DatabankApi::normalize_identifier($type, $value);
                return $type;
            } catch (Helpers_DatabankApi_Exception $e) {
                // try the next type
            }
        }
        return '';
    }

    private function _records_per_search()
    {
        return max(1, min(50, (int) Kohana::$config->load('databank')->get('records_per_search', 25)));
    }

    /**
     * Who did what: every DRAMS user shares one Databank API identity, so
     * the Databank's own audit trail cannot tell them apart. The requestId
     * links this line to the Databank's record; no searched value is logged.
     */
    private function _audit($what, $request_id)
    {
        $user = Auth::instance()->get_user();
        Kohana::$log->add(Log::INFO, 'Databank: user :user :what (Databank reference :ref)', array(
            ':user' => $user ? (int) $user->id : 0,
            ':what' => $what,
            ':ref'  => $request_id !== '' ? $request_id : '-',
        ));
    }

    private function _no_filters()
    {
        return '<div class="dbk-alert dbk-alert-info"><i class="fa fa-info-circle"></i>'
             . '<div>Type a value above and click <strong>Search</strong>.</div></div>';
    }

    private function _notice($level, $message)
    {
        $icon = $level === 'danger' ? 'fa-times-circle' : ($level === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle');
        return '<div class="dbk-alert dbk-alert-' . $level . '"><i class="fa ' . $icon . '"></i><div>'
             . HTML::chars($message) . '</div></div>';
    }

    /** A Databank API failure: its (user-safe) message, never "no matching records". */
    private function _api_failed(Helpers_DatabankApi_Exception $e)
    {
        return $this->_notice($e->reason === 'invalid-request' ? 'warning' : 'danger', $e->getMessage());
    }

    private function _search_failed()
    {
        return '<div class="dbk-alert dbk-alert-danger"><i class="fa fa-times-circle"></i><div>Search failed. '
             . 'The Databank API may be unreachable.</div></div>';
    }

    /**
     * Same gate as the sidebar's DRAMS Databank menu: the user's "DRAMS
     * Databank" right. Redirects to access_denied without it.
     */
    private function _require_databank_access()
    {
        try {
            $user = Auth::instance()->get_user();
            if (!Helpers_Databank::has_access($user->id)) {
                $this->redirect('user/access_denied');
                return;
            }
        } catch (Exception $e) {
            $this->redirect('user/access_denied');
            return;
        }
    }

}
