<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//get person assets dowload path
$person_download_data_path = !empty(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) ? Helpers_Person::get_person_download_data_path(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) : '';
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Person's Portal
        <small><?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id=' . $_GET['id']); ?>">Person Dashboard </a></li>
        <li class="active">One Page Perfoma</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">    
    <div class="row">
        <div class="col-md-12">
            <div class="">
                <div class="">
                    <div class="box box-primary">
                        <form id="one_page_form" name="one_page_form" class="ipf-form cell_log_summary" method="POST" action="<?php echo url::site() . 'persons/one_page_performa/?id=' . $_GET['id'] ?>" >                                 
                            <div class="box box-default collapsed-box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Select Options For One Page Perfoma</h3>                                                
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body" style="display:block;">                                
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mobile Number</label>
                                            <select class="form-control " name="phone_number" id="phone_number" onchange="person_bparty()">                                                        
                                                <option value="">Please Select Person Number</option>
                                                <?php
                                                try {
                                                    $p_id = isset($_GET['id']) ? Helpers_Utilities::encrypted_key($_GET['id'], "decrypt") : 0;
                                                    $sims_list = Helpers_Person::get_person_inuse_SIMs($p_id);
                                                    foreach ($sims_list as $sim) {
                                                        ?>
                                                        <option <?php echo (!empty($search_post['phone_number']) && ($search_post['phone_number'] == $sim->phone_number)) ? 'selected' : '' ?> value=<?php echo $sim->phone_number ?> > <?php echo $sim->phone_number ?> </option>
                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select>
                                        </div>          
                                    </div>                                                
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="searchfield">Start Date (mm/dd/yyyy)</label>
                                            <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="startdate" id="startdate" value="<?php echo (!empty($search_post['startdate']) ? $search_post['startdate'] : ''); ?>">                                        

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="searchfield">End Date (mm/dd/yyyy)</label>
                                            <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="enddate" id="enddate" value="<?php echo (!empty($search_post['enddate']) ? $search_post['enddate'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group" >
                                            <label for="quickoption" class="control-label">Quick Options (for start and End Date)</label>
                                            <div class="col-md-12" id="quickoption">
                                                <button type="button" onclick="dateonemonth()" class="btn btn-primary" >Last 1 Month </button>                                            
                                                <button type="button" onclick="datetwomonths()" class="btn btn-primary" >Last 2 Months </button>                                            
                                                <button type="button" onclick="datethreemonths()" class="btn btn-primary" >Last 3 Months </button>
                                                <button type="button" onclick="datesixmonths()" class="btn btn-primary" >Last 6 Months </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">                                    
                                        <div class="form-group pull-right" style="">                                         
                                            <!--<button type="submit" class="btn btn-primary">Search</button>-->
                                            <button type="submit" class="btn btn-primary">Generate One Pager</button>                                                        
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                    <!-- /.row -->
                                </div>        
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($search_post)) { ?>
            <div class="col-md-12">
                <div class="box"> 
                    <div class="box-header with-border">
                        <span>
                            <h3 class="box-title">One Page Perfoma of <?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></h3>
                        </span>
                        <span class="badge badge-primary">
                            <h3 class="box-title">Mobile Number: <?php echo $search_post['phone_number']; ?></h3>
                        </span>
                        <span class="badge badge-success">
                            <h3 class="box-title">Date: <?php echo $search_post['startdate'] . ' To ' . $search_post['enddate']; ?></h3>
                        </span>
                    </div>
                    <div class="box-body">
                        <div class="col-md-12">

                        </div>
                        <div class="col-md-12">
                            <div id="image_data" class="col-md-2">
                                <div class="img-circle" style="float: left;height: 100%;width: 100%">
                                    <?php
                                    if (!empty($person_data['basicinfo']->image_url) || $person_data['basicinfo']->image_url != 0 || !empty($person_download_data_path)) {
                                        $profilepiclink = $person_download_data_path . $person_data['basicinfo']->image_url;
                                        echo HTML::image("{$profilepiclink}", array("width" => "100%"));
                                    } else {
                                        echo HTML::image("dist/img/avtar6.jpg", array("width" => "100%"));
                                    }
                                    ?>                               
                                </div>                        
                            </div>
                            <div id="personal_data" class="col-md-6">
                                <?php
                                $person_name = $person_data['basicinfo']->first_name . ' ' . $person_data['basicinfo']->last_name;
                                $person_father_name = $person_data['basicinfo']->father_name;
                                $person_cnic = $person_data['basicinfo']->cnic_number;
                                $person_address = $person_data['basicinfo']->address;
                                $person_id = $person_data['basicinfo']->person_id;
                                $category_id = Helpers_Person::get_person_category_id($person_id);
                                $category_name = Helpers_Utilities::get_category_name($category_id);
                                ?>
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <i class="fa fa-mobile"></i>
                                        <h3 class="box-title">Basic Information </h3>
                                    </div>                                
                                    <div class="box-body">
                                        <ul class="todo-list">
                                            <li class="dashboard-sticky-danger">
                                                <span class="text-black"> <b>Mobile: </b> <?php echo $person_data['siminfo']; ?> </span>
                                            </li>
                                            <li class="dashboard-sticky-danger">
                                                <span class="text-black"> <b>Name: </b> <?php echo $person_name; ?> </span>
                                            </li>
                                            <li class="dashboard-sticky-danger">
                                                <span class="text-black"> <b>Father/Husband Name: </b><?php echo $person_father_name; ?> </span>
                                            </li>
                                            <li class='dashboard-sticky-danger'>
                                                <span class="text-black"> 
                                                    <b>CNIC: </b>  <?php echo $person_cnic; ?>   
                                                </span>
                                                <?php
                                                    $url_path = "http://www.ims.ctdpunjab.com/frontcat/pid?cnic=" . (int)trim($person_cnic);
                                                    $url = file_get_contents($url_path);
                                                    $wms_pid = !empty($url)? $url:'Not Found';
                                                    echo '<b>WMS PID:</b> ' . $wms_pid;
                                                ?>
                                            </li>   
                                            <li class='dashboard-sticky-danger'>
                                                <span class="text-black"> <b>Category: </b>  <?php echo $category_name; ?>   </span>
                                            </li>   
                                            <li class='dashboard-sticky-danger'>
                                                <span class="text-black"> <b>Address: </b>  <?php echo $person_address; ?>   </span>
                                            </li>   
                                        </ul>
                                    </div>                                                           
                                </div>                            
                            </div>
                            <div id="sims_data" class="col-md-4">
                                <div class="box box-solid">
                                    <div class="box-header with-border">
                                        <i class="fa fa-mobile"></i>
                                        <h3 class="box-title">Devices Used</h3>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body no-padding">
                                        <table class="table table-striped">
                                            <tbody>
                                                <tr>
                                                    <th>IMEI Number</th>
                                                    <th>Last Interaction</th>
                                                </tr>
                                                <?php
                                                $device_data = $person_data['deviceinfo'];
                                                if (!empty($device_data)) {
                                                    foreach ($device_data as $devices) {
                                                        ?>
                                                        <tr>
                                                            <td> <?php echo $devices->imei_number; ?> </td>
                                                            <td> <?php echo $devices->last_interaction_at; ?> </td>
                                                        </tr>
                                                    <?php }
                                                } else { ?> 
                                                <td style="text-align: center;" colspan="2"> No Data Found </td>
    <?php } ?>

                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.box-body -->
                                </div>                       
                            </div>  
                        </div>                    
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Favourite Callers of <?php echo!empty(trim($person_name)) ? $person_name : 'N/A' ?> </h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body table-responsive no-padding">
                                    <table class="table table-hover">
                                        <tbody>
                                            <tr>
                                                <th>A Party</th>
                                                <th>B Party</th>
                                                <th>Name</th>
                                                <th>CNIC</th>
                                                <th>Address</th>
                                                <th>Calls</th>
                                                <th>SMS</th>
                                            </tr>
                                            <?php
                                            //this
                                            $favoriteperson_data = $person_data['favouriteperson'];
                                            if (!empty($favoriteperson_data)) {
                                                foreach ($favoriteperson_data as $persons) {
                                                    $aparty = $persons['phone'];
                                                    $bparty = $persons['otherphone'];
                                                    $bparty_id = $persons['other_id'];
                                                    $bparty_name = '';
                                                    $bparty_cnic = '';
                                                    $bparty_address = '';
                                                    $bparty_calls = $persons['calls'];
                                                    $bparty_sms = $persons['sms'];
                                                    if (!empty($bparty_id)) {
                                                        $bparty_name = Helpers_Person::get_person_name($bparty_id);
                                                        $bparty_name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($bparty_id, "encrypt")) . '" > [View Profile] </a>';
                                                        $bparty_cnic = Helpers_Person::get_person_cnic($bparty_id);
                                                        $bparty_address = Helpers_Person::get_person_address($bparty_id);
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td> <?php echo $aparty; ?> </td>
                                                        <td> <?php echo $bparty; ?> </td>
                                                        <td> <?php echo $bparty_name; ?> </td>
                                                        <td> <?php echo $bparty_cnic; ?> </td>
                                                        <td> <?php echo $bparty_address; ?> </td>
                                                        <td> <?php echo $bparty_calls; ?> </td>
                                                        <td> <?php echo $bparty_sms; ?> </td>
                                                    </tr>          
                                            <?php }
                                        } else { ?> 
                                            <td style="text-align: center;" colspan="7"> No Data Found </td>
    <?php } ?> 
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>   
                        <div class="col-md-12">                       
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">DB Match of  <?php echo!empty(trim($person_name)) ? $person_name : 'N/A' ?> </h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th>A Party</th>
                                                <th>B Party</th>
                                                <th>Name</th>
                                                <th>CNIC</th>
                                                <th>Address</th>
                                                <th>O Calls</th>
                                                <th>I Calls</th>
                                                <th>O SMS</th>
                                                <th>I SMS</th>
                                            </tr>
                                            <?php
                                            $dbmatch_data = $person_data['dbmatch'];
                                            if (!empty($dbmatch_data)) {
                                                foreach ($dbmatch_data as $data) {
                                                    if ($data['other_id'] == 0)
                                                        continue;
                                                    ?>
                                                    <tr>
                                                        <td> <?php echo $data['phone']; ?> </td>                                                                                                    
                                                        <td> <?php echo $data['bparty']; ?> </td>                                                                                                    
                                                        <td> 
                                                            <?php
                                                                $name = '';
                                                                $other_id = $data['other_id'];
                                                                $name .= Helpers_Person::get_person_name($other_id);
                                                                $tags = Helpers_Watchlist::get_person_tags_data_comma($other_id);
                                                                $name .= '<a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($other_id, "encrypt")) . '" > [View Profile] </a> <br>';
                                                                $name .= $tags;
                                                                echo $name;
                                                            ?> 
                                                        </td>                                                        
                                                        <td> <?php echo !empty($data['other_id']) ? Helpers_Person::get_person_cnic($data['other_id']) : '-'; ?> </td>                                                    
                                                        <td> <?php echo !empty($data['other_id']) ? Helpers_Person::get_person_address($data['other_id']) : '-'; ?> </td>                                                    
                                                        <td> <?php echo isset($data['outgoing_calls']) ? $data['outgoing_calls'] : '-'; ?> </td>
                                                        <td> <?php echo isset($data['incoming_calls']) ? $data['incoming_calls'] : '-'; ?> </td>
                                                        <td> <?php echo isset($data['outgoing_sms'])   ? $data['outgoing_sms']   : '-'; ?> </td>
                                                        <td> <?php echo isset($data['incoming_sms'])   ? $data['incoming_sms']   : '-'; ?> </td>
                                                    </tr>
                                            <?php }
                                        } else { ?> 
                                            <td style="text-align: center;" colspan="9"> No Data Found </td>
                                        <?php } ?> 
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                        <hr class="style14 col-md-12">  
                        <div class="col-md-12 ">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Location Summary of  <?php echo!empty(trim($person_name)) ? $person_name : 'N/A' ?> </h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th>Phone Number</th>
                                                <th>Location</th>
                                                <th>Location Count</th>
                                            </tr>
                                            <?php
                                            $location_data = $person_data['person_locations'];
                                            if (!empty($location_data)) {
                                                foreach ($location_data as $location) {
                                                    ?>
                                                    <tr>
                                                        <td> <?php echo $location['phone']; ?> </td>
                                                        <td> <?php echo $location['address']; ?> </td>
                                                        <td> <?php echo $location['loc_count']; ?> </td>
                                                    </tr>
        <?php }
    } else { ?> 
                                            <td style="text-align: center;" colspan="3"> No Data Found </td>
    <?php } ?>
                                        </tbody>
                                    </table>
                                </div>                            
                            </div>    
                        </div>
                        <hr class="style14 col-md-12">
                        <div class="col-md-12 ">
                            <div class="box ">
                                <div class="box-header">
                                    <h3 class="box-title">Current Location Summary of  <?php echo!empty(trim($person_name)) ? $person_name : 'N/A' ?> </h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th>Phone Number</th>
                                                <th>Location</th>
                                                <th>LAC ID</th>
                                                <th>CELL ID</th>
                                                <th>Location Count</th>
                                            </tr>
                                            <?php
                                            $current_location_data = $person_data['person_current_location'];
                                            if (!empty($current_location_data)) {
                                                foreach ($current_location_data as $location) {
                                                    ?>
                                                    <tr>
                                                        <td> <?php echo $location['phone_number']; ?> </td>
                                                        <td> <?php echo $location['address']; ?> </td>
                                                        <td> <?php echo $location['lac_id']; ?> </td>
                                                        <td> <?php echo $location['cell_id']; ?> </td>
                                                        <td> <?php echo $location['location_count']; ?> </td>
                                                    </tr>
        <?php }
    } else { ?> 
                                            <td style="text-align: center;" colspan="5"> No Data Found </td>
    <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>                                             
    <?php /*
      echo '<Pre>';
      print_r($person_data['basicinfo']);
      exit; */
    ?>
                    </div>
                </div>
            </div>   
<?php } ?>
    </div>    
</section>
<script type="text/javascript">

    //$(document).ready(function () {

        $("#one_page_form").validate({
            rules: {
                phone_number: {
                    required: true,
                },
                enddate: {
                    check_list1: true,
                    greaterThan: "#startdate",
                },
            },
            messages: {
                enddate: {
                    greaterThan: "Must be greater than start date",
                },
            }
        });
        $.validator.addMethod("check_list1", function (sel, element) {
            if ($('#enddate').val() != "" && $('#startdate').val() == "") {
                return false;
            } else {
                return true;
            }
        }, "<span>Please Select Start Date</span>");

        jQuery.validator.addMethod("greaterThan", function (value, element, params) {
            if ($('#enddate').val() != "") {
                if (!/Invalid|NaN/.test(new Date(value))) {
                    return new Date(value) >= new Date($(params).val());
                }
                return isNaN(value) && isNaN($(params).val())
                        || (Number(value) >= Number($(params).val()));
            } else {
                return true;
            }
        }, 'Must be greater than ( Date From )');                    
        
    //});
    function dateonemonth() {
        var today = currentdate();
        var onemonthago = backdate(30);
        document.getElementById('enddate').value = today;
        document.getElementById('startdate').value = onemonthago;
    }
    function datetwomonths() {
        var today = currentdate();
        var twomonthsago = backdate(60);
        document.getElementById('enddate').value = today;
        document.getElementById('startdate').value = twomonthsago;
    }
    function datethreemonths() {
        var today = currentdate();
        var threemonthsago = backdate(90);
        document.getElementById('enddate').value = today;
        document.getElementById('startdate').value = threemonthsago;
    }
    function datesixmonths() {
        var today = currentdate();
        var sixmonthago = backdate(180);
        document.getElementById('enddate').value = today;
        document.getElementById('startdate').value = sixmonthago;
    }
    function currentdate() {
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }
        if (mm < 10) {
            mm = '0' + mm
        }
        today = mm + '/' + dd + '/' + yyyy;
        return today;
    }
    function backdate(value) {
        var beforedate = new Date();
        var priordate = new Date();
        priordate.setDate(beforedate.getDate() - value);
        var dd2 = priordate.getDate();
        var mm2 = priordate.getMonth() + 1;//January is 0, so always add + 1
        var yyyy2 = priordate.getFullYear();
        if (dd2 < 10) {
            dd2 = '0' + dd2
        }
        ;
        if (mm2 < 10) {
            mm2 = '0' + mm2
        }
        ;
        var datefrommonthago = mm2 + '/' + dd2 + '/' + yyyy2;

        return datefrommonthago;
    }


    //Date picker
    $('#startdate').datepicker({
        autoclose: true
    });
//Date picker
    $('#enddate').datepicker({
        autoclose: true
    });
</script>