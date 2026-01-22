<?php defined('SYSPATH') or die('No direct script access.');

class Controller_ErrorLog extends Controller_Working
{

    public function action_index()
    {

        // Filters from GET
        $filters = [
            'error_source' => Arr::get($_GET, 'error_source'),
            'error_type' => Arr::get($_GET, 'error_type'),
            'process_stage' => Arr::get($_GET, 'process_stage'),
            'severity' => Arr::get($_GET, 'severity'),
            'request_id' => Arr::get($_GET, 'request_id'),
            'company_name' => Arr::get($_GET, 'company_name'),
            'processing_index' => Arr::get($_GET, 'processing_index'),
            'date_from' => Arr::get($_GET, 'date_from'),
            'date_to' => Arr::get($_GET, 'date_to'),
            'search' => trim(Arr::get($_GET, 'search')),
        ];

        // Build query
        $query = DB::select()
            ->from('system_error_log')
            ->order_by('created_at', 'DESC');

        // Apply filters
        if ($filters['error_source']) {
            $query->where('error_source', '=', $filters['error_source']);
        }
        if ($filters['error_type']) {
            $query->where('error_type', '=', $filters['error_type']);
        }
        if ($filters['process_stage']) {
            $query->where('process_stage', '=', $filters['process_stage']);
        }
        if ($filters['severity']) {
            $query->where('severity', '=', $filters['severity']);
        }
        if ($filters['request_id'] && is_numeric($filters['request_id'])) {
            $query->where('request_id', '=', (int)$filters['request_id']);
        }
        if ($filters['company_name'] !== '') {
            $query->where('company_name', '=', $filters['company_name']);
        }
        if ($filters['processing_index'] !== '') {
            // Filter by processing_index in context_data using safer JSON search
            $processing_search = '%"processing_index":' . (int)$filters['processing_index'] . '%';
            $query->where('context_data', 'LIKE', $processing_search);
        }
        if ($filters['date_from']) {
            $query->where('created_at', '>=', $filters['date_from'] . ' 00:00:00');
        }
        if ($filters['date_to']) {
            $query->where('created_at', '<=', $filters['date_to'] . ' 23:59:59');
        }
        if ($filters['search']) {
            $search = '%' . DB::expr($filters['search']) . '%';
            $query->and_where_open()
                ->or_where('error_message', 'LIKE', $search)
                ->or_where('context_data', 'LIKE', $search)
                ->or_where('error_trace', 'LIKE', $search)
                ->and_where_close();
        }
// ────────────────────────────────────────────────
// Manual Pagination – build count query using same filters
// ────────────────────────────────────────────────

        $count_query = DB::select([DB::expr('COUNT(*)'), 'total'])
            ->from('system_error_log');

// Re-apply the same filters used in main query
        if ($filters['error_source']) {
            $count_query->where('error_source', '=', $filters['error_source']);
        }
        if ($filters['error_type']) {
            $count_query->where('error_type', '=', $filters['error_type']);
        }
        if ($filters['process_stage']) {
            $count_query->where('process_stage', '=', $filters['process_stage']);
        }
        if ($filters['severity']) {
            $count_query->where('severity', '=', $filters['severity']);
        }
        if ($filters['request_id'] && is_numeric($filters['request_id'])) {
            $count_query->where('request_id', '=', (int)$filters['request_id']);
        }
        if ($filters['company_name'] !== '') {
            $count_query->where('company_name', '=', $filters['company_name']);
        }
        if ($filters['processing_index'] !== '') {
            // Filter by processing_index in context_data using safer JSON search
            $processing_search = '%"processing_index":' . (int)$filters['processing_index'] . '%';
            $count_query->where('context_data', 'LIKE', $processing_search);
        }
        if ($filters['date_from']) {
            $count_query->where('created_at', '>=', $filters['date_from'] . ' 00:00:00');
        }
        if ($filters['date_to']) {
            $count_query->where('created_at', '<=', $filters['date_to'] . ' 23:59:59');
        }
        if ($filters['search']) {
            $search = '%' . $filters['search'] . '%';
            $count_query->and_where_open()
                ->or_where('error_message', 'LIKE', $search)
                ->or_where('context_data', 'LIKE', $search)
                ->or_where('error_trace', 'LIKE', $search)
                ->and_where_close();
        }

        $total_items = (int) $count_query->execute()->get('total', 0);

// Main query (keep original $query with ORDER BY)
        $per_page     = 50;
        $current_page = max(1, (int) Arr::get($_GET, 'page', 1));
        $offset       = ($current_page - 1) * $per_page;

        $logs = $query
            ->limit($per_page)
            ->offset($offset)
            ->execute()
            ->as_array();

// Generate pagination HTML (Bootstrap 5 style)
        $pagination_html = '';
        if ($total_items > $per_page) {
            $total_pages = ceil($total_items / $per_page);
            $pagination_html = '<nav aria-label="Error log pagination"><ul class="pagination justify-content-center">';

            // Previous
            if ($current_page > 1) {
                $prev_params = $_GET;
                $prev_params['page'] = $current_page - 1;
                $prev_url = URL::site(Request::current()->uri()) . '?' . http_build_query($prev_params);
                $pagination_html .= '<li class="page-item"><a class="page-link" href="' . $prev_url . '">Previous</a></li>';
            }

            // Pages (show 5 around current)
            $start_page = max(1, $current_page - 2);
            $end_page   = min($total_pages, $current_page + 2);
            for ($i = $start_page; $i <= $end_page; $i++) {
                $active = ($i == $current_page) ? ' active' : '';
                $page_params = $_GET;
                $page_params['page'] = $i;
                $page_url = URL::site(Request::current()->uri()) . '?' . http_build_query($page_params);
                $pagination_html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $page_url . '">' . $i . '</a></li>';
            }

            // Next
            if ($current_page < $total_pages) {
                $next_params = $_GET;
                $next_params['page'] = $current_page + 1;
                $next_url = URL::site(Request::current()->uri()) . '?' . http_build_query($next_params);
                $pagination_html .= '<li class="page-item"><a class="page-link" href="' . $next_url . '">Next</a></li>';
            }

            $pagination_html .= '</ul></nav>';
        }

        $this->template->content =  View::factory('error/index')
            ->set('title', 'System Error Logs')
            ->set('logs', $logs)
            ->set('filters', $filters)
            ->set('per_page', $per_page)
            ->set('total_items', $total_items)

                ->set('pagination_html', $pagination_html) // instead of Pagination object
            ->set('company_options', $this->get_company_options()) // helper method
            ->set('source_options', $this->get_source_options())  // helper method
            ->set('severity_options', $this->get_severity_options())  // helper method
            ->set('processing_index_options', $this->get_processing_index_options()); // helper method
    }

    // Helper: Company dropdown options (adjust as per your system)
    private function get_company_options()
    {
        return [
            '' => 'All Companies',
            '1' => 'Jazz / Mobilink',
            '3' => 'Ufone',
            '4' => 'Zong',
            '6' => 'Telenor',
            '7' => 'Warid',
            // add more if needed
        ];
    }

    // Helper: Error source dropdown (add more as you create new sources)
    private function get_source_options()
    {
        return [
            '' => 'All Sources',
            'action_email_receive2' => 'Email Receive 2',
            'cron_parse_sub' => 'Subscriber Parsing Cron',
            'receive_email' => 'Email Receiving',
            'receive_email_backup' => 'Email Receiving Backup',
            'get_email_status' => 'Email Status Check',
            'send_email' => 'Email Sending',
            'cron_email_send' => 'Email Sending Cron',
            'cron_parse_imei' => 'IMEI Parsing',
            'send_high_priority' => 'Send High Priority',
            'send_high_priority_query' => 'High Priority Query',
            'high_priority_send' => 'High Priority Send',
            'high_priority_fetch' => 'High Priority Fetch',
            'test_cron' => 'Test / Debug',
            'upload' => 'File Upload',
            'controller_action' => 'Controller Action',
        ];
    }

    // Helper: Severity level dropdown
    private function get_severity_options()
    {
        return [
            '' => 'All Severities',
            'error' => 'Error',
            'warning' => 'Warning',
            'success' => 'Success',
            'info' => 'Info',
        ];
    }

    // Helper: Processing index dropdown for tracking status
    private function get_processing_index_options()
    {
        return [
            '' => 'All Status',
            '3' => 'Status 3 (Error)',
            '5' => 'Status 5 (Not Found)',
        ];
    }

    /**
     * Clear error logs action
     */
    public function action_clear()
    {
        // Only allow POST requests

        if ($this->request->method() !== Request::POST) {
            $this->redirect('errorlog/');
        }

        $range = Arr::get($_POST, 'clear_range', '7days');
        $from = Arr::get($_POST, 'date_from');
        $to = Arr::get($_POST, 'date_to');

        // Perform the deletion
        $deleted = Model_ErrorLog::clearLogs($range, $from, $to);

        // Set success message
        Session::instance()->set('success', "Successfully cleared $deleted log entries.");

        // Redirect back to index
        $this->redirect('errorlog/index');
    }
}