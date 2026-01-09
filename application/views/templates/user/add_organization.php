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
        Add/Edit Organization
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#"></i> Organization</a></li>
        <li class="active"></i>Add/Edit Organization</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="">
                <div class="box box-primary">  
                    <div class="alert alert-warning">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> Alert! Organization Add is not currently available. Contact Support Team.</h4>
                        </div>
                        <?php
                        if (isset($_GET["message"]) && $_GET["message"] == 1) {
                            ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> Congratulation! Organization Is Successfully Added.</h4>
                        </div>
                                    <?php } ?>
                                    <?php
                                    if (isset($_GET["message"]) && $_GET["message"] == 2) {
                                        ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> Congratulation! Organization Is Successfully Updated.</h4>
                        </div>
                                <?php } ?>
                    <form class="" name="addprojectform" action="<?php echo url::site() . 'organization/post' ?>" id="addprojectform" method="post">
                        <div class="box-body">
                                    <?php if (!empty($records)) { ?>
                                        <?php
                                        foreach ($records as $record) {                                            
                                            $org_id = !empty($record['org_id']) ? $record['org_id'] : 0;
                                            $oname = !empty($record['org_name']) ? $record['org_name'] : "NA";
                                            $notification = !empty($record['notification_no']) ? $record['notification_no'] : "NA";
                                            $org_acr = !empty($record['org_acronym']) ? $record['org_acronym'] : "NA";
                                            $drive_id = !empty($record['drived_from_id']) ? $record['drived_from_id'] : "NA";
                                        }
                                    } else {
                                        $org_id = '';
                                        $oname = '';
                                        $notification = '';
                                        $org_acr = '';
                                        $drive_id = '';
                                    }
                                    ?>  
                            <div class="col-sm-4">
                                <!-- email subject -->
                                <div class="form-group">
                                    <label for="organizationname" >Organization Name</label>
                                    <input type="text" name="organizationname" id="organizationname" placeholder="Organization Name" value="<?php echo $oname; ?>" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <!-- email subject -->
                                <div class="form-group">
                                    <label for="projectname" >Organization Acronym</label>
                                    <input type="text" name="org_acr" id="projectname" placeholder="Organization Acronym" value="<?php echo $org_acr; ?>" class="form-control" />
                                </div>
                            </div>
                            <!-- operating company names -->

                            <div class="col-sm-4">
                                <div class="form-group">                                                            
                                    <label for="porganization" class="control-label">Drive Organization </label> 
                                        <?php try{
                                        $rqts = Model_Intprojects::get_banned_org_list();
                                        ?>
                                    <select <?php if (!empty($drive_id)) {
                                        echo 'readonly';
                                    } ?> class="form-control select2" name="porganization" id="porganization">
                                        <option value="">Please select organization name</option>
                                        <?php foreach ($rqts as $rqt) { ?>
                                            <option <?php if ($drive_id == $rqt['org_id']) {
                                            echo 'Selected';
                                        } ?>
                                                value="<?php echo $rqt['org_id']; ?>"><?php echo $rqt['org_name']; ?></option>
                                        <?php }
                                        }  catch (Exception $ex){   }?>                                                                                                                
                                    </select>                                                                                    
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <!-- email format -->
                                <div class="form-group"> 
                                    <label for="projectdetails" >Notification No</label>  
                                    <div class="box">
                                        <!-- /.box-header -->
                                        <div class="box-body pad">                                             
                                            <textarea id="projectdetails" value=""  name="notification" class="textarea form-control" placeholder="Please enter notification details" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo $notification; ?></textarea>                                        
                                        </div>
                                    </div>
                                </div>                                
                            </div>

                        </div>
                        <!-- </box-body> -->
                        <!-- form buttons -->
                        <div class="box-footer">
                            <div class="col-sm-12">	
                                <input type="button"  onclick="clearform();" class="btn btn-success" value="Clear"/>
                                <input type="hidden" value="<?php echo $org_id; ?>" name="id" id="id" />
                                <input disabled="disabled" type="submit" value="Submit" class="btn btn-primary" />
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
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        <?php if(!empty($drive_id) && $drive_id == 'NA'){ ?>
            $('select').select2().select2('val','<?php echo $drive_id; ?>')
        <?php } ?>

    });
    $("#addprojectform").validate({
            rules: {
                projectname: {
                    required: true,
                    // alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 50,
                },
                porganization: {
                    required: true,
                    check_list: true,
                },
                projectdetails: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 900,
                },
            },
            messages: {
                projectname: {
                    required: "Please Enter Name",
                    maxlenght: "Maximum character limit is 50",
                    minlength: "Min character limit is 1"
                },
                porganization: {
                    required: "Please Select Organization",
                },
                projectdetails: {
                    required: "Please Enter Details",
                    maxlenght: "Maximum character limit is 900",
                    minlength: "Min character limit is 1",
                },
            }

        });

        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            //alert(value);
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);
        }, "Only letters, Numbers & Space Allowed.");
        
        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "") {
                return false;
            } else {
                return true;
            }
        }, "<span>Select One</span>");
    // function to clear form
    function clearform() {
        var ab = '';
        $("#organizationname").val(ab);
        $("#projectname").val(ab);
        jQuery("#porganization").select2().select2('val', ['']);
        $("#projectdetails").val(ab);
    }
</script>
<script src="<?php echo URL::base() . 'dist/js/typeahead.js'; ?>"></script>
 <script>
    $(document).ready(function () {
        $('#organizationname').typeahead({
            source: function (query, result) {
                $.ajax({
                    url: "<?php echo URL::base() . "organization/organization_name/"; ?>",
		    data: 'query=' + query,            
                    dataType: "json",
                    type: "POST",
                    success: function (data) {
                        if(data== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                                result($.map(data, function (item) {
                                return item;
                        }));
                    }
                });
            }
        });
    });
</script>