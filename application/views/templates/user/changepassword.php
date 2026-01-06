
<div class="container bootstrap snippet" id="changepassword">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-2">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <span class="glyphicon glyphicon-th"></span>
                        Change password   
                    </h3>
                </div>
                <form class="ipf-form" name="changepassword_form" action="<?php echo url::site().'user/update_password'?>" id="changepassword_form" method="post" enctype="multipart/form-data">
                    <div class="panel-body">
                        <div class="row">                       
                            <div class="col-md-5 separator social-login-box"> <br>
                                <?php
                                if (!empty($data->file_name) || $data->file_name != 0) {
                                    echo HTML::image("dist/uploads/user/profile_images/{$data->file_name}", array("height" => "", "width" => "200px"));
                                } else {
                                    echo HTML::image("dist/img/avtar6.jpg", array("height" => "", "width" => "200px"));
                                }
                                ?>
                                <!--<img alt="" class="img-thumbnail" src="https://bootdey.com/img/Content/avatar/avatar1.png">-->                        
                            </div>
                            <div style="margin-top:50px; width: 55% !important; padding: 0 0 0 10px" class="col-md-7 login-box">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
                                        <input class="form-control" type="password" id="oldpassword" name="oldpassword" placeholder="Current Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon"><span class="glyphicon glyphicon-log-in"></span></div>
                                        <input class="form-control" type="password" name="password" id="password" placeholder="Enter New Password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon"><span class="glyphicon glyphicon-log-in"></span></div>
                                        <input class="form-control" type="password" id="password_confirm" name="password_confirm" placeholder="Re-Enter New Password">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6"></div>
                            <div class="col-xs-6 col-sm-6 col-md-6">
                                <button class="btn icon-btn-save btn-success" type="submit">
                                    <span class="btn-save-label"><i class="glyphicon glyphicon-floppy-disk"></i></span>save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    #changepassword .separator {
        border-right: 1px solid #dfdfe0; 
    }
    #changepassword .icon-btn-save {
        padding-top: 0;
        padding-bottom: 0;
    }
    #changepassword .input-group {
        margin-bottom:10px; 
    }
    #changepassword .btn-save-label {
        position: relative;
        left: -12px;
        display: inline-block;
        padding: 6px 12px;
        background: rgba(0,0,0,0.15);
        border-radius: 3px 0 0 3px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function(){
        $("#changepassword_form").validate({
                  rules:{
                      oldpassword:{
                          required:true, 
                          oldpasswordmatch: true
                      },
                        password:{
                            required:true,
                            alphanumericspecial: true,
                            minlength: 8,
                            maxlength: 20
                            },
                        password_confirm:{
                            required:true,
                            equalTo: "#password"
                            },  
                        },
                    messages: {
                        oldpassword:{
                            required:"Enter Current password" 
                            //oldpasswordmatch:"Password is not correct", 
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
        jQuery.validator.addMethod("alphanumericspecial", function(value, element) {
        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_@]+$/);
        }, "Enter Only letters, Numbers,_ or @"); 
        
        jQuery.validator.addMethod("oldpasswordmatch", function(value, element) {
        //alert(value);        
        var isSuccess;
                var result = {oldpassword: value}
                isSuccess =  ajax1(result);                                 
                    setTimeout(function(){                         
                        isSuccess= isSuccess;                    
                            }, 1000);      
                            return isSuccess;                     
        }, "Password does not match");
        
        function ajax1(result) {               
            var isSuccess = false;
             $.ajax({
                    url: "<?php echo URL::site("User/current_password"); ?>",
                        type: 'POST',
                        data: result,
                        cache: false,
                        //dataType: "text",
                        dataType: 'json',
                        async: false,        
                        success: function (msg) {
                           isSuccess =  msg;
                        }

            });
            
            return isSuccess; 
            }
        
        
            });
</script>