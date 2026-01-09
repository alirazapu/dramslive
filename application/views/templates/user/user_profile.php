<?php
 $user_id = $data->user_id;
 $login_user = Auth::instance()->get_user();
 $permission = Helpers_Utilities::get_user_permission($login_user->id);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-files-o"></i>
        User Profile
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">User Profile</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-user"></i> User Profile</h3>
                </div>
                <?php
                if (!empty($_GET['accessmessage'])) {
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <?php echo $_GET['accessmessage']; ?></h4>
                    </div>
                <?php } ?>
                <div class="box-body">                    
                        <div class="form-group col-md-4" >  
                            <div class="form-group col-md-12" >
                                <h3 class="style14 col-md-12">Account Picture </h3>
                                <div class="img-thumbnail ">
                                    <?php if(!empty($data->file_name) || $data->file_name != 0){                                 
                                     echo HTML::image("dist/uploads/user/profile_images/{$data->file_name}",array("height"=> "100%","width"=>"100%"));  
                                    }else{
                                       echo HTML::image("dist/img/avtar6.jpg",array("height"=> "75%","width"=>"75%"));  
                                    }?>                               
                                </div> 
                            </div>
                            <?php if ($permission == 1) { ?>
                            <div class="form-group col-md-12" >
                                <form class="ipf-form" name="memberform" action="<?php echo url::site().'user/update_profile_picture'?>" id="update_picture" method="post" enctype="multipart/form-data" >
                                    <h3 class="style14 col-md-12">Update Account Picture </h3>
                                    <div class="form-group col-md-12"> 
                                        <input type="hidden" class="form-control" id="user_id" value="<?php echo $data->user_id; ?>" name="user_id">
                                    </div>
                                    <div class="form-group col-md-12"> 
                                        <input required="" type="file" accept=".jpg,.gif,.png" id="user_pic_update" name="user_pic_update" placeholder="Select Image">                                 
                                    </div>   
                                    <div class="form-group col-md-12"> 
                                        <div class="pull-right">                                             
                                                <input type="submit" value="Update Profile Picture" class="btn btn-primary " />
                                        </div>
                                    </div>   
                                </form>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="form-group col-md-8" >
                                <h3 class="style14 col-md-12">Personal Information </h3>
                            <div class="form-group col-md-6">
                                <label for="first_name">First Name</label>
                                <input disabled type="text" class="form-control" id="first_name" value="<?php echo $data->first_name; ?>" name="first_name" placeholder="Enter First Name">
                            </div> 
                            <div class="form-group col-md-6">
                                <label for="last_name">Last Name</label>
                                <input disabled type="text" class="form-control" id="last_name" value="<?php echo $data->last_name; ?>" name="last_name" placeholder="Enter First Name">
                            </div> 

                            <div class="form-group col-md-6">
                                <label for="father_name">Father Name</label>
                                <input disabled type="text" class="form-control" id="father_name" name="father_name" value="<?php echo $data->father_name; ?>" placeholder="Enter Father Name">
                            </div>  
                            <div class="form-group col-md-3">
                                <label for="mobile_number">Mobile Number</label>
                                <input disabled type="number" class="form-control" id="mobile_number" name="mobile_number" value="<?php echo $data->mobile_number; ?>" placeholder="Enter Mobile Number">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="mobile_number">CNIC Number</label>
                                <input disabled type="number" class="form-control" id="cnic_number" name="cnic_number" value="<?php echo !empty($data->cnic_number) ? $data->cnic_number : 'N/A'; ?>" placeholder="Enter Mobile Number">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">Email</label>
                                <input disabled type="email" class="form-control" name="email" id="email" value="<?php echo ( isset($user_log_info->email) ) ? $user_log_info->email : 'NA' ; ?>" placeholder="Enter Email Address">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="home_district">Select Home District</label>
                                <select disabled class="form-control" name="home_district" id="home_district">
                                    <option value="">Please Select District</option>
                                    <?php 
                                    echo $data->district_id;
                                    $district_list = Helpers_Utilities::get_district();
                                    foreach($district_list as $list)
                                    {     
                                     ?>
                                    <option <?php echo ($data->district_id==$list->district_id)?"Selected":''; ?> value="<?php echo $list->district_id ?>"><?php echo $list->name ?></option>
                                    <?php } ?>
                                    
                                </select>
                            </div>
                                 <hr class="style14 col-md-12">
                            <h3 class="style14 col-md-12">Departmental Information </h3>
                            <div class="form-group col-md-6">
                                <label for="designation">Designation </label>
                                 <input disabled type="text" class="form-control" name="designation" id="designation" value="<?php echo ( isset($data->job_title) ) ? $data->job_title : 'NA' ; ?>" placeholder="Enter Designation">
<!--                                <select disabled class="form-control" id="designation" name="designation">                                   
                                    <option>Please Select Designation</option>
                                    <option <?php echo ($data->job_title== 'Addl. IG')?"Selected":''; ?> value="Addl. IG">Addl. IG</option>
                                    <option <?php echo ($data->job_title=='DIG')?"Selected":'DIG'; ?> value="DIG">DIG</option>
                                    <option <?php echo ($data->job_title=='SSP')?"Selected":'SSP'; ?>  value="SSP">SSP</option>                                    
                                    <option <?php echo ($data->job_title=='SP')?"Selected":'SP'; ?>  value="SP">SP</option>
                                    <option <?php echo ($data->job_title=='DSP')?"Selected":'DSP'; ?> value="DSP">DSP</option>
                                    <option <?php echo ($data->job_title=='Corporal')?"Selected":'Corporal'; ?>  value="Corporal">Corporal(Corp)</option>
                                    <option <?php echo ($data->job_title=='Inspector')?"Selected":''; ?>  value="Inspector">Inspector (IP)</option>
                                    <option <?php echo ($data->job_title=='SI')?"Selected":''; ?>  value="SI">Sub-Inspector (SI)</option>
                                    <option <?php echo ($data->job_title=='Assistant Sub-Inspector')?"Selected":''; ?> value="Assistant Sub-Inspector">Assistant Sub-Inspector (ASI)</option>
                                    <option <?php echo ($data->job_title=='Head Constable')?"Selected":''; ?>  value="Head Constable">Head Constable (HC)</option>
                                    <option <?php echo ($data->job_title=='Constable')?"Selected":''; ?>  value="Constable">Constable (C)</option>
                                </select>-->
                            </div>
                            <div class="form-group col-md-6">
                                <label for="belt">Belt#/Rank</label>
                                <input disabled type="text" class="form-control" id="belt" name="belt" value="<?php echo $data->belt; ?>" placeholder="Enter Belt No#">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Select Posting</label>
                                <select disabled class="form-control" name="posting">
                                    <option>Please Select Posting</option>
                                    <optgroup label="Region">                                    
                                    <?php  try{
                                    $region_list = Helpers_Utilities::get_region();
                                    foreach($region_list as $list)
                                    {     
                                     ?>
                                    <option <?php echo ($data->posted== 'r-' . $list->region_id)?"Selected":''; ?> value="r-<?php echo $list->region_id ?>"><?php echo "RO: ".$list->name ?></option>
                                    <?php } 
                                    }  catch (Exception $ex){   }?>
                                    <optgroup label="District">                                    
                                    <?php try{
                                    $district_list = Helpers_Utilities::get_district();
                                    foreach($district_list as $list)
                                    {     
                                     ?>
                                    <option <?php echo ($data->posted== 'd-' . $list->district_id)?"Selected":''; ?> value="d-<?php echo $list->district_id ?>"><?php echo "DO: ".$list->name ?></option>
                                    <?php } 
                                    }  catch (Exception $ex){   }?>
                                    <optgroup label="Police Station">                                    
                                    <?php try{
                                    $police_station_list = Helpers_Utilities::get_police_station();
                                    foreach($police_station_list as $list)
                                    {     
                                     ?>
                                    <option <?php echo ($data->posted== 'p-' . $list->id)?"Selected":''; ?> value="p-<?php echo $list->id ?>"><?php echo "PS: ".$list->name ?></option>
                                    <?php } 
                                    }  catch (Exception $ex){   }?>
                                    <optgroup label="Head Quarter">                                    
                                    <?php try{
                                    $headquarter_list = Helpers_Utilities::get_headquarter();
                                    foreach($headquarter_list as $list)
                                    {     
                                     ?>
                                    <option <?php echo ($data->posted== 'h-' . $list->id)?"Selected":''; ?> value="h-<?php echo $list->id ?>"><?php echo "HQRS: ".$list->name ?></option>
                                    <?php } 
                                    }  catch (Exception $ex){   }?>
                                </select>
                            </div> 
                            <hr class="style14 col-md-12">
                            <h3 class="style14 col-md-12">Account Information </h3>
                        <form class="ipf-form" name="memberform" action="<?php echo url::site().'user/update_user_role'?>" id="update_role" method="post" enctype="multipart/form-data" >
                            <div class="form-group col-md-6">
                                <label for="username">Username</label>
                                <input  type="text" name="username" class="form-control" id="username" value="<?php echo ( isset($user_log_info->username) ) ? $user_log_info->username : 'NA';?>" placeholder="Username">
                            </div>                            
                                <div class="form-group col-md-6">
                                    <label for="user_type">User Type</label>                                    
                                    <select <?php echo ($permission == 1 || $permission == 5) ? '' : 'disabled'?> class="form-control" name="user_role" id="user_role">
                                        <option value="">Please Select User Role</option>
                                        <?php                     
                                        $role_id= Helpers_Utilities::get_user_role_id($data->user_id); 
                                        $roles_list = Helpers_Utilities::get_user_type();
                                        foreach ($roles_list as $role) {   ?>
                                            <option <?php echo ($role_id == $role->id) ? "Selected" : ''; ?> value="<?php echo $role->id ?>"><?php echo $role->label ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php $user_id = !empty($data->user_id) ? $data->user_id : 0 ?>
                                    <input name="user_id" type="hidden" value="<?php echo $user_id?>">
                                </div>
                                <?php if ($permission == 1 || $permission == 5) { ?>
                                <div class="form-group col-md-12">
                                    <div class="pull-right">                                             
                                        <input type="submit" value="Update User Role" class="btn btn-primary " />
                                    </div>
                                </div>
                                <?php } ?>
                            </form>
                            <div class="form-group col-md-6">
                                <label for="tlogin">Total Logins</label>
                                <input disabled type="text" class="form-control" name="tlogin"  value="<?php echo ( isset($user_log_info->logins) ) ? $user_log_info->logins : 'NA' ;?>" id="tlogin" placeholder="Total Login">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="llogin">Last Login</label>
                                <input disabled type="text" class="form-control" id="llogin" value="<?php if(isset($user_log_info->Last_login)){echo date('m/d/Y H:i:s' ,$user_log_info->Last_login);} else{echo 'N/A';} ?>" name="llogin" placeholder="Last Login">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="astatus">Account Status</label>
                                <input disabled type="text" class="form-control" id="astatus" value="<?php if(isset($user_log_info->is_active)){ switch ($user_log_info->is_active){ case "1": echo 'Active'; break; case "0": echo 'In-Active'; break;}} else{    echo 'N/A';}?>" name="astatus" placeholder=" Status">
                            </div> 
                            <?php 
                                    if(!empty($data->created_by)){
                                ?>
                            <div class="form-group col-md-6">                                
                                <label for="createdby">Created By</label>
                                <input disabled type="text" class="form-control" id="createdby" value="<?php echo Helpers_Utilities::get_user_name($data->created_by); ?>" name="createdby" placeholder=" Created By">
                            </div> 
                                    <?php } ?>
                            <div class="form-group col-md-6">
                                <label for="createdat">Created At</label>
                                <input disabled type="text" class="form-control" id="createdat" value="<?php echo $data->created_at; ?>" name="createdat" placeholder=" Created At">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="modifiedat">Modified At</label>
                                <input disabled type="text" class="form-control" id="modifiedat" value="<?php if($data->modified_at==""){echo "Nill";}else {echo $data->modified_at;}; ?>" name="modifiedat" placeholder=" Modified At">
                            </div>
<!--                            <div class="form-group col-md-6" style="margin-top: 24px">
                            <div class="pull-right">
                                    <input disabled type="submit" value="Change Password"  id="changepassowrd" class="btn btn-success "/>
                            </div>
                            </div>-->
                            <hr class="style14 col-md-12">
                            <h3 class="style14 col-md-12">User's Persons </h3>
                            <div class="form-group col-md-6">
                                <label for="tblack">Total Black</label>
                                <input disabled type="text" class="form-control" id="tblack" value="<?php echo Helpers_Utilities::get_users_black_person($user_id); ?>" name="tblack" placeholder=" Nill">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="tgray">Total Gray</label>
                                <input disabled type="text" class="form-control" id="tgray" value="<?php echo Helpers_Utilities::get_users_grey_person($user_id); ?>" name="tgray" placeholder=" Nill">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="twhite">Total White</label>
                                <input disabled type="text" class="form-control" id="twhite" value="<?php echo Helpers_Utilities::get_users_white_person($user_id); ?>" name="twhite" placeholder=" Nill">
                            </div>
                        </div>                                        
                    </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->
<script>
     //Person tags form submit through ajax call
             $('#update_picture').on('submit', function (e) {
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
                            document.getElementById("update_picture").reset();
                            if (msg == 1) {
                                swal("Congratulations!", "User Profile Picture Updated Successfully.", "success");
                                location.reload();
                             }else{
                                swal("System Error", "Contact Support Team.", "error");
                            }
                            
                        },
                        error: function (data) {
                        console.log("error");
                        console.log(data);
                        }
                    });
                });
             $('#update_role').on('submit', function (e) {
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
                            if (msg == 1) {
                                swal("Congratulations!", "User Role Updated Successfully.", "success");
                                location.reload();
                             }else{
                                swal("System Error", "Contact Support Team.", "error");
                            }
                            
                        },
                        error: function (data) {
                        console.log("error");
                        console.log(data);
                        }
                    });
                });
</script>