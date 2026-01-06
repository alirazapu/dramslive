<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



if (!empty($record)) {
    $record_id = !empty($record['id']) ? $record['id'] : 0;
    // $person_id = !empty($record['person_id']) ? $record['person_id'] : '';
    $sw_type_id = !empty($record['sw_type_id']) ? $record['sw_type_id'] : '';
    $person_sw_id = !empty($record['person_sw_id']) ? $record['person_sw_id'] : '';
    $sw_profile_link = !empty($record['sw_profile_link']) ? $record['sw_profile_link'] : '';
    $phone_number = !empty($record['phone_number']) ? $record['phone_number'] : '';
    $information = !empty($record['information']) ? $record['information'] : '';
    $file_link = !empty($record['file_link']) ? $record['file_link'] : '';
} else {
    $record_id = '';
    $sw_type_id = '';
    $person_sw_id = '';
    $sw_profile_link = '';
    $phone_number = '';
    $information = '';
    $file_link = '';
}
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Person's Portal
        <small><?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/=?id=' . $_GET['id']); ?>">Person Dashboard </a></li>                        
        <li><a href="<?php echo URL::site('socialanalysis/social_links/?id=' . $_GET['id']); ?>">Social Analysis</a></li>
        <li class="active">Add Social Link</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="">
                <div class="box box-primary">                    


                    <div class="box-header">
                        <span>
                            <h3 class="box-title"><i class="fa fa-plus-square"></i>
                                <?php if (!empty($record)) {
                                    echo "Update Social Link";
                                } else {
                                    echo "Add New Social Link";
                                } ?> 
                            </h3>
                        </span>
                    </div>
                    <?php
                    if (isset($_GET["message"]) && $_GET["message"] == 1) {
                        echo '<script> sociallinkadded(); function sociallinkadded(){swal("Congratulations!", "Person Social Link Record Added Successfully.", "success");};</script>';
                    }
                    ?>                    
                    <?php
                    if (isset($_GET["message"]) && $_GET["message"] == 2) {
                        echo '<script> sociallinkadded(); function sociallinkadded(){swal("Congratulations!", "Person Social Link Updated Successfully.", "success");};</script>';
                    }
                    ?>
                    <?php
                    if (isset($_GET["message"]) && $_GET["message"] == 9) {
                        echo '<script> sociallinkadded(); function sociallinkadded(){swal("System Error", "Contact Support Team.", "error");};</script>';
                    }
                    ?>
                    <form class="" name="sociallinkform" action="<?php echo url::site() . 'socialanalysis/updatelink' ?>" id="sociallinkform" method="post" enctype="multipart/form-data">
                        <div class="box-body">
                            <!-- social websites names -->                            
                            <div class="col-sm-6">
                                <div class="form-group">                                                            
                                    <label for="socialwebsite" class="control-label">Social Website </label> 
                                        <?php
                                        try {
                                            $rqts = Model_Socialanalysis::get_social_website_list();
                                            ?>
                                        <select <?php if (!empty($sw_type_id)) {
                                            echo 'disabled';
                                        } ?>  class="form-control select2" name="socialwebsite" id="socialwebsite">
                                            <option value="">Please select website</option>
                                                <?php foreach ($rqts as $rqt) {
                                                    ?>
                                                <option <?php
                                                    if ($sw_type_id == $rqt['id']) {
                                                        echo 'Selected';
                                                    }
                                                    ?>
                                                    value="<?php echo $rqt['id']; ?>"><?php echo $rqt['website_name']; ?></option>
                                                <?php
                                                }
                                            } catch (Exception $ex) {

                                            }
                                            ?>                                                                                                                
                                    </select>                                                                                    
                                </div>
                            </div>
                            <div class="form-group col-md-6" >
                                <label title="Enter Person Website ID Like:Email, Phone#,Username"  for="person_sw_id">Person Social Website ID</label>
                                <input title="Enter Person Website ID Like:Email, Phone#,Username" type="text" class="form-control" id="person_sw_id" name="person_sw_id" value="<?php echo $person_sw_id; ?>" placeholder="Enter Website ID">
                            </div>
                            <div class="form-group col-md-6" >
                                <label for="sw_profile_link" title="Person Profile Link On Website">Website Profile Link</label>
                                <input type="text" class="form-control" id="sw_profile_link" name="sw_profile_link" value="<?php echo $sw_profile_link; ?>" placeholder="Enter Profile Link">
                            </div>
                            <div class="form-group col-md-6" >
                                <label for="phone_number" title="Phone Number Linked With Website">Mobile Number</label>
                                <input title="Phone Number Linked With Website" type="text" class="form-control" id="phone_number" value="<?php echo $phone_number; ?>" name="phone_number" placeholder="Enter Phone Number">
                            </div>                           
                            <div class="form-group col-md-6"> 
                                <label title="File contains more information about this link" for="personfile">Upload File <small>(jpg,gif,png,doc,xls,docx,inf,inp and pdf files)</small></label>
                                <input title="File contains more information about this link" type="file" accept=".jpg,.gif,.png,.doc,.docx,.inf,.pdf,.inp" id="personfile" name="personfile" placeholder="Select Linked File">  
                            </div> 
                            <div cla
                                 <div class="col-sm-12">
                                    <!-- email format -->
                                    <div class="form-group"> 
                                        <label for="information" >Other Information</label>  
                                        <div class="box">
                                            <!-- /.box-header -->
                                            <div class="box-body pad">                                             
                                                <textarea id="information" value=""  name="information" class="textarea form-control" placeholder="Please enter information" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo $information; ?></textarea>                                        
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
                                    <input type="hidden" value="<?php echo $record_id; ?>" name="record_id" id="record_id" />
                                    <input type="hidden" value="<?php echo $person_id; ?>" name="person_id" id="person_id" />
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
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
                                        $(function () {
                                            //Initialize Select2 Elements
                                            $(".select2").select2();
                                        });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $("#sociallinkform").validate({
            rules: {
                socialwebsite: {
                    required: true,
                    check_list: true
                },
                person_sw_id: {
                    required: true,
                    minlength: 1,
                    maxlength: 50
                },
                sw_profile_link: {
                    minlength: 1,
                    maxlength: 150
                },
                phone_number: {
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                },
                personfile: {
                    accept: "jpg,jpeg,gif,png,doc,docx,inf,pdf,inp",
                    filesize: 900000,
                },
                information: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 900
                },
            },
            messages: {
                socialwebsite: {
                    required: "Please Select Website",
                },
                person_sw_id: {
                    required: "Please Enter Social Website ID",
                    maxlenght: "Maximum character limit is 50",
                    minlength: "Min character limit is 1"
                },
                sw_profile_link: {
                    maxlenght: "Maximum character limit is 150",
                    minlength: "Min character limit is 1"
                },
                phone_number: {
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },
                information: {
                    required: "Please Enter Details",
                    maxlenght: "Maximum character limit is 900",
                    minlength: "Min character limit is 1"
                },
            }

        });

        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_.,-''"")( ]+$/);
        }, "Only letters, Numbers & Space/underscore Allowed.");

        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");

        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        }, 'File size must be less than {0} Kb');


        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "") {
                return false;
            } else {
                return true;
            }
        }, "<span>Select One</span>");


    });
    // function to clear form
    function clearform() {
        var ab = '';
        $("#person_sw_id").val(ab);
        $("#sw_profile_link").val(ab);
        $("#phone_number").val(ab);
        jQuery("#socialwebsite").select2().select2('val', ['']);
        $("#information").val(ab);
        $("#record_id").val(ab);
        $("#linkfile").val(ab);
        $("#socialwebsite").attr("disabled", false);
        document.getElementById("personfile").value = "";
    }
</script>