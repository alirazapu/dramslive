<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>	
                        <i  class="fa fa-user"></i>
                        User's Report 
                        <small>Tracer</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">User Registration</li>
                    </ol>
                </section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-user"></i> User Registration</h3>
                </div>
                
                <?php if(!empty($message))
                    {                  
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <?php echo $message; ?></h4>
                    </div>
                    <?php } ?>
                <?php if(!empty($errors))
                    {                  
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <?php print_r($errors); ?></h4>
                        
                    </div>
                    <?php } ?>
                
                 <form class="ipf-form" name="memberform" action="<?php echo url::site().'user/create'?>" id="userregistration" method="post" enctype="multipart/form-data" >
                    <div class="box-body">
                        <div class="form-group col-md-4" >    
                            <h3 class="style14 col-md-12">Account Picture </h3>
                            <div class="img-circle">
                                <img src="../dist/img/avtar6.jpg" width="75%" height="75%" />
                            </div>
                            <div class="form-group col-md-6"> 
                                <label for="user_pic">Change Picture</label>
                                <input type="file" accept=".jpg,.gif,.png" id="user_pic" name="user_pic" placeholder="Select Image">                                 
                            </div>                            
                        </div>
                        <div class="form-group col-md-8" >
                                <h3 class="style14 col-md-12">Personal Information </h3>
                            <div class="form-group col-md-6">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control" id="first_name"  name="first_name" placeholder="Enter First Name">
                            </div> 
                            <div class="form-group col-md-6">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" id="last_name"  name="last_name" placeholder="Enter First Name">
                            </div> 

                            <div class="form-group col-md-6">
                                <label for="father_name">Father Name</label>
                                <input type="text" class="form-control" id="father_name" name="father_name" placeholder="Enter Father Name">
                            </div>  
                            <div class="form-group col-md-6">
                                <label for="cnic_number">CNIC Number</label>
                                <input type="number" class="form-control" id="cnic_number" name="cnic_number" placeholder="Enter CNIC Number">

                            </div>
                            <div class="form-group col-md-6">
                                <label for="mobile_number">Mobile Number</label>
                                <input type="number" class="form-control" id="mobile_number" name="mobile_number" placeholder="Enter Mobile Number">

                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email Address">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="home_district">Select Home District</label>
                                <select class="form-control" name="home_district" id="home_district">
                                    <option value="">Please Select District</option>
                                    <?php  try{
                                    $district_list = Helpers_Utilities::get_district();
                                    foreach($district_list as $list)
                                    {     
                                     ?>
                                    <option value="<?php echo $list->district_id ?>"><?php echo $list->name ?></option>
                                    <?php } 
                                    }  catch (Exception $ex){   }?>
                                    
                                </select>
                            </div>
                                 <hr class="style14 col-md-12">
                            <h3 class="style14 col-md-12">Departmental Information </h3>
                            <div class="form-group col-md-6">
                                <label for="designation">Designation</label>
                                <select class="form-control" id="designation" name="designation">
                                    <option value="">Please Select Designation</option>
                                    <option value="Addl. IG">Addl. IG</option>
                                    <option value="DIG">DIG</option>
                                    <option value="SSP">SSP</option>                                    
                                    <option value="SP">SP</option>
                                    <option value="DSP">DSP</option>
                                    <option value="Corporal">Corporal(Corp)</option>
                                    <option value="Inspector">Inspector (IP)</option>
                                    <option value="Sub-Inspector">Sub-Inspector (SI)</option>
                                    <option value="Assistant Sub-Inspector">Assistant Sub-Inspector (ASI)</option>
                                    <option value="Head Constable">Head Constable (HC)</option>
                                    <option value="Constable">Constable (C)</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="belt">Belt#/Rank</label>
                                <input type="text" class="form-control" id="belt" name="belt" placeholder="Enter Belt No#">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Select Posting</label>
                                <select class="form-control" name="posting">
                                    <option value="">Please Select Posting</option>
                                    <?php 
                                        $login_user = Auth::instance()->get_user();
                                        $DB = Database::instance();
                                        $permission = Helpers_Utilities::get_user_permission($login_user->id);
                                        $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
                                        $posting = $login_user_profile->posted;
                                        $result = explode('-', $posting);
                                        switch ($result[0])
                                        {
                                            case 'h':
                                                ?>
                                                <optgroup label="Region">                                    
                                                <?php 
                                                $region_list = Helpers_Utilities::get_region();
                                                foreach($region_list as $list)
                                                {     
                                                 ?>
                                                <option value="r-<?php echo $list->region_id ?>"><?php echo $list->name ?></option>
                                                <?php } ?>
                                                <optgroup label="District">                                    
                                                 <?php 
                                                 $district_list = Helpers_Utilities::get_district();
                                                  foreach($district_list as $list)
                                                  {     
                                                   ?>
                                                  <option value="d-<?php echo $list->district_id ?>"><?php echo $list->name ?></option>
                                                  <?php } ?>
                                                <optgroup label="Police Station">                                    
                                                <?php 
                                                $police_station_list = Helpers_Utilities::get_police_station();
                                                foreach($police_station_list as $list)
                                                {     
                                                 ?>
                                                <option value="p-<?php echo $list->id ?>"><?php echo $list->name ?></option>
                                                <?php } ?>
                                                <optgroup label="Head Quarter">                                    
                                                <?php 
                                                $headquarter_list = Helpers_Utilities::get_headquarter();
                                                foreach($headquarter_list as $list)
                                                {     
                                                 ?>
                                                <option value="h-<?php echo $list->id ?>"><?php echo $list->name ?></option>
                                                <?php } ?>
                                                <?php
                                                break;
                                            case 'r':
                                                ?>
                                                <optgroup label="Region">                                    
                                                <?php 
                                                $region_list = Helpers_Utilities::get_region();
                                                foreach($region_list as $list)
                                                {     
                                                 ?>
                                                <option value="r-<?php echo $list->region_id ?>"><?php echo $list->name ?></option>
                                                <?php } ?>
                                                <optgroup label="District">                                    
                                                 <?php 
                                                 $district_list = Helpers_Utilities::get_district();
                                                  foreach($district_list as $list)
                                                  {     
                                                   ?>
                                                  <option value="d-<?php echo $list->district_id ?>"><?php echo $list->name ?></option>
                                                  <?php } ?>
                                                <optgroup label="Police Station">                                    
                                                <?php 
                                                $police_station_list = Helpers_Utilities::get_police_station();
                                                foreach($police_station_list as $list)
                                                {     
                                                 ?>
                                                <option value="p-<?php echo $list->id ?>"><?php echo $list->name ?></option>
                                                <?php }?>
                                                <?php
                                                break;
                                            case 'd':
                                                 ?><optgroup label="District">                                    
                                                <?php 
                                                $district_list = Helpers_Utilities::get_district();
                                                foreach($district_list as $list)
                                                {     
                                                 ?>
                                                <option value="d-<?php echo $list->district_id ?>"><?php echo $list->name ?></option>
                                                <?php } ?>
                                                <?php
                                                break;
                                            case 'p':
                                                ?>
                                                <optgroup label="Police Station">                                    
                                                <?php 
                                                $police_station_list = Helpers_Utilities::get_police_station();
                                                foreach($police_station_list as $list)
                                                {     
                                                 ?>
                                                <option value="p-<?php echo $list->id ?>"><?php echo $list->name ?></option>
                                                <?php } ?>
                                                <?php
                                                break;
                                        }
                                    ?>
                                    
                                </select>
                            </div> 
                            <div class="form-group col-md-6">
                                <label for="order">Office Order No.</label>
                                <input type="text" class="form-control" id="order" name="order" placeholder="Enter Office Order No.">
                            </div>
                            <hr class="style14 col-md-12">
                            <h3 class="style14 col-md-12">Account Information </h3>
                            <div class="form-group col-md-6">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" id="username" placeholder="Username">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="user_type">User Type</label>
                                <select class="form-control" name="type" id="type">
                                    <option value="">Please Select User Type</option>
                                    <?php 
                                    $roles_list = Helpers_Utilities::get_roles_data();
                                    foreach($roles_list as $list)
                                    {     
                                     ?>
                                    <option value="<?php echo $list->name ?>"><?php echo $list->label ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="password">Enter New Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="password_confirm">Confirm New Passsword</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirm Password">
                            </div>  
<!--                            <div class="form-group col-md-6" style="margin-top: 24px">
                            <div class="pull-right">
                                    <input type="submit" value="Change Password"  id="changepassowrd" class="btn btn-success "/>
                            </div>
                            </div>-->
                            <hr class="style14 col-md-12">
                            <div class="form-group col-md-12 " style="margin-top: 24px">
                                <div class="pull-right">                                             
                                    <input type="submit" value="Submit" class="btn btn-primary " />
                                </div>
                            </div>
                        </div>                                        
                    </div>
                </form>
            </div>
        </div>
    </div>


</section>
<!-- /.content -->

<script type="text/javascript">
    $(document).ready(function(){
        $("#userregistration").validate({
                  rules:{
                        first_name:{
                                required:true,
                                alphanumericspecial: true,
                                minlength: 1,
                                maxlength: 20
                                   },
                        last_name:{
                               required:true,
                                alphanumericspecial: true,
                                minlength: 1,
                                maxlength: 20
                            },
                        father_name:{
                                required:true,
                                alphanumericspecial: true,
                                minlength: 1,
                                maxlength: 40
                            },
                        mobile_number:{
                            required: true,
                            number: true,
                            numberthree: true,
                            maxlength: 10,
                            minlength: 10,
                        },
                        cnic_number:{
                            required: true,
                            number: true,
                            maxlength: 13,
                            minlength: 13,
                        },
                        email:{
                            required: true, 
                            email: true,
                            maxlength: 40,
                            duplicate: true
                        },
                        home_district:{
                                required:true,
                                check_list:true
                            },
                        designation:{
                                required:true,
                                check_list:true
                            },
                         belt:{
                             required:true,
                                alphanumericspecial: true,
                                minlength: 1,
                                maxlength: 20
                         },
                         order:{
                             required:true,
                                alphanumericorder: true,
                                minlength: 3,
                                maxlength: 20
                         },
                        posting:{
                                required:true,
                                check_list:true
                            },      
                        type:{
                                required:true,
                                check_list:true
                            },      
                       username:{
                                required:true,
                                minlength: 8,
                                lonly: true,
                                duplicateusername: true
                            },
                        password:{
                            required:true,
                            minlength: 8,
                            maxlength: 20
                            },
                        password_confirm:{
                            required:true,
                            equalTo: "#password"
                            },  
                        },
                    messages: {
                        first_name:{
                                required:"Enter Last Name Name",
                             maxlenght:"Maximum character limit is 20",
                             minlength:"Min character limit is 1"
                              },
                        last_name:{
                                 required:"Enter Last Name Name",
                             maxlenght:"Maximum character limit is 20",
                             minlength:"Min character limit is 1"
                               },
                        father_name:{
                             required:"Enter Father Name",
                             maxlenght:"Maximum character limit is 40",
                             minlength:"Min character limit is 1"
                        },
                        mobile_number:{
                                required:"Enter Mobile Number",
                                Number:"Enter only number",
                                maxlenght:"Maximum character limit is 10"
                                },
                        cnic_number:{
                                //required:"Enter Mobile Number",
                                //Number:"Enter only number",
                                //maxlenght:"Maximum character limit is 10"
                                },
                        email:{
                                required:"Enter valid email",
                                email:"Email is invalid",
                                maxlenght:"Maximum character limit is 40",
                                numberthree:"Numbers must be 10 digits, starting from 3"
                             },
                         home_district:{
                             required:"Enter Home District"
                         },
                         designation:{
                              required:"Enter Designation",
                         },
                         belt:{
                              required:"Enter Belt Number",
                         },
                         order:{
                              required:"Enter Officer Order No.",
                         },
                         posting:{
                              required:"Enter Posting location",
                         },
                         type:{
                              required:"Enter User Type",
                         },
                         username:{
                              required:"Enter User Name",
                              minlength: "Minimum characters limit 8"
                              
                         },
                         password:{
                            required:"Enter password",
                            minlength: "Min 8-characters",
                            maxlength: "Max 20-characters"
                            },
                        password_confirm:{
                            required:"Enter confirm password",
                            equalTo: "Password does not match"
                            },  
                        }                       
                   
                });
                
        $.validator.addMethod("check_list",function(sel,element){
            if(sel == "" || sel == 0){
                return false;
             }else{
                return true;
             }
            },"<span>Select One</span>");

      $.validator.addMethod("lettersonly", function(value, element) 
            {
                return this.optional(element) || /^[a-z]+$/i.test(value);
             }, "<span>Letters Only</span>");
             
        jQuery.validator.addMethod("numberthree", function(value, element) {
        return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3."); 
        
    jQuery.validator.addMethod("alphanumericspecial", function(value, element) {
        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ /]+$/);
        }, "Only letters, Numbers & Space/underscore Allowed."); 
        
    jQuery.validator.addMethod("alphanumericorder", function(value, element) {
        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ /.]+$/);
        }, "Only letters, Numbers, / & Space/underscore Allowed.");  
        
    jQuery.validator.addMethod("lonly", function(value, element) {
        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9]+$/);
        }, "Only letters and Numbers,without space"); 
        
    jQuery.validator.addMethod("duplicate", function(value, element) {
        //alert(value);        
        var isSuccess;
                var result = {email: value}
                isSuccess =  ajax1(result);                                 
                    setTimeout(function(){                         
                        isSuccess= isSuccess;                    
                            }, 1000);      
                            return isSuccess;                     
        }, "E-mail Already Exits in database");                        
        
        function ajax1(result) {    
            var isSuccess = false;
             $.ajax({
                    url: "<?php echo URL::site("User/email_duplicate"); ?>",
                        type: 'POST',
                        data: result,
                        cache: false,
                        //dataType: "text",
                        dataType: 'json',
                        async: false,        
                        success: function (msg) {
                            if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                           isSuccess =  msg;
                        }

            });
            
            return isSuccess; 
            }
    jQuery.validator.addMethod("duplicateusername", function(value, element) {
        //alert(value);        
        var isSuccess;
                var result = {username: value}
                isSuccess =  ajax2(result);                                 
                    setTimeout(function(){                         
                        isSuccess= isSuccess;                    
                            }, 1000);      
                            return isSuccess;                     
        }, "User Name already taken,choose another");                        
        
        function ajax2(result) {    
            var isSuccess = false;
             $.ajax({
                    url: "<?php echo URL::site("User/username_duplicate"); ?>",
                        type: 'POST',
                        data: result,
                        cache: false,
                        //dataType: "text",
                        dataType: 'json',
                        async: false,        
                        success: function (msg) {
                            if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                           isSuccess =  msg;
                        }

            });
            
            return isSuccess; 
            }
    });
</script>

    