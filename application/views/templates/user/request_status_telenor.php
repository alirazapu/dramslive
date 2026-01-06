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
        <i class="fa fa-files-o"></i>
        Telenor Less Than Six Months Request Status
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Request Status</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <form class="" name="fixerror" id="fixerror" action="<?php echo url::site() . 'user/data_upload' ?>"  method="post"  >
            <input type="hidden" name="requestid" id="requestid" value="" >
            <input type="hidden" name="receivedfilepath" id="receivedfilepath" value="" >
            <input type="hidden" name="receivedbody" id="receivedbody" value="" >
            <input type="hidden" name="requesttype" value="2" >
            <input type="hidden" name="requestvalue" id="requestvalue" value="" >           
        </form>
        <div class="row">
            <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-search"></i> Request Status</h3>
                </div>
                <?php
                if (!empty($message)) {
                    ?>
                <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <?php echo $message; ?></h4>
                    </div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="requeststatus" class="table table-bordered table-striped">
                            <thead>
                                <tr>                                   
                                    <th class="no-sort">User</th>                                                                                                          
                                    <th class="no-sort">Request Type</th>                                    
                                    <th class="no-sort">Company</th>
                                    <th class="no-sort">Days</th>
                                    <th class="no-sort">Subject</th>
                                    <th class="no-sort">E-Mail Body</th>
                                    <th>Date</th>
                                    <th class="no-sort">E-Mail Status</th>
                                    <th class="no-sort" style="width: 15%">Request Sent</th>
                                    
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>                                     
                                    <th>User</th>                                                                                                          
                                    <th>Request Type</th>
                                    <th>Company</th>
                                    <th>Days</th>
                                    <th>Subject</th>
                                    <th>E-Mail Body</th>
                                    <th>Date</th>
                                    <th>E-Mail Status</th>
                                    <th>Request Sent</th>
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
        objDT = $('#requeststatus').dataTable(
                {"aaSorting": [[5, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userrequest/ajaxuserrequeststatustelenor', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,                   
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Request Value:"
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
                required: true,
            },
            "requesttype[]": {
                required: true,
            },
        },
        messages: {
            field: {
                required: "Please select search type",
            },
            "posting[]": {
                required: "Select any option from list",
            },
        }
    });
    //request full parse cdr against imei
    function fullparseimeicdr(requestid,recfile,recbody,requestedvalue) {
    $("#requestid").val(requestid);
    $("#receivedfilepath").val(recfile);
    $("#receivedbody").val(recbody);
    $("#requestvalue").val(requestedvalue);
    $("#fixerror").submit();
    //alert($("#receivedbody").val());
    }
    function clearSearch() {
        window.location.href = '<?php echo URL::site('userrequest/request_status', TRUE); ?>';
    }    
    
    function requestmanualsent(request_id) {
        $.ajax({url: '<?php echo URL::base() . "Userrequest/manualrequestsend/"; ?>' + request_id, success: function (result) {
            refreshGrid();       
        }});
    }    
    
    
    $("#requesttype").on("change", function(){      
    if ($(this).find(":selected").text() == "All Request"){
     $('#requesttype option').prop('selected', true); 
     $('#requesttype option[value=99]').prop("selected", false);
     $('#requesttype option[value=""]').prop("selected", false);
    }
});

</script>

<style>
    .table-striped > tbody > tr:nth-of-type(n+1){
        background-color: white !important;
    }
</style>