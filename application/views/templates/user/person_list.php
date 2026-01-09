<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$value='';
$value = !empty($_GET['reg'])?$_GET['reg']: null;

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-users"></i>
        Person's Report 
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >Person's Report</li>
        <li class="active">Person list</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="box box-primary">
                <?php
                $url = 'personsreports/person_list/';
                if (!empty($_GET['category'])) {
                    $url = 'personsreports/person_list/?category='.$_GET['category'];
                }
                if (!empty($_GET['reg']) && !empty($_GET['category'])) {
                    $url = 'personsreports/person_list/?category='.$_GET['category'].'&reg='.$_GET['reg'];
                }
                if (!empty($_GET['reg'])) {
                    $url = 'personsreports/person_list/?reg='.$_GET['reg'];
                }
                ?>
                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site($url); ?>">
                    <?php
                    if(empty($value)){
                    ?>
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
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="posting">Select Posting </label>
                                        <select class="form-control select2" multiple="multiple"  id="posting" name="posting[]"  data-placeholder="Select User Posting From List" style="width: 100%;">
                                            <option value="">Please Select Posting </option>
                                            <optgroup label="Region">                                    
                                                <?php
                                                try {
                                                    $region_list = Helpers_Utilities::get_region();
                                                    foreach ($region_list as $list) {
                                                        ?>
                                                        <option <?php echo (!empty($search_post['posting']) && in_array('r-' . $list->region_id, $search_post['posting'])) ? 'Selected' : ''; ?> value="r-<?php echo $list->region_id ?>"><?php echo $list->name ?></option>
                                                    <?php }
                                                } catch (Exception $ex) {
                                                    
                                                }

                                                ?>
                                            <optgroup label="District">
                                                <?php
                                                try {
                                                    $district_list = Helpers_Utilities::get_district();
                                                    foreach ($district_list as $list) {
                                                        ?>
                                                        <option <?php echo (!empty($search_post['posting']) && in_array('d-' . $list->district_id, $search_post['posting'])) ? 'Selected' : ''; ?> value="d-<?php echo $list->district_id ?>"><?php echo $list->name ?></option>
                                                    <?php }
                                                } catch (Exception $ex) {

                                                }
                                                ?>
                                            <optgroup label="Police Station">
                                                <?php
                                                try {
                                                    $police_station_list = Helpers_Utilities::get_police_station();
                                                    foreach ($police_station_list as $list) {
                                                        ?>
                                                        <option <?php echo (!empty($search_post['posting']) && in_array('p-' . $list->id, $search_post['posting'])) ? 'Selected' : ''; ?> value="p-<?php echo $list->id ?>"><?php echo $list->name ?></option>
                                                    <?php }
                                                } catch (Exception $ex) {

                                                }
                                                ?>
                                            <optgroup label="Head Quarter">
<?php
try {
    $headquarter_list = Helpers_Utilities::get_headquarter();
    foreach ($headquarter_list as $list) {
        ?>
                                                        <option <?php echo (!empty($search_post['posting']) && in_array('h-' . $list->id, $search_post['posting'])) ? 'Selected' : ''; ?> value="h-<?php echo $list->id ?>"><?php echo $list->name ?></option>
    <?php }
} catch (Exception $ex) {

}
?>                                                      
                                        </select>
                                    </div>
                                </div>
                            <!-- /.col -->                            
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


 <?php } ?>

                </form>

            </div>
        </div>


        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-search"></i>Person's List (w.r.t Category,User)</h3>                        
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="table-responsive">
                    <table id="personslist" class="table table-bordered table-striped">
                        <thead>
                            <tr> 
                                <th class="no-sort">Person Name</th>
                                <th class="no-sort">Category</th>
                                <th class="no-sort">Organization</th>
                                <th>Added On</th>
                                <th class="no-sort">Added By User</th>
                                <th class="no-sort">User Type</th>
                                <th class="no-sort">Posting</th>                                                                               
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>                                    
                            <tr>                    
                                <th>Person Name</th>
                                <th>Category</th>
                                <th>Organization</th>
                                <th>Added On</th>
                                <th>Added By User</th>
                                <th>User Type</th>
                                <th>Posting</th>                                        
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

</section>
<!-- /.content -->
<script type="text/javascript">
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
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

        objDT = $('#personslist').dataTable(
                {"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('personsreports/ajaxpersonlist', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By User Name Or Person CNIC:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");
        $.fn.dataTable.ext.errMode = 'none';
        $('#personslist').on('error.dt', function (e, settings, techNote, message) {
            swal("System Error", "Contact Technical Support Team.", "error");
        })

    });
    function clearSearch() {
        window.location.href = '<?php echo URL::site('personsreports/person_list', TRUE); ?>';
    }
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
</script>