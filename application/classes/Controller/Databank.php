<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * DRAMS Databank — self-contained advanced search across the external
 * databases this app reads (config/database.php).
 *
 * Each menu entry is an "advanced filter" page: a multi-field form, an
 * AJAX result endpoint, and a result table specific to that database's
 * schema. The pages do NOT delegate to /persons/ext_db_* — Databank is
 * a standalone search facility, intentionally separated from the
 * person-dashboard tabs that are scoped by person_id.
 *
 * Pages (one per database):
 *   /databank/subscriber_advanced     — unified Mobile + Foreigner
 *   /databank/ecp_advanced            — unified ECP CNIC + Address + Phone
 *   /databank/ctd_kpk_advanced        — KPK CTD person profile
 *   /databank/dlms_advanced           — DLMS driving licences
 *   /databank/govt_employee_advanced  — government employee data
 *
 * Each page hits its own AJAX endpoint of the form
 *   /databank/<page>_results
 * which returns an HTML fragment rendered into the results panel by
 * the inline JS in the host view.
 *
 * Backend lives in Helpers_Databank — this controller only handles
 * routing, access control, and view binding.
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
        $this->redirect('databank/subscriber_advanced');
    }

    /* ================================================================== */
    /*  Subscriber Advanced Search (unified Mobile + Foreigner)           */
    /* ================================================================== */

    public function action_subscriber_advanced()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_form_view(array(
            'title'      => 'Subscriber Advanced Search',
            'subtitle'   => 'Mobile + Afghan accounts (auto-detected)',
            'breadcrumb' => 'Subscriber Advanced Search',
            'ajax_url'   => URL::site('databank/subscriber_advanced_results', TRUE),
            'fields'     => array(
                array('name' => 'msisdn',   'label' => 'MSISDN',   'placeholder' => '03100910677 / 3100910677 / +923100910677'),
                array('name' => 'cnic',     'label' => 'CNIC',     'placeholder' => 'Pakistani 13-digit OR Afghan ID'),
                array('name' => 'imsi',     'label' => 'IMSI',     'placeholder' => '15-digit IMSI (mobile only)'),
                array('name' => 'name',     'label' => 'Name',     'placeholder' => 'Master name (foreigner only)'),
                array('name' => 'father',   'label' => 'Father',   'placeholder' => "Father's name (foreigner only)"),
                array('name' => 'address',  'label' => 'Address',  'placeholder' => 'Site address (foreigner only)'),
                array('name' => 'district', 'label' => 'District', 'placeholder' => 'District / Tehsil (foreigner only)'),
            ),
        ));
    }

    public function action_subscriber_advanced_results()
    {
        $this->auto_render = FALSE;
        $this->_require_databank_access();
        try {
            $filters = $this->_collect_filters(array('msisdn','cnic','imsi','name','father','address','district'));
            if (empty($filters)) {
                $this->response->body($this->_no_filters());
                return;
            }
            $rows = Helpers_Databank::search_subscriber_unified($filters, 200);
            if (empty($rows)) {
                $this->response->body($this->_no_results());
                return;
            }
            $this->response->body(View::factory('templates/user/databank_advanced_results')
                ->set('rows', $rows)
                ->set('columns', array(
                    array('key' => 'source',     'label' => 'Source'),
                    array('key' => 'msisdn',     'label' => 'MSISDN'),
                    array('key' => 'cnic',       'label' => 'CNIC',
                                                 'fallback_keys' => array('foreign_cnic', 'master_acc_number')),
                    array('key' => 'imsi',       'label' => 'IMSI'),
                    array('key' => 'subscriber_name', 'label' => 'Name',
                                                 'fallback_keys' => array('master_name')),
                    array('key' => 'father_name','label' => 'Father'),
                    array('key' => 'site_address','label' => 'Address'),
                    array('key' => 'master_pak_district','label' => 'District'),
                ))
                ->set('summary', count($rows) . ' result(s)')
                ->render());
        } catch (Exception $e) {
            $this->response->body($this->_search_failed());
        }
    }

    /* ================================================================== */
    /*  ECP Advanced Search (unified CNIC + Address + Phone)              */
    /* ================================================================== */

    public function action_ecp_advanced()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_form_view(array(
            'title'      => 'ECP Advanced Search',
            'subtitle'   => 'Search the electoral roll (ecp_persons)',
            'breadcrumb' => 'ECP Advanced Search',
            'ajax_url'   => URL::site('databank/ecp_advanced_results', TRUE),
            'fields'     => array(
                array('name' => 'cnic',     'label' => 'CNIC',         'placeholder' => '13-digit CNIC (no dashes)'),
                array('name' => 'name',     'label' => 'Name',         'placeholder' => 'Person name (partial OK)'),
                array('name' => 'father',   'label' => 'Father',       'placeholder' => "Father's name (partial OK)"),
                array('name' => 'address',  'label' => 'Address',      'placeholder' => 'Address text (partial OK)'),
                array('name' => 'phone',    'label' => 'Phone Number', 'placeholder' => 'Linked mobile number'),
                array('name' => 'district', 'label' => 'UC / Block',   'placeholder' => 'UC / Block code (partial OK)'),
            ),
        ));
    }

    public function action_ecp_advanced_results()
    {
        $this->auto_render = FALSE;
        $this->_require_databank_access();
        try {
            $filters = $this->_collect_filters(array('cnic','name','father','address','phone','district'));
            if (empty($filters)) {
                $this->response->body($this->_no_filters());
                return;
            }
            $rows = Helpers_Databank::search_ecp($filters, 200);
            if (empty($rows)) {
                $this->response->body($this->_no_results());
                return;
            }
            $this->response->body(View::factory('templates/user/databank_advanced_results')
                ->set('rows', $rows)
                ->set('columns', array(
                    array('key' => 'cnic',          'label' => 'CNIC'),
                    array('key' => 'name_text',     'label' => 'Name'),
                    array('key' => 'father_text',   'label' => 'Father'),
                    array('key' => 'address_text',  'label' => 'Address'),
                    array('key' => 'age',           'label' => 'Age'),
                    array('key' => 'gender',        'label' => 'Gender'),
                    array('key' => 'uc_block_code', 'label' => 'UC/Block'),
                    array('key' => 'linked_numbers','label' => 'Phones'),
                    array('key' => 'address_image_base64', 'label' => 'Addr. Image', 'formatter' => 'image_jpeg'),
                ))
                ->set('summary', count($rows) . ' ECP record(s)')
                ->render());
        } catch (Exception $e) {
            $this->response->body($this->_search_failed());
        }
    }

    /* ================================================================== */
    /*  CTD KPK Advanced Search                                           */
    /* ================================================================== */

    public function action_ctd_kpk_advanced()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_form_view(array(
            'title'      => 'CTD KPK Advanced Search',
            'subtitle'   => 'KPK CTD person profile (dct_person_profile)',
            'breadcrumb' => 'CTD KPK Advanced Search',
            'ajax_url'   => URL::site('databank/ctd_kpk_advanced_results', TRUE),
            'fields'     => array(
                array('name' => 'cnic',     'label' => 'CNIC',     'placeholder' => '13-digit CNIC, with or without dashes'),
                array('name' => 'name',     'label' => 'Name',     'placeholder' => 'Person name (partial OK)'),
                array('name' => 'father',   'label' => 'Father',   'placeholder' => "Father's name (partial OK)"),
                array('name' => 'district', 'label' => 'District', 'placeholder' => 'Permanent or current district id (partial OK)'),
            ),
        ));
    }

    public function action_ctd_kpk_advanced_results()
    {
        $this->auto_render = FALSE;
        $this->_require_databank_access();
        try {
            $filters = $this->_collect_filters(array('cnic','name','father','district'));
            if (empty($filters)) {
                $this->response->body($this->_no_filters());
                return;
            }
            $rows = Helpers_Databank::search_ctd_kpk($filters, 200);
            if (empty($rows)) {
                $this->response->body($this->_no_results());
                return;
            }
            $this->response->body(View::factory('templates/user/databank_advanced_results')
                ->set('rows', $rows)
                ->set('columns', array(
                    array('key' => 'CNIC',           'label' => 'CNIC'),
                    array('key' => 'Name',           'label' => 'Name'),
                    array('key' => 'FatherName',     'label' => 'Father'),
                    array('key' => 'PermAdrDistrict','label' => 'Perm. District'),
                    array('key' => 'CurrAdrDistrict','label' => 'Curr. District'),
                    array('key' => 'PermAdrTehsil',  'label' => 'Perm. Tehsil'),
                    array('key' => 'CurrAdrTehsil',  'label' => 'Curr. Tehsil'),
                ))
                ->set('summary', count($rows) . ' CTD KPK record(s)')
                ->render());
        } catch (Exception $e) {
            $this->response->body($this->_search_failed());
        }
    }

    /* ================================================================== */
    /*  DLMS Advanced Search                                              */
    /* ================================================================== */

    public function action_dlms_advanced()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_form_view(array(
            'title'      => 'DLMS Advanced Search',
            'subtitle'   => 'Driving Licence (License_Person + License_Details)',
            'breadcrumb' => 'DLMS Advanced Search',
            'ajax_url'   => URL::site('databank/dlms_advanced_results', TRUE),
            'fields'     => array(
                array('name' => 'cnic',       'label' => 'CNIC',           'placeholder' => '13-digit CNIC, with or without dashes'),
                array('name' => 'name',       'label' => 'Name',           'placeholder' => 'First / Middle / Last (partial OK)'),
                array('name' => 'father',     'label' => 'Father',         'placeholder' => "Father's name parts (partial OK)"),
                array('name' => 'license_no', 'label' => 'Licence Number', 'placeholder' => 'Exact licence number'),
            ),
        ));
    }

    public function action_dlms_advanced_results()
    {
        $this->auto_render = FALSE;
        $this->_require_databank_access();
        try {
            $filters = $this->_collect_filters(array('cnic','name','father','license_no'));
            if (empty($filters)) {
                $this->response->body($this->_no_filters());
                return;
            }
            $rows = Helpers_Databank::search_dlms($filters, 200);
            if (empty($rows)) {
                $this->response->body($this->_no_results());
                return;
            }
            $this->response->body(View::factory('templates/user/databank_advanced_results')
                ->set('rows', $rows)
                ->set('columns', array(
                    array('key' => 'CNIC',         'label' => 'CNIC'),
                    array('key' => 'FirstName',    'label' => 'First'),
                    array('key' => 'MiddleName',   'label' => 'Middle'),
                    array('key' => 'LastName',     'label' => 'Last'),
                    array('key' => 'FatherFName',  'label' => "Father's First"),
                    array('key' => 'FatherLName',  'label' => "Father's Last"),
                    array('key' => 'DOB',          'label' => 'DOB'),
                    array('key' => 'LicenseNo',    'label' => 'Licence No'),
                    array('key' => 'ExpiryDate',   'label' => 'Expiry'),
                ))
                ->set('summary', count($rows) . ' DLMS record(s)')
                ->render());
        } catch (Exception $e) {
            $this->response->body($this->_search_failed());
        }
    }

    /* ================================================================== */
    /*  Government Employee Advanced Search                               */
    /* ================================================================== */

    public function action_govt_employee_advanced()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_form_view(array(
            'title'      => 'Government Employee Advanced Search',
            'subtitle'   => 'Employee data (govt_emp_data.employee_data)',
            'breadcrumb' => 'Govt Employee Advanced Search',
            'ajax_url'   => URL::site('databank/govt_employee_advanced_results', TRUE),
            'fields'     => array(
                array('name' => 'cnic',     'label' => 'CNIC / National ID', 'placeholder' => '13-digit national ID'),
                array('name' => 'name',     'label' => 'Name',               'placeholder' => 'First or last name (partial OK)'),
                array('name' => 'father',   'label' => 'Father / Husband',   'placeholder' => "Father / husband name (partial OK)"),
                array('name' => 'pers_no',  'label' => 'Personnel Number',   'placeholder' => 'Exact personnel number'),
                array('name' => 'org_unit', 'label' => 'Org Unit',           'placeholder' => 'Org unit / department (partial OK)'),
                array('name' => 'job',      'label' => 'Job / Title',        'placeholder' => 'Position / job title (partial OK)'),
            ),
        ));
    }

    public function action_govt_employee_advanced_results()
    {
        $this->auto_render = FALSE;
        $this->_require_databank_access();
        try {
            $filters = $this->_collect_filters(array('cnic','name','father','pers_no','org_unit','job'));
            if (empty($filters)) {
                $this->response->body($this->_no_filters());
                return;
            }
            $rows = Helpers_Databank::search_govt_emp($filters, 200);
            if (empty($rows)) {
                $this->response->body($this->_no_results());
                return;
            }
            $this->response->body(View::factory('templates/user/databank_advanced_results')
                ->set('rows', $rows)
                ->set('columns', array(
                    array('key' => 'national_id',         'label' => 'CNIC'),
                    array('key' => 'first_name',          'label' => 'First Name'),
                    array('key' => 'last_name',           'label' => 'Last Name'),
                    array('key' => 'father_husband_name', 'label' => 'Father / Husband'),
                    array('key' => 'pers_no',             'label' => 'Pers. No.'),
                    array('key' => 'job_title',           'label' => 'Job Title',
                                                          'fallback_keys' => array('position', 'job')),
                    array('key' => 'org_unit',            'label' => 'Org Unit'),
                    array('key' => 'org_unit_short_text', 'label' => 'Unit (short)'),
                ))
                ->set('summary', count($rows) . ' employee record(s)')
                ->render());
        } catch (Exception $e) {
            $this->response->body($this->_search_failed());
        }
    }

    /* ================================================================== */
    /*  Internal helpers                                                  */
    /* ================================================================== */

    /**
     * Build a generic advanced-search form view from $opts. Single
     * call site so all five host pages stay one-liners and the form
     * looks identical across databases.
     *
     * Expected keys: title, subtitle, breadcrumb, ajax_url, fields[].
     * Each field is { name, label, placeholder }.
     */
    private function _form_view(array $opts)
    {
        $defaults = array(
            'title'      => 'Advanced Search',
            'subtitle'   => '',
            'breadcrumb' => 'Search',
            'ajax_url'   => '#',
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
            if (isset($src[$n]) && trim((string) $src[$n]) !== '') {
                $out[$n] = trim((string) $src[$n]);
            }
        }
        return $out;
    }

    private function _no_filters()
    {
        return '<div class="text-muted" style="padding:8px;">'
             . 'Fill at least one field above and click <strong>Search</strong>.</div>';
    }

    private function _no_results()
    {
        return '<div class="text-info" style="padding:8px;">'
             . '<i class="fa fa-info-circle"></i> No matching records.</div>';
    }

    private function _search_failed()
    {
        return '<div class="alert alert-danger" style="margin:8px;">Search failed. '
             . 'The remote database may be unreachable.</div>';
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
