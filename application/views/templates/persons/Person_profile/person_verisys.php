<?php 
$person_download_data_path = !empty(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) ? Helpers_Person::get_person_download_data_path(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) : '';
?>
<section class="content-header">
    <h1>
        Person's Portal
        <small><?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id=' . $_GET['id']); ?>">Person Dashboard </a></li>
        <li class="active">One Page Perfoma</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">    
    <div class="row">
        <div style="display:none;" id="custom-form"></div>
        <div class="col-md-12">
            <div class="">
                <div class="box box-primary">                    
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Person CNIC (Verisys):</h3>                             
                        </div>
                        <div class="box-body" style="display:block;">                                
                            <div class="form-group col-md-6">  
                                <form class="" name="veriysispics" id="veriysispics" action="<?php echo url::site() . 'personprofile/update_personverysis/?id=' . $_GET['id'] ?>"  method="post" enctype="multipart/form-data" >  
                                    <h4 class="style14 col-md-12"> Person CNIC (Verisys):</h4>
                                    <hr class="style14 col-md-12"> 
                                    <div class="img-circle" id="person_verisis">
                                        <?php
                                        try {
                                            $verisisimg = Helpers_Person::get_person_nadra_perofile($person_id);
                                            $verisisimg1 = !empty($verisisimg->cnic_image_url) ? $verisisimg->cnic_image_url : "NA";

                                            $frontpic = (!empty($verisisimg1) && !empty($person_download_data_path)) ? $person_download_data_path . $verisisimg1 : '';
                                            if ($verisisimg1 == "NA" && empty($frontpic)) {
                                                // echo $data->image_url;
                                                echo '<img class="img-responsive" src="' . URL::base() . 'dist/img/noperson.png" alt="No Data" width="450px" height="450px">';
                                                //now
                                            } else {
                                                $ext = pathinfo($frontpic, PATHINFO_EXTENSION);
                                                if ($ext == 'pdf') {
                                                    echo '<iframe src="'. $frontpic . '" style="height:650px;width:450px"></iframe>';
                                                }else {
                                                    echo HTML::image("{$frontpic}", array("height" => "450px", "width" => "450px"));
                                                }



                                            }
                                        } catch (Exception $ex) {
                                            
                                        }
                                        ?>                                          
                                    </div>
                                    <?php //echo $frontpic; ?>
                                    <?php
                                    try {
                                        $verisisimg = Helpers_Person::get_person_nadra_perofile($person_id);
                                        $nic_number = !empty($verisisimg->cnic_number) ? $verisisimg->cnic_number : 0;
                                        $verisisimg1 = !empty($verisisimg->cnic_image_url) ? $verisisimg->cnic_image_url : "NA";
                                        $login_user = Auth::instance()->get_user();
                                        $permission = Helpers_Utilities::get_user_permission($login_user->id);
                                        if ($verisisimg1 == "NA" || $permission == 1 || $permission == 5 || $permission == 2) {
                                            ?>
                                            <div class="form-group col-md-6"> 
                                                <label for="personverysis">Upload<small>(JPG,gif,PNG and PDF files only)</small></label>
                                                <input type="file" accept=".jpg,.gif,.png,.pdf" id="personverysis" name="personverysis" placeholder="Select Image">
                                            </div>



                                            <div class="form-group col-md-6"> 
                                                <button type="submit" class="btn btn-primary pull-left" style="margin-top:10px" >Upload</button>                                       
                                            </div> <?php
                                        }
                                    } catch (Exception $ex) {
                                        
                                    }
                                    ?>
                                </form>
                            </div>
                            <?php
                            if ($verisisimg1 != "NA" && !empty($frontpic)) {
                            ?>
                            <div class="col-md-6 verisys_info_form">
                                <!-- check if old cnic is expired-->
                                <?php
                                $expiry_date = Helpers_Profile::check_expiry_date($person_id);
                                $image_url_check = Helpers_Profile::check_image_url($person_id);
                                $current_date = date('Y-m-d');
                                ?>
                                <h4 class="style14 col-md-12"> Request for Fresh Verisys: (If Expired)</h4>
                                <hr class="style14 col-md-12">  
                                <?php if ( (!empty($expiry_date) && $expiry_date < $current_date)) { //($image_url_check == 0) && ?>
                                <div id="div_new_request" class="col-md-12">
                                    <button id="" type="button" onclick="requestnadraverysis(<?php echo $nic_number; ?>,<?php echo $person_id; ?>)" class="btn btn-primary">Request Fresh Verisys</button>
                                    <hr class="style14 col-md-12"> 
                                </div>
                                <?php }elseif ($expiry_date > $current_date) {  ?>
                                <h4 class="style14 col-md-12">Previous Verisys Is Not Expired. Date of Expiry is: <?php echo $expiry_date;?> </h4>
                                <hr class="style14 col-md-12"> 
                                <?php } ?>
                                <div id="div_verisys_info" style="display: block;">                                    
                                    <form class="ipf-form request_net" name="verisys_info_form" id="verisys_info_form" action="<?php echo url::site() . 'personprofile/update_verisys_info/?id=' . $_GET['id'] ?>" method="post" enctype="multipart/form-data" >
                                        <h4 class="style14 col-md-12"> Information Required If You Want to Request Fresh Verisys:</h4>
                                        <div class="col-md-6">
                                            <input type="hidden" name="cnic_image_url" id="cnic_image_url" value="<?php echo $verisisimg1; ?>">                                        
                                            <div class="form-group">
                                                <label for="person_name">Person Name:</label>
                                                <input type="text" placeholder="Enter Person Full Name" class="form-control" name="person_name" id="person_name" value="">                                        

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="person_g_name">Guardian Name:</label>
                                                <input type="text"  name="person_g_name" id="person_g_name" placeholder="Enter Person Full Name" class="form-control">                                        

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Person_gender">Gender:</label>
                                                <select class="form-control valid" id="Person_gender" name="Person_gender" >
                                                    <option value="">Please Select Gender</option>
                                                    <option value="0">Male</option>
                                                    <option value="1">Female</option>
                                                    <option value="2">Other</option>                                                
                                                </select>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date_of_birth">Date Of Birth</label>
                                                <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="date_of_birth" id="date_of_birth" >

                                            </div>
                                        </div> 
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="present_address">Present Address:</label>
                                                <input type="text"  name="present_address" id="present_address" placeholder="Enter Person Present Address" class="form-control">                                        

                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="permanent_address">Permanent Address:</label>
                                                <input type="text"  name="permanent_address" id="permanent_address" placeholder="Enter Person Permanent Address" class="form-control">                                        

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="issue_date">Issue Date:</label>
                                                <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="issue_date" id="issue_date" >

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="expiry_date">Expiry Date:</label>
                                                <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="expiry_date" id="expiry_date" >

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="family_number">Family Number:</label>
                                                <input type="text"  name="family_number" id="family_number" placeholder="Enter Person Family Number" class="form-control">                                        

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="birth_place">Place Of Birth:</label>
                                                <input type="text"  name="birth_place" id="birth_place" placeholder="Enter Person Place Of Birth" class="form-control">                                        

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="religion">Religion:</label>
                                                <input type="text"  name="religion" id="religion" placeholder="Enter Person Religion" class="form-control">                                        

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mother_name">Mother's Name:</label>
                                                <input type="text"  name="mother_name" id="mother_name" placeholder="Enter Person Mohter's Name" class="form-control">                                        

                                            </div>
                                        </div>
                                        <div class="col-md-12">                                    
                                            <div class="form-group pull-right" style="">                                         
                                                <button type="submit" class="btn btn-primary">Update Verisys Information</button>                                                        
                                            </div>
                                        </div>                                    
                                    </form>
                                </div>
                            </div>
                            <?php } ?>
                        </div>        
                    </div>
                </div>
            </div>
        </div>
    </div>    
</section>
<style>
    .verisys_info_form .form-group {
        height: 80px;
    }
</style>
<script type="text/javascript">
    //Date picker
    $('#date_of_birth').datepicker({
        autoclose: true,
        endDate: "today",

    });
    //Date picker
    $('#issue_date').datepicker({
        autoclose: true,
        endDate: "today",
    });
    //Date picker
    $('#expiry_date').datepicker({
        autoclose: true
    });
    $(document).ready(function (e) {
        $('#veriysispics').on('submit', (function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            if ($('#veriysispics').valid())
            {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (msg) {
                        if (msg == 2) {
                            swal("System Error", "Contact Support Team.", "error");
                        } else {
                            $("#person_verisis").html(msg);
                        }
                        document.getElementById("veriysispics").reset();
                    },
                    error: function (data) {
                        console.log("error");
                        console.log(data);
                    }
                });
            }
        }));
        
         //validate Person Verisis
        $("#veriysispics").validate({
            rules: {
                personverysis: {
                    required: true,
                    accept: "jpg,jpeg,gif,png,pdf",
                    filesize: 2000000
                },
            },
            messages: {
                personverysis: {
                    required: "File Required",
                    filesize: "Max file size allowed is 2 MB"
                },
            }

        });

        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        }, 'File size must be less than {0} kb');
        
        //form validation
        $("#verisys_info_form").validate({
            rules: {
                person_name: {
                    required: true,
                    minlength: 10,
                    maxlength: 25,
                },
                person_g_name: {
                    required: true,
                    minlength: 10,
                    maxlength: 25,
                },
                Person_gender: {
                    required: true,
                },
                date_of_birth: {
                    required: true,
                },
                present_address: {
                    required: true,
                    minlength: 5,
                    maxlength: 500,
                    alphanumericspecial: true,
                },
                permanent_address: {
                    required: true,
                    minlength: 5,
                    maxlength: 500,
                    alphanumericspecial: true,
                },
                issue_date: {
                    required: true,
                },
                expiry_date: {
                    required: true,
                },
                family_number: {
                    required: true,
                    minlength: 5,
                    maxlength: 15,
                },
                birth_place: {
                    required: true,
                    minlength: 5,
                    maxlength: 25,
                },
                religion: {
                    required: true,
                    minlength: 5,
                    maxlength: 25,
                },
                mother_name: {
                    required: true,
                    minlength: 5,
                    maxlength: 25,
                },
            },
            messages: {
                person_name: {
                    // required: "File Required",
                },
            }

        });

        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ #.,/]+$/);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

        //form submit via ajax
        $('#verisys_info_form').on('submit', (function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            if ($('#verisys_info_form').valid())
            {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        if (data == 1) {
                            document.getElementById("verisys_info_form").reset();
                            swal("Congratulations!", "Verisys Information Saved successfully.", "success");
                            location.reload();
                        } else if (data == 99) {
                            swal("Warning!", "Data With Same Information Already Exist", "warning");
                        }else {
                            swal("System Error", "Kindly Contact Technical Support Team.", "error");
                        }
                    },
                    error: function (data) {
                        console.log("error");
                        console.log(data);
                    }
                });
            }
        }));        
    });

//function to call request page of verisys          
        function requestnadraverysis(cnicnumber, personid) {
            var url = window.location.href;
            var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestverisys/?id=" . $_GET['id']); ?>" method="POST">');
            newForm.append(jQuery('<input>', {
                'name': 'cnicnumber',
                'value': cnicnumber,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'url',
                'value': url,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'pid',
                'value': personid,
                'type': 'text'
            }));
            newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
            $('#custom-form').append(newForm);
            //$("#custom_form_bd").submit();
            $('#custom_form_request_button').trigger('click');

        }
</script>