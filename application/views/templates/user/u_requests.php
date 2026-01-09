<?php
$id_encrypted = Helpers_Utilities::encrypted_key($id, "encrypt");

$dquery = '';
if (!empty($_GET['fr'])) {
    $start_date = date("Y-m-d", strtotime($_GET['fr']));
    $dquery .= '&fr=' . $start_date;
}
if (!empty($_GET['to'])) {
    $end_date = date("Y-m-d", strtotime($_GET['to']));
    $dquery .= '&to=' . $end_date;
}
if (!empty($_GET['req'])) {
    $dquery .= '/&req=' . $_GET['req'];
}

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
        User Requests Status
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">User Requests Status</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">


        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i>User Requests Status</h3>
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
                                    <th>User</th>
                                    <th>Request Type</th>
                                    <th class="no-sort">Company</th>
                                    <th class="no-sort">Reason</th>
                                    <th>Date</th>
                                    <th>E-Mail Status</th>
                                    <th class="no-sort" >Action</th>

                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>User</th>
                                    <th>Request Type</th>
                                    <th>Company</th>
                                    <th>Reason</th>
                                    <th>Date</th>
                                    <th>E-Mail Status</th>
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
    <!--     user request status update-->
    <div class="modal modal-info fade" id="modalupdatestatus">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Update Request Status</h4>
                </div>
                <div class="modal-body" style='background-color: #fff !important; color: black !important; '>
                    <div  style="display:block;margin-top: 5px;margin-bottom: 5px;" class="col-md-12">

                        <div class="col-md-12" style="background-color: #fff;color: black">
                            <hr class="style14 ">
                            <div class="form-group col-sm-6">
                                <label>Request ID:</label>
                                <input  class="form-control "type="text" id="request_status_update_id" name="request_id" value="" readonly>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>Reference No:</label>
                                <input  class="form-control "type="text" id="request_status_refno" name="refno" value="" readonly>
                            </div>
                            <div class="form-group col-sm-12" >
                                <label>Email Status:</label>
                                <select  class="form-control" name="request_status" id="request_status">
                                    <?php try{
                                        $request_status = Helpers_Utilities::get_request_status_name();

                                        $i=0;
                                        foreach ($request_status as $list) {
                                            ?>
                                            <option  value="<?php echo $i ?>"><?php echo $list ?></option>
                                            <?php
                                            $i++;
                                        }
                                    }  catch (Exception $ex){   }?>
                                </select>
                            </div>
                            <div class="form-group col-sm-12" >
                                <label>Processing Status:</label>
                                <select  class="form-control" name="processing_index" id="processing_index">
                                    <?php
                                    $request_status = Helpers_Utilities::get_parsing_status_name();
                                    $i=0;
                                    foreach ($request_status as $list) {
                                        ?>
                                        <option  value="<?php echo $i ?>"><?php echo $list ?></option>
                                        <?php
                                        $i++;
                                    } ?>
                                </select>
                            </div>

                            <div class="form-group col-sm-12" >
                                <hr class="style14 ">
                                <span id="" class="text-black" > </span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                        <button type="button" onclick="UpdateRequestStatusDetails()" class="btn btn-primary ">Update</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
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
            {"aaSorting": [[4, "desc"]],
                "bPaginate": true,
                "bProcessing": true,
                //"bStateSave": true,
                "bServerSide": true,
                "sAjaxSource": "<?php echo URL::site('adminrequest/ajaxusersentrequests?id='.$id_encrypted.$dquery, TRUE); ?>",
                "sPaginationType": "full_numbers",
                "bFilter": true,
                "bLengthChange": true,
                "oLanguage": {
                    "sProcessing": "Loading...",
                    "sSearch": "Username,ID,Reference No., Mobile No or Request Type:"
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
        window.location.href = '<?php echo URL::site('adminrequest/admin_request_status1', TRUE); ?>';
    }


    $("#requesttype").on("change", function(){
        if ($(this).find(":selected").text() == "All Request"){
            $('#requesttype option').prop('selected', true);
            $('#requesttype option[value=99]').prop("selected", false);
            $('#requesttype option[value=""]').prop("selected", false);
        }
    });

    //function to delete user request type
    function deleteuserrequest(id,value)
    {
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
        //var elem = $(this).parent().parent();// .closest('.action');
        // var elem = $(this).closest('tr[class^="action"]');
        var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title'     : 'Delete Record confirmation',
            'message'   : 'Do you really want to delete this request ?' ,
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){
                        $.ajax({url: '<?php echo URL::base() . "Userrequest/deleteuserrequest/"; ?>'  + id ,
                            success: function(result){
                                if (result == -2) {

                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                else{
                                    elem.slideUp(10000);
                                    refreshGrid();
                                }
                            }});

                    }
                },
                'No'    : {
                    'class' : 'blue',
                    'action': function(){}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    //function to open model for request status
    function UpdateRequestStatus(id,refno,status,processing) {
        $("#modalupdatestatus").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');
        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open")
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second
            $(".modal-backdrop.fade.in").remove();
        }, 300);
        $("#request_status_update_id").val(id);
        $("#request_status_refno").val(refno);
        $("#request_status").val(status);
        $("#processing_index").val(processing);
    }
    //function to open model for request status
    function UpdateRequestStatusDetails() {
        $("#modalupdatestatus").modal("hide");
        var request_id=    $("#request_status_update_id").val();
        var request_status=    $("#request_status").val();
        var processing_index=    $("#processing_index").val();
        var result = {request_id: request_id,request_status:request_status,processing_index:processing_index}
        //ajax to upload device informaiton
        $.ajax({
            url: "<?php echo URL::site("userrequest/update_user_request_status"); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (result) {
                if (result == -2) {

                    swal("System Error", "Contact Support Team.", "error");
                } else{
                    refreshGrid();
                }
            }
        });
    }


    //Date picker
    $('#startdate').datepicker({
        autoclose: true
    });
    //Date picker
    $('#enddate').datepicker({
        autoclose: true
    });
    
    $("#rqtbyname").on("keyup", function(){
        var rqtbyname = $(this).val();
        if (rqtbyname !=="") {
          $.ajax({
            url:"<?php echo URL::site("adminrequest/autocomplete"); ?>",
            type:"POST",
            cache:false,
            data:{rqtbyname:rqtbyname},
            success:function(data){
              $("#rqtbynamelist").html(data);
              $("#rqtbynamelist").fadeIn();
            }  
          });
        }else{
          $("#rqtbynamelist").html("");  
          $("#rqtbynamelist").fadeOut();
        }
    });
$(document).on("click","li", function(){
        $('#rqtbyname').val($(this).text());
        $('#rqtbynamelist').fadeOut("fast");
      });
</script>
<style>
#rqtbynamelist ul.list-unstyled {
    background-color: #def;
    padding: 10px;
    cursor: pointer;
}
</style>