<?php
//echo '<pre>';
//print_r('test');
//exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//get person assets dowload path
$person_download_data_path = !empty(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) ? Helpers_Person::get_person_download_data_path(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) : '';

$personage = '';


?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Person's Portal
        <small><?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Persons Dashboard</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <?php
    if (isset($_GET["message"]) && $_GET["message"] == 1) {
        ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-check"></i> Congratulation! Information Successfully Added.</h4>
        </div>
    <?php } ?>
    <?php
    if (isset($_GET["nadraerror"])) {
        ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-check"></i> <?php echo $_GET['nadraerror']; ?></h4>
        </div>
    <?php } ?>
    <?php
    if (isset($_GET["tag_message"]) && $_GET["tag_message"] == 1) {
        ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-check"></i> Congratulation! Information Successfully Added.</h4>
        </div>
    <?php } ?>
    <?php
    try {
        $prof = Helpers_Person::get_person_for_dashboard_perofile($person_id);
//echo '<pre>';
//print_r($prof);
//exit;
        $cat_id = Helpers_Person::get_person_category_id($person_id);
        $nic_number = !empty($person_id) ? Helpers_Person::get_person_cnic($person_id) : '';
        $cat_name = Helpers_Utilities::get_category_name($cat_id);
      //  $first_name = !empty($prof->first_name) ? $prof->first_name : " ";
        $first_name = !empty($prof['first_name']) ? $prof['first_name'] : " ";

        $middle_name = !empty($prof['middle_name']) ? $prof['middle_name'] : " ";
        $father_name = !empty($prof['father_name']) ? $prof['father_name'] : " ";
        $last_name = !empty($prof['last_name']) ? $prof['last_name'] : " ";
        $address = !empty($prof['address']) ? $prof['address'] : " ";
        //$person_id = !empty($prof->person_id) ? $prof->person_id : " ";
        $imageurl = !empty($prof->image_url) ? $prof->image_url : "NA";
        //to check if person is foreginer
        $isforeigner = !empty($person_id) ? Helpers_Utilities::check_is_foreigner($person_id) : -7;
        if ($isforeigner == 1) {
            //get person foreigner profile
            $profnadra = Helpers_Person::get_person_foreigner_perofile($person_id);
        } else {
            //get person nadra profile
            $profnadra = Helpers_Person::get_person_nadra_perofile($person_id);
        }
//       echo '<pre>';
//       print_r($person_id);
//       exit;
        $verisisimg1 = !empty($profnadra->cnic_image_url) ? $profnadra->cnic_image_url : "NA";
        $famtreeimg1 = !empty($profnadra->family_image_url) ? $profnadra->family_image_url : "NA";
        //get person date of birth
        // this date of birth was fetched from nadra profile but now nadra profile not availabe so get date of birth from person_detail_info
        /* $datofbirth = !empty($profnadra->person_dob) ? $profnadra->person_dob : "NA"; */
        
        $detailinfo = Helpers_Person::get_person_detail_info($person_id);

        $datofbirth = !empty($detailinfo->dob) ? $detailinfo->dob : "";
        $personage = (!empty($datofbirth) && $datofbirth != "NA") ? Helpers_Person::get_age($datofbirth) : "NA";

    } catch (Exception $ex) {
        echo '<pre>';
        print_r($ex);
        exit;
        
    }
    ?>
    <!-- title row -->
    <div class="row">
        <?php
        try {
            //get login user id
            $user_obj = Auth::instance()->get_user();
            $login_user_id = $user_obj->id;
            $of_id=Helpers_Utilities::get_user_place_of_posting($login_user_id);
            $flagfavourite = Helpers_Person::is_favourite_person($login_user_id, $person_id);
        } catch (Exception $ex) {
            
        }
        ?>
        <div class="col-md-4 bg-aqua">
            <h2 class="page-header ">
                <i class="fa fa-globe"></i> <?php
                try {
                    echo Helpers_Utilities::custom_echo($first_name . " " . $middle_name . " " . $last_name, 27);
                } catch (Exception $ex) {
                    
                }
                ?>
            </h2>
        </div>
        <?php
        try {
            $user_obj = Auth::instance()->get_user();
            $permission = Helpers_Utilities::get_user_permission($user_obj->id);
            if ($permission == 1 || $permission == 3 || $permission==5) {

                $flagsensitive = Helpers_Person::is_sensitive_person($login_user_id, $person_id)
                ?>
                <div class="col-md-2 bg-aqua">
                    <h2 class="page-header">
                        Favourite: 
                        <span id="favt" class="pull-right">
                            <?php if ($flagfavourite == 'TRUE') { ?> 
                                <a class="bg-aqua" href="javascript:ConfirmChoiceUnmark(<?php echo $person_id; ?>)"> <i class="fa fa-star"></i> </a>
                            <?php } else { ?>
                                <a class="bg-aqua" href="javascript:ConfirmChoice(<?php echo $person_id; ?>)"> <i class="fa fa-star-o"></i></a>
        <?php } ?>
                        </span>

                    </h2>
                </div> 
                <div class="col-md-2 bg-aqua">
                    <h2 class="page-header" title="Click to allow access to specific users">
                        <span id="sens">
                            <?php if ($flagsensitive == 'TRUE') { ?>                                             
                                <a href="javascript:sensitivepersonacl(<?php echo $person_id; ?>)" class="bg-aqua"> Sensitive: </a>
                            <?php } else { ?>
                                Sensitive:
        <?php } ?>
                        </span>
                        <span id="sensitive" class="pull-right">
                            <?php if ($flagsensitive == 'TRUE') { ?>                                             
                                <a class="bg-aqua" href="javascript:ConfirmUnmarkSensitive(<?php echo $person_id; ?>)"> <i class="fa fa-star"></i> </a>
                            <?php } else { ?>
                                <a class="bg-aqua" href="javascript:ConfirmMarkSensitive(<?php echo $person_id; ?>)"> <i class="fa fa-star-o"></i></a>
                        </span>


                            <?php } ?>
                    </h2>

<!--                    --><?php // if ($flagsensitive == 'TRUE'){?>
<!--                        <span id="user" style="display: none" class="pull-right">-->
<!---->
<!---->
<!---->
<!--                                    <label for="user_name_get" class="control-label" title="Click to allow access to specific users">User's Name </label>-->
<!--                                    <select class="form-control select2" name="user_name_get" id="user_name_get" data-placeholder="Select user from list"  style="width: 100%">-->
<!--                                        --><?php //$user_name_list = Helpers_Utilities::get_users_list_against_posting_id($of_id);
//                                        foreach ($user_name_list as $list) { ?>
<!--                                            <option value="--><?php //echo $list->name ?><!--">--><?php //echo $list->name ?><!--</option>-->
<!--                                        --><?php //} ?>
<!---->
<!---->
<!--                                    </select>-->
<!---->
<!--                        </span>-->
<!---->
<!--                    --><?php //}?>

                </div>



            <?php   } else if ($permission == 1) {
                $flagsensitive = Helpers_Person::is_sensitive_person($login_user_id, $person_id)
                ?>
                <div class="col-md-4 bg-aqua">
                    <h2 class="page-header" title="Click to allow access to specific users">
                        <span id="sens">
                            <?php if ($flagsensitive == 'TRUE') { ?>                                             
                                <a href="javascript:sensitivepersonacl(<?php echo $person_id; ?>)" class="bg-aqua"> Sensitive: </a>
                            <?php } else { ?>
                                Sensitive:
        <?php } ?>                
                        </span>
                        <span id="sensitive" class="pull-right">
                            <?php if ($flagsensitive == 'TRUE') { ?>                                             
                                <a class="bg-aqua" href="javascript:ConfirmUnmarkSensitive(<?php echo $person_id; ?>)"> <i class="fa fa-star"></i> </a>
                            <?php } else { ?>
                                <a class="bg-aqua" href="javascript:ConfirmMarkSensitive(<?php echo $person_id; ?>)"> <i class="fa fa-star-o"></i></a>
        <?php } ?>
                        </span>

                    </h2>
                </div>
    <?php

            }
             else { ?>
                <div class="col-md-4 bg-aqua">
                    <h2 class="page-header">
                        Favourite: 
                        <span id="favt" class="pull-right">
                            <?php if ($flagfavourite == 'TRUE') { ?> 
                                <a class="bg-aqua" href="javascript:ConfirmChoiceUnmark(<?php echo $person_id; ?>)"> <i class="fa fa-star"></i> </a>
                            <?php } else { ?>
                                <a class="bg-aqua" href="javascript:ConfirmChoice(<?php echo $person_id; ?>)"> <i class="fa fa-star-o"></i></a>
        <?php } ?>
                        </span>

                    </h2>
                </div>
                <?php
            }
        } catch (Exception $ex) {
            
        }
        ?>
        <div class="col-md-4 bg-aqua">
            <h2 class="page-header">
                Status: 
                <span id="stcng" class="text-bold text-bold"> <?php echo $cat_name; ?> </span>                                
                <div class="input-group-btn pull-right" style="margin-right: 115px">
                    <!--                    //change button-->
                    <button type="button" onclick="changestatus()" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Change Status</button>                    
                </div> 
            </h2>
        </div>                                                                                                      
    </div>
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-md-12">
            <div class="box box-info box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Personal Information </h3>
                    <div class="box-tools pull-right">
<!--                        <a title="Click To Tag Person In Specific Category" class="custom-cursor" onclick="tagperson(<?php /* echo $person_id; */ ?>)"> <span class="label label-success">Tag Person</span> </a>-->
                        <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="padding: 0; height: 140px">
                    <ul id="basicifo" class="todo-list">
                        <li class="dashboard-sticky-red">
                            <span class="text-black"> <b>Name: </b> <?php echo $first_name . " " . $middle_name . " " . $last_name; ?> </span>
                        </li>
                        <li class="dashboard-sticky-red">
                            <span class="text-black"> <b>Father/Husband Name: </b><?php echo $father_name; ?> </span>
                        </li>
                        <li class='dashboard-sticky-red'>

                            <span class="text-black"> 
                                <b>CNIC: </b>  <?php echo $nic_number; ?>   , &nbsp &nbsp &nbsp 
                                
                                    <b>DOB: </b> <?php echo $datofbirth; ?>   <b>
                                        <?php if (!empty($datofbirth)) {  ?>
                                        , Age: </b><?php echo $personage; ?>
                                <?php }  ?>
                            </span>

                        </li>
                        <li class='dashboard-sticky-red'>
                            <?php
                            // $profile = Helpers_Person::get_person_nadra_profile($person_id);
                            if (!empty($nic_number)) {
                                ?>
                                <div class="">
                                    <?php
                                    $left = -1;
                                    $check_old_request = Helpers_Email::check_old_familytree_request($nic_number);
                                    $en_check_old_request = trim(Helpers_Utilities::encrypted_key($check_old_request, 'encrypt'));
                                    ?>


                                    <!--branchless banking request CTFU-->
<!--                                        <a title="Click To Request Banchless Banking" href="#" onclick="requestbranchlessbanking('<?php /*echo $nic_number */ ?>', '<?php /* echo $person_id; */ ?>')"> <span class="label label-warning">Branchless Banking</span> </a>                                    -->
                                    <!--branchless banking request CTFU-->
                                    <?php
                                    $nadra_status = isset($profnadra->person_nadra_status) ? $profnadra->person_nadra_status : 0;
                                    if (FALSE) {//$nadra_status == 0) {
                                        ?>
                                                        <!--<a href="#" onclick="requestnadra(<?php //echo $nic_number . ',' . $person_id;     ?>)"> <span class="label label-success">Nadra Profile</span> </a>-->
                                        <a   style="position: absolute; left: <?php echo $left+=13; ?>%;"  title="Click To Request Profile From Nadra" href='<?php echo URL::site("persons/nadra_profile?cnic=" . $nic_number . "&pid=" . $_GET['id']); ?>' onclick="requestnadra(<?php echo $nic_number . ',' . $person_id; ?>)"> <span class="label label-primary">Nadra Profile</span> </a>
                                        <?php
                                    }
                                    if ($isforeigner == 1) {
                                        $nic_number_forg = "'" . trim($nic_number) . "'";
                                        ?>
                                        <a style="position: absolute; left: <?php echo $left+=21; ?>%;" title="Click For Request" class="custom-cursor" onclick="requestcnicsims(<?php echo $nic_number_forg . ',' . (($person_id)); ?>)"> <span class="label label-primary">Request SIM's Against CNIC</span> </a>

                                    <?php } else { ?>

                                        <a style="position: absolute; left: <?php echo $left+=21; ?>%;" title="Click For Request" class="custom-cursor" onclick="external_search_model(<?php echo '0,' . (($nic_number)); ?>)"> <span class="label label-primary">Request SIM's Against CNIC</span> </a>   
                                <?php } ?>
                                        <?php
                                        //$user_obj = Auth::instance()->get_user();
                                        //$login_user_id = $user_obj->id;
                                        //if ($login_user_id == 136 || $login_user_id == 137 || $login_user_id == 138) {
                                        $count_old_request = Helpers_Email::check_old_travelhistory_request($nic_number); 
                                        if ($count_old_request > 0) {
                                            $left += 27;
                                            $url = '<form id="id-aies" method="post" action="http://www.suspect.ctdpunjab.com/persons/travel_history/?id='.$_GET['id'].'" target="_blank" style="left:'. $left .'%; position: absolute;">
                                            <input type="hidden" name="username" value="' . Auth::instance()->get_user()->username . '">
                                            <input type="hidden" name="userid" value="' . Auth::instance()->get_user()->id . '">
                                            <input type="hidden" name="password" value="' . Auth::instance()->get_user()->password . '">
                                            <input type="hidden" name="personid" value="' . $_GET['id'] . '">
                                            <input type="hidden" name="siteid" value="1">
                                            <input type="hidden" name="smartuser" value="">
                                            <button style="position: absolute;" class="label label-success" type="submit"><span class="label label-success"> View Travel History</span></button>
                                            </form>';
                                            echo $url; 
                                            ?>
                                        <a style="position: absolute; left: <?php echo $left+=20; ?>%;" title="Click To Request Fresh Travel History" href="#" onclick="requesttravelhistory('<?php echo $nic_number ?>', '<?php echo $person_id; ?>')"> <span class="label label-danger">Request Fresh Travel History</span> </a>
                                            
                                        <?php } else { ?>
                                            <a style="position: absolute; left: <?php echo $left+=30; ?>%;" title="Click To Request Travel History" href="#" onclick="requesttravelhistory('<?php echo $nic_number ?>', '<?php echo $person_id; ?>')"> <span class="label label-danger">Request Travel History</span> </a>
                                        <?php } ?> 
                                </div>
                                <?php } ?>

                        </li>
                        <li class='dashboard-sticky-red'>
                            <!--                          NOW this -->
                            <?php
                            $pid = !empty($person_id) ? Helpers_Utilities::encrypted_key($_GET['id'], 'decrypt') : 0;
                            $tags = Helpers_Watchlist::get_person_tags_data_comma($pid);
                            ?>
                            <!--                            <span class="text-black"> <b>DOB: </b> <?php /* echo $datofbirth; */ ?> <b>, Age: </b><?php /* echo $personage; */ ?> </span>                            -->
                            <span class="text-black"> <b>Tags: </b> <?php echo $tags; ?> </span>                            
                        </li>
                        <li class='dashboard-sticky-red'>
                            <!--<a href="<?php //echo URL::site('personprofile/person_profile/?id=' . $_GET['id']); ?>"> <span class=""> <i class="fa fa-inbox"></i> Person Profile &nbsp </span> </a>-->
                            <?php
                            $url = '<form id="id-aies" method="post" action="http://www.suspect.ctdpunjab.com/" target="_blank">
                            <input type="hidden" name="username" value="' . Auth::instance()->get_user()->username . '">
                            <input type="hidden" name="userid" value="' . Auth::instance()->get_user()->id . '">
                            <input type="hidden" name="password" value="' . Auth::instance()->get_user()->password . '">
                            <input type="hidden" name="personid" value="' . $_GET['id'] . '">
                            <input type="hidden" name="siteid" value="1">
                            <input type="hidden" name="smartuser" value="">
                            <button style="" class=" person-link ml-56  form-control btn btn-primary " type="submit">
                             &nbsp<span class=""> <i class="fa fa-inbox"></i> Person Profile &nbsp </span></button>
                            </form>';
                            echo $url;
                            ?>

                            <?php
                            $user_obj = Auth::instance()->get_user();
                            $login_user_id = $user_obj->id;
                            if ($login_user_id == 842 || $login_user_id == 137 || $login_user_id == 2031) {
                                ?>
                                <a style="margin-top: -27px;" href="<?php echo URL::site('personprofile/person_info_update/?id=' . $_GET['id']); ?>"> <span class=""> <i class="fa fa-ambulance"></i> Update Info &nbsp</span> </a>
<?php } ?>
                        </li>
                    </ul>
                    <!-- <br>-->
                </div>                                
                <!-- /.box-body -->                                
            </div>
            <!-- /.box -->

        </div>
        <div class="col-md-12">
            <div class="box box-info box-solid">
                <div class="box-header with-border">
                    <h3 title="Total Own Name SIMs Plus Total SIMs Under Personal Use" class="box-title">SIMs Information</h3>
                    <div style="display:none;" id="custom-form"></div>
                    <div class="box-tools pull-right">
                        <span class="label label-info" id="person_sims_count"></span>
                        <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="padding: 0; height: 140px" id="person_total_sims_detail">
                    <?php echo Helpers_Layout::get_ajax_loader(); ?>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>  
        <div class="col-md-12">
            <div class="box box-info box-solid">
                <div class="box-header with-border">
                    <h3 title="Total Devices Used By This Person" class="box-title">Devices Information</h3>
                    <div class="box-tools pull-right">
                        <span class="label label-info" id="person_device_count"></span>  
                        <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="padding: 0; height: 140px" id="person_total_devices_details">
                    <?php echo Helpers_Layout::get_ajax_loader(); ?>
                </div>                
            </div>            
        </div>  
<!--        Link With Project last 5 records-->
        <div class="col-md-12">
            <div class="box box-info box-solid">
                <div class="box-header with-border">
                    <h3 title="Total Devices Used By This Person" class="box-title">Link With Projects (Last 5 Records)</h3>
                    <div class="box-tools pull-right">
                        <span class="label label-info" id="person_device_count"></span>  
                        <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="padding: 0; height: 140px" id="person_link_with_projects">
                    <?php echo Helpers_Layout::get_ajax_loader(); ?>
                </div>                
            </div>            
        </div>  
    </div>
    <div class="row invoice-info">
        <div class="col-md-12">
            <div class="box box-success collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Person Last Location</h3>
                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="person_last_location" class="panelisopen btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>                
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pad">
                                <!-- Map will be created here -->
                                <!--<div id="world-map-markers" style="height: 325px;"></div>-->
                                <div id="map" style="height: 355px;">
                                    <?php
                                    try {
                                        echo Helpers_Layout::get_ajax_loader();
                                    } catch (Exception $ex) {
                                        
                                    }
                                    ?>
                                </div>
                                <img id="nodata" style="display:none; width: 534px; margin: auto" class="img-responsive" src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                            </div>
                        </div>
                        <!-- /.col -->               
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-success collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Person's Current Location History</h3>
                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="current_loc_history" class="panelisopen btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>                
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding" style="height: 375px">
                    <div class="row">
                        <div class="col-md-12" style="height: 360px !important; overflow: auto; scroll-behavior:auto;">
                            <div class="pad" id="current_location_history">                                                                                                
<?php
echo Helpers_Layout::get_ajax_loader();
?>
                            </div>
                        </div>
                        <!-- /.col -->               
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div> 
        </div>
    </div>
    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <div class="col-md-12">
            <!-- MAP & BOX PANE -->
            <!-- TABLE: LATEST ORDERS -->
            <!-- AREA CHART -->
            <div class="box box-primary collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Call and SMS Log</h3>

                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="call_sms_log" class="panelisopen btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>                
                    </div>
                </div>
                <div class="box-body" style="height: 300px">
                    <div class="chart"> 
                        <div id="call_sms_ajax_loader">                             
<?php
echo Helpers_Layout::get_ajax_loader();
?>
                        </div>
                        <canvas id="areaChart" style="height:250px">
                        </canvas>
                        <img id="nodatachart" style="display:none; width: 444px; margin: auto" class="img-responsive" src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            <!-- /.box -->

            <div class="row">
                <div class="col-md-12">  
                    <div class="box box-info box-solid collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Person Last Call & SMS</h3>                            
                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide" id="person_last_cm" class="panelisopen btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>                
                            </div>
                        </div>
                        <div class="box-body " id="person_last_call_sms">
<?php
echo Helpers_Layout::get_ajax_loader();
?>
                        </div>                        
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box box-info box-solid collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Favourite Person</h3>
                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide" id="favourite_person" class="panelisopen btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>                
                            </div>
                        </div>
                        <div class="box-body" id="person_favourite_person_list">     
<?php
echo Helpers_Layout::get_ajax_loader();
?>
                        </div>
                    </div> 
                </div>
                <div class="col-md-12">  
                    <div class="box box-info box-solid collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Other Numbers</h3>
                            <div class="box-tools pull-right">
                                <a title="Click to add new PTCL or International number" class="custom-cursor" onclick="addothernumber()"> 
                                    <span class="label label-success">Add New Number</span> 
                                </a>
                                <button type="button" title="Show/Hide" id="other_number" class="panelisopen btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>                
                            </div>
                        </div>
                        <div class="box-body" >
                            <div  id="person_other_numbers">    
<?php echo Helpers_Layout::get_ajax_loader(); ?>                            
                            </div> 
                        </div>                        
                    </div>
                </div>
                <div class="col-md-12">  
                    <div class="box box-info box-solid collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Other Information</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide" id="other_information" class="panelisopen btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                </button>                
                            </div>
                        </div>
                        <div class="box-body" >
                            <div  id="person_affiliations_and_social_links">    
<?php
echo Helpers_Layout::get_ajax_loader();
?>                            
                            </div> 
                        </div>                        
                    </div>
                </div>

            </div>
        </div>
        <!-- /.col -->

        <div class="col-md-12">
            <!--Person Nadra Information-->
            <div class="box box-info box-solid  collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Person Nadra Profile</h3>
                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="person_nadra_profile" class="panelisopen btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>                
                    </div>
                </div>
                <div class="box-body" style="height: 300px; overflow: auto; scroll-behavior:auto">                            
                    <?php
                    $person_name = !empty($profnadra->person_name) ? $profnadra->person_name : "Unknown";
                    $person_g_name = !empty($profnadra->person_g_name) ? $profnadra->person_g_name : "Unknown";

                    $person_gender = isset($profnadra->person_gender) ? $profnadra->person_gender : "Unknown";
                    if ($person_gender == '0') {
                        $person_gender = 'Male';
                    } elseif ($person_gender == 1) {
                        $person_gender = 'Female';
                    } else {
                        $person_gender = 'Other';
                    }
                    $maritalstatus = isset($profnadra->martial_status) ? $profnadra->martial_status : "";
                    if ($maritalstatus == 1) {
                        $maritalstatus = 'Single';
                    } elseif ($maritalstatus == 2) {
                        $maritalstatus = 'Married';
                    } elseif ($maritalstatus == 3) {
                        $maritalstatus = 'Divorced';
                    } elseif ($maritalstatus == 4) {
                        $maritalstatus = 'Widowed';
                    } elseif ($maritalstatus == 5) {
                        $maritalstatus = 'Separated';
                    } else {
                        $maritalstatus = '';
                    }

                    $person_gender = $person_gender . ', ' . $maritalstatus;
                    $person_dob = !empty($profnadra->person_dob) ? $profnadra->person_dob : "Unknown";
                    //$lastLogin = ( isset($item['last_login']) ) ? date('m/d/Y H:i:s', $item['last_login']) : 'NA';                    
                    $person_present_add = !empty($profnadra->person_present_add) ? $profnadra->person_present_add : "Unknown";
                    $person_permanent_add = !empty($profnadra->person_permanent_add) ? $profnadra->person_permanent_add : "Unknown";

                    // $profile = Helpers_Person::get_person_nadra_profile($person_id);
                    $imageurl = !empty($profnadra->person_photo_url) ? $profnadra->person_photo_url : "NA";
                    //$path = URL::base() . "dist .DIRECTORY_SEPARATOR. uploads/person/profile_images/{$imageurl}";
                    $path = $person_download_data_path . $imageurl;
                    //if person is foreginer
                    if ($isforeigner == 1) {
                        $familyid = !empty($profnadra->family_id) ? $profnadra->family_id : "NA";
                        $pakdistrict = !empty($profnadra->pak_district) ? $profnadra->pak_district : "";
                        $paktehsil = !empty($profnadra->pak_tehsil) ? $profnadra->pak_tehsil : "";
                        $homecountry = !empty($profnadra->home_country) ? $profnadra->home_country : "";
                        $ethnicity = !empty($profnadra->ethnicity) ? $profnadra->ethnicity : "NA";
                        $foreign_prof_note = "Family Id: " . $familyid . " , Ethnicity: " . $ethnicity . " , Country:" . $homecountry;
                        $person_permanent_add = $person_permanent_add . ', ' . $paktehsil . ',' . $pakdistrict;
                    } else {
                        $foreign_prof_note = '';
                    }
                    ?>
                    <div class="col-md-4"> 
                        <?php
                        echo '<p style="display:none">' . $path;
//  echo @getimagesize($path);
                        echo '<p>';
//echo @getimagesize($path); exit;
//if ($imageurl == "NA" || @getimagesize($path) == FALSE) {
                        if ($imageurl == "NA" || empty($imageurl) || empty($person_download_data_path)) {
                            echo '<img class="myImg" src="' . URL::base() . 'dist/img/avtar6.jpg" alt="No Data" style="width: 100%; height: 100%;margin: auto;">';
                        } else {
                            $imageurl_link = $person_download_data_path . $imageurl;
                            //print_r($imageurl_link); exit;
                            echo HTML::image("{$imageurl_link}", array("id" => "myImg", "height" => "120px", "width" => ""));
                        }
                        ?>
                    </div>
                    <div class="col-md-8 nadraprofile"> 
                        <div class="col-md-12">
                            <strong> Name:</strong> 
                        </div>
                        <div class="col-md-12">
                            <strong style="float: right"> <?php echo $person_name; ?> </strong>                                          
                        </div>
                        <div class="col-md-12">
                            <strong> Guardian Name:</strong> 
                        </div>
                        <div class="col-md-12">
                            <strong style="float: right"> <?php echo $person_g_name; ?> </strong>                                         
                        </div>
                        <div class="col-md-12">
                            <strong> Gender:</strong> 
                        </div>
                        <div class="col-md-12">
                            <strong style="float: right"> <?php echo $person_gender; ?> </strong>                                         
                        </div>                            
                        <div class="col-md-12">
                            <strong> DOB:</strong> 
                        </div>
                        <div class="col-md-12">
                            <strong style="float: right"> <?php echo $person_dob; ?> </strong>                                         
                        </div>  
                    </div>
                    <div class="col-md-12 pull-right-5">
                        <hr class="style14 " style="margin-top: 5px; margin-bottom: 5px"> 
                    </div>
                    <div class="col-md-1">
                        <strong><i class="fa fa-envelope margin-r-5"></i> </strong> 
                    </div>
                    <div class="col-md-10">
                        <strong> Present Address:</strong> 
                    </div>
                    <div class="col-md-12">
                        <strong style="float: right"> <?php echo $person_present_add; ?> </strong>                                         
                    </div>                            
                    <div class="col-md-1">
                        <strong><i class="fa fa-envelope-o margin-r-5"></i> </strong> 
                    </div>
                    <div class="col-md-10">
                        <strong> Permanent Address:</strong> 
                    </div>
                    <div class="col-md-12">
                        <strong style="float: right"> <?php echo $person_permanent_add; ?> </strong>                                         
                    </div> 
<?php
if ($isforeigner == 1) {
    ?>
                        <div class="col-md-1">
                            <strong><i class="fa fa-list margin-r-5"></i> </strong> 
                        </div>
                        <div class="col-md-10">
                            <strong> Note:</strong> 
                        </div>
                        <div class="col-md-12 ">
                            <strong  style="float: right"> <?php echo $foreign_prof_note; ?> </strong>                                         
                        </div>  
    <?php
}
?>
                </div>
            </div>      
            <div class="box box-primary collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Person CNIC (Verisys):</h3>

                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="person_cnic_verisys" class="panelisopen btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>                
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <!-- Trigger the Modal -->
                    <div> 
                        <?php
                        try {
                            if ($isforeigner == 1) {
                                echo '<img class="mycnic" src="' . URL::base() . 'dist/img/noperson.png" alt="No Data" style="width: 100%;margin: auto;height:470px;padding: 27px 0"> ';
                            } else {
                                if ($verisisimg1 == "NA" || empty($person_download_data_path)) {
                                    echo '<img class="mycnic" src="' . URL::base() . 'dist/img/noperson.png" alt="No Data" style="width: 100%;margin: auto;height:470px;padding: 27px 0"> ';
                                    $stat = Helpers_Utilities::get_nadra_request_status($nic_number);
                                    if ($stat == 0) { //lol3 
                                        ?>                        
                                        <a class="btn btn-primary" href='javascript:requestnadraverysis(<?php echo $nic_number; ?>,<?php echo $person_id; ?>)'   > Request NADRA Verisys </a> 
                                    <?php } else { ?>
                                        <label class="label-info">Request In Process</label>                                
                                    <?php
                                    }
                                } else {
                                    $nadralinkurl = $person_download_data_path . $verisisimg1;
                                    $ext = pathinfo($nadralinkurl, PATHINFO_EXTENSION);
                                    if ($ext == 'pdf') {
                                        echo '<iframe src="'. $nadralinkurl . '" style="height:650px;width:450px"></iframe>';
                                    }else {
                                        echo HTML::image("{$nadralinkurl}", array("id" => "mycnic", "height" => "493px", "width" => "100%"));
                                    }
                                    ?>
                                        <a class="btn btn-primary" href='javascript:requestnadraverysis(<?php echo $nic_number; ?>,<?php echo $person_id; ?>)'   > Request for Updated NADRA Verisys </a> 
                                    <?php

                                }
                            }
                        } catch (Exception $ex) {
                            
                        }
                        ?>
                    </div>
                    <!-- The Modal -->
                    <div id="cnicmodal" class="modal">

                        <!-- The Close Button -->
                        <span class="close" onclick="document.getElementById('cnicmodal').style.display = 'none'">&times;</span>

                        <!-- Modal Content (The Image) -->
                        <img class="modal-content" id="imgcnic">

                        <!-- Modal Caption (Image Text) -->
                        <div id="captionnadra">Person Nadra Verisis</div>
                    </div>
                </div>
            </div>



            <div class="box box-primary collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Person Family Tree</h3>

                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="person_family_tree" class="panelisopen btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="border-bottom-width: 20px; border-bottom-style: solid; color: white"  >
                    <!-- Trigger the Modal -->
                    <div>
                        <?php
                        try {

                            if ($isforeigner == 1) {
                                echo '<img class="myftree" src="' . URL::base() . 'dist/img/noperson.png" alt="No Data" style="width: 100%;margin: auto;height:470px;padding: 27px 0"> ';
                            } else {
                                if ($famtreeimg1 == "NA" || empty($person_download_data_path)) {

   if (!empty($check_old_request)) {


                                    ?>
                        <a  style="position: absolute; left: <?php echo $left+=1; ?>;" href='#' onclick="familytreedetail(<?php echo $check_old_request; ?>)" > <span class="label label-success">View Family Tree</span> </a>
                        <?php }

                                    else {

                                        echo '<img class="myftree" src="' . URL::base() . 'dist/img/noperson.png" alt="No Data" style="width: 100%;margin: auto;height:470px;padding: 27px 0"> ';
                                        $stat = Helpers_Utilities::get_famlytree_request_status($nic_number);
                                        if ($stat == 0) { //lol3
                                            ?>
                                            <a class="btn btn-primary"
                                               href='javascript:requestfamilytree1(<?php echo $nic_number; ?>,<?php echo $person_id; ?>)'>
                                                Request Family Tree </a>
                                        <?php } else { ?>
                                            <label class="label-info">Request In Process</label>
                                            <?php
                                        }
                                    }
                                } else {
                                    //$nadralinkurl1 = $person_download_data_path . $famtreeimg1;
                                    //echo HTML::image("{$nadralinkurl1}", array("id" => "myftree", "height" => "493px", "width" => "100%"));
                                    
                                    $nadralinkurl1 = $person_download_data_path . $famtreeimg1;
                                    $ext = pathinfo($nadralinkurl1, PATHINFO_EXTENSION);
                                    if ($ext == 'pdf') {
                                        echo '<iframe src="'. $nadralinkurl1 . '" style="height:650px;width:450px"></iframe>';
                                    }else {
                                        echo HTML::image("{$nadralinkurl1}", array("id" => "myftree", "height" => "493px", "width" => "100%"));
                                    }
                                }
                            }
                        } catch (Exception $ex) {

                        }
                        ?>
                    </div>
                    <!-- The Modal -->
                    <div id="famtreemodal" class="modal">

                        <!-- The Close Button -->
                        <span class="close" onclick="document.getElementById('famtreemodal').style.display = 'none'">&times;</span>

                        <!-- Modal Content (The Image) -->
                        <img class="modal-content" id="imgfamtree">

                        <!-- Modal Caption (Image Text) -->
                        <div id="captionfamtree">Person Family Tree</div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- /.row -->

</section>
<div class="modal modal-info fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Allow Access to Specific Users</h4>
            </div>
            <form class="" name="acl_form" action="<?php echo url::site() . 'persons/sensitiveperson_acl_form' ?>" id="acl_form" method="post">
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Name</th>
                                    <th>Designation</th>                                  
                                    <th>Posting</th>                                  
                                    <th>Access</th>                                  
                                </tr>
                            </thead>
                            <tbody id="acl_user_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>         
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="categorychangemode">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Category Change</h4>
            </div>
 <?php 
 $affiliation_count = Helpers_Person::check_person_affiliations(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt"));
 if ($affiliation_count > 0) { ?>
            <form class="" name="categorychange" id="categorychange" action="<?php echo url::site() . 'persons/change' ?>"  method="post" enctype="multipart/form-data" >  
 <?php } ?>
                <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>

                    <!--Manual Upload CDR Against IMEI-->
                    <div  style="display:block;margin-top: 5px;margin-bottom: 5px;" class="col-md-12"> 
<?php if ($affiliation_count > 0) { ?>
                            <div class="col-md-12" style="background-color: #fff;color: black">
                                <hr class="style14 ">
                                <div class="form-group col-sm-12">                                 
                                    <label>You Are Changing Category of Person</label>
                                    <input  class="form-control " type="text" id="person_name" name="person_name" value="<?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?>" readonly>
                                    <input  class="form-control " type="hidden" id="person_id" name="person_id" value="<?php echo Helpers_Utilities::encrypted_key($_GET['id'], "decrypt"); ?>" readonly>                                                                                                                  
                                </div> 
                                <div class="form-group col-sm-12">                                 
                                    <label>Select New Category</label>
                                    <select class="form-control" name="category" id='category'>                                                        
                                        <option value="">Please Select Type</option>                                            
                                        <option <?php echo (( isset($cat_id) && ($cat_id == '0')) ? 'selected' : ''); ?> value="0"> White</option>
                                        <option <?php echo (( isset($cat_id) && ($cat_id == '1')) ? 'selected' : ''); ?> value="1"> Gray</option>
                                        <option <?php echo (( isset($cat_id) && ($cat_id == '2')) ? 'selected' : ''); ?> value="2"> Black</option>                                                                                    
                                    </select>
                                </div> 
                                <div class="col-sm-12" id="project_div" style="display: block;margin-top: 10px;">
                                    <div class="form-group">                                                            
                                        <label for="inputproject" class="control-label">Link With Project</label> 
                                            <?php
                                            try {
                                                $rqts = Helpers_Utilities::get_projects_list();
                                            } catch (Exception $ex) {
                                                
                                            }
                                            ?>
                                        <select class="form-control" name="inputproject[]" data-placeholder="Select Project Name" id="inputproject" style="width: 100%">
                                            <option value="">Please select project</option>
                                            <?php
                                            try {
                                                foreach ($rqts as $rqt) {
                                                    $district_id = $rqt->district_id;
                                                    if (isset($district_id) && !empty($district_id)) {
                                                        $district_name = Helpers_Utilities::get_district($district_id);
                                                    } else {
                                                        $district_name = '';
                                                    }
                                                    ?>
                                                    <option value="<?php echo $rqt->id; ?>"><?php echo $rqt->project_name . " [" . " District: " . $district_name . " Region: " . $rqt->name . "]"; ?></option>
                                    <?php
                                }
                            } catch (Exception $ex) {

                            }
                            ?>                                                                                                                
                                        </select>                                                                                    
                                    </div>
                                </div>
                                <div class="col-sm-12" id="reason_div" style="display: block;margin-top: 10px">
                                    <div class="form-group" >                                                            
                                        <label for="inputreason" class="control-label">Reason For Changing Category</label>
                                        <textarea class="form-control" name="inputreason" id="inputreason"  placeholder="Enter Reason For Request" ></textarea>                                                          
                                    </div>
                                </div>
                                <div class="form-group col-sm-12" >
                                    <hr class="style14 ">
                                </div>
                                <span id="" class="text-black" > </span>
                            </div>
                                    <?php } else { ?>
                            <!--   Affiliation message div  -->
                            <div class="col-md-12" style="background-color: #fff;color: black">
                                <hr class="style14 ">
                                <div class="form-group col-sm-12">                                 
                                    <label>If you want to change the category of "<?php
                                    try {
                                        echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt"));
                                    } catch (Exception $ex) {
                                        echo ' ';
                                    }
                                    ?>"</label>
                                    <input  class="form-control " type="hidden" id="person_name" name="person_name" value="<?php
                                    try {
                                        echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt"));
                                    } catch (Exception $ex) {
                                        
                                    }
                                    ?>" readonly>
                                    <input  class="form-control " type="hidden" id="person_id" name="person_id" value="<?php
                                    try {
                                        echo Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
                                    } catch (Exception $ex) {
                                        
                                    }
                                    ?>" readonly>                                                                                                                  
                                    <label>The person must be affiliated with an Organization.
                                        <!--<a href="<?php //echo URL::site('personprofile/person_profile/?id=' . $_GET['id'] . '&tab=affiliations'); ?>"> Click Here </a>--> 
                                    <?php
                                    $url = ' <form id="id-aies" method="post" action="http://www.suspect.ctdpunjab.com/" target="_blank">
                                        <input type="hidden" name="username" value="' . Auth::instance()->get_user()->username . '">
                                        <input type="hidden" name="userid" value="' . Auth::instance()->get_user()->id . '">
                                        <input type="hidden" name="password" value="' . Auth::instance()->get_user()->password . '">
                                        <input type="hidden" name="personid" value="' . $_GET['id'] . '">
                                        <input type="hidden" name="siteid" value="1">
                                        <input type="hidden" name="smartuser" value="">
                                        <button style="" class=" person-link ml-56  form-control btn btn-primary " type="submit">
                                                           &nbspClick Here</button>
                                        </form>';
                                    echo $url;
                                    ?>
                                      to affiliate this person with an Organization.</label>
                                </div>  
                                <div class="form-group col-sm-12" >
                                    <hr class="style14 ">
                                </div>

                            </div>
                <?php } ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                    <?php if ($affiliation_count > 0) { ?>
                        <button type="submit"  class="btn btn-primary ">Save Data</button>
                    <?php } ?>
                </div> 
                <?php if ($affiliation_count > 0) { ?>
            </form>
             <?php } ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="tagperson">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Select Person Tags</h4>
            </div>
            <form class="" name="blocknumber" id="blocknumber" action="<?php echo URL::site() . 'persons/person_tags' ?>"  method="post" enctype="multipart/form-data" >  
                <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>
                    <div  style="display:block;margin-top: 5px;margin-bottom: 5px;" class="col-md-12">
                        <div class="col-md-12" style="background-color: #fff; color: black; padding: 14px; font-size: larger;">
                            <div class="form-group col-md-12" style="min-height: 30px !important;">
                                <h4 class="modal-title text-primary">Select Person Tags</h4>
                            </div>
                            <input  class="form-control " type="hidden" id="person_id" name="person_id" value="<?php echo Helpers_Utilities::encrypted_key($_GET['id'], "decrypt"); ?>" readonly>
                                <?php
                                try {
                                    $user_obj = Auth::instance()->get_user();
                                    $login_user_id = $user_obj->id;
                                    $permission = Helpers_Utilities::get_user_permission($login_user_id);
                                    $login_user_profile = Helpers_Profile::get_user_perofile($login_user_id);
                                    $posting = $login_user_profile->posted;
                                    $result = explode('-', $posting);
                                    $user_district_id = $result[1];
                                    if ($result[0] == 'd' || $permission == 1) {
                                        ?>                           
                                    <div class="category_data col-md-12">
                                        <?php
                                        $tags = Helpers_Watchlist::get_tags_data();
                                        $person_tags = Helpers_Watchlist::get_person_tags(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt"));
                                        $active_tags = array_column($person_tags, 'tag_id');
                                        $active_tag_discrict = array_column($person_tags, 'tag_district_id');
                                        for ($count = 0; sizeof($active_tags) > $count; $count++)
                                            $district_active[$active_tags[$count]] = $active_tag_discrict[$count];
                                        foreach ($tags as $tag) {
                                            if (in_array($tag->id, $active_tags)) {
                                                $checked = 'checked';
                                                $district = $district_active[$tag->id];
                                                $district_name = Helpers_Utilities::get_district($district);
                                                if ($district == $user_district_id) {
                                                    $disabled = '';
                                                } else {
                                                    $disabled = 'onclick="return false;"';
                                                }
                                            } else {
                                                $disabled = '';
                                                $checked = '';
                                                $district_name = '';
                                            }
                                            ?>                                
                                            <div class="form-group col-md-4"> 
                                                <div class="col-md-12">
                                                    <input type="checkbox" <?php echo $disabled; ?> <?php echo $checked; ?> name="category_type[<?php echo $tag->id; ?>]" id="forthschedule">
                                                    <label for="forthschedule" title="<?php echo $tag->tag_description; ?>" style="padding-left: 12px;"> <?php echo $tag->tag_name; ?> </label>
                                                </div>
                                                <div class="col-md-12">
                                                    <span id="forthschedule_district"> <b><?php echo $district_name; ?></b> </span>
                                                </div>
                                            </div>

                                    <?php } ?>

                                    </div>

                                    <?php } else { ?>
                                    <div class="category_data col-md-12">
                                        <span class="text-warning"><b>Access Denied!</b> Only <b>District Officer</b> or <b>District Focal Person</b> Tag a person.</span>
                                    </div>
                                    <?php } } catch (Exception $ex) { } ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>                    
                    <?php if ($result[0] == 'd') { ?>
                    <button type="submit"  class="btn btn-primary ">Save Data</button>
                    <?php } ?>
                </div>  
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="external_search_model">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Search Results</h4>
            </div>

            <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>
                <!--searching data form external sources-->
                <div id="externa_search_results_div" style="display: block;">                                                            
                    <div class="col-md-12" style="background-color: #fff;color: black"> 
                        <div class="col-sm-12">
                            <div class="form-group">                                                                                
                                <label   for="external_search_key" class="control-label">Search Key:
                                </label>
                                <input name="external_search_key" type="text" class="form-control" id="external_search_key" placeholder="Search Key" readonly >
                            </div>
                            <hr class="style14 " style="margin-top: -10px; "> 
                            <div class="col-sm-12" id="external_search_results" style="display: block">   
                                <div style='background-image: url("<?php echo URL::base(); ?>dist/img/loader.gif"); width:100%; height:11px; background-position:center; display:block'><label    class="control-label" style="alignment-adjust: central">Exploring External Sources
                                    </label></div>  
                                <hr class="style14 ">
                            </div>
                        </div>                                                            
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" style="margin-top: 10px" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>
            </div>  
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="add_other_number_model">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Other Number</h4>
            </div>

            <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>               
                <div id="externa_search_results_div" style="display: block;">                                                            
                    <div class="col-md-12" style="background-color: #fff;color: black"> 
                        <form class="" name="othernumber_add" id="othernumber_add"  enctype="multipart/form-data" >  
                            <div class="col-sm-12">
                                <div class="col-md-4" >
                                    <div class="form-group">                                                            
                                        <label for="number_type">Number Type</label>                                            
                                        <select class="form-control" id="number_type" name="number_type" onchange="">
                                            <option selected="" value="1">PTCL Number</option>
                                            <option  value="2">International Number</option>                                                                                                               
                                        </select>                                                                                    
                                    </div>
                                </div> 
                                <div class="form-group col-md-8" >
                                    <label for="ptclnumber">Other Number</label>
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <input type="text" class="form-control" value="" id="othernumber" name="othernumber" placeholder="Enter Other Number">
                                        <input type="hidden" class="form-control" value="<?php echo Helpers_Utilities::encrypted_key($_GET['id'], "decrypt") ?>" id="person_id" name="person_id" >

                                    </div>
                                </div>
                                <div class="col-md-12" >
                                    <div class="form-group pull-right" >
                                        <button type="button" class="btn btn-primary " onclick="saveothernumber()">Save Data</button>                                    
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" style="margin-top: 10px" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>
            </div>  
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!--date of birth update Model-->
<div class="modal modal-info fade" id="update_date_of_birth">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Update Date of Birth of Person</h4>
            </div>

            <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>               
                <div id="externa_search_results_div" style="display: block;">                                                            
                    <div class="col-md-12" style="background-color: #fff;color: black"> 
                        <form class="" name="update_date_of_birth_form" id="update_date_of_birth_form"  enctype="multipart/form-data" >  
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-8" style="margin-top: 25px;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="dateofbirth">Select Date of Birth (mm/dd/yyyy)</label>
                                        <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="dateofbirth" id="dateofbirth" value="">                                        
                                        <input type="hidden" class="form-control" value="<?php echo Helpers_Utilities::encrypted_key($_GET['id'], "decrypt") ?>" id="person_id_dob" name="person_id_dob" >
                                    </div>
                                </div>
                                <div class="col-md-12" >
                                    <div class="form-group pull-right" >
                                        <button type="button" class="btn btn-primary " onclick="updatedateofbirth()">Save Data</button>                                    
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" style="margin-top: 10px" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>
            </div>  
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
    // request verisis
    function requestnadraverysisdetails() {
        $("#requestverysismodel").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);

    }
    // Get the modal
    var modal = document.getElementById('myModal');

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img = document.getElementById('myImg');
    var modalImg = document.getElementById("img01");
    var captionText = document.getElementById("caption");
    if (img != null)
    {
        img.onclick = function () {
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        }
    }
    // Get the modal
    var modal = document.getElementById('cnicmodal');
    var modal1 = document.getElementById('famtreemodal');

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var img = document.getElementById('mycnic');
    var img1 = document.getElementById('myftree');
    var modalImg = document.getElementById("imgcnic");
    var modalImg1 = document.getElementById("imgfamtree");
    var captionText = document.getElementById("captionnadra");
    var captionText1 = document.getElementById("captionfamtree");
    if (img != null) {
        img.onclick = function () {
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        }
    }
    if (img1 != null) {
        img1.onclick = function () {
            modal1.style.display = "block";
            modalImg1.src = this.src;
            captionText1.innerHTML = this.alt;
        }
    }
    //function to call request page for CDR
    function requestcdr(sim, personID) {
        var request = "existing";
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr" id="custom_form_bd_cdr" action="<?php echo URL::site("userrequest/requestcdr/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'msisdn',
            'value': sim,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'requesttype',
            'value': 1,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': personID,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'request',
            'value': request,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_bd_bt_cdr" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_bd_bt_cdr').trigger('click');

    }
    //function to call request page for location
    function requestlocation(sim, personID) {
        var request = "existing";
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/requestlocation/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'msisdn',
            'value': sim,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'requesttype',
            'value': 4,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': personID,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'request',
            'value': request,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_bd_bt_location" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_bd_bt_location').trigger('click');

    }
    //function to call request page for subscriber new irfan
    function requestsub(sim) {
        var phonenumber = $("#phonenumber").val();
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestsubscriber/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'msisdn',
            'value': sim,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': <?php $personID = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
echo $personID;
?>,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');

    }
    //function to call request page for CDR
    function requestimeicdr(imei, personID) {
        var request = "existing";
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/requestcdrimei/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'imei',
            'value': imei,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'requesttype',
            'value': 2,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': personID,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'request',
            'value': request,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_bd_bt_cdr_imei" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_bd_bt_cdr_imei').trigger('click');

    }
    //function to call request page for sims's againt cnic new irfan
    function requestcnicsims(cnicnumber) {
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestcnicsims/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'cnic',
            'value': cnicnumber,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': <?php $personID = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
echo $personID;
?>,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');

    }
    //function to call request page of PTCL subscriber    
    function requestptclsubs(ptclnumber, personID) {
        var request = "existing";
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/requestsubptcl/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'ptclnumber',
            'value': ptclnumber,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': personID,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_bd_bt_cdr" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_bd_bt_cdr').trigger('click');
    }
    //request CDR ptcl //lol
    function requestptclcdr(ptclnumber, personID) {
        var request = "existing";
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/requestcdrptcl/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'ptclnumber',
            'value': ptclnumber,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'requesttype',
            'value': 7,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': personID,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'request',
            'value': request,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_bd_bt_cdr" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_bd_bt_cdr').trigger('click');

    }
    //function to call request page of international number cdr    
    function requestintercdr(number, personID) {
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/requestcdrinternational/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'internationalnumber',
            'value': number,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': personID,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_bd_bt_cdr" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_bd_bt_cdr').trigger('click');

    }
    function changests(pid, stat, std) {
        //var stat = $("#stcng").val(); 
        var result = {stat: stat, pid: pid}
        $.ajax({
            url: "<?php echo URL::site("persons/change"); ?>",
            type: 'POST',
            data: result,
            cache: false,
            //dataType: "text",
            success: function (msg) {
                if (msg == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#stcng").html(std);

            }
        });
    }
    //Add other number against person
    function addothernumber() {
        $("#add_other_number_model").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);
    }
    //Update person Date of Birth
    function updatedateofbirthmodal() {
        $("#update_date_of_birth").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);
    }
    //Confirm choice to mark favourite person
    function changestatus() {
        $.confirm({
            'title': 'Change Status Confirmation',
            'message': 'Do you really want to change category of this person?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $("#categorychangemode").modal("show");
                        //appending modal background inside the blue div
                        $('.modal-backdrop').appendTo('.blue');

                        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                        $('body').removeClass("modal-open");
                        $('body').css("padding-right", "");
                        setTimeout(function () {
                            // Do something after 1 second     
                            $(".modal-backdrop.fade.in").remove();
                        }, 300);
//                        changests(pid, stat, std);
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
</script>                   
<script>
    //Confirm choice to mark favourite person
    function ConfirmChoice(id) {
        $.confirm({
            'title': 'Add Favourite confirmation',
            'message': 'Do you really want to Add this Person as your Favourite person?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::base() . "persons/addfavouriteperson/"; ?>' + id,
                            success: function (result) {
                                if (result == 2)
                                {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                if (result == '-2') {
                                    alert('Access denied, contact your technical support team');
                                } else {
                                    $("#favt").html('<a class="bg-aqua" href="javascript:ConfirmChoiceUnmark(<?php echo $person_id; ?>)"> <i class="fa fa-star"></i> </a>');
                                }
                            }});
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    function ConfirmChoiceUnmark(id) {
        $.confirm({
            'title': 'Delete Favourite confirmation',
            'message': 'Do you really want to Delete this Person from your Favourite person?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::base() . "persons/deletefavouriteperson/"; ?>' + id,
                            success: function (result) {
                                if (result == 2)
                                {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                if (result == '-2') {
                                    alert('Access denied, contact your technical support team');
                                } else {
                                    $("#favt").html('<a class="bg-aqua" href="javascript:ConfirmChoice(<?php echo $person_id; ?>)"> <i class="fa fa-star-o"></i> </a>');
                                }
                            }});
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    //Confirm choice to mark sensitive person+
    function sensitivepersonacl(person_acl) {
        if (person_acl !== '')
        {
            var request = $.ajax({
                url: "<?php echo URL::site("persons/sensitiveperson_acl_data"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: person_acl},
                success: function (responseTex)
                {
                    if (responseTex == 2)
                    {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    $("#modal-default").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#acl_user_data").html(responseTex);

                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function ConfirmMarkSensitive(id) {
        $.confirm({
            'title': 'Mark Sensitive confirmation',
            'message': 'Do you really want to Add this Person as your Sensitive person?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::base() . "persons/addsensitveperson/"; ?>' + id,
                            success: function (result) {
                                // if(result==1|| result==0)
                                // {
                                //
                                // }

                                if (result == 2)
                                {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                if (result == '-2') {
                                    alert('Access denied, contact your technical support team');
                                } else {
                                    // $("#user").show();
                                    $("#sensitive").html('<a class="bg-aqua" href="javascript:ConfirmUnmarkSensitive(<?php echo $person_id; ?>)"> <i class="fa fa-star"></i> </a>');
                                    $("#sens").html('<a href="javascript:sensitivepersonacl(<?php echo $person_id; ?>)" class="bg-aqua"> Sensitive: </a>');

                                }
                            }});
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    //remove person from sensitve list
    function ConfirmUnmarkSensitive(id) {
        $.confirm({
            'title': 'Delete Sensitive confirmation',
            'message': 'Do you really want to Delete this Person from your Sensitve person?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::base() . "persons/deletesensitiveperson/"; ?>' + id,
                            success: function (result) {
                                if (result == 2)
                                {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                if (result == '-2') {
                                    alert('Access denied, contact your technical support team');
                                } else {
                                    $("#sensitive").html('<a class="bg-aqua" href="javascript:ConfirmMarkSensitive(<?php echo $person_id; ?>)"> <i class="fa fa-star-o"></i> </a>');
                                    $("#sens").html('Sensitive:');
                                }
                            }});
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    //function to call nadra profile
    function requestnadra(cnic, personID) {
        var result = {cnic: cnic, pid: personID}
        $.ajax({
            url: "<?php echo URL::site("Persons/nadra_profile"); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (msg) {
                if (msg == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                var newDoc = document.open("text/html", "replace");
                newDoc.write(msg);
                newDoc.close();
            }
        });

    }
</script> 

<script src="https://maps.google.com/maps/api/js?key=AIzaSyBwL7lXZwan5Cp6GjddHiNNM3VJhZ3oYvE&sensor=false" type="text/javascript"></script>


<script src="<?php echo URL::base() . 'plugins/chartjs/Chart.min.js'; ?>"></script>

<script>
    $(document).ready(function () {
        //ajax call to update person_sims_count
        $.ajax({
            url: "<?php echo URL::site("Persons/person_sims_count"); ?>",
            //type: 'POST',
            //data: result,
            data: {id: '<?php echo $_GET['id']; ?>'},
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (msg) {
                if (msg == '-2')
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#person_sims_count").html(msg);
                
            }
        });
        //Person tags form submit through ajax call
        $('#blocknumber').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (msg) {
                    $("#tagperson").modal("hide");
                    if (msg == 1) {
                        swal("Congratulations!", "Person Tags Updated Successfully.", "success");
                    }
                    if (msg == 6)
                    {
                        swal("System Error", "Contact Support Team.", "error");
                    }

                },
                error: function (data) {
                    console.log("error");
                    console.log(data);
                }
            });
        });

        //ajax call to update person_total_sims_detail
        $.ajax({
            url: "<?php echo URL::site("Persons/person_total_sims_detail"); ?>",
            //type: 'POST',
            //data: result,
            data: {id: '<?php echo $_GET['id']; ?>'},
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (msg) {
                if (msg == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#person_total_sims_detail").html(msg);
            }
        });
        //ajax call to update person_device_count
        $.ajax({
            url: "<?php echo URL::site("Persons/person_device_count"); ?>",
            //type: 'POST',
            //data: result,
            data: {id: '<?php echo $_GET['id']; ?>'},
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (msg) {
                if (msg == '-2')
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#person_device_count").html(msg);
            }
        });
        //ajax call to update person_total_devices_details
        $.ajax({
            url: "<?php echo URL::site("Persons/person_total_devices_details"); ?>",
            //type: 'POST',
            //data: result,
            data: {id: '<?php echo $_GET['id']; ?>'},
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (msg) {
                if (msg == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#person_total_devices_details").html(msg);
            }
        });
        //ajax call to update person_link_with_projects
        $.ajax({
            url: "<?php echo URL::site("Persons/person_link_with_projects"); ?>",
            //type: 'POST',
            //data: result,
            data: {id: '<?php echo $_GET['id']; ?>'},
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (msg) {
                if (msg == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#person_link_with_projects").html(msg);
            }
        });

        $("#categorychange").validate({
            rules: {
                "inputproject[]": {
                    required: true,
                    check_list: true
                },
                category: {
                    required: true,
                },
                inputreason: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 5,
                    maxlength: 1000
                }
            },
            messages: {
                inputproject: {
                    required: "Select project from list"
                },
                cnic_number: {
                    required: "Please Select Category",
                },
                inputreason: {
                    required: "Enter reason for request",
                    maxlenght: "Maximum character limit is 500",
                    minlength: "Min character limit is 10"
                }
            }

            // $('#upload').show()
        });

        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .,/]+$/);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "" || sel == 0) {
                return false;
            } else {
                return true;
            }
        }, "<span>Select One</span>");
    })
    // validate other number form
    $("#othernumber_add").validate({
        errorElement: 'span',
        rules: {
            othernumber: {
                required: true,
                number: true,
                startingzero: true,
                minlength: 8,
                maxlength: 15
            }
        },
        submitHandler: function () {
            $("#othernumber_add").submit();

        }

        // $('#upload').show()
    });
    // Validators start with zero
    jQuery.validator.addMethod("startingzero", function (value, element) {
        return this.optional(element) || value == value.match(/^[1-9]\d+$/);
    }, "Number can't start with zero, ignore zero");
    //now this
    //Confirm choice to mark favourite person
    function tagperson(personid)
    {
        $("#tagperson").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);
    }


    // request subscriber
    function external_search_model(mobile, cnicnumber) {
        $("#external_search_model").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);
        var cnic = cnicnumber;
        var msisdn = mobile;
        var is_foreigner = 0;
        if (msisdn != 0 || msisdn != '') {
            $("#external_search_key").val(msisdn);
            search_local_subscriber_detail('msisdn', msisdn);
        } else if (cnic != 0 || cnic != '') {
            $("#external_search_key").val(cnic);
            if (is_foreigner == 1) {
                search_foreinger_detail('foreigner_profile', cnic);
            } else {
                search_local_subscriber_detail('cnic', cnic);
            }
        } else if (imsi != 0 || imsi != '') {
            $("#external_search_key").val(imsi);
            search_local_subscriber_detail('imsi', imsi);
        }

    }
    //function to search subscriber in local sources
    function search_local_subscriber_detail(search_type, search_value) {
        var result = {search_type: search_type, search_value: search_value};
        $.ajax({
            url: "<?php echo URL::site('userreports/msisdn_data_search', TRUE); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (msg) {
                $("#external_search_results").html(msg);
            }
        });

    }
    //function to call request page of verisys          
    function requestnadraverysis(cnicnumber, personid) {
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestverisys/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'cnicnumber',
            'value': cnicnumber,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': personid,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');

    }
    //function to call request page of family tree
    function requestfamilytree1(cnicnumber, personid) {
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestfamtree/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'cnicnumber',
            'value': cnicnumber,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': personid,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');

    }
    //function to call request page of TravelHistory          
    function requesttravelhistory(cnicnumber, personid) {
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requesttravelhistory/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'cnicnumber',
            'value': cnicnumber,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': personid,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');

    }
    //function to call request page of Family Tree         
    function requestfamilytree(cnicnumber, personid) {
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestfamilytree/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'cnicnumber',
            'value': cnicnumber,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': personid,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');

    }
    //function to call request page of Branchless banking CTFU         
    function requestbranchlessbanking(requested_value,personid) {        
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestbranchlessbanking/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'requested_value',
            'value': requested_value,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'pid',
            'value': personid,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');

    }
    //family tree request status detail        
    function familytreedetail(request_id) {
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/familytree_detail/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'request_id',
            'value': request_id,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');
    }
    function saveothernumber() {
        var person_id = $("#person_id").val();
        var number_type = $("#number_type").val();
        var othernumber = $("#othernumber").val();
        var result = {person_id: person_id, number_type: number_type, othernumber: othernumber}
        if ($('#othernumber_add').valid())
        {
            $.ajax({
                url: "<?php echo URL::site('Othernumbersearch/add_other_number', TRUE); ?>",
                type: 'POST',
                data: result,
                cache: false,
                success: function (response) {
                    if (response == 1) {
                        swal("Congratulations!", "Record Updated successfully.", "success");
                        $('#add_other_number_model').modal('toggle');
                        load_other_numbers();
                    }
                    if (response == '-2') {
                        swal("System Error", "Duplicate! Number already exists.", "error");
                    }
                    if (response == '-3') {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                }
            });
        }
    }


    //date of birth form validation
    $("#update_date_of_birth_form").validate({
        errorElement: 'span',
        rules: {
            dateofbirth: {
                sdate_value: true,
//                birth: true,
            },
            messages: {
                dateofbirth: {
                    sdate_value: "Please Select Valid Date",
//                minbirth: "vvvvvvvvvvvvvvvvvvvv",
                }
            }
        },
        submitHandler: function () {
            $("#update_date_of_birth_form").submit();

        }

        // $('#upload').show()
    });
//    $.validator.addMethod("minbirth", function (value, element) {
//        var year = value.split('/');
//        if ( value.match(/^\d\d?\/\d\d?\/\d\d\d\d$/) && parseInt(year[2]) <= 2002 )
//            return true;
//        else
//            return false;
//    },"<span>PsValid Date</span>");
    $.validator.addMethod("sdate_value", function (sel, element) {
        if ($('#dateofbirth').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Please Select Valid Date</span>");
    //update date of birth
    function updatedateofbirth() {
        var dateofbirth = $("#dateofbirth").val();
        var person_id = $("#person_id_dob").val();
        var result = {dateofbirth: dateofbirth, person_id: person_id}
        if ($('#update_date_of_birth_form').valid())
        {
            $.ajax({
                url: "<?php echo URL::site('Personprofile/person_dob_update', TRUE); ?>",
                type: 'POST',
                data: result,
                cache: false,
                success: function (response) {
                    if (response == 1) {
                        swal("Congratulations!", "Date of Birth Updated.", "success");
                        $('#update_date_of_birth').modal('toggle');
                        location.reload();
                    } else {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                }
            });
        }
    }
    function load_other_numbers() {
        //ajax call to update person_other_numbers
        $.ajax({
            url: "<?php echo URL::site("Persons/person_other_numbers"); ?>",
            //type: 'POST',
            //data: result,
            data: {id: '<?php echo $_GET['id']; ?>'},
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (msg) {
                $("#person_other_numbers").html(msg);
            }
        });
    }

    $('#person_last_location').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            //person last location details
            if (!$(this).hasClass("already-done")) {
                $.ajax({
                    url: "<?php echo URL::site("Persons/personlastlocation"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {id: '<?php echo $_GET['id']; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'json',
                    success: function (msg) {
                        $("#person_last_location").addClass("already-done");
                        if (msg == 2)
                        {
                            swal("System Error", "Contact Support Team.", "error");
                        }
                        if (msg == '-1')
                        {
                            $('#map').hide();
                            $('#nodata').show();
                        } else {
                            $('#nodata').hide();
                            $('#map').show();
                            var location = msg.location;
                            var phonenumber = msg.phone_number;
                            var time = msg.time;
                            var latitude = msg.latitude;
                            var longitude = msg.longitude;
                            $(function () {
                                var locations = [
                                    [location + ' (0' + phonenumber + ') Time: ' + time, latitude, longitude]
                                ];

                                var map = new google.maps.Map(document.getElementById('map'), {
                                    zoom: 10,
                                    //center: new google.maps.LatLng(31.550453, 74.377699),
                                    center: new google.maps.LatLng(latitude, longitude),
                                    mapTypeId: google.maps.MapTypeId.ROADMAP
                                });

                                var infowindow = new google.maps.InfoWindow();

                                var marker, i;

                                for (i = 0; i < locations.length; i++) {
                                    marker = new google.maps.Marker({
                                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                                        map: map
                                    });

                                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                                        return function () {
                                            infowindow.setContent(locations[i][0]);
                                            infowindow.open(map, marker);
                                        }
                                    })(marker, i));
                                }

                            });
                        }   //here
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
    $('#current_loc_history').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person current location history
                $.ajax({
                    url: "<?php echo URL::site("Persons/person_current_location_history"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {id: '<?php echo $_GET['id']; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {
//                if (msg == 2) 
//			{
//			swal("System Error", "Contact Support Team.", "error");
//			}
                        $("#current_loc_history").addClass("already-done");
                        $("#current_location_history").html(msg);
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
    $('#call_sms_log').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {

                //ajax call to update person call and sms log
                $.ajax({
                    url: "<?php echo URL::site("Persons/callandsmslog"); ?>",
                    //type: 'POST',
                    //data: result,
                    cache: false,
                    //dataType: "text",
                    data: {id: '<?php echo $_GET['id']; ?>'},
                    dataType: 'json',
                    success: function (msg) {
                        $("#call_sms_log").addClass("panelisopen");

                        if (msg == 2)
                        {
                            swal("System Error", "Contact Support Team.", "error");
                        }
                        if (msg == '-1')
                        {
                            $('#areaChart').hide();
                            $('#call_sms_ajax_loader').hide();
                            $('#nodatachart').show();
                        } else {
                            $('#call_sms_ajax_loader').hide();
                            $('#nodatachart').hide();
                            $('#areaChart').show();
                            var calls = msg.calls;
                            var sms = msg.sms;
                            var month = msg.month;
                            $(function () {
                                var month = msg.month;

                                $(function () {
                                    /* ChartJS
                                     * -------
                                     * Here we will create a few charts using ChartJS
                                     */

                                    //--------------
                                    //- AREA CHART -
                                    //--------------

                                    // Get context with jQuery - using jQuery's .get() method.
                                    var areaChartCanvas = $("#areaChart").get(0).getContext("2d");
                                    // This will get the first returned node in the jQuery collection.
                                    var areaChart = new Chart(areaChartCanvas);

                                    var areaChartData = {
                                        labels: month, //["January", "February", "March", "April", "May", "June", "July"],
                                        datasets: [
                                            {
                                                label: "SMS",
                                                fillColor: "rgba(210, 214, 222, 1)",
                                                strokeColor: "rgba(210, 214, 222, 1)",
                                                pointColor: "rgba(210, 214, 222, 1)",
                                                pointStrokeColor: "#c1c7d1",
                                                pointHighlightFill: "#fff",
                                                pointHighlightStroke: "rgba(220,220,220,1)",
                                                //  data: [65, 59, 80, 81, 56, 55, 40]
                                                data: sms
                                            },
                                            {
                                                label: "Call",
                                                fillColor: "rgba(60,141,188,0.9)",
                                                strokeColor: "rgba(60,141,188,0.8)",
                                                pointColor: "#3b8bba",
                                                pointStrokeColor: "rgba(60,141,188,1)",
                                                pointHighlightFill: "#fff",
                                                pointHighlightStroke: "rgba(60,141,188,1)",
                                                // data: [28, 48, 40, 19, 86, 27, 90]
                                                data: calls
                                            }
                                        ]
                                    };

                                    var areaChartOptions = {
                                        //Boolean - If we should show the scale at all
                                        showScale: true,
                                        //Boolean - Whether grid lines are shown across the chart
                                        scaleShowGridLines: false,
                                        //String - Colour of the grid lines
                                        scaleGridLineColor: "rgba(0,0,0,.05)",
                                        //Number - Width of the grid lines
                                        scaleGridLineWidth: 1,
                                        //Boolean - Whether to show horizontal lines (except X axis)
                                        scaleShowHorizontalLines: true,
                                        //Boolean - Whether to show vertical lines (except Y axis)
                                        scaleShowVerticalLines: true,
                                        //Boolean - Whether the line is curved between points
                                        bezierCurve: true,
                                        //Number - Tension of the bezier curve between points
                                        bezierCurveTension: 0.3,
                                        //Boolean - Whether to show a dot for each point
                                        pointDot: false,
                                        //Number - Radius of each point dot in pixels
                                        pointDotRadius: 4,
                                        //Number - Pixel width of point dot stroke
                                        pointDotStrokeWidth: 1,
                                        //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                                        pointHitDetectionRadius: 20,
                                        //Boolean - Whether to show a stroke for datasets
                                        datasetStroke: true,
                                        //Number - Pixel width of dataset stroke
                                        datasetStrokeWidth: 2,
                                        //Boolean - Whether to fill the dataset with a color
                                        datasetFill: true,
                                        //String - A legend template
                                        legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                                        //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                                        maintainAspectRatio: true,
                                        //Boolean - whether to make the chart responsive to window resizing
                                        responsive: true
                                    };
                                    //Create the line chart
                                    areaChart.Line(areaChartData, areaChartOptions);
                                });

                            });
                            //here
                        }
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
    $('#person_last_cm').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person person_last_call_sms
                $.ajax({
                    url: "<?php echo URL::site("Persons/person_last_call_sms"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {id: '<?php echo $_GET['id']; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {
                        $('#person_last_cm').addClass("panelisopen");
                        if (msg == 2)
                        {
                            swal("System Error", "Contact Support Team.", "error");
                        }
                        $("#person_last_call_sms").html(msg);
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
    $('#favourite_person').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person person_favourite_person_list
                $.ajax({
                    url: "<?php echo URL::site("Persons/person_favourite_person_list"); ?>",
                    //type: 'POST',
                    //data: result,https://www.facebook.com/discreetsoft/
                    data: {id: '<?php echo $_GET['id']; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {
                        $('#favourite_person').addClass("panelisopen");
                        if (msg == -2)
                        {
                            swal("System Error", "Contact Support Team.", "error");
                        }
                        $("#person_favourite_person_list").html(msg);
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
    $('#other_number').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person_other_numbers
                $.ajax({
                    url: "<?php echo URL::site("Persons/person_other_numbers"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {id: '<?php echo $_GET['id']; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {
                        $('#other_number').addClass("panelisopen");
                        if (msg == -2)
                        {
                            swal("System Error", "Contact Support Team.", "error");
                        }
                        $("#person_other_numbers").html(msg);
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
    $('#other_information').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person_affiliations_and_social_links
                $.ajax({
                    url: "<?php echo URL::site("Persons/person_affiliations_and_social_links"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {id: '<?php echo $_GET['id']; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {
                        $("#other_information").addClass("panelisopen");
                        if (msg == 2)
                        {
                            swal("System Error", "Contact Support Team.", "error");
                        }
                        $("#person_affiliations_and_social_links").html(msg);
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
//    $('#person_last_location').click(function () {
//          if($(this).hasClass("panelisopen")){
//          $(this).removeClass("panelisopen");
//          if(!$(this).hasClass("already-done")){
//          
//          }
//      } else {          
//          $(this).addClass("panelisopen");
//      }
//  });

//Date picker
    $('#dateofbirth').datepicker({
        autoclose: true
    });
    $(document).ready(function () {
        <?php
        $person_total_db_match= Helpers_Utilities::get_person_db_match_count($person_id);
        $person_total_favourite_person= Helpers_Utilities::get_person_total_favourite_person($person_id);
        $linked_with_affiliated_person= Helpers_Utilities::get_linked_with_affiliated_person($person_id);
        if(!empty($person_total_db_match)){
        ?>
        msgboxbox.show("<?php echo 'Total DB Match: ' . $person_total_db_match ?>", null);
        <?php }
        if(!empty($person_total_favourite_person)){
            ?>
        msgboxbox.show("<?php echo 'Total Favourite Person: ' . $person_total_favourite_person?>", null);
        <?php }
        if(!empty($linked_with_affiliated_person)){
            ?>
        msgboxbox.show("<?php echo 'Total Affliation Person: '. $linked_with_affiliated_person ?>", null);
        <?php } ?>
    });
</script>

<script>
    

    class MessageBox {
        constructor(id, option) {
            this.id = id;
            this.option = option;
        }

        show(msg, label = "CLOSE", callback = null) {
            if (this.id === null || typeof this.id === "undefined") {
                // if the ID is not set or if the ID is undefined

                throw "Please set the 'ID' of the message box container.";
            }

            if (msg === "" || typeof msg === "undefined" || msg === null) {
                // If the 'msg' parameter is not set, throw an error

                throw "The 'msg' parameter is empty.";
            }

            if (typeof label === "undefined" || label === null) {
                // Of the label is undefined, or if it is null

                label = "CLOSE";
            }

            let option = this.option;

            let msgboxArea = document.querySelector(this.id);
            let msgboxBox = document.createElement("DIV");
            let msgboxContent = document.createElement("DIV");
            let msgboxClose = document.createElement("A");

            if (msgboxArea === null) {
                // If there is no Message Box container found.

                throw "The Message Box container is not found.";
            }

            // Content area of the message box
            msgboxContent.classList.add("msgbox-content");
            msgboxContent.innerText = msg;

            // Close burtton of the message box
            msgboxClose.classList.add("msgbox-close");
            msgboxClose.setAttribute("href", "#");
            msgboxClose.innerText = label;

            // Container of the Message Box element
            msgboxBox.classList.add("msgbox-box");
            msgboxBox.appendChild(msgboxContent);

            if (option.hideCloseButton === false
                || typeof option.hideCloseButton === "undefined") {
                // If the hideCloseButton flag is false, or if it is undefined

                // Append the close button to the container
                msgboxBox.appendChild(msgboxClose);
            }

            msgboxArea.appendChild(msgboxBox);

            msgboxClose.addEventListener("click", (evt) => {
                evt.preventDefault();

                if (msgboxBox.classList.contains("msgbox-box-hide")) {
                    // If the message box already have 'msgbox-box-hide' class
                    // This is to avoid the appearance of exception if the close
                    // button is clicked multiple times or clicked while hiding.

                    return;
                }

                this.hide(msgboxBox, callback);
            });

            if (option.closeTime > 0) {
                this.msgboxTimeout = setTimeout(() => {
                    this.hide(msgboxBox, callback);
                }, option.closeTime);
            }
        }

        hide(msgboxBox, callback) {
            if (msgboxBox !== null) {
                // If the Message Box is not yet closed

                msgboxBox.classList.add("msgbox-box-hide");
            }

            msgboxBox.addEventListener("transitionend", () => {
                if (msgboxBox !== null) {
                    // If the Message Box is not yet closed

                    msgboxBox.parentNode.removeChild(msgboxBox);

                    clearTimeout(this.msgboxTimeout);

                    if (callback !== null) {
                        // If the callback parameter is not null
                        callback();
                    }
                }
            });
        }
    }

    let msgboxShowMessage = document.querySelector("#msgboxShowMessage");
    let msgboxHiddenClose = document.querySelector("#msgboxHiddenClose");

    // Creation of Message Box class, and the sample usage
    let msgboxbox = new MessageBox("#msgbox-area", {
        closeTime: 10000,
        hideCloseButton: false
    });
    let msgboxboxPersistent = new MessageBox("#msgbox-area", {
        closeTime: 0
    });
    let msgboxNoClose = new MessageBox("#msgbox-area", {
        closeTime: 5000,
        hideCloseButton: true
    });


</script>
<style>
    .msgbox-area {
        max-height: 100%;
        position: fixed;
        bottom: 15px;
        left: 20px;
        right: 20px;
    }

    .msgbox-area .msgbox-box {
        font-size: inherit;
        color: #ffffff;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 18px 20px;
        margin: 0 0 15px;
        display: flex;
        align-items: center;
        position: relative;
        border-radius: 12px;
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.65);
        transition: opacity 300ms ease-in;
    }

    .msgbox-area .msgbox-box.msgbox-box-hide {
        opacity: 0;
    }

    .msgbox-area .msgbox-box:last-child {
        margin: 0;
    }

    .msgbox-area .msgbox-content {
        flex-shrink: 1;
    }

    .msgbox-area .msgbox-close {
        color: #ffffff;
        font-weight: bold;
        text-decoration: none;
        margin: 0 0 0 20px;
        flex-grow: 0;
        flex-shrink: 0;
        position: relative;
        transition: text-shadow 225ms ease-out;
    }

    .msgbox-area .msgbox-close:hover {
        text-shadow: 0 0 3px #efefef;
    }

    @media (min-width: 481px) and (max-width: 767px) {
        .msgbox-area {
            left: 80px;
            right: 80px;
        }
    }

    @media (min-width: 768px) {
        .msgbox-area {
            width: 480px;
            height: 0;
            top: 50%;
            left: auto;
            right: 15px;
        }
    }


     .vitem {
         height: 90px;
         line-height: 30px;
     }
    .vwrap {
        height: 286px;
        line-height: 30px;
        background: white local;
        overflow: hidden; /* HIDE SCROLL BAR */
    }
    /* (C) TICKER ITEMS */
    .vitem { text-align: center; }

    /* (D) ANIMATION - MOVE ITEMS FROM TOP TO BOTTOM */
    /* CHANGE KEYFRAMES IF YOU ADD/REMOVE ITEMS */
    .vmove { position: relative; }
    @keyframes tickerv {
        0% { bottom: 0; } /* FIRST ITEM */
        30% { bottom: 30px; } /* SECOND ITEM */
        60% { bottom: 60px; } /* THIRD ITEM */
        90% { bottom: 90px; } /* FORTH ITEM */
        100% { bottom: 0; } /* BACK TO FIRST */
    }
    .vmove {
        animation-name: tickerv;
        animation-duration: 10s;
        animation-iteration-count: infinite;
        animation-timing-function: cubic-bezier(1, 0, .5, 0);
    }
    .vmove:hover { animation-play-state: paused; }


</style>
<div id="msgbox-area" class="msgbox-area"></div>

<div id="cdrModal" style="display:none;">
    <div id="cdrModalContent">
        <h3>CDR Requested</h3>

        <div id="modalBody">Loading...</div>

        <br>
        <button id="closeModalBtn">Close</button>
    </div>
</div>

<script>
function requestcdrdownload(sim, person_id)
{
    // Apply overlay style
    $("#cdrModal").css({
        position: "fixed",
        top: "0",
        left: "0",
        width: "100%",
        height: "100%",
        background: "rgba(0,0,0,0.6)",
        zIndex: "9999"
    });

    // Apply modal box style
    $("#cdrModalContent").css({
        background: "#fff",
        width: "400px",
        padding: "20px",
        borderRadius: "5px",
        position: "absolute",
        top: "50%",
        left: "50%",
        transform: "translate(-50%, -50%)"
    });

    $("#modalBody").html("Loading...");
    $("#cdrModal").fadeIn();

    $.ajax({
        url: "<?php echo URL::site('Persons/get_cdr_data'); ?>",
        type: "POST",
        data: {
            sim: sim,
            person_id: person_id
        },
        cache: false,
        dataType: "html",
        success: function (msg)
        {
            //console.log("CDR Response:", msg);

            if ($.trim(msg) == "2")
            {
                $("#modalBody").html(
                    "<span style='color:red;'>System Error. Contact Support.</span>"
                );
            }
            else
            {
                $("#modalBody").html(msg);
            }
        },
        error: function ()
        {
            $("#modalBody").html(
                "<span style='color:red;'>Server Error</span>"
            );
        }
    });
}

// Close button
$(document).on("click", "#closeModalBtn", function(){
    $("#cdrModal").fadeOut();
});

// Close when clicking outside modal box
$(document).on("click", "#cdrModal", function(e){
    if(e.target.id === "cdrModal"){
        $("#cdrModal").fadeOut();
    }
});
</script>
