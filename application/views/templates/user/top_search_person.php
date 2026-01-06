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
        <i class="fa fa-users"></i>
        Person's Report 
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >Person's Report</li>
        <li class="active">Top Searched Persons</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('personsreports/top_search_persons'); ?>">


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
                                    <select class="form-control select2" name="field" id='field' onchange="showDiv(this)">
                                        <option value=""> Please Select Type</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'name')) ? 'selected' : ''); ?> value="name"> Name</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'father_name')) ? 'selected' : ''); ?>  value="father_name"> Father Name</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'cnic_number')) ? 'selected' : ''); ?> value="cnic_number"> CNIC</option>   
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'address')) ? 'selected' : ''); ?> value="address"> Address</option> 
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'category')) ? 'selected' : ''); ?> value="category"> Category</option> 
                                    </select>
                                </div>          
                            </div>
                            <!-- /.col -->
                            <div class="col-md-6" id="key_div">
                                <div class="form-group">
                                    <label for="searchfield">Search Key</label>
                                    <input type="text" class="form-control" id="searchfield" value="<?php echo (!empty($search_post['key']) ? $search_post['key'] : ''); ?>" name="key" placeholder="Enter Text">
                                </div>                                
                            </div>
                            <div class="col-md-6" id="cat_div">
                                <label for="category">Category </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-file-pdf-o"></i>
                                    </div>
                                    <select class="form-control" name="category" id='category'>                                                        
                                        <option value="">Please Select Type</option>
                                        <option <?php echo (( isset($search_post['category']) && ($search_post['category'] == '0')) ? 'selected' : ''); ?> value="0"> White</option>
                                        <option <?php echo ((!empty($search_post['category']) && ($search_post['category'] == '1')) ? 'selected' : ''); ?>  value="1"> Gray</option>
                                        <option <?php echo ((!empty($search_post['category']) && ($search_post['category'] == '2')) ? 'selected' : ''); ?> value="2"> Black</option>                                                                                    
                                    </select>
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
                    </div>
                </form>

            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> Top Searched Persons</h3>
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="topsearchedperson" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                        
                                        <th style="width: 15%">Name</th>
                                        <th class="no-sort" style="width: 12%">Father/Husband Name</th>
                                        <th class="no-sort" style="width: 10%">CNIC</th>
                                        <th class="no-sort">Address</th>
                                        <th class="no-sort" style="width: 10%">Category</th>
                                        <th style="width: 12%">Search Count</th>
                                        <th class="no-sort"  style="width: 8%">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                    <tr>                                        
                                        <th>Name</th>
                                        <th>Father/Husband Name</th>
                                        <th>CNIC</th>
                                        <th>Address</th>
                                        <th>Category</th>
                                        <th>Search Count</th>
                                        <th>Detail</th>
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
        }
        else if (elem == 'category')
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
                {"aaSorting": [[5, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('personsreports/ajaxtopsearchpersons', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
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
        }
        else if (elem.value == 'category')
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
        window.location.href = '<?php echo URL::site('personsreports/top_search_persons', TRUE); ?>';
    }
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
</script>