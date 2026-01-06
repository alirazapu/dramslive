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
        Bulk Travel History Response
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Bulk Travel History Response</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form role="form" name="verisys_temp_upload" id="verisys_temp_upload" class="ipf-form" method="POST" action="<?php echo URL::site('Personprofile/travelhistory_requests_temp_upload'); ?>" >
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-search"></i> Bulk Travel History Response</h3>
                            <a title="Sync Travel History With Requests" href="javascript:syncthwithrequests()" class="btn btn-success btn-small" style="float: right;"><i class="fa fa-refresh"></i>  Sync Travel History With Requests</a>
                        </div>
                        <div class="box-body" style="display:block;">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <div class="form-group pull-right">
                                        <label for="personverysis">Upload<small>   (JPG,GIF,PDF and PNG files only)</small></label>
                                    </div>
                                </div>
                                <div class="col-md-4">                                        
                                    <div class="form-group">                                            
                                        <input multiple type="file" accept=".jpg,.gif,.png,.pdf" id="travelhistoryfiles" name="travelhistoryfiles[]" placeholder="Select Image">                                              
                                    </div>                                       
                                </div>
                                <div class="col-md-4 pull-left">
                                    <button type="submit"  class="btn btn-primary ">Upload Travel History Files</button>
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
                            <table id="temp_travelhistory" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                   
                                        <th class="no-sort">Image Name</th>    
                                        <th class="no-sort">CNIC</th>  
                                        <th class="no-sort">Uploaded By</th>                                    
                                        <th>Uploaded Date</th>                                    
                                        <th>Attachment Status</th>
                                        <th class="no-sort" >Action</th>
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
                                        <th>Attachment Status</th>
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
            rules: {
                "personverysis[]": {
                    required: true,
                    accept: "jpg,jpeg,gif,png",
                    filesize: 900000,
                },
            },
            messages: {
                "personverysis[]": {
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
        objDT = $('#temp_travelhistory').dataTable(
                {"aaSorting": [[3, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "mark": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userrequest/ajaxbulktravelhistory', TRUE); ?>",
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
    //
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
                    if (msg == undefined || msg == null || msg.length == 0 || msg) {
                        swal("Congratulations!", "All Files Uploaded Successfully.", "success");
                    } else { //error in any one file
                        swal("oops!", "All Files Not Uploaded Successfully.", "warning");
                    }
                    if (msg == 2)
                    {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                    $("body").removeClass("loading");

                },
            });
        }
    });

    function syncthwithrequests() {
        $("body").addClass("loading");
            $.ajax({
                url: "<?php echo URL::site("Verisyssync/sync_temp_uploaded_travelhistory"); ?>",
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
                        $.ajax({url: '<?php echo URL::base() . "Userrequest/delete_temp_travelhistory_record/"; ?>' + id,
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