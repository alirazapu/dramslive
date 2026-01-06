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
        <i class="fa fa-files-o"></i>
        Travel History Requests
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Travel History Requests</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
        <div class="box box-primary">
            <div class="box">                 
                <div class="form-group col-md-12 " >
                    <div class="alert-dismissible notificationclosereports" id="notification_msgreports" style="display: none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divreports"></div></h4>
                    </div>
                </div>
                <?php
                if (!empty($message)) {
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <?php echo $message; ?></h4>
                    </div>
                <?php } ?>

                <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userrequest/travelhistory_requests'); ?>" >
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-search"></i> Advanced Search</h3>
                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">
                            <div class="col-md-12">
                                <div class="col-md-4 posting_acl">
                                    <div class="form-group">
                                        <label for="region">Region</label>
                                        <select onclick="clearoptions(1)" class="form-control select2" multiple="multiple" id="region" name="region[]" style="width: 100%;">
                                            <option value="">Please Select Region</option>                                            
                                            <?php
                                            try {
                                                $login_user = Auth::instance()->get_user();
                                                $DB = Database::instance();
                                                $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
                                                $region_id = $login_user_profile->region_id;
                                                $district_id = $login_user_profile->district_id;
                                                $posting = $login_user_profile->posted;
                                                $permission = Helpers_Utilities::get_user_permission($login_user->id);

                                                if (!empty($region_id)) {
                                                    $region_data = Helpers_Utilities::get_region($region_id);
                                                    ?>
                                                    <option <?php echo (!empty($search_post['region']) && in_array($region_id, $search_post['region'])) ? 'Selected' : ''; ?> value="<?php echo $region_id ?>"><?php echo $region_data ?></option>
                                                    <?php
                                                }
                                                if (empty($region_id) || ($permission == 1 || $permission == 2)) {
                                                    $region_data = Helpers_Utilities::get_region();
                                                    foreach ($region_data as $region_list) {
                                                        if ($region_list->region_id != $region_id) {
                                                            ?>
                                                            <option <?php echo (!empty($search_post['region']) && in_array($region_list->region_id, $search_post['region'])) ? 'Selected' : ''; ?> value="<?php echo $region_list->region_id ?>"><?php echo $region_list->name ?></option>
                                                        <?php
                                                        }
                                                    }
                                                }
                                            } catch (Exception $ex) {
                                                
                                            }
                                            ?>                                                    
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 posting_acl">
                                    <div class="form-group">
                                        <label for="user">User</label>
                                        <select onclick="clearoptions(2)" class="form-control select2" multiple="multiple" id="user" name="user[]" style="width: 100%;">
                                            <option value="">Please Select User</option>                                                       
                                            <?php
                                            try {
                                                $users_data = Helpers_Utilities::get_users_list_with_posting($posting);
                                                foreach ($users_data as $users_list) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['user']) && in_array($users_list->user_id, $search_post['user'])) ? 'Selected' : ''; ?> value="<?php echo $users_list->user_id ?>"><?php echo $users_list->name . " (" . $users_list->job_title . " " . $users_list->belt . ")" ?></option>
        <?php
    }
} catch (Exception $ex) {
    
}
?>                                                     
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Travel History Request Status</label>
                                        <div class="radio radio-primary" style="margin-left: 20px">                                            
                                            <input type="radio" <?php echo ((!empty($search_post['e_status']) && ($search_post['e_status'] == 3)) || !isset($search_post['e_status']) ) ? 'checked' : ''; ?>  name="e_status" id="e_status_4" value="3">
                                            <label for="e_status_4" style="padding-left: 2px; margin-right: 25px">
                                                All
                                            </label>                                            
                                            <input type="radio" <?php echo (!empty($search_post['e_status']) && ($search_post['e_status'] == 2)) ? 'checked' : ''; ?> name="e_status" id="e_status_1" value="2">
                                            <label for="e_status_1" style="padding-left: 2px; margin-right: 25px">
                                                Completed
                                            </label>
                                            <input type="radio" <?php echo (isset($search_post['e_status']) && ($search_post['e_status'] == 1)) ? 'checked' : ''; ?> name="e_status" id="e_status_3" value="1">
                                            <label for="e_status_3" style="padding-left: 2px; margin-right: 25px">
                                                Pending
                                            </label>
                                        </div>
                                    </div>          
                                </div>

                            </div>  
                            <!-- /.col -->
                            <div class="col-md-12">
                                <div class="form-group pull-right">
                                    <button   type="submit" class="btn btn-primary">Search</button>
                                    <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                    <input id="xport" name="xport" type="hidden" value="" />
                                </div>
                            </div>

                            <!-- /.col -->
                            <!-- /.row -->
                        </div>        
                    </div>
                </form>
                <!-- /.box -->                            
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <div class="col-xs-4">
                            <h3 class="box-title"><i class="fa fa-list"></i> Travel History Request</h3>
                        </div>
                        <div class="col-xs-4">
                            <a title="Upload Bulk Verisys" href="javascript:uploadbulktravelhistory()" class="btn btn-primary btn-small" style="float: right;"><i class="fa fa-upload"></i>  Upload Bulk Travel History</a>
                        </div>
                        <div class="col-xs-4">
                            <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export Peding Request's CNIC</a>
                        </div>
                    </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive-11">
                    <table id="requeststatus" class="table table-bordered table-striped">
                        <thead>
                            <tr>                                   
                                <th>User</th>    
                                <th>Region</th>  
                                <th class="no-sort">Request Type</th>                                    
                                <th class="no-sort">Requested Value</th>                                    
                                <th >Person</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="no-sort" >Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>                                     
                                <th>User</th> 
                                <th>Region</th>  
                                <th>Request Type</th>
                                <th>Requested Value</th> 
                                <th>Person</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
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
<div class="modal modal-info fade" id="process_travelhistory">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Proceed Travel History Request</h4>
            </div>
            <form class="" name="travelhistoryfile" id="travelhistoryfile" action="<?php echo url::site() . 'personprofile/update_persontravelhistory/' ?>"  method="post" enctype="multipart/form-data" >  

                <div class="modal-body" style='background-color: #fff !important; color: black !important; '>
                    <!--Manual Upload CDR Against IMEI-->
                    <div id="manualuploadimeicdr" style="display:block;margin-top: 5px;margin-bottom: 5px;" class="col-md-12">                                            
                        <div id="loader-div" style='background-image: url("<?php echo URL::base(); ?>dist/img/loader.gif"); width:100%; height:11px; background-position:center; display:none'></div>
                        <div class="col-md-12" style="background-color: #fff;color: black">
                            <hr class="style14 ">
                            <div class="form-group col-sm-12">                                 
                                <label>Upload Image Against This CNIC:</label>
                                <input  class="form-control " type="hidden" id="process_request_id" name="process_request_id" value="" readonly>                                                  
                                <input  class="form-control " type="hidden" id="process_project_id" name="process_project_id" value="" readonly>                                                  
                                <input  class="form-control " type="hidden" id="userid" name="userid" value="" readonly>                                                  
                                <input  class="form-control " type="text" id="cnic_number" name="cnic_number" value="" readonly>                                                  
                            </div>                                    
                            <div class="form-group col-md-12"> 
                                <label for="travelhistoryfile">Choose File<small>   (JPG,GIF,PDF and PNG files only)</small></label>
                                <input type="file" accept=".jpg,.gif,.png,.pdf" id="travelhistoryfile" name="travelhistoryfile" placeholder="Select Image">  
                            </div> 
                            <div class="form-group col-sm-12" >
                                <hr class="style14 ">
                            </div>
                            <!--<div class="form-group col-sm-12"></div>-->
                            <span id="" class="text-black" > </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                    <button type="submit"  class="btn btn-primary ">Upload</button>
                </div>  
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>
<!-- /.content -->
<script type="text/javascript">
    /* For nadra verisis request processing */
    function findphonenumber(cnic, requestid, projectid, userid) {

        $("#cnic_number").val(cnic);
        $("#process_request_id").val(requestid);
        $("#process_project_id").val(projectid);
        $("#userid").val(userid);
        $("#process_travelhistory").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);

    }

    //table 
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        //validate Person Verisis

        $("#travelhistoryfile").validate({
            rules: {
                personverysis: {
                    required: true,
                    accept: "jpg,jpeg,gif,png",
                    filesize: 900000,
                },
            },
            messages: {
                personverysis: {
                    required: "File Required",
                },
            }

        });

        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        }, 'File size must be less than {0} Kb');

        //table data
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
        objDT = $('#requeststatus').dataTable(
                {"aaSorting": [[4, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "mark": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userrequest/ajaxusertravelhistory', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By User Name, Request ID or Requested Value:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });
// request update
    $(document).ready(function (e) {
        $('#travelhistoryfile').on('submit', (function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            if ($('#travelhistoryfile').valid())
            {
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        document.getElementById("travelhistoryfile").reset();
                        $("#process_travelhistory").modal("hide");
                        if (data == 1) {
                            swal("Congratulations!", "Request Responded successfully.", "success");
                        }else{
                            swal("System Error", "Contact Support Team.", "error");                            
                        }                        
                        //$("#notification_msg_divreports").html('Successfully Updated');
                        //$("#notification_msgreports").show();
                        //$("#notification_msgreports").addClass('alert');
                        //$("#notification_msgreports").addClass('alert-success');
//                        var elem = $(".notificationclosereports");
//                        elem.slideUp(10000);                        
                        refreshGrid();
                    },
                    error: function (data) {
                        console.log("error");
                        console.log(data);
                    }
                });
            }
        }));

//    $("#ImageBrowse").on("change", function() {
//        $("#imageUploadForm").submit();
//    });

    });
    function clearoptions(value) {

        if (value == 1) {
            $("#region").select2().val('');
        } else {
            $("#user").select2().val('');
        }
    }

    function clearSearch() {
        window.location.href = '<?php echo URL::site('userrequest/nadra_requests', TRUE); ?>';
    }
    function excel(){    
            $('#xport').val('excel');
            $('#search_form').submit();            
            $('#xport').val('');
    }
    function uploadbulktravelhistory(){   
        window.location.href = '<?php echo URL::site('userrequest/bulk_travelhistory', TRUE); ?>';
    }
    
    function requeueverisys(request_id){
        //var stat = $("#stcng").val(); 
        var result = {request_id : request_id}
        $.ajax({
            url: "<?php echo URL::site("Userrequest/request_requeue_verisys"); ?>",
            type: 'POST',
            data: result,
            cache: false,
            //dataType: "text",
            success: function (msg) {
                refreshGrid();
                if (msg == 1)
                {
                    swal("Congratulations!", "Request Moved To Queue.", "success");                    
                }else{
                    swal("System Error", "Contact Support Team.", "error");
                }                
            }
        });
    }




</script>