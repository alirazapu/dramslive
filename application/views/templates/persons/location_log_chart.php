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
        Person's Portal
        <small><?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id=' . $_GET['id']); ?>">Person Dashboard </a></li>
        <li class="active">CDR Location Chart</li>
    </ol>
</section>
<!-- Main content -->

<section class="content">
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Location Chart Between 6am-11am</h3>
                <div class="box-tools pull-right">
                    <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-12">
                    <div class="chart">
                        <canvas id="barChart1" style="height:245px"></canvas>
                        <img id="nodatachart1" style="display:none; width: 370px; margin: auto" class="img-responsive"
                             src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                    </div>
                </div>
                <div class="col-md-12">
                    <ol id="locdetail1" class="fancy"></ol>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Location Chart Between 11am-5pm</h3>
                <div class="box-tools pull-right">
                    <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-12">
                    <div class="chart">
                        <canvas id="barChart2" style="height:245px"></canvas>
                        <img id="nodatachart2" style="display:none; width: 370px; margin: auto" class="img-responsive"
                             src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                    </div>
                </div>
                <div class="col-md-12">
                    <ol id="locdetail2" class="fancy"></ol>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Location Chart Between 5pm-12am </h3>
                <div class="box-tools pull-right">
                    <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-12">
                    <div class="chart">
                        <canvas id="barChart" style="height:245px"></canvas>
                        <img id="nodatachart" style="display:none; width: 370px; margin: auto" class="img-responsive"
                             src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                    </div>
                </div>
                    <div class="col-md-12">
                        <ol id="locdetail" class="fancy"></ol>
                    </div>
                </div>
            </div>
        </div>

    <div class="col-md-12">
        <div class="box box-success" >
            <div class="box-header with-border">
                <h3 class="box-title">Location Chart Between 12am-6am</h3>
                <div class="box-tools pull-right">
                    <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-12">
                    <div class="chart">
                        <canvas id="barChart3" style="height:245px"></canvas>
                        <img id="nodatachart3" style="display:none; width: 370px; margin: auto" class="img-responsive"
                             src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                    </div>
                    <div class="col-md-12">
                        <ol id="locdetail3" class="fancy"></ol>
                    </div>
                </div>
            </div>
        </div>
    </div>


</section>

<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/chartjs/Chart.min.js'; ?>"></script>

<script>

    $.ajax({
        url: "<?php echo URL::site('persons/ajax_loc_chartt1/?id=' . $_GET['id'], True); ?>",
        //type: 'POST',
        //data: result,
        cache: false,
        //dataType: "text",
        dataType: 'json',
        success: function (msg) {
            var i;
            var location = [];
            var address = [];
            var count = [];

            var trHTML = '';
            for (var f = 0; f < msg.t1.length; f++) {
                var add =msg.t1[f]['address'];


               var url1= "<?php echo URL::site('persons/location_log_summary/?id='.$_GET['id'].'&st=6am&et=11am&add='); ?>"+ add;
                 trHTML += '<li>' +  msg.t1[f]['address'] + ' (<strong> <h7 style="color:red;"> <a href="'+ url1 +'" title="View Information" >' + msg.t1[f]['loc_count'] + '</a></strong> )' + '</li>';

            }
            $('#locdetail1').html(trHTML);

            if (msg.t1.length < 1) {

                $('#barChart1').hide();
                $('#nodatachart1').show();
            } else {

                $('#nodatachart1').hide();
                $('#barChart1').show();
                for (i = 0; i < msg.t1.length; ++i) {
                    // do something with `substr[i]`
                    location.push(i + 1);
                    count.push(msg.t1[i].loc_count);
                    address.push(msg.t1[i].address);
                }
                //console.log(address);


                var areaChartData = {
                    //labels: ["January", "February", "March", "April", "May", "June", "July"],
                    labels: location,
                    datasets: [
                        {
                            label: "Location Count",
                            fillColor: "rgba(255, 255, 255, 1)",
                            strokeColor: "rgba(0, 128, 0, 1)",
                            pointColor: "rgba(210, 214, 222, 1)",
                            pointStrokeColor: "#c1c7d1",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(220,220,220,1)",
                            //  data: [35, 19, 28, 17, 46, 55, 40]
                            data: count
                        }
                    ],
                };


                //-------------
                //- BAR CHART -
                //-------------
                var barChartCanvas = $("#barChart1").get(0).getContext("2d");
                var barChart = new Chart(barChartCanvas);
                var barChartData = areaChartData;
                //   barChartData.datasets[1].fillColor = "#00a65a";
                //  barChartData.datasets[1].strokeColor = "#00a65a";
                // barChartData.datasets[1].pointColor = "#00a65a";
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
                    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<address.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(address[i]){%><%=address[i]%><%}%></li><%}%></ul>",
                    //Boolean - whether to make the chart responsive
                    responsive: true,
                    maintainAspectRatio: true,
                    tooltips: {
                        callbacks: {
                            title: function (tooltipItems, data) {
                                return data.datasets[tooltipItems[0].datasetIndex].label;
                            },
                            label: function (tooltipItem, data) {
                                return '$' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 20000000,

                                userCallback: function (value, index, values) {

                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);

                                    value = value.join(',');
                                    return '$' + value;
                                }
                            }
                        }]
                    }
                };

                barChartOptions.datasetFill = false;
                barChart.Bar(barChartData, barChartOptions);
            }
            if (msg == 2) {
                swal("System Error", "Contact Support Team.", "error");
            }
        }
    });
    $.ajax({
        url: "<?php echo URL::site('persons/ajax_loc_chartt2/?id=' . $_GET['id'], True); ?>",
        //type: 'POST',
        //data: result,
        cache: false,
        //dataType: "text",
        dataType: 'json',
        success: function (msg) {
            var i;
            var location = [];
            var address = [];
            var count = [];

            var trHTML = '';
            for (var f = 0; f < msg.t2.length; f++) {
                var add =msg.t2[f]['address'];

                var url2= "<?php echo URL::site('persons/location_log_summary/?id='.$_GET['id'].'&st=11am&et=5pm&add='); ?>"+ add;
                trHTML += '<li>' +  msg.t2[f]['address'] + ' (<strong> <h7 style="color:red;"> <a href="'+ url2 +'" title="View Information" >' + msg.t2[f]['loc_count'] + '</a></strong> )' + '</li>';


            //    trHTML += '<li>' + msg.t2[f]['address'] + ' (<strong> <h7 style="color:red;">' + msg.t2[f]['loc_count'] + '</strong> )' + '</li>';
            }
            $('#locdetail2').html(trHTML);

            if (msg.t2.length < 1) {

                $('#barChart2').hide();
                $('#nodatachart2').show();
            } else {

                $('#nodatachart2').hide();
                $('#barChart2').show();
                for (i = 0; i < msg.t2.length; ++i) {
                    // do something with `substr[i]`
                    location.push(i + 1);
                    count.push(msg.t2[i].loc_count);
                    address.push(msg.t2[i].address);
                }
                //console.log(address);


                var areaChartData = {
                    //labels: ["January", "February", "March", "April", "May", "June", "July"],
                    labels: location,
                    datasets: [
                        {
                            label: "Location Count",
                            fillColor: "rgba(255, 255, 255, 1)",
                            strokeColor: "rgba(0, 128, 0, 1)",
                            pointColor: "rgba(210, 214, 222, 1)",
                            pointStrokeColor: "#c1c7d1",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(220,220,220,1)",
                            //  data: [35, 19, 28, 17, 46, 55, 40]
                            data: count
                        }
                    ],
                };


                //-------------
                //- BAR CHART -
                //-------------
                var barChartCanvas = $("#barChart2").get(0).getContext("2d");
                var barChart1 = new Chart(barChartCanvas);
                var barChartData = areaChartData;
                //   barChartData.datasets[1].fillColor = "#00a65a";
                //  barChartData.datasets[1].strokeColor = "#00a65a";
                // barChartData.datasets[1].pointColor = "#00a65a";
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
                    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<address.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(address[i]){%><%=address[i]%><%}%></li><%}%></ul>",
                    //Boolean - whether to make the chart responsive
                    responsive: true,
                    maintainAspectRatio: true,
                    tooltips: {
                        callbacks: {
                            title: function (tooltipItems, data) {
                                return data.datasets[tooltipItems[0].datasetIndex].label;
                            },
                            label: function (tooltipItem, data) {
                                return '$' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 20000000,

                                userCallback: function (value, index, values) {

                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);

                                    value = value.join(',');
                                    return '$' + value;
                                }
                            }
                        }]
                    }
                };

                barChartOptions.datasetFill = false;
                barChart1.Bar(barChartData, barChartOptions);
            }
            if (msg == 2) {
                swal("System Error", "Contact Support Team.", "error");
            }
        }
    });
    $.ajax({
        url: "<?php echo URL::site('persons/ajax_loc_chartt/?id=' . $_GET['id'], True); ?>",
        //type: 'POST',
        //data: result,
        cache: false,
        //dataType: "text",
        dataType: 'json',
        success: function (msg) {
            var i;
            var location = [];
            var address = [];
            var count = [];

            var trHTML = '';
            for (var f = 0; f < msg.t.length; f++) {
                var add =msg.t[f]['address'];

                var url= "<?php echo URL::site('persons/location_log_summary/?id='.$_GET['id'].'&st=5pm&et=12am&add='); ?>"+ add;
                trHTML += '<li>' +  msg.t[f]['address'] + ' (<strong> <h7 style="color:red;"> <a href="'+ url +'" title="View Information" >' + msg.t[f]['loc_count'] + '</a></strong> )' + '</li>';

                // trHTML += '<li>' + msg.t[f]['address'] + ' (<strong> <h7 style="color:red;">' + msg.t[f]['loc_count'] + '</strong> )' + '</li>';
            }
            $('#locdetail').html(trHTML);

            if (msg.t.length < 1) {

                $('#barChart').hide();
                $('#nodatachart').show();
            } else {

                $('#nodatachart').hide();
                $('#barChart').show();
                for (i = 0; i < msg.t.length; ++i) {
                    // do something with `substr[i]`
                    location.push(i + 1);
                    count.push(msg.t[i].loc_count);
                    address.push(msg.t[i].address);
                }
                //console.log(address);


                var areaChartData = {
                    //labels: ["January", "February", "March", "April", "May", "June", "July"],
                    labels: location,
                    datasets: [
                        {
                            label: "Location Count",
                            fillColor: "rgba(255, 255, 255, 1)",
                            strokeColor: "rgba(0, 128, 0, 1)",
                            pointColor: "rgba(210, 214, 222, 1)",
                            pointStrokeColor: "#c1c7d1",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(220,220,220,1)",
                            //  data: [35, 19, 28, 17, 46, 55, 40]
                            data: count
                        }
                    ],
                };


                //-------------
                //- BAR CHART -
                //-------------
                var barChartCanvas = $("#barChart").get(0).getContext("2d");
                var barChart = new Chart(barChartCanvas);
                var barChartData = areaChartData;
                //   barChartData.datasets[1].fillColor = "#00a65a";
                //  barChartData.datasets[1].strokeColor = "#00a65a";
                // barChartData.datasets[1].pointColor = "#00a65a";
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
                    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<address.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(address[i]){%><%=address[i]%><%}%></li><%}%></ul>",
                    //Boolean - whether to make the chart responsive
                    responsive: true,
                    maintainAspectRatio: true,
                    tooltips: {
                        callbacks: {
                            title: function (tooltipItems, data) {
                                return data.datasets[tooltipItems[0].datasetIndex].label;
                            },
                            label: function (tooltipItem, data) {
                                return '$' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 20000000,

                                userCallback: function (value, index, values) {

                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);

                                    value = value.join(',');
                                    return '$' + value;
                                }
                            }
                        }]
                    }
                };

                barChartOptions.datasetFill = false;
                barChart.Bar(barChartData, barChartOptions);
            }
            if (msg == 2) {
                swal("System Error", "Contact Support Team.", "error");
            }
        }
    });
    $.ajax({
        url: "<?php echo URL::site('persons/ajax_loc_chartt3/?id=' . $_GET['id'], True); ?>",
        //type: 'POST',
        //data: result,
        cache: false,
        //dataType: "text",
        dataType: 'json',
        success: function (msg) {
            var i;
            var location = [];
            var address = [];
            var count = [];

            var trHTML = '';
            for (var f = 0; f < msg.t3.length; f++) {
                var add =msg.t3[f]['address'];

                var url3= "<?php echo URL::site('persons/location_log_summary/?id='.$_GET['id'].'&st=12am&et=6am&add='); ?>"+ add;
                trHTML += '<li>' +  msg.t3[f]['address'] + ' (<strong> <h7 style="color:red;"> <a href="'+ url3 +'" title="View Information" >' + msg.t3[f]['loc_count'] + '</a></strong> )' + '</li>';

                // trHTML += '<li>' + msg.t3[f]['address'] + ' (<strong> <h7 style="color:red;">' + msg.t3[f]['loc_count'] + '</strong> )' + '</li>';
            }
            $('#locdetail3').html(trHTML);


            if (msg.t3.length < 1) {

                $('#barChart3').hide();
                $('#nodatachart3').show();
            } else {

                $('#nodatachart3').hide();
                $('#barChart3').show();
                for (i = 0; i < msg.t3.length; ++i) {
                    // do something with `substr[i]`
                    location.push(i + 1);
                    count.push(msg.t3[i].loc_count);
                    address.push(msg.t3[i].address);
                    // $('#add1 tr:last').after('<tr><td> $('address')</td></tr>');


                }


                var areaChartData = {
                    //labels: ["January", "February", "March", "April", "May", "June", "July"],
                    labels: location,
                    datasets: [
                        {
                            label: "Location Count",
                            fillColor: "rgba(255, 255, 255, 1)",
                            strokeColor: "rgba(0, 128, 0, 1)",
                            pointColor: "rgba(210, 214, 222, 1)",
                            pointStrokeColor: "#c1c7d1",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(220,220,220,1)",
                            //  data: [35, 19, 28, 17, 46, 55, 40]
                            data: count
                        }
                    ],
                };


                //-------------
                //- BAR CHART -
                //-------------
                var barChartCanvas = $("#barChart3").get(0).getContext("2d");
                var barChart = new Chart(barChartCanvas);
                var barChartData = areaChartData;
                //   barChartData.datasets[1].fillColor = "#00a65a";
                //  barChartData.datasets[1].strokeColor = "#00a65a";
                // barChartData.datasets[1].pointColor = "#00a65a";
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
                    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<address.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(address[i]){%><%=address[i]%><%}%></li><%}%></ul>",
                    //Boolean - whether to make the chart responsive
                    responsive: true,
                    maintainAspectRatio: true,
                    tooltips: {
                        callbacks: {
                            title: function (tooltipItems, data) {
                                return data.datasets[tooltipItems[0].datasetIndex].label;
                            },
                            label: function (tooltipItem, data) {
                                return '$' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 20000000,

                                userCallback: function (value, index, values) {

                                    value = value.toString();
                                    value = value.split(/(?=(?:...)*$)/);

                                    value = value.join(',');
                                    return '$' + value;
                                }
                            }
                        }]
                    }
                };

                barChartOptions.datasetFill = false;
                barChart.Bar(barChartData, barChartOptions);
            }

            if (msg == 2) {
                swal("System Error", "Contact Support Team.", "error");
            }

        }
    });


</script>