<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header col-md-12">
    <h1 class="col-md-4">
        <i class="fa fa-dashboard"></i>
        Dashboard
        <small>DRAMS</small>
    </h1>
    <?php
    try {
        $notification = Helpers_Layout::get_notification();
        echo $notification;
    } catch (Exception $ex) {
        
    }
    ?>
    <ol class="breadcrumb col-md-2" style="float: right;">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <?php
    try {
        $user_obj = Auth::instance()->get_user();
        $permission = Helpers_Utilities::get_user_permission($user_obj->id);
        $cat_white = Helpers_Utilities::encrypted_key(0, 'encrypt');
        $cat_grey = Helpers_Utilities::encrypted_key(1, 'encrypt');
        $cat_black = Helpers_Utilities::encrypted_key(2, 'encrypt');
        if ($permission == 1 || $permission == 5 || $permission == 2 || $permission == 3) {

            $black = Helpers_Utilities::get_users_black_person();
            $grey = Helpers_Utilities::get_users_grey_person();
            $white = Helpers_Utilities::get_users_white_person();
            $total = $black + $grey + $white;
        } else if ($permission == 4) {
            $black = Helpers_Utilities::get_users_black_person($user_id);
            $grey = Helpers_Utilities::get_users_grey_person($user_id);
            $white = Helpers_Utilities::get_users_white_person($user_id);
            $total = $black + $grey + $white;
        }
    } catch (Exception $ex) {
        
    }
    ?>
    <!-- Small boxes (Stat box) -->    
    <div class="row">
<?php
if (!empty($_GET['accessmessage'])) {
    ?>
            <div class="alert alert-success alert-dismissible" style="height: 60px; margin-top: 40px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> <?php echo $_GET['accessmessage']; ?></h4>
            </div>
<?php } ?>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->            
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?php echo $black ?></h3>
                    <p>Total Black Person</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add" style="color: gray;"></i>
                </div>

                <a href="<?php echo URL::site('personsreports/person_list/?category=' . $cat_black); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>            
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?php echo $grey ?></h3>
                    <p>Total Gray Person</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>

                <a href="<?php echo URL::site('personsreports/person_list/?category=' . $cat_grey); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green" style="border: 1px #d0c8c8 solid">
                <div class="inner">
                    <h3><?php echo $white ?></h3>
                    <p>Total White Person</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>

                <a href="<?php echo URL::site('personsreports/person_list/?category=' . $cat_white); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua-active">
                <div class="inner">
                    <h3><?php echo $total ?></h3>
                    <p>Total Persons</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="<?php echo URL::site('personsreports/person_list'); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

    </div>
    <!-- /.row -->
    <?php
    try {
        if ($permission == 1 || $permission == 5 || $permission == 3 || $permission == 2) {
            $personid = isset(Helpers_Person::get_most_searched_person()->PID) ? Helpers_Person::get_most_searched_person()->PID : '0';
            $label = 'Most Searched Person';
        } else if ($permission == 4) {
            $personid = Helpers_Profile::get_user_latest_favourite_person($user_id);
            $label = 'Recent Favourite Person';
        }
    } catch (Exception $ex) {
        
    }
    ?>
    <div class="row invoice-info">
        
        <!-- /.col -->
        <div class="col-md-12" >
            <!-- Widget: user widget style 1 -->
            <!-- BAR CHART -->
            <div class="box box-info" style="max-height:315px">
                <div class="box-header with-border">
                    <h3 class="box-title">Date-wise Comparative Analysis of White, Gray, and Black Classifications</h3>
                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>                        
                    </div>
                </div>
                <div class="box-body">
                    <div class="chart">
                        <canvas id="barChart" style="height:245px"></canvas>
                        <img id="nodatachart" style="display:none; width: 100%; margin: auto" class="img-responsive" src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
            <!-- /.widget-user -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
    <!-- /.row -->
    <?php
    try {
        if ($permission == 1 || $permission == 5) {



            $label = 'Most Active User';
            $userid = Helpers_Profile::get_user_highest_black();
            if (!empty($userid) && $user_id != 0) {
                $userblack = Helpers_Utilities::get_users_black_person($userid);
                $usergrey = Helpers_Utilities::get_users_grey_person($userid);
                $userwhite = Helpers_Utilities::get_users_white_person($userid);
            } else {
                $userblack = $usergrey = $userwhite = 0;
            }
            $userimg = Helpers_Profile::get_user_image_by_id($userid); // 20170718021625;
            if ($userimg == 0 || $userimg == NULL) {
                $path = '../dist/img/avtar6.jpg';
            } else {
                $path = '../dist/uploads/user/profile_images/';
                $path .= $userimg;
            }
            $userposting = isset(Helpers_Profile::get_user_perofile($userid)->posted) ? Helpers_Profile::get_user_perofile($userid)->posted : "";
            $user_belt_number = isset(Helpers_Profile::get_user_perofile($userid)->belt) ? Helpers_Profile::get_user_perofile($userid)->belt : "";
            $user_district = isset(Helpers_Profile::get_user_perofile($userid)->district_id) ? Helpers_Profile::get_user_perofile($userid)->district_id : 0;
            $user_district_name = isset($user_district) ? Helpers_Utilities::get_user_district($user_district) : "";
            //print_r($user_district); exit;
            $user_mobile = isset(Helpers_Profile::get_user_perofile($userid)->mobile_number) ? Helpers_Profile::get_user_perofile($userid)->mobile_number : "";
        } else {
            $label = 'Recent Favourite User';
            $userid = Helpers_Profile::get_user_latest_favourite_user($user_id);
            if ($userid > 0) {
                $userblack = Helpers_Utilities::get_users_black_person($userid);

                $usergrey = Helpers_Utilities::get_users_grey_person($userid);
                $userwhite = Helpers_Utilities::get_users_white_person($userid);

                $userimg = Helpers_Profile::get_user_image_by_id($userid); // 20170718021625;
                if ($userimg == 0 || $userimg == NULL) {
                    $path = '../dist/img/avtar6.jpg';
                } else {
                    $path = '../dist/uploads/user/profile_images/';
                    $path .= $userimg;
                }
                $userposting = isset(Helpers_Profile::get_user_perofile($userid)->posted) ? Helpers_Profile::get_user_perofile($userid)->posted : "";
                $user_belt_number = isset(Helpers_Profile::get_user_perofile($userid)->belt) ? Helpers_Profile::get_user_perofile($userid)->belt : "";
                $user_district = isset(Helpers_Profile::get_user_perofile($userid)->district_id) ? Helpers_Profile::get_user_perofile($userid)->district_id : 0;
                $user_district_name = isset($user_district) ? Helpers_Utilities::get_user_district($user_district) : "";
                $user_mobile = isset(Helpers_Profile::get_user_perofile($userid)->mobile_number) ? Helpers_Profile::get_user_perofile($userid)->mobile_number : "";
            } else {
                $userblack = 0;
                $usergrey = 0;
                $userwhite = 0;

                $userimg = Helpers_Profile::get_user_image_by_id($userid); // 20170718021625;
                if ($userimg == 0 || $userimg == NULL) {
                    $path = '../dist/img/avtar6.jpg';
                } else {
                    $path = '../dist/uploads/user/profile_images/';
                    $path .= $userimg;
                }
                $userposting = '';
                $user_belt_number = '';
                $user_district = '';
                $user_district_name = '';
                $user_mobile = '';
            }
        }
    } catch (Exception $ex) {
        
    }
    ?>
    

</section>
<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/chartjs/Chart.min.js'; ?>"></script>
<script>
    $(function () {
        $(document).ready(function () {

    <?php if ($cnic_exist == 0) { ?>
            cnicinput();
    <?php } ?>
    <?php if ($password_change_required == 0) { ?>
        //password change required
            password_change_required();
    <?php } ?>

            //alert(document.getElementById('valueholder').value);
//            if (document.getElementById('valueholder').value == 0)
//            {
//                $('#nodata').show();
//                $('#hide').hide();
//            }
            $.ajax({
                url: "<?php echo URL::site("Userdashboard/categorycomparison"); ?>",
                //type: 'POST',
                //data: result,
                cache: false,
                //dataType: "text",
                dataType: 'json',
                success: function (msg) {
                    var i;
                    var month = [];
                    var black = [];
                    var white = [];
                    var gray = [];

                    if (msg.length < 1)
                    {

                        $('#barChart').hide();
                        $('#nodatachart').show();
                    } else {
                        $('#nodatachart').hide();
                        $('#barChart').show();
                        for (i = 0; i < msg.length; ++i) {
                            // do something with `substr[i]`
                            month.push(msg[i].month);
                            black.push(msg[i].Black);
                            white.push(msg[i].White);
                            gray.push(msg[i].Gray);

                        }


                        var areaChartData = {
                            //labels: ["January", "February", "March", "April", "May", "June", "July"],
                            labels: month,
                            datasets: [
                                {
                                    label: "White Persons",
                                    fillColor: "rgba(255, 255, 255, 1)",
                                    strokeColor: "rgba(0, 128, 0, 1)",
                                    pointColor: "rgba(210, 214, 222, 1)",
                                    pointStrokeColor: "#c1c7d1",
                                    pointHighlightFill: "#fff",
                                    pointHighlightStroke: "rgba(220,220,220,1)",
                                    //  data: [35, 19, 28, 17, 46, 55, 40]
                                    data: white
                                },
                                {
                                    label: "Gray Persons",
                                    fillColor: "rgba(128,128,128,1)",
                                    strokeColor: "rgba(255,255,0,1)",
                                    pointColor: "#3b8bba",
                                    pointStrokeColor: "rgba(128,128,128,1)",
                                    pointHighlightFill: "#dfd",
                                    pointHighlightStroke: "rgba(128,128,128,1)",
                                    // data: [28, 4, 1, 8, 20, 10, 23]
                                    data: gray
                                },
                                {
                                    label: "Black Persons",
                                    fillColor: "rgba(0,0,0,0.9)",
                                    strokeColor: "rgba(255,0,0,0.8)",
                                    pointColor: "#3b8bba",
                                    pointStrokeColor: "rgba(60,141,188,1)",
                                    pointHighlightFill: "#fff",
                                    pointHighlightStroke: "rgba(256,141,256,256)",
                                    // data: [28, 4, 1, 8, 20, 10, 23]
                                    data: black
                                }
                            ]
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
                            legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                            //Boolean - whether to make the chart responsive
                            responsive: true,
                            maintainAspectRatio: true
                        };

                        barChartOptions.datasetFill = false;
                        barChart.Bar(barChartData, barChartOptions);
                    }
                    if (msg == 2) {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                }
            });
            var marquee = $(".marquee").html();
            $(".marquee").html('<p><b>CTD KPK,</b> We are delighted to welcome you here at AIES</p> <p> AIES is a unique and intelligent tool that performs in-depth analysis </p><p>Link Analysis is one of the most leading feature of the software</p><p>Efficiency CDR analysis from different perspective</p>');
            /*  $.ajax({
             url: "<?php //echo URL::site("Userdashboard/respone_check");  ?>",
             //type: 'POST',
             //data: result,
             cache: false,
             //dataType: "text",
             dataType: 'json',
             success: function (msg) {
             $(".marquee").html(marquee);
             }
             });  */

        });
    });

    $.ajax({
        url: "<?php echo URL::site("Userdashboard/userscomparison"); ?>",
        //type: 'POST',
        //data: result,
        cache: false,
        //dataType: "text",
        dataType: 'json',
        success: function (msg) {
            var i;
            var total_user = msg.total_user;
            var favourite_user = msg.favourite_user;

            $(function () {
                //-------------
                //- PIE CHART -
                //-------------
                // Guard: the <canvas id="pieChart"> was removed from the
                // dashboard markup at some point but this JS was kept. Without
                // the element, $("#pieChart").get(0) is undefined and the
                // .getContext("2d") call throws a TypeError that breaks the
                // rest of the dashboard's onload. Bail silently if it's gone.
                var pieChartEl = $("#pieChart").get(0);
                if (!pieChartEl) {
                    return;
                }
                // Get context with jQuery - using jQuery's .get() method.
                var pieChartCanvas = pieChartEl.getContext("2d");
                var pieChart = new Chart(pieChartCanvas);
                var PieData = [
                    {
                        value: total_user,
                        color: "#f56954",
                        highlight: "#f56954",
                        label: "Total Users"
                    },
                    {
                        value: favourite_user,
                        color: "#00a65a",
                        highlight: "#00a65a",
                        label: "Favourites Users"
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

    function cnicinput() {
        swal({
            title: "CNIC Number Required!",
            text: "Dear User Please Provide Your CNIC Number",
            type: "input",
            showCancelButton: false,
            closeOnConfirm: false,
            allowEscapeKey: false,
            inputPlaceholder: "Please Enter 13 digit CNIC Number"
        }, function (inputValue) {
            if (inputValue === false)
                return false;
            if (inputValue === "") {
                swal.showInputError("Please Enter 13 digit CNIC Number!");
                return false
            }
            if (inputValue.length < 13) {
                swal.showInputError("Minimum 13 digit CNIC Number!");
                return false
            }
            if (inputValue.length > 13) {
                swal.showInputError("Maximum 13 digit CNIC Number!");
                return false
            }
            var isnum = /^\d+$/.test(inputValue);
            if (!isnum) {
                swal.showInputError("Only Number allowed as CNIC");
                return false
            }
            var data = {cnic_number: inputValue};
            $.ajax({
                url: "<?php echo URL::site("Userdashboard/cnic_number_save"); ?>",
                type: 'POST',
                data: data,
                cache: false,
                //dataType: "text",
                dataType: 'json',
                success: function (msg) {
                    if (msg == 1) {
                        swal("Congratulations!!", "CNIC Number Saved Successfully!", "success");
                    } else {
                        swal("System Error", "Contact Support Team.", "error");
                    }
                }
            });
        });
    }
    function password_change_required() {
        swal({
            title: "Password Change Required!",
            text: "Dear User Please Change Your Password",
            type: "warning",
            showCancelButton: false,
            closeOnConfirm: false,
            allowEscapeKey: false,
        }, function () {            
            window.location.href = '<?php echo URL::site('user/changepassword', TRUE); ?>';
        });
    }

</script>