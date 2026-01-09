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
                <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userrequest/request_status_branclessbanking'); ?>" >
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>
                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post) && (empty($search_post['message']) || $search_post['message'] != 1)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="<?php echo (!empty($search_post) && (empty($search_post['message']) || $search_post['message'] != 1)) ? 'display:block;' : ''; ?>">
                            <div class="col-md-12">                                                                
                                <div class="col-md-3"> 
                                    <div class="col-md-12">
                                        <label>Select Company Name </label>
                                        <div class="checkbox">
                                            <label>
                                                <?php 
                                                $banks_list = Helpers_Utilities::get_banks_list();
                                                foreach ($banks_list as $bank) {                                                        
                                                    if ($bank->bank_type == 8) {  ?>
                                                        <input name="bank_id[]" type="checkbox"  <?php echo (!empty($search_post['bank_id']) && in_array($bank->id, $search_post['bank_id'])) ? 'Checked' : ''; ?> value="<?php echo $bank->id;?>" > <?php echo $bank->name;?> <br>
                                                <?php }} ?>
                                            </label>                                  
                                        </div> 
                                    </div>                                    
                                </div>
                                <div class="col-md-8">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Request Status</label>
                                            <div class="radio radio-primary" style="margin-left: 20px">                                            
                                                <input type="radio" <?php echo ((!empty($search_post['r_status']) && ($search_post['r_status'] == 4)) || !isset($search_post['r_status']) ) ? 'checked' : ''; ?>  name="r_status" id="r_status_4" value="4">
                                                <label for="r_status_4" style="padding-left: 2px; margin-right: 25px">
                                                    All
                                                </label>
                                                <input type="radio" <?php echo (!empty($search_post['r_status']) && ($search_post['r_status'] == 1)) ? 'checked' : ''; ?> name="r_status" id="r_status_1" value="1">
                                                <label for="r_status_1" style="padding-left: 2px; margin-right: 25px">
                                                    Request Send
                                                </label>
                                                <input type="radio" <?php echo (!empty($search_post['r_status']) && ($search_post['r_status'] == 2)) ? 'checked' : ''; ?> name="r_status" id="r_status_2" value="2">
                                                <label for="r_status_2" style="padding-left: 2px; margin-right: 25px">
                                                    Request Dispatched
                                                </label>
                                                <input type="radio" <?php echo (isset($search_post['r_status']) && ($search_post['r_status'] == 3)) ? 'checked' : ''; ?> name="r_status" id="r_status_3" value="3">
                                                <label for="r_status_3" style="padding-left: 2px; margin-right: 25px">
                                                    Request Received
                                                </label>
                                            </div>
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
                                </div>
                            </div>                              
                            <div class="col-md-12">
                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                </div>
                            </div>
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
                    <h3 class="box-title"><i class="fa fa-search"></i> Branchless Banking Request Status</h3>
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
                        <table id="requeststatus_ctfu" class="table table-bordered table-striped">
                            <thead>
                                <tr>                                   
                                    <th class="no-sort">User Name</th>                                                                                                          
                                    <th class="no-sort">Request Type</th>                                    
                                    <th class="no-sort">Bank Name</th>
                                    <th class="no-sort">Person Name</th>
                                    <th>Request Date</th>
                                    <th class="no-sort">Request Status</th>                                    
                                    <th class="no-sort" >Action</th>                                    
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>                                     
                                    <th>User Name</th>                                                                                                          
                                    <th>Request Type</th>
                                    <th>Bank Name</th>
                                    <th>Person Name</th>
                                    <th>Request Date</th>
                                    <th>Request Status</th>
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
        objDT = $('#requeststatus_ctfu').dataTable(
                {"aaSorting": [[4, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userrequest/ajaxstatusbranclessbanking', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,                   
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Request ID, Ref ID, Dispatch ID or Requested Value:"
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
        window.location.href = '<?php echo URL::site('userrequest/request_status_branclessbanking', TRUE); ?>';
    }    
    
    
    $("#requesttype").on("change", function(){      
    if ($(this).find(":selected").text() == "All Request"){
     $('#requesttype option').prop('selected', true); 
     $('#requesttype option[value=99]').prop("selected", false);
     $('#requesttype option[value=""]').prop("selected", false);
    }
});
		
</script>