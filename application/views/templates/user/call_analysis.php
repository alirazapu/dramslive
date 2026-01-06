<?php
//$pid = Helpers_Utilities::encrypted_key(($_GET['id']), "decrypt");

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
        Call Analysis
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >Call Analysis</li>

    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
<!--        --><?php
//        $url = 'personsreports/person_call_analysis/';
//        if (!empty($_GET['id'])) {
//            $url = 'personsreports/person_call_analysis/?id='.$_GET['id'];
//        }
//        ?>
        <div class="box">
            <div class="box-header">
                <h3 class="box-title"><i class="fa fa-search"></i>Call Analysis</h3>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table id="personslist" class="table table-bordered table-striped">
                        <thead>
                            <tr> 
                                <th >Sr. No.</th>
                                <th class="no-sort">Phone-></th>
                                <th class="no-sort">Phone-></th>
                                <th class="no-sort">Phone-></th>
                                <th class="no-sort">Phone-></th>
                                <th class="no-sort">Phone-></th>
                                <th class="no-sort">Phone-></th>
                                <th class="no-sort">Phone-></th>
                                <th class="no-sort">Phone-></th>
                                <th class="no-sort">Phone-></th>
                                <th class="no-sort">Phone-></th>
                                <th class="no-sort">Status </th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>                                    
                            <tr>
                                <th >Sr. No.</th>
                                <th >Phone </th>
                                <th >Phone </th>
                                <th >Phone </th>
                                <th >Phone </th>
                                <th >Phone </th>
                                <th >Phone </th>
                                <th >Phone </th>
                                <th >Phone </th>
                                <th >Phone </th>
                                <th >Phone </th>
                                <th >Status </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->

    </div>


</section>
<!-- /.content -->
<!--<script src="https://code.iconify.design/2/2.0.3/iconify.min.js"></script>-->
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
                {//"aaSorting": [[2, "desc"]],

                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,

                    //"sAjaxSource": "<?php //echo URL::site('personsreports/ajax_person_call_analysis/?id=' . $_GET['id'], TRUE); ?>//",
                    "sAjaxSource": "<?php echo URL::site('personsreports/ajax_person_call_analysis', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": false,
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

</script>