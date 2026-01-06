<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$stime='1';
$etime='';
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Person's Portal
        <small><?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id='.$_GET['id']); ?>">Person Dashboard </a></li>                        
        <li class="active">CDR SMS Chart</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
                    <div class="col-md-12" >
                            <div class="box box-danger" style="height:900px">
                                <div class="box-header with-border">
                                    <h3 class="box-title">SMS LOG CHART </h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <canvas id="pieChart" style="height:250px"></canvas>
                                </div>
                                <div class="col-md-12">
                                    <ol id="smsdetails" class="fancy"></ol>
                                </div>

                            </div>


                    </div>



</section>
<!-- /.content -->

<script src="<?php echo URL::base() . 'plugins/chartjs/Chart.min.js'; ?>"></script>

<script>
    $.ajax({
        url: "<?php echo URL::site('persons/ajax_sms_chart/?id='.$_GET['id'], True); ?>",
        //type: 'POST',
        //data: result,
        cache: false,
        //dataType: "text",
        dataType: 'json',
        success: function (msg) {
            var i;
            var t1 = msg.t1;
            var t2 = msg.t2;
            var t3 = msg.t3;
            var t4 = msg.t4;

            var trHTML = '';
            trHTML += '<li>' + "<b>Night (12am-6am)</b>" + ' (<strong> <h7 style="color:red;"> <a href="<?php echo URL::site('persons/sms_log_summary/?id='.$_GET['id'].'&st=12am&et=6am'); ?>" title="View Information" >' + t1 + '</a></strong> )' + '</li>';
            trHTML += '<li>' + "<b>Morning (6am-11am)</b>" + ' (<strong> <h7 style="color:red;"> <a href="<?php echo URL::site('persons/sms_log_summary/?id='.$_GET['id'].'&st=6am&et=11am'); ?>" title="View Information">' + t2 + '</a></strong> )' + '</li>';
            trHTML += '<li>' + "<b>Afternoon (11am-5pm)</b>" + ' (<strong> <h7 style="color:red;"> <a href="<?php echo URL::site('persons/sms_log_summary/?id='.$_GET['id'].'&st=11am&et=5pm'); ?>" title="View Information" >' + t3 + '</a></strong> )' + '</li>';
            trHTML += '<li>' + "<b>Evening (5pm-12am)</b>" + ' (<strong> <h7 style="color:red;"> <a href="<?php echo URL::site('persons/sms_log_summary/?id='.$_GET['id'].'&st=5pm&et=12am'); ?>" title="View Information">' + t4 + '</a></strong> )' + '</li>';
            $('#smsdetails').html(trHTML);

            $(function () {
                //-------------
                //- PIE CHART -
                //-------------
                // Get context with jQuery - using jQuery's .get() method.
                var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
                var pieChart = new Chart(pieChartCanvas);
                var PieData = [
                    {
                        value: t1,
                        color: "#f56954",
                        highlight: "#f56954",
                        label: "Night (12AM-6AM)"
                    },
                    {
                        value: t2,
                        color: "#00a65a",
                        highlight: "#00a65a",
                        label: "Morning (6AM-11AM)"
                    },
                    {
                        value: t3,
                        color: "#950875",
                        highlight: "#950875",
                        label: "Afternoon (11AM-5PM)"
                    },
                    {
                        value: t4,
                        color: "#900000",
                        highlight: "#900000",
                        label: "Evening (5PM-12AM)"
                    },
                ];
                var pieOptions = {
                    //Boolean - Whether we should show a stroke on each segment
                    segmentShowStroke: true,
                    //String - The colour of each segment stroke
                    segmentStrokeColor: "#fff",
                    //Number - The width of each segment stroke
                    segmentStrokeWidth: 2,
                    //Number - The percentage of the chart that we cut out of the middle
                    percentageInnerCutout: 50, // This is 0 for Pie charts
                    //Number - Amount of animation steps
                    animationSteps: 100,
                    //String - Animation easing effect
                    animationEasing: "easeOutBounce",
                    //Boolean - Whether we animate the rotation of the Doughnut
                    animateRotate: true,
                    //Boolean - Whether we animate scaling the Doughnut from the centre
                    animateScale: false,
                    //Boolean - whether to make the chart responsive to window resizing
                    responsive: true,
                    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                    maintainAspectRatio: true,
                    //String - A legend template
                    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
                };
                //Create pie or douhnut chart
                // You can switch between pie and douhnut using the method below.
                pieChart.Doughnut(PieData, pieOptions);


            });
            if (msg == 2) {
                swal("System Error", "Contact Support Team.", "error");
            }
        }

    });
</script>
