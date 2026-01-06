<?php

abstract class Helpers_Layout {    
    
    public static function get_notification()
    {
        /* get notification from db */
        $notification_div= '<br/><br/>'; 
        /*'<div class="marquee col-md-6">
		<p>CTD Punjab, AIES Notice</p>
		<p>Your Internet Connection Is Slow.</p>
		<p>Please wait or refresh your page again.</p>
	</div>';*/
        
        return $notification_div;        
    }
    /* Loader */
    public static function get_ajax_loader()
    {
        /* get notification from db */
        $notification_div='<div class="ajax-loader">
                            ' .  HTML::image("dist/img/ajax-loader.gif",array('alt'=>'Loading....')) . '
                            <div class="" style="position: relative;">
                                Loading...
                            </div>
                            </div>';
        
        return $notification_div;        
    }
    public static function get_header()
    {
        //Any calling template must have this view in required location
        $site_header = View::factory('templates/layout/site-header');
        return $site_header;        
    }
    
    public static function get_sidebar_user(){
        $site_topnav = View::factory('templates/layout/sidebar_user');
        return $site_topnav;    
    }
    public static function get_sidebar_person(){
        $site_topnav = View::factory('templates/layout/sidebar_person');
        return $site_topnav;
    }
    public static function get_topnav($theme_dir,$template_dir,$site_id){
        //Any calling template must have this view in required location
        $site_topnav = View::factory('templates/layout/site-topnav');
        return $site_topnav;    
    }
    
    //public static function get_sitefooter($theme_dir,$template_dir,$site_id){
    public static function get_sitefooter(){
        //Any calling template must have this view in required location
        $site_footer = View::factory('templates/layout/site-footer');
        return $site_footer;    
    }
    public static function get_query_string(){
        $request_uri  = isset($_SERVER['REQUEST_URI'])     ? ($_SERVER['REQUEST_URI'])  : 'n/a';
        $query_string = isset($_SERVER['QUERY_STRING'])    ? ($_SERVER['QUERY_STRING']) : 'n/a';
        $ip_address   = isset($_SERVER['REMOTE_ADDR'])     ? htmlentities($_SERVER['REMOTE_ADDR'],     ENT_QUOTES, 'UTF-8') : 'n/a';
        $user_agent   = isset($_SERVER['HTTP_USER_AGENT']) ? htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8') : 'n/a';

        $report = 'User Agent: '   . $user_agent   ."\n";
        $report .= 'Query String: ' . ($query_string) ."\n";

        $current_date = date('Y-m-d H:i:s');        
        $login_user = Auth::instance()->get_user();
        $user_id = !empty($login_user->id)? $login_user->id:"NA";
        $query1 = DB::insert('url_hits_log', array('user_id', 'user_ip', 'user_agent', 'accessed_url', 'accessed_url_status_code', 'timestamp'))
                        ->values(array($user_id, $ip_address, $report, ($request_uri), http_response_code(), $current_date))
                        ->execute();
        
        
    }
    
    
}

?>
