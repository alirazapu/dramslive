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
        <i  class="fa fa-user"></i>
        User's Report
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li> </i>User's Report</a></li>
        <li ></i>User's Favourite Person</li>
        <li class="active"></i>User's Favourite Person List</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">        
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <?php
                        $user_id_encoded = Session::instance()->get('userid');
                        //$user_id_decoded = (int) Helpers_Utilities::encrypted_key($user_id_encoded, "decrypt");
                        ?>
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo Helpers_Utilities::get_user_name($user_id_encoded) ?>'s Favourite Persons</h3>
                        <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userreports/user_favourite_person_list/'.Helpers_Utilities::encrypted_key($user_id,"encrypt")); ?>" >
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                        <input id="xport" name="xport" type="hidden" value="" />
                        </form>
                    </div>
                    <!-- /.box-header -->
                    <div class=" box-body">
                        <div class="table-responsive">
                            <table id="userfavouritepersonlist" class="table  table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Person Name</th>
                                        <th>Father/Husband Name</th>
                                        <th>CNIC</th>
                                        <th class="no-sort">Address</th>
                                        <th class="no-sort">View Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Person Name</th>
                                        <th>Father/Husband Name</th>
                                        <th>CNIC</th>
                                        <th class="no-sort">Address</th>
                                        <th class="no-sort">View Details</th>
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
        objDT = $('#userfavouritepersonlist').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userreports/ajaxusersfavouritepersonlist', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Person Name or CNIC:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");
        $.fn.dataTable.ext.errMode = 'none';
        $('#userfavouritepersonlist').on('error.dt', function(e, settings, techNote, message) {
           swal("System Error", "Contact Technical Support Team.", "error");
        })

    });
    
    function excel(id){    
            $('#xport').val('excel');
            $('#search_form').submit();            
            $('#xport').val('');
    }

</script>

