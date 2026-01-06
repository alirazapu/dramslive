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
        <i class="fa fa-user" ></i>
        User's Report
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li></i>User's Report</a></li>
        <li class="active"></i>Performance Report</li>
    </ol>

</section>






<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="box">
        <form id="search_form1" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userreports/performance_report'); ?>">
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="field">Select Type</label>
                            <select class="form-control " name="field" id='field' onchange="showDiv(this)">
                                <option value="def"> Please Select Type</option>
                                <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'username')) ? 'selected' : ''); ?> value="username"> User Name</option>
                                <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'designation')) ? 'selected' : ''); ?>  value="designation"> Designation</option>
                                <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'posting')) ? 'selected' : ''); ?> value="posting"> Posting</option>

                            </select>
                        </div>
                    </div>


                    <div id="posting-hide">
                        <div class="col-md-6 posting_acl">
                            <div class="form-group">
                                <label for="posting">Select Posting</label>
                                <select class="form-control select2" multiple="multiple" id="posting" name="posting[]" style="width: 100%;">
                                    <option value="">Please Select Posting</option>
                                    <optgroup label="Region">
                                        <?php try{
                                            $region_list = Helpers_Utilities::get_region();
                                            foreach ($region_list as $list) {
                                                ?>
                                                <option <?php echo (!empty($search_post['posting']) && in_array('r-' . $list->region_id, $search_post['posting'])) ? 'Selected' : ''; ?> value="r-<?php echo $list->region_id ?>"><?php echo $list->name ?></option>
                                            <?php }
                                        }  catch (Exception $ex){   }?>
                                        <optgroup label="District">
                                            <?php try{
                                                $district_list = Helpers_Utilities::get_district();
                                                foreach ($district_list as $list) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['posting']) && in_array('d-' . $list->district_id, $search_post['posting'])) ? 'Selected' : ''; ?> value="d-<?php echo $list->district_id ?>"><?php echo $list->name ?></option>
                                                <?php }
                                            }  catch (Exception $ex){   }?>
                                            <optgroup label="Police Station">
                                                <?php try{
                                                    $police_station_list = Helpers_Utilities::get_police_station();
                                                    foreach ($police_station_list as $list) {
                                                        ?>
                                                        <option <?php echo (!empty($search_post['posting']) && in_array('p-' . $list->id, $search_post['posting'])) ? 'Selected' : ''; ?> value="p-<?php echo $list->id ?>"><?php echo $list->name ?></option>
                                                    <?php }
                                                }  catch (Exception $ex){   }?>
                                                <optgroup label="Head Quarter">
                                                    <?php try{
                                                        $headquarter_list = Helpers_Utilities::get_headquarter();
                                                        foreach ($headquarter_list as $list) {
                                                            ?>
                                                            <option <?php echo (!empty($search_post['posting']) && in_array('h-' . $list->id, $search_post['posting'])) ? 'Selected' : ''; ?> value="h-<?php echo $list->id ?>"><?php echo $list->name ?></option>
                                                        <?php }
                                                    }  catch (Exception $ex){   }?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="utype">
                  
                    </div>

                    <!-- /.col -->
                    <div id="key-hide">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="searchfield">Search Key</label>
                                <input type="text" class="form-control" id="searchfield" value="<?php echo (!empty($search_post['key']) ? $search_post['key'] : ''); ?>" name="key" placeholder="Enter Text">
                            </div>
                        </div>
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




        </form>

    </div>
    </div>
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> Performance Report</h3>
                        <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userreports/performance_report'); ?>" >
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                        <input id="xport" name="xport" type="hidden" value="" />
                        </form>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="performancereport" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                        
                                        <th style="width: 30%">User Name</th>
                                        <th class="no-sort" style="width: 20%">User Type</th>
                                        <th class="no-sort" style="width: 15%">Designation</th>
                                        <th class="no-sort" style="width: 20%">Posting</th>
                                        <th class="no-sort" >Total Request</th>
                                        <th class="no-sort" >Favourite Person</th>
                                        <th class="no-sort" >Favourite User</th>
                                        <th class="no-sort" >Total White</th>
                                        <th class="no-sort" >Total Gray</th>
                                        <th class="no-sort" >Total Black</th>
                                        <th class="no-sort" >Total Login</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                                <tfoot>                                
                                <th>User Name</th>
                                <th>User Type</th>
                                <th>Designation</th>
                                <th>Posting</th>
                                <th>Total Request</th>
                                <th>Favourite Person</th>
                                <th>Favourite User</th>
                                <th>Total White</th>
                                <th>Total Gray</th>
                                <th>Total Black</th>
                                <th>Total Login</th>
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
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
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
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }



    $(document).ready(function () {



       var elem = $('#field').val();
        if (elem == 'def')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }
        else if (elem == 'username')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }





        else if (elem == 'designation')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //enable
            // $('#searchfield').removeAttr("disabled");
        }
        else if (elem == 'posting')
        {
            //show
            document.getElementById('posting-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
        }
        else if (elem == 'utype')
        {
            //show
            document.getElementById('utype').style.display = "block";
            //hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('key-hide').style.display = "none";
        }


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
        objDT = $('#performancereport').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userreports/ajaxperformancereport', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By User Name:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });
    $("#search_form1").validate({
        rules: {
                 field: {
                check_list: true,
            },
            key: {
                key_value: true,
                required: true,
            },
            "posting[]": {
                required: true,
            },

        },
        messages: {
            field: {
                check_list: "Please select search type",
            },
            key: {
                key_value: "Enter Valid Search Value",
            },
            "posting[]": {
                required: "Enter Valid Search Value",
            },
        }
    });

    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "def") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("key_value", function (sel, element) {
        if ($('#searchfield').val() !== "") {
            return true;
        } else {
            return false;
        }
    }, "<span>Select One</span>");

    function showDiv(elem) {
        // alert(elem);
        if (elem.value == 'def')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }
        else if (elem.value == 'username')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }


        else if (elem.value == 'designation')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";

            //enable
            // $('#searchfield').removeAttr("disabled");
        }
        else if (elem.value == 'posting')
        {
            //show
            document.getElementById('posting-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
        }

    }


    function clearSearch() {
        window.location.href = '<?php echo URL::site('userreports/performance_report', TRUE); ?>';
    }
function excel(id){    
            $('#xport').val('excel');
            $('#search_form').submit();
            $('#search_form1').submit();
            $('#xport').val('');
    }
</script>

