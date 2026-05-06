
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$current_url = request::current()->controller();
try {
    $user = Auth::instance()->get_user();
    $user_first_letter = Helpers_Profile::get_first_letters($user->id);
    $user_name = Helpers_Utilities::get_user_name($user->id);


} catch (Exception $ex) {
    $user_first_letter='';
    $user_name='';
}
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
          <span class="name-letter">
                    <?php
                    echo $user_first_letter;
                    ?>
                </span>
            </div>
            <div class="pull-left info">
                <p><?php echo $user_name?></p>
            </div>
        </div>     
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <?php
        try {
            $permission = Helpers_Utilities::get_user_permission($user->id);
            $role_id = Helpers_Utilities::get_user_role_id($user->id);
            //get user posting
            $login_user = Auth::instance()->get_user();
            $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
            $posting = $login_user_profile->posted;
            $result = explode('-', $posting);


        } catch (Exception $ex) {
            
        }
        ?>
        <ul class="sidebar-menu">
            <li class="header"> <span id="date_time"></span></li>

<?php if (Helpers_Utilities::chek_role_access($role_id, 1) == 1) { ?>
    <li class="treeview <?php echo ($menu_name == 'dashboard') ? 'active' : ''; ?>">
        <a href="<?php echo URL::site('Userdashboard/dashboard'); ?>">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>            
        </a>          
</li> <?php } ?>

<?php if (Helpers_Utilities::chek_role_array_access($role_id, array(9,10,11,12,13)) == 1) { ?>

<li class="treeview <?php
    echo (
        $current_url == 'User' && in_array($menu_name, array(
            'upload_against_msisdn',
            'upload_against_cnic',
            'upload_against_imei',
            'uploaded_cdrs',
            'cdr_upload_format'
        ))
    ) ? 'active' : '';
?>">

    <a href="#">
        <i class="fa fa-upload"></i>
        <span>Data Upload</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <ul class="treeview-menu">

        <?php if (Helpers_Utilities::chek_role_access($role_id, 9) == 1) { ?>
            <li class="<?php echo ($menu_name == 'upload_against_msisdn') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('User/upload_against_msisdn'); ?>">
                    <i class="fa fa-circle-o"></i> Upload CDRs by Mobile#
                </a>
            </li>
        <?php } ?>

        <?php if (Helpers_Utilities::chek_role_access($role_id, 10) == 1) { ?>
            <li class="<?php echo ($menu_name == 'upload_against_cnic') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('User/upload_against_cnic'); ?>">
                    <i class="fa fa-circle-o"></i> Upload CDRs by CNIC
                </a>
            </li>
        <?php } ?>

        <?php if (Helpers_Utilities::chek_role_access($role_id, 11) == 1) { ?>
            <li class="<?php echo ($menu_name == 'upload_against_imei') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('User/upload_against_imei'); ?>">
                    <i class="fa fa-circle-o"></i> Upload CDRs by IMEI
                </a>
            </li>
        <?php } ?>

        <?php if (Helpers_Utilities::chek_role_access($role_id, 12) == 1) { ?>
            <li class="<?php echo ($menu_name == 'uploaded_cdrs') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('User/uploaded_cdrs'); ?>">
                    <i class="fa fa-circle-o"></i> Uploaded CDR Status
                </a>
            </li>
        <?php } ?>


    </ul>
</li>

<?php } ?>


<?php if (Helpers_Utilities::chek_role_array_access($role_id, array(2,3,4,5,6,7,8)) == 1) { ?>
<li class="treeview <?php
    echo (
        ($current_url == 'User' && in_array($menu_name, array(
            'bparty_search',
            'bparty_search_aies',
            'search_identity',
            'search_person',
            'bulk_data_search',
            'mobile_bulk_data_search',
            'bparty_active',
            'lat_long_active',
            'lac_cell_active',
            'multi_analysis_against_mob_numbers',
            'imei_against_sim',
            'most_imei_against_sim',
            'imei_active',
            'sims_against_imei_active',          
        )))
        || ($current_url == 'Othernumbersearch' && in_array($menu_name, array(
            'bulk_search_indepth',
            'other_number_search'
        )))
        || (strtolower($current_url) == 'userreports' && in_array(strtolower($menu_name), array(
            'my_favourite_persons',
            'my_persons_analysis'
        )))
    ) ? 'active' : '';
?>">

    <a href="#">
        <i class="fa fa-search-plus"></i>
        <span>Person & Data Search</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <ul class="treeview-menu">

        <?php if (Helpers_Utilities::chek_role_access($role_id, 2) == 1) { ?>
            <li class="<?php echo ($menu_name == 'bparty_search_aies') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('User/bparty_search_aies'); ?>">
                    <i class="fa fa-circle-o"></i> Mobile Search
                </a>
            </li>            
        <?php } ?>

        <?php if (Helpers_Utilities::chek_role_access($role_id, 3) == 1) { ?>
            <li class="<?php echo ($menu_name == 'search_identity') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('User/search_identity'); ?>">
                    <i class="fa fa-circle-o"></i> Identity Verification
                </a>
            </li>
        <?php } ?>

        <?php if (Helpers_Utilities::chek_role_access($role_id, 4) == 1) { ?>
            <li class="<?php echo ($menu_name == 'search_person') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('User/search_person'); ?>">
                    <i class="fa fa-circle-o"></i> Advanced Person Search
                </a>
            </li>
        <?php } ?>

        <?php if (Helpers_Utilities::chek_role_access($role_id, 5) == 1) { ?>
            <li class="<?php echo ($menu_name == 'bulk_data_search') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('User/bulk_data_search'); ?>">
                    <i class="fa fa-circle-o"></i> CNIC Bulk Search
                </a>
            </li>

            <li class="<?php echo ($menu_name == 'mobile_bulk_data_search') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('User/mobile_bulk_data_search'); ?>">
                    <i class="fa fa-circle-o"></i> Mobile Bulk Search
                </a>
            </li>
        <?php } ?>

        <?php if (Helpers_Utilities::chek_role_access($role_id, 6) == 1) { ?>
            <li class="<?php echo ($menu_name == 'other_number_search') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('Othernumbersearch/other_number_search'); ?>">
                    <i class="fa fa-circle-o"></i> Alternate Number Search
                </a>
            </li>
        <?php } ?>

        <?php if ($posting != 'r-33' && $posting != 'r-25') { ?>

            <?php if (Helpers_Utilities::chek_role_access($role_id, 7) == 1) { ?>                

                <li class="<?php echo ($menu_name == 'bparty_active') ? 'active' : ''; ?>">
                    <a href="<?php echo URL::site('User/bparty_active'); ?>">
                        <i class="fa fa-circle-o"></i> Most Active B-Party
                    </a>
                </li>

                <li class="<?php echo ($menu_name == 'lat_long_active') ? 'active' : ''; ?>">
                    <a href="<?php echo URL::site('User/lat_long_active'); ?>">
                        <i class="fa fa-circle-o"></i> Most Active Locations
                    </a>
                </li>

                <li class="<?php echo ($menu_name == 'lac_cell_active') ? 'active' : ''; ?>">
                    <a href="<?php echo URL::site('User/lac_cell_active'); ?>">
                        <i class="fa fa-circle-o"></i> Active LAC & Cell IDs
                    </a>
                </li>

                <li class="<?php echo ($menu_name == 'imei_active') ? 'active' : ''; ?>">
                    <a href="<?php echo URL::site('User/imei_active'); ?>">
                        <i class="fa fa-circle-o"></i> Most Active IMEIs
                    </a>
                </li>

                <li class="<?php echo ($menu_name == 'imei_against_sim') ? 'active' : ''; ?>">
                    <a href="<?php echo URL::site('User/imei_against_sim'); ?>">
                        <i class="fa fa-circle-o"></i> IMEIs Linked to SIMs
                    </a>
                </li>

                <li class="<?php echo ($menu_name == 'multi_analysis_against_mob_numbers') ? 'active' : ''; ?>">
                    <a href="<?php echo URL::site('User/multi_analysis_against_mob_numbers'); ?>">
                        <i class="fa fa-circle-o"></i> Mobile Multi-Number Analysis
                    </a>
                </li>
            <?php } ?>

        <?php } ?>

        <?php if (Helpers_Utilities::chek_role_access($role_id, 8) == 1) { ?>
            <li class="<?php echo ($menu_name == 'my_persons_analysis') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('userreports/my_persons_analysis/' . Helpers_Utilities::encrypted_key($login_user->id, 'encrypt')); ?>">
                    <i class="fa fa-circle-o"></i> My Analysis Reports
                </a>
            </li>

            <li class="<?php echo ($menu_name == 'my_favourite_persons') ? 'active' : ''; ?>">
                <a href="<?php echo URL::site('userreports/my_favourite_persons/' . Helpers_Utilities::encrypted_key($login_user->id, 'encrypt')); ?>">
                    <i class="fa fa-circle-o"></i> My Favourite Persons
                </a>
            </li>
        <?php } ?>

    </ul>
</li>
<?php } ?>
<?php if (Helpers_Utilities::chek_role_array_access($role_id, array(25,26)) == 1) { ?>
    <li class="treeview <?php echo ($current_url == 'Watchlist' ) ? 'active' : ''; ?>">
        <a href="#">
            <i class="fa  fa-clock-o"></i>
            <span>Watch List Persons</span> 
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>

        <ul class="treeview-menu">
            <?php if ((Helpers_Utilities::chek_role_access($role_id, 25) == 1 ) || (($permission == 2 || $permission == 3 || $permission == 4) && ($result[0] == 'd') )) { ?>
                <li class="<?php echo ($menu_name == 'add_watch_list') ? 'active' : ''; ?>"><a href="<?php echo URL::site('watchlist/add_watch_list'); ?>"><i class="fa fa-cog"></i> Add Watch List Person </a></li>
            <?php } ?>
            <?php if (Helpers_Utilities::chek_role_access($role_id, 26) == 1) { ?>
            <li class="<?php echo ($menu_name == 'view_watch_list' || $menu_name == 'view_watch_list_details') ? 'active' : ''; ?>"><a href="<?php echo URL::site('watchlist/view_watch_list'); ?>"><i class="fa fa-cog"></i> Area Wise Watch List Persons</a></li>
            <?php } ?>
            <?php if (Helpers_Utilities::chek_role_access($role_id, 26) == 1) { ?>
            <li class="<?php echo ($menu_name == 'users_view_watch_list'|| $menu_name == 'user_wl_person' ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('watchlist/users_view_watch_list'); ?>"><i class="fa fa-cog"></i> User's Watch List Persons</a></li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
<?php if (Helpers_Utilities::chek_role_array_access($role_id, array(27,28)) == 1) { ?>
  <li class="treeview <?php echo ($current_url == 'Emailtemplate' ) ? 'active' : ''; ?>">
      <a href="#">
          <i class="fa  fa-send"></i>
          <span>Email Template</span> 
          <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
          </span>
      </a>

      <ul class="treeview-menu">
          <?php if (Helpers_Utilities::chek_role_access($role_id, 27) == 1) { ?>
          <li class="<?php echo ($menu_name == 'index') ? 'active' : ''; ?>"><a href="<?php echo URL::site('emailtemplate'); ?>"><i class="fa fa-circle-o"></i> List Email Template </a></li>
          <?php } ?>
          <?php if (Helpers_Utilities::chek_role_access($role_id, 28) == 1) { ?>
          <li class="<?php echo ($menu_name == 'showform') ? 'active' : ''; ?>"><a href="<?php echo URL::site('emailtemplate/showform'); ?>"><i class="fa fa-circle-o"></i> Add New Template</a></li>            
          <?php } ?>
      </ul>
  </li>
<?php } ?>
<?php if (Helpers_Utilities::chek_role_array_access($role_id, array(27,28)) == 1) { ?>
  <li class="treeview <?php echo ($current_url == 'Shortcode' ) ? 'active' : ''; ?>">
      <a href="#">
          <i class="fa  fa-send"></i>
          <span>Short Code</span>
          <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
          </span>
      </a>

      <ul class="treeview-menu">
          <?php if (Helpers_Utilities::chek_role_access($role_id, 27) == 1) { ?>
          <li class="<?php echo ($menu_name == 'index') ? 'active' : ''; ?>"><a href="<?php echo URL::site('shortcode'); ?>"><i class="fa fa-circle-o"></i> List Short Code </a></li>
          <?php } ?>
          <?php if (Helpers_Utilities::chek_role_access($role_id, 28) == 1) { ?>
          <li class="<?php echo ($menu_name == 'showform') ? 'active' : ''; ?>"><a href="<?php echo URL::site('shortcode/showform'); ?>"><i class="fa fa-circle-o"></i> Add New Short Code</a></li>
          <?php } ?>
      </ul>
  </li>
<?php } ?>               
<?php if ($role_id == 1 || $role_id == 9) { ?>
  <li class="treeview <?php echo ($menu_name == 'menu_managment') ? 'active' : ''; ?>">
      <a href="<?php echo URL::site('Adminreports/menu_managment'); ?>">
          <i class="fa fa-send"></i>
          <span>Menu Management</span>            
      </a>          
  </li>
<?php } ?> 
<?php if ($role_id == 1 || $role_id == 9) { ?>
  <li class="treeview <?php echo ($menu_name == 'user_registration') ? 'active' : ''; ?>">
      <a href="<?php echo URL::site('user/user_registration'); ?>">
          <i class="fa fa-send"></i>
          <span>User Registration</span>            
      </a>          
  </li>
<?php } ?>   
<?php if (Helpers_Utilities::chek_role_access($role_id, 33) == 1) { ?>
    <li class="treeview <?php echo ($menu_name == 'access_control_list') ? 'active' : ''; ?>">
        <a href="<?php echo URL::site('userreports/access_control_list'); ?>">
            <i class="fa fa-expeditedssl"></i>
            <span>Access Control List</span>            
        </a>          
    </li>
<?php } ?>
     
<?php if (Helpers_Utilities::chek_role_array_access($role_id, array(14,15,16,17,18,19,20,21,22,23,24)) == 1) { ?>
                <li class="treeview <?php echo ($current_url == 'Userrequest') ? 'active' : ''; ?>">
                    <a href="#">
                        <i class="fa  fa-files-o"></i>
                        <span>User Requests</span> 
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 14) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'request_status' || $menu_name == 'request_status_detail') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/request_status'); ?>"><i class="fa fa-circle-o"></i> Request Status</a></li>
                        <?php } ?>
                        <?php /* if (Helpers_Utilities::chek_role_access($role_id, 15) == 1) { */ ?>
                        <?php if (FALSE) { ?>
                        <li class="<?php echo ($menu_name == 'request_status_branclessbanking' || $menu_name == 'request_status_detail_banking') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/request_status_branclessbanking'); ?>"><i class="fa fa-circle-o"></i> Requests Branchless Banking</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 16) == 1) { ?>                        
                            <li class="<?php echo ($menu_name == 'nadra_requests' ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/nadra_requests'); ?>"><i class="fa fa-circle-o"></i> NADRA Requests</a></li>
                        <?php } ?>
                        <?php if ((Helpers_Utilities::chek_role_access($role_id, 18) == 1) || ($role_id==4 && $result[1]==4)) { ?>
                            <li class="<?php echo ($menu_name == 'familytree_requests' ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/familytree_requests'); ?>"><i class="fa fa-circle-o"></i> Family Tree Requests</a></li>
                        <?php } ?>
                        <?php if (($role_id == 1) || ($role_id == 4 && $result[1] == 1)) { ?>                        
                            <li class="<?php echo ($menu_name == 'travelhistory_requests' ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/travelhistory_requests'); ?>"><i class="fa fa-circle-o"></i> Travel History</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 17) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'rejected_requests' ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/rejected_requests'); ?>"><i class="fa fa-circle-o"></i> Rejected Requests</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 18) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'request_status_familytree' ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/request_status_familytree'); ?>"><i class="fa fa-circle-o"></i> Family Tree Request</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 19) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'blocked_numbers' ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/blocked_numbers'); ?>"><i class="fa fa-circle-o"></i> Blocked Number</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 20) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'request_schedular' ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/request_schedular'); ?>"><i class="fa fa-circle-o"></i> Request Scheduler</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 21) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'parsing_queue' ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/parsing_queue'); ?>"><i class="fa fa-circle-o"></i> Parsing Queue</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 22) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'cronjobmanual' ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/cronjobmanual'); ?>"><i class="fa fa-circle-o"></i> Cron Job Manual</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 23) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'request_status_resend' ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userrequest/request_status_resend'); ?>"><i class="fa fa-circle-o"></i>Old Request Resend</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 24) == 1) { ?>
                            <li class="treeview <?php echo ($menu_name == 'request_status_telenor') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userrequest/request_status_telenor'); ?>"><i class="fa fa-file-archive-o"></i><span>Telenor Request<small style=" font-size: 10px;">(Less Than 6 Months)</small></span><span class="pull-right-container"></span></a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 24) == 1) { ?>
                            <li class="treeview <?php echo ($menu_name == 'request_status_telenor_sixmonths') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userrequest/request_status_telenor_sixmonths'); ?>"><i class="fa fa-file-archive-o"></i><span>Telenor Request<small style=" font-size: 10px;">(More Than 6 Months)</small></span><span class="pull-right-container"></span></a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 24) == 1) { ?>
                            <li class="treeview <?php echo ($menu_name == 'request_status_ufone') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userrequest/request_status_ufone'); ?>"><i class="fa fa-file-archive-o"></i><span>Ufone Request</span><span class="pull-right-container"></span></a></li>
                        <?php } ?>
                    </ul>
                </li>           
            <?php } ?>
                
<?php if (Helpers_Utilities::chek_role_array_access($role_id, array(29,30)) == 1) { ?>
                <li class="treeview <?php echo ($current_url == 'Intprojects' || ($current_url == 'Userreports' && ($menu_name == 'project_request_type' || $menu_name == 'project_request_send_detail'))) ? 'active' : ''; ?>">
                    <a href="#">
                        <i class="fa  fa-list"></i>
                        <span>Projects</span> 
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 29) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'index') ? 'active' : ''; ?>"><a href="<?php echo URL::site('intprojects'); ?>"><i class="fa fa-circle-o"></i> List Projects </a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 30) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'showform') ? 'active' : ''; ?>"><a href="<?php echo URL::site('intprojects/showform'); ?>"><i class="fa fa-circle-o"></i> Add New Project</a></li>            
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>

            <?php if ($login_user->id == 842 || $login_user->id == 137 || $login_user->id == 2031) { ?>
                <li class="treeview <?php echo ($current_url == 'user' && $menu_name == 'interaction') ? 'active' : ''; ?>" style="display:none">
                    <a href="#">
                        <i class="fa  fa-list"></i>
                        <span>Admin Tools</span> 
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        <li class="<?php echo ($menu_name == 'interaction') ? 'active' : ''; ?>"><a href="<?php echo URL::site('user/interaction'); ?>"><i class="fa fa-circle-o"></i> System Interaction </a></li>            
                    </ul>
                </li>
            <?php } ?>

            <?php if (Helpers_Utilities::chek_role_array_access($role_id, array(31,32)) == 1) { ?>
                <li class="treeview <?php echo ($current_url == 'Organization' ) ? 'active' : ''; ?>">
                    <a href="#">
                        <i class="fa  fa-university"></i>
                        <span>Organization</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>

                    <ul class="treeview-menu">
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 31) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'index') ? 'active' : ''; ?>"><a href="<?php echo URL::site('organization'); ?>"><i class="fa fa-circle-o"></i> List Organization </a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 32) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'showform') ? 'active' : ''; ?>"><a href="<?php echo URL::site('organization/showform'); ?>"><i class="fa fa-circle-o"></i> Add New Organization</a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            

            <?php if (Helpers_Utilities::chek_role_array_access($role_id, array(34,35,36,37,38,39,40)) == 1) { ?>
                <li class="treeview <?php echo (($current_url == 'Userreports' && $menu_name == 'telco_reports') || ($current_url == 'Userreports' && $menu_name == 'admin_reports') || ($current_url == 'Adminreports' && ($menu_name == 'identity_breakup_report' || $menu_name == 'verisys_pending_report' || $menu_name == 'users_breakup_report' || $menu_name == 'blocked_ip_list' || $menu_name == 'password_reset'))) ? 'active' : ''; ?>">
                    <a href="#">
                        <i class="fa  fa-send"></i>
                        <span>Admin Reports</span> 
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'identity_breakup_report') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminreports/identity_breakup_report'); ?>"><i class="fa fa-circle-o"></i>Identity Breakup</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 35) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'verisys_pending_report') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminreports/verisys_pending_report'); ?>"><i class="fa fa-circle-o"></i>Verisys Pending</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 36) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'admin_reports') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userreports/admin_reports'); ?>"><i class="fa fa-circle-o"></i>NADRA API</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 37) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'telco_reports') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userreports/telco_reports'); ?>"><i class="fa fa-circle-o"></i>Telco Reports </a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 38) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'users_breakup_report') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminreports/users_breakup_report'); ?>"><i class="fa fa-circle-o"></i>Users Breakup</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 39) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'blocked_ip_list') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminreports/blocked_ip_list'); ?>"><i class="fa fa-circle-o"></i>Blocked IPs </a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 40) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'password_reset') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminreports/password_reset'); ?>"><i class="fa fa-circle-o"></i>Password Reset </a></li>
                        <?php } ?>                    
                    </ul>
                </li>
            <?php } ?>

            <?php if ((Helpers_Utilities::chek_role_array_access($role_id, array(34,35)) == 1) || $user->id ==171 ||  $user->id ==719) { ?>
                <li class="treeview <?php echo (($current_url == 'Adminrequest' && ($menu_name == 'admin_request_sent_form' || $menu_name == 'admin_custom_request_form' || $menu_name == 'admin_advance_custom_request_form' || $menu_name == 'admin_sent_request_status'|| $menu_name == 'admin_sent_request_count'|| $menu_name == 'travel_request_sent_form'|| $menu_name == 'nadra_request_sent_form'|| $menu_name == 'user_request_count'||  $menu_name == 'familytree_request_sent_form' || $menu_name == 'nadra_bulk_request_sent_form'))) ? 'active' : ''; ?>">
                    <a href="#">
                        <i class="fa  fa-plus"></i>
                        <span>DRAMS Plus</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'admin_request_sent_form') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminrequest/admin_request_sent_form'); ?>"><i class="fa fa-circle-o"></i>Single Request</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'admin_custom_request_form') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminrequest/admin_custom_request_form'); ?>"><i class="fa fa-circle-o"></i>Custom Request</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'admin_advance_custom_request_form') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminrequest/admin_advance_custom_request_form'); ?>"><i class="fa fa-rocket"></i> Advance Custom Request</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 35) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'admin_sent_request_status') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminrequest/admin_sent_request_status'); ?>"><i class="fa fa-circle-o"></i> Admin Request Status</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 35) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'admin_sent_request_count') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminrequest/admin_sent_request_count'); ?>"><i class="fa fa-circle-o"></i>Admin Requests Count</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 35) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'user_request_count') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminrequest/user_request_count'); ?>"><i class="fa fa-circle-o"></i>User Requests Count</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1 ||  $user->id ==719) { ?>
                            <li class="<?php echo ($menu_name == 'nadra_request_sent_form') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminrequest/nadra_request_sent_form'); ?>"><i class="fa fa-circle-o"></i>Nadra Request Form</a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1 ||  $user->id ==719) { ?>
                            <li class="<?php echo ($menu_name == 'travel_request_sent_form') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminrequest/travel_request_sent_form'); ?>"><i class="fa fa-circle-o"></i>Travel History Form</a></li>
                        <?php } ?>    
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1 ||  $user->id ==719) { ?>
                            <li class="<?php echo ($menu_name == 'familytree_request_sent_form') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminrequest/familytree_request_sent_form'); ?>"><i class="fa fa-circle-o"></i>Family Tree Form</a></li>
                        <?php } ?>    
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1 ||  $user->id ==719) { ?>
                            <li class="<?php echo ($menu_name == 'nadra_bulk_request_sent_form') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminrequest/nadra_bulk_request_sent_form'); ?>"><i class="fa fa-circle-o"></i>Nadra Bulk Requests Form</a></li>
                        <?php } ?>

                    </ul>
                </li>
            <?php } ?>
            <?php if ((Helpers_Utilities::chek_role_array_access($role_id, array(34,35)) == 1) || $user->id ==171 ||  $user->id ==170) { ?>
                <?php
                    $databank_active =
                        ($current_url == 'Admindatabank' && in_array($menu_name, array(
                            'bulk_nadra_requests_databank',
                            'nadra_requests_reports_databank',
                            'breakup_report',
                            'data_upload_against_msisdn',
                            'msisdn_requests_reports_databank',
                            'msisdn_breakup_report',
                            'msisdn_breakup_report_individual',
                            'msisdn_no_request_send_reports_detail',
                        )))
                        || ($current_url == 'Databank' && in_array($menu_name, array(
                            'ecp_address',
                            'mobile_subscriber',
                            'foreigner',
                        )));
                ?>
                <li class="treeview <?php echo $databank_active ? 'active' : ''; ?>">
                    <a href="#">
                        <i class="fa  fa-database"></i>
                        <span>DRAMS Databank</span>
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">

                        <!-- External-DB search pages (Controller_Databank) -->
                        <li class="<?php echo ($current_url == 'Databank' && $menu_name == 'mobile_subscriber') ? 'active' : ''; ?>">
                            <a href="<?php echo URL::site('databank/mobile_subscriber'); ?>"><i class="fa fa-mobile"></i>Mobile Subscriber Search</a>
                        </li>
                        <li class="<?php echo ($current_url == 'Databank' && $menu_name == 'foreigner') ? 'active' : ''; ?>">
                            <a href="<?php echo URL::site('databank/foreigner'); ?>"><i class="fa fa-globe"></i>Foreigner Account Search</a>
                        </li>
                        <li class="<?php echo ($current_url == 'Databank' && $menu_name == 'ecp_address') ? 'active' : ''; ?>">
                            <a href="<?php echo URL::site('databank/ecp_address'); ?>"><i class="fa fa-search"></i>ECP Address Search</a>
                        </li>

                        <li class="header" style="padding:6px 15px; color:#8aa4af; font-size:11px; text-transform:uppercase;">Reports &amp; Uploads</li>

                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'bulk_nadra_requests_databank') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Admindatabank/bulk_nadra_requests_databank'); ?>"><i class="fa fa-circle-o"></i>Nadra Databank </a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'nadra_requests_reports_databank') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Admindatabank/nadra_requests_reports_databank'); ?>"><i class="fa fa-circle-o"></i>Nadra Reports </a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'breakup_report') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Admindatabank/breakup_report'); ?>"><i class="fa fa-circle-o"></i>Nadra Breakup Reports </a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'data_upload_against_msisdn') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Admindatabank/data_upload_against_msisdn'); ?>"><i class="fa fa-circle-o"></i>Old Databank(MSISDN) </a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'msisdn_requests_reports_databank') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Admindatabank/msisdn_requests_reports_databank'); ?>"><i class="fa fa-circle-o"></i>MSISDN Reports </a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 34) == 1) { ?>
                            <li class="<?php echo ($menu_name == 'msisdn_breakup_report' || ($menu_name == 'msisdn_breakup_report_individual'|| $menu_name == 'msisdn_no_request_send_reports_detail') ) ? 'active' : ''; ?>"><a href="<?php echo URL::site('Admindatabank/msisdn_breakup_report'); ?>"><i class="fa fa-circle-o"></i>MSISDN Breakup Reports </a></li>
                        <?php } ?>



                    </ul>
                </li>
            <?php } ?>
<!--            --><?php //if (Helpers_Utilities::chek_role_array_access($role_id, array(34,35)) == 1) { ?>
<!--                <li class="treeview --><?php //echo (($current_url == 'Adminrequest' && ($menu_name == 'nadra_request_sent_form' ))) ? 'active' : ''; ?><!--">-->
<!--                    <a href="#">-->
<!--                        <i class="fa  fa-send"></i>-->
<!--                        <span>Nadra Requests</span>-->
<!--                        <span class="pull-right-container">-->
<!--                            <i class="fa fa-angle-left pull-right"></i>-->
<!--                        </span>-->
<!--                    </a>-->
<!--                    <ul class="treeview-menu">-->
<!--                        --><?php //if (Helpers_Utilities::chek_role_access($role_id, 34) == 1) { ?>
<!--                            <li class="--><?php //echo ($menu_name == 'nadra_request_sent_form') ? 'active' : ''; ?><!--"><a href="--><?php //echo URL::site('Adminrequest/nadra_request_sent_form'); ?><!--"><i class="fa fa-circle-o"></i>Nadra Request Form</a></li>-->
<!--                        --><?php //} ?>
<!---->
<!--                        --><?php ////if (Helpers_Utilities::chek_role_access($role_id, 35) == 1) { ?>
<!--                            <li class="--><?php ////echo ($menu_name == 'nadra_sent_request_status') ? 'active' : ''; ?><!--"><a href="--><?php ////echo URL::site('Adminrequest/nadra_sent_request_status'); ?><!--"><i class="fa fa-circle-o"></i> Nadra Request Status</a></li>-->
<!--                        --><?php ////} ?>
<!---->
<!--                    </ul>-->
<!--                </li>-->
<!--            --><?php //} ?>

            <?php if (Helpers_Utilities::chek_role_array_access($role_id, array(41,42,43,44,45,46,47,48,49,50,51,52)) == 1) { ?>
            <li class="treeview <?php echo ($menu_name == 'users_favourite_user' || $menu_name == 'users_favourite_agent' || $menu_name == 'user_favourite_person' || $menu_name == 'user_favourite_person_list' || $menu_name == 'no_request_send' || $menu_name == 'no_request_send_type' || $menu_name == 'no_request_send_detail' || $menu_name == 'no_record_search' || $menu_name == 'no_of_login' || $menu_name == 'audit_report_basic' || $menu_name == 'audit_report' || $menu_name == 'performance_report' || $menu_name == 'users_list' || $menu_name == 'users_list_blocked' || $menu_name == 'users_list_new' || $menu_name == 'request_breakup_report' || $menu_name=='users_transferred_list' || $menu_name == 'request_breakup_district' || $menu_name == 'request_type_breakup_district' || ($current_url == 'Adminreports' && $menu_name == 'verisys_response_report') ) ? 'active' : ''; ?>">
                <a href="#">
                    <i class="fa fa-user"></i>
                    <span>User's Report</span> 
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 41) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'users_favourite_user' || $menu_name == 'users_favourite_agent') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userreports/users_favourite_user'); ?>"><i class="fa fa-circle-o text-yellow"></i> User's Favourite User</a></li>
                    <?php } ?>
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 42) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'user_favourite_person' || $menu_name == 'user_favourite_person_list') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userreports/user_favourite_person'); ?>"><i class="fa fa-circle-o text-yellow"></i> User's Favourite Persons</a></li>
                    <?php } ?>
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 43) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'no_request_send' || $menu_name == 'no_request_send_type' || $menu_name == 'no_request_send_detail') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userreports/no_request_send'); ?>"><i class="fa fa-circle-o text-yellow"></i>Request Sent Log</a></li>
                    <?php } ?>
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 44) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'no_record_search') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userreports/no_record_search'); ?>"><i class="fa fa-circle-o text-yellow"></i> Record Search Log </a></li>
                    <?php } ?>
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 45) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'no_of_login') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userreports/no_of_login'); ?>"><i class="fa fa-circle-o text-yellow"></i> No. of Time Login/out</a></li>
                    <?php } ?>
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 46) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'audit_report' || $menu_name == 'audit_report_basic') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userreports/audit_report_basic'); ?>"><i class="fa fa-circle-o text-yellow"></i> Audit Report</a></li>
                    <?php } ?>
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 47) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'performance_report') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userreports/performance_report'); ?>"><i class="fa fa-circle-o text-yellow"></i> Performance Report</a></li>
                    <?php } ?>
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 48) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'users_list') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userreports/users_list'); ?>"><i class="fa fa-circle-o text-yellow"></i> User's List</a></li>
                    <?php } ?>
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 48) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'users_transferred_list') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userreports/users_transferred_list'); ?>"><i class="fa fa-circle-o text-yellow"></i> Transferred Users</a></li>
                    <?php } ?>
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 49) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'users_list_blocked') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userreports/users_list_blocked'); ?>"><i class="fa fa-circle-o text-yellow"></i> Blocked Users</a></li>
                    <?php } ?>                    
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 50) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'users_list_new') ? 'active' : ''; ?>"><a href="<?php echo URL::site('userreports/users_list_new'); ?>"><i class="fa fa-circle-o text-yellow"></i> New Users Approval</a></li>
                    <?php } ?>
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 51) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'verisys_response_report') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Adminreports/verisys_response_report'); ?>"><i class="fa fa-circle-o"></i>Verisys Report </a></li>                    
                    <?php } ?>
                    <?php if (Helpers_Utilities::chek_role_access($role_id, 52) == 1) { ?>
                        <li class="<?php echo ($menu_name == 'request_breakup_report' || $menu_name == 'request_breakup_district' || $menu_name == 'request_type_breakup_district') ? 'active' : ''; ?>"><a href="<?php echo URL::site('Userreports/request_breakup_report'); ?>"><i class="fa fa-circle-o"></i>Requests Breakup Report </a></li>                    
                    <?php } ?>
                </ul>
            </li>
            <?php } ?>
            <?php if (Helpers_Utilities::chek_role_array_access($role_id, array(53,54,55,56,60)) == 1) { ?>
                <li class="treeview <?php echo ($menu_name == 'person_list' ||$menu_name == 'person_category_wise_list' || $menu_name == 'top_search_persons' || $menu_name == 'project_persons' || $menu_name == 'sensitive_person_list' || $menu_name == 'person_breakup_report' || $menu_name == 'person_breakup_district'  || $menu_name == 'person_list_district'|| $menu_name == 'person_call_analysis') ? 'active' : ''; ?>">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span>Persons's Report</span> 
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">   
                        <?php if($posting != 'r-33' && $posting != 'r-25'){  ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 53) == 1) { ?>
                        <li class="treeview <?php echo ($menu_name == 'top_search_persons') ? 'active' : ''; ?>"><a href="<?php echo URL::site('personsreports/top_search_persons'); ?>"><i class="fa fa-search text-aqua"></i><span>Top Searched Person </span></a></li>
                        <?php } ?>
                        <?php } ?>
                        
                        
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 54) == 1) { ?>
                        <li class="treeview <?php echo ($menu_name == 'person_list') ? 'active' : ''; ?>"><a href="<?php echo URL::site('personsreports/person_list'); ?>"><i class="fa fa-user text-aqua"></i><span>Person's List </span></a></li> 
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 54) == 1) { ?>
                        <li class="treeview <?php echo ($menu_name == 'person_category_wise_list') ? 'active' : ''; ?>"><a href="<?php echo URL::site('personsreports/person_category_wise_list'); ?>"><i class="fa fa-user text-aqua"></i><span>Person's Category Wise List </span></a></li>
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 55) == 1) { ?>
                        <li class="treeview <?php echo ($menu_name == 'project_persons') ? 'active' : ''; ?>"><a href="<?php echo URL::site('personsreports/project_persons'); ?>"><i class="fa fa-user text-aqua"></i><span>Project Affiliated persons </span></a></li> 
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 56) == 1) { ?>
                        <li class="treeview <?php echo ($menu_name == 'sensitive_person_list') ? 'active' : ''; ?>"><a href="<?php echo URL::site('personsreports/sensitive_person_list'); ?>"><i class="fa fa-list text-aqua"></i><span>Sensitive Person's List </span></a></li> 
                        <?php } ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 60) == 1) { ?>
                        <li class="treeview <?php echo ($menu_name == 'person_breakup_report' || $menu_name == 'person_breakup_district' || $menu_name == 'person_list_district') ? 'active' : ''; ?>"><a href="<?php echo URL::site('personsreports/person_breakup_report'); ?>"><i class="fa fa-book text-aqua"></i><span>New Person's List </span></a></li> 
                        <?php } ?>
                        <?php if($posting != 'r-33' && $posting != 'r-25'){  ?>
                        <?php if (Helpers_Utilities::chek_role_access($role_id, 54) == 1) { ?>
                        <li class="treeview <?php echo ($menu_name == 'person_call_analysis') ? 'active' : ''; ?>"><a href="<?php echo URL::site('personsreports/person_call_analysis'); ?>"><i class="fa fa-reddit-alien text-aqua"></i><span>Call Analysis </span></a></li>
                        <?php } ?>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php if (Helpers_Utilities::chek_role_access($role_id, 57) == 1) { ?>
                <li class="treeview <?php echo ($menu_name == 'panel_log') ? 'active' : ''; ?>">
                    <a href="<?php echo URL::site('userreports/panel_log'); ?>">
                        <i class="fa fa-odnoklassniki"></i>
                        <span>Panel Log/History</span>            
                    </a>          
                </li>
                
               
                 <li class="treeview <?php echo ($menu_name == 'panel_log_officewise') ? 'active' : ''; ?>">
                    <a href="<?php echo URL::site('userreports/panel_log_officewise'); ?>">
                        <i class="fa fa-odnoklassniki"></i>
                        <span>Panel Log/Office Wise History</span>            
                    </a>          
                </li>
                
                
            <?php } ?>
            <?php if (Helpers_Utilities::chek_role_access($role_id, 58) == 1) { ?>
                <li class="treeview <?php echo ($menu_name == 'url_hits_log') ? 'active' : ''; ?>">
                    <a href="<?php echo URL::site('userreports/url_hits_log'); ?>">
                        <i class="fa fa-odnoklassniki"></i>
                        <span>URL Hits Log/History</span>            
                    </a>          
                </li>
            <?php } ?>            
            
        </ul>
    </section>
    <!-- /.sidebar -->
                    
</aside>
