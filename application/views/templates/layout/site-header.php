<?php
/*
 * View to load by Helpers_Layout -> get_header
 */
?>
<header class="main-header">
    <a href="<?php echo URL::site('Userdashboard/dashboard'); ?>" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><!--<img src="<?php echo URL::base(); ?>dist/img/logo-2.png" alt="logo">--></span>
        <!-- logo for regular state and mobile devices -->
<!--        <span class="logo-lg"><img style="width: 208px;" src="<?php echo URL::base(); ?>dist/img/icon/logo.png" alt="logo"></span>-->
        <span class="logo-lg" style="margin:auto;"><!--<img style="width: 100%;height:45px" src="<?php echo URL::base(); ?>dist/img/logo-2.png" alt="logo">-->
        
        </span>
    </a>

    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <a href="javascript:;" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <span class="logo-lg" style="margin: auto;color: white;font-size: 30px;font-weight: inherit;">
            Digital Records Analysis & Monitoring System
        </span>
        <?php try{
        $user = Auth::instance()->get_user();
        $user_data = Helpers_Utilities::get_user_name($user->id);
        $cpr = Helpers_Profile::count_password_requests();
        $pr = Helpers_Profile::password_requests();
        $permission = Helpers_Utilities::get_user_permission($user->id);
        }  catch (Exception $ex){ //echo 'Error! Please contact to SES!'; exit;   }
            
        }
?>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav"> 
                <!-- Notifications: style can be found in dropdown.less -->
                <li class="dropdown notifications-menu">
                    <a href="<?php echo URL::base(TRUE) .('Adminreports/password_reset'); ?>"> <!-- class="dropdown-toggle" data-toggle="dropdown">-->
                        <i class="fa fa-bell-o"></i>
                        <span class="label label-warning"><?php echo $cpr; ?></span>
                    </a>
                    <?php
                    if($permission==1 ){  ?>
                    <ul class="dropdown-menu">
                        <li class="header">You have<?php echo " " . $cpr . " "; ?>notifications</li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu">  
                                <?php
                                $ct = 1;
                                foreach ($pr as $p) {
                                    if ($ct <= 5) {
                                        ?>                                            
                                        <li class="list-group-item">
                                            <span>
                                                <a href="#" class="chagne-st-btn2">
                                                    <i class="fa fa-user text-red " style="margin-right: 5px; margin-left: 5px"></i>
                                                        <?php echo $p->username . "(" . $p->email . ") "; ?>has requested for password change.
                                                </a>
                                            </span>
                                        </li>
                                        <?php
                                    } $ct++;
                                }
                                ?>  
                                <!--                                             <button id="cpr" name="cpr" class="btn btn-primary btn-xs pull-right chagne-st-btn" style="margin-left: 5px;margin-right: 5px">Proceed</button>-->
                            </ul>
                        </li>
                        <li class="footer chagne-st-btn2"><a href="#">View all</a></li>
                    </ul>
                    <?php } ?>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php try{
                        $user_profile = Helpers_Profile::get_user_perofile($user->id);
                        $user_first_letter = Helpers_Profile::get_first_letters($user->id);
                        ?>
                        <span class="name-letter"><?php echo $user_first_letter; }  catch (Exception $ex){   }?></span>

                        <span class="hidden-xs"><?php echo $user_data; ?>
                            <i style="margin-top: 4px;" class="fa fa-angle-double-down pull-right"></i>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <span class="name-letter user-top-pic"><?php echo $user_first_letter; ?></span>
                            <p>
                                <?php
                                echo $user_data . '- ' . $user_profile->job_title;
                                ?>
                                <small>Member since <?php
                                    echo date("M Y", strtotime($user_profile->created_at));
                                    ?></small>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                         <li class="user-footer">
                             <div class="col-md-2" style="padding-left:0px">
                                <a   style="padding-left: 5px; padding-right: 5px;" href="<?php echo URL::site('user/user_profile') . '/' . Helpers_Utilities::encrypted_key($user->id,"encrypt"); ?>" class="btn btn-default btn-flat">Profile</a>
                            </div>
                            
                            <div class="col-md-6">
                                <a  style="padding-left: 5px; padding-right: 5px;" href="<?php echo URL::site('user/changepassword'); ?>" class="btn btn-default btn-flat">Change Password</a>
                            </div>                            
                            <div class="col-md-3" style="padding-left:10px">
                                <a   style="padding-left: 5px; padding-right: 5px;" href="<?php echo URL::site('user/logout'); ?>" class="btn btn-default btn-flat">Sign out</a>
                            </div>
                            <!--irfan code end-->
                        </li>
                    </ul>
                </li>          
            </ul>
        </div>
    </nav>
</header>

<div class="modal modal-info fade" id="modal-default_not">
    <div class="modal-dialog modal-lg">
        <div class="modal-content notification">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: red">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">You have<?php echo " " . $cpr . " "; ?>notifications</h4>
            </div>
            <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>
                <div class="alert alert-success alert-dismissible" id="notification_msg">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4>
                        <div id="notification_msg_div"> 
                      
                        </div>
                    </h4>

                </div>
                <table class="table table-bordered table-striped table-responsive">
                    <tr>
                        <th>Notification</th>
                        <th>Action</th>
                    </tr> 
                    <?php 
                    if($permission==1 ){  
                    foreach ($pr as $p) {
                        ?>  
                        <tr class="notify-<?php echo $p->id; ?>">
                            <td>                                
                                <span><a href="<?php echo URL::site('user/user_profile/' . Helpers_Utilities::encrypted_key($p->id,"encrypt")); ?>"><i class="fa fa-user text-red" style="margin-right: 5px; margin-left: 5px"></i><?php echo $p->username . "(" . $p->email . ") "; ?>has requested for password change.</a> </span>
                                <div id="notif-<?php echo $p->id; ?>" class="collapse">                                        
                                    <div class="col-lg-8">
                                        <input type="hidden" id="notify_id<?php echo $p->id; ?>" name="notify_id<?php echo $p->id; ?>" value="<?php echo $p->id; ?>" />
                                        <input style="margin-top: 5px" type="text" class="btn-xs pull-right" id="notify_pd<?php echo $p->id; ?>" name="notify_pd<?php echo $p->id; ?>" placeholder="Enter New Password">
                                    </div>
                                    <div class="col-lg-4">
                                        <a class="btn btn-primary btn-xs" href="javascript:change('<?php echo $p->id; ?>')" style="margin-top: 5px">Change</a>
                                    </div>                                        
                                </div>                               
                            </td>
                            <td>
                                <a  href="javascript:inactive('<?php echo $p->id; ?>')" id="cprc" name="cprc" class="btn btn-danger btn-primary btn-xs " style="margin-left: 5px;margin-right: 5px">Block User</a>
                                <button  data-toggle="collapse" data-target="#notif-<?php echo $p->id; ?>" id="cpr" name="cpr" class="btn btn-primary btn-xs" style="margin-left: 5px;margin-right: 5px">Proceed</button>
                                <a  href="javascript:cancelrequest('<?php echo $p->id; ?>')" id="changepswdclear" name="changepswdclear" class="btn btn-warning btn-primary btn-xs " style="margin-left: 5px;margin-right: 5px">Cancel</a>                                                                    
                            </td>
                        </tr>
                    <?php } 
                    }
                    ?>  

                </table>                          
            </div>
            <div class="modal-footer">
                <!--                <button type="button" class="btn btn-primary btn-danger">Close</button>-->
            </div>
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    function showDiv(elem) {
        var abc = document.getElementById('div_pwd').style.display;
        if (abc === "none")
        {
            //Hide
            document.getElementById('div_pwd').style.display = "block";
        } else {
            //show
            document.getElementById('div_pwd').style.display = "none";
        }
    }
</script>
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
    $(document).ready(function () {
        $("#notification_msg").hide();
        $("body").on("click", ".chagne-st-btn2", function () {

            $("#modal-default_not").modal("show");

            //appending modal background inside the blue div
            $('.modal-backdrop').appendTo('.blue');

            //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
            $('body').removeClass("modal-open")
            $('body').css("padding-right", "");
            setTimeout(function () {
                // Do something after 1 second     
                $(".modal-backdrop.fade.in").remove();
            }, 300);


        });

    });
    //block user
    function inactive(id) {
        $.confirm({
            'title': 'Block user confirmation',
            'message': 'Do you really want to Block this user?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({
                            url: "<?php echo URL::site("user/useractive"); ?>" + "/" + id,
                            cache: false,
                            success: function (msg) {
                                var elem = $(".notify-" + id);
                                elem.slideUp();
                                //$("#notification_msg_div").html('Successfully deleted');
                                $("#notification_msg").show();
                            }
                        });
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {}  // Nothing to do in this case. You can as well omit the action property.
                    }
            }
        });
    }
    //Cancel password reset request
    function cancelrequest(id) {
        $.confirm({
            'title': 'Cancel request confirmation',
            'message': 'Do you really want to cancel password reset request?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({
                            url: "<?php echo URL::site("user/request_cancel"); ?>" + "/" + id,
                            cache: false,
                            success: function (msg) {
                                var elem = $(".notify-" + id);
                                elem.slideUp();
                                //$("#notification_msg_div").html('Request ');
                                //$("#notification_msg").show();
                            }
                        });
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    
    
    function change(id) {
        var newpassword = $("#notify_pd" + id).val();
        var n = newpassword.length;
        //alert(newpassword);
       if(newpassword=="" || newpassword==0 || n < 8){
           $("#notification_msg_div").html('Valid eight character password required');
                $("#notification_msg").show();
                
           var elem = $("#notification_msg");
                elem.slideUp(5000);
        }else{
        var result = {newpassword: newpassword, id: id}
        $.ajax({
            url: "<?php echo URL::site("user/change"); ?>",
            type: 'POST',
            data: result,
            cache: false,
            dataType: "text",
            success: function (msg) {
                var elem = $(".notify-" + id);
                elem.slideUp();
                $("#notification_msg_div").html('Successfully Password Change');
                $("#notification_msg").show();
            }
        });
        }
    }
</script>  