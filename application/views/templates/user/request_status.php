<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$login_user = Auth::instance()->get_user();
//echo '<pre>';
//print_r($login_user->id);
//exit();
$user_id=$login_user->id;
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-files-o"></i>
        Request Status
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
        <div class="">
            <div class="box box-primary">
                <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userrequest/request_status'); ?>" >
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
                                                <input name="mnc_new[]" type="checkbox" <?php echo (!empty($search_post['mnc_new']) && in_array(8, $search_post['mnc_new'])) ? 'Checked' : ''; ?> value="8"> SCOM <br>                                                                                        
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
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Search User</label>
                                            <input name="user" id="user" class="form-control" type="text" value="<?php echo (!empty($search_post['user'])) ? $search_post['user'] : ''; ?>">
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
                                    <div class="col-md-12">
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
                                    </div>
                                    <div class="col-md-12">
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
                                    </div>                                    
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

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="searchfield">Start Date/Time</label>
                                                <input type="text" readonly="readonly" placeholder="" class="form-control" name="starttime" id="starttime" value="<?php echo (!empty($search_post['starttime']) ? $search_post['starttime'] : ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label title="If not selected, Current time is default Time" for="searchfield">End Date/Time</label>
                                                <input title="If not selected, Current time is default Time" type="text" readonly="readonly" placeholder="" class="form-control" name="endtime" id="endtime" value="<?php echo (!empty($search_post['endtime']) ? $search_post['endtime'] : ''); ?>">
                                            </div>
                                        </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Search Person</label>
                                            <input name="person" id="person" class="form-control" type="text" value="<?php echo (!empty($search_post['person'])) ? $search_post['person'] : ''; ?>">
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
                                    <th class="no-sort">Person</th>
                                    <th>Date</th>
                                    <th>E-Mail Status</th>
                                    <th class="no-sort">Reply</th>
                                    <th>Status</th>
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
                                    <th>Person</th>
                                    <th>Date</th>
                                    <th>E-Mail Status</th>
                                    <th>Reply</th>
                                    <th>Status</th>
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
                    "sAjaxSource": "<?php echo URL::site('userrequest/ajaxuserrequeststatus', TRUE); ?>",
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
        window.location.href = '<?php echo URL::site('userrequest/request_status', TRUE); ?>';
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
//Date/Time picker
    $('#starttime').datetimepicker({
      autoclose: true
    });
//Date/Time picker
    $('#endtime').datetimepicker({
      autoclose: true
    });

    $(document).ready(function () {
        <?php
        $person_total_ownership_requests_error= Helpers_Utilities::get_person_total_subscriber_requests_p_error($user_id);
        $person_total_current_location_requests_error= Helpers_Utilities::get_person_total_current_location_p_error($user_id);
        $person_total_sims_against_cnic_requests_error= Helpers_Utilities::get_person_total_sims_against_cnic_p_error($user_id);
        $person_total_sim_against_imsi_requests_error= Helpers_Utilities::get_person_total_sims_against_imsi_p_error($user_id);
        $user_role= Helpers_Utilities::get_user_role_id($user_id);
        ?>
        <?php if($user_role==8){ ?>
        <?php if(!empty($person_total_ownership_requests_error)){ ?>
        msgboxbox.show("<?php echo 'Parsing Errors, Subscriber Against Mobile Number : '.$person_total_ownership_requests_error  ?>", null);
        <?php }
        if(!empty($person_total_current_location_requests_error)){ ?>
        msgboxbox.show("<?php echo 'Parsing Errors, Current Location : '.$person_total_current_location_requests_error  ?>", null);
        <?php }
        if(!empty($person_total_sims_against_cnic_requests_error)){ ?>
        msgboxbox.show("<?php echo 'Parsing Errors, Sims Against CNIC : '.$person_total_sims_against_cnic_requests_error  ?>", null);
        <?php }
        if(!empty($person_total_sim_against_imsi_requests_error)){ ?>
        msgboxbox.show("<?php echo 'Parsing Errors, Sims Against IMSI : '.$person_total_sim_against_imsi_requests_error  ?>", null);
        <?php } ?>
        <?php } ?>
    });
</script>
<script>


    class MessageBox {
        constructor(id, option) {
            this.id = id;
            this.option = option;
        }

        show(msg, label = "CLOSE", callback = null) {
            if (this.id === null || typeof this.id === "undefined") {
                // if the ID is not set or if the ID is undefined

                throw "Please set the 'ID' of the message box container.";
            }

            if (msg === "" || typeof msg === "undefined" || msg === null) {
                // If the 'msg' parameter is not set, throw an error

                throw "The 'msg' parameter is empty.";
            }

            if (typeof label === "undefined" || label === null) {
                // Of the label is undefined, or if it is null

                label = "CLOSE";
            }

            let option = this.option;

            let msgboxArea = document.querySelector(this.id);
            let msgboxBox = document.createElement("DIV");
            let msgboxContent = document.createElement("DIV");
            let msgboxClose = document.createElement("A");

            if (msgboxArea === null) {
                // If there is no Message Box container found.

                throw "The Message Box container is not found.";
            }

            // Content area of the message box
            msgboxContent.classList.add("msgbox-content");
            msgboxContent.innerText = msg;

            // Close burtton of the message box
            msgboxClose.classList.add("msgbox-close");
            msgboxClose.setAttribute("href", "#");
            msgboxClose.innerText = label;

            // Container of the Message Box element
            msgboxBox.classList.add("msgbox-box");
            msgboxBox.appendChild(msgboxContent);

            if (option.hideCloseButton === false
                || typeof option.hideCloseButton === "undefined") {
                // If the hideCloseButton flag is false, or if it is undefined

                // Append the close button to the container
                msgboxBox.appendChild(msgboxClose);
            }

            msgboxArea.appendChild(msgboxBox);

            msgboxClose.addEventListener("click", (evt) => {
                evt.preventDefault();

                if (msgboxBox.classList.contains("msgbox-box-hide")) {
                    // If the message box already have 'msgbox-box-hide' class
                    // This is to avoid the appearance of exception if the close
                    // button is clicked multiple times or clicked while hiding.

                    return;
                }

                this.hide(msgboxBox, callback);
            });

            if (option.closeTime > 0) {
                this.msgboxTimeout = setTimeout(() => {
                    this.hide(msgboxBox, callback);
                }, option.closeTime);
            }
        }

        hide(msgboxBox, callback) {
            if (msgboxBox !== null) {
                // If the Message Box is not yet closed

                msgboxBox.classList.add("msgbox-box-hide");
            }

            msgboxBox.addEventListener("transitionend", () => {
                if (msgboxBox !== null) {
                    // If the Message Box is not yet closed

                    msgboxBox.parentNode.removeChild(msgboxBox);

                    clearTimeout(this.msgboxTimeout);

                    if (callback !== null) {
                        // If the callback parameter is not null
                        callback();
                    }
                }
            });
        }
    }

    let msgboxShowMessage = document.querySelector("#msgboxShowMessage");
    let msgboxHiddenClose = document.querySelector("#msgboxHiddenClose");

    // Creation of Message Box class, and the sample usage
    let msgboxbox = new MessageBox("#msgbox-area", {
        closeTime: 10000,
        hideCloseButton: false
    });
    let msgboxboxPersistent = new MessageBox("#msgbox-area", {
        closeTime: 0
    });
    let msgboxNoClose = new MessageBox("#msgbox-area", {
        closeTime: 5000,
        hideCloseButton: true
    });


</script>
<style>
    .msgbox-area {
        max-height: 100%;
        position: fixed;
        bottom: 15px;
        left: 20px;
        right: 20px;
    }

    .msgbox-area .msgbox-box {
        font-size: inherit;
        color: #ffffff;
        background-color: rgba(0, 0, 0, 0.8);
        padding: 18px 20px;
        margin: 0 0 15px;
        display: flex;
        align-items: center;
        position: relative;
        border-radius: 12px;
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.65);
        transition: opacity 300ms ease-in;
    }

    .msgbox-area .msgbox-box.msgbox-box-hide {
        opacity: 0;
    }

    .msgbox-area .msgbox-box:last-child {
        margin: 0;
    }

    .msgbox-area .msgbox-content {
        flex-shrink: 1;
    }

    .msgbox-area .msgbox-close {
        color: #ffffff;
        font-weight: bold;
        text-decoration: none;
        margin: 0 0 0 20px;
        flex-grow: 0;
        flex-shrink: 0;
        position: relative;
        transition: text-shadow 225ms ease-out;
    }

    .msgbox-area .msgbox-close:hover {
        text-shadow: 0 0 3px #efefef;
    }

    @media (min-width: 481px) and (max-width: 767px) {
        .msgbox-area {
            left: 80px;
            right: 80px;
        }
    }

    @media (min-width: 768px) {
        .msgbox-area {
            width: 480px;
            height: 0;
            top: 50%;
            left: auto;
            right: 15px;
        }
    }

    .vitem {
         height: 90px;
         line-height: 30px;
     }
    .vwrap {
        height: 286px;
        line-height: 30px;
        background: white local;
        overflow: hidden; /* HIDE SCROLL BAR */
    }
    /* (C) TICKER ITEMS */
    .vitem { text-align: center; }

    /* (D) ANIMATION - MOVE ITEMS FROM TOP TO BOTTOM */
    /* CHANGE KEYFRAMES IF YOU ADD/REMOVE ITEMS */
    .vmove { position: relative; }
    @keyframes tickerv {
        0% { bottom: 0; } /* FIRST ITEM */
        30% { bottom: 30px; } /* SECOND ITEM */
        60% { bottom: 60px; } /* THIRD ITEM */
        90% { bottom: 90px; } /* FORTH ITEM */
        100% { bottom: 0; } /* BACK TO FIRST */
    }
    .vmove {
        animation-name: tickerv;
        animation-duration: 10s;
        animation-iteration-count: infinite;
        animation-timing-function: cubic-bezier(1, 0, .5, 0);
    }
    .vmove:hover { animation-play-state: paused; }


</style>
<div id="msgbox-area" class="msgbox-area"></div>