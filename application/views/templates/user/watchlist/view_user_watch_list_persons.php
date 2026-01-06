<?php
//print_r($post['id']); exit;
$enc_uid=$post['id'];
$user_id=Helpers_Utilities::encrypted_key($post['id'], "decrypt");

$user_name=Helpers_Utilities::get_user_name($user_id);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-circle-o"></i> Watch List Persons
        <small>Tracer</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>	
        <li><a href="#"> Watch List</a></li>
        <li class="active">View Watch List</li>
        <li class="active">View Watch List Details</li>
      </ol>
    </section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-search" <span>  Watch List Persons added by <?php echo $user_name ?></span></i>  </h3>
                </div>
                <?php
               ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="personlist" class="table table-bordered table-striped">
                            <thead>
                                <tr>                                   
                                    <th class="no-sort">Person Name</th>
                                    <th class="no-sort">Total Requests</th>
                                    <th class="no-sort">Requests By User</th>
                                    <th class="no-sort">Last Sent Request</th>
                                    <th class="no-sort">Action</th>

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>                                     
                                    <th>Person Name</th>
                                    <th>Total Requests</th>
                                    <th>Requests By User</th>
                                    <th>Last Sent Request</th>
                                    <th>Action</th>
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
<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>
<script type="text/javascript">
    /* For nadra verisis request processing */
    function findphonenumber(cnic,requestid) {

        $("#cnic_number").val(cnic);
        $("#process_request_id").val(requestid);
        $("#process_nadra_verysis").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);

    }

    //table 
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        //validate Person Verisis
        



    
    //table data
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
        objDT = $('#personlist').dataTable(
                {"aaSorting": [],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('watchlist/ajax_user_added_wl_persons/?id='. $enc_uid, TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": false,
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
        $.fn.dataTable.ext.errMode = 'none';
        $('#personlist').on('error.dt', function(e, settings, techNote, message) {
           swal("System Error", "Contact Technical Support Team.", "error");
        })

    });


    



</script>