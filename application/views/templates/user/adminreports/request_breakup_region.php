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
        Requests Breakup Report 
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >Requests Breakup</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('Userreports/request_breakup_report'); ?>">
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">                                                           
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
                                    <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;">
                                        <!--<i class="fa fa-file-excel-o"></i>-->  
                                        Export</a>
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
                        if (!empty($search_post)) {
                            $duration_flag = $search_post['startdate'] . " TO " . $search_post['enddate'];
                        } else {
                            $duration_flag = " ALL ";
                        }
                        ?>
                        <h3 class="box-title"><i class="fa fa-list"></i> Requests Breakup (Region) <u> <?php echo ( $duration_flag ); ?> </u> </h3>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="telcoreports" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                        
                                        <th >Region</th>
                                        <th class="no-sort">Total Requests</th>
                                        <th class="no-sort">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Region</th>
                                        <th>Total Requests</th>
                                        <th class="no-sort">Action</th>
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
<div class="modal modal-info fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Verisys Response Breakup</h4>
            </div>
            <form class="" name="acl_form" action="<?php echo url::site() . 'userreports/access_control_form' ?>" id="acl_form" method="post">
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Region Name</th>
                                    <th>District Name</th>
                                    <th>Date</th>
                                    <th>Total Requests</th>                                  
                                    <th>Total Response</th>                                  
                                </tr>
                            </thead>
                            <tbody id="verisys_response_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>
            </form>         
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2({
         placeholder: "Please select one or Multiple Company Name"   
        });
    });
</script>
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
                    "sAjaxSource": "<?php echo URL::site('Userreports/ajaxrequestbreakupreport', TRUE); ?>",
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
        window.location.href = '<?php echo URL::site('Userreports/request_breakup_report', TRUE); ?>';
    }
    function excel(id){          
            $('#xport').val('excel');
            $('#search_form').submit();            
            $('#xport').val('');
    }
   function regionbreakup(region,date2) {
        if (region !== 0) {
            var request = $.ajax({
                url: "<?php echo URL::site("Adminreports/verisys_response_details"); ?>",
                type: "POST",
                dataType: 'html',
                data: {region: region, date: date2},
                success: function (response)
                {                    
                if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}    
                    $("#modal-default").modal("show");

                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');

                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);


                    $("#verisys_response_data").html(response);

                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    
        function excel(id){          
            $('#xport').val('excel');
            $('#search_form').submit();            
            $('#xport').val('');
    }
    
</script> 
