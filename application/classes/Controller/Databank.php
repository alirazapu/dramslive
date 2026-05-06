<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * DRAMS Databank — generic external-database search hub.
 *
 * Each action is a thin host page that:
 *   1. Renders a search form using the generic templates/user/databank_search view.
 *   2. AJAX-posts/gets to a backend endpoint, which is either:
 *      - a fresh endpoint in this controller (e.g. action_ecp_address_results), or
 *      - an already-existing endpoint in Userreports / Persons that we
 *        just point at (e.g. /userreports/msisdn_data_search).
 *
 * Visibility is gated by the same role check used in sidebar_user.php
 * for the DRAMS Databank parent menu (role 34 or 35, OR user 170/171).
 * Each action enforces it again so URLs cannot be hit directly by an
 * unauthorised user.
 *
 * Design intent: replaces the old per-controller scattering of search
 * forms (e.g. /persons/ecp_address_search_page) with one consistent
 * landing place that mirrors the menu groupings.
 */
class Controller_Databank extends Controller_Working
{

    public function __Construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->request  = $request;
        $this->response = $response;
    }

    /** Default landing — first available search. */
    public function action_index()
    {
        $this->redirect('databank/ecp_address');
    }

    /* ------------------------------------------------------------------ */
    /*  ECP database (192.168.0.156)                                      */
    /* ------------------------------------------------------------------ */

    /** ECP address-text search — host page. */
    public function action_ecp_address()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_search_view(array(
            'title'        => 'ECP Address Search',
            'subtitle'     => 'Find ECP records by free-text in the address column',
            'breadcrumb'   => 'ECP Address Search',
            'placeholder'  => 'e.g. Karachi, Saddar, House #123…',
            'input_name'   => 'q',
            'ajax_url'     => URL::site('databank/ecp_address_results', TRUE),
            'ajax_method'  => 'GET',
            'ajax_extra'   => array('limit' => 100),
            'help_text'    => 'Searches ecp_persons.address_text. Rows whose address has not yet been OCR\'d from the image will not appear here — run /cronjob/ecp_address_diagnostic to check the backlog.',
        ));
    }

    /** ECP address-text search — AJAX results endpoint (fragment HTML). */
    public function action_ecp_address_results()
    {
        $this->auto_render = FALSE;
        try {
            $_GET  = Helpers_Utilities::remove_injection($_GET);
            $q     = isset($_GET['q'])     ? trim((string) $_GET['q']) : '';
            $limit = isset($_GET['limit']) ? (int) $_GET['limit']      : 100;

            if ($q === '') {
                $this->response->body('<div class="text-muted">Enter address text to search.</div>');
                return;
            }
            $rows = Helpers_Person::search_ecp_by_address($q, $limit);
            if (empty($rows)) {
                $this->response->body('<div class="text-info"><i class="fa fa-info-circle"></i> No matching ECP records.</div>');
                return;
            }
            $view = View::factory('templates/user/ecp_address_search')
                ->set('q', $q)
                ->set('rows', $rows);
            $this->response->body($view->render());
        } catch (Exception $e) {
            $this->response->body('<div class="text-danger">Search failed.</div>');
        }
    }

    /** ECP person profile by CNIC — host page (delegates to /persons/ext_db_ecp). */
    public function action_ecp_cnic()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_search_view(array(
            'title'        => 'ECP Person Search by CNIC',
            'subtitle'     => 'Look up a person\'s ECP record (electoral list)',
            'breadcrumb'   => 'ECP Person Search',
            'placeholder'  => '13-digit CNIC (with or without dashes)',
            'input_name'   => 'cnic',
            'ajax_url'     => URL::site('persons/ext_db_ecp', TRUE),
            'ajax_method'  => 'GET',
            'help_text'    => 'Searches ecp_persons by CNIC. Renders the same panel used on the person dashboard, including Family Tree.',
        ));
    }

    /* ------------------------------------------------------------------ */
    /*  CTD KPK database (192.168.5.204)                                  */
    /* ------------------------------------------------------------------ */

    public function action_ctd_kpk()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_search_view(array(
            'title'        => 'CTD KPK Search by CNIC',
            'subtitle'     => 'KPK CTD records — Personal Info / Schedule IV / Accused',
            'breadcrumb'   => 'CTD KPK Search',
            'placeholder'  => '13-digit CNIC',
            'input_name'   => 'cnic',
            'ajax_url'     => URL::site('persons/ext_db_ctd_kpk', TRUE),
            'ajax_method'  => 'GET',
            'help_text'    => 'Searches dct_person_profile in the ctd_kpk database (192.168.5.204). The Schedule IV and Accused tabs are lazy-loaded after the panel renders.',
        ));
    }

    /* ------------------------------------------------------------------ */
    /*  DLMS driving licence (SQL Server, 192.168.0.152)                  */
    /* ------------------------------------------------------------------ */

    public function action_dlms()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_search_view(array(
            'title'        => 'DLMS Driving Licence Search',
            'subtitle'     => 'Driving licence records by CNIC',
            'breadcrumb'   => 'DLMS Search',
            'placeholder'  => '13-digit CNIC',
            'input_name'   => 'cnic',
            'ajax_url'     => URL::site('persons/ext_db_dlms', TRUE),
            'ajax_method'  => 'GET',
            'help_text'    => 'Searches License_Person / License_Details on DLMS_FamzSolutions (SQL Server, 192.168.0.152).',
        ));
    }

    /* ------------------------------------------------------------------ */
    /*  Government employees (govt_emp_data on 192.168.0.151)             */
    /* ------------------------------------------------------------------ */

    public function action_govt_employee()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_search_view(array(
            'title'        => 'Government Employee Search',
            'subtitle'     => 'Look up a government employee record by CNIC',
            'breadcrumb'   => 'Govt Employee Search',
            'placeholder'  => '13-digit CNIC',
            'input_name'   => 'cnic',
            'ajax_url'     => URL::site('persons/ext_db_employee', TRUE),
            'ajax_method'  => 'GET',
            'help_text'    => 'Searches employee_data in the govt_emp_data database (192.168.0.151).',
        ));
    }

    /* ------------------------------------------------------------------ */
    /*  Mobile subscriber database (subscriber_db on 192.168.0.151)       */
    /* ------------------------------------------------------------------ */

    /** Mobile-subscriber search — host page. Delegates to existing AJAX. */
    public function action_mobile_subscriber()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_search_view(array(
            'title'             => 'Mobile Subscriber Search',
            'subtitle'          => 'Search by MSISDN / CNIC / IMSI in subscribers_main',
            'breadcrumb'        => 'Mobile Subscriber Search',
            'placeholder'       => 'Enter MSISDN / CNIC / IMSI value',
            'input_name'        => 'search_value',
            'input_select_name' => 'search_type',
            'input_select'      => array(
                'msisdn' => 'MSISDN',
                'cnic'   => 'CNIC',
                'imsi'   => 'IMSI',
            ),
            'ajax_url'          => URL::site('userreports/msisdn_data_search', TRUE),
            'ajax_method'       => 'POST',
            'help_text'         => 'Searches subscriber_db on 192.168.0.151. Returns the same fragment used by the existing search_person page.',
        ));
    }

    /* ------------------------------------------------------------------ */
    /*  Foreigner accounts (afghan_accounts in subscriber_db)             */
    /* ------------------------------------------------------------------ */

    public function action_foreigner()
    {
        $this->_require_databank_access();
        $this->template->content = $this->_search_view(array(
            'title'             => 'Foreigner Account Search',
            'subtitle'          => 'Search Afghan accounts by master account number / foreign CNIC',
            'breadcrumb'        => 'Foreigner Account Search',
            'placeholder'       => 'Enter the lookup value',
            'input_name'        => 'search_value',
            'input_select_name' => 'search_type',
            'input_select'      => array(
                'master_acc_number' => 'Master Account #',
                'foreign_cnic'      => 'Foreign CNIC',
                'foreigner_profile' => 'Foreigner Profile',
            ),
            'ajax_url'          => URL::site('userreports/search_foreinger_detail', TRUE),
            'ajax_method'       => 'POST',
            'help_text'         => 'Searches afghan_accounts on subscriber_db (192.168.0.151).',
        ));
    }

    /* ------------------------------------------------------------------ */
    /*  Internal helpers                                                  */
    /* ------------------------------------------------------------------ */

    /**
     * Build a databank_search view with the supplied options. Fills in
     * sane defaults for everything the template expects so individual
     * actions only set what they care about.
     */
    private function _search_view(array $opts)
    {
        $defaults = array(
            'title'             => 'Search',
            'subtitle'          => '',
            'breadcrumb'        => 'Search',
            'placeholder'       => '',
            'input_name'        => 'q',
            'input_select_name' => '',
            'input_select'      => array(),
            'ajax_url'          => '#',
            'ajax_method'       => 'GET',
            'ajax_extra'        => array(),
            'help_text'         => '',
        );
        $opts = array_merge($defaults, $opts);

        $view = View::factory('templates/user/databank_search');
        foreach ($opts as $k => $v) {
            $view->set($k, $v);
        }
        return $view;
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
