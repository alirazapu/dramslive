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
        Telco Reports 
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >Telco Reports</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userreports/telco_reports'); ?>">
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
                           
                                <div class="col-md-4 ">
                                    <div class="form-group">
                                        <label for="companyname">Select Company</label>
                                        <select class="form-control select2" multiple="multiple" id="companyname" name="companyname[]" style="width: 100%;">
                                                <?php try{
                                                $companies = Helpers_Utilities::get_companies_data();
                                                foreach ($companies as $list) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['companyname']) && in_array($list->mnc, $search_post['companyname'])) ? 'Selected' : ''; ?> value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                                <?php }
                                                }
                                                catch (Exception $ex){

                                                    /*$this->template->content = View::factory('templates/user/telco_reports')
                                                        ->bind('exception', $ex);*/

                                                }?>
                                        </select>
                                    </div>
                                </div>
                            <div id="blocktohide">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="searchfield">Start Date (mm/dd/yyyy)</label>
                                        <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="startdate" id="startdate" value="<?php echo (!empty($search_post['startdate']) ? $search_post['startdate'] : ''); ?>" required>
                                    </div>
                                </div>
                                <!-- /.col -->
                                <!-- /.col -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="searchfield">End Date (mm/dd/yyyy)</label>
                                        <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="enddate" id="enddate" value="<?php echo (!empty($search_post['enddate']) ? $search_post['enddate'] : ''); ?>" required>
                                    </div>
                                </div>
                                <!-- /.col -->
                                <!-- /.col -->
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                    <input id="xport" name="xport" type="hidden" value="" />
                                </div>
                            </div>
                            <!-- /.col -->
                            <!-- /.row -->
                          </div>      
                    </div>




                </form>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-list"></i> Telco Reports Types</h3>
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="telcoreports" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                        
                                        <th>Date</th>
                                        <th>Company Name</th>
                                        <th class="no-sort" title="High Priority Requests" >High</th>
                                        <th class="no-sort" title="Medium Priority Requests" >Medium</th>
                                        <th class="no-sort" title="Low Priority Requests" >Low</th>
                                        <th  >Total</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Date</th>
                                        <th>Company Name</th>
                                        <th title="High Priority Requests" >High</th>
                                        <th title="Medium Priority Requests" >Medium</th>
                                        <th title="Low Priority Requests" >Low</th>
                                        <th  >Total</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- /.box-body -->
                </div>
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-list"></i>Total Stats </h3>
                   <!-- --><?php
/*                    $post= Session::instance()->get('telco_reports_post');


                    echo  $post['total']*/?>

                    <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="telcoreportstotal" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Company Name</th>
                               <!-- <th class="no-sort" title="High Priority Requests" >High</th>
                                <th class="no-sort" title="Medium Priority Requests" >Medium</th>
                                <th class="no-sort" title="Low Priority Requests" >Low</th>-->
                                <th  >Total</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Date</th>
                                <th>Company Name</th>
                                <!--<th title="High Priority Requests" >High</th>
                                <th title="Medium Priority Requests" >Medium</th>
                                <th title="Low Priority Requests" >Low</th>-->
                                <th  >Total</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
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
                    "sAjaxSource": "<?php echo URL::site('userreports/ajaxtelcoreports', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
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


        objDT = $('#telcoreportstotal').dataTable(
            {"aaSorting": [[0, "desc"]],
                "bPaginate": true,
                "bProcessing": true,
                //"bStateSave": true,
                "bServerSide": true,
                "sAjaxSource": "<?php echo URL::site('userreports/ajaxtelcoreportstotal', TRUE); ?>",
                "sPaginationType": "full_numbers",
                "bFilter": true,
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
            "companyname[]": {
             //  check_list: true,

            },
            startdate: {
                sdate_value: true,
            },
            enddate: {                
                edate_value: true,
            },
        },
        messages: {
            "companyname[]": {
                check_list: "Please select company",
            },
            startdate: {
                sdate_value: "Select Start Date",
            },
            etartdate: {
                edate_value: "Select End Date",
            },
        }
    });
    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

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
 function showDiv(elem) {
        if (elem.value == 'def')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem.value == 'name')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem.value == 'designation')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //enable
            // $('#searchfield').removeAttr("disabled");
        } else if (elem.value == 'posting')
        {
            //show
            document.getElementById('posting-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
        }
    }
    function clearSearch() {
        window.location.href = '<?php echo URL::site('userreports/telco_reports', TRUE); ?>';
    }
    function excel(id){          
            $('#xport').val('excel');
            $('#search_form').submit();            
            $('#xport').val('');
    }
   
    
</script> 
