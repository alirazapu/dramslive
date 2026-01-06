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
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Person Pictures </h3>
                </div> 
                <div class="box-body">
                    <div class="form-group" id="" style="display: ">
                        <div class="form-group col-md-12 " >
                            <div class="alert-dismissible notificationclosepictures" id="notification_msgpictures" style="display: none;">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divpictures"></div></h4>
                            </div>
                        </div>
                        <!--                                    <div class="col-md-12">
                                                                <h4 class="style14 col-md-12" style="text-align: center;"><b><u><?php // echo $fname." ".$lname." ";     ?> </u></b></h4>
                                                            </div>-->
                        <div class="form-group col-md-12">                                         
                            <div class="col-md-4">
                                <div class="profice_pictures">                                            
                                    <h4 class="style14 col-md-12">Person Left Picture:</h4>
                                    <div class="img-circle" id="person_pic_left">
                                        <?php
                                        try {
                                            $image_path = Helpers_Person::get_person_pictures($person_id, 2);
                                            $frontpic = (!empty($image_path->image_url) && !empty($image_path->image_url)) ? $person_download_data_path . $image_path->image_url : '';
                                            if (empty($image_path->image_url) || $image_path->image_url == 0 || empty($frontpic)) {
                                                echo HTML::image("dist/img/avtar6.jpg", array("height" => "150px", "width" => "150px"));
                                            } else {
                                                echo HTML::image("{$frontpic}", array("height" => "200px", "width" => "200px"));
                                            }
                                        } catch (Exception $ex) {
                                            
                                        }
                                        ?>
                                    </div>

                                    <form class="" name="profilepics" id="profilepicleft"  method="post" enctype="multipart/form-data" >  
                                        <?php
                                        try {
                                            $image_path = Helpers_Person::get_person_pictures($person_id, 2);
                                            if (empty($image_path->image_url) || $image_path->image_url == 0) {
                                                ?>    
                                                <div class="form-group col-md-12 pic-height"> 
                                                    <input type="text" name="picture_type" hidden="" value="2">
                                                    <label for="personpic">Upload<small>(JPG,gif and PNG files only)</small></label>
                                                    <input type="file" accept=".jpg,.gif,.png" id="personpic" name="personpic" placeholder="Select Image">  
                                                </div> 
                                                <div class="form-group col-md-12 uploadbutton"> 
                                                    <button type="submit" class="btn btn-primary pull-right" style="margin-top:10px" >Save Picture</button>
                                                </div>
                                                <?php
                                            }
                                        } catch (Exception $ex) {
                                            
                                        }
                                        ?>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-4" >
                                <div class="profice_pictures">                                            
                                    <h4 class="style14 col-md-12">Person Front Picture:</h4>
                                    <div class="img-circle" id="person_pic_front">
                                        <?php
                                        try {
                                            $image_path = Helpers_Person::get_person_pictures($person_id, 1);
                                            $frontpic = (!empty($image_path->image_url) && !empty($person_download_data_path)) ? $person_download_data_path . $image_path->image_url : '';

                                            if (empty($image_name->image_url) && empty($frontpic)) {

                                                echo HTML::image("dist/img/avtar6.jpg", array("height" => "150px", "width" => "150px"));
                                            } else {
                                                echo HTML::image("{$frontpic}", array("height" => "200px", "width" => "200px"));
                                            }
                                        } catch (Exception $ex) {
                                            
                                        }
                                        ?>
                                    </div>

                                    <form class="" name="profilepics" id="profilepicfront" action="<?php echo url::site() . 'personprofile/upload_person_pictures/?id=' . $_GET['id'] ?>"  method="post" enctype="multipart/form-data" >  
                                        <?php
                                        try {
                                            $image_path = Helpers_Person::get_person_pictures($person_id, 1);
                                            if (empty($image_path->image_url) || $image_path->image_url == 0) {
                                                ?>
                                                <div class="form-group col-md-12 pic-height">
                                                    <input type="text" name="picture_type" hidden="" value="1">
                                                    <label for="personpic">Upload<small>(JPG,gif and PNG files only)</small></label>
                                                    <input type="file" accept=".jpg,.gif,.png" id="personpic" name="personpic" placeholder="Select Image">  
                                                </div> 
                                                <div class="form-group col-md-12 uploadbutton"> 
                                                    <button type="submit" class="btn btn-primary pull-right" style="margin-top:10px" >Save Picture</button>
                                                </div>
                                                <?php
                                            }
                                        } catch (Exception $ex) {
                                            
                                        }
                                        ?>
                                    </form>
                                </div>
                            </div>                                            
                            <div class="col-md-4">
                                <div class="profice_pictures">                                            
                                    <h4 class="style14 col-md-12">Person Right Picture:</h4>
                                    <div class="img-circle" id="person_pic_right">
                                        <?php
                                        try {
                                            $image_path = Helpers_Person::get_person_pictures($person_id, 3);
                                            $frontpic = (!empty($image_path->image_url) && !empty($image_path->image_url)) ? $person_download_data_path . $image_path->image_url : '';
                                            if (empty($image_path->image_url) || $image_path->image_url == 0 || empty($frontpic)) {
                                                echo HTML::image("dist/img/avtar6.jpg", array("height" => "150px", "width" => "150px"));
                                            } else {
                                                echo HTML::image("{$frontpic}", array("height" => "200px", "width" => "200px"));
                                            }
                                        } catch (Exception $ex) {
                                            
                                        }
                                        ?>
                                    </div>

                                    <form class="" name="profilepics" id="profilepicright" action="<?php echo url::site() . 'personprofile/upload_person_pictures/?id=' . $_GET['id'] ?>"  method="post" enctype="multipart/form-data" >  
                                        <?php
                                        try {
                                            $image_path = Helpers_Person::get_person_pictures($person_id, 3);
                                            if (empty($image_path->image_url) || $image_path->image_url == 0) {
                                                ?>
                                                <div class="form-group col-md-12 pic-height"> 
                                                    <input type="text" name="picture_type" hidden="" value="3">
                                                    <label for="personpic">Upload<small>(JPG,gif and PNG files only)</small></label>
                                                    <input type="file" accept=".jpg,.gif,.png" id="personpic" name="personpic" placeholder="Select Image">  
                                                </div> 
                                                <div class="form-group col-md-12 uploadbutton"> 
                                                    <button type="submit" class="btn btn-primary pull-right" style="margin-top:10px" >Save Picture</button>
                                                </div>
                                                <?php
                                            }
                                        } catch (Exception $ex) {
                                            
                                        }
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <hr class="style14 col-md-12"> 
                    </div> 
                </div>
                <!-- /.box-header -->
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
        //validate Person Profile Pic
        $("#profilepics").validate({
            rules: {
                personpic: {
                    required: true,
                    filesize: 2000000
                },
            },
            messages: {
                personpic: {
                    required: "File Required",
                    filesize: "Max file size allowed is 2 MB"
                },
            }

        });
        //validate Person Profile Pic
        $("#profilepicfront").validate({
            rules: {
                personpic: {
                    required: true,
                    filesize: 2000000
                },
            },
            messages: {
                personpic: {
                    required: "File Required",
                    filesize: "Max file size allowed is 2 MB"
                },
            }

        });
        //validate Person Profile Pic
        $("#profilepicright").validate({
            rules: {
                personpic: {
                    required: true,
                    filesize: 2000000
                },
            },
            messages: {
                personpic: {
                    required: "File Required",
                    filesize: "Max file size allowed is 2 MB"
                },
            }

        });
        //validate Person Profile Pic
        $("#profilepicleft").validate({
            rules: {
                personpic: {
                    required: true,
                    filesize: 2000000
                },
            },
            messages: {
                personpic: {
                    required: "File Required",
                    filesize: "Max file size allowed is 2 MB"
                },
            }

        });
        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        }, 'File size must be less than {0} kb');
        //profile pics form submit
        $('#profilepicfront').on('submit', (function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            if ($('#profilepicfront').valid())
            {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (msg) {
                        document.getElementById("profilepicfront").reset();
                        if (msg == 2) {
                            swal("System Error", "Contact Support Team.", "error");
                        } else {
                            $("#person_pic_front").html(msg);
                        }
                    },
                    error: function (data) {
                        console.log("error");
                        console.log(data);
                    }
                });
            }
        }));

        $('#profilepicright').on('submit', (function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            if ($('#profilepicright').valid())
            {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (msg) {
                        document.getElementById("profilepicright").reset();
                        if (msg == 2) {
                            swal("System Error", "Contact Support Team.", "error");
                        } else {
                            $("#person_pic_right").html(msg);
                        }
                    },
                    error: function (data) {
                        console.log("error");
                        console.log(data);
                    }
                });
            }
        }));
        $('#profilepicleft').on('submit', (function (e) {            
            e.preventDefault();
            var formData = new FormData(this);
            if ($('#profilepicleft').valid())
            {
                $.ajax({
                    type: 'POST',
                    url: "<?php echo URL::site("personprofile/upload_person_pictures/?id=". $_GET['id'] ); ?>",
                    //url: $(this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (msg) {
                       // document.getElementById("profilepicleft").reset();
                        if (msg == 2) {
                            swal("System Error", "Contact Support Team.", "error");
                        } else {
                            $("#person_pic_left").html(msg);
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

</script>