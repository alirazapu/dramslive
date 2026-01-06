<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$uid=$user_id;
$post_country='';
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-files-o"></i>
        Bulk Verisys Response
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Bulk Verisys Response</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form role="form" name="verisys_temp_upload" id="verisys_temp_upload" class="ipf-form" method="POST" action="<?php echo URL::site('admindatabank/nadra_requests_temp_upload_databank'); ?>" enctype="multipart/form-data" >
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-search"></i> Bulk Verisys Response</h3>
<!--                            <a title="Sync Verisys With Requests" href="javascript:syncverisyswithrequests()" class="btn btn-success btn-small" style="float: right;"><i class="fa fa-refresh"></i>  Sync Verisys With Requests</a>-->
                        </div>
                        <div class="box-body" style="display:block;">
                            <div class="row">
                            <div class="col-md-12">
                                <div class="col-sm-4" >
                                    <div class="form-group">
                                        <label class="control-label">CNIC No.<span class="text-danger">(*)</span></label>
                                        <input type="text" class="form-control" name="cnic_number" id="cnic_number"
                                               placeholder="Cnic No.">
                                    </div>
                                </div>
                                <div class="col-sm-4" >
                                    <div class="form-group">
                                        <label class="control-label">First Name</label>
                                        <input type="text" class="form-control" name="first_name" id="first_name"
                                               placeholder="Enter name">
                                    </div>
                                </div>
                                <div class="col-sm-4" >
                                    <div class="form-group">
                                        <label class="control-label">Middle Name</label>
                                        <input type="text" class="form-control" name="middle_name" id="middle_name"
                                               placeholder="Enter middle name">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-4" >
                                    <div class="form-group">
                                        <label class="control-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" id="last_name"
                                               placeholder="Enter last name">
                                    </div>
                                </div>
                                <div class="col-sm-4" >
                                    <div class="form-group">
                                        <label class="control-label">Father Name</label>
                                        <input type="text" class="form-control" name="father_name" id="father_name"
                                               placeholder="Enter father name">
                                    </div>
                                </div>
                                <div class="col-sm-4" >
                                    <div class="form-group">
                                        <label class="control-label"> Address</label>
                                        <input type="text" class="form-control" name="address" id="address"
                                               placeholder="Enter address">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-8" id="project_div" style="margin-top: 10px; " >
                                    <div class="form-group" >
                                        <label for="inputproject" class="control-label">Linked Project</label>
                                        <?php $projects_data = Helpers_Utilities::get_projects_list(); ?>
                                        <select class="form-control select2" name="project_id[]" id="project_id" dataplaceholder="please select project" style="width: 100%!important;">
                                            <option value="">Please select project name</option>
                                            <?php foreach ($projects_data as $project) {
                                                $region_district = Helpers_Requests::get_project_region_district($project->region_id, $project->district_id); ?>
                                                <option  value="<?php echo $project->id; ?>"><?php echo $project->project_name . $region_district; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-4" >
                                    <label for="cnic_is_foreigner" class="control-label">Country</label>
                                    <select class="form-control" name="is_foreigner" id="is_foreigner">
                                        <option  <?php if ($post_country == 0) { echo "selected";  } ?> value="0">Pakistan</option>
                                        <option  <?php if ($post_country == 1) {  echo "selected";   }  ?> value="1">Foreign</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-sm-12" id="reason_div">
                                    <div class="form-group">
                                        <label for="inputreason" class="control-label">Reason For This Request<span class="text-danger">(*)</span></label>
                                        <textarea class="form-control" name="reason" id="reason"
                                                  placeholder="Enter Reason For Request"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group pull-right">
                                        <label for="personverysis">Upload<small>   (JPG,GIF and PNG files only)<span class="text-danger">(*)</span></small></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">                                            
                                        <input  type="file" accept=".jpg,.gif,.png" id="personverysis" name="personverysis" placeholder="Select Image">
                                    </div>                                       
                                </div>
                                <div class="col-md-4 pull-left">
                                    <button type="submit"  class="btn btn-primary ">Upload Files</button>
                                </div>
                            </div>
                    </div>
                        </div>        
                    </div>
                </form>
                <!-- /.box -->                            
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">                    
                    <div class="box-body">
                        <div class="table-responsive-11">
                            <table id="temp_verisys" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                   
                                        <th class="no-sort">Image Name</th>
                                        <th class="no-sort">CNIC</th>  
                                        <th class="no-sort">Uploaded By</th>                                    
                                        <th>Uploaded Date</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>                                     
                                        <th>Image Name</th>
                                        <th>CNIC</th>  
                                        <th>Uploaded By</th>                                    
                                        <th>Uploaded Date</th>                                    

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

        $("#verisys_temp_upload").validate({
            rules:
                {
                "personverysis": {
                    required: true,
                    accept: "jpg,jpeg,gif,png",
                    filesize: 900000,
                },
                cnic_number: {
                    required: true,
                    minlength: {
                        param:13,
                        depends: function(element) {
                            return ($("#cnic_number").val() != 0);
                        }
                    },
                    maxlength: {
                        param:13,
                        depends: function(element) {
                            return ($("#cnic_number").val() != 0);
                        }
                    },

                },
                reason: {
                    required: true,
                    minlength: {
                        param:15,
                        depends: function(element) {
                            return ($("#reason").val() != 0);
                        }
                    },


                },
                    "project_id[]":{
                        required: {
                            depends: function(element){
                                if('none' == $('#project_id').val()){
                                    //Set predefined value to blank.
                                    $('#project_id').val('');
                                }
                                return true;
                            }
                        }
                    }
                    // project_id: {
                    //     required: {
                    //         depends: function(element) {
                    //             return $("#project_id").val() == "none";
                    //         }
                    //     }
                    // },
                //     project_id: {
                //     required: true,
                // },
            },
            messages: {
                "personverysis": {
                    required: "File Required",
                },
                cnic_number: {
                   required: "Entered 13 Digit Cnic number",
                },
                reason: {
                   required: "Enter reason",
                },
                // project_id: {
                //     required: "Please select an option from the list, if none are appropriate please select 'Unknown[Head Quarters]'",
                // },
                // project_id: {
                //    required: "Select Project ",
                // },
            }

        });

        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        }, 'File size must be less than {0} Kb');

        // jQuery.validator.addMethod("reason", function(value, element) {
        //     // allow any non-whitespace characters as the host part
        //     return this.optional( element ) || /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@(?:\S{1,63})$/.test( value );
        // }, 'Please enter a reason.');


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
        objDT = $('#temp_verisys').dataTable(
                {//"aaSorting": [[3, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "mark": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('admindatabank/ajaxdatabanknadrarequestsbulk?uid='.$uid, TRUE); ?>",
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

    //verisys_temp_upload submit through ajax call
    $('#verisys_temp_upload').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        if ($('#verisys_temp_upload').valid())
        {
            $("body").addClass("loading");
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (msg) {

                    $("#verisys_temp_upload")[0].reset();
                    refreshGrid();
                    $("body").removeClass("loading");
                    if (msg == '2') {
                        swal({
                            title: "Info",
                            text: "Cnic and Nadra Verirsys already exist.",
                            type: "warning"
                        });
                        //swal("Info", "Cnic and Nadra Verirsys already exist.", "alert");
                    }
                    // else { //error in any one file
                    //     swal("oops!", "All Files Not Uploaded Successfully.", "warning");
                    // }
                    if (msg == 3)
                    {
                        swal("Success ", "Cnic exist and Nadra Verisys uploaded.", "success");
                    }
                    if (msg == 4)
                    {
                        swal("Success ", "Cnic added and Nadra Verisys uploaded.", "success");
                    }
                    if (msg == 5)
                    {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    $("body").removeClass("loading");

                },
            });
        }
    });

    function syncverisyswithrequests() {
        $("body").addClass("loading");
            $.ajax({
                url: "<?php echo URL::site("admindatabank/sync_temp_uploaded_verisys"); ?>",
                cache: false,
                //dataType: "text",
                dataType: 'html',
                success: function (msg) {
                    if (msg == '-2')
                    {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    refreshGrid();
                }
            });            
        $("body").removeClass("loading");
    }
    //delete Request not found entry
    function delete_record(id) {
        $.confirm({
            'title': 'Delete Record confirmation',
            'message': 'Do you really want to delete this request ?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::base() . "Userrequest/delete_temp_verisys_record/"; ?>' + id,
                            success: function (result) {
                                if (result == -2) {
                                    swal("System Error", "Contact Support Team.", "error");
                                } else {
                                    refreshGrid();
                                }
                            }});

                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
</script>