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
        Most Active Lac and Cell ID
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Active Lac and Cell ID</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
<!--                    <div class="box-header">-->
<!--                        <h3 class="box-title"><i class="fa fa-search"></i> Most Active Lac and Cell ID</h3>-->
<!--                    </div>-->
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="laccellsearch" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th class="no-sort">Lac ID </th>
                                        <th>Cell ID</th>
                                        <th>Count</th>
                                        <th class="no-sort">View Detail</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>

                                <th>Lac ID</th>
                                <th>Cell ID</th>
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
        objDT = $('#laccellsearch').dataTable(
                {"aaSorting": [[1, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                  //  //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('user/ajaxlaccellactive', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search by Lac Or Cell ID:",
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