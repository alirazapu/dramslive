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
        Request Status
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Request Status</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('Adminrequest/admin_sent_request_status'); ?>" >
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post) && (empty($search_post['message']) || $search_post['message'] != 1)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post) && (empty($search_post['message']) || $search_post['message'] != 1)) ? 'display:block;' : ''; ?>">
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <label>Select Request Type</label>
                                    <div class="checkbox">
                                        <?php try{
                                            $requesttype_list = Helpers_Utilities::get_request_type();
                                            foreach ($requesttype_list as $list) {
                                                ?>
                                                <label>
                                                    <input name="request_type_new[]" type="checkbox" <?php echo (!empty($search_post['request_type_new']) && in_array($list->id, $search_post['request_type_new'])) ? 'Checked' : ''; ?> value="<?php echo $list->id ?>"><?php echo $list->email_type_name ?>
                                                </label>
                                                <br>
                                            <?php }
                                        }  catch (Exception $ex){   }?>
                                    </div>
                                </div>                                
                                <div class="col-md-3">
                                    <div class="col-md-12">
                                        <label>Select Network</label>
                                        <div class="checkbox">
                                            <label>
                                                <input name="mnc_new[]" type="checkbox" <?php echo (!empty($search_post['mnc_new']) && in_array(1, $search_post['mnc_new'])) ? 'Checked' : ''; ?> value="1"> Mobilink <br>
                                                <input name="mnc_new[]" type="checkbox" <?php echo (!empty($search_post['mnc_new']) && in_array(7, $search_post['mnc_new'])) ? 'Checked' : ''; ?> value="7"> Warid <br>
                                                <input name="mnc_new[]" type="checkbox" <?php echo (!empty($search_post['mnc_new']) && in_array(3, $search_post['mnc_new'])) ? 'Checked' : ''; ?> value="3"> Ufone <br>
                                                <input name="mnc_new[]" type="checkbox" <?php echo (!empty($search_post['mnc_new']) && in_array(6, $search_post['mnc_new'])) ? 'Checked' : ''; ?> value="6"> Telenor <br>
                                                <input name="mnc_new[]" type="checkbox" <?php echo (!empty($search_post['mnc_new']) && in_array(4, $search_post['mnc_new'])) ? 'Checked' : ''; ?> value="4"> Zong <br>
                                            </label>
                                        </div>
                                    </div>                                    
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Type of Request</label>
                                            <div class="radio radio-primary" style="margin-left: 20px">
                                                <input type="radio" <?php echo ((!empty($search_post['r_category']) && ($search_post['r_category'] == 1)) ||!isset($search_post['r_category']) ) ? 'checked' : ''; ?>  name="r_category" id="r_category_1" value="1">
                                                <label for="r_category_1" style="padding-left: 2px; margin-right: 25px">
                                                    All
                                                </label>
                                                <input type="radio" <?php echo ((!empty($search_post['r_category']) && ($search_post['r_category'] == 2)))  ? 'checked'  : ''; ?> name="r_category" id="r_category_2" value="2">
                                                <label for="r_category_2" style="padding-left: 2px; margin-right: 25px">
                                                    Mine
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Search From Body</label>
                                            <input name="txtbd" id="txtbd" class="form-control" type="text" value="<?php echo (!empty($search_post['txtbd'])) ? $search_post['txtbd'] : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Email Status</label>
                                            <div class="radio radio-primary" style="margin-left: 20px">
                                                <input type="radio" <?php echo ((!empty($search_post['e_status']) && ($search_post['e_status'] == 3)) || !isset($search_post['e_status']) ) ? 'checked' : ''; ?>  name="e_status" id="e_status_4" value="3">
                                                <label for="e_status_4" style="padding-left: 2px; margin-right: 25px">
                                                    All
                                                </label>
                                                <input type="radio" <?php echo (!empty($search_post['e_status']) && ($search_post['e_status'] == 2)) ? 'checked' : ''; ?> name="e_status" id="e_status_2" value="2">
                                                <label for="e_status_2" style="padding-left: 2px; margin-right: 25px">
                                                    Received
                                                </label>
                                                <input type="radio" <?php echo (!empty($search_post['e_status']) && ($search_post['e_status'] == 1)) ? 'checked' : ''; ?> name="e_status" id="e_status_1" value="1">
                                                <label for="e_status_1" style="padding-left: 2px; margin-right: 25px">
                                                    Send
                                                </label>
                                                <input type="radio" <?php echo (isset($search_post['e_status']) && ($search_post['e_status'] == 0)) ? 'checked' : ''; ?> name="e_status" id="e_status_3" value="0">
                                                <label for="e_status_3" style="padding-left: 2px; margin-right: 25px">
                                                    Pending
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12" id="reason_div">
                                        <div class="form-group" >
                                            <label for="inputreason" class="control-label">Requested By</label>
                                              <input type="text"   class="form-control" name="rqtbyname" id="rqtbyname" value="<?php echo (!empty($search_post['rqtbyname'])) ? $search_post['rqtbyname'] : ''; ?>" placeholder="Name">
                                              <div id="rqtbynamelist"></div>
                                        </div>
                                    </div>
<!--                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Reply Status</label>
                                            <div class="radio radio-primary" style="margin-left: 20px">
                                                <input type="radio" <?php echo ((isset($search_post['r_status']) && ($search_post['r_status'] == 2)) || !isset($search_post['r_status'])) ? 'checked' : ''; ?> name="r_status" id="r_status_1" value="2">
                                                <label for="r_status_1" style="padding-left: 2px; margin-right: 25px">
                                                    Both
                                                </label>
                                                <input type="radio" <?php echo (isset($search_post['r_status']) && ($search_post['r_status'] == 1)) ? 'checked' : ''; ?>  name="r_status" id="r_status_2" value="1">
                                                <label for="r_status_2" style="padding-left: 2px; margin-right: 25px">
                                                    Sent
                                                </label>
                                                <input type="radio" <?php echo (isset($search_post['r_status']) && ($search_post['r_status'] == 0)) ? 'checked' : ''; ?>  name="r_status" id="r_status_3" value="0">
                                                <label for="r_status_3" style="padding-left: 2px; margin-right: 25px">
                                                    Pending
                                                </label>
                                            </div>
                                        </div>
                                    </div>-->
<!--                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Parsing Status</label>
                                            <div class="radio radio-primary" style="margin-left: 20px">
                                                <input type="radio" <?php echo ((isset($search_post['p_status']) && ($search_post['p_status'] == 7)) || !isset($search_post['p_status'])) ? 'checked' : ''; ?> name="p_status" id="p_status_1" value="7">
                                                <label for="p_status_1" style="padding-left: 2px; margin-right: 25px">
                                                    All
                                                </label>
                                                <input type="radio" <?php echo (!empty($search_post['p_status']) && ($search_post['p_status'] == 5)) ? 'checked' : ''; ?>  name="p_status" id="p_status_2" value="5">
                                                <label for="p_status_2" style="padding-left: 2px; margin-right: 25px">
                                                    Completed
                                                </label>
                                                <input type="radio" <?php echo (!empty($search_post['p_status']) && ($search_post['p_status'] == 4)) ? 'checked' : ''; ?> name="p_status" id="p_status_5" value="4">
                                                <label for="p_status_5" style="padding-left: 2px; margin-right: 25px">
                                                    Waiting
                                                </label>
                                                <input type="radio" <?php echo (!empty($search_post['p_status']) && ($search_post['p_status'] == 3)) ? 'checked' : ''; ?> name="p_status" id="p_status_3" value="3">
                                                <label for="p_status_3" style="padding-left: 2px; margin-right: 25px">
                                                    Parsing Error
                                                </label>
                                            </div>
                                        </div>
                                    </div>-->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="searchfield">Start Date (mm/dd/yyyy)</label>
                                            <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="startdate" id="startdate" value="<?php echo (!empty($search_post['startdate']) ? $search_post['startdate'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label title="If not selected, Today is default Date" for="searchfield">End Date (mm/dd/yyyy)</label>
                                            <input title="If not selected, Today is default Date" type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="enddate" id="enddate" value="<?php echo (!empty($search_post['enddate']) ? $search_post['enddate'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12">

                                    </div>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-md-12">
                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                </div>
                            </div>

                            <!-- /.col -->
                            <!-- /.row -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <form class="" name="fixerror" id="fixerror" action="<?php echo url::site() . 'user/upload_against_imei' ?>"  method="post"  >
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
                                    <th>User</th>
                                    <th>Request Type</th>
                                    <th class="no-sort">Company</th>
                                    <th class="no-sort">Reason</th>
                                    <th>Date</th>
                                    <th>E-Mail Status</th>
<!--                                    <th class="no-sort">Reply</th>
                                    <th>Status</th>-->
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
<!--                                    <th>Reply</th>
                                    <th>Status</th>-->
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
                "sAjaxSource": "<?php echo URL::site('adminrequest/ajaxadminsentrequeststatus', TRUE); ?>",
                "sPaginationType": "full_numbers",
                "bFilter": true,
                "bLengthChange": true,
                "oLanguage": {
                    "sProcessing": "Loading...",
                    "sSearch": "Username,Request ID,Reference No. or Requested Value:"
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
    function deleteuserrequest(id)
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
                        $.ajax({url: '<?php echo URL::base() . "adminrequest/deleteuserrequest/"; ?>'  + id ,
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