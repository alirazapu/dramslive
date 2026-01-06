<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$telcoemails= Helpers_Email::get_telco_emails();
// print_r($telcoemails); exit;
 foreach ($telcoemails as $telcoemail){
     if($telcoemail->mnc==1){
       $mobilink1=!empty($telcoemail->email1) ? $telcoemail->email1 : '';
       $mobilink2=!empty($telcoemail->email2) ? $telcoemail->email2 : '';
       $mis_second=!empty($telcoemail->is_second) ? $telcoemail->is_second : 0;
     }
     if($telcoemail->mnc==3){
       $ufone1=!empty($telcoemail->email1) ? $telcoemail->email1 : '';
       $ufone2=!empty($telcoemail->email2) ? $telcoemail->email2 : '';
       $uis_second=!empty($telcoemail->is_second) ? $telcoemail->is_second : 0;
     }
     if($telcoemail->mnc==4){
       $zong1=!empty($telcoemail->email1) ? $telcoemail->email1 : '';
       $zong2=!empty($telcoemail->email2) ? $telcoemail->email2 : '';
       $zis_second=!empty($telcoemail->is_second) ? $telcoemail->is_second : 0;
     }
     if($telcoemail->mnc==6){
       $telenor1=!empty($telcoemail->email1) ? $telcoemail->email1 : '';
       $telenor2=!empty($telcoemail->email2) ? $telcoemail->email2 : '';
       $tis_second=!empty($telcoemail->is_second) ? $telcoemail->is_second : 0;
     }
     if($telcoemail->mnc==7){
       $warid1=!empty($telcoemail->email1) ? $telcoemail->email1 : '';
       $warid2=!empty($telcoemail->email2) ? $telcoemail->email2 : '';
       $wis_second=!empty($telcoemail->is_second) ? $telcoemail->is_second : 0;
     }

 }
?>
<!-- Morris charts -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-safari"></i>
        Request Scheduler
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Request Scheduler</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!--First row of three box-->
        <div class="row">
            <div class="col-md-4" style="display: none">
                <!-- DONUT CHART -->
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Today's Requests</h3>

                        <div class="box-tools pull-right">
                            <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" title="Close" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="sales-chart" style="height: 200px; position: relative;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">                
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">All Requests Sending Statistics</h3>

                        <div class="box-tools pull-right">
                            <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" title="Close" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="bar-chart" style="height: 200px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <!-- BAR CHART -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Request Sending Errors</h3>

                        <div class="box-tools pull-right">
                            <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" title="Close" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div style="height: 200px;">
                            <div class="holder">
                                <div class="tickercontainer">
                                    <div class="mask">
                                        <ul id="ticker01" class="newsticker" style="height: auto; top: auto;">
                                            <?php try{
                                            $rows = 0;
                                            $data = Helpers_Utilities::get_send_failed_request();
                                            $rows  = count($data);
                                            //print_r($rows); exit;
                                            foreach ($data as $request) {
                                                $e_request = Helpers_Utilities::encrypted_key($request->request_id,"encrypt");
                                                ?>  
                                                <li><span><?php echo $request->created_at ?></span><a rel="nofollow" href="<?php echo URL::site('userrequest/request_status_detail/' . $e_request) ?>"> <?php echo Helpers_Utilities::get_request_type_name($request->user_request_type_id) ?></a></li>
                                            <?php } 
                                            }  catch (Exception $ex){   }?>                                            
                                        </ul>
                                        <img id="nodata" style="display:<?php echo ($rows == 0) ? 'block' : 'none'?>; width: 534px; margin: auto" class="img-responsive" src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">                
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">All Requests Received Statistics</h3>

                        <div class="box-tools pull-right">
                            <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" title="Close" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="received-stats" style="height: 200px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--end of first row, with three box-->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title right">Daily Request's Send count </h3>
                        (<span class="blink">Today Total: <b><?php try{ echo Helpers_Utilities::get_today_request_count(); }  catch (Exception $ex){   }?></b> </span>)
                        <div class="box-tools pull-right">
                            <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" title="Close" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="telco_daily_count" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">                
                <div class="box box-warning" style="height: 264px">
                    <div class="box-header with-border">
                        <h3 class="box-title">Company Last Response</h3>
                        <div class="box-tools pull-right">
                            <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" title="Close" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body last-response">
                        
                            <?php try{
                            $mobile_companies = Helpers_Utilities::get_companies_data();                            
                            foreach ($mobile_companies as $compnay){
                                $value_day = 2;
                                $compnay_name = $compnay->company_name;
                                $compnay_mnc  = $compnay->mnc;
                                $response_date = Helpers_Utilities::get_company_last_response_date($compnay_mnc);
                                if ($response_date != 0) {
                                    $differenceFormat = '%d Day %h Hours %i Minutes %s Seconds';
                                    $current_date = date("Y-m-d h:i:s");
                                    $response_date = date("Y-m-d h:i:s", strtotime($response_date));
                                    $datetime1 = date_create($response_date);
                                    $datetime2 = date_create($current_date);
                                    $interval = date_diff($datetime1, $datetime2);
                                    $value = $interval->format($differenceFormat);
                                    
                                    $value_day = $interval->format('%d');
                                    
                                }else{
                                    $value = 'Un-Known';
                                }
                                ?>                        
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box <?php if($value_day == 1){echo 'bg-yellow';}elseif ($value_day > 1) {echo 'bg-red';} else{    echo 'bg-green';} ?>">
                                    <span class="info-box-icon"><i class="fa  fa-clock-o"></i></span>

                                    <div class="info-box-content">
                                        <strong class="info-box-text"><?php echo $compnay_name; ?></strong>
                                        <span class=""><?php echo $value; ?></span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                            
                            <?php }
                            }  catch (Exception $ex){   }?>
                                                    
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box box-primary">

                        <div class="box box-default">
                            <div class="box-header with-border">
                                <span><h3 class="box-title">User's Requests</h3><button type="button" style="margin-top: -5px" onclick="telcoemailconfig()" class="btn btn-primary small pull-right">Telco Email Configuration</button></span>

                            </div>
                        </div>                    
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="request_schedular" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                        
                                        <th class="no-sort">User Name</th>
                                        <th class="no-sort">Posted IN</th>
                                        <th class="no-sort">Request Type</th>
                                        <th class="no-sort">Company</th>
                                        <th class="no-sort">Requested Value</th>
                                        <th class="no-sort">Request Date</th>
                                        <th> Priority</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>                                        
                                        <th class="no-sort">User Name</th>
                                        <th class="no-sort">Posted IN</th>
                                        <th class="no-sort">Request Type</th>
                                        <th class="no-sort">Company</th>
                                        <th class="no-sort">Requested Value</th>
                                        <th class="no-sort">Request Date</th>
                                        <th class="no-sort">Priority</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<!--        acl right div-->
<div class="modal modal-info fade" id="telcoemailconfig">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Telco Email's Configuration</h4>
            </div>
            
            <form class="" name="telcoemail" id="telcoemail" action="<?php echo url::site() . 'email/update_telcoemail_config/' ?>"  method="post" enctype="multipart/form-data" >  

                <div class="modal-body" style='background-color: #fff !important; color: black !important; '>
                    <!--Telco Email config-->                  
                       
                        <div class="col-md-12" style="background-color: #fff;color: black">
                            <hr class="style14 ">
                            <div class="form-group col-sm-12"> 
                                <div class="col-sm-5"> 
                                    <label>Telenor:</label> 
                                <input  class="form-control " type="email" id="telenor1" name="telenor1" value="<?php echo $telenor1; ?>" placeholder="Email-1">                                                  
                                </div>
                                <div class="col-sm-4" style="padding: 24px"> 
                                <input  class="form-control " type="email" id="telenor2" name="telenor2" value="<?php echo $telenor2; ?>" placeholder="Email-2">                                                  
                                </div>
                                <div class="col-sm-3" style="padding: 24px"> 
                                <div class="checkbox">
                                    <label><input name="tis_second" type="checkbox" <?php echo ($tis_second==1)?"checked":''; ?> value="<?php echo $tis_second; ?>">Is Second</label>
                                </div>
                                </div>                                    
                            </div> 
                            <div class="form-group col-sm-12"> 
                                <div class="col-sm-5"> 
                                    <label>Zong:</label> 
                                <input  class="form-control " type="email" id="zong1" name="zong1" value="<?php echo $zong1; ?>" placeholder="Email-1">                                                  
                                </div>
                                <div class="col-sm-4" style="padding: 24px"> 
                                <input  class="form-control " type="email" id="zong2" name="zong2" value="<?php echo $zong2; ?>" placeholder="Email-2">                                                  
                                </div>
                                <div class="col-sm-3" style="padding: 24px"> 
                                <div class="checkbox">
                                    <label><input name="zis_second" type="checkbox" <?php echo ($zis_second==1)?"checked":''; ?> value="<?php echo $zis_second; ?>">Is Second</label>
                                </div>
                                </div>
                            </div> 
                            <div class="form-group col-sm-12"> 
                                <div class="col-sm-5"> 
                                    <label>Mobilink:</label> 
                                <input  class="form-control " type="email" id="mobilink1" name="mobilink1" value="<?php echo $mobilink1; ?>" placeholder="Email-1">                                                  
                                </div>
                                <div class="col-sm-4" style="padding: 24px"> 
                                <input  class="form-control " type="email" id="mobilink2" name="mobilink2" value="<?php echo $mobilink2; ?>" placeholder="Email-2">                                                  
                                </div>
                                <div class="col-sm-3" style="padding: 24px"> 
                                <div class="checkbox">
                                    <label><input name="mis_second" type="checkbox" <?php echo ($mis_second==1)?"checked":''; ?> value="<?php echo $mis_second; ?>">Is Second</label>
                                </div>
                                </div>
                            </div> 
                            <div class="form-group col-sm-12"> 
                                <div class="col-sm-5"> 
                                    <label>Warid:</label> 
                                <input  class="form-control " type="email" id="warid1" name="warid1" value="<?php echo $warid1; ?>" placeholder="Email-1">                                                  
                                </div>
                                <div class="col-sm-4" style="padding: 24px"> 
                                <input  class="form-control " type="email" id="warid2" name="warid2" value="<?php echo $warid2; ?>" placeholder="Email-2">                                                  
                                </div>
                                <div class="col-sm-3" style="padding: 24px"> 
                                <div class="checkbox">
                                    <label><input name="wis_second" type="checkbox" <?php echo ($wis_second==1)?"checked":''; ?> value="<?php echo $wis_second; ?>">Is Second</label>
                                </div>
                                </div>
                            </div> 
                            <div class="form-group col-sm-12"> 
                                <div class="col-sm-5"> 
                                    <label>Ufone:</label> 
                                <input  class="form-control " type="email" id="ufone1" name="ufone1" value="<?php echo $ufone1; ?>" placeholder="Email-1">                                                  
                                </div>
                                <div class="col-sm-4" style="padding: 24px"> 
                                <input  class="form-control " type="email" id="ufone2" name="ufone2" value="<?php echo $ufone2; ?>" placeholder="Email-2">                                                  
                                </div>
                                <div class="col-sm-3" style="padding: 24px"> 
                                <div class="checkbox">
                                    <label><input name="uis_second" type="checkbox" <?php echo ($uis_second==1)?"checked":''; ?> value="<?php echo $uis_second; ?>">Is Second</label>
                                </div>
                                </div>
                            </div> 
                            <div class="form-group col-sm-12" >
                                <hr class="style14 ">
                            </div>
                            <!--<div class="form-group col-sm-12"></div>-->
                            <span id="" class="text-black" > </span>
                        </div>                   
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                    <button type="submit"  class="btn btn-primary ">Update</button>
                </div>  
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<link rel="stylesheet" href="<?php echo URL::base() . 'dist/css/morris.css'; ?>">
<script src="<?php echo URL::base() . 'dist/js/morris.min.js'; ?>"></script>
<script src="<?php echo URL::base() . 'dist/js/fastclick.js'; ?>"></script>
<script src="<?php echo URL::base() . 'dist/js/raphael-min.js'; ?>"></script>
<!--<script src="<?php // echo URL::base() . 'dist/js/jquery.min.js.download';      ?>"></script>-->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

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
        objDT = $('#request_schedular').dataTable(
                {"aaSorting": [[6, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "mark": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userrequest/ajaxrequestschedular', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "User Name,Request ID,Reference No. or Requested Value:"
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
                check_list: true,
            },
            key: {
                key_value: true,
                required: true,
            },
            "posting[]": {
                required: true,
            },
            "activity[]": {
                required: true,
            },
        },
        messages: {
            field: {
                check_list: "Please select search type",
            },
            key: {
                key_value: "Enter Valid Search Value",
            },
            "posting[]": {
                required: "Select any option from list",
            },
            "activity[]": {
                required: "Select any option from list",
            },
        }
    });
    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "def") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("key_value", function (sel, element) {
        if ($('#searchfield').val() !== "") {
            return true;
        } else {
            return false;
        }
    }, "<span>Select One</span>");

    function ChangePriority(request_id)
    {
        $.confirm({
            'title': 'Change Priority Confirmation',
            'message': 'Do you really want to change priority of this request?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        var request_priority = $("#priority-" + request_id).val();
                        var result = {requestid: request_id, requestp: request_priority}
                        $.ajax({
                            url: "<?php echo URL::site("Userrequest/ChangePriority"); ?>",
                            type: 'POST',
                            data: result,
                            cache: false,
                            //dataType: "text",
                            success: function (msg) {
                                refreshGrid();
                                if (msg == 2){
                                 swal("System Error", "Contact Support Team.", "error");
                             }
                            }
                            
                        });
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }

    $.ajax({
        url: "<?php echo URL::site("Userrequest/dailyrequestcomparison"); ?>",
        //type: 'POST',
        //data: result,
        cache: false,
        //dataType: "text",
        dataType: 'json',
        success: function (msg) {
            if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
            var i;
            var total_request = msg.today_total;
            var send_request = msg.today_send;
            var pending_request = msg.today_pending;
            var error_request = msg.today_error;

            $(function () {
//DONUT CHART
                var donut = new Morris.Donut({
                    element: 'sales-chart',
                    resize: true,
                    colors: ["#3c8dbc", "#f56954", "#00a65a", "#f39c12"],
                    data: [
                        {label: "Total Request", value: total_request},
                        {label: "Errors", value: error_request},
                        {label: "Completed Requests", value: send_request},
                        {label: "Pending Requests", value: pending_request}
                    ],
                    hideHover: 'auto'
                });
            });
        }
    });
    $.ajax({
        url: "<?php echo URL::site("Userrequest/totalrequestcomparison"); ?>",
        //type: 'POST',
        //data: result,
        cache: false,
        //dataType: "text",
        dataType: 'json',
        success: function (msg) {
            if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
            var i;
            var total_request = msg.total;
            var send_request = msg.send;
            var pending_request = msg.pending;
            var error_request = msg.error;

            $(function () {
                //BAR CHART
                var bar = new Morris.Bar({
                    element: 'bar-chart',
                    resize: true,
                    data: [
                        {y: 'Total Requests', total: total_request, completed: send_request, pending: pending_request, error: error_request}
                    ],
                    barColors: ["#3c8dbc", "#00a65a", "#f39c12", "#f56954"],
                    xkey: 'y',
                    ykeys: ['total', 'completed', 'pending', 'error'],
                    labels: ['Total', 'Completed', 'Pending', 'Error'],
                    hideHover: 'auto'

                });
            });
        }
    });
    $.ajax({
        url: "<?php echo URL::site("Userrequest/totalreceivedcomparison"); ?>",
        //type: 'POST',
        //data: result,
        cache: false,
        //dataType: "text",
        dataType: 'json',
        success: function (msg) {
            if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
            var i;
            var total_request = msg.total;
            var received_request = msg.received;
            var pending_request = msg.pending;

            $(function () {
                //BAR CHART
                var bar = new Morris.Bar({
                    element: 'received-stats',
                    resize: true,
                    data: [
                        {y: 'Requests Received', total: total_request, received: received_request, pending: pending_request}
                    ],
                    barColors: ["#3c8dbc", "#00a65a", "#f39c12"],
                    xkey: 'y',
                    ykeys: ['total', 'received', 'pending'],
                    labels: ['E-Mail Sent', 'E-Mail Received', 'E-Mail Pending'],
                    hideHover: 'auto'

                });
            });
        }
    });
    $.ajax({
        url: "<?php echo URL::site("Userrequest/dailyrequestcount"); ?>",
        //type: 'POST',
        //data: result,
        cache: false,
        //dataType: "text",
        dataType: 'json',
        success: function (record) {
             if(record== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
            $(function () {
                //BAR CHART
                var bar = new Morris.Bar({
                    element: 'telco_daily_count',
                    resize: true,
                    data: [
                        {y: 'Mobilink', normal: record.mobilink_normal, medium: record.mobilink_medium, high: record.mobilink_high},
                        {y: 'Warid', normal: record.warid_normal, medium: record.warid_medium, high: record.warid_high},
                        {y: 'Ufone', normal: record.ufone_normal, medium: record.ufone_medium, high: record.ufone_high},
                        {y: 'Telenor', normal: record.telenor_normal, medium: record.telenor_medium, high: record.telenor_high},
                        {y: 'Zong', normal: record.zong_normal, medium: record.zong_medium, high: record.zong_high},
                        {y: 'PTCL', normal: record.ptcl_normal, medium: record.ptcl_medium, high: record.ptcl_high},
                        {y: 'International', normal: record.international_normal, medium: record.international_medium, high: record.international_high},
                        {y: 'NADRA', normal: record.nadra_normal, medium: record.nadra_medium, high: record.nadra_high},
                    ],
                    barColors: ["#3c8dbc", "#00a65a", "#f39c12", "#f56954", "#942f88", "#2f946f"],
                    xkey: 'y',
                    ykeys: ['normal', 'medium', 'high'],
                    labels: ['Normal', 'Medium', 'High'],
                    yLabels: '',
                    hideHover: 'auto',
                    grid: 'true',
                    postUnits: '',
                    resize: 'true'

                });
            });
        }
    });
//    $.ajax({
//        url: "<?php/* echo URL::site("Userrequest/company_response"); */?>",
//        //type: 'POST',
//        //data: result,
//        cache: false,
//        //dataType: "text",
//        dataType: 'json',
//        success: function (msg) {
//            var i;
//            var mobilink_time = msg.mobilink_data;
//            var warid_time = msg.warid_data;
//            var ufone_time = msg.ufone_data;
//            var telenor_time = msg.telenor_data;
//            var zong_time = msg.zong_data;
//
//            $(function () {
//                //BAR CHART
//                var bar = new Morris.Bar({
//                    element: 'response-stats',
//                    resize: true,
//                    data: [
//                        {label: 'Companies Respose Time', mobilink: mobilink_time, warid: warid_time, ufone: ufone_time, telenor: telenor_time, zong: zong_time}
//                    ],
//                    barColors: ["#3c8dbc", "#00a65a", "#f39c12"],
//                    xkey: 'label',
//                    ykeys: ['mobilink', 'warid', 'ufone', 'telenor', 'zong'],
//                    labels: ['Mobilink', 'Warid', 'Ufone', 'Telenor', 'Zong'],
//                    hideHover: 'auto'
//
//                });
//            });
//        }
//    });
</script>
<script>
    jQuery.fn.liScroll = function (settings) {
        settings = jQuery.extend({
            travelocity: 0.03
        }, settings);
        return this.each(function () {
            var $strip = jQuery(this);
            $strip.addClass("newsticker")
            var stripHeight = 1;
            $strip.find("li").each(function (i) {
                stripHeight += jQuery(this, i).outerHeight(true); // thanks to Michael Haszprunar and Fabien Volpi
            });
            var $mask = $strip.wrap("<div class='mask'></div>");
            var $tickercontainer = $strip.parent().wrap("<div class='tickercontainer'></div>");
            var containerHeight = $strip.parent().parent().height();	//a.k.a. 'mask' width 	
            $strip.height(stripHeight);
            var totalTravel = stripHeight;
            var defTiming = totalTravel / settings.travelocity;	// thanks to Scott Waye		
            function scrollnews(spazio, tempo) {
                $strip.animate({top: '-=' + spazio}, tempo, "linear", function () {
                    $strip.css("top", containerHeight);
                    scrollnews(totalTravel, defTiming);
                });
            }
            scrollnews(totalTravel, defTiming);
            $strip.hover(function () {
                jQuery(this).stop();
            },
                    function () {
                        var offset = jQuery(this).offset();
                        var residualSpace = offset.top + stripHeight;
                        var residualTime = residualSpace / settings.travelocity;
                        scrollnews(residualSpace, residualTime);
                    });
        });
    };

    $(function () {
        $("ul#ticker01").liScroll();
    });
    //# sourceURL=pen.js
    
     /* For telco emails config*/
    function telcoemailconfig() {
        $("#telcoemailconfig").modal("show");
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
    
    
</script>