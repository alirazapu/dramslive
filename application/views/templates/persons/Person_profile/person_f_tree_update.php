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
                            <h3 class="box-title">Person Family Tree:</h3>
                        </div>
                        <div class="box-body" style="display:block;">                                
                            <div class="form-group col-md-6">  
                                <form class="" name="ftreepics" id="ftreepics" action="<?php echo url::site() . 'personprofile/update_personftreepic/?id=' . $_GET['id'] ?>"  method="post" enctype="multipart/form-data" >
                                    <h4 class="style14 col-md-12"> Person Family Tree:</h4>
                                    <hr class="style14 col-md-12"> 
                                    <div class="img-circle" id="person_ftreepic">
                                        <?php
                                        try {
                                            $verisisimg = Helpers_Person::get_person_nadra_perofile($person_id);
                                            $verisisimg1 = !empty($verisisimg->family_image_url) ? $verisisimg->family_image_url : "NA";

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
                                        $verisisimg1 = !empty($verisisimg->family_image_url) ? $verisisimg->family_image_url : "NA";
                                        $login_user = Auth::instance()->get_user();
                                        $permission = Helpers_Utilities::get_user_permission($login_user->id);
                                        if ($verisisimg1 == "NA" || $permission == 1 || $permission == 5 || $permission == 2) {
                                            ?>
                                            <div class="form-group col-md-6"> 
                                                <label for="personftreepic">Upload<small>(JPG,gif,PNG and PDF files only)</small></label>
                                                <input type="file" accept=".jpg,.gif,.png,.pdf" id="personftreepic" name="personftreepic" placeholder="Select Image">
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


    $(document).ready(function (e) {
        $('#ftreepics').on('submit', (function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            if ($('#ftreepics').valid())
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
                            $("#person_ftreepic").html(msg);
                        }
                        document.getElementById("ftreepics").reset();
                    },
                    error: function (data) {
                        console.log("error");
                        console.log(data);
                    }
                });
            }
        }));
        
         //validate Person Verisis
        $("#ftreepics").validate({
            rules: {
                personftreepic: {
                    required: true,
                    accept: "jpg,jpeg,gif,png,pdf",
                    filesize: 2000000
                },
            },
            messages: {
                personftreepic: {
                    required: "File Required",
                    filesize: "Max file size allowed is 2 MB"
                },
            }

        });

        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        }, 'File size must be less than {0} kb');
        




    });


</script>