
<html lang="en" class="cookie_used_true js pc chrome68 gr__codepen_io"><!--<![endif]--><head>
  <meta charset="UTF-8">
  <title>Error Page 404  SMART</title>
   <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php echo URL::base(); ?>bootstrap/css/bootstrap.min.css">
  <!-- jQuery 2.2.3 -->
        <script src="<?php echo URL::base(); ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
        <!-- Bootstrap 3.3.6 -->
        <script src="<?php echo URL::base(); ?>bootstrap/js/bootstrap.min.js"></script>
  <script src="<?php echo URL::base(); ?>dist/js/jquery.validate.js"></script>
  <style>
      @import url(https://fonts.googleapis.com/css?family=opensans:500);
body{
                background: #33cc99;
                color:#fff;
                font-family: 'Open Sans', sans-serif;
                max-height:700px;
                overflow: hidden;
            }
            .c{
                text-align: center;
                display: block;
                position: relative;
                width:80%;
                margin:100px auto;
            }
            ._404{
                font-size: 220px;
                position: relative;
                display: inline-block;
                z-index: 2;
                height: 250px;
                letter-spacing: 15px;
            }
            ._1{
                text-align:center;
                display:block;
                position:relative;
                letter-spacing: 12px;
                font-size: 4em;
                line-height: 80%;
            }
            ._2{
                text-align:center;
                display:block;
                position: relative;
                font-size: 20px;
            }
            .text{
                font-size: 70px;
                text-align: center;
                position: relative;
                display: inline-block;
                margin: 19px 0px 0px 0px;
                /* top: 256.301px; */
                z-index: 3;
                width: 100%;
                line-height: 1.2em;
                display: inline-block;
            }
           

            .btn{
                background-color: rgb( 255, 255, 255 );
                position: relative;
                display: inline-block;
                width: 358px;
                padding: 5px;
                z-index: 5;
                font-size: 25px;
                margin:0 auto;
                color:#33cc99;
                text-decoration: none;
                margin-right: 10px
            }
            .right{
                float:right;
                width:60%;
            }
            
            hr{
                padding: 0;
                border: none;
                border-top: 5px solid #fff;
                color: #fff;
                text-align: center;
                margin: 0px auto;
                width: 420px;
                height:10px;
                z-index: -10;
            }
            
            hr:after {
                content: "\2022";
                display: inline-block;
                position: relative;
                top: -0.75em;
                font-size: 2em;
                padding: 0 0.2em;
                background: #33cc99;
            }
            
            .cloud {
                width: 350px; height: 120px;

                background: #FFF;
                background: linear-gradient(top, #FFF 100%);
                background: -webkit-linear-gradient(top, #FFF 100%);
                background: -moz-linear-gradient(top, #FFF 100%);
                background: -ms-linear-gradient(top, #FFF 100%);
                background: -o-linear-gradient(top, #FFF 100%);

                border-radius: 100px;
                -webkit-border-radius: 100px;
                -moz-border-radius: 100px;

                position: absolute;
                margin: 120px auto 20px;
                z-index:-1;
                transition: ease 1s;
            }

            .cloud:after, .cloud:before {
                content: '';
                position: absolute;
                background: #FFF;
                z-index: -1
            }

            .cloud:after {
                width: 100px; height: 100px;
                top: -50px; left: 50px;

                border-radius: 100px;
                -webkit-border-radius: 100px;
                -moz-border-radius: 100px;
            }

            .cloud:before {
                width: 180px; height: 180px;
                top: -90px; right: 50px;

                border-radius: 200px;
                -webkit-border-radius: 200px;
                -moz-border-radius: 200px;
            }
            
            .x1 {
                top:-50px;
                left:100px;
                -webkit-transform: scale(0.3);
                -moz-transform: scale(0.3);
                transform: scale(0.3);
                opacity: 0.9;
                -webkit-animation: moveclouds 15s linear infinite;
                -moz-animation: moveclouds 15s linear infinite;
                -o-animation: moveclouds 15s linear infinite;
            }
            
            .x1_5{
                top:-80px;
                left:250px;
                -webkit-transform: scale(0.3);
                -moz-transform: scale(0.3);
                transform: scale(0.3);
                -webkit-animation: moveclouds 17s linear infinite;
                -moz-animation: moveclouds 17s linear infinite;
                -o-animation: moveclouds 17s linear infinite; 
            }

            .x2 {
                left: 250px;
                top:30px;
                -webkit-transform: scale(0.6);
                -moz-transform: scale(0.6);
                transform: scale(0.6);
                opacity: 0.6; 
                -webkit-animation: moveclouds 25s linear infinite;
                -moz-animation: moveclouds 25s linear infinite;
                -o-animation: moveclouds 25s linear infinite;
            }

            .x3 {
                left: 250px; bottom: -70px;

                -webkit-transform: scale(0.6);
                -moz-transform: scale(0.6);
                transform: scale(0.6);
                opacity: 0.8; 

                -webkit-animation: moveclouds 25s linear infinite;
                -moz-animation: moveclouds 25s linear infinite;
                -o-animation: moveclouds 25s linear infinite;
            }

            .x4 {
                left: 470px; botttom: 20px;

                -webkit-transform: scale(0.75);
                -moz-transform: scale(0.75);
                transform: scale(0.75);
                opacity: 0.75;

                -webkit-animation: moveclouds 18s linear infinite;
                -moz-animation: moveclouds 18s linear infinite;
                -o-animation: moveclouds 18s linear infinite;
            }

            .x5 {
                left: 200px; top: 300px;

                -webkit-transform: scale(0.5);
                -moz-transform: scale(0.5);
                transform: scale(0.5);
                opacity: 0.8; 

                -webkit-animation: moveclouds 20s linear infinite;
                -moz-animation: moveclouds 20s linear infinite;
                -o-animation: moveclouds 20s linear infinite;
            }

            @-webkit-keyframes moveclouds {
                0% {margin-left: 1000px;}
                100% {margin-left: -1000px;}
            }
            @-moz-keyframes moveclouds {
                0% {margin-left: 1000px;}
                100% {margin-left: -1000px;}
            }
            @-o-keyframes moveclouds {
                0% {margin-left: 1000px;}
                100% {margin-left: -1000px;}
            }
  </style>
</head>

<body class="room-editor editor state-htmlOn-cssOn-jsOn   layout-top     logged-out" data-gr-c-s-loaded="true">
 <div id="clouds">
            <div class="cloud x1"></div>
            <div class="cloud x1_5"></div>
            <div class="cloud x2"></div>
            <div class="cloud x3"></div>
            <div class="cloud x4"></div>
            <div class="cloud x5"></div>
        </div>
        <div class='c'>
            <div class='_404'>909</div>
            <hr>
            <div class='_1'><?php echo !empty($_GET['msg'])?$_GET['msg']:'Something Went Wrong'; ?></div>
            <br>
            <div class='_2' style="margin-bottom: 10px">Please try again</div>
            <hr>
            <a class='btn' href='http://www.smart.ctdpunjab.com'>BACK TO SMART</a>
            <!--<a class="btn md-trigger" data-toggle="modal" data-target="#subscribeModal"> Forget AIES Password</a>-->            
            
        </div>
    


    
  <div class="modal fade text-center py-5"  id="subscribeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-body">
				<div class="top-strip"></div>
                <a class="h2" target="_blank">Forget Password</a>                
                <hr class="hr"/>
                <!--<p class="pb-1 text-muted"><small>Sign up to update with our latest news and products.</small></p>-->
                
                <form class="ipf-form" action="<?php echo url::site().'login/forget'?>" name="forgetform" id="forgetform1" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-3" style="padding-top: 10px">
                                        <!--<label class="">Who You Are?</label>-->
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control" name="ftype" id="typeidn">
                                            <option value="">Please Select Type</option>
                                            <?php
                                            $roles = Helpers_Utilities::get_roles_data();
                                            foreach ($roles as $role) {

                                                echo ' <option value="' . $role->id . '">' . $role->label . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div> 
                                </div>
                            </div>
                    <!--<span class="withoutline"></span>-->
                            <div class="form-group">   
                                <div class="row"> 
                                    <div class="col-md-3" style="padding-top: 10px"> 
                                        <!--<label>Username</label>-->
                                    </div>
                                    <div class="col-md-6"> 
                                        <input type="text" name="fusername" class="form-control" id="fusername1" placeholder="Enter Username"/>
                                    </div>
                                </div>
                            </div>
                    <!--<span class="withoutline"></span>-->
                            <div class="form-group"> 
                                <div class="row">
                                    <div class="col-md-3" style="padding-top: 10px"> 
                                        <!--<label>Email</label>-->
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" name="femail" id="femail1" placeholder="Enter Email"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group"> 
                                <div class="row">
                                    <div class="modal-footer">
                                        <!--<button type="button" class="btn btn-default pull-left" id="button-addon2" data-dismiss="modal">Close</button>-->
                                        <button type="submit" class="btn btn-primary bttn">Request</button>                                        
                                    </div>
                                </div>
                            </div>                    
                        </form>
                <!--<p class="pb-1 text-muted"><small>Your email is safe with us. We won't spam.</small></p>-->
				<div class="bottom-strip"></div>
            </div>
        </div>
    </div>
</div>

...

<div class="md-overlay"></div>
<style>
    label.error {
        color: #943636;
    }
   #subscribeModal .modal-content{
	overflow:hidden;
}
a.h2{
    color:#007b5e;
    margin-bottom:0;
    text-decoration:none;
}
#subscribeModal .form-control {
    height: 56px;
    border-top-left-radius: 30px;
    border-bottom-left-radius: 30px;
	padding-left:30px;
}
#subscribeModal .btn {
    border-top-right-radius: 30px;
    border-bottom-right-radius: 30px;
	padding-right:20px;
	background:#007b5e;
	border-color:#007b5e;
}
#subscribeModal .form-control:focus {
    color: #495057;
    background-color: #fff;
    border-color: #007b5e;
    outline: 0;
    box-shadow: none;
}
#subscribeModal .top-strip{
	height: 155px;
    background: #007b5e;
    transform: rotate(141deg);
    margin-top: -94px;
    margin-right: 190px;
    margin-left: -130px;
    border-bottom: 65px solid #4CAF50;
    border-top: 10px solid #4caf50;
}
#subscribeModal .bottom-strip{
	height: 155px;
    background: #007b5e;
    transform: rotate(112deg);
    margin-top: -110px;
    margin-right: -215px;
    margin-left: 300px;
    border-bottom: 65px solid #4CAF50;
    border-top: 10px solid #4caf50;
}

/**************************/
/****** modal-lg stips *********/
/**************************/
#subscribeModal .modal-lg .top-strip {
    height: 155px;
    background: #007b5e;
    transform: rotate(141deg);
    margin-top: -106px;
    margin-right: 457px;
    margin-left: -130px;
    border-bottom: 65px solid #4CAF50;
    border-top: 10px solid #4caf50;
}
#subscribeModal .modal-lg .bottom-strip {
    height: 155px;
    background: #007b5e;
    transform: rotate(135deg);
    margin-top: -115px;
    margin-right: -339px;
    margin-left: 421px;
    border-bottom: 65px solid #4CAF50;
    border-top: 10px solid #4caf50;
}

/****** extra *******/
#Reloadpage{
    cursor:pointer;
}
.hr{
   padding-top: 12px;
    border: none;
    border-top: 3px solid #4caf50;
    color: #fff;
    text-align: center;
    margin: 0px auto;
    width: 420px;
    height: 10px;
    z-index: -10;
}
.hr:after { 
    background: #4caf50;
}
.bttn {
    width: 162px;
    left: -140px;
}
.withoutline:after {
    content: "\2022";
    display: inline-block;
    position: relative;
    top: -7px;
    font-size: 2em;
    padding: 0px 0.2em;
    background: #4caf50;
    margin: -9px;
    z-index: 0;
}
</style>

<script>
$(document).ready(function(){        
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
                                maxlength: 50
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
                             maxlenght:"Maximum character limit is 50",
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


