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
        Add Short Code
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#"></i> Short Code</a></li>
        <li class="active"></i>Add Short Code</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="">
                <div class="box box-primary">  
                    <?php if(isset($_GET["message"]) && $_GET["message"] == 1)
                    {                  
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> Congratulation! Short Code Successfully Added.</h4>
                    </div>
                    <?php } ?>
                     <?php if(isset($_GET["message"]) && $_GET["message"] == 2)
                    {                  
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> Congratulation! Short Code Successfully Updated.</h4>
                    </div>
                    <?php } ?>
                    <form class="" name="short_code" action="<?php echo url::site() . 'shortcode/post' ?>" id="short_code" method="post">
                        <div class="box-body">
                            <!-- operating company names -->


                            <div class="col-sm-6">
                                <!-- email subject -->
                                <div class="form-group">
                                    <label for="emailsubject" >Company Name</label>
                                    <input type="text" name="company_name" id="company_name" value="<?php echo (!empty($results['company_name']))? $results['company_name']:''; ?>"  class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <!-- email subject -->
                                <div class="form-group">
                                    <label for="emailsubject" >Code</label>
                                    <input type="text" name="code" id="code"  value="<?php echo (!empty($results['code']))? $results['code']:''; ?>" class="form-control" />
                                </div>
                            </div>


                        </div>
                        <!-- </box-body> -->
                        <!-- form buttons -->
                        <div class="box-footer">
                            <div class="col-sm-12">	
                                <button id="clear" class="btn btn-success">Clear</button>
                                <input type="hidden" value="<?php echo (!empty($results['id']))? $results['id']:''; ?>" name="id" id="id" />
                                <input type="submit" value="Submit" class="btn btn-primary" />
                            </div>
                        </div>
                    </form> 
                </div>
            </div>
        </div>

    </div>
</section>
<!-- /.content -->
<!-- Bootstrap WYSIHTML5 -->
<!--<script src="<?php echo URL::base(); ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>-->
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>

<script>
                                                $(function () {
                                                    //bootstrap WYSIHTML5 - text editor
                                                    //$(".textarea").wysihtml5();
                                                    CKEDITOR.replace('body_txt');
                                                    CKEDITOR.disableAutoInline = false;
                                                });
</script>

<script type="text/javascript">
    function addText() {
        var content = CKEDITOR.instances.body_txt.getData()
        content += ' ' + $('#token_list option:selected').val();
        CKEDITOR.instances.body_txt.setData(content)
        console.log(content);
    }
</script>
<!--<script type="text/javascript">-->
<!--    $(document).ready(function(){-->
<!--        $("#email_template").validate({-->
<!--                  rules:{-->
<!--                        company_name:{-->
<!--                                required:true,-->
<!--                                check_list:true-->
<!--                                },-->
<!--                        email_type_name:{-->
<!--                                required:true,-->
<!--                                check_list:true-->
<!--                                }, -->
<!--                        subject:{-->
<!--                                required:true,-->
<!--                               // alphanumericspecial: true,-->
<!--                                minlength: 1,-->
<!--                                maxlength: 50-->
<!--                            },                             -->
<!--                        body:{-->
<!--                                required:true,-->
<!--                                //alphanumericspecial: true,-->
<!--                                minlength: 1,-->
<!--                                maxlength: 50-->
<!--                            }, -->
<!--                        },-->
<!--                    messages: {-->
<!--                        company_name:{-->
<!--                                required:"Please Select Company",-->
<!--                            },-->
<!--                        email_type_name:{-->
<!--                                required:"Please Select Type",-->
<!--                            },-->
<!--                        subject:{-->
<!--                                required:"Please Enter Subject",-->
<!--                                maxlenght:"Maximum character limit is 50",-->
<!--                                minlength:"Min character limit is 1"-->
<!--                            }, -->
<!--                            -->
<!--                        body:{-->
<!--                                required:"Please Enter Body",-->
<!--                                maxlenght:"Maximum character limit is 50",-->
<!--                                minlength:"Min character limit is 1"-->
<!--                            },-->
<!--                        }    -->
<!--                                -->
<!--        });                -->
<!--     -->
<!--//    jQuery.validator.addMethod("alphanumericspecial", function(value, element) {-->
<!--//        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);-->
<!--//        }, "Only letters, Numbers & Space/underscore Allowed.");                        -->
<!--    -->
<!--      $.validator.addMethod("check_list",function(sel,element){-->
<!--            if(sel == "" || sel == 0){-->
<!--                return false;-->
<!--             }else{-->
<!--                return true;-->
<!--             }-->
<!--            },"<span>Select One</span>");           -->
<!--      -->
<!-- -->
<!--           });-->
<!--</script>-->