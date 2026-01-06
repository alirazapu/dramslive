<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>	<i class="fa fa-envelope" aria-hidden="true"></i>
        System Interaction
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a class="active" ></i> System Interaction</a></li>
    </ol>
</section>
<!-- Main content -->
<?php if (!empty($uid) && ($uid == 842 || $uid == 137 || $uid == 2031)) { ?>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="">
                        <div class="box box-primary">  
                            <?php
                            if (!empty($post["message"]) ) {
                            ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> Congratulation! Query Successfully Run.</h4>
                        </div>
                                    <?php } ?>
                                <form class="" name="interaction" action="#" id="interaction" method="post">
                                    <div class="box-body"> 
                                        <div class="col-sm-12">
                                            <!-- email format -->
                                    <div class="form-group"> 
                                        <label for="body" >PHP Query</label>  
                                        <div class="box">
                                            <!-- /.box-header -->
                                            <div class="box-body pad">                                             
                                                <textarea id="body" value=""  name="body" class="textarea form-control" placeholder="Query Command !" style="width: 100%; height: 150px; font-size: 16px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;background-color: black;color: white"><?php if(!empty($post['body'])){     print_r($post['body']); } ?></textarea>                                        
                                            </div>
                                        </div>
                                    </div>   
                                </div>                              
                                <div class="col-sm-12">
                                    <div class="form-group">
                                            <div class="col-sm-4">                                            
                                                <label for="type" class="control-label">Query Type </label> 
                                                <select class="form-control" data-placeholder=""  name="type" id="type"  style="width: 100%;">                                        
                                                    <option  value="" >Please Select Option</option>
                                                    <option  value="1" >Select Query</option>
                                                    <option  value="2" >Insert Query</option>
                                                    <option  value="3" >Update Query</option>
                                                    <option  value="4" >Delete Query</option>                                                                                                                                                     
                                                </select> 
                                            </div>                                        
                                            <div class="col-sm-4">                                            
                                                <label for="confirmation" >Confirmation Code</label>
<!--                                                onkeypress="encryptconf(this)"-->
                                                <input type="password"  name="confirmation" id="confirmation" placeholder="Enter Confirmation Code" value="" class="form-control" />
                                            </div>                                        
                                            <div class="col-sm-4 " style="margin-top: 25px" >
                                                <input type="hidden" value="ok" name="run" id="run" />
                                                <input type="submit" value="Submit" class="btn btn-primary pull-right" />
                                                <input type="button"  onclick="formclean()" class="btn btn-success pull-right" style="margin-right: 5px" value="Clear"/>
                                            </div>
                                        </div>
                                    </div>  
                                <div class="col-sm-12"  style="display: <?php if(!empty($post['response'])){ echo "block"; }else{ echo "none"; } ?>" id="response_div">
                                        <div class="form-group"> 
                                        <label for="response" >Response</label>  
                                        <div class="box">
                                            <!-- /.box-header -->
                                            <div class="box-body pad">                                             
                                                <textarea readonly="" id="response" value=""  name="response" class="textarea form-control" placeholder="Your Response" style="width: 100%; height: 150px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php if(!empty($post['response'])){     print_r($post['response']); } ?></textarea>                                        
                                            </div>
                                        </div>
                                    </div>                                
                                </div> 
                            </div>
                            
                        </form> 
                    </div>
                </div>
            </div>

        </div>
    </section>
<?php } ?> 
<!-- /.content -->

<script type="text/javascript">
    $(document).ready(function () {
        $("#interaction").validate({
            rules: {
                confirmation: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 5,
                    maxlength: 20,
                },
                body: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 10,
                    maxlength: 200,
                },
                type: {
                    required: true,
                    check_list: true
                },
            },
            messages: {
                confirmation: {
                    required: "Please Enter Code",
                    maxlenght: "Maximum character limit is 20",
                    minlength: "Min character limit is 5"
                },
                body: {
                    required: "Please Enter Details",
                    maxlenght: "Maximum character limit is 200",
                    minlength: "Min character limit is 10",
                },
                 type: {
                    required: "Please Select Type",
                },
            }

        });
        

        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
           // alert(value);
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_=$% *.,()';'""]+$/);
        }, "Only letters, Numbers Space, Single Quote, Double Quote AND ,.$%*= Allowed");
        
        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "") {
                return false;
            } else {
                return true;
            }
        }, "<span>Select One</span>");
        
    });

    // function to clear form
    function formclean() {
        var ab = '';
        $("#body").val(ab);
        $("#confirmation").val(ab);
        $("#response").val(ab);
    }
    // encryption
//    function encryptconf(pass) {
//        var strMD5 = $().crypt({
//                method: "md5",
//                source: pass
//            });
//        $("#confirmation").val(strMD5);
//        
//        alert(strMD5); 
//    }
    
</script>
