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
        <i class="fa fa-search"></i>
        Most Active Latitude and Longitude
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Active Latitude and Longitude</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="box box-primary">

            <form class="searchform_input_height" id="search_form" name="search_form" method="post" role="form" action="<?php echo URL::site('User/lat_long_active'); ?>" >
                <div class="box box-default searchperson">
                    <div class="box-header with-border">
                        <h3 class="box-title">Lat Long Search</h3>

                        <div class="box-tools pull-right">
                            <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                            <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">

                        <div class="form-group col-md-6">
                            <label for="lat_search">Latitude </label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-star"></i>
                                </div>
                                <input type="number" step="0.01" class="form-control" value="<?php echo ((!empty($search_post['lat_search'])) ? $search_post['lat_search'] : ''); ?>" id="lat_search" name="lat_search" placeholder="Enter Latitude">

                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="long_search">Longitude </label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-star"></i>
                                </div>
                                <input type="number" step="0.01" class="form-control" value="<?php echo ((!empty($search_post['long_search'])) ? $search_post['long_search'] : ''); ?>" id="long_search" name="long_search" placeholder="Enter Longitude">

                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group pull-right" >
                                <button type="submit" onclick="return validateAndSend()" class="btn btn-primary ">Search</button>
                                <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
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
                        <h3 class="box-title"><i class="fa fa-search"></i> Most Active Latitude and Longitude</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="latlongsearch" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th class="no-sort">Latitude </th>
                                        <th>Longitude</th>
                                        <th>Count</th>
                                        <th class="no-sort">View Detail</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>

                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Count</th>
                                <th>View Detail</th> 
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
        objDT = $('#latlongsearch').dataTable(
                {"aaSorting": [[1, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                  //  //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('user/ajaxlatlongactive', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": false,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search by Latitude Or Longitude:",
                        "sEmptyTable": 'Information not found'
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false
                        }]
                }
        );
        // validation for data search
        $("#search_form").validate({
            rules:{
                lat_search:{
                    numeric:true,
                    minlength:3,
                    maxlength: 12
                },
                long_search:{
                    numeric:true,
                    minlength:0,
                    maxlength: 12
                }
            },
            messages: {
                lat_search:{
                    maxlenght:"Maximum character limit is 12",
                    minlength:"Minimum character limit is 3"
                },
                long_search:{
                    maxlenght:"Maximum character limit is 12",
                    minlength:"Minimum character limit is 3"
                }
            },

            submitHandler: function () {
                $("#search_form").submit();

            }
            // $('#upload').show()
        });

    });
    //
    // function validateAndSend() {
    //     if(search_form.lat_search.value == "" ){
    //         alert('Please Enter Valid Latitude');
    //         return false;
    //     }
    //     if(search_form.long_search.value == "" ){
    //         alert('Please Enter Valid Longitude');
    //         return false;
    //     }
    // }
    function clearSearch() {
        window.location.href = '<?php echo URL::site('User/lat_long_active', TRUE); ?>';
    }

</script>