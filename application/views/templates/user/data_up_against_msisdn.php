<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$uid = $user_id;
$post_country = '';
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-upload"></i>Data Upload (MSISDN)
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Data Upload Against Mobile#</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="box box-primary">

            <form role="form" name="msisdn_form" id="msisdn_form" class="ipf-form" method="POST"
                  action="<?php echo URL::site('admindatabank/data_upload_against_msisdn_feed'); ?>"
                  enctype="multipart/form-data">

                <div class="box box-default collapsed-box">
                    <div class="box-body" style="display:block;">
                        <div class="row">
                            <div class="col-md-12">
                            <div class="col-sm-6" style="display: block;margin-top: 10px;">
                                <div class="form-group">
                                    <label for="msisdn_is_foreigner" class="control-label">Country</label>
                                    <select <?php if ($post_country == 0 OR $post_country == 1) {
                                        echo "readonly";
                                    } ?> class="form-control" name="is_foreigner" id="msisdn_is_foreigner">
                                        <option <?php if ($post_country == 0) {
                                            echo "selected";
                                        } ?> value="0">Pakistan
                                        </option>
                                        <option <?php if ($post_country == 1) {
                                            echo "selected";
                                        } ?> value="1">Foreign
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6" id="project_div" style="display: block;margin-top: 10px;">
                                <div class="form-group">
                                    <label for="inputproject" class="control-label">Link With Project<span
                                                class="text-danger">(*)</span></label>
                                    <?php $projects_data = Helpers_Utilities::get_projects_list(); ?>
                                    <select class="form-control select2" name="inputproject[]" id="inputproject"
                                            dataplaceholder="please select project" style="width: 100%!important;">
                                        <option value="">Please select project name</option>
                                        <?php foreach ($projects_data as $project) {
                                            $region_district = Helpers_Requests::get_project_region_district($project->region_id, $project->district_id); ?>
                                            <option value="<?php echo $project->id; ?>"><?php echo $project->project_name . $region_district; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="inputSubNO" class="control-label">Subscriber Number<span
                                                class="text-danger">(*)</span></label>
                                    <input name="mobile_number" type="number" class="form-control" id="inputSubNO"
                                           placeholder="Subscriber Number"/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label title="Date when sim is activated in given company" for="inputActivationDate"
                                           class="control-label">SIM Activation Date</label>
                                    <input readonly="" title="Date when sim is activated in given company"
                                           name="act_date" type="text" class="form-control" id="act_date"
                                           placeholder="mm/dd/yyyy">
                                </div>
                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="imsi" class=" control-label">IMSI</label>
                                    <input type="text" name="imsi" class="form-control" id="imsi"
                                           placeholder="19 digists number only">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="imei" class=" control-label">IMEI</label>
                                    <input type="text" name="imei" class="form-control" id="inputimei"
                                           placeholder="15 digists number only">
                                </div>
                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="inputName" class="control-label">First Name</label>
                                    <input name="first_name" type="text" class="form-control" id="inputName"
                                           placeholder="First Name">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="inputName1" class="control-label">Last Name</label>
                                    <input name="last_name" type="text" class="form-control" id="inputName1"
                                           placeholder="Last Name">
                                </div>
                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label title="In case of foreigner select country as foreigner" for="inputCNIC"
                                           class="control-label">CNIC</label>
                                    <input title="In case of foreigner select country as foreigner" name="cnic_number"
                                           type="text" class="form-control" id="inputCNIC"
                                           placeholder="13 digists number only without dashes">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="inputAddress" class=" control-label">Address</label>
                                    <input type="text" name="address" class="form-control" id="inputAddress"
                                           placeholder="Address">
                                </div>
                            </div>
                            </div>
                            <div class="col-md-12">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="ConnectionTypeRadio1" class="control-label">Connection Type</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="ConnectionTypeRadios" id="ConnectionTypeRadio1"
                                                   value="0" checked="">
                                            Pre-Paid
                                        </label>
                                        <label>
                                            <input type="radio" name="ConnectionTypeRadios" id="ConnectionTypeRadio2"
                                                   value="1">
                                            Post-Paid
                                        </label>
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="StatusRadio1" class="control-label">Status</label>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="StatusRadios" id="StatusRadio1" value="0"
                                                   checked="">
                                            Active
                                        </label>
                                        <label>
                                            <input type="radio" name="StatusRadios" id="StatusRadio2" value="1">
                                            In-Active
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group pull-left">
                                    <label for="cdr_file">Upload<small> (files)</small></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="file" accept=".jpg,.gif,.png" id="cdr_file" name="cdr_file"
                                           placeholder="Select Image">
                                </div>
                            </div>
                            </div>
                            <div class="form-group col-sm-12 pull-left">
                                <button type="submit" class="btn btn-primary" style="margin-top:10px">Submit</button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="table-responsive-11">
                                <table id="msisdn_data" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th class="no-sort"> Person Info</th>
                                        <th class="no-sort">Mobile Info</th>
                                        <th>Project </th>
                                        <th class="no-sort">Uploaded By</th>
                                        <th class="no-sort"> File</th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th class="no-sort"> Person Info</th>
                                        <th class="no-sort">Mobile Info</th>
                                        <th>Project </th>
                                        <th class="no-sort">Uploaded By</th>
                                        <th class="no-sort"> File</th>

                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>

        </div>
    </div>


</section>
<div class="modalwait"><!-- Place at bottom of page --></div>
<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>
<!-- /.content -->
<script type="text/javascript">


    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }


    $(document).ready(function () {
        //validate

        $("#msisdn_form").validate({
            rules:
                {
                    // "cdr_file": {
                    //     required: true,
                    //     accept: "jpg,jpeg,gif,png",
                    //     filesize: 900000,
                    // },
                    // cnic_number: {
                    //     required: true,
                    //     minlength: {
                    //         param: 13,
                    //         depends: function (element) {
                    //             return ($("#cnic_number").val() != 0);
                    //         }
                    //     },
                    //     maxlength: {
                    //         param: 13,
                    //         depends: function (element) {
                    //             return ($("#cnic_number").val() != 0);
                    //         }
                    //     },
                    //
                    // },
                    // imei: {
                    //     required: true,
                    //     number: true,
                    //     minlength: {
                    //         param: 15,
                    //         depends: function (element) {
                    //             return ($("#imei").val() != 0);
                    //         }
                    //     },
                    //     maxlength: {
                    //         param: 15,
                    //         depends: function (element) {
                    //             return ($("#imei").val() != 0);
                    //         }
                    //     },
                    // },
                    // imsi: {
                    //     required: true,
                    //     number: true,
                    //     minlength: {
                    //         param: 12,
                    //         depends: function (element) {
                    //             return ($("#imsi").val() != 0);
                    //         }
                    //     },
                    // },
                    mobile_number: {
                        required: true,
                        number: true,
                        numberthree: true,
                        maxlength: 10,
                        minlength: 10
                    },

                    "inputproject[]": {
                        required: {
                            depends: function (element) {
                                if ('none' == $('#inputproject').val()) {
                                    //Set predefined value to blank.
                                    $('#inputproject').val('');
                                }
                                return true;
                            }
                        }
                    }

                },
            messages: {
                // "cdr_file": {
                //     required: "File Required",
                // },
                // cnic_number: {
                //     required: "Entered 13 Digit Cnic number",
                // },
                // imei: {
                //     minlength: "Minimum 15 digits required",
                //     maxlenght: "Maximum 15 digits required"
                // },
                // imsi: {
                //     minlength: "Minimum 12 digits required"
                // },
                mobile_number: {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },

            }

        });

        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        }, 'File size must be less than {0} Kb');
        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");

        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .,/]+$/);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

        $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
            if (oSettings.oFeatures.bServerSide === false) {
                var before = oSettings._iDisplayStart;
                oSettings.oApi._fnReDraw(oSettings);
                // iDisplayStart has been reset to zero - so lets change it back
                oSettings._iDisplayStart = before;
                oSettings.oApi._fnCalculateEnd(oSettings);
            }

            // draw the 'current' page
            oSettings.oApi._fnDraw(oSettings);
        };
        objDT = $('#msisdn_data').dataTable(
            {//"aaSorting": [[3, "desc"]],
                "bPaginate": true,
                "bProcessing": true,
                //"bStateSave": true,
                "mark": true,
                "bServerSide": true,
                "sAjaxSource": "<?php echo URL::site('admindatabank/ajaxmsisdn_old_data?uid='.$uid, TRUE); ?>",
                "sPaginationType": "full_numbers",
                "bFilter": true,
                "bLengthChange": true,
                "oLanguage": {
                    "sProcessing": "Loading...",
                    "sSearch": "Search By CNIC :"
                },
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }]
            }
        );
        $('.dataTables_empty').html("Information not found");

    });
    //Date picker
    $('#act_date').datepicker({
        autoclose: true
    });

    //msisdn_form submit through ajax call
    $('#msisdn_form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        if ($('#msisdn_form').valid()) {
            $("body").addClass("loading");
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (msg) {

                    $("#msisdn_form")[0].reset();
                    refreshGrid();
                    $("body").removeClass("loading");
                    if (msg == '1') {
                        swal({
                            title: "Success",
                            text: "Data added successfuly.",
                            type: "success"
                        });
                    }
                    // else { //error in any one file
                    //     swal("oops!", "All Files Not Uploaded Successfully.", "warning");
                    // }

                    if (msg == 2) {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    $("body").removeClass("loading");

                },
            });
        }
    });


</script>