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
 * Visibility: same gate as the sidebar's DRAMS Databank parent
 * treeview (role 34/35 OR user 170/171). _require_databank_access()
 * is called from every action so URLs cannot be hit directly by
 * unauthorised users.
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
        $this->template->content = $this->_form_view(array(
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
            $filters = $this->_collect_filters(array('value', 'type', 'linked'));
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
            $this->response->body($this->_all_sources_html($result));
        } catch (Helpers_DatabankApi_Exception $e) {
            $this->response->body($this->_api_failed($e));
        } catch (Exception $e) {
            $this->response->body($this->_search_failed());
        }
    }

    /* ================================================================== */
    /*  Internal helpers                                                  */
    /* ================================================================== */

    /** One result table per source (databank_advanced_results), with the source's status. */
    private function _all_sources_html(array $result)
    {
        $html  = '<p class="text-muted small" style="margin:0 0 8px;">'
               . ($result['request_id'] !== '' ? 'Databank reference ' . HTML::chars($result['request_id']) . ' · ' : '')
               . 'At most ' . (int) $result['limit'] . ' records per source.</p>';
        if ($result['linked']) {
            $html .= '<div class="callout callout-info" style="padding:6px 12px;"><strong>Linked identifiers followed</strong><ul style="margin:4px 0 0;">';
            foreach ($result['linked'] as $step) {
                $html .= '<li>' . HTML::chars($step) . '</li>';
            }
            $html .= '</ul></div>';
        }
        $level = array('ok' => 'success', 'empty' => 'default', 'unavailable' => 'danger', 'unsupported' => 'warning');
        foreach ($result['blocks'] as $b) {
            $html .= '<div class="box box-' . (isset($level[$b['status']]) ? $level[$b['status']] : 'default') . '" style="margin-bottom:10px;">'
                   . '<div class="box-header with-border"><h3 class="box-title">' . HTML::chars($b['label']) . '</h3>'
                   . '<div class="box-tools pull-right"><span class="text-muted">' . HTML::chars($b['statusLabel'])
                   . ($b['rows'] ? ' (' . count($b['rows']) . ')' : '') . '</span></div></div><div class="box-body">';
            if ($b['rows']) {
                $html .= View::factory('templates/user/databank_advanced_results')
                    ->set('rows', $b['rows'])
                    ->set('columns', $b['columns'])
                    ->set('summary', count($b['rows']) . ' record(s)')
                    ->render();
            } else {
                $html .= '<span class="text-muted">' . HTML::chars($b['message'] !== '' ? $b['message'] : $b['statusLabel']) . '</span>';
            }
            $html .= '</div></div>';
        }
        return $html;
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
        return '<div class="text-muted" style="padding:8px;">'
             . 'Fill at least one field above and click <strong>Search</strong>.</div>';
    }

    private function _notice($level, $message)
    {
        return '<div class="alert alert-' . $level . '" style="margin:8px;">' . HTML::chars($message) . '</div>';
    }

    /** A Databank API failure: its (user-safe) message, never "no matching records". */
    private function _api_failed(Helpers_DatabankApi_Exception $e)
    {
        return $this->_notice($e->reason === 'invalid-request' ? 'warning' : 'danger', $e->getMessage());
    }

    private function _search_failed()
    {
        return '<div class="alert alert-danger" style="margin:8px;">Search failed. '
             . 'The Databank API may be unreachable.</div>';
    }

    /**
     * Same gate as the sidebar's DRAMS Databank section. Redirects to
     * access_denied if the current user is not entitled.
     */
    private function _require_databank_access()
    {
        try {
            $user    = Auth::instance()->get_user();
            $role_id = Helpers_Utilities::get_user_role_id($user->id);
            $allowed = Helpers_Utilities::chek_role_array_access($role_id, array(34, 35)) == 1
                       || (int) $user->id === 170
                       || (int) $user->id === 171;
            if (!$allowed) {
                $this->redirect('user/access_denied');
                return;
            }
        } catch (Exception $e) {
            $this->redirect('user/access_denied');
            return;
        }
    }

}
