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
        Add Email Template
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#"></i> Email Template</a></li>
        <li class="active"></i>Add Email Template</li>
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
                        <h4><i class="icon fa fa-check"></i> Congratulation! E-Mail Template Successfully Added.</h4>
                    </div>
                    <?php } ?>
                     <?php if(isset($_GET["message"]) && $_GET["message"] == 2)
                    {                  
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> Congratulation! E-Mail Template Successfully Updated.</h4>
                    </div>
                    <?php } ?>
                    <form class="" name="email_template" action="<?php echo url::site() . 'emailtemplate/post' ?>" id="email_template" method="post">
                        <div class="box-body">
                            <!-- operating company names -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="company" >Company</label>
                                    <?php try{
                                    $comp = Helpers_Utilities::get_companies_data();
                                    ?> 
                                    <select class="form-control" name="company_name" id="company">
                                        <option value="">Please Select Company Name </option>
                                        <?php foreach ($comp as $com) { ?>
                                        <option <?php echo (!empty($results['company_id']) && $results['company_id'] == $com->mnc)? 'Selected':''; ?>
                                            value="<?php echo $com->mnc; ?>"><?php echo $com->company_name; ?></option>
                                        <?php } 
                                        }  catch (Exception $ex){   }?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">                                                            
                                    <label for="ChooseTemplate" class="control-label">Request Type</label> 
                                    <?php try{
                                    $rqts = Helpers_Utilities::emailtemplatetype();
                                    ?>
                                    <select class="form-control" name="email_type_name" id="email_type_name">
                                        <option value="">Please select Request Type</option>
                                        <?php foreach ($rqts as $rqt) { ?>
                                            <option <?php echo (!empty($results['email_type']) && $results['email_type'] == $rqt['id'])? 'Selected':''; ?>
                                                value="<?php echo $rqt['id']; ?>"><?php echo $rqt['email_type_name']; ?></option>
                                        <?php } 
                                        }  catch (Exception $ex){   }?>                                                                                                                
                                    </select>                                                                                    
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <!-- email subject -->
                                <div class="form-group">
                                    <label for="emailsubject" >Email Subject</label>
                                    <input type="text" name="subject" id="emailsubject" value="<?php echo (!empty($results['subject']))? $results['subject']:''; ?>" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <!-- email format -->
                                <div class="form-group"> 
                                    <label for="emailformat" >Email Format</label>  
                                    <div class="box">
                                        <div class="box-header">
                                            <h3 class="box-title">E-Mail Body
                                            </h3>
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body pad">                                             
                                            <textarea id="body_txt" value=""  name="body" class="textarea form-control" placeholder="Please enter template format heare with the help of tokens" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo (!empty($results['body_txt']))? $results['body_txt']:''; ?></textarea>                                        
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group"> 
                                    <label for="emailtokens" >Email Tokens</label>
                                    <div class="box">
                                        <div class="box-header">
                                            <h3 class="box-title">Available Tokens</h3>
                                            <br>
                                            <small>(Add from this list)</small>
                                        </div>
                                        <?php try{
                                        $token = Helpers_Utilities::get_token_name();
                                        ?>
                                        <div class="box-body">                                            
                                            <select onClick="addText()" multiple class="col-md-12" name="token_list" id="token_list" >
                                                <?php foreach ($token as $tkn) { ?>
                                                    <option class="fa  fa-arrow-left" value="<?php echo $tkn->token; ?>"><?php echo '&nbsp;&nbsp;' . $tkn->token_name; ?></option>
                                                <?php }
                                                }  catch (Exception $ex){   }?>                                                
                                            </select>
                                        </div>
                                    </div>
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
        // Optional: If you still have any old WYSIHTML5 code commented out, you can remove it.
        // $(".textarea").wysihtml5();  // ← safe to delete if not used

        CKEDITOR.replace('body_txt', {
            versionCheck: false,  // This ensures it's disabled for this instance (extra safety)

            // If you want, you can move some/all of your toolbar/custom settings here instead of config.js
            // (but since you already have them in config.js, no need unless overriding per-instance)
            toolbarGroups: [
                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'forms' },
                { name: 'tools' },
                { name: 'document',       groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'others' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
                { name: 'styles' },
                { name: 'colors' },
                { name: 'about' }
            ],
            removeButtons: 'Underline,Subscript,Superscript',
            format_tags: 'p;h1;h2;h3;pre',
            removeDialogTabs: 'image:advanced;link:advanced'
            // Add any other per-instance overrides if needed
        });

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
<script type="text/javascript">
    $(document).ready(function(){
        $("#email_template").validate({
                  rules:{
                        company_name:{
                                required:true,
                                check_list:true
                                },
                        email_type_name:{
                                required:true,
                                check_list:true
                                }, 
                        subject:{
                                required:true,
                               // alphanumericspecial: true,
                                minlength: 1,
                                maxlength: 50
                            },                             
                        body:{
                                required:true,
                                //alphanumericspecial: true,
                                minlength: 1,
                                maxlength: 50
                            }, 
                        },
                    messages: {
                        company_name:{
                                required:"Please Select Company",
                            },
                        email_type_name:{
                                required:"Please Select Type",
                            },
                        subject:{
                                required:"Please Enter Subject",
                                maxlenght:"Maximum character limit is 50",
                                minlength:"Min character limit is 1"
                            }, 
                            
                        body:{
                                required:"Please Enter Body",
                                maxlenght:"Maximum character limit is 50",
                                minlength:"Min character limit is 1"
                            },
                        }    
                                
        });                
     
//    jQuery.validator.addMethod("alphanumericspecial", function(value, element) {
//        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);
//        }, "Only letters, Numbers & Space/underscore Allowed.");                        
    
      $.validator.addMethod("check_list",function(sel,element){
            if(sel == "" || sel == 0){
                return false;
             }else{
                return true;
             }
            },"<span>Select One</span>");           
      
 
           });
</script>