<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */try {
    $fname = isset($data->first_name) ? $data->first_name : "";
    $lname = isset($data->last_name) ? $data->last_name : "";
//get person assets dowload path
    $person_download_data_path = !empty(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) ? Helpers_Person::get_person_download_data_path(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) : '';

    $getdistrictlist = Helpers_Utilities::get_district();
    $getregionlist = Helpers_Utilities::get_region();
    $getpslist = Helpers_Utilities::get_punjab_police_station();
} catch (Exception $ex) {
    
}
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Person's Portal
        <small><?php echo (!empty(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) ? Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) : ''); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id=' . $_GET['id']); ?>">Person Dashboard </a></li>                        
        <li class="active">Person Profile</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="box-header">
                <h3 class="box-title">Person profile</h3>
            </div>
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="<?php echo (!empty($_GET['tab']) ? ($_GET['tab'] == 'basicinfo') ? 'active' : '' : 'active'); ?>"><a href="#basicinfo" data-toggle="tab">Basic Info</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'detailinfo') ? 'active' : ''; ?>"><a href="#detailinfo" data-toggle="tab">Detailed Info</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'identities') ? 'active' : ''; ?>"><a href="#identities" onclick="callpersonidentity()" data-toggle="tab">Identities</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'education') ? 'active' : ''; ?>"><a href="#education" data-toggle="tab" onclick="callpersonedu()">Education</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'sourceofincome') ? 'active' : ''; ?>"><a href="#sourceofincome" onclick="callpersonsources()" data-toggle="tab">Income Sources</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'banksdetails') ? 'active' : ''; ?>"><a href="#banksdetails" data-toggle="tab" onclick="callpersonbanks()">Banks Details</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'assetsdetails') ? 'active' : ''; ?>"><a href="#assetsdetails" onclick="callpersonassets()" data-toggle="tab">Asset Details</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'mobiles') ? 'active' : ''; ?>"><a href="#mobiles" onclick="callpersonmobiles()"  data-toggle="tab">Mobiles</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'relations') ? 'active' : ''; ?>"><a href="#relations" onclick="callpersonrelations()" data-toggle="tab">Relations</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'criminalrecord') ? 'active' : ''; ?>"><a href="#criminalrecord" onclick="callpersoncrimes()" data-toggle="tab">Criminal Record</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'affiliations') ? 'active' : ''; ?>"><a href="#affiliations" onclick="callpersonaffiliations()" data-toggle="tab">Affiliations/Trainings</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'linkedprojects') ? 'active' : ''; ?>"><a href="#linkedprojects" onclick="calllinkedprojects()"  data-toggle="tab">Link with Projects</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'categorychangehistory') ? 'active' : ''; ?>"><a href="#categorychangehistory" onclick="callcategoryhistory()"  data-toggle="tab">Category Change History</a></li>
                    <li class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'reports') ? 'active' : ''; ?>"><a href="#reports" onclick="callpersonreports()" data-toggle="tab">Person Reports</a></li>                                        
                </ul>
                <div class="tab-content">                   
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) ? ($_GET['tab'] == 'basicinfo') ? 'active' : '' : 'active'); ?> tab-pane" id="basicinfo">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Basic Info</h3>
                            </div>                            
                            <div class="box-body">
                                <form class="" name="basicinfoform" id="basicinfoform" action="#"  method="post" enctype="multipart/form-data" >  
                                    <div class="form-group" id="" style="display: "> 
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationclosebasic" id="notification_msgbasic" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divbasic"></div></h4>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="pid">Person ID</label>
                                            <input type="number" class="form-control" value="<?php echo $person_id; ?>" id="pid" name="pid" placeholder="Enter Person ID" disabled="">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="cnic">CNIC</label>
                                            <input type="text" class="form-control" id="cnic" value="<?php
                                            $cnic = isset($data->cnic_number) ? $data->cnic_number : 0;
                                            echo $cnic;
                                            ?>" name="cnic" placeholder="Enter CNIC" disabled="">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="firstname">First Name</label>
                                            <input type="text" class="form-control" value="<?php echo $fname; ?>" id="firstname" name="first_name" placeholder="Enter Name" disabled="">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="lname">Last Name</label>
                                            <input type="text" class="form-control" value="<?php echo $lname; ?>" id="lastname" name="last_name" placeholder="Enter Last Name" disabled="">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="fathername">Father/Husband Name</label>
                                            <input type="text" class="form-control" value="<?php
                                            $fathername = isset($data->father_name) ? $data->father_name : "";
                                            echo $fathername;
                                            ?>" id="fathername" name="fathername" placeholder="Enter Father Name" disabled="">
                                        </div>
                                        <fieldset class="form-group col-md-12">
                                            <legend>Permanent Address:</legend>

                                            <div class="form-group col-md-12">
                                                <label for="permanentaddress">Address</label>
                                                <input type="text" class="form-control" value="<?php
                                                $address = isset($data->address) ? $data->address : "";
                                                echo $address;
                                                ?>" id="permanentaddress" name="address" placeholder="Enter Permanent Address" disabled="">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="region">Region</label>
                                                <select class="form-control select2" id="region" name="region" style="width: 100%;border-radius:0px"" disabled="">
                                                    <option value="">Select Region From List</option>                                                    
                                                    <?php
                                                    $check_province = '';
                                                    foreach ($getregionlist as $getregionlist1) {
                                                        if ($getregionlist1->region_id != 11) {
                                                            if($check_province!=$getregionlist1->province_id){                                                                
                                                                $check_province=!empty($getregionlist1->province_id) ? $getregionlist1->province_id : 0;
                                                                $province_name = Helpers_Utilities::get_province($check_province);
                                                                echo '<optgroup label="'.$province_name.'">';                                                        
                                                            }                                                                
                                                            ?>
                                                            <option value="<?php echo $getregionlist1->region_id ?>" <?php echo (!empty($data->region_id) && $data->region_id == $getregionlist1->region_id) ? "selected" : ''; ?>><?php echo $getregionlist1->name ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select> 
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="district">District</label>
                                                <select class="form-control select2" id="district" name="district" style="width: 100%;border-radius:0px" disabled="">
                                                    <option value="">Select Region First</option>
                                                </select> 
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="policestation">Police Stations</label>                                            
                                                <select class="form-control select2" id="policestation" name="policestation" disabled="" style="width: 100%;border-radius:0px">
                                                    <option value="">Select District First</option></select>  
                                            </div>

                                        </fieldset>
                                        <div class="col-sm-12">
                                            <button type="button" class="btn btn-primary pull-right" id="basicupdate" onclick="updatebasicinfo()" style="margin-top:10px; margin-left: 10px" disabled="">Update</button>
                                            <button type="button" class="btn btn-primary pull-right" id="basicedit" onclick="editbasicinfo()" style="margin-top:10px; display: block" >Edit</button>
                                        </div>
                                    </div> 
                                </form>
                            </div>
                        </div>                                            
                    </div>
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'detailinfo') ? 'active' : ''; ?>  tab-pane" id="detailinfo">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Detail Info</h3>
                            </div>
                            <div class="box-body">
                                <?php
                                try {
                                    $pdinfo = Helpers_Person::get_person_detail_info($person_id);
                                } catch (Exception $ex) {
                                    
                                }
                                ?>
                                <form class="" name="basicinfoform" id="detailedinfoform" action="#"  method="post" enctype="multipart/form-data" >  
                                    <div class="form-group" id="" style="display: ">                                        
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationclosedetail" id="notification_msgdetail" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divdetail"></div></h4>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="alias">Alias</label>
                                            <input type="text" class="form-control" value="<?php echo!empty($pdinfo->alias) ? $pdinfo->alias : ''; ?>" id="alias" name="alias" placeholder="Enter Alias" disabled="">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="caste">Caste</label>                                            
                                            <select class="form-control select2" id="caste" name="caste" disabled="" style="width: 100%;border-radius:0px">
                                                <option value="">Select</option>
                                                <?php
                                                try {
                                                    $getcastelist = Helpers_Utilities::get_caste();
                                                    foreach ($getcastelist as $getcaste) {
                                                        ?>
                                                        <option value="<?php echo $getcaste->id ?>" <?php echo (!empty($pdinfo->caste) && $pdinfo->caste == $getcaste->id) ? "selected" : ''; ?>><?php echo $getcaste->caste; ?></option>
                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select>  
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="maritalstatus">Marital Status</label>                                            
                                            <select class="form-control select2" id="maritalstatus" name="maritalstatus" disabled="" style="width: 100%;border-radius:0px">
                                                <option value="">Select</option>
                                                <?php
                                                try {
                                                    $getmaritallist = Helpers_Utilities::get_marital_status();
                                                    foreach ($getmaritallist as $getmarital) {
                                                        ?>
                                                        <option value="<?php echo $getmarital->id ?>" <?php echo (!empty($pdinfo->marital_status) && $pdinfo->marital_status == $getmarital->id) ? "selected" : ''; ?>><?php echo $getmarital->marital_status; ?></option>
                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select>  
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="is_sensitive_dept">Belong To Sensitive Department</label>                                            
                                            <select class="form-control select2" id="is_sensitive_dept" name="is_sensitive_dept" disabled="" style="width: 100%;border-radius:0px">
                                                <option value="0">No</option>
                                                <?php
                                                try {
                                                    $getsensitivedept = Helpers_Utilities::get_sensitive_dept();
                                                    foreach ($getsensitivedept as $getsensitivedept1) {
                                                        ?>
                                                        <option value="<?php echo $getsensitivedept1->id ?>" <?php echo (isset($pdinfo->is_sensitive_department) && $pdinfo->is_sensitive_department == $getsensitivedept1->id ) ? "selected" : ''; ?>><?php echo $getsensitivedept1->department_name; ?></option>
                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select>  
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="religion">Religious Status</label>                                            
                                            <select class="form-control select2" id="religion" name="religion" disabled="" style="width: 100%;border-radius:0px">
                                                <option value="">Select</option>
                                                <?php
                                                try {
                                                    $getreligionlist = Helpers_Utilities::get_religion();
                                                    foreach ($getreligionlist as $getreligion) {
                                                        ?>
                                                        <option value="<?php echo $getreligion->id ?>" <?php echo (!empty($pdinfo->religion) && $pdinfo->religion == $getreligion->id) ? "selected" : ''; ?>><?php echo $getreligion->religion; ?></option>
                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select>  
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="sect">Sect</label>                                            
                                            <select class="form-control select2" id="sect" name="sect" disabled="" style="width: 100%;border-radius:0px">
                                                <option value="">Select Religion First</option>                                               
                                            </select>  
                                        </div>                                        


                                        <div class="form-group col-md-12">
                                            <label for="physicalappearance">Physical Appearance (Body Structure) </label>
                                            <textarea title="Please add body structure like healthy,black color,bend nose etc." id="physicalappearance"   name="physicalappearance" class="textarea form-control" placeholder="Enter Brief Details"  disabled=""><?php
                                                $pap = isset($pdinfo->physical_appearance) ? $pdinfo->physical_appearance : "NA";
                                                echo $pap;
                                                ?> </textarea>                                        
                                        </div>
                                        <fieldset class="form-group col-md-12">
                                            <legend>Temporary Address:</legend>
                                            <div class="form-group col-md-12"> 
                                                <label for="temporaryaddress">Address</label>
                                                <input type="text" class="form-control" value="<?php echo!empty($pdinfo->temporary_address) ? $pdinfo->temporary_address : ''; ?>"  id="temporaryaddress" name="temporaryaddress" placeholder="Enter Temporary Address" disabled="">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="tempregion">Region</label>
                                                <select class="form-control select2" id="tempregion" name="tempregion" style="width: 100%;border-radius:0px"" disabled="">
                                                    <option value="">Select</option>
                                                    <?php
                                                    foreach ($getregionlist as $getregionlist1) {
                                                        if ($getregionlist1->region_id != 11) {
                                                            ?>
                                                            <option value="<?php echo $getregionlist1->region_id ?>" <?php echo (!empty($pdinfo->region_id) && $pdinfo->region_id == $getregionlist1->region_id) ? "selected" : ''; ?>><?php echo $getregionlist1->name ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select> 
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="tempdistrict">District</label>
                                                <select class="form-control select2" id="tempdistrict" name="tempdistrict" style="width: 100%;border-radius:0px" disabled="">
                                                    <option value="">Select Region First</option></select> 
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label for="temppolicestation">Police Stations</label>                                            
                                                <select class="form-control select2" id="temppolicestation" name="temppolicestation" disabled="" style="width: 100%;border-radius:0px">
                                                    <option value="">Select District First</option></select>  
                                            </div>


                                        </fieldset>

                                        <div class="col-sm-12">
                                            <button type="button" class="btn btn-primary pull-right" id="detailupdate" onclick="updatedetailinfo()" style="margin-top:10px; margin-left: 10px" disabled="">Update</button>
                                            <button type="button" class="btn btn-primary pull-right" id="detailedit" onclick="editdetailinfo()" style="margin-top:10px; display: block" >Edit</button>
                                        </div>
                                    </div>  
                                </form>
                            </div>
                        </div>                                            
                    </div>
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'identities') ? 'active' : ''; ?> tab-pane" id="identities">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Identities</h3>
                            </div>
                            <div class="box-body">
                                <form class="" name="basicinfoform" id="identityform" action="#"  method="post" enctype="multipart/form-data" >  
                                    <div class="form-group" id="" style="display: ">
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationcloseidentity" id="notification_msgidentity" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_dividentity"></div></h4>
                                            </div>
                                        </div>
                                        <input type="hidden" class="form-control" id="idnid" name="idnid" >
                                        <div class="col-sm-6" >
                                            <div class="form-group">                                                            
                                                <label for="idnname">Identity Type</label>
                                                <?php
                                                try {
                                                    $person_identity_list = Helpers_Person::get_person_identity_type();
//print_r($person_identity_list); exit;
                                                    ?>
                                                    <select class="form-control" id="idnname" name="idnname" onchange="clearidentityfields()" >
                                                        <option value="">Please Select Identity Type</option>
                                                        <?php foreach ($person_identity_list as $person_identity) { ?>
                                                            <option value="<?php echo $person_identity->id; ?>"  <?php
                                                            if ($person_identity->id == 4 OR $person_identity->id == 5) {
                                                                echo 'disabled';
                                                            }
                                                            ?> ><?php echo $person_identity->identity; ?></option>
                                                                    <?php
                                                                }
                                                            } catch (Exception $ex) {
                                                                
                                                            }
                                                            ?>                                                                                                                
                                                </select>                                                                                    
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="idnno">Identity No</label>
                                            <input type="text" class="form-control" id="idnno" name="idnnnumber" placeholder="Enter Identity No">
                                        </div>                                        
                                        <div class="col-sm-12">
                                            <button type="button" onclick="updateidentityno()" class="btn btn-primary pull-right" style="margin-top:10px" >Update</button>
                                            <input type="reset" onclick="clearidentityfields()" class="btn btn-primary pull-right" style="margin-top:10px;margin-right: 5px" value="Reset">
                                        </div>
                                        <hr class="style14 col-md-12"> 
                                    </div> 
                                </form>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="identitytable" name="identitytable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Identity Name</th>
                                                <th>Identity Number</th>
                                                <th class="no-sort">Action</th>                                        
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th>Identity Name</th>
                                                <th>Identity Number</th>
                                                <th class="no-sort">Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>                                            
                    </div>
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'education') ? 'active' : ''; ?> tab-pane" id="education">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Education</h3>
                            </div> 
                            <div class="box-body">
                                <form class="" name="educationform" id="educationform" action="#"  method="post" enctype="multipart/form-data" >  
                                    <div class="form-group" id="" style="display: ">
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationcloseeducation" id="notification_msgeducation" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_diveducation"></div></h4>
                                            </div>
                                        </div>
                                        <input type="hidden" class="form-control" id="degid" name="degid" placeholder="Enter Degree Name">
                                        <div class="form-group col-md-4">
                                            <label for="edutype">Education Type</label>
                                            <select class="form-control" id="edutype" name="edutype">
                                                <option value=" ">Select</option>
                                                <option value="0">Religious</option>
                                                <option value="1">Non Religious</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="edulevel">Education Level</label>
                                            <select class="form-control " id="edulevel" name="edulevel" style="width: 100%;border-radius:0px">
                                                <option value="">Select</option>
                                                <?php
                                                try {
                                                    $geteducationlevel = Helpers_Utilities::get_education_level();
                                                    foreach ($geteducationlevel as $geteducationlevel1) {
                                                        ?>
                                                        <option value="<?php echo $geteducationlevel1->id ?>" ><?php echo $geteducationlevel1->education_level ?></option>
                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select> 
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="compyear">Completion Year</label>
                                            <input type="number" min="1900" max="2099" step="1" value="" class="form-control" id="compyear" name="compyear" placeholder="e.g. 2005">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="degname">Degree Name</label>
                                            <input type="text" class="form-control" id="degname" name="degname" placeholder="Enter Degree Name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="institute">Institute Name</label>
                                            <input type="text" class="form-control" id="institute" name="institute" placeholder="Enter Institute">
                                        </div>
                                        <div class="col-sm-12">
                                            <button type="button" onclick="updateeducation()" class="btn btn-primary pull-right" style="margin-top:10px" >Update</button>
                                            <input type="reset" class="btn btn-primary pull-right" style="margin-top:10px;margin-right: 5px" value="Reset">
                                        </div>
                                        <hr class="style14 col-md-12"> 
                                    </div>  
                                </form>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="edutable" name="edutable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Level</th>
                                                <th>Degree Name</th>
                                                <th>Completion Year</th>
                                                <th>Institute</th>
                                                <th class="no-sort">Action</th>                                        
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th>Type</th>
                                                <th>Level</th>
                                                <th>Degree Name</th>
                                                <th>Completion Year</th>
                                                <th>Institute</th>
                                                <th class="no-sort">Action</th> 
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>                                            
                    </div>
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'sourceofincome') ? 'active' : ''; ?> tab-pane" id="sourceofincome">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Source of Income Info</h3>
                            </div> 
                            <div class="box-body">
                                <form class="" name="ajaxincomesources" id="ajaxincomesources" action="<?php echo url::site() . 'personprofile/update_personincomesource/?id=' . $_GET['id'] ?>"  method="post" enctype="multipart/form-data" >  
                                    <input type="hidden" class="form-control" id="sourceid" name="sourceid" >
                                    <div class="form-group" id="" style="display: ">
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationclosesources" id="notification_msgsources" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divsources"></div></h4>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="sourcename">Income Source Name</label>
                                            <input type="text" class="form-control" id="sourcename" name="sourcename" placeholder="Enter Income Source Type Name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="personfile">Upload <small>(pdf,doc,inpage and jpg files only)</small></label>
                                            <input type="file" accept=".pdf,.doc,.docx,.inp,.jpg" id="personfile" name="personfile" placeholder="Select File">  
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="sourcedetails">Details</label>
                                            <textarea id="sourcedetails" value=""  name="sourcedetails" class="textarea form-control" placeholder="Enter Details" ></textarea>                                        
                                        </div>
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-primary pull-right" style="margin-top:10px" >Update</button>
                                            <input type="reset" class="btn btn-primary pull-right" style="margin-top:10px;margin-right: 5px" value="Reset">
                                        </div>
                                        <hr class="style14 col-md-12"> 
                                    </div> 
                                </form>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="incomesourcestable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="no-sort">Source Name</th>
                                                <th class="no-sort">Details</th>
                                                <th class="no-sort">File</th>
                                                <th class="no-sort">Action</th>                                        
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Source Name</th>
                                                <th>Details</th>
                                                <th>File</th>
                                                <th class="no-sort">Action</th> 
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>                                            
                    </div>
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'banksdetails') ? 'active' : ''; ?> tab-pane" id="banksdetails">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Account Details</h3>
                            </div>
                            <div class="box-body"> 
                                <form class="" name="banksform" id="banksform" action="#"  method="post" enctype="multipart/form-data" >  
                                    <div class="form-group" id="" style="display: ">
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationclosebanks" id="notification_msgbanks" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divbanks"></div></h4>
                                            </div>
                                        </div>
                                        <input type="hidden" class="form-control" id="bankrecid" name="bankrecid" >

                                        <div class="form-group col-md-6">
                                            <label for="bankname">Bank Name</label>
                                            <select class="form-control select2" id="bankname" name="bankname" style="width: 100%;border-radius:0px">
                                                <option value="">Select</option>
                                                <?php
                                                try {
                                                    $banklist = Helpers_Utilities::get_bank_list();
                                                    foreach ($banklist as $bankname) {
                                                        ?>
                                                        <option value="<?php echo $bankname->id ?>" ><?php echo $bankname->name ?></option>
                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select> 
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="branch_name">Branch Name</label>
                                            <input type="text" class="form-control" id="branchname" name="branchname" placeholder="Enter Branch Name">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="account_no">A/C No</label>
                                            <input type="text" class="form-control" id="accountno" name="accountno" placeholder="Enter Account No">

                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="atmno">ATM Debit/Credit</label>
                                            <input type="number" class="form-control" id="atmno" name="atmno" placeholder="Enter Card#">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="is_internet_banking">Is Internet banking?</label>
                                            <select class="form-control " id="is_internet_banking" name="is_internet_banking" style="width: 100%;border-radius:0px">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select> 
                                        </div>
                                        <div class="col-sm-12">
                                            <button type="button" class="btn btn-primary pull-right" onclick="updatebanks()" style="margin-top:10px" >Update</button>
                                            <input type="reset" class="btn btn-primary pull-right" style="margin-top:10px;margin-right: 5px" value="Reset">
                                        </div>
                                        <hr class="style14 col-md-12">
                                    </div>  
                                </form>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="bankstable" name="accounttable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>A/C #</th>
                                                <th>ATM #</th>
                                                <th>BankName</th>
                                                <th>Branch</th>
                                                <th>Is_Internet?</th>
                                                <th class="no-sort">Action</th>                                        
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>A/C #</th>
                                                <th>ATM #</th>
                                                <th>BankName</th>
                                                <th>Branch</th>
                                                <th>Is_Internet?</th>
                                                <th class="no-sort">Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>                                            
                    </div>
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'assetsdetails') ? 'active' : ''; ?> tab-pane" id="assetsdetails">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Asset Details</h3>
                            </div> 
                            <div class="box-body">  
                                <form class="" name="ajaxassets" id="ajaxassets" action="<?php echo url::site() . 'personprofile/update_personassets?id=' . $_GET['id'] ?>"  method="post" enctype="multipart/form-data" >  
                                    <div class="form-group" id="" style="display: ">
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationcloseassets" id="notification_msgassets" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divassets"></div></h4>
                                            </div>
                                        </div>
                                        <input type="hidden" class="form-control" id="assetid" name="assetid" >
                                        <div class="form-group col-md-6">
                                            <label for="assetname">Asset Name</label>
                                            <input type="text" class="form-control" id="assetname" name="assetname" placeholder="Enter Asset Name">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="personfile">Upload<small>(pdf,doc,inpage and jpg files only)</small></label>
                                            <input type="file" accept=".pdf,.doc,.docx,.inp,.jpg" id="personfileasset" name="personfile" placeholder="Select File">  
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="assetdetails">Details</label>
                                            <textarea id="assetdetails" value=""  name="assetdetails" class="textarea form-control" placeholder="Enter Details" ></textarea>                                        
                                        </div>
                                        <div class="col-sm-12">
                                            <button type="submit" class="btn btn-primary pull-right" style="margin-top:10px" >Update</button>
                                            <input type="reset" class="btn btn-primary pull-right" style="margin-top:10px;margin-right: 5px" value="Reset">
                                        </div>
                                        <hr class="style14 col-md-12"> 
                                    </div>  
                                </form>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="assetstable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="no-sort">Asset Name</th>
                                                <th class="no-sort">Details</th>
                                                <th class="no-sort">File</th>
                                                <th class="no-sort">Action</th>                                        
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Asset Name</th>
                                                <th>Details</th>
                                                <th>File</th>
                                                <th>Action</th> 
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>                                            
                    </div>
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'mobiles') ? 'active' : ''; ?> tab-pane" id="mobiles">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Mobile Numbers</h3>
                            </div>                             
                            <div class="box-body"> 
                                <form class="" name="mobileform" id="mobileform" action=""  method="post" enctype="multipart/form-data" >  
                                    <div class="form-group" id="" style="display: ">
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationclosemobiles" id="notification_msgmobiles" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divmobiles"></div></h4>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="msisdn">SIM Number</label>
                                            <input type="text" onkeyup="check_msisdn_exist(this)" class="form-control" id="msisdn" name="msisdn" placeholder="e.g 3001234567">
                                            <span id="errormobile" style="color:red"  >  </span>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label for="contact_type">Contact Type</label>
                                            <select class="form-control" id="contact_type" name="contact_type">
                                                <option value="1">Personal</option>
                                                <option value="2">Home</option>
                                                <option value="3">Office</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-4">
                                            <button type="button" id="updatenumberuser" onclick="updatemobileno()" class="btn btn-primary pull-left" style="margin-top:18px" >Update Number User</button>
                                        </div>
                                        <hr class="style14 col-md-12"> 
                                    </div> 
                                </form>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="mobilestable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Mobile Number</th>
                                                <th>Type</th>
                                                <th>Owner</th>                                                
                                                <th>User</th>
                                                <th class="no-sort">Company</th>
                                                <th class="no-sort">Status</th>
                                                <th class="no-sort">Type</th>
                                                <th class="no-sort">Activation</th>                                       
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Mobile Number</th>
                                                <th>Type</th>
                                                <th>Owner</th>                                                
                                                <th>User</th>
                                                <th>Company</th>
                                                <th>Status</th>
                                                <th>Type</th>
                                                <th>Activation</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>                                            
                    </div>                    
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'relations') ? 'active' : ''; ?> tab-pane" id="relations">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Relations Info</h3>
                            </div> 
                            <div class="box-body">
                                <form class="" name="relationform" id="relationform" action=""  method="post" enctype="multipart/form-data" >  
                                    <div class="form-group" id="" style="display: ">
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationcloserelations" id="notification_msgrelations" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divrelations"></div></h4>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="firstname"></label>
                                            <b><input type="text" class="form-control" value="<?php
                                                $lname = isset($data->last_name) ? $data->last_name : "NA";
                                                $cnic = isset($data->cnic_number) ? $data->cnic_number : 0;
                                                $fname = isset($data->first_name) ? $data->first_name : "NA";
                                                echo $fname . " " . $lname . " (" . $cnic . ") is:";
                                                ?>" id="firstname" name="first_name" placeholder="Unknow" disabled=""></b>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="relation_type">Relation Type</label>
                                            <?php
                                            try {
                                                $reltype = Helpers_Person::get_person_relation_type();                                           //   print_r($reltype); exit;     
                                                ?>
                                                <select class="form-control" id="r_type" name="relationtype">
                                                    <option value="">No Relation</option>
                                                    <?php foreach ($reltype as $reptyp) { ?>
                                                        <option  value="<?php echo $reptyp->id; ?>" ><?php echo $reptyp->relation_name; ?></option>
                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="relation_custodian">Under Custodian</label>
                                            <select class="form-control" id="relation_custodian" name="relation_custodian">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="r_name">Name</label>
                                            <input type="text" class="form-control" id="r_name" name="relationname" placeholder="Enter Name">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="r_cnic">CNIC</label>
                                            <input type="text" class="form-control" id="r_cnic" name="relationcnic" placeholder="Enter CNIC Number">
                                        </div>
                                        <div class="form-group col-sm-4" >
                                            <label for="r_is_foreigner" class="control-label">Country</label>
                                            <select  class="form-control" name="r_is_foreigner" id="r_is_foreigner">
                                                <option   value="0">Pakistan</option>
                                                <option   value="1">Foreign</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12">
                                            <button type="button" onclick="updaterelation()"  class="btn btn-primary pull-right" style="margin-top:25px" >Update</button>
                                            <input type="reset" class="btn btn-primary pull-right" style="margin-top:25px;margin-right: 5px" value="Reset">
                                        </div>
                                        <hr class="style14 col-md-12"> 
                                    </div> 
                                </form>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="relationstable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Relation From</th>
                                                <th>Relation Type</th>
                                                <th>Relation With</th>
                                                <th>Under Custodian</th>
                                                <th class="no-sort">Action</th>                                        
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Relation From</th>
                                                <th>Relation Type</th>
                                                <th>Relation With</th>                                                
                                                <th>Under Custodian</th>
                                                <th class="no-sort">Action</th> 
                                            </tr> 
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>                                            
                    </div>
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'criminalrecord') ? 'active' : ''; ?> tab-pane" id="criminalrecord">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Criminal Record</h3>
                            </div>
                            <div class="box-body">
                                <form class="" name="criminalform" id="criminalform" action=""  method="post" enctype="multipart/form-data" >  
                                    <div class="form-group" id="" style="display: ">
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationclosecrime" id="notification_msgcrime" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divcrime"></div></h4>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="firno">FIR No</label>
                                            <input type="number" class="form-control" min="1" id="firno" name="firno" placeholder="Enter FIR Number">
                                        </div>  
                                        <div class="form-group col-md-6">
                                            <label for="firdate">FIR Date (mm/dd/yyyy)</label>
                                            <input type="text" class="form-control" id="firdate" name="firdate" placeholder="mm/dd/yyyy">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <?php
                                            try {
                                                $getpslist2 = Helpers_Utilities::get_punjab_police_station();
                                                $ctd_police_stations_list = Helpers_Utilities::get_police_station();
                                            } catch (Exception $ex) {
                                                
                                            }
                                            ?>                                              
                                            <label for="policestationcr">Police Station</label>                                            
                                            <select class="form-control select2" id="policestationcr" name="policestationcr"  style="width: 100%;border-radius:0px">
                                                <option value=" ">Select</option>
                                                <optgroup label="CTD Police Stations">
                                                    <?php foreach ($ctd_police_stations_list as $ctd_ps) { ?>
                                                        <option value="<?php echo $ctd_ps->id ?>"> <?php echo $ctd_ps->name; ?></option>
<?php } ?>                                                                                                              
                                                </optgroup> 
                                                <optgroup label="Police Stations">
                                                    <?php foreach ($getpslist2 as $getpslist1) { ?>
                                                        <option value="<?php echo $getpslist1->ps_id ?>" > <?php echo $getpslist1->ps_name ?></option>
<?php } ?> 
                                                </optgroup>
                                            </select>  
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="sections">Sections Applied</label>
                                            <input type="text" class="form-control" id="sections" name="sections" placeholder="Enter Sections Seperated With Commas">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="form-group">                                                            
                                                <label for="caseposition">Case Position</label>
                                                <?php
                                                try {
                                                    $case_accused_positon = Helpers_Person::get_case_accused_position();
                                                    ?>
                                                    <select class="form-control" id="caseposition" name="caseposition">
                                                        <option value="">Please Select Type</option>
                                                        <?php for ($i = 1; $i <= sizeof($case_accused_positon); $i++) { ?>
                                                            <option value="<?php echo $i; ?>"   ><?php echo $case_accused_positon[$i]; ?></option>
                                                            <?php
                                                        }
                                                    } catch (Exception $ex) {
                                                        
                                                    }
                                                    ?>                                                                                                                
                                                </select>                                                                                    
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <div class="form-group">                                                            
                                                <label for="accusedposition">Accused Position</label>
                                                <?php
                                                try {
                                                    $case_accused_positon = Helpers_Person::get_case_accused_position();
                                                    ?>
                                                    <select class="form-control" id="accusedposition" name="accusedposition">
                                                        <option value="">Please Select Type</option>
                                                        <?php for ($i = 1; $i <= sizeof($case_accused_positon); $i++) { ?>
                                                            <option value="<?php echo $i; ?>"   ><?php echo $case_accused_positon[$i]; ?></option>
                                                            <?php
                                                        }
                                                    } catch (Exception $ex) {
                                                        
                                                    }
                                                    ?>                                                                                                                
                                                </select>                                                                                    
                                            </div>
                                        </div>                                    
                                        <div class="col-sm-12">
                                            <button type="button" class="btn btn-primary pull-right" onclick="updatecriminalr()" style="margin-top:10px" >Update</button>
                                            <input type="reset" class="btn btn-primary pull-right" onclick="resetps()" style="margin-top:10px;margin-right: 5px" value="Reset">
                                        </div>
                                        <hr class="style14 col-md-12"> 
                                    </div>  
                                </form>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="crrecordtable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>FIR#</th>
                                                <th>Date</th>
                                                <th class="no-sort">Police Station</th>
                                                <th class="no-sort">Section</th>
                                                <th class="no-sort">Case Position</th>
                                                <th class="no-sort">Accused Position</th>
                                                <th class="no-sort">Action</th>                                        
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>41/16</td>
                                                <td>05/16/2005</td>
                                                <td>PS Model Town</td>
                                                <td>PPC/144, PPC/188</td>
                                                <td>Convicted</td>
                                                <td>Acquited</td>
                                                <td><a href="">Edit</a>&nbsp; , &nbsp;<a href="">Delete</a></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>FIR#</th>
                                                <th>Date</th>
                                                <th>Police Station</th>
                                                <th>Section</th>
                                                <th>Case Position</th>
                                                <th>Accused Position</th>
                                                <th class="no-sort">Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>                                            
                    </div>
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'affiliations') ? 'active' : ''; ?> tab-pane" id="affiliations">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Affiliations/Trainings</h3>
                            </div> 
                            <div class="box-body">
                                <form class="" name="affilform" id="affilform" action=""  method="post" enctype="multipart/form-data" >  
                                    <input type="hidden" class="form-control" id="affupdate" name="affupdate" value="0">
                                    <div class="form-group" id="" style="display: ">
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationcloseaffiliations" id="notification_msgaffiliations" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divaffiliations"></div></h4>
                                            </div>
                                        </div>

                                        <div  class="form-group col-md-6">
                                            <label for="afforg">Organization Name</label>
                                            <select  class="form-control select2" id="afforg" name="afforg" style="width: 100%;border-radius:0px">
                                                <option value=" ">Select Organization name</option>
                                                <?php
                                                try {
                                                    $org = Helpers_Utilities::get_banned_organizations();
                                                    foreach ($org as $org1) {
                                                        ?>
                                                        <option value="<?php echo $org1->org_id; ?>"><?php echo $org1->org_name; ?></option>
                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select>
                                        </div>                                   
                                        <div  class="form-group col-md-6">
                                            <label for="affstance">Ideological Stance</label>
                                            <select  class="form-control select2" id="affstance"  name="affstance" style="width: 100%;border-radius:0px">
                                                <option value="">Select Ideological Stance</option>
                                                <?php
                                                try {
                                                    $orgstance = Helpers_Utilities::get_organizations_stance();
                                                    foreach ($orgstance as $orgstance1) {
                                                        ?>
                                                        <option value="<?php echo $orgstance1->id; ?>"><?php echo $orgstance1->organization_stance; ?></option>

                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select>
                                        </div>                                   
                                        <div  class="form-group col-md-6">
                                            <label for="affdesig">Designation</label>
                                            <select  class="form-control select2" id="affdesig" name="affdesig" style="width: 100%;border-radius:0px">
                                                <option value=" ">Select Designation Name</option>
                                                <?php
                                                try {
                                                    $org_desg = Helpers_Utilities::get_organization_designation();
                                                    foreach ($org_desg as $org_desg1) {
                                                        ?>
                                                        <option value="<?php echo $org_desg1->id; ?>"><?php echo $org_desg1->organization_designation; ?></option>

                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select>
                                        </div>  
                                        <div class="form-group col-md-6">
                                            <label for="affdetail">Other Details</label>
                                            <input type="text" class="form-control" id="affdetail" name="affdetail" placeholder="Enter Other Details">
                                        </div>                                        
                                        <div class="form-group col-md-6">
                                            <label for="affrecruited">Self Recruited Details(Did You Ever Recruit? How Did You Do It)</label>
                                            <input type="text" class="form-control" id="affrecruited" name="affrecruited" placeholder="Enter details if ever recruited by him">
                                        </div>                                        
                                        <div class="col-sm-6" style="margin-top:27px;">
                                            <button type="button" onclick="updateaffiliations()" class="btn btn-primary pull-right" >Update</button>
                                            <input type="reset" class="btn btn-primary pull-right" onclick="resetorg()" style=" margin-right: 5px" value="Reset">
                                        </div>
                                    </div>  
                                </form>

                            </div>

                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="affilitiontable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="no-sort">Organization</th>
                                                <th class="no-sort">Org. Stance</th>
                                                <th class="no-sort">Designation</th>
                                                <th class="no-sort">Details</th>
                                                <th class="no-sort">Self Recruited</th>
                                                <th class="no-sort">Is Trained</th>
                                                <th class="no-sort">Action</th>                                        
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Organization</th>                                                
                                                <th class="no-sort">Org. Stance</th>
                                                <th>Designation</th>
                                                <th>Details</th>
                                                <th class="no-sort">Self Recruited</th>
                                                <th class="no-sort">Is Trained</th>
                                                <th class="no-sort">Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <hr class="style14 col-md-12"> 
                            </div>
                            <div class="box-body">
                                <div class="form-group" >  
                                    <form class="" name="training_form" id="training_form" action=""  method="post" enctype="multipart/form-data" >  
                                        <input type="hidden" class="form-control" id="training_update" name="training_update" value="0">
                                        <div  class="form-group col-md-6">
                                            <label for="training_org">Is Trained From ?</label>
                                            <select onchange="show_training_div()" class="form-control " id="training_org" name="training_org" style="width: 100%;border-radius:0px">
                                                <option value=" ">Select</option> 
                                            </select>
                                        </div> 
                                        <div id="training_div" style="display:none">
                                            <div  class="form-group col-md-6">
                                                <label for="training_type">Training Type</label>
                                                <select  class="form-control select2" id="training_type" name="training_type" style="width: 100%;border-radius:0px">
                                                    <option value=" ">Select Training Type</option>
                                                    <?php
                                                    try {
                                                        $training_type = Helpers_Utilities::get_organization_training_type();
                                                        foreach ($training_type as $training_type1) {
                                                            ?>
                                                            <option value="<?php echo $training_type1->id; ?>"><?php echo $training_type1->training_type; ?></option>

                                                            <?php
                                                        }
                                                    } catch (Exception $ex) {
                                                        
                                                    }
                                                    ?>
                                                </select>
                                            </div> 
                                            <div  class="form-group col-md-6">
                                                <label for="training_camp">Training Camp</label>
                                                <select  class="form-control select2" id="training_camp" name="training_camp" style="width: 100%;border-radius:0px">
                                                    <option value=" ">Select Training Camp</option>
                                                    <?php
                                                    try {
                                                        $training_camp = Helpers_Utilities::get_organization_training_camp();
                                                        foreach ($training_camp as $training_camp1) {
                                                            ?>
                                                            <option value="<?php echo $training_camp1->id; ?>"><?php echo $training_camp1->training_camp; ?></option>

                                                            <?php
                                                        }
                                                    } catch (Exception $ex) {
                                                        
                                                    }
                                                    ?>
                                                </select>
                                            </div> 

                                            <div class="form-group col-md-6"  >
                                                <label for="training_site">Training Site</label>
                                                <input type="text" class="form-control" id="training_site" name="training_site" placeholder="Enter Training Site / Address">
                                            </div>
                                            <div class="form-group col-md-6"  >
                                                <label for="training_year">Training Year</label>
                                                <input type="number" class="form-control" id="training_year" name="training_year" placeholder="Enter Training Year">
                                            </div>
                                            <div class="form-group col-md-6"  >
                                                <label for="training_duration">Training Duration (Days)</label>
                                                <input type="number" class="form-control" id="training_duration" name="training_duration" placeholder="Enter Training Duration">
                                            </div>
                                            <div class="form-group col-md-6"  >
                                                <label for="training_purpose">Training Purpose</label>
                                                <input type="text" class="form-control" id="training_purpose" name="training_purpose" placeholder="Enter Training Purpose">
                                            </div>
                                            <div class="form-group col-md-6"  >
                                                <label for="training_material">Material Taught</label>
                                                <input type="text" class="form-control" id="training_material" name="training_purpose" placeholder="Enter Training Material Taught">
                                            </div>
                                            <div class="form-group col-md-6"  >
                                                <label for="training_details">Other Details</label>
                                                <input type="text" class="form-control" id="training_details" name="training_details" placeholder="Enter Training Other Details">
                                            </div>

                                            <div class="col-sm-6" style="margin-top:27px;">
                                                <button type="button" onclick="updatetraining()" class="btn btn-primary pull-right" >Update</button>
                                                <input type="reset" class="btn btn-primary pull-right" onclick="resettraining()" style=" margin-right: 5px" value="Reset">
                                            </div>
                                        </div>
                                    </form>
                                </div>  
                                <!-- /.box-header -->

                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="trainingtable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="no-sort">Organization</th>
                                                <th class="no-sort">Camp</th>
                                                <th class="no-sort">Site</th>
                                                <th class="no-sort">Type</th>
                                                <th class="no-sort">Days</th>
                                                <th class="no-sort">Year</th>
                                                <th class="no-sort">Purpose</th>
                                                <th class="no-sort">Material Taught</th>
                                                <th class="no-sort">Details</th>
                                                <th class="no-sort">Action</th>                                        
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th class="no-sort">Organization</th>
                                                <th class="no-sort">Camp</th>
                                                <th class="no-sort">Site</th>
                                                <th class="no-sort">Type</th>
                                                <th class="no-sort">Days</th>
                                                <th class="no-sort">Year</th>
                                                <th class="no-sort">Purpose</th>
                                                <th class="no-sort">Material Taught</th>
                                                <th class="no-sort">Details</th>
                                                <th class="no-sort">Action</th> 
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <hr class="style14 col-md-12"> 
                            </div>
                        </div>                                            
                    </div>
                    <!-- /.tab-pane -->
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'linkedprojects') ? 'active' : ''; ?> tab-pane" id="linkedprojects">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Linked Projects</h3>
                            </div> 
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="linkedprojectstable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Linked From</th>
                                                <th>Request Type</th>
                                                <th>Requested Value</th>
                                                <th>Project Name</th>                                                
                                                <th>Linked On</th>                                       
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Linked From</th>
                                                <th>Request Type</th>
                                                <th>Requested Value</th>
                                                <th>Project Name</th>                                                
                                                <th>Linked On</th> 
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>                                            
                    </div>                    
                    <!-- /.tab-pane -->
                    <!-- /.tab-pane -->
                    <!--                     //now-->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'categorychangehistory') ? 'active' : ''; ?> tab-pane" id="categorychangehistory">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Category Change History</h3>
                            </div> 
                            <!-- /.box-header -->
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="categorychangetable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class="no-sort">Old Category</th>
                                                <th class="no-sort">New Category</th>
                                                <th class="no-sort">Changed By</th>
                                                <th >Change Date</th>
                                                <th class="no-sort">Reason</th>                                                                                                                                       
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Old Category</th>
                                                <th>New Category</th>
                                                <th>Changed By</th>
                                                <th>Change Date</th>
                                                <th>Reason</th> 
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>                                            
                    </div>                    
                    <!-- /.tab-pane -->
                    <div class="<?php echo (!empty($_GET['tab']) && $_GET['tab'] == 'reports') ? 'active' : ''; ?> tab-pane" id="reports">                                        
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">Person Reports</h3>
                            </div> 
                            <div class="box-body">
                                <form class="" name="ajaxreports" id="ajaxreports" action="<?php echo url::site() . 'personprofile/update_personreports/?id=' . $_GET['id'] ?>"  method="post" enctype="multipart/form-data" >  
                                    <div class="form-group" id="" style="display: ">
                                        <div class="form-group col-md-12 " >
                                            <div class="alert-dismissible notificationclosereports" id="notification_msgreports" style="display: none;">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divreports"></div></h4>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="reporttype">Report Type</label>
                                            <?php
                                            try {
                                                $reportdata = Helpers_Utilities::get_person_report_types();
                                                ?>
                                                <select class="form-control" id="reporttype" name="reporttype">
                                                    <option value="">Select</option>
                                                    <?php for ($i = 2; $i <= sizeof($reportdata) + 1; $i++) { ?>
                                                        <option value="<?php echo $i; ?>"><?php echo $reportdata[$i] ?></option>
                                                        <?php
                                                    }
                                                } catch (Exception $ex) {
                                                    
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="reportno">Report Reference No</label>
                                            <input type="text" class="form-control" id="reportno" name="reportno" placeholder="Enter Report Reference">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="reportdate">Report Date (mm/dd/yyyy)</label>
                                            <input type="text" class="form-control" id="reportdate" name="reportdate" placeholder="mm/dd/yyyy">
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="reportfile">Upload<small> (pdf,gif,png,doc,inpage and jpg files only)</small></label>
                                            <input type="file" accept=".jpg,.gif,.png,.pdf,.doc,.docx,.inp" id="reportfile" name="personfile" placeholder="Select File">  
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label for="reportbrief">Brief</label>
                                            <textarea id="reportbrief" value=""  name="reportbrief" class="textarea form-control" placeholder="Enter Brief Details" ></textarea>                                        
                                        </div>
                                        <div class="col-sm-12">

                                            <button type="submit" class="btn btn-primary pull-right" style="margin-top:10px; margin-left: 5px" >Update</button>
                                            <input type="reset" class="btn btn-primary pull-right" style="margin-top:10px" value="Reset">
                                        </div>
                                        <hr class="style14 col-md-12"> 
                                    </div> 
                                </form>
                            </div>
                            <!-- /.box-header -->
                            <div class="row">
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table id="reporttable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="no-sort">Report Type</th>
                                                    <th class="no-sort">Reference</th>
                                                    <th class="no-sort">Date</th>
                                                    <th class="no-sort">Brief</th>
                                                    <th class="no-sort">File</th>
                                                    <th class="no-sort">Action</th>                                        
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Interrogation Report</td>
                                                    <td>RO/GRW 299/16</td>
                                                    <td>05/23/16</td>
                                                    <td>5646544554.pdf</td>
                                                    <td><a href="">Edit</a>&nbsp; , &nbsp;<a href="">Delete</a></td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Report Type</th>
                                                    <th>Reference</th>
                                                    <th>Date</th>
                                                    <th>Brief</th>
                                                    <th>File</th>
                                                    <th class="no-sort">Action</th>  
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>                                            
                    </div>                    
                </div>                
            </div>
        </div>
    </div>
    <div class="modal modal-info fade" id="external_search_model">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>

                    <!--searching data form external sources-->
                    <div id="externa_search_results_div" style="display: block;">                                                            
                        <div class="col-md-12" style="background-color: #fff;color: black"> 

                            <div class="col-sm-12">
                                <div class="form-group">                                                                                
                                    <label   for="external_search_key" class="control-label">Mobile No:
                                    </label>
                                    <input name="external_search_key" type="text" class="form-control" id="external_search_key" placeholder="Search Key" readonly >
                                </div>

                                <div class="col-sm-12" id="external_search_results" style="display: block">   

                                    <div style='background-image: url("<?php echo URL::base(); ?>dist/img/loader.gif"); width:100%; height:11px; background-position:center; display:block'><label    class="control-label" style="alignment-adjust: central">Searching Number Details
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
</section>
<!-- /.content -->
<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>
<script>
    var objDT;
    var count = 0;
    var countmobilestab = 0;
    var countidentities = 0;
    var countbanks = 0;
    var countedu = 0;
    var countcrime = 0;
    var countaffil = 0;
    var countraining = 0;
    var countreports = 0;
    var countsources = 0;
    var countassets = 0;
    var countprojectstab = 0;
    var countcategorytab = 0;
    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }

    }
    function refreshGrid1() {
        // objDT.fnDraw();
        objDT1.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }

    }
    //for training details
    function show_training_div() {
        var tr = $("#training_org").val();
        if (tr == "") {
            $('#training_div').hide();
        } else {
            $('#training_div').show();
        }
    }
    //get district by region
    var region_id = $("#region").val();
    var request = $.ajax({
        url: "<?php echo URL::site("personprofile/get_district"); ?>",
        type: "POST",
        dataType: 'html',
        data: {region: region_id, district_id: '<?php echo $data->district_id ?>'},
        success: function (responseTex)
        {
            if (responseTex == 2)
            {
                swal("System Error", "Contact Support Team.", "error");
            }
            $("#district").html(responseTex);
            ////////
            //get police station        
            var district_id = jQuery("#district option:selected").val();

            var request = $.ajax({
                url: "<?php echo URL::site("personprofile/get_police_station"); ?>",
                type: "POST",
                dataType: 'html',
                data: {district_id: district_id, police_station_id: '<?php echo $data->police_station_id ?>'},
                success: function (responseTex)
                {
                    if (responseTex == 2)
                    {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    $("#policestation").html(responseTex);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                }
            });
            ////////
        },
        error: function (jqXHR, textStatus) {
            alert('Failed to recognize');
        }
    });
    $("#region").on("change", function () {
        var region_id = $("#region").val();
        var request = $.ajax({
            url: "<?php echo URL::site("personprofile/get_district"); ?>",
            type: "POST",
            dataType: 'html',
            data: {region: region_id, district_id: '<?php echo $data->district_id ?>'},
            success: function (responseTex)
            {
                if (responseTex == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#district").html(responseTex);

                //get police station        
                var district_id = jQuery("#district option:selected").val();

                var request = $.ajax({
                    url: "<?php echo URL::site("personprofile/get_police_station"); ?>",
                    type: "POST",
                    dataType: 'html',
                    data: {district_id: district_id, police_station_id: '<?php echo $data->police_station_id ?>'},
                    success: function (responseTex)
                    {
                        if (responseTex == 2)
                        {
                            swal("System Error", "Contact Support Team.", "error");
                        }
                        $("#policestation").html(responseTex);
                    },
                    error: function (jqXHR, textStatus) {
                        alert('Failed to recognize');
                    }
                });
            },
            error: function (jqXHR, textStatus) {
                alert('Failed to recognize');
            }
        });
    });

    //get district by region
    var region_id = $("#tempregion").val();
    var request = $.ajax({
        url: "<?php echo URL::site("personprofile/get_district"); ?>",
        type: "POST",
        dataType: 'html',
        data: {region: region_id, district_id: '<?php echo!empty($pdinfo->district_id) ? $pdinfo->district_id : 0 ?>'},
        success: function (responseTex)
        {
            if (responseTex == 2)
            {
                swal("System Error", "Contact Support Team.", "error");
            }
            $("#tempdistrict").html(responseTex);

            //get police station
            var district_id = $("#tempdistrict").val();
            var request = $.ajax({
                url: "<?php echo URL::site("personprofile/get_police_station"); ?>",
                type: "POST",
                dataType: 'html',
                data: {district_id: district_id, police_station_id: '<?php echo!empty($pdinfo->police_station_id) ? $pdinfo->police_station_id : 0 ?>'},
                success: function (responseTex)
                {
                    if (responseTex == 2)
                    {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    $("#temppolicestation").html(responseTex);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                }
            });
        },
        error: function (jqXHR, textStatus) {
            alert('Failed to recognize');
        }
    });
    $("#tempregion").on("change", function () {
        var region_id = $("#tempregion").val();
        var request = $.ajax({
            url: "<?php echo URL::site("personprofile/get_district"); ?>",
            type: "POST",
            dataType: 'html',
            data: {region: region_id, district_id: '<?php echo!empty($pdinfo->district_id) ? $pdinfo->district_id : 0 ?>'},
            success: function (responseTex)
            {
                if (responseTex == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#tempdistrict").html(responseTex);

                //get police station
                var district_id = $("#tempdistrict").val();
                var request = $.ajax({
                    url: "<?php echo URL::site("personprofile/get_police_station"); ?>",
                    type: "POST",
                    dataType: 'html',
                    data: {district_id: district_id, police_station_id: '<?php echo!empty($pdinfo->police_station_id) ? $pdinfo->police_station_id : 0 ?>'},
                    success: function (responseTex)
                    {
                        if (responseTex == 2)
                        {
                            swal("System Error", "Contact Support Team.", "error");
                        }
                        $("#temppolicestation").html(responseTex);
                    },
                    error: function (jqXHR, textStatus) {
                        alert('Failed to recognize');
                    }
                });
            },
            error: function (jqXHR, textStatus) {
                alert('Failed to recognize');
            }
        });
    });


    $("#district").on("change", function () {
        var district_id = $("#district").val();
        var request = $.ajax({
            url: "<?php echo URL::site("personprofile/get_police_station"); ?>",
            type: "POST",
            dataType: 'html',
            data: {district_id: district_id, police_station_id: '<?php echo!empty($data->police_station_id) ? $data->police_station_id : 0 ?>'},
            success: function (responseTex)
            {
                if (responseTex == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#policestation").html(responseTex);
            },
            error: function (jqXHR, textStatus) {
                alert('Failed to recognize');
            }
        });
    });

    $("#tempdistrict").on("change", function () {
        var district_id = $("#tempdistrict").val();
        var request = $.ajax({
            url: "<?php echo URL::site("personprofile/get_police_station"); ?>",
            type: "POST",
            dataType: 'html',
            data: {district_id: district_id, police_station_id: '<?php echo!empty($pdinfo->police_station_id) ? $pdinfo->police_station_id : 0 ?>'},
            success: function (responseTex)
            {
                if (responseTex == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#temppolicestation").html(responseTex);
            },
            error: function (jqXHR, textStatus) {
                alert('Failed to recognize');
            }
        });
    });

    //update religion
    var religion = $("#religion").val();
    var request = $.ajax({
        url: "<?php echo URL::site("personprofile/get_sect"); ?>",
        type: "POST",
        dataType: 'html',
        data: {religion: religion, sect: '<?php echo!empty($pdinfo->sect) ? $pdinfo->sect : 0; ?>'},
        success: function (responseTex)
        {
            if (responseTex == 2)
            {
                swal("System Error", "Contact Support Team.", "error");
            }
            $("#sect").html(responseTex);
        },
        error: function (jqXHR, textStatus) {
            alert('Failed to recognize');
        }
    });
    $("#religion").on("change", function () {
        var religion = $("#religion").val();
        var request = $.ajax({
            url: "<?php echo URL::site("personprofile/get_sect"); ?>",
            type: "POST",
            dataType: 'html',
            data: {religion: religion, sect: '<?php echo!empty($pdinfo->sect) ? $pdinfo->sect : 0; ?>'},
            success: function (responseTex)
            {
                if (responseTex == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#sect").html(responseTex);
            },
            error: function (jqXHR, textStatus) {
                alert('Failed to recognize');
            }
        });
    });

    //function to update basic info
    function updatebasicinfo() {
        var fname = $("#firstname").val();
        var lname = $("#lastname").val();
        var fathname = $("#fathername").val();
        var add = $("#permanentaddress").val();
        var ps = $("#policestation").val();
        var region = $("#region").val();
        var district = $("#district").val();
        var result = {fname: fname, lname: lname, fathname: fathname, add: add, id: '<?php echo $_GET['id']; ?>', ps: ps, region: region, district: district}
        if ($('#basicinfoform').valid())
        {
            $.ajax({
                url: "<?php echo URL::site("personprofile/update_basic_info"); ?>",
                type: 'POST',
                data: result,
                cache: false,
                success: function (msg) {
                    if (msg == 1) {
                        swal("Congratulations!", "Record Updated successfully.", "success");
                    }

                    var elem = $(".notificationclosebasic");


                    $('#firstname').prop('disabled', true);
                    $('#lastname').prop('disabled', true);
                    $('#fathername').prop('disabled', true);
                    $('#permanentaddress').prop('disabled', true);
                    $('#policestation').prop('disabled', true);
                    $('#region').prop('disabled', true);
                    $('#district').prop('disabled', true);
                    $('#basicupdate').prop('disabled', true);
                    $('#basicedit').prop('disabled', false);
                    if (msg == 2) {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                }
            });
        }
    }
    //function to enable disable basic info
    function editbasicinfo() {
        $('#firstname').prop('disabled', false);
        $('#lastname').prop('disabled', false);
        $('#fathername').prop('disabled', false);
        $('#permanentaddress').prop('disabled', false);
        $('#policestation').prop('disabled', false);
        $('#region').prop('disabled', false);
        $('#district').prop('disabled', false);
        $('#basicupdate').prop('disabled', false);
        $('#basicedit').prop('disabled', true);
    }
    //function to update basic info
    function updatedetailinfo() {
        var alias = $("#alias").val();
        var caste = $("#caste").val();
        var sect = $("#sect").val();
        var religion = $("#religion").val();
        var maritalstatus = $("#maritalstatus").val();
        var is_sensitive_dept = $("#is_sensitive_dept").val();
        var temporaryaddress = $("#temporaryaddress").val();
        var policestation = $("#temppolicestation").val();
        var district = $("#tempdistrict").val();
        var region = $("#tempregion").val();
        var physicalappearance = $("#physicalappearance").val();
        var result = {alias: alias, caste: caste, id: '<?php echo $_GET['id']; ?>', sect: sect, religion: religion, maritalstatus, maritalstatus, temporaryaddress: temporaryaddress, policestation: policestation, district: district, region: region, physicalappearance: physicalappearance, is_sensitive_dept: is_sensitive_dept}
        if ($('#detailedinfoform').valid())
        {
            $.ajax({
                url: "<?php echo URL::site("personprofile/update_detail_info"); ?>",
                type: 'POST',
                data: result,
                cache: false,
                success: function (msg) {
                    if (msg == 1) {
                        swal("Congratulations!", "Record Updated successfully.", "success");
                    }
                    var elem = $(".notificationclosedetail");
                    elem.slideUp(10000);
                    $('#alias').prop('disabled', true);
                    $('#caste').prop('disabled', true);
                    $('#sect').prop('disabled', true);
                    $('#religion').prop('disabled', true);
                    $('#maritalstatus').prop('disabled', true);
                    $('#is_sensitive_dept').prop('disabled', true);
                    $('#temporaryaddress').prop('disabled', true);
                    $('#temppolicestation').prop('disabled', true);
                    $('#tempdistrict').prop('disabled', true);
                    $('#tempregion').prop('disabled', true);
                    $('#physicalappearance').prop('disabled', true);
                    $('#detailupdate').prop('disabled', true);
                    $('#detailedit').prop('disabled', false);
                    if (msg == 2) {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                }
            });
        }
    }
    //function to enable disable detail info
    function editdetailinfo() {
        $('#alias').prop('disabled', false);
        $('#caste').prop('disabled', false);
        $('#sect').prop('disabled', false);
        $('#religion').prop('disabled', false);
        $('#maritalstatus').prop('disabled', false);
        $('#is_sensitive_dept').prop('disabled', false);
        $('#temporaryaddress').prop('disabled', false);
        $('#temppolicestation').prop('disabled', false);
        $('#tempdistrict').prop('disabled', false);
        $('#tempregion').prop('disabled', false);
        $('#physicalappearance').prop('disabled', false);
        $('#detailupdate').prop('disabled', false);
        $('#detailedit').prop('disabled', true);
    }

    function callpersonmobiles() {
        if (countmobilestab >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }

                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#mobilestable').dataTable(
                    {//"aaSorting": [[2, "desc"]],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('personprofile/ajaxmobilenumbers/?id=' . $_GET['id'], TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading..."
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );
        }
        $('.dataTables_empty').html("Information not found");
        countmobilestab = countmobilestab + 1;
    }
    function calllinkedprojects() {
        if (countprojectstab >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }

                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#linkedprojectstable').dataTable(
                    {//"aaSorting": [[2, "desc"]],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": '<?php echo URL::site("personprofile/ajaxlinkedprojects/?id=" . $_GET['id']); ?>',
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading...",
                            "sSearch": "Search By Request Type:"
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );
        }
        $('.dataTables_empty').html("Information not found");
        countprojectstab = countprojectstab + 1;
    }
    //function to get data of category change history 
    function callcategoryhistory() {
        if (countcategorytab >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }

                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#categorychangetable').dataTable(
                    {"aaSorting": [[3, "desc"]],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": '<?php echo URL::site("personprofile/ajaxcategoryhistory/?id=" . $_GET['id']); ?>',
                        "sPaginationType": "full_numbers",
                        "bFilter": false,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading..."
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );
        }
        $('.dataTables_empty').html("Information not found");
        countcategorytab = countcategorytab + 1;
    }
    //function to edit mobile number
    function editmsisdn(sim, contacttype) {
        $("#msisdn").val(sim);
        $("#contact_type").val(contacttype);
        $("#msisdn").attr("readonly", true);
    }
    //function to update mobile no
    function updatemobileno() {
        var number = $("#msisdn").val();
        var contact_type = $("#contact_type").val();
        var result = {number: number, contact_type: contact_type}
        if ($('#mobileform').valid())
        {
            $.confirm({
                'title': 'Update Confirmation',
                'message': 'Do you really want to update ' + number + ' ? (Not removeable)',
                'buttons': {
                    'Yes': {
                        'class': 'gray',
                        'action': function () {
                            $.ajax({url: '<?php echo URL::site("personprofile/update_mobiles/?id=" . $_GET['id']); ?>',
                                type: 'POST',
                                data: result,
                                cache: false,
                                success: function (msg) {
                                    $("#msisdn").attr("readonly", false);
                                    $("#msisdn").val('');
                                    var elem = $(".notificationclosemobiles");
                                    if (msg == 1) {
                                        swal("Congratulations!", "Record Updated successfully.", "success");
                                    } else if (msg == 2) {
                                        swal("System Error", "Contact Support Team.", "error");
                                    }
                                    refreshGrid();
                                }});
                        }
                    },
                    'No': {
                        'class': 'blue',
                        'action': function () {
                        }  // Nothing to do in this case. You can as well omit the action property.
                    }
                }
            });
            /*
             $.ajax({
             url: "<?php // echo URL::site("personprofile/update_mobiles");      ?>",
             type: 'POST',
             data: result,
             cache: false,
             success: function (msg) {
             $("#notification_msg_divmobiles").html('Successfully Updated');
             $("#notification_msgmobiles").show();
             $("#notification_msgmobiles").addClass('alert');
             $("#notification_msgmobiles").addClass('alert-success');
             var elem = $(".notificationclosemobiles");
             elem.slideUp(10000);
             refreshGrid();
             
             }
             }); */
        }
    }
    function callpersonrelations() {
        if (count >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }

                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#relationstable').dataTable(
                    {//"aaSorting": [[2, "desc"]],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('personprofile/ajaxpersonrelations/?id=' . $_GET['id'], TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading..."
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );
        }
        $('.dataTables_empty').html("Information not found");
        count = count + 1;
    }
    //function to edit relation
    function editrelation(cnic, relid, isf, custodian) {
        $("#r_cnic").val(cnic);
        $("#r_type").val(relid);
        $("#relation_custodian").val(custodian);
        $("#r_is_foreigner").val(isf);

    }
    //function to update relations
    function updaterelation() {
        var relation = $("#r_type").val();
        var relation_custodian = $("#relation_custodian").val();
        var cnic = $("#r_cnic").val();
        var name = $("#r_name").val();
        // alert(cnic);
        var result = {relation: relation, cnic: cnic, name: name, relation_custodian: relation_custodian}

        if ($('#relationform').valid())
        {
            $.ajax({
                url: "<?php echo URL::site("personprofile/update_relations/?id=" . $_GET['id']); ?>",
                type: 'POST',
                data: result,
                cache: false,
                success: function (msg) {
                    var elem = $(".notificationcloserelations");
                    if (msg == 1) {
                        swal("Congratulations!", "Record Updated successfully.", "success");
                    } else if (msg == 2) {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    refreshGrid();
                    $("#r_type").val('');
                    $("#relation_custodian").val('0');
                    $("#r_cnic").val('');
                    $("#r_name").val('');
                    $("#r_is_foreigner").val('0');

                }
            });
        }
    }
    //function to update identities
    function callpersonidentity() {
        if (countidentities >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }

                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#identitytable').dataTable(
                    {//"aaSorting": [[2, "desc"]],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('personprofile/ajaxpersonidentity/?id=' . $_GET['id'], TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading...",
                            "sSearch": "Search By Identity Number:"
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );
        }
        $('.dataTables_empty').html("Information not found");
        countidentities = countidentities + 1;
    }
    //function to edit identity
    function clearidentityfields() {
        $("#idnno").val('');
        $("#idnid").val('');


    }
    //function to edit identity
    function editidentity(id, no, recid) {
        $("#idnname").val(id);
        $("#idnno").val(no);
        $("#idnid").val(recid);


    }
    //function to delete identity
    function deleteidentity(id, idname)
    {
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
        //var elem = $(this).parent().parent();// .closest('.action');
        // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        var idnname = idname;
        $.confirm({
            'title': 'Delete Confirmation',
            'message': 'Do you really want to delete ' + idnname + '?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: "<?php echo URL::site('personprofile/delete_identity/?id=' . $_GET['id'] . '&recordid=', TRUE); ?>" + id,
                            success: function (msg) {
                                var elem = $(".notificationcloseidentity");
                                if (msg == 1) {
                                    swal("Congratulations!", "Record Updated successfully.", "success");
                                } else if (msg == 2) {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                refreshGrid();
                            }});
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {
                    }  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
//function to update mobile no
    function updateidentityno() {
        var idnname = $("#idnname").val();
        var idnno = $("#idnno").val();
        var idnid = $("#idnid").val();
        var result = {idnname: idnname, idnno: idnno, idnid: idnid, id: '<?php echo $_GET['id']; ?>'}
        if ($('#identityform').valid())
        {
            $.ajax({
                url: "<?php echo URL::site("personprofile/update_identity"); ?>",
                type: 'POST',
                data: result,
                cache: false,
                success: function (msg) {
                    $("#identityform").trigger("reset");
                    if (msg == 1) {
                        swal("Congratulations!", "Record Updated successfully.", "success");
                    }
                    refreshGrid();
                    if (msg == 2) {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                }
            });
        }
    }
    //function to update education
    function callpersonedu() {
        if (countedu >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }

                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#edutable').dataTable(
                    {//"aaSorting": [[2, "desc"]],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('personprofile/ajaxpersonedu/?id=' . $_GET['id'], TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading...",
                            "sSearch": "Search By Degree Name:"
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );
        }
        $('.dataTables_empty').html("Information not found");
        countedu = countedu + 1;
    }
    //function to edit education
    function editedu(type, degree, year, inst, degid, level) {
        $("#edulevel").val(level);
        $("#edutype").val(type);
        $("#degname").val(degree);
        $("#compyear").val(year);
        $("#institute").val(inst);
        $("#degid").val(degid);
    }
    //function to update Education
    function updateeducation() {
        var edutype = $("#edutype").val();
        var edulevel = $("#edulevel").val();
        var degname = $("#degname").val();
        var compyear = $("#compyear").val();
        var institute = $("#institute").val();
        var degid = $("#degid").val();
        var result = {edulevel: edulevel, edutype: edutype, degname: degname, compyear: compyear, institute: institute, degid: degid}
        if ($('#educationform').valid())
        {
            $.ajax({
                url: "<?php echo URL::site("personprofile/update_education/?id=" . $_GET['id']); ?>",
                type: 'POST',
                data: result,
                cache: false,
                success: function (msg) {
                    var elem = $(".notificationcloseeducation");
                    $("#educationform").trigger('reset');
                    if (msg == 1) {
                        swal("Congratulations!", "Record Updated successfully.", "success");
                    }

                    if (msg == 2) {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    refreshGrid();
                }
            });
        }
    }
    //function to delete education
    function deleteedu(degname, id)
    {
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
        //var elem = $(this).parent().parent();// .closest('.action');
        // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        var degname = degname;
        $.confirm({
            'title': 'Delete Confirmation',
            'message': 'Do you really want to delete ' + degname + '?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: "<?php echo URL::site('Personprofile/delete_education/?id=' . $_GET['id'] . '&education_id=', TRUE); ?>" + id,
                            success: function (msg) {
                                var elem = $(".notificationcloseeducation");
                                if (msg == 1) {
                                    swal("Congratulations!", "Record Updated successfully.", "success");
                                } else if (msg == 2) {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                refreshGrid();
                            }});
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {
                    }  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    //account details
    function callpersonbanks() {
        if (countbanks >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }

                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#bankstable').dataTable(
                    {//"aaSorting": [[2, "desc"]],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('personprofile/ajaxpersonbanks/?id=' . $_GET['id'], TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading...",
                            "sSearch": "Search By A/C #, ATM #, Bank Name or Branch Name:"
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );
        }
        $('.dataTables_empty').html("Information not found");
        countbanks = countbanks + 1;
    }
    //function to update Education
    function updatebanks() {
        var accountno = $("#accountno").val();
        var atmno = $("#atmno").val();
        var bankname = $("#bankname").val();
        var branchname = $("#branchname").val();
        var bankrecid = $("#bankrecid").val();
        var is_internet_banking = $("#is_internet_banking").val();
        var result = {is_internet_banking: is_internet_banking, accountno: accountno, atmno: atmno, bankname: bankname, branchname: branchname, bankrecid: bankrecid}

        if ($('#banksform').valid())
        {
            $.ajax({
                url: "<?php echo URL::site("personprofile/update_banks/?pid=" . $_GET['id']); ?>",
                type: 'POST',
                data: result,
                cache: false,
                success: function (msg) {
                    var a = '';
                    $("#accountno").val(a);
                    $("#atmno").val(a);
                    $("#bankname").val(a).trigger('change');
                    $("#branchname").val(a);
                    $("#bankrecid").val(a);
                    $("#is_internet_banking").val('0');
                    var elem = $(".notificationclosebanks");
                    if (msg == 1) {
                        swal("Congratulations!", "Record Updated successfully.", "success");
                    } else if (msg == 2) {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    refreshGrid();
                }
            });
        }
    }
    //function to edit banks
    function editbanks(ac, atm, bank, branch, bankrecid, is_internet) {
        $("#accountno").val(ac);
        $("#atmno").val(atm);
        $("#bankname").val(bank).trigger('change');
        $("#branchname").val(branch);
        $("#bankrecid").val(bankrecid);
        $("#is_internet_banking").val(is_internet);

    }
    //function to delete banks
    function deletebank(bankrecid, account, atm)
    {
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
        //var elem = $(this).parent().parent();// .closest('.action');
        // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + bankrecid).closest('.actiontd');
        var ac = account;
        var atm = atm;
        var bankrecid = bankrecid;
        $.confirm({
            'title': 'Delete Confirmation',
            'message': 'Do you really want to delete ' + ac + ',' + atm + ' ?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {     //sajid   
                        $.ajax({url: "<?php echo URL::site('Personprofile/delete_bank/?id=' . $_GET['id'] . '&bankrecid=', TRUE); ?>" + bankrecid,
                            success: function (msg) {
                                var elem = $(".notificationclosebanks");
                                if (msg == 1) {
                                    swal("Congratulations!", "Record deleted successfully.", "warning");
                                } else if (msg == 2) {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                refreshGrid();
                            }});
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {
                    }  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    //account details
    function callpersoncrimes() {
        if (countcrime >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }
                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#crrecordtable').dataTable(
                    {//"aaSorting": [[0, "desc"]],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('personprofile/ajaxpersoncrimes/?id=' . $_GET['id'], TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading...",
                            "sSearch": "Search By Sections Applied:"
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );
        }
        $('.dataTables_empty').html("Information not found");
        countcrime = countcrime + 1;
    }
    //function to edit education
    function editcrrecord(fr, frdate, ps, sec, cp, ap) {
        $("#firno").val(fr);
        $("#firdate").val(frdate);
        $("#policestationcr").val(ps).trigger('change');
        $("#sections").val(sec);
        $("#caseposition").val(cp);
        $("#accusedposition").val(ap);
    }
    //function to reset ps
    function resetps() {
        $("#policestationcr").val(" ").trigger('change');
    }
    //function to update Criminal Record
    function updatecriminalr() {
        var firno = $("#firno").val();
        var firdate = $("#firdate").val();
        var policestationcr = $("#policestationcr").val();
        var sections = $("#sections").val();
        var caseposition = $("#caseposition").val();
        var accusedposition = $("#accusedposition").val();
        var result = {firno: firno, firdate: firdate, policestationcr: policestationcr, sections: sections, caseposition: caseposition, accusedposition: accusedposition}
        if ($('#criminalform').valid())
        {
            $.ajax({
                url: "<?php echo URL::site("personprofile/update_criminalr/?id=" . $_GET['id']); ?>",
                type: 'POST',
                data: result,
                cache: false,
                success: function (msg) {
                    var elem = $(".notificationclosecrime");
                    if (msg == 1) {
                        swal("Congratulations!", "Record Updated successfully.", "success");
                        document.getElementById("criminalform").reset();
                    } else if (msg == 2) {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    refreshGrid();

                }
            });
        }
    }
    //function to delete criminal record
    function deletecriminalrec(fir, date, ps, pid)
    {
        var result = {firno: fir, firdate: date, policestationcr: ps, pid: pid}
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
        //var elem = $(this).parent().parent();// .closest('.action');
        // var elem = $(this).closest('tr[class^="action"]');       
        // var elem = $(".item-" + fir).closest('.actiontd');
        $.confirm({
            'title': 'Delete Confirmation',
            'message': 'Do you really want to delete this record?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::base() . "Personprofile/deletecriminalrecord/?id=" . $_GET['id']; ?>',
                            type: 'POST',
                            data: result,
                            cache: false,
                            success: function (msg) {
                                var elem = $(".notificationclosecrime");
                                if (msg == 1) {
                                    swal("Congratulations!", "Record deleted successfully.", "warning");
                                } else if (msg == 2) {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                refreshGrid();
                            }});
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {
                    }  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    //account details
    function callpersonaffiliations() {
        if (countaffil >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }
                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#affilitiontable').dataTable(
                    {"aaSorting": [],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('personprofile/ajaxpersonaffiliations/?id=' . $_GET['id'], TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading...",
                            "sSearch": "Search By Details:"
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );

        }
        $('.dataTables_empty').html("Information not found");
        countaffil = countaffil + 1;

        if (countraining >= 1)
        {
            refreshGrid1();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }
                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT1 = $('#trainingtable').dataTable(
                    {"aaSorting": [],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('personprofile/ajaxpersontrainings/?id=' . $_GET['id'], TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading...",
                            "sSearch": "Search By Details:"
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );

            get_person_affiliated_org_list();
        }
        $('.dataTables_empty').html("Information not found");
        countraining = countraining + 1;
        //to get training organizations

    }
    //function to get person affilated organizations
    function get_person_affiliated_org_list() {
        var request = $.ajax({
            url: "<?php echo URL::site("personprofile/ajaxgettrainingorg"); ?>",
            type: "POST",
            dataType: 'html',
            data: {id: '<?php echo $_GET['id']; ?>'},
            success: function (responseTex)
            {
                if (responseTex == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#training_org").html(responseTex);
            },
            error: function (jqXHR, textStatus) {
                alert('Failed to recognize');
            }
        });

    }
    //function to edit affiliations
    function editaffiliations(recordid, projectid, org, desig, det, stance, recruited) {
        $("#affrecruited").val(recruited);
        $("#affstance").val(stance).trigger('change');
        $("#affproject").val(projectid).trigger('change');
        $("#afforg").val(org).trigger('change');
        $("#affdesig").val(desig).trigger('change');
        $("#affupdate").val(recordid);
        $("#affdetail").val(det);
        $('#afforg').attr("disabled", "disabled");

    }
    //function to edit training
    function edittraining(id, org_id, pid, tcamp, site, purpose, material, detail, type, year, duration) {
        $("#training_org").val(org_id).trigger('change');
        $("#training_update").val(id);
        $("#training_type").val(type).trigger('change');
        $("#training_camp").val(tcamp).trigger('change');
        $("#training_site").val(site);
        $("#training_year").val(year);
        $("#training_duration").val(duration);
        $("#training_purpose").val(purpose);
        $("#training_details").val(detail);
        $("#training_material").val(material);

    }

    //function to change org name
    function selectorgname(pro) {

        if (pro.value == 1) {
            $("#org_div").show();
        } else {
            $("#afforg").val("").trigger('change');
            $("#org_div").hide();
        }
    }
    //function to change org name
    function selectproname(org) {
        var org_id = org.value;
        if (org_id == 1) {

        } else if (org_id != 1 && org_id != '') {

            var result = {org_id: org_id}

            $.ajax({
                url: "<?php echo URL::site("personprofile/get_project_id"); ?>",
                type: 'POST',
                data: result,
                dataType: 'json',
                cache: false,
                success: function (msg) {
                    if (msg == 2)
                    {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    if (msg == 1) {
                        $("#affproject").val(msg).trigger('change');
                    } else {
                        $("#affproject").val(msg).trigger('change');
                        $("#org_div").hide();
                    }
                }
            });

        }
    }

    //function to reset org
    function resetorg() {
        $("#afforg").val(" ").trigger('change');
        $("#affstance").val("").trigger('change');
        $("#affdesig").val(" ").trigger('change');
        $("#affupdate").val('0');
        $("#affrecruited").val('');
        showDiv1();
    }
    //function to reset trainings
    function resettraining() {
        var ab = '';
        $("#affproject").val(ab).trigger('change');
        $("#training_update").val('0');
        $("#training_org").val(ab).trigger('change');
        $("#training_type").val(ab).trigger('change');
        $("#training_camp").val(ab).trigger('change');
        $("#training_site").val(ab);
        $("#training_year").val(ab);
        $("#training_duration").val(ab);
        $("#training_purpose").val(ab);
        $("#training_details").val(ab);
        $("#training_material").val(ab);

    }
    //function to update Affiliation
    function updateaffiliations() {
        var project = $("#affproject").val();
        var recruited = $("#affrecruited").val();
        var stance = $("#affstance").val();
        var org = $("#afforg").val();
        var recordid = $("#affupdate").val();
        var desig = $("#affdesig").val();
        var detail = $("#affdetail").val();

        var result = {stance: stance, recruited: recruited, recordid: recordid, project: project, org: org, desig: desig, detail: detail, id: '<?php echo $_GET['id']; ?>'};
        if ($('#affilform').valid())
        {
            $.ajax({
                url: "<?php echo URL::site("personprofile/update_affiliations/?id=" . $_GET['id']); ?>",
                type: 'POST',
                data: result,
                cache: false,
                success: function (msg) {
                    var elem = $(".notificationcloseaffiliations");
                    if (msg == 1) {
                        swal("Congratulations!", "Record Updated successfully.", "success");
                    } else if (msg == 2) {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    refreshGrid();
                    var ab = '';
                    $('#afforg').removeAttr("disabled");
                    $("#affproject").val(ab).trigger('change');
                    $("#affstance").val(ab).trigger('change');
                    $("#afforg").val(ab).trigger('change');
                    $("#affdesig").val(ab).trigger('change');
                    $("#affupdate").val('0');
                    $("#affdetail").val(ab);
                    $("#affrecruited").val(ab);
                    get_person_affiliated_org_list();

                }
            });
        }
    }
    //function to update training
    function updatetraining() {
        var training_update = $("#training_update").val();
        var training_org = $("#training_org").val();
        var training_type = $("#training_type").val();
        var training_camp = $("#training_camp").val();
        var training_site = $("#training_site").val();
        var training_year = $("#training_year").val();
        var training_duration = $("#training_duration").val();
        var training_purpose = $("#training_purpose").val();
        var material_taught = $("#training_material").val();
        var training_details = $("#training_details").val();

        var result = {material_taught: material_taught, training_details: training_details, training_purpose: training_purpose, training_duration: training_duration, training_year: training_year, training_update: training_update, training_org: training_org, training_type: training_type, training_camp: training_camp, training_site: training_site, id: '<?php echo $_GET['id']; ?>'};
        if ($('#training_form').valid())
        {
            $.ajax({
                url: "<?php echo URL::site("personprofile/update_trainings/?id=" . $_GET['id']); ?>",
                type: 'POST',
                data: result,
                cache: false,
                success: function (msg) {
                    var elem = $(".notificationcloseaffiliations");
                    if (msg == 1) {
                        swal("Congratulations!", "Record Updated successfully.", "success");
                    } else if (msg == 2) {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    refreshGrid1();
                    var ab = '';
                    $("#affproject").val(ab).trigger('change');
                    $("#training_update").val('0');
                    $("#training_org").val(ab).trigger('change');
                    $("#training_type").val(ab).trigger('change');
                    $("#training_camp").val(ab).trigger('change');
                    $("#training_site").val(ab);
                    $("#training_year").val(ab);
                    $("#training_duration").val(ab);
                    $("#training_purpose").val(ab);
                    $("#training_details").val(ab);
                    $("#training_material").val(ab);
                    get_person_affiliated_org_list();

                }
            });
        }
    }
    //function to delete criminal record
    function deleteaffiliations(id, proj_name)
    {
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
        //var elem = $(this).parent().parent();// .closest('.action');
        // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title': 'Delete Confirmation',
            'message': 'Do you really want to delete ' + proj_name + ' ?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::base() . "Personprofile/deleteaffiliations/?id=" . $_GET['id']; ?>' + '&affiliationid=' + id,
                            success: function (msg) {
                                var elem = $(".notificationcloseaffiliations");
                                if (msg == 1) {
                                    swal("Congratulations!", "Record deleted successfully.", "warning");
                                } else if (msg == 2) {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                refreshGrid();
                            }});
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {
                    }  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }

    $(document).ready(function (e) {        

//    $("#ImageBrowse").on("change", function() {
//        $("#imageUploadForm").submit();
//    });
    });
    //hide button of mobiles tab
    $("#updatenumberuser").hide();

    $(document).ready(function (e) {        

//    $("#ImageBrowse").on("change", function() {
//        $("#imageUploadForm").submit();
//    });
    });
//account details
    function callpersonreports() {
        if (countreports >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }
                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#reporttable').dataTable(
                    {"aaSorting": [],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('personprofile/ajaxpersonreports/?id=' . $_GET['id'], TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading...",
                            "sSearch": "Search By Report Brief:"
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );
        }
        $('.dataTables_empty').html("Information not found");
        countreports = countreports + 1;
    }
    //function to edit affiliations
    function editreports(rep, ref, date, bri) {

        $("#reporttype").val(rep);
        $("#reportno").val(ref);
        $("#reporttype").prop("disabled", true);
        document.getElementById("reportno").readOnly = true;
        $("#reportdate").val(date);
        $("#reportbrief").val(bri);
    }

    $(document).ready(function (e) {
        $('#ajaxreports').on('submit', (function (e) {
            $("#reporttype").prop("disabled", false);
            e.preventDefault();
            var formData = new FormData(this);
            if ($('#ajaxreports').valid())
            {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (msg) {
                        var ab = '';
                        $("#reportfile").val(ab);
                        $("#reportno").val(ab);
                        $("#reportdate").val(ab);
                        $("#reportfile").val(ab);
                        $("#reportbrief").val(ab);
                        $("#reporttype").val(ab);
                        document.getElementById("reportno").readOnly = false;
                        var elem = $(".notificationclosereports");
                        // $("#reporttype").prop( "disabled", true );
                        if (msg == 1) {
                            swal("Congratulations!", "Record Updated successfully.", "success");
                        } else //if (msg == 2)
                        {
                            swal("System Error", "Contact Support Team.", "error");
                        }
                        refreshGrid();
                    },
                    error: function (data) {
                        console.log("error");
                        console.log(data);
                    }
                });
            }
        }));

//    $("#ImageBrowse").on("change", function() {
//        $("#imageUploadForm").submit();
//    });
    });
//Date picker
    $('#reportdate').datepicker({
        autoclose: true
    });
//Date picker
    $('#firdate').datepicker({
        autoclose: true
    });

//function to delete report
    function deletereport(id, ref)
    {
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
        //var elem = $(this).parent().parent();// .closest('.action');
        // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title': 'Delete Confirmation',
            'message': 'Do you really want to delete this record?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::base() . "Personprofile/deletereport/?id=" . $_GET['id']; ?>' + '&reportid=' + id + '&refrenceid=' + ref,
                            success: function (msg) {
                                ;
                                var elem = $(".notificationclosereports");
                                if (msg == 1) {
                                    swal("Congratulations!", "Record deleted successfully.", "warning");
                                } else if (msg == 2) {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                refreshGrid();
                            }});
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {
                    }  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    //Person recommendations/remarks
    function callpersonsources() {
        if (countsources >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }
                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#incomesourcestable').dataTable(
                    {"aaSorting": [],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('personprofile/ajaxpersonsources/?id=' . $_GET['id'], TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading...",
                            "sSearch": "Search By Source Name or Details:"
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );
        }
        $('.dataTables_empty').html("Information not found");
        countsources = countsources + 1;
    }
    //function to edit income sources
    function editsource(nam, det, sourceid) {
        $("#sourcename").val(nam);
        $("#sourcedetails").val(det);
        $("#sourceid").val(sourceid);
    }
    //function to delete report
    function deletesource(sname, id)
    {
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
        //var elem = $(this).parent().parent();// .closest('.action');
        // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        var sname = sname;
        $.confirm({
            'title': 'Delete Confirmation',
            'message': 'Do you really want to delete ' + sname + ' ?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::site('Personprofile/deletesource/?id=' . $_GET['id'] . '&source_income=', TRUE); ?>' + id,
                            success: function (msg) {
                                var elem = $(".notificationclosesources");
                                if (msg == 1) {
                                    swal("Congratulations!", "Record deleted successfully.", "warning");
                                } else if (msg == 2) {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                refreshGrid();
                            }
                        });
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {
                    }  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    $(document).ready(function (e) {
        $('#ajaxincomesources').on('submit', (function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            if ($('#ajaxincomesources').valid())
            {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (msg) {
                        var elem = $(".notificationclosesources");
                        $("#sourcename").val('');
                        $("#personfile").val('');
                        $("#sourcedetails").val('');
                        if (msg == 1) {
                            swal("Congratulations!", "Record Updated successfully.", "success");
                        } else //if (msg == 2) {
                        {
                            swal("System Error", "Contact Support Team.", "error");
                        }

                        refreshGrid();
                    },
                    error: function (msg) {
                        // console.log("error");
                        //  console.log(data);
                    }
                });
            }
        }));

//    $("#ImageBrowse").on("change", function() {
//        $("#imageUploadForm").submit();
//    });
    });
    $(document).ready(function (e) {
        $('#ajaxassets').on('submit', (function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            if ($('#ajaxassets').valid())
            {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (msg) {
                        var ab = '';
                        $("#assetname").val(ab);
                        $("#assetdetails").val(ab);
                        $("#assetid").val(ab);
                        $("#personfileasset").val(ab);
                        var elem = $(".notificationcloseassets");
                        refreshGrid();
                        if (msg == 1) {
                            swal("Congratulations!", "Record Updated successfully.", "success");
                        } else //if (msg == 2) {
                        {
                            swal("System Error", "Contact Support Team.", "error");
                        }
                    },
                    error: function (msg) {
                        console.log("error");
                        console.log(data);
                    }
                });
            }
        }));

//    $("#ImageBrowse").on("change", function() {
//        $("#imageUploadForm").submit();
//    });
    });
//Person recommendations/remarks
    function callpersonassets() {
        if (countassets >= 1)
        {
            refreshGrid();
        } else {
            $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
                if (oSettings.oFeatures.bServerSide === false) {
                    var before = oSettings._iDisplayStart;
                    oSettings.oApi._fnReDraw(oSettings);
                    // iDisplayStart has been reset to zero - so lets change it back
                    oSettings._iDisplayStart = before;
                    oSettings.oApi._fnCalculateEnd(oSettings);
                }
                // draw the 'current' page
                oSettings.oApi._fnDraw(oSettings);
            };
            objDT = $('#assetstable').dataTable(
                    {"aaSorting": [],
                        "bPaginate": true,
                        "bProcessing": true,
                        //"bStateSave": true,
                        "bServerSide": true,
                        "sAjaxSource": "<?php echo URL::site('personprofile/ajaxpersonassets/?id=' . $_GET['id'], TRUE); ?>",
                        "sPaginationType": "full_numbers",
                        "bFilter": true,
                        "bLengthChange": true,
                        "oLanguage": {
                            "sProcessing": "Loading...",
                            "sSearch": "Search By Asset Name or Details:"
                        },
                        "columnDefs": [{
                                "targets": 'no-sort',
                                "orderable": false,
                            }]
                    }
            );
        }
        $('.dataTables_empty').html("Information not found");
        countassets = countassets + 1;
    }
    //function to edit asset
    function editasset(nam, det, assetid) {
        $("#assetname").val(nam);
        $("#assetdetails").val(det);
        $("#assetid").val(assetid);
    }
    //function to delete report
    function deleteasset(id, aname)
    {
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
        //var elem = $(this).parent().parent();// .closest('.action');
        // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        var aname = aname;
        $.confirm({
            'title': 'Delete Confirmation',
            'message': 'Do you really want to delete ' + aname + ' ?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::site('Personprofile/deleteasset/?id=' . $_GET['id'] . '&assetid=', TRUE); ?>' + id,
                            success: function (msg) {
                                var elem = $(".notificationcloseassets");
                                if (msg == 1) {
                                    swal("Congratulations!", "Record deleted successfully.", "warning");
                                } else if (msg == 2) {
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                refreshGrid();
                            }});
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {
                    }  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
</script>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
<script type="text/javascript">
    $(document).ready(function () {

        //validate Person Basic Info
        $("#basicinfoform").validate({
            rules: {
                first_name: {
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 50
                },
                last_name: {
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 50
                },
                fathername: {
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 50
                },
                address: {
                    // alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 500
                },
            },
            messages: {
                first_name: {
                    alphanumericspecial: "Only letters, Numbers & Space/underscore Allowed",
                    maxlenght: "Maximum character limit is 50",
                    minlength: "Min character limit is 1"
                },
                last_name: {
                    alphanumericspecial: "Only letters, Numbers,Comma,Colon & Space/underscore Allowed",
                    maxlenght: "Maximum character limit is 50",
                    minlength: "Min character limit is 1"
                },
                fathername: {
                    alphanumericspecial: "Only letters, Numbers,Comma,Colon & Space/underscore Allowed",
                    maxlenght: "Maximum character limit is 50",
                    minlength: "Min character limit is 1"
                },
                address: {
                    //  alphanumericspecial:"Only letters, Numbers & Space/underscore Allowed",
                    maxlenght: "Maximum character limit is 500",
                    minlength: "Min character limit is 1"
                },
            }

        });
        //validate Person Detailed Info
        $("#detailedinfoform").validate({
            rules: {
                alias: {
                    lettersandspaceonly: true,
                    minlength: 1,
                    maxlength: 50
                },
                temporaryaddress: {
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 500
                },
                physicalappearance: {
                    alphanumericspecial: true,
                    maxlength: 500
                },
            },
            messages: {
                alias: {
                    lettersandspaceonly: "Only letters & Space Allowed",
                    maxlenght: "Maximum character limit is 50",
                    minlength: "Min character limit is 1"
                },
                temporaryaddress: {
                    alphanumericspecial: "Only letters, Numbers,Comma,Colon & Space/underscore Allowed",
                    maxlenght: "Maximum character limit is 500",
                    minlength: "Min character limit is 1"
                },
                physicalappearance: {
                    alphanumericspecial: "Only letters, Numbers,Comma,Colon & Space/underscore Allowed",
                    maxlenght: "Maximum character limit is 500",
                },
            }

        });
        //validate Person Identities
        $("#identityform").validate({
            rules: {
                idnname: {
                    required: true,
                },
                idnnnumber: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 30
                },
            },
            messages: {
                idnname: {
                    required: "Required",
                },
                idnnnumber: {
                    required: "Required",
                    alphanumericspecial: "Only letters, Numbers,Dot,Comma,Colon & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
            }

        });
        //validate Person Education
        $("#educationform").validate({
            rules: {
                edutype: {
                    check_list: true,
                },
                degname: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 30
                },
                compyear: {
                    // required: true,
                    numbersonly: true,
                    minlength: 4,
                    maxlength: 4
                },
                institute: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 100
                },
            },
            messages: {
                edutype: {
                    required: "Required",
                },
                degname: {
                    required: "Required",
                    alphanumericspecial: "Only letters, Numbers,Dot,Comma,Colon & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
                compyear: {
                    required: "Required",
                    numbersonly: "Numbers Only",
                    maxlenght: "Maximum 4 digits",
                    minlength: "Min 4 digits"
                },
                institute: {
                    required: "Required",
                    alphanumericspecial: "Only letters, Numbers,Dot,Comma,Colon & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
            }

        });
        //validate Person Income Source
        $("#ajaxincomesources").validate({
            rules: {
                sourcename: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 50
                },
                sourcedetails: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 1000
                },
            },
            messages: {
                sourcename: {
                    required: "Enter Income Source Name",
                    maxlenght: "Maximum character limit is 50",
                    minlength: "Min character limit is 1"
                },
                sourcedetails: {
                    required: "Enter Source Details",
                    maxlenght: "Maximum character limit is 1000",
                    minlength: "Min character limit is 1"
                },
            }

        });
        //validate Person Banks Details
        $("#banksform").validate({
            rules: {
                accountno: {
                    required: true,
                    lettersandnumbersonly: true,
                    minlength: 5,
                    maxlength: 30
                },
                atmno: {
                    numbersonly: true,
                    minlength: 5,
                    maxlength: 25
                },
                bankname: {
                    check_list: true,
                },
                branchname: {
                    alphanumericspecial: true,
                    minlength: 2,
                    maxlength: 50
                },
            },
            messages: {
                accountno: {
                    required: "Enter Accountn No",
                    lettersandnumbersonly: "Letters Numbers, Dash and Underscore Only",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 5"
                },
                bankname: {
                    required: "Required",
                },
                atmno: {
                    numbersonly: "Numbers Only",
                    maxlenght: "Maximum character limit is 25",
                    minlength: "Min character limit is 5"
                },
                branchname: {
                    required: "Enter Branch Name",
                    maxlenght: "Maximum character limit is 50",
                    minlength: "Min character limit is 5"
                },
            }

        });
        //validate Person Assets
        $("#ajaxassets").validate({
            rules: {
                assetname: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 30
                },
                assetdetails: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 500
                },
            },
            messages: {
                assetname: {
                    required: "Required",
                    alphanumericspecial: "Only letters, Numbers,Dot,Comma,Colon & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
                assetdetails: {
                    required: "Required",
                    alphanumericspecial: "Only letters, Numbers,Dot,Comma,Colon & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 500",
                    minlength: "Min character limit is 1"
                },
            }

        });
        //validate Person Mobiles
        $("#mobileform").validate({
            rules: {
                msisdn: {
                    required: true,
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                },
            },
            messages: {
                msisdn: {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },
            }

        });
        //validate Person Relations
        $("#relationform").validate({
            rules: {
//                relationtype: {
//                    check_list: true,
//                },
                relationcnic: {
                    required: true,
                    custominput: true,
                    maxlength: 13,
                    minlength: 13
                },
                relationname: {
                    alphanumericspecial: true,
                    maxlength: 50
                }
            },
            messages: {
//                relationtype: {
//                    required: "Required",
//                },
                relationcnic: {
                    required: "Enter CNIC Number",
                    custominput: "Enter correct no",
                    maxlenght: "13 digits only",
                    minlength: "13 digits only"
                },
                relationname: {
                    alphanumericspecial: "Only Alpha Numeric Values",
                    maxlength: "Maximum 50 digits"
                }
            }

        });
        //validate Person Affiliations/Trainings
        $("#affilform").validate({
            rules: {
                affproject: {
                    check_list: true,
                },
                afforg: {
                    check_list: true,
                },
                affdesig: {
                    check_list: true,
                },
                affstance: {
                    check_list: true,
                },
                affdetail: {
                    alphanumericspecial: true,
                    maxlength: 500
                },
                ttype: {
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 30
                },
                tduration: {
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 30
                },
                tyear: {
                    number: true,
                    minlength: 4,
                    maxlength: 4
                },
            },
            messages: {
                affproject: {
                    required: "Required",
                },
                afforg: {
                    required: "Required",
                },
                affdesig: {
                    required: "Required",
                },
                affdetail: {
                    alphanumericspecial: "Only letters, Numbers,Dot,comma & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 500"
                },
                ttype: {
                    alphanumericspecial: "Only letters, Numbers,Dot,Comma,Colon & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
                tduration: {
                    alphanumericspecial: "Only letters, Numbers,Dot,Comma,Colon & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
                tyear: {
                    number: "Numbers Only",
                    maxlenght: "4 digits",
                    minlength: "4 digits"
                },
            }

        });
        //validate Person Trainings
        $("#training_form").validate({
            rules: {
                training_type: {
                    check_list: true,
                },
                training_camp: {
                    check_list: true,
                },
                training_site: {
                    alphanumericspecial: true,
                    maxlength: 500
                },
                training_purpose: {
                    required: true,
                    alphanumericspecial: true,
                    maxlength: 500
                },
                training_material: {
                    alphanumericspecial: true,
                    maxlength: 500
                },
                training_details: {
                    alphanumericspecial: true,
                    maxlength: 500
                },
                training_duration: {
                    number: true,
                    minlength: 1,
                    maxlength: 5
                },
                training_year: {
                    number: true,
                    minlength: 4,
                    maxlength: 4
                },
            },
            messages: {
                training_type: {
                    required: "Required",
                },
                training_camp: {
                    required: "Required",
                },
                training_site: {
                    alphanumericspecial: "Only letters, Numbers,Dot,comma & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 500"
                },
                training_purpsoe: {
                    required: "Required",
                    alphanumericspecial: "Only letters, Numbers,Dot,comma & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 500"
                },
                training_material: {
                    alphanumericspecial: "Only letters, Numbers,Dot,comma & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 500"
                },
                training_details: {
                    alphanumericspecial: "Only letters, Numbers,Dot,comma & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 500"
                },
                training_duration: {
                    number: "Only Numbers Allowed.",
                    maxlenght: "Maximum limit is 5",
                    minlength: "Min limit is 1"
                },
                training_year: {
                    number: "Numbers Only",
                    maxlenght: "4 digits",
                    minlength: "4 digits"
                },
            }

        });
        //validate Person Criminal Record
        $("#criminalform").validate({
            rules: {
                policestationcr: {
                    check_list: true,
                },
                caseposition: {
                    check_list: true,
                },
                accusedposition: {
                    check_list: true,
                },
                firno: {
                    required: true,
                    minlength: 1,
                    maxlength: 5
                },
                firdate: {
                    required: true,
                    validdate: true,
                },
                sections: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 240
                },
            },
            messages: {
                policestationcr: {
                    required: "Required",
                },
                caseposition: {
                    required: "Required",
                },
                accusedposition: {
                    required: "Required",
                },
                firno: {
                    required: "Required",
                    maxlenght: "Maximum limit is 5",
                    minlength: "Min  limit is 1"
                },
                firdate: {
                    required: "Required",
                    validdate: "(mm/dd/yyyy)",
                },
                sections: {
                    required: "Required",
                    alphanumericspecial: "Only letters, Numbers,Dot,Comma,Colon & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
            }
        });
        //validate Person Reports
        $("#ajaxreports").validate({
            rules: {
                reporttype: {
                    check_list: true,
                },
                reportno: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 30
                },
                reportdate: {
                    required: true,
                    validdate: true,
                },
                reportbrief: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 5,
                    maxlength: 1000
                },
            },
            messages: {
                reporttype: {
                    required: "Required",
                },
                reportno: {
                    required: "Required",
                    alphanumericspecial: "Only letters, Numbers,Dot,Comma,Colon & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
                reportdate: {
                    required: "Required",
                    validdate: "(mm/dd/yyyy)",
                },
                reportbrief: {
                    required: "Required",
                    alphanumericspecial: "Only letters, Numbers,Dot,Comma,Colon & Space/underscore Allowed.",
                    maxlenght: "Maximum character limit is 1000",
                    minlength: "Min character limit is 5"
                },
            }
        });
       

        jQuery.validator.addMethod("custominput", function (value, element, params) {
            //return this.optional(element) || value == params[0] + params[1];
            if (jQuery("#r_is_foreigner").val() == 0)
            {
                return ($.isNumeric($(element).val()));
            } else {
                if (jQuery.type($(element).val()) === "string")
                    return true;
                else
                    return false;
            }
        }, jQuery.validator.format("Please enter the correct value"));
//Validators
        $.validator.addMethod("lettersonly", function (value, element)
        {
            return this.optional(element) || /^[a-z]+$/i.test(value);
        }, "<span>Letters Only</span>");

        $.validator.addMethod("numbersonly", function (value, element)
        {
            return this.optional(element) || /^[0-9]+$/i.test(value);
        }, "<span>Numbers Only</span>");

        $.validator.addMethod("lettersandnumbersonly", function (value, element)
        {
            return this.optional(element) || /^[a-z0-9_-]+$/i.test(value);
        }, "<span>Letters & Numbers Only</span>");

        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");

        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .:,/]+$/);
        }, "Only letters, Numbers,Dot,Comma,Colon & Space/underscore Allowed.");

        jQuery.validator.addMethod("lettersandspaceonly", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z ]+$/);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

        jQuery.validator.addMethod("validdate",
                function (value, element) {
                    var isValid = false;
                    var reg = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
                    if (reg.test(value)) {
                        var splittedDate = value.split('/');
                        var mm = parseInt(splittedDate[0], 10);
                        var dd = parseInt(splittedDate[1], 10);
                        var yyyy = parseInt(splittedDate[2], 10);
                        var newDate = new Date(yyyy, mm - 1, dd);
                        if ((newDate.getFullYear() == yyyy) && (newDate.getMonth() == mm - 1)
                                && (newDate.getDate() == dd))
                            isValid = true;
                        else
                            isValid = false;
                    } else
                        isValid = false;
                    return this.optional(element) || isValid;
                },
                "Please enter a valid date (mm/dd/yyyy)");

        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "" || sel == " ") {
                return false;
            } else {
                return true;
            }
        }, "<span>This fiels is required</span>");

    });


    $(document).ready(function () {
        var tab_val = jQuery(".nav-tabs li.active").index();
        // alert(tab_val);
        if (tab_val == 2) {
            callpersonidentity();
        } else if (tab_val == 3) {
            callpersonedu();
        } else if (tab_val == 4) {
            callpersonsources();
        } else if (tab_val == 5) {
            callpersonbanks();
        } else if (tab_val == 6) {
            callpersonassets();
        } else if (tab_val == 7) {
            callpersonmobiles();
        } else if (tab_val == 8) {
            callpersonrelations();
        } else if (tab_val == 9) {
            callpersoncrimes();
        } else if (tab_val == 10) {
            callpersonaffiliations();
        } else if (tab_val == 11) {
            callpersonreports();
        } else if (tab_val == 12) {
            callcategoryhistory();
        }
    });
//    if ($('#mobileform').valid())
//    {
    // alert('valid');
    function check_msisdn_exist(number) {
        var num = number.value;
        var n = num.length;
        if (num == '') {
            $("#errormobile").html('');
        }
        if (n == 10) {
            // alert(num);
            var msisdn_number = num;
            var request = $.ajax({
                url: "<?php echo URL::site("upload/checkmobilenumberexist"); ?>",
                type: "POST",
                dataType: 'text',
                data: {msisdn_number: msisdn_number},
                success: function (exist)
                {
                    if (exist == 2)
                    {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    if (exist == 1) {
                        $("#errormobile").html(' Number Already Exist In AIES <a title="" class="" style="padding:0px !important" href="#" onclick="external_search_model()" >Click Here</a> for Details, If You Click "Update Number User", SIM user will be changed ');
                        $("#updatenumberuser").show();
                    } else {
                        $("#errormobile").html(' Number Does Not Exist In AIES, To Upload New Number Use Data Upload Option');
                        $("#updatenumberuser").hide();
                    }

                },
            });
        } else {
            $("#errormobile").html('');
        }
    }
    //}
    function external_search_model() {
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
        var msisdn = $("#msisdn").val();

        $("#external_search_key").val(msisdn);

        if (msisdn != 0) {
            checkmsisdndetail(msisdn);
        }
    }
    // get mobile details
    function checkmsisdndetail(msisdn) {
        //ajax call to get subscriber info
        var msisdn_number = msisdn;
        var request = $.ajax({
            url: "<?php echo URL::site("upload/checkmsisdndetail"); ?>",
            type: "POST",
            dataType: 'text',
            data: {msisdn_number: msisdn_number},
            success: function (msisdndetail)
            {
                if (msisdndetail == 2)
                {
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#externa_search_results_div").html(msisdndetail);
            },
        });
    }
</script>
