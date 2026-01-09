<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Morris charts -->


<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-safari"></i>
        Parsing Queue
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Parsing Queue</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->    

        <div class="row">

            <div class="col-md-4">
                <!-- BAR CHART -->
                <!-- BAR CHART -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Total Request Parsing Status</h3>

                        <div class="box-tools pull-right">
                            <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" title="Close" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="bar-chart" style="height: 300px;"></div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-md-4">
                <!-- DONUT CHART -->
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Today's Request Parsing Status</h3>

                        <div class="box-tools pull-right">
                            <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" title="Close" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div class="chart" id="sales-chart" style="height: 300px; position: relative;"></div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="col-md-4">
                <!-- BAR CHART -->
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Parsing Errors</h3>

                        <div class="box-tools pull-right">
                            <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" title="Close" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body chart-responsive">
                        <div style="height: 300px;">
                            <div class="holder">
                                <div class="tickercontainer">
                                    <div class="mask">
                                        <ul id="ticker01" class="newsticker" style="height: 100%; top: auto;">
                                            <?php try{
                                            $rows = 0;
                                            $data = Helpers_Utilities::get_processing_error_request();                                            
                                            $rows  = count($data);
                                            foreach ($data as $request) {
                                                $e_request = Helpers_Utilities::encrypted_key($request->req_id,"encrypt");
                                                ?>  
                                                <li><span><?php echo $request->rec_date ?></span><a rel="nofollow" href="<?php echo URL::site('userrequest/request_status_detail/' . $e_request) ?>"> <?php echo Helpers_Utilities::get_request_type_name($request->req_type_id) ?></a></li>

                                                <?php
                                            }
                                            }  catch (Exception $ex){   }?>
                                        </ul>
                                        <img id="nodata" style="display:<?php echo ($rows == 0) ? 'block' : 'none'?>; width: 534px; margin: auto" class="img-responsive" src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
<!--        <div class="col-md-12">
             BAR CHART 
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Parsing Completion Status (Percent)</h3>

                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" title="Close" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body chart-responsive">
                    <div class="chart" id="parsingstatus" style="height: 300px;"></div>
                </div>
                 /.box-body 
            </div>
             /.box 
        </div> -->

        <div class="row">
            <div class="col-xs-12">

                <div class="box">

                    <!-- /.box-header -->
                    <div class="box-body">
                        <!--table data-->
                        <div class="table-responsive">
                            <table id="parsing_queue" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                        
                                        <th class="no-sort">Username</th>
                                        <th class="no-sort">Posted In</th>
                                        <th class="no-sort">Request Type</th>
                                        <th class="no-sort">Request Send Date</th>
                                        <th class="no-sort">Requested Value</th>
                                        <th>Response Date</th>
                                        <th class="no-sort">Parsing Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                                <tfoot>
                                    <tr>                                        
                                        <th>Username</th>
                                        <th>Posted In</th>
                                        <th>Request Type</th>
                                        <th>Request send Date</th>
                                        <th>Requested Value</th>
                                        <th>Response Date</th>
                                        <th>Parsing Status</th>
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

<link rel="stylesheet" href="<?php echo URL::base() . 'dist/css/morris.css'; ?>">
<script src="<?php echo URL::base() . 'dist/js/morris.min.js'; ?>"></script>
<script src="<?php echo URL::base() . 'dist/js/fastclick.js'; ?>"></script>
<script src="<?php echo URL::base() . 'dist/js/raphael-min.js'; ?>"></script>
<!--<script src="<?php // echo URL::base() . 'dist/js/jquery.min.js.download';  ?>"></script>-->
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
        objDT = $('#parsing_queue').dataTable(
                {"aaSorting": [[5, "asc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('Userrequest/ajaxrequestqueue', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By User Name or Requested Value:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });


    $.ajax({
        url: "<?php echo URL::site("Userrequest/dailyparsingcomparison"); ?>",
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
            var parsing_complete = msg.today_complete;
            var pending_request = msg.today_pending;
            var error_request = msg.today_error;

            $(function () {
                //DONUT CHART
                var donut = new Morris.Donut({
                    element: 'sales-chart',
                    resize: true,
                    colors: ["#3c8dbc", "#f56954", "#00a65a", "#f39c12"],
                    data: [
                        {label: "Total Received", value: total_request},
                        {label: "Total Errors", value: error_request},
                        {label: "Parsing Completed", value: parsing_complete},
                        {label: "Parsing Waiting", value: pending_request}
                    ],
                    hideHover: 'auto'
                });
            });
        }
    });
    $.ajax({
        url: "<?php echo URL::site("Userrequest/totalparsingcomparison"); ?>",
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
            var total_received = msg.total;
            var total_complete = msg.total_complete;
            var total_error = msg.total_error;
            var total_pending = msg.total_pending;

            $(function () {
                //BAR CHART
                var bar = new Morris.Bar({
                    element: 'bar-chart',
                    resize: true,
                    data: [
                        {y: 'Total Requests Parsing Status', total: total_received, completed: total_complete, error: total_error, pending: total_pending}
                    ],
                    barColors: ["#3c8dbc", "#00a65a", "#f56954", "#f39c12"],
                    xkey: 'y',
                    ykeys: ['total', 'completed', 'error', 'pending'],
                    labels: ['Total Received', 'Parsing Completed', 'Parsing Error', 'Parsing Pending'],
                    hideHover: 'auto'

                });
            });
        }
    });
    var bar = new Morris.Bar({
        element: 'parsingstatus',
        resize: true,
        data: [
            {y: 'Mobilink', subs: 0, currentlocation: 0, smsdetailsagainstsim: 5, cdragainstsims: 0, cdragainstimei: 15, simsagainstcnic: 0},
            {y: 'Warid', subs: 0, currentlocation: 5, smsdetailsagainstsim: 9, cdragainstsims: 0, cdragainstimei: 6, simsagainstcnic: 0},
            {y: 'Ufone', subs: 0, currentlocation: 8, smsdetailsagainstsim: 45, cdragainstsims: 5, cdragainstimei: 0, simsagainstcnic: 51},
            {y: 'Telenor', subs: 36, currentlocation: 0, smsdetailsagainstsim: 0, cdragainstsims: 0, cdragainstimei: 90, simsagainstcnic: 0},
            {y: 'Zong', subs: 21, currentlocation: 0, smsdetailsagainstsim: 48, cdragainstsims: 0, cdragainstimei: 0, simsagainstcnic: 0}
        ],
        barColors: ["#3c8dbc", "#00a65a", "#f39c12", "#f56954", "#942f88", "#2f946f"],
        xkey: 'y',
        ykeys: ['subs', 'currentlocation', 'smsdetailsagainstsim', 'cdragainstsims', 'cdragainstimei', 'simsagainstcnic'],
        labels: ['Subsciber', 'Cur_Loc', 'SMS_Details', 'CDR_Mobile', 'CDR_IMEI', 'CNIC_SIMs'],
        ymax: '100',
        ymin: '0',
        hideHover: 'auto',
        grid: 'true',
        postUnits: ' %',
        interval: '10',
        resize: 'true'
    });

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
</script>