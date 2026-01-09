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
        <i class="fa fa-whatsapp"></i>
        Family tree Requests
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Request</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <?php
            $person_id = !empty($post['pid']) ? $post['pid'] : 0;
            $request = !empty($post['request']) ? $post['request'] : "";
            $requesttype = !empty($post['requesttype']) ? $post['requesttype'] : 0;
            $cnic = !empty($post['cnic']) ? $post['cnic'] : 0;
            $request_status = !empty($post['status']) ? $post['status'] : 0;

            $redirect_url = !empty($post['url']) ? $post['url'] : URL::site('userdashboard/dashboard');

            ?>
            <div class="box box-primary">
                <div id="headerdiv" class="box-header with-border">
                    <h3 class="box-title">Request</h3>
                    <a href="<?php echo $redirect_url; ?>" class="btn btn-warning btn-small" style="float: right;"><i
                                class="fa fa-backward"></i> Go Back</a>
                </div>
                <div class="box-body">
                 <div class="col-md-12">
                <form class="ipf-form request_net" name="requestform" id="userrequest" method="post"
                      enctype="multipart/form-data">

                        <div class="alert " id="upload" style="color: '#ff5b3c'; display: none">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i>
                                <span id='parsresult'> Be Patient request in process
                                    <img style="width: 12%; height: 29px;" id="parse_loader"
                                         src="<?php //echo URL::base() . 'dist/img/103.gif'; ?>">
                                </span></h4>
                        </div>
                        <input type="hidden" name="person_id" value="<?php echo $person_id; ?>"/>
                        <input type="hidden" name="redirect_url" id="redirect_url"
                               value="<?php echo $redirect_url; ?>"/>

                        <div class="col-sm-6" >
                            <div class="form-group">
                                <label class="control-label">CNIC No.<span class="text-danger">(*)</span></label>
                                <input type="number" class="form-control" name="cnic" id="cnic"
                                       placeholder="Cnic No.">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label> Date (mm/dd/yyyy)<span class="text-danger">(*)</span></label>
                            <div class="form-group">
                                                    <span class="input-group-prepend">
                                                        <span class="input-group-text"><i
                                                                    class="fas fa-calendar-alt"></i>
                                                        </span>
                                                    </span>
                                <input type="text" id="date" name="date"
                                       class="form-control" data-plugin-datepicker=""
                                       placeholder="Select Date">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="reqbyregion" class="control-label">Requested By Region</label>
                                <?php try{
                                $rqts= Helpers_Utilities::get_region();
                                $data = $rqts->as_array();
                                ?>
                                <select <?php if (!empty($region_id)) {echo 'selected readonly="readonly" disabled'; } ?> class="form-control" onchange="region_district()" data-placeholder="Please Select Region Name" name="reqbyregion" id="reqbyregion" style="width: 100%">
                                    <option hidden value="" >Please Select Region Name</option>
                                    <?php foreach ($data as $rqt) { ?>
                                        <option <?php if (!empty($region_id) && $region_id == $rqt->region_id) { echo 'Selected';} ?>
                                                value="<?php echo $rqt->region_id; ?>"><?php echo $rqt->name; ?></option>
                                    <?php }
                                    }  catch (Exception $ex){   }?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="reqbydistrict" class="control-label">Requested By Region/District/Police Station</label>
                                <select <?php if (!empty($district_id)) {echo 'selected readonly="readonly" disabled'; } ?> class="form-control"  placeholder="Select Region/District/Police Station" name="reqbydistrict" id="reqbydistrict" style="width: 100%">

                                </select>
                            </div>
                        </div>

                    <div class="col-sm-6" id="reason_div">
                        <div class="form-group">
                            <label for="inputreason" class="control-label">Requested Attachment</label>
                            <input type="file" accept=".jpg,.gif,.png" id="rqtfile" name="rqtfile"
                                   placeholder="Select Image">
                        </div>
                    </div>
                    <div class="col-sm-6" id="reason_div">
                        <div class="form-group">
                            <label for="inputreason" class="control-label">Requested By<span class="text-danger">(*)</span></label>
                            <input type="text" class="form-control" name="rqtbyname" id="rqtbyname" value=""
                                   placeholder="Name">
                            <div id="rqtbynamelist"></div>
                        </div>
                    </div>

                        <div class="col-sm-12" id="reason_div">
                            <div class="form-group">
                                <label for="inputreason" class="control-label">Reason For This Request<span class="text-danger">(*)</span></label>
                                <textarea class="form-control" name="inputreason" id="inputreason"
                                          placeholder="Enter Reason For Request"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Request Status</label>
                                <div class="radio radio-primary" style="margin-left: 20px" id="status">
                                    <input type="radio" <?php echo (isset($request_status) && $request_status == 0) ? 'checked' : ''; ?> name="status" id="req_status_1" value="0">
                                    <label for="req_status_1" style="padding-left: 2px; margin-right: 25px">
                                        Pending
                                    </label>
                                    <input type="radio" <?php echo (isset($request_status) && $request_status == 1) ? 'checked' : ''; ?>  name="status" id="req_status_2" value="1">
                                    <label for="req_status_2" style="padding-left: 2px; margin-right: 25px">
                                        Processed
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="submit_div">
                            <div class="col-sm-12">
                                <button id="userrequestbtn" type="button" onclick="submitrequestform()"
                                        class="btn btn-primary pull-right" style="margin-top:10px">Submit
                                </button>
                            </div>
                        </div>
                </form>
                </div>
                </div>



                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title"><i class="fa fa-desktop"></i><b> Request Details</b></h3>
                            </div>
                            <?php
                            if (!empty($message)) {
                                ?>
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-check"></i> <?php echo $message; ?></h4>
                                </div>
                            <?php } ?>
                <div class="box-body">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="requeststatus" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th> User Name</th>
                                        <th>Request By</th>
                                        <th>Request By Region</th>
                                        <th>CNIC</th>
                                        <th class="no-sort">Reason</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th class="no-sort" >Action</th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th> User Name</th>
                                        <th>Request By</th>
                                        <th>Request By Region</th>
                                        <th>CNIC</th>
                                        <th class="no-sort">Reason</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                </div>
                        </div>
                        </div>
                        </div>


            </div>

            </div>
        </div>
    </div>

    <div class="modal modal-info fade" id="modalupdatestatus">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Update Nadra Request Status</h4>
                </div>
                <div class="modal-body" style='background-color: #fff !important; color: black !important; '>
                    <div  style="display:block;margin-top: 5px;margin-bottom: 5px;" class="col-md-12">

                        <div class="col-md-12" style="background-color: #fff;color: black">
                            <hr class="style14 ">
                            <div class="form-group col-sm-6">
                                <label>Request ID:</label>
                                <input  class="form-control "type="text" id="request_status_update_id" name="request_id" value="" readonly>
                            </div>
                            <div class="form-group col-sm-12" >
                                <label> Status:</label>
                                <select  class="form-control" name="processing_index" id="processing_index">
                                <option value="0">Pending</option>
                                <option value="1">Processed</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-12" >
                                <hr class="style14 ">
                                <span id="" class="text-black" > </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                        <button type="button" onclick="UpdateRequestStatusDetails()" class="btn btn-primary ">Update</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
</section>

<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>
<script>
    $('#userrequest').one('submit', function () {
        $(this).find('input[type="submit"]').attr('disabled', 'disabled');
    });
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>
<script type="text/javascript">

    //additional changes
    var objDT;
    var objDT1;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        $("#rqtbyname").on("keyup", function () {
            var rqtbyname = $(this).val();
            if (rqtbyname !== "") {
                $.ajax({
                    url: "<?php echo URL::site("adminrequest/autocomplete_nadra"); ?>",
                    type: "POST",
                    cache: false,
                    data: {rqtbyname: rqtbyname},
                    success: function (data) {
                        $("#rqtbynamelist").html(data);
                        $("#rqtbynamelist").fadeIn();
                    }
                });
            } else {
                $("#rqtbynamelist").html("");
                $("#rqtbynamelist").fadeOut();
            }
        });
        $(document).on("click", "li", function () {
            $('#rqtbyname').val($(this).text());
            $('#rqtbynamelist').fadeOut("fast");
        });


        $('#dataform').hide();
        //subscriber
        var redirect_url = $("#redirect_url").val();
        var inputSubNO = $("#inputSubNO").val();
        var result = {redirect_url: redirect_url, inputSubNO: inputSubNO};

        // close intital request controller
        $("#userrequest").validate({
            rules: {
                cnic: {
                    required: true,
                    number: true,
                    maxlength: 13,
                    minlength: 13
                },
                rqtbyname: {
                    required: true,
                },
                date: {
                    required: true,
                },

                inputreason: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 5,
                    maxlength: 500
                }
            },
            messages: {
                cnic: {
                    required: "Enter CNIC Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 13 digits",
                    minlength: "Minimum 13 digits"
                },

                rqtbyname: {},
                inputreason: {
                    required: "Enter reason for request",
                    maxlenght: "Maximum character limit is 500",
                    minlength: "Min character limit is 10"
                }
            },
        });
        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .,/]+$/);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed.");
    });

    var today = new Date();
    $('#date').datepicker({
        endDate: "today",
        maxDate: today,
        autoclose: true
    });
    //user request form submit via ajax
    function submitrequestform() {
        var data = new FormData();
        var form_data = $('#userrequest').serializeArray();
        $.each(form_data, function (key, input) {
            data.append(input.name, input.value);
        });
        var files = $('#rqtfile')[0].files;
        data.append('file', files[0]);
        var url = $("#redirect_url").val();
        if ($('#userrequest').valid()) {
            $("#preloader").show();
            $.ajax({
                type: 'POST',
                url: "<?php echo URL::site('adminrequest/admin_familytree_send') ?>",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {
                    $("#preloader").hide();
                    if (result == 1) {
                        swal({
                                title: "Congratulations!",
                                text: "Record added successfully?",
                                type: "success",
                                showCancelButton: false,
                                confirmButtonClass: "btn-primary",
                                confirmButtonText: "OK",

                                closeOnConfirm: true,
                                closeOnCancel: true,
                            },
                            function (isConfirm) {
                                if (isConfirm) {
                                    window.location.href = '<?php echo URL::site('adminrequest/familytree_request_sent_form', TRUE); ?>';
                                } else {
                                    window.location.href = url;
                                }
                            });
                    } else {
                        swal("System Error", "Contact AIES Support Team.", "error");
                        // window.location.reload(false);
                    }
                },
                error: function (data) {
                    console.log("error");
                    console.log(result);
                }
            });
        }
    }

    $(document).ready(function () {

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

        objDT1 = $('#requeststatus').dataTable(
            {"aaSorting": [[5, "desc"]],
                "bPaginate": true,
                "bProcessing": true,
                //"bStateSave": true,
                "bServerSide": true,
                "sAjaxSource": "<?php echo URL::site('adminrequest/ajax_familytree_request_status', TRUE); ?>",
                "sPaginationType": "full_numbers",
                "bFilter": false,
                "bLengthChange": true,
                "oLanguage": {
                    "sProcessing": "Loading...",
                    "sSearch": "Search by CNIC No."
                },

                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }]
            }
        );
        $('.dataTables_empty').html("Information not found");

    });
    //function to open model for request status
    function UpdateRequestStatus(id,status) {
// alert('test');
        $("#modalupdatestatus").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');
        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open")
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second
            $(".modal-backdrop.fade.in").remove();
        }, 300);
        $("#request_status_update_id").val(id);
        // $("#cnic").val(cnic);

        $("#processing_index").val(status);
    }

    //function to open model for request status
    function UpdateRequestStatusDetails() {
        $("#modalupdatestatus").modal("hide");
        var request_id=    $("#request_status_update_id").val();
        // var request_cnic=    $("#cnic").val();
        var processing_index=    $("#processing_index").val();
        var result = {request_id: request_id,processing_index:processing_index}
        //ajax to upload device informaiton
        $.ajax({
            url: "<?php echo URL::site("adminrequest/update_familytree_request_status"); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (result) {
                if (result == -2) {

                    swal("System Error", "Contact Support Team.", "error");
                } else{
                    refreshGrid();
                }
            }
        });
    }


    $('#field').change(function () {
        $('#cnic').val('')
        $('#inputreason').val('')
        $('#date').val('')
        $('#reqbyregion').val('').change();
        $('#reqbydistrict').val('').change();

    });

    function region_district() {
        var region_id = $('#reqbyregion').val();
        var district = <?php echo (!empty($record['district_id']) ? $record['district_id'] : '0')?>;
        var searchresults = {region: region_id , district: district}
        $.ajax({
            url: "<?php echo URL::site("intprojects/region_district")?>",
            type: 'POST',
            data: searchresults,
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (data)
            {
                if(data== 2){
                    swal("System Error", "Contact Support Team.", "error");
                }
                $("#reqbydistrict").html(data);
            }
        });
    }
</script>
<style>
    #rqtbynamelist ul.list-unstyled {
        background-color: #def;
        padding: 10px;
        cursor: pointer;
    }
</style>


