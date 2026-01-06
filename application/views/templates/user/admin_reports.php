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
        <i class="fa fa-map-pin"></i>
        Admin Reports 
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >Admin Reports</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">

                    <!-- /.box-header -->
                    <div class="box-body">
                        <!-- BAR CHART -->
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <div class="col-md-2">
                                    <h3 class="box-title">Advance Search</h3>
                                </div>

                                <div class="form-group form-inline col-md-4" style="margin-bottom: 0px">  
                                    <label for="apimonth">Month:</label>
                                    <input type="text"  readonly="readonly"  name="apimonth" id="apimonth">
                                </div>

                                <div class="form-group form-inline col-md-4" style="margin-bottom: 0px"> 
                                    <label for="apiyear">Year:</label>
                                    <input type="text"  readonly="readonly"  name="apiyear" id="apiyear">
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="advancesearch">

                                </div>
                            </div>
                        </div>
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-list"></i> Nadra API Requests</h3>                                               
                        </div>
                        <div class="box-body">
                            <div class="chart">
                                <!--<canvas id="barChart" style="height:230px"></canvas>-->
                                <img id="nodata" style="display:none; width: 325px; margin: auto" class="img-responsive" src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                            </div>
                        </div>
                        <!-- /.box-body -->


                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
    <div style="display:none" id="div-dialog-warning">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><div/></p>
    </div>
</section>
<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>
<script src="<?php echo URL::base() . 'plugins/chartjs/Chart.min.js'; ?>"></script>
<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });

    var today = new Date();
    $('#apimonth').datepicker({
        minViewMode: 'months',
        viewMode: 'months',
        pickTime: false,
        format: 'MM',
        autoclose: true
    });
    $('#apiyear').datepicker({
        minViewMode: 'years',
        viewMode: 'years',
        pickTime: false,
        format: 'yyyy',
        autoclose: true
    });
    $("#apimonth").datepicker('setDate', new Date());
    $("#apiyear").datepicker('setDate', new Date());

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
        $('#apimonth'). datepicker().on('changeDate', function(ev) {
            //Functionality to be called whenever the date is changed
            drawgraph();
        });
        drawgraph();
    });
    /*$('#apimonth').off('change').on('change', function (e) {      
        e.preventDefault();
    if (e.handled !== true) { //Checking for the event whether it has occurred or not.
        e.handled = true; // Basically setting value that the current event has occurred.
        alert("Clicked");
        console.log(e);
      //  jQuery(this).unbind('change');
    }else{
        alert('test');
    }        
    
    
        //drawgraph();
    });*/
//    $('#apiyear').change(function () {
//       // drawgraph();
//    });

//    $('#apimonth').on('change', function() {
//        drawgraph();
//    })

    function drawgraph() {
        var month = $("#apimonth").val();
        var year = $("#apiyear").val();
        var barChart = null;
        var result = {monthapi: month, yearapi: year}
        $.ajax({
            url: "<?php echo URL::site("userreports/nadra_profile_data"); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (msg) {
                barChart = null;
                if (barChart != undefined || barChart != null) {                
                    barChart=null;
                    $('canvas').remove();
                }
                var x = Math.floor((Math.random() * 10) + 1);
                if (msg == '-1')
                {
                    $('#barChart_' + x).hide();
                    $('#nodata').show();
                } else {                    
                    $('.chart').html('');
                    $('.chart').html('<canvas id="barChart_' + x + '" style="height: 229px; width: 1019px;" height="229" width="1019"></canvas>');
                    $('#nodata').hide();
                    $('#barChart').show();
                    var barChartData = JSON.parse(msg);
                    var region = barChartData.region;
                    var requests = barChartData.requests;
                    $(function () {
                        var areaChartData = {
                            // labels: ["H.Q", "Lahore", "Sheikhupura", "Gujranwala", "Rawalpindi", "Sargodha", "Faisalabad", "Sahiwal", "Multan", "Bahwalpur", "D.G Khan"],
                            labels: region,
                            datasets: [
                                {
                                    label: "Nadra Requests",
                                    fillColor: "rgba(210, 214, 222, 1)",
                                    strokeColor: "rgba(210, 214, 222, 1)",
                                    pointColor: "rgba(210, 214, 222, 1)",
                                    pointStrokeColor: "#c1c7d1",
                                    pointHighlightFill: "#fff",
                                    pointHighlightStroke: "rgba(220,220,220,1)",
                                    //data: [28, 48, 40, 19, 86, 27, 90,1,1,1,1]
                                    data: requests
                                }
                            ]
                        };
                        //-------------
                        //- BAR CHART -
                        //-------------
                        var barvar = ("#barChart_" + x);
                        console.log(barvar);
                        var barChartCanvas = $(barvar).get(0).getContext("2d");
                        barChart = new Chart(barChartCanvas);
                        console.log(barChart);
                        
                        var barChartData = areaChartData;
                        barChartData.datasets[0].fillColor = "#00a65a";
                        barChartData.datasets[0].strokeColor = "#00a65a";
                        barChartData.datasets[0].pointColor = "#00a65a";
                        var barChartOptions = {
                            //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                            scaleBeginAtZero: true,
                            //Boolean - Whether grid lines are shown across the chart
                            scaleShowGridLines: true,
                            //String - Colour of the grid lines
                            scaleGridLineColor: "rgba(0,0,0,.05)",
                            //Number - Width of the grid lines
                            scaleGridLineWidth: 1,
                            //Boolean - Whether to show horizontal lines (except X axis)
                            scaleShowHorizontalLines: true,
                            //Boolean - Whether to show vertical lines (except Y axis)
                            scaleShowVerticalLines: true,
                            //Boolean - If there is a stroke on each bar
                            barShowStroke: true,
                            //Number - Pixel width of the bar stroke
                            barStrokeWidth: 2,
                            //Number - Spacing between each of the X value sets
                            barValueSpacing: 5,
                            //Number - Spacing between data sets within X values
                            barDatasetSpacing: 1,
                            //String - A legend template
                            legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                            //Boolean - whether to make the chart responsive
                            responsive: true,
                            maintainAspectRatio: true
                        };

                        barChartOptions.datasetFill = false;
                        barChart.Bar(barChartData, barChartOptions); 
                        
                        if (barChart != undefined || barChart != null) {                        
                            barChart=null;
                        //$('canvas:nth-of-type(1)').remove();
                        }
                    });
                }
                //here
            }
        });
    }
</script> 
