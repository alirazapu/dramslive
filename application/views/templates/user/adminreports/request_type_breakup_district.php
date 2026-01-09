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
        <i class="fa fa-wifi"></i>
        Request Types Breakup Report 
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >Request Types Breakup</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('Userreports/request_type_breakup_district/?id='.$_GET['id']); ?>">
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post['startdate'])) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post['startdate'])) ? 'display:block;' : ''; ?>">                                                           
                            <div id="blocktohide">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="searchfield">Start Date (mm/dd/yyyy)</label>
                                        <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="startdate" id="startdate" value="<?php echo (!empty($search_post['startdate']) ? $search_post['startdate'] : ''); ?>">                                        
                                    </div>
                                </div>
                                <!-- /.col -->
                                <!-- /.col -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="searchfield">End Date (mm/dd/yyyy)</label>
                                        <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="enddate" id="enddate" value="<?php echo (!empty($search_post['enddate']) ? $search_post['enddate'] : ''); ?>">
                                    </div>
                                </div>
                                <!-- /.col -->
                                <!-- /.col -->
                            </div>
                            
                            <div class="col-md-4 verisysreport">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                    <input id="xport" name="xport" type="hidden" value="" />
                                </div>
                            </div>
                          </div>      
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <!-- Request duration flag-->
                        <?php
                        if (!empty($search_post['startdate']) && !empty( $search_post['enddate']) ) {
                            $duration_flag = $search_post['startdate'] . " TO " . $search_post['enddate'];
                        } else {
                            $duration_flag = " ALL ";
                        }
                        ?>
                        <h3 class="box-title"><i class="fa fa-list"></i> Request Types Breakup (District) <u> <?php echo ( $duration_flag ); ?> </u> </h3>
                       
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="telcoreports" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                        
                                        <th >Region</th>
                                        <th >Office</th>
                                        <th class="no-sort">Request Types</th>
                                        <th class="no-sort">Total</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Region</th>
                                        <th >Office</th>
                                        <th class="no-sort">Request Types</th>
                                        <th class="no-sort">Total</th>
                                    </tr>
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
<div style="display:none" id="div-dialog-warning">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><div/></p>
</div>
</section>

<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>


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
        objDT = $('#telcoreports').dataTable(
                {"aaSorting": [[0, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('Userreports/ajaxrequesttypebreakupreportdist', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": false,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Company Name or Total Request"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });
   $("#search_form").validate({
        rules: {
            startdate: {
                required: true,
                sdate_value: true,
            },
            enddate: { 
                required: true,
                edate_value: true,
            },
        },
        messages: {
            startdate: {
                sdate_value: "Select Start Date",
            },
            etartdate: {
                edate_value: "Select End Date",
            },
        }
    });

     $.validator.addMethod("sdate_value", function (sel, element) {
        if ($('#field').val() == "date" && $('#startdate').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("edate_value", function (sel, element) {
        if ($('#field').val() == "date" && $('#enddate').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");
//Date picker
    $('#startdate').datepicker({
      autoclose: true
    });
//Date picker
    $('#enddate').datepicker({
      autoclose: true
    });
 
    function clearSearch() {
        window.location.href = '<?php echo URL::site('Userreports/request_type_breakup_district/?id='.$_GET['id'], TRUE); ?>';
    }
    
       
</script> 
