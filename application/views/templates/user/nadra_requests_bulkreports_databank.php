<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//echo '<pre>';
//print_r($post_data);
//exit();
//$uid=$user_id;
//$user_name=$post_data['user_name'];
//$date_from=$post_data['datefrom'];
//$date_to=$post_data['dateto'];

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
                <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('admindatabank/nadra_requests_reports_databank'); ?>" enctype="multipart/form-data" >
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post) && (empty($search_post['message']) || $search_post['message'] != 1)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-md-12">

                            <div class="form-group">
                                <div class="col-sm-4" >
                                    <div class="form-group">
                                        <label class="control-label">Uploaded by</label>
                                        <input type="text" class="form-control" name="user_name" id="user_name"
                                               placeholder="Enter name" value="<?php echo (!empty($search_post['user_name']) ? $search_post['user_name'] : ''); ?>" >
                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="searchfield">Start Date (mm/dd/yyyy)</label>
                                        <input type="text"  placeholder="mm/dd/yyyy" class="form-control" name="startdate" id="startdate" value="<?php echo (!empty($search_post['startdate']) ? $search_post['startdate'] : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label title="If not selected, Today is default Date" for="searchfield">End Date (mm/dd/yyyy)</label>
                                        <input title="If not selected, Today is default Date" type="text"  placeholder="mm/dd/yyyy" class="form-control" name="enddate" id="enddate" value="<?php echo (!empty($search_post['enddate']) ? $search_post['enddate'] : ''); ?>">
                                    </div>
                                </div>



                            </div>
                        </div>

                        <!-- /.col -->
                        <div class="col-md-12">
                            <div class="form-group pull-right">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                            </div>
                        </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>

    </div>



    <div class="container-fluid">

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
                    "sAjaxSource": "<?php echo URL::site('admindatabank/ajaxdatabanknadrarequestsreports', TRUE); ?>",
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
    $('#startdate').datepicker({
        autoclose: true
    });
    //Date picker
    $('#enddate').datepicker({
        autoclose: true
    });

    function clearSearch() {
        window.location.href = '<?php echo URL::site('admindatabank/nadra_requests_reports_databank', TRUE); ?>';
    }

</script>