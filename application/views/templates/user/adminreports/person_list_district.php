<?php
$posting = !empty($search_post['id']) ? Helpers_Utilities::encrypted_key($search_post['id'], 'decrypt') : 0;
$office = Helpers_Profile::get_user_posting($posting);
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-users"></i>
        Person's Report 
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >Person's List</li>
        <li class="active">Top Searched Persons</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary"> 
                <?php
                $url = 'personsreports/person_list_district/?id='.$_GET['id'];
                if (!empty($_GET['startdate']) && $_GET['enddate']) {
                    $url = 'personsreports/person_list_district/?id='.$_GET['id'].'&startdate=' .$_GET['startdate']. '&enddate=' .$_GET['enddate'];
                }
                ?>
                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site($url); ?>">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Added Person Type</label>
                                    <div class="radio radio-primary" style="margin-left: 20px">                                            
                                        <input type="radio" <?php echo empty($search_post['p_type']) ? 'checked' : ''; ?>  name="p_type" id="p_type_0" value="0">
                                        <label for="p_type_0" style="padding-left: 2px; margin-right: 25px">
                                            All
                                        </label>
                                        <input type="radio" <?php echo ((!empty($search_post['p_type']) && ($search_post['p_type'] == 1))) ? 'checked' : ''; ?>  name="p_type" id="p_type_1" value="1">
                                        <label for="p_type_1" style="padding-left: 2px; margin-right: 25px">
                                            Fe-Male
                                        </label>
                                        <input type="radio" <?php echo ((!empty($search_post['p_type']) && ($search_post['p_type'] == 2))) ? 'checked' : ''; ?> name="p_type" id="p_type_2" value="2">
                                        <label for="p_type_2" style="padding-left: 2px; margin-right: 25px">
                                            Male
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" style="margin-top: 24px;"> 
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Search</button>                                    
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
                        <h3 class="box-title"><i class="fa fa-search"></i> Person List of: <b> <?php echo $office; ?> </b> </h3>
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="topsearchedperson" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                        
                                        <th>Person Name</th>
                                        <th class="no-sort">Father/Husband Name</th>
                                        <th class="no-sort">CNIC</th>                                        
                                        <th class="no-sort">Category</th>
                                        <th>Added On</th>
                                        <th class="no-sort">Added By User</th>                                        
                                        <th class="no-sort">View Detail</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                    <tr>                                        
                                        <th>Person Name</th>
                                        <th>Father/Husband Name</th>
                                        <th>CNIC</th>
                                        <th>Category</th>
                                        <th>Added On</th>
                                        <th>Added By User</th>
                                        <th>View Detail</th>
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
        // alert(elem);
        if (elem == '' || elem == 'name' || elem == 'father_name' || elem == 'cnic_number' || elem == 'address')
        {
            //Hide
            document.getElementById('cat_div').style.display = "none";
            //show
            document.getElementById('key_div').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem == 'category')
        {
            //Hide
            document.getElementById('key_div').style.display = "none";
            //show
            document.getElementById('cat_div').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
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
        objDT = $('#topsearchedperson').dataTable(
                {"aaSorting": [[4, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('personsreports/ajaxpersonlistdistrict', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": false,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Person Name:"
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
            key: {
                key_value: true,
                required: true,
            },
            category: {
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
        }
    });
    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "") {
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
        if (elem.value == '' || elem.value == 'name' || elem.value == 'father_name' || elem.value == 'cnic_number' || elem.value == 'address')
        {
            //Hide
            document.getElementById('cat_div').style.display = "none";
            //show
            document.getElementById('key_div').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem.value == 'category')
        {
            //Hide
            document.getElementById('key_div').style.display = "none";
            //show
            document.getElementById('cat_div').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }
    }
    function clearSearch() {
        window.location.href = '<?php echo URL::site('personsreports/person_list_district', TRUE); ?>';
    }
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
</script>