<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-info"></i> Person Info Update
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>	
        <li><a href="<?php echo URL::site('persons/dashboard/?id=' . $_GET['id']); ?>">Person Dashboard </a></li>                        
        <li class="active">Person Info Update</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">


        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">                          
                        <form class="ipf-form" action="<?php echo url::site() . 'Personprofile/person_info_update_post' ?>" name="person_info_update" id="person_info_update" method="post" enctype="multipart/form-data" >
                            <input type="hidden" readonly="" class="form-control" id="person_id"  name="person_id" value="<?php echo $person_id; ?>">                            
                            <?php
                            if (!empty($person_table_data)) {
                                $first_name = !empty($person_table_data->first_name) ? $person_table_data->first_name : '';
                                $last_name = !empty($person_table_data->last_name) ? $person_table_data->last_name : '';
                                $father_name = !empty($person_table_data->father_name) ? $person_table_data->father_name : '';
                                $user_id = !empty($person_table_data->user_id) ? $person_table_data->user_id : 0;
                                ?>
                            <input type="hidden" readonly="" class="form-control" id="user_id"  name="user_id" value="<?php echo $user_id; ?>">
                                <div class="form-group col-md-12" >
                                    <h3 class="style14 col-md-12">Basic Information (Person Table) <b> <?php echo 'ID = ' . $person_id; ?> </b></h3>
                                    <div class="form-group col-md-4">
                                        <label for="first_name">First Name</label>
                                        <input type="text" readonly="" class="form-control" id="first_name"  name="first_name" value="<?php echo $first_name; ?>" placeholder="Enter First Name">                                        
                                    </div> 
                                    <div class="form-group col-md-4">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" readonly="" class="form-control" id="last_name"  name="last_name"  value="<?php echo $last_name; ?>" placeholder="Enter Last Name">
                                    </div> 
                                    <div class="form-group col-md-4">
                                        <label for="father_name">Father Name</label>
                                        <input type="text" readonly="" class="form-control" id="father_name"  name="father_name"  value="<?php echo $father_name; ?>" placeholder="Enter Father Name">
                                    </div>                             
                                </div>
                            <?php } ?>
                            <?php
                            if (!empty($person_initiate)) {
                                $cnic_number = !empty($person_initiate->cnic_number) ? $person_initiate->cnic_number : '';
                                $cnic_number_foreigner = !empty($person_initiate->cnic_number_foreigner) ? $person_initiate->cnic_number_foreigner : '';
                                $is_foreigner = isset($person_initiate->is_foreigner) ? $person_initiate->is_foreigner : '';
                                ?>
                                <div class="form-group col-md-12" >
                                    <h3 class="style14 col-md-12">Person Initiate</h3>
                                    <div class="form-group col-md-4">
                                        <label for="cnic_number">CNIC Number</label>
                                        <input type="text" readonly="" class="form-control" id="cnic_number"  name="cnic_number"  value="<?php echo $cnic_number; ?>" placeholder="Enter CNIC Number">
                                    </div> 
                                    <div class="form-group col-md-4">
                                        <label for="cnic_number_foreigner">CNIC Number Foreigner</label>
                                        <input type="text" readonly="" class="form-control" id="cnic_number_foreigner"  name="cnic_number_foreigner"   value="<?php echo $cnic_number_foreigner; ?>"  placeholder="Enter CNIC Number Foreigner">
                                    </div> 
                                    <div class="form-group col-md-4">
                                        <label for="is_foreigner">Is Foreigner</label>
                                        <input type="text" readonly="" class="form-control" id="is_foreigner"  name="is_foreigner"  value="<?php echo $is_foreigner; ?>"  placeholder="Enter Is Foreigner ">
                                    </div>                             
                                </div>
                            <?php } ?>
                            <?php
                                $pnp_cnic_number = !empty($person_nadra_profile->cnic_number) ? $person_nadra_profile->cnic_number : '';
                                ?>
                                <div class="form-group col-md-6" >
                                    <h3 class="style14 col-md-12">Person Nadra Profile</h3>
                                    <div class="form-group col-md-12">
                                        <label for="pnp_cnic_number">CNIC Number</label>
                                        <input type="text" readonly="" class="form-control" id="pnp_cnic_number"  name="pnp_cnic_number" value="<?php echo $pnp_cnic_number; ?>"   placeholder="CNIC Person NADRA Profile">
                                    </div>                                                              
                                </div>                            
                            <?php
                                $pfp_cnic_number = !empty($person_foreigner->cnic_number) ? $person_foreigner->cnic_number : '';
                                ?>
                                <div class="form-group col-md-6" >
                                    <h3 class="style14 col-md-12">Person Foreigner Profile</h3>
                                    <div class="form-group col-md-12">
                                        <label for="pnp_cnic_number">CNIC Number</label>
                                        <input type="text" readonly="" class="form-control" id="pfp_cnic_number"  name="pfp_cnic_number" value="<?php echo $pfp_cnic_number; ?>" >
                                    </div>                                                              
                                </div>                            
                            <hr class="style14 col-md-12">
                            <div class="form-group col-md-12 " style="">
                                <div class="pull-right">                                                                                 
                                    <input type="button" id="editinformation" onclick="editform()" value="Edit Information" class="btn btn-warning " />
                                    <input type="submit" id="updateinformation" value="Update Information" class="btn btn-success " />
                                </div>
                            </div>
                        </form>
                    </div>                    
                </div>                
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

<script>
    $(document).ready(function () {
        $('#updateinformation').prop('disabled', true);
        //validate Person Profile Pic
        $("#person_info_update").validate({
            rules: {                
                cnic_number: {
                    number: true,
                    maxlength: 13,
                    minlength: 13,
                    required: function (element) {                
                        return $("#cnic_number_foreigner").val() == '';
                        //return isEmpty(("#cnic_number_foreigner"));
                    }
                },
                is_foreigner: {
                    isforeigner: true,
                    number: true,
                    maxlength: 1,
                    minlength: 1,
                    required: true,
                },
                cnic_number_foreigner: {
                    required: function (element) {
                        return $("#cnic_number").val() == '';
                    }
                },
                pnp_cnic_number: {
                    number: true,
                    maxlength: 13,
                    minlength: 13,
                    required: function (element) {
                        return $("#is_foreigner").val() == '0';
                    }
                },
                pfp_cnic_number: {                    
                    maxlength: 13,
                    minlength: 13,
                    required: function (element) {
                        return $("#is_foreigner").val() == '1';
                    }
                }
            },
            messages: {
//                is_foreigner: {
//                    //required: "File Required",
//                },
                cnic_number: {
                    required: "CNIC number or foreigner required",
                },
                cnic_number_foreigner: {
                    required: "CNIC number or foreigner required",
                },
            }

        });
        
                //alert('not validated');
        jQuery.validator.addMethod("isforeigner", function (value, element) {
            return this.optional(element) || value == value.match(/^[0,1]$/);
        }, "Only 0=Pakistan or 1=Foreigner Allowed");
        //Person tags form submit through ajax call
        $('#person_info_update').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            if ($('#person_info_update').valid())
            {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (msg) {
                        if (msg == 1) {
                            swal("Congratulations!", "Person Information Updated Successfully.", "success");
                        resetform();
                        location.reload();
                        }else{
                            swal("System Error", "Contact Support Team.", "error");
                        }                        
                    }
                });
            }
        });
    });
    //function to enable disable basic info
    function editform() {
        $('#first_name').prop('readonly', false);
        $('#last_name').prop('readonly', false);
        $('#father_name').prop('readonly', false);
        $('#cnic_number').prop('readonly', false);
        $('#cnic_number_foreigner').prop('readonly', false);
        $('#is_foreigner').prop('readonly', false);
        $('#pnp_cnic_number').prop('readonly', false);
        $('#pfp_cnic_number').prop('readonly', false);

        $('#editinformation').prop('disabled', true);
        $('#updateinformation').prop('disabled', false);
    }
    
    //function to enable disable basic info
    function resetform() {
        $('#first_name').prop('readonly', true);
        $('#last_name').prop('readonly', true);
        $('#father_name').prop('readonly', true);
        $('#cnic_number').prop('readonly', true);
        $('#cnic_number_foreigner').prop('readonly', true);
        $('#is_foreigner').prop('readonly', true);
        $('#pnp_cnic_number').prop('readonly', true);
        $('#pfp_cnic_number').prop('readonly', true);

        $('#editinformation').prop('disabled', false);
        $('#updateinformation').prop('disabled', true);
    }


</script>  