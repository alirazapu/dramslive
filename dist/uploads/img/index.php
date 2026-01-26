<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>CTD | Log in</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="https://www.aiesmail.com/bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="https://www.aiesmail.com/dist/css/kpkhtml.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="https://www.aiesmail.com/plugins/iCheck/square/blue.css">

        <!--New Added-->
        <link rel="stylesheet" href="https://www.aiesmail.com/dist/css/pre-loader.css">
        <link rel="stylesheet" href="https://www.aiesmail.com/dist/css/animate.css">
        <link rel="stylesheet" href="https://www.aiesmail.com/dist/css/new-style.css">
        <!--New Added-->
        <link rel="shortcut icon" href="https://www.aiesmail.com/dist/img/icon/icon.png" type="image/x-icon"> 

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="hold-transition login-page">
        <div id="preloader" style="display: block;">
            <div class="loader">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div> 
        <div id="canvas_go">
            <div id="particles-js"></div>
            <div class="count-particles">
                <span class="js-count-particles"></span>
            </div>
        </div>
        <div class="login-box">
            <div class="login-logo">
                <a href=""><b></b><img style="width:153px;" src="https://www.aiesmail.com/dist/img/CTD-Logo.png"> </a>
            </div>
            <!-- /.login-logo -->
            <div class="login-box-body">                
                <div>
                                                                            </div>
                <div class="form-top-left">
                    <h3>Sign in</h3>
                    <p>Enter your username and password</p>
                </div>

                <div class="form-top-right">
                    <i class="fa fa-key"></i>
                </div>

<form action="https://www.aiesmail.com/login/check" method="post" id="entrypoint" accept-charset="utf-8">    

                <div><label for="username" generated="true" class="error" style="display: none"></label></div>
                <div class="form-group has-feedback icon-set">                        
<input type="text" name="username" value="" class="form-control" placeholder="Username" />                    <i class="fa fa-user"></i>
                </div>
                <div><label for="password" generated="true" class="error" style="display: none"></label></div>
                <div class="form-group has-feedback icon-set">
<input type="password" name="password" value="" class="form-control" placeholder="Password" />                    <i class="fa fa-lock"></i> 
                    
                </div>
                <div class="row">
                    <div class="col-md-4" style="padding-top: 10px">
                        <label class="">Who You Are?</label>
                    </div>
                    <div class="col-md-8">
                        <select class="form-control" name="type" id="typeidn">
                            <option value="">Please Select Type</option>
                             <option value="admin">Administrator</option> <option value="ts">Technical Support</option> <option value="ext">Executive</option> <option value="rts">Regional Technical Support</option> <option value="ro">Regional Officer</option> <option value="dts">District Technical Support</option> <option value="do">District Officer</option> <option value="fo">Field Officer</option>                        </select>
                    </div>  
                    <div class="col-md-12">
                    <a class="pull-right fp forgetformopen" style="padding-top: 10px" href="#" >I forgot my password</a>
                    </div>
                </div>
                <div class="row">
                   
<!--                    <div class="col-xs-12">
                        
                        <div class="checkbox icheck">
                            <label for="remember">Remember Me</label>                            <input type="checkbox" name="remember" />                            <p>(Keeps you logged in for 2 weeks)</p>
                        </div>
                    </div>-->
                    <!-- /.col -->
                    <div class="col-xs-12">            
<input type="submit" name="login" class="btn btn-primary btn-block btn-act" />                    </div>
                    <!-- /.col -->
                </div>
</form>
            </div>
            <!-- /.login-box-body -->
        </div>
        <!-- /.log
        in-box -->
        
        <!--        acl right div-->
        <div class="modal modal-info fade" id="forgetformmodel">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Request Password Recovery</h4>
                    </div>
                    <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>
                       <form class="ipf-form" action="https://www.aiesmail.com/login/forget" name="forgetform" id="forgetform1" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3" style="padding-top: 10px">
                                        <label class="">Who You Are?</label>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control" name="ftype" id="typeidn">
                                            <option value="">Please Select Type</option>
                                             <option value="admin">Administrator</option> <option value="ts">Technical Support</option> <option value="ext">Executive</option> <option value="rts">Regional Technical Support</option> <option value="ro">Regional Officer</option> <option value="dts">District Technical Support</option> <option value="do">District Officer</option> <option value="fo">Field Officer</option>                                        </select>
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group">   
                                <div class="row"> 
                                    <div class="col-md-3" style="padding-top: 10px"> 
                                        <label>Username</label>
                                    </div>
                                    <div class="col-md-6"> 
                                        <input type="text" name="fusername" class="form-control" id="fusername1" placeholder="Enter Username"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group"> 
                                <div class="row">
                                    <div class="col-md-3" style="padding-top: 10px"> 
                                        <label>Email</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" name="femail" id="femail1" placeholder="Enter Email"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group"> 
                                <div class="row">
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Request</button>
                                    </div>
                                </div>
                            </div>                    
                        </form>
                    </div>  
                </div>   
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <!--New Added-->
        <script src="https://www.aiesmail.com/dist/js/particles.js"></script>
        <script src="https://www.aiesmail.com/dist/js/particles-app.js"></script>
        <!--New Added-->

        <!-- jQuery 2.2.3 -->
        <script src="https://www.aiesmail.com/plugins/jQuery/jquery-2.2.3.min.js"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="https://www.aiesmail.com/bootstrap/js/bootstrap.min.js"></script>
        <!-- iCheck -->
        <script src="https://www.aiesmail.com/plugins/iCheck/icheck.min.js"></script>
        <script src="https://www.aiesmail.com/dist/js/jquery.validate.js"></script>
        <!--for password recover-->
        <script src="https://www.aiesmail.com/plugins/select2/select2.full.min.js"></script>
        <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
        <script>
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' // optional
                });
            });

            jQuery(window).load(function () {
                setTimeout(function () {
                    jQuery("#preloader").hide();
                }, 600);

            });

        </script>
        
        <!--for password recover-->
       
        <script>
                    $(document).ready(function(){
                        $("body").on("click",".forgetformopen",function(){

                             $("#forgetformmodel").modal("show");

                             //appending modal background inside the blue div
                             $('.modal-backdrop').appendTo('.blue');   

                             //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                             $('body').removeClass("modal-open")
                             $('body').css("padding-right","");     
                             setTimeout( function(){ 
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
          }  , 300 );
                     
                     
                         });        
        
        $("#entrypoint").validate({
                  rules:{
                        type:{
                                required:true,
                                check_list:true
                                },
                         username:{
                                required:true,
                                alphanumericspecial: true,
                                minlength: 8,
                                maxlength: 50
                             },
                         password:{
                                required:true,
                                alphanumericspecial: true,
                                minlength: 8,
                                maxlength: 50
                             },                       
                        },
                    messages: {
                        type:{
                                required:"Identity Required",
                            },
                        username:{
                             required:"Enter Username",
                             maxlenght:"Maximum character limit is 20",
                             minlength:"Min character limit is 8"
                            },
                         password:{
                             required:"Enter Password",
                             maxlenght:"Maximum character limit is 20",
                             minlength:"Min character limit is 8"  
                             },                          
                        }                  
                   
                });
        
        $("#forgetform1").validate({
                  rules:{
                        ftype:{
                                required:true,
                                check_list:true
                                },
                         fusername:{
                                required:true,
                                alphanumericspecial: true,
                                minlength: 8,
                                maxlength: 20
                             },
                         femail:{
                                required:true,
                                email: true,
                                minlength: 8,
                                maxlength: 25
                             }                     
                        },
                    messages: {
                        ftype:{
                                required:"Identity Required",
                            },
                        fusername:{
                             required:"Enter Username",
                             maxlenght:"Maximum character limit is 20",
                             minlength:"Min character limit is 8"
                            },
                         femail:{
                             required:"Enter Email",
                             maxlenght:"Maximum character limit is 25",
                             minlength:"Min character limit is 8"  
                             }                      
                        },                  
                   
                });
            
        $.validator.addMethod("check_list",function(sel,element){
            if(sel == "" || sel == 0){
                return false;
             }else{
                return true;
             }
            },"<span>Select One</span>");   

    jQuery.validator.addMethod("alphanumericspecial", function(value, element) {
        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_@*!+-/^]+$/);
        }, "Only letters, Numbers,_,@,*,!,+,-,^ are allowed");   
        
  });

</script>
    </body>
</html>
