<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if(!isset($_GET['id']))
{     
 //  header("Location:" . URL::base() . "errors?_e=wrong_parameters");
  // exit;
 }
 try{
    $sidebar_user = Auth::instance()->get_user();
    $sidebar_user_name = Helpers_Utilities::get_user_name($sidebar_user->id);
    $sidebar_user_initials = Helpers_Profile::get_first_letters($sidebar_user->id);
 }  catch (Exception $ex){
     $sidebar_user_name = '';
     $sidebar_user_initials = '';
 }
?>
<!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <?php if(!empty($sidebar_user_name)){ ?>
                    <div class="user-panel">
                        <div class="pull-left image">
                            <span class="name-letter">
                                <?php echo $sidebar_user_initials; ?>
                            </span>
                        </div>
                        <div class="pull-left info">
                            <p>
                                <?php echo $sidebar_user_name; ?>
                            </p>
                            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                        </div>
                    </div>
                    <?php } ?>     
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">       
 						<li class="header"> <span id="date_time"></span></li>					
                        <li class="treeview">
                            <a href="<?php echo URL::site('Userdashboard/dashboard'); ?>">
                                <i class="fa fa-rotate-left text-aqua"></i> <span>Back</span>            
                            </a>          
                        </li>

                        <li class="treeview <?php echo ($menu_name=='dashboard')?'active':''; ?>">
                            <a href="<?php echo URL::site('persons/dashboard/?id=' . $_GET['id']); ?>">
                                <i class="fa fa-dashboard text-aqua"></i> <span>Person Dashboard</span>            
                            </a>          
                        </li>                                                                                                
                        <li class="treeview <?php echo ($menu_name=='branchless_transactions')?'active':''; ?>"><a href="<?php echo URL::site('persons/branchless_transactions/?id=' . $_GET['id']); ?>">
                                <i class="fa  fa-lock text-aqua"></i> <span>Branchless Transactions</span>
                            </a>
                        </li>                                                                        
                        
                        <li class="treeview <?php echo ($menu_name=='call_summary' || $menu_name=='call_summary_detail' || $menu_name=='sms_summary' || $menu_name=='sms_summary_detail' || $menu_name=='cdr_summary' || $menu_name=='shortcode_analysis' || $menu_name=='b_party' || $menu_name=='cdr_report_Detail'|| $menu_name=='bparty_subscriber' )?'active':''; ?>">
                            <a href="#">
                                <i class="fa fa-user text-aqua"></i>
                                <span>Cell Summary</span> 
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="<?php echo ($menu_name=='call_summary' || $menu_name=='call_summary_detail')?'active':''; ?>" ><a href="<?php echo URL::site('persons/call_summary/?id=' . $_GET['id']); ?>"><i class="fa fa-circle-o text-yellow"></i> Call Summary</a></li>
                                <li class="<?php echo ($menu_name=='sms_summary' || $menu_name=='sms_summary_detail')?'active':''; ?>"><a href="<?php echo URL::site('persons/sms_summary/?id=' . $_GET['id']); ?>"><i class="fa fa-circle-o text-yellow"></i> SMS Summary</a></li>
                                <li class="<?php echo ($menu_name=='cdr_summary' || $menu_name=='cdr_report_Detail')?'active':''; ?>"><a href="<?php echo URL::site('persons/cdr_summary/?id=' . $_GET['id']); ?>"><i class="fa fa-circle-o text-yellow"></i> CDR Summary </a></li>
                                <li class="<?php echo ($menu_name=='shortcode_analysis' )?'active':''; ?>"><a href="<?php echo URL::site('persons/shortcode_analysis/?id=' . $_GET['id']); ?>"><i class="fa fa-circle-o text-yellow"></i> Short Code Analysis </a></li>
                                <li class="<?php echo ($menu_name=='b_party' )?'active':''; ?>"><a href="<?php echo URL::site('persons/b_party/?id=' . $_GET['id']); ?>"><i class="fa fa-circle-o text-yellow"></i> B-Party Summary </a></li>
                                <li class="<?php echo ($menu_name=='bparty_subscriber' )?'active':''; ?>"><a href="<?php echo URL::site('persons/bparty_subscriber/?id=' . $_GET['id']); ?>"><i class="fa fa-circle-o text-yellow"></i> B-Party Subscriber(Slow) </a></li>

                            </ul>
                        </li>

                        <li class="treeview <?php echo ($menu_name == 'location_call_log' || $menu_name == 'physical_location_summary') ? 'active' : '' ; ?>">
                            <a href="#">
                                <i class="fa fa-map text-aqua"></i>
                                <span>Location Summary</span> 
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="treeview <?php echo ($menu_name=='physical_location_summary')?'active':''; ?>"><a href="<?php echo URL::site('persons/physical_location_summary/?id=' . $_GET['id']); ?>"><i class="fa fa-map-signs text-aqua"></i> <span>Physical Location</span></a></li>                                
                                <li class="treeview <?php echo ($menu_name=='location_call_log')?'active':''; ?>"><a href="<?php echo URL::site('persons/location_call_log/?id=' . $_GET['id']); ?>"><i class="fa fa-map-marker text-aqua"></i> <span>Google Map Location</span></a></li>                                
                            </ul>
                        </li>
                        <li class="treeview <?php echo ($menu_name=='cell_log_summary')?'active':''; ?>">
                            <a href="<?php echo URL::site('persons/cell_log_summary/?id=' . $_GET['id']); ?>">
                                <i class="fa fa-mobile-phone text-aqua"></i><span>Call Log</span>
                            </a>
                        </li>
                        <li class="treeview <?php echo ($menu_name=='sms_log_summary')?'active':''; ?>"><a href="<?php echo URL::site('persons/sms_log_summary/?id=' . $_GET['id']); ?>">
                                <i class="fa  fa-inbox text-aqua"></i> <span>SMS Log</span>
                            </a>
                        </li>
                        <li class="treeview <?php echo ($menu_name == 'cdr_graphic') ? 'active' : ''; ?>">
                            <a href="<?php echo URL::site('persons/cdr_graphic/?id=' . $_GET['id']); ?>"><i class="fa fa-street-view text-aqua"></i> <span>CDR Graphical View</span></a>
                        </li>
                        <li class="treeview <?php echo ($menu_name=='person_favourite_person')?'active':''; ?>">
                            <a href="<?php echo URL::site('persons/person_favourite_person/?id=' . $_GET['id']); ?>"><i class="fa fa-users text-aqua"></i> <span>Person's Favourite Persons</span></a>
                        </li>                        
                        <li class="treeview <?php echo ($menu_name=='person_db_match')?'active':''; ?>">
                            <a href="<?php echo URL::site('persons/person_db_match/?id=' . $_GET['id']); ?>"><i class="fa fa-exchange text-aqua"></i> <span>Person's DB Match</span></a>
                        </li>                        
                        <li class="treeview <?php echo ($menu_name=='person_affiliation')?'active':''; ?>">
                            <a href="<?php echo URL::site('persons/person_affiliation/?id=' . $_GET['id']); ?>"><i class="fa fa-link text-aqua"></i> <span>Link With Affiliated Persons</span></a>
                        </li>
                        <li class="treeview <?php echo ($menu_name=='other_portals_links')?'active':''; ?>">
                            <a href="<?php echo URL::site('socialanalysis/other_portals_links?id='. $_GET['id']); ?>"><i class="fa fa-external-link text-aqua"></i> <span>Other Portal Records</span></a>
                        </li>
                       
                        <?php $menu_arrar_profile = array('person_pictures', 'person_verisys'); ?>
                        <li class="treeview  <?php echo (in_array($menu_name , $menu_arrar_profile)) ? 'active' : ''; ?>">
                            <a href="#"> <i class="fa  fa-user text-aqua"></i><span>Person Profile Details</span> 
                                <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                            </a>
                            <ul class="treeview-menu">
                                <!--Person verisys manu-->
                                <li class="<?php echo ($menu_name == 'person_verisys') ? 'active' : ''; ?>">
                                    <a href="<?php echo URL::site('personprofile/person_verisys?id='. $_GET['id']); ?>">
                                        <i class="fa fa-file-picture-o"></i>Person Verisys
                                    </a>
                                </li>
                                <li class="<?php echo ($menu_name == 'person_ftree_update') ? 'active' : ''; ?>">
                                    <a href="<?php echo URL::site('personprofile/person_ftree_update?id='. $_GET['id']); ?>">
                                        <i class="fa fa-file-picture-o"></i>Person Family Tree
                                    </a>
                                </li>
<!--                                <li class="<?php /* echo ($menu_name == 'person_pictures') ? 'active' : ''; */?>">
                                    <a href="<?php /* echo URL::site('personprofile/person_pictures?id='. $_GET['id']); */?>">
                                        <i class="fa fa-picture-o"></i>Person Pictures
                                    </a>
                                </li>-->
                            </ul>
                        </li>
                        <li class="treeview  <?php echo ($menu_name=='social_links' || $menu_name=='add_social_link' || $menu_name=='edit_social_link') ? 'active' : ''; ?>">
                            <a href="#">
                                <i class="fa  fa-list text-aqua"></i>
                                <span>Social Analysis</span> 
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>

                            <ul class="treeview-menu">
                                <li class="<?php echo ($menu_name == 'social_links') ? 'active' : ''; ?>"><a href="<?php echo URL::site('socialanalysis/social_links?id='. $_GET['id']); ?>"><i class="fa fa-mars-double"></i> Social Links</a></li>
                                <li class="<?php echo ($menu_name == 'add_social_link' || $menu_name=='edit_social_link') ? 'active' : ''; ?>"><a href="<?php echo URL::site('socialanalysis/add_social_link/?id='. $_GET['id']); ?>"><i class="fa fa-user"></i> Add Social Link</a></li>            
                            </ul>
                        </li>
                        <?php 
                        $user = Auth::instance()->get_user();
                        $permission = Helpers_Utilities::get_user_permission($user->id);
                        ?>
                        
                        <li class="treeview <?php echo ($menu_name=='one_page_performa')?'active':''; ?>">
                            <a href="<?php echo URL::site('persons/one_page_performa/?id=' . $_GET['id']); ?>"><i class="fa fa-book text-aqua"></i> <span>One Page Performa</span></a>
                        </li>

                        
                        <?php if ($permission == 1 || $permission == 2 ||$permission == 5) { ?> 
                        <li class="treeview <?php echo ($menu_name=='user_activity_log')?'active':''; ?>">
                            <a href="<?php echo URL::site('persons/user_activity_log/?id=' . $_GET['id']); ?>"><i class="fa fa-lock text-aqua"></i> <span>User Activity Log</span></a>
                        </li>
                        <?php } ?>
                        <li class="treeview <?php echo ($menu_name=='users_feedback')?'active':''; ?>">
                            <a href="<?php echo URL::site('persons/users_feedback/?id=' . $_GET['id']); ?>"><i class="fa fa-history text-aqua"></i> <span>Users Feedback/History</span></a>
                        </li>
                    </ul>
                    
                </section>
                <!-- /.sidebar -->
            </aside>
