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
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li></i>User's Report</a></li>
        <li class="active"></i>No. of Login/out</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form role="form" id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userreports/no_of_login'); ?>">


                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="no-of-login" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Select Type</label>
                                    <select class="form-control select2" name="field" id='field' onchange="showDiv(this)">
                                        <option value="def"> Please Select Type</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'name')) ? 'selected' : ''); ?> value="name"> Name</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'designation')) ? 'selected' : ''); ?>  value="designation"> Designation</option>                                        
                                    </select>
                                </div>          
                            </div>
                            <!-- /.col -->
                            <div class="col-md-6" id="search_key">
                                <div class="form-group">
                                    <label for="searchfield">Search Key</label>
                                    <input type="text" class="form-control" id="searchfield" value="<?php echo (!empty($search_post['key']) ? $search_post['key'] : ''); ?>" name="key" placeholder="Enter Text">
                                </div>
                            </div>                                    
                            <div class=" form-group col-md-6" id="designation_div">
                                <label for="designation">Designation</label>
                                <select class="form-control" id="designation" name="designation">
                                    <option value="">Please Select Designation</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Addl. IG')) ? 'selected' : ''); ?> value="Addl. IG">Addl. IG</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'DIG')) ? 'selected' : ''); ?> value="DIG">DIG</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'SSP')) ? 'selected' : ''); ?> value="SSP">SSP</option>                                    
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'SP')) ? 'selected' : ''); ?> value="SP">SP</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'DSP')) ? 'selected' : ''); ?> value="DSP">DSP</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Corporal')) ? 'selected' : ''); ?> value="Corporal">Corporal(Corp)</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Inspector')) ? 'selected' : ''); ?> value="Inspector">Inspector (IP)</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Sub-Inspector')) ? 'selected' : ''); ?> value="Sub-Inspector">Sub-Inspector (SI)</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Assistant Sub-Inspector')) ? 'selected' : ''); ?> value="Assistant Sub-Inspector">Assistant Sub-Inspector (ASI)</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Head Constable')) ? 'selected' : ''); ?> value="Head Constable">Head Constable (HC)</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Constable')) ? 'selected' : ''); ?> value="Constable">Constable (C)</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Director')) ? 'selected' : ''); ?> value="Director">Director</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Deputy Director')) ? 'selected' : ''); ?> value="Deputy Director">Deputy Director</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Assistant Director')) ? 'selected' : ''); ?> value="Assistant Director">Assistant Director</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Supervisor')) ? 'selected' : ''); ?> value="Supervisor">Supervisor</option>
                                    <option <?php echo ((!empty($search_post['designation']) && ($search_post['designation'] == 'Computer Operator')) ? 'selected' : ''); ?> value="Computer Operator">Computer Operator</option>
                                </select>
                            </div>

                            <div class="form-group pull-right">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                <input id="xport" name="xport" type="hidden" value="" />
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
                        <h3 class="box-title"><i class="fa fa-search"></i> No. of Login/out</h3>
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="nooflogin" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                        
                                        <th>User ID</th>
                                        <th>User Name</th>
                                        <th class="no-sort">User Type</th>
                                        <th class="no-sort">Designation</th>
                                        <th class="no-sort">Posting</th>
                                        <th>Last Login</th>
                                        <th>No. Of Login</th>                                        
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>                                        
                                        <th>User ID</th>
                                        <th>User Name</th>
                                        <th>User Type</th>
                                        <th>Designation</th>
                                        <th>Posting</th>
                                        <th>Last Login</th>
                                        <th>No. Of Login</th>
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

</section>
<!-- /.content -->
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
            document.getElementById('designation_div').style.display = "none";
            //show
            document.getElementById('search_key').style.display = "block";
            //Disable
            $('#searchfield').attr("disabled", "disabled");
            //$('#searchfield').removeAttr("disabled");
        } else if (elem == 'name')
        {
            //Hide
            document.getElementById('designation_div').style.display = "none";
            //show
            document.getElementById('search_key').style.display = "block";
            //Disable
            //$('#searchfield').attr("disabled","disabled");
            $('#searchfield').removeAttr("disabled");
        } else if (elem == 'designation')
        {
            //Hide
            document.getElementById('designation_div').style.display = "block";
            //show
            document.getElementById('search_key').style.display = "none";
            //Disable
            //$('#searchfield').attr("disabled","disabled");
            //$('#searchfield').removeAttr("disabled");
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
        objDT = $('#nooflogin').dataTable(
                {"aaSorting": [[6, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userreports/ajaxnooflogin', TRUE); ?>",
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
    $("#search_form").validate({
        rules: {
            field: {
                check_list: true,
            },
            designation:{
                required:true,
                check_list:true
            },
            key: {
                key_value: true,
                required: true,
            },
        },
        messages: {
            field: {
                check_list: "Please select search type",
            },
            designation:{
                 required:"Enter Designation",
            },
            key: {
                key_value: "Enter Valid Search Value",
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
        if (elem.value == 'def')
        {
            //Hide
            document.getElementById('designation_div').style.display = "none";
            //show
            document.getElementById('search_key').style.display = "block";
            //Disable
            $('#searchfield').attr("disabled", "disabled");
            //$('#searchfield').removeAttr("disabled");
        } else if (elem.value == 'name')
        {
            //Hide
            document.getElementById('designation_div').style.display = "none";
            //show
            document.getElementById('search_key').style.display = "block";
            //Disable
            //$('#searchfield').attr("disabled","disabled");
            $('#searchfield').removeAttr("disabled");
        } else if (elem.value == 'designation')
        {
            //Hide
            document.getElementById('designation_div').style.display = "block";
            //show
            document.getElementById('search_key').style.display = "none";
            //Disable
            //$('#searchfield').attr("disabled","disabled");
            //$('#searchfield').removeAttr("disabled");
        }
    }
    function clearSearch() {
        window.location.href = '<?php echo URL::site('userreports/no_of_login', TRUE); ?>';
    }
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
</script>

