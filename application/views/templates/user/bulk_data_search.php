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
        <i class="fa fa-search-plus"></i>
        Search Person
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Search Person</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <?php if (!empty($error_message)) { ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <?php echo $error_message; ?></h4>
                    </div>
                <?php } ?>
                <form name="bulk_data_search" id="bulk_data_search" action="<?php echo URL::site('user/bulk_data_search'); ?>" method="post" enctype="multipart/form-data">
                    <div class="box-body"> 
                        <div class="col-md-6">
                            <div class="form-group col-md-12">
                                <label ><u>Bulk Data Search Instructions!</u></label>
                                <p><u>1.</u> Please make sure that uploaded file is in excel format, sheet one, column A containing CNIC number without dashes.</p>
                                <p><u>2.</u> Excel file can also contain foreigner's CNIC numbers without dashes like 'DK72310106341'. <a target="NEW" href="<?php echo URL::site('User/bulksearch_instructions'); ?>" class=""> View Sample </a></p>
                                <p><u>3.</u> In case of error in uploaded file, try again after following above instructions.</p>                                
                            </div>
                            <hr class="style14 col-md-12"> 
                        </div>
                        <div class="col-md-6">
                            <div class="form-group col-sm-12">                               
                                <label for="number_type">Select File Type </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-mobile-phone"></i>
                                    </div>
                                    <select class="form-control" name="search_type" id="search_type">                                          
                                        <option <?php echo (( isset($search_post['search_type']) && ($search_post['search_type'] == '1')) ? 'selected' : ''); ?> value="1">CNIC Number</option>
                                    </select>
                                </div>                            
                            </div>                                                                
                            <div class="form-group col-sm-12">
                                <label for="data_file">File input</label>                                                
                                <input type="file" name="data_file" id="data_file" class="">
                                <p class="help-block">Upload file from here</p>
                            </div> 
                            <div class="form-group col-sm-12">
                                <button type="submit" class="btn btn-primary pull-right">Search Data</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->

                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> Search Results</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="searchperson" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th style="width: 15%">Person's Name</th>
                                        <th style="width: 15%">Father/Husband Name</th>
                                        <th class="no-sort" style="width: 10%">CNIC</th>
                                        <th class="no-sort" style="width: 10%">WMS (PID)</th>
                                        <th class="no-sort"  style="width: 8%">Category</th>
                                        <th class="no-sort"  style="width: 8%">Tags</th>
                                        <th class="no-sort"  style="width: 8%">Offices</th>
                                        <th class="no-sort"  style="width: 8%">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <th>Person's Name</th>
                                <th>Father/Husband Name</th>
                                <th>CNIC</th>
                                <th>WMS (PID)</th>
                                <th>Category</th>
                                <th>Tags</th>
                                <th>Offices</th>
                                <th>Detail</th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>

</section>
<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script>
//
$(function () {
    //Initialize Select2 Elements
    $(".select2").select2();
});
</script>
<script type="text/javascript">
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() !== "") {
            $("#msg_" + $("#msg_to_show").val()).show();
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
        objDT = $('#searchperson').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //  //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('user/ajaxbulkdatasearch', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": false,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        // "sSearch": "Search By Name, Father Name and CNIC:",
                        "sEmptyTable": "Information not found"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false
                        }],
                    dom: 'Bfrtip',
                    buttons: [
                        'pageLength','excel', 'pdf', 'print'
                    ]
                }
        );

        // $('.dataTables_empty').html("Information not found, <a href='<?php // echo URL::site('userrequest/request/1', TRUE);  ?>'> Please Request form Here </a>");


// validation for data search
        $("#bulk_data_search").validate({
            errorElement: 'span',
            rules: {
                search_type: {
                    required: true
                },
                data_file: {
                    required: true,
                    accept: "xls|xlsx|csv",
                    filesize: 10048576
                }
            },
            messages: {
                search_type: {
                    //alphanumericspecial: "Special Characters Not Allowed"
                },
                data_file: {
                    //alphanumericspecial: "Special Characters Not Allowed"
                },
                data_file: "File must be XLS, XLSX less than 2MB"
            },
            submitHandler: function () {
                $("#bulk_data_search").submit();

            }
            // $('#upload').show()
        });
                // Validators file size
        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param)
        });
        // Validators for custom value number or string
        jQuery.validator.addMethod("custominput", function (value, element) {
            //return this.optional(element) || value == params[0] + params[1];
            var cnic_no = $("#cnic").val();
            if (cnic_no != '') {
                if (jQuery("#is_foreigner").val() == 0)
                {
                    return ($.isNumeric($(element).val()));
                } else {
                    if (jQuery.type($(element).val()) === "string")
                        return true;
                    else
                        return false;
                }
            } else {
                return true;
            }
        }, jQuery.validator.format("Please enter the correct value"));


        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");

    });

    jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .,/]+$/);
    }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "" || sel == 0) {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>"); 
    
</script>