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
        Old Request Resend
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Old Request Resend</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userrequest/request_status_resend'); ?>" >
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
                                <div class="col-md-6">   
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
                                <div class="col-md-6"> 
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
                                    <th style="width: 15%" class="no-sort">User</th>                                                                                                          
                                    <th style="width: 20%" class="no-sort">Request Type</th>                                    
                                    <th style="width: 10%" class="no-sort">Company</th>
                                    <th style="width: 15%" >Request Date</th>
                                    <th style="width: 15%" >E-Mail Sent Date</th>
                                    <th style="width: 5%" >Count</th>
                                    <th style="width: 10%" class="no-sort">Status</th>
                                    <th style="width: 10%" class="no-sort" >Action</th>
                                    
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>                                     
                                    <th>User</th>                                                                                                          
                                    <th>Request Type</th>
                                    <th>Company</th>                                    
                                    <th>Request Date</th>
                                    <th>E-Mail Sent Date</th>
                                    <th>Count</th>
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
                                <div class="form-group col-sm-12">
                                    <label>Request ID:</label>
                                    <input  class="form-control "type="text" id="request_status_update_id" name="request_id" value="" readonly>                                                  
                                </div>                                    
                                <div class="form-group col-sm-12" >
                                    <label>Email Status:</label>
                                    <select  class="form-control" name="request_status" id="request_status">
                                        <?php try{
                                        $request_status = Helpers_Utilities::get_request_status_name();
                                        print_r($request_status); 
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
                                        <?php try{
                                        $request_status = Helpers_Utilities::get_parsing_status_name();
                                        //print_r($request_status); 
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
                {"aaSorting": [[4, "asc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userrequest/ajaxuserrequeststatusresend', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,                   
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Request ID or Requested Value:"
                    },
                            
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });
    function clearSearch() {
        window.location.href = '<?php echo URL::site('userrequest/request_status_resend', TRUE); ?>';
    }    


   //function to open model for request status
function request_resend(requestid) {    
        var request_id=   requestid;
         var result = {request_id: request_id}
                    //ajax to upload device informaiton
                    $.ajax({
                        url: "<?php echo URL::site("userrequest/request_resend_tech"); ?>",
                        type: 'POST',
                        data: result,
                        cache: false,
                        success: function (msg) {
                            if (msg == 1) {
                                swal("Congratulations!", "Request Resend successfully.", "success");
                                refreshGrid();
                             }
                             else if (msg == 2){
                                 swal("System Error", "Contact Support Team.", "error");
                             }                            
                        }
                    });
            }
            
             function request_reject(requestid) {    
        var request_id=   requestid;
         var result = {request_id: request_id}
                swal({
                   title: "Are you sure?",
                   text: "User request will be rejected",
                   type: "warning",
                   showCancelButton: true,
                   confirmButtonClass: "btn-danger",
                   confirmButtonText: "Reject ",
                   closeOnConfirm: false
                 },
                 function(){
                   $.ajax({
                        url: "<?php echo URL::site("userrequest/request_reject_tech"); ?>",
                        type: 'POST',
                        data: result,
                        cache: false,
                        success: function (msg) {
                            if (msg == 1) {
                                swal("Success!", "Request Resend successfully.", "success");
                                refreshGrid();
                             }
                             else if (msg == 2){
                                 swal("System Error", "Contact Support Team.", "error");
                             }                            
                        }
                    });
                 });

            }
		
</script>