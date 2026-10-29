<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * DRAMS Databank — searches of the external databases through the
 * Databank integration API (Helpers_DatabankApi, config/databank.php).
 * DRAMS no longer queries those databases for these pages; the Databank
 * decides what the DRAMS API user may read and see.
 *
 * Per-source pages (a form + an AJAX result endpoint each):
 *   /databank/subscriber_advanced     — Mobile Subscription (local + foreign)
 *   /databank/ecp_advanced            — Election Commission (ECP)
 *   /databank/ecp_family              — household of one ECP record
 *   /databank/ctd_kpk_advanced        — CTD KPK FIR records
 *   /databank/dlms_advanced           — driving licences
 *   /databank/govt_employee_advanced  — government employees
 *   /databank/vehicle_advanced        — vehicle / excise registrations
 * All sources at once:
 *   /databank/all_sources             — one identifier in every source,
 *                                       optionally following linked ones
 * Advanced Search (an investigation session kept by the Databank):
 *   /databank/advanced_search         — the page; its JS calls
 *   /databank/advanced_api            — JSON, whitelisted calls only
 *   /databank/advanced_export         — CSV download
 *
 * The API searches by ONE identifier per request (mobile number, CNIC,
 * name or FIR number). The other filled-in fields of a form narrow the
 * records it returns (Helpers_Databank::refine_rows), so all filters
 * still AND together; fields the API can neither search nor return are
 * shown disabled.
 *
 * Visibility: same gate as the sidebar's DRAMS Databank parent
 * treeview (role 34/35 OR user 170/171). _require_databank_access()
 * is called from every action so URLs cannot be hit directly by
 * unauthorised users.
 */
class Controller_Databank extends Controller_Working
{
    /** Identifier types in the order one is picked as THE value searched. */
    private static $search_order = array('fir', 'mobile', 'cnic', 'name');

    /** Session key: Advanced Search sessions this DRAMS user started. */
    const SESSION_SEARCHES = 'databank_search_ids';

    public function __Construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->request  = $request;
        $this->response = $response;
    }

    public function action_index()
    {
        $this->redirect('databank/subscriber_advanced');
    }

    /* ================================================================== */
    /*  Per-source searches                                               */
    /* ================================================================== */

    public function action_subscriber_advanced()        { $this->_form_page('subscriber'); }
    public function action_subscriber_advanced_results() { $this->_results_page('subscriber'); }

    public function action_ecp_advanced()                { $this->_form_page('ecp'); }
    public function action_ecp_advanced_results()        { $this->_results_page('ecp'); }

    public function action_ctd_kpk_advanced()            { $this->_form_page('ctd_kpk'); }
    public function action_ctd_kpk_advanced_results()    { $this->_results_page('ctd_kpk'); }

    public function action_dlms_advanced()               { $this->_form_page('dlms'); }
    public function action_dlms_advanced_results()       { $this->_results_page('dlms'); }

    public function action_govt_employee_advanced()         { $this->_form_page('govt_employee'); }
    public function action_govt_employee_advanced_results() { $this->_results_page('govt_employee'); }

    public function action_vehicle_advanced()            { $this->_form_page('vehicle'); }
    public function action_vehicle_advanced_results()    { $this->_results_page('vehicle'); }

    /**
     * ECP household page, reached from the "Family" button of an ECP
     * result. Uses POST integration/ecp/family (added to the Databank
     * together with this integration); a Databank without that endpoint
     * answers 404, shown as "not available yet".
     *
     * URL: /databank/ecp_family?ecp_id=12345
     */
    public function action_ecp_family()
    {
        $this->_require_databank_access();
        $ecp_id  = isset($_GET['ecp_id']) && ctype_digit((string) $_GET['ecp_id']) ? (int) $_GET['ecp_id'] : 0;
        $seed    = NULL;
        $family  = array();
        $message = '';
        if ($ecp_id > 0) {
            try {
                $result = Helpers_Databank::ecp_family($ecp_id);
                $seed   = $result['seed'];
                $family = $result['family'];
                $this->_audit('ecp_family', '');
            } catch (Helpers_DatabankApi_Exception $e) {
                $message = $e->reason === 'not-found'
                    ? 'The ECP family view needs the Databank API update that adds integration/ecp/family; it is not available on the Databank server yet.'
                    : $e->getMessage();
            } catch (Exception $e) {
                $message = 'The ECP family could not be loaded.';
            }
        }
        $this->template->content = View::factory('templates/user/databank_ecp_family')
            ->set('seed',    $seed)
            ->set('family',  $family)
            ->set('message', $message);
    }

    /* ================================================================== */
    /*  All sources                                                       */
    /* ================================================================== */

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
    /*  Advanced Search                                                   */
    /* ================================================================== */

    public function action_advanced_search()
    {
        $this->_require_databank_access();
        $this->template->content = View::factory('templates/user/databank_advanced_search')
            ->set('api_url',    URL::site('databank/advanced_api', TRUE))
            ->set('export_url', URL::site('databank/advanced_export', TRUE));
    }

    /**
     * JSON proxy for the Advanced Search page: POST op=<call>,
     * payload=<JSON body>. Only Helpers_DatabankApi::$advanced_ops calls
     * are made, and only on searches this DRAMS user started (another
     * DRAMS user cannot read them through the shared API identity).
     */
    public function action_advanced_api()
    {
        $this->auto_render = FALSE;
        $this->_require_databank_access();
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        try {
            $op    = (string) $this->request->post('op');
            $input = json_decode((string) $this->request->post('payload'), TRUE, 8);
            if (!is_array($input)) {
                $input = array();
            }
            if (!isset(Helpers_DatabankApi::$advanced_ops[$op])) {
                throw new Helpers_DatabankApi_Exception('Unknown Advanced Search action.', 'invalid-request');
            }
            $search_id = '';
            if ($op !== 'start') {
                $search_id = isset($input['searchId']) ? (string) $input['searchId'] : '';
                if (!in_array($search_id, $this->_my_searches(), TRUE)) {
                    throw new Helpers_DatabankApi_Exception('This search is not one of yours or has ended. Start a new search.', 'not-found');
                }
            }
            // Source searches run inside these calls (see config advanced_timeout).
            set_time_limit((int) Kohana::$config->load('databank')->get('advanced_timeout', 150) + 30);

            $json = Helpers_DatabankApi::advanced($op, $input, $search_id);
            if ($op === 'start' && isset($json['searchId']) && is_string($json['searchId'])) {
                $this->_remember_search($json['searchId']);
            }
            $this->_audit('advanced ' . $op, isset($json['requestId']) ? (string) $json['requestId'] : '');
            $this->response->body(self::_json($json));
        } catch (Helpers_DatabankApi_Exception $e) {
            $this->response->body(self::_json(array(
                'ok' => FALSE, 'error' => $e->getMessage(), 'code' => $e->reason, 'requestId' => $e->request_id,
            )));
        } catch (Exception $e) {
            $this->response->body(self::_json(array('ok' => FALSE, 'error' => 'The request could not be completed.', 'code' => 'error')));
        }
    }

    /** CSV export of one of this user's Advanced Search sessions (POST searchId, kind, numbers?). */
    public function action_advanced_export()
    {
        $this->auto_render = FALSE;
        $this->_require_databank_access();
        try {
            $search_id = (string) $this->request->post('searchId');
            if (!in_array($search_id, $this->_my_searches(), TRUE)) {
                throw new Helpers_DatabankApi_Exception('This search is not one of yours or has ended. Start a new search.', 'not-found');
            }
            $numbers = array_values(array_filter(array_map('trim', explode(',', (string) $this->request->post('numbers')))));
            set_time_limit((int) Kohana::$config->load('databank')->get('advanced_timeout', 150) + 30);
            $csv = Helpers_DatabankApi::export($search_id, (string) $this->request->post('kind'), $numbers ?: NULL);
            $this->_audit('advanced export ' . (string) $this->request->post('kind'), '');
            $this->response->headers('Content-Type', 'text/csv; charset=utf-8');
            $this->response->headers('Content-Disposition', 'attachment; filename="' . $csv['filename'] . '"');
            $this->response->body($csv['body']);
        } catch (Helpers_DatabankApi_Exception $e) {
            $this->response->headers('Content-Type', 'text/plain; charset=utf-8');
            $this->response->status(400);
            $this->response->body($e->getMessage());
        }
    }

    /* ================================================================== */
    /*  Internal helpers                                                  */
    /* ================================================================== */

    /**
     * Form fields, result columns and API mapping of each per-source page.
     *
     * A field is { name, label, placeholder } plus its role:
     *   search      => mobile | cnic | name | fir — the API can search by it
     *   refine      => row keys — narrows the returned records (match:
     *                  contains (default), exact or cnic)
     *   unsupported => TRUE — the API can neither search nor return it
     */
    private function _page($page)
    {
        $cnic = array('search' => 'cnic', 'match' => 'cnic');
        switch ($page) {
            case 'subscriber':
                return array(
                    'title'  => 'Subscriber Search',
                    'helper' => 'search_subscriber_unified',
                    'noun'   => 'result(s)',
                    'fields' => array(
                        array('name' => 'msisdn',   'label' => 'MSISDN',   'placeholder' => '03100910677 / 3100910677 / +923100910677', 'search' => 'mobile'),
                        array('name' => 'cnic',     'label' => 'CNIC',     'placeholder' => '13-digit CNIC, with or without dashes', 'refine' => array('cnic')) + $cnic,
                        array('name' => 'imsi',     'label' => 'IMSI',     'unsupported' => TRUE),
                        array('name' => 'name',     'label' => 'Name',     'placeholder' => 'Subscriber name (mobile + foreigner)', 'search' => 'name', 'refine' => array('name')),
                        array('name' => 'father',   'label' => 'Father',   'unsupported' => TRUE),
                        array('name' => 'address',  'label' => 'Address',  'placeholder' => 'Address (narrows the results)', 'refine' => array('address')),
                        array('name' => 'district', 'label' => 'District', 'placeholder' => 'District / Tehsil (narrows the results)', 'refine' => array('master_pak_district', 'master_pak_tehsil')),
                    ),
                    'columns' => array(
                        array('key' => 'source',              'label' => 'Source'),
                        array('key' => 'msisdn',              'label' => 'MSISDN'),
                        array('key' => 'cnic',                'label' => 'CNIC'),
                        array('key' => 'name',                'label' => 'Name'),
                        array('key' => 'address',             'label' => 'Address'),
                        array('key' => 'master_pak_district', 'label' => 'District'),
                        array('key' => 'operator',            'label' => 'Operator'),
                        array('key' => 'status',              'label' => 'Status'),
                        array('key' => 'bvs_status',          'label' => 'BVS'),
                    ),
                    'notes' => array('The Databank API does not provide IMSI or father\'s name for subscribers, and searches '
                                   . 'foreign accounts by 13-digit CNIC only.'),
                );
            case 'ecp':
                return array(
                    'title'  => 'Election Commission of Pakistan',
                    'helper' => 'search_ecp',
                    'noun'   => 'ECP record(s)',
                    'fields' => array(
                        array('name' => 'cnic',     'label' => 'CNIC',         'placeholder' => '13-digit CNIC (no dashes)', 'refine' => array('cnic')) + $cnic,
                        array('name' => 'name',     'label' => 'Name',         'placeholder' => 'Person name (partial OK)', 'search' => 'name', 'refine' => array('name_text')),
                        array('name' => 'father',   'label' => 'Father',       'placeholder' => "Father's name (narrows the results)", 'refine' => array('father_text')),
                        array('name' => 'address',  'label' => 'Address',      'placeholder' => 'Address text (narrows the results)', 'refine' => array('address_text')),
                        array('name' => 'phone',    'label' => 'Phone Number', 'placeholder' => 'Linked mobile number', 'search' => 'mobile'),
                        array('name' => 'district', 'label' => 'UC / Block',   'placeholder' => 'UC / Block code (narrows the results)', 'refine' => array('uc_block_code')),
                    ),
                    'columns' => array(
                        // ECP ID: the number in the record's Databank reference.
                        array('key' => 'id',             'label' => 'ECP ID'),
                        array('key' => 'cnic',           'label' => 'CNIC'),
                        array('key' => 'name_text',      'label' => 'Name'),
                        array('key' => 'father_text',    'label' => 'Father'),
                        array('key' => 'address_text',   'label' => 'Address'),
                        array('key' => 'age',            'label' => 'Age'),
                        array('key' => 'gender',         'label' => 'Gender'),
                        array('key' => 'uc_block_code',  'label' => 'UC/Block'),
                        array('key' => 'linked_numbers', 'label' => 'Phones'),
                        array('key' => 'address_image_base64', 'label' => 'Addr. Image', 'formatter' => 'image_jpeg'),
                    ),
                    'notes' => array('A phone number is searched by the CNIC it is registered to when the Databank knows it, '
                                   . 'otherwise by the numbers listed on ECP records. ECP name searches can take too long on '
                                   . 'the Databank server; prefer a CNIC or phone number.'),
                );
            case 'ctd_kpk':
                return array(
                    'title'  => 'CTD FIR Records',
                    'helper' => 'search_ctd_kpk',
                    'noun'   => 'CTD KPK record(s)',
                    'fields' => array(
                        array('name' => 'cnic',     'label' => 'CNIC',         'placeholder' => '13-digit CNIC, with or without dashes', 'refine' => array('CNIC')) + $cnic,
                        array('name' => 'name',     'label' => 'Name',         'placeholder' => 'Person name (partial OK)', 'search' => 'name', 'refine' => array('Name')),
                        array('name' => 'father',   'label' => 'Father',       'placeholder' => "Father's name (narrows the results)", 'refine' => array('FatherName')),
                        array('name' => 'district', 'label' => 'District',     'unsupported' => TRUE),
                        array('name' => 'fir',      'label' => 'F.I.R / M.S.', 'placeholder' => 'Exact FIR number (Schedule IV or terrorism)', 'search' => 'fir'),
                    ),
                    'columns' => array(
                        array('key' => 'CNIC',             'label' => 'CNIC'),
                        array('key' => 'Name',             'label' => 'Name'),
                        array('key' => 'FatherName',       'label' => 'Father'),
                        array('key' => 'Alias',            'label' => 'Alias'),
                        array('key' => 'Tribe',            'label' => 'Tribe'),
                        array('key' => 'BirthPlace',       'label' => 'Birth Place'),
                        array('key' => 'LatestFirRefNo',   'label' => 'Latest FIR No'),
                        array('key' => 'LatestFirRefDate', 'label' => 'Latest FIR Date'),
                    ),
                    'notes' => array('The Databank API does not provide permanent / current district and tehsil, so they '
                                   . 'are not shown and cannot be searched.'),
                );
            case 'dlms':
                return array(
                    'title'  => 'Driving License Records',
                    'helper' => 'search_dlms',
                    'noun'   => 'DLMS record(s)',
                    'fields' => array(
                        array('name' => 'cnic',       'label' => 'CNIC',           'placeholder' => '13-digit CNIC, with or without dashes', 'refine' => array('CNIC')) + $cnic,
                        array('name' => 'name',       'label' => 'Name',           'placeholder' => 'First / Middle / Last (partial OK)', 'search' => 'name', 'refine' => array('Name')),
                        array('name' => 'father',     'label' => 'Father',         'placeholder' => "Father's name (narrows the results)", 'refine' => array('FatherName')),
                        array('name' => 'license_no', 'label' => 'Licence Number', 'placeholder' => 'Exact licence number (narrows the results)', 'refine' => array('LicenseNo'), 'match' => 'exact'),
                    ),
                    'columns' => array(
                        array('key' => 'CNIC',       'label' => 'CNIC'),
                        array('key' => 'Name',       'label' => 'Name'),
                        array('key' => 'FatherName', 'label' => 'Father'),
                        array('key' => 'DOB',        'label' => 'DOB'),
                        array('key' => 'Gender',     'label' => 'Gender'),
                        array('key' => 'LicenseNo',  'label' => 'Licence No'),
                        array('key' => 'IssueDate',  'label' => 'Issued'),
                        array('key' => 'ExpiryDate', 'label' => 'Expiry'),
                    ),
                    'notes' => array('The Databank API returns the full name and father\'s name, not their first / middle / '
                                   . 'last parts, and cannot search by licence number alone.'),
                );
            case 'govt_employee':
                return array(
                    'title'  => 'Government Employee Records',
                    'helper' => 'search_govt_emp',
                    'noun'   => 'employee record(s)',
                    'fields' => array(
                        array('name' => 'cnic',     'label' => 'CNIC / National ID', 'placeholder' => '13-digit national ID', 'refine' => array('national_id')) + $cnic,
                        array('name' => 'name',     'label' => 'Name',               'placeholder' => 'First or last name (partial OK)', 'search' => 'name', 'refine' => array('name')),
                        array('name' => 'father',   'label' => 'Father / Husband',   'placeholder' => 'Father / husband name (narrows the results)', 'refine' => array('father_husband_name')),
                        array('name' => 'pers_no',  'label' => 'Personnel Number',   'placeholder' => 'Exact personnel number (narrows the results)', 'refine' => array('pers_no'), 'match' => 'exact'),
                        array('name' => 'org_unit', 'label' => 'Org Unit',           'placeholder' => 'Org unit / department (narrows the results)', 'refine' => array('org_unit_short_text')),
                        array('name' => 'job',      'label' => 'Job / Title',        'placeholder' => 'Position / job title (narrows the results)', 'refine' => array('job_title')),
                    ),
                    'columns' => array(
                        array('key' => 'national_id',         'label' => 'CNIC'),
                        array('key' => 'name',                'label' => 'Name'),
                        array('key' => 'father_husband_name', 'label' => 'Father / Husband'),
                        array('key' => 'pers_no',             'label' => 'Pers. No.'),
                        array('key' => 'job_title',           'label' => 'Job Title'),
                        array('key' => 'org_unit_short_text', 'label' => 'Org Unit'),
                        array('key' => 'personnel_area',      'label' => 'Personnel Area'),
                    ),
                    'notes' => array('The Databank API returns the full name (not first / last separately) and the '
                                   . 'organisation unit text, not its numeric code.'),
                );
            case 'vehicle':
                return array(
                    'title'  => 'Vehicle Registration Records',
                    'helper' => 'search_vehicle',
                    'noun'   => 'vehicle record(s)',
                    'fields' => array(
                        array('name' => 'cnic',   'label' => 'Owner CNIC',    'placeholder' => '13-digit CNIC, with or without dashes', 'search' => 'cnic'),
                        array('name' => 'mobile', 'label' => 'Mobile Number', 'placeholder' => 'Searched by the CNIC it is registered to', 'search' => 'mobile'),
                    ),
                    'columns' => NULL, // from the records (Helpers_Databank::records_table)
                    'notes'   => array('Vehicle records are searched by the owner\'s CNIC (a mobile number by the CNIC it is '
                                     . 'registered to). Registration-number searches are available in Advanced Search.'),
                );
        }
        throw new Kohana_Exception('Unknown Databank page :page', array(':page' => $page));
    }

    /** Render a per-source search page. */
    private function _form_page($page)
    {
        $this->_require_databank_access();
        $spec = $this->_page($page);

        $by = array();
        $narrow = array();
        foreach ($spec['fields'] as $f) {
            if (!empty($f['search'])) {
                $by[] = $f['label'];
            } elseif (!empty($f['refine'])) {
                $narrow[] = $f['label'];
            }
        }
        $hint = 'The Databank API searches by ' . self::_or_list($by) . ' (at most ' . $this->_records_per_search()
              . ' records per search)' . ($narrow ? '; ' . implode(', ', $narrow) . ' narrow those records' : '') . '.';

        $this->template->content = $this->_form_view(array(
            'title'      => $spec['title'],
            'breadcrumb' => $spec['title'],
            'ajax_url'   => URL::site('databank/' . $page . '_advanced_results', TRUE),
            'hint'       => $hint,
            'fields'     => $spec['fields'],
        ));
    }

    /** Answer a per-source page's AJAX search with the results fragment. */
    private function _results_page($page)
    {
        $this->auto_render = FALSE;
        $this->_require_databank_access();
        try {
            $this->response->body($this->_results($this->_page($page), $page));
        } catch (Helpers_DatabankApi_Exception $e) {
            $this->response->body($this->_api_failed($e));
        } catch (Exception $e) {
            $this->response->body($this->_search_failed());
        }
    }

    private function _results(array $spec, $page)
    {
        $names = array();
        foreach ($spec['fields'] as $f) {
            $names[] = $f['name'];
        }
        $filters = $this->_collect_filters($names);
        if (empty($filters)) {
            return $this->_no_filters();
        }

        $unsupported = array();
        foreach ($spec['fields'] as $f) {
            if (!empty($f['unsupported']) && isset($filters[$f['name']])) {
                $unsupported[] = $f['label'];
            }
        }
        if ($unsupported) {
            return $this->_notice('warning', implode(', ', $unsupported) . ' cannot be searched through the Databank API. '
                . 'Clear ' . (count($unsupported) > 1 ? 'those fields' : 'that field') . ' and search again.');
        }

        // The API searches by one identifier; every other filled field
        // narrows what it returns.
        $primary = NULL;
        foreach (self::$search_order as $type) {
            foreach ($spec['fields'] as $f) {
                if ($primary === NULL && isset($f['search']) && $f['search'] === $type && isset($filters[$f['name']])) {
                    $primary = $f;
                }
            }
        }
        if ($primary === NULL) {
            $by = array();
            foreach ($spec['fields'] as $f) {
                if (!empty($f['search'])) {
                    $by[] = $f['label'];
                }
            }
            return $this->_notice('warning', 'Fill in ' . self::_or_list($by) . ' as well: the Databank API searches by '
                . (count($by) > 1 ? 'one of these' : 'it') . ', and the other fields only narrow the records it finds.');
        }

        $conditions = array();
        foreach ($spec['fields'] as $f) {
            if ($f['name'] === $primary['name'] || !isset($filters[$f['name']])) {
                continue;
            }
            if (!empty($f['search'])) {
                if (empty($f['refine'])) {
                    return $this->_notice('warning', 'Search by ' . $primary['label'] . ' or by ' . $f['label']
                        . ', not both: the Databank API searches by one of them at a time.');
                }
                // Same validation as when it is the value searched.
                Helpers_DatabankApi::normalize_identifier($f['search'], $filters[$f['name']]);
            }
            if (!empty($f['refine'])) {
                $conditions[] = array('field' => $f['label'], 'keys' => $f['refine'],
                                      'value' => $filters[$f['name']], 'match' => isset($f['match']) ? $f['match'] : 'contains');
            }
        }

        $result = call_user_func(array('Helpers_Databank', $spec['helper']), $primary['search'], $filters[$primary['name']]);
        $this->_audit($page . ' ' . $primary['search'], $result['request_id']);
        $returned = count($result['rows']);
        $refined  = Helpers_Databank::refine_rows($result['rows'], $conditions);
        if ($refined['unavailable']) {
            return $this->_notice('warning', implode(', ', $refined['unavailable']) . ' could not be applied: the Databank API '
                . 'returned no such values for the ' . $returned . ' record(s) found.');
        }

        $notes = array();
        if ($conditions) {
            $labels = array();
            foreach ($conditions as $c) {
                $labels[] = $c['field'];
            }
            $notes[] = 'Searched the Databank by ' . $primary['label'] . '; ' . implode(', ', $labels)
                     . ' narrowed the ' . $returned . ' record(s) it returned.';
        }
        if ($returned >= $result['limit']) {
            $notes[] = 'The Databank API returns at most ' . $result['limit'] . ' records per search, so more may exist. '
                     . 'Add a CNIC or a fuller name to narrow the search.';
        }
        if ($primary['search'] === 'name') {
            $notes[] = 'A Databank name search also matches other text on a record (for example the father\'s name or address).';
        }
        $notes = array_merge($notes, $spec['notes']);

        if (empty($refined['rows'])) {
            return $this->_no_results($notes);
        }

        $view = View::factory('templates/user/databank_advanced_results')
            ->set('rows', $refined['rows'])
            ->set('columns', $spec['columns'] !== NULL ? $spec['columns'] : $result['columns'])
            ->set('summary', count($refined['rows']) . ' ' . $spec['noun']
                           . ($result['request_id'] !== '' ? ' · Databank reference ' . $result['request_id'] : ''))
            ->set('notes', $notes);
        if ($page === 'ecp') {
            // Per-row "Family" action — opens the household page in a new tab.
            $family_url_base = URL::site('databank/ecp_family');
            $view->set('row_actions', function ($r) use ($family_url_base) {
                $id = (is_array($r) && isset($r['id']) && ctype_digit((string) $r['id'])) ? (int) $r['id'] : 0;
                if ($id <= 0) {
                    return '<span class="text-muted">&mdash;</span>';
                }
                return '<a href="' . HTML::chars($family_url_base . '?ecp_id=' . $id) . '" target="_blank"'
                     . ' class="btn btn-xs btn-info" title="View family members">'
                     . '<i class="fa fa-sitemap"></i> Family</a>';
            });
        }
        return $view->render();
    }

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
     * Build a generic advanced-search form view from $opts. Single
     * call site so all host pages stay one-liners and the form looks
     * identical across databases.
     *
     * Expected keys: title, subtitle, breadcrumb, ajax_url, hint, fields[].
     * Each field is { name, label, placeholder } and optionally
     * type (text | select | checkbox), options (select) or unsupported.
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
     * remove_injection, and return only the non-empty ones (so the
     * helpers' "ignore empty filters" rule kicks in).
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

    /** Advanced Search sessions started by the current DRAMS user (most recent last). */
    private function _my_searches()
    {
        $ids = Session::instance()->get(self::SESSION_SEARCHES, array());
        return is_array($ids) ? $ids : array();
    }

    private function _remember_search($search_id)
    {
        $ids   = array_values(array_diff($this->_my_searches(), array($search_id)));
        $ids[] = $search_id;
        // The Databank expires sessions after 2 hours; keep the recent ones.
        Session::instance()->set(self::SESSION_SEARCHES, array_slice($ids, -20));
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

    private static function _json(array $data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
                                | JSON_PARTIAL_OUTPUT_ON_ERROR);
    }

    private static function _or_list(array $labels)
    {
        if (count($labels) < 2) {
            return implode('', $labels);
        }
        $last = array_pop($labels);
        return implode(', ', $labels) . ' or ' . $last;
    }

    private function _no_filters()
    {
        return '<div class="text-muted" style="padding:8px;">'
             . 'Fill at least one field above and click <strong>Search</strong>.</div>';
    }

    private function _no_results(array $notes = array())
    {
        $html = '<div class="text-info" style="padding:8px;">'
              . '<i class="fa fa-info-circle"></i> No matching records.</div>';
        foreach ($notes as $n) {
            $html .= '<p class="text-muted small" style="margin:0 8px 4px;">' . HTML::chars($n) . '</p>';
        }
        return $html;
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
