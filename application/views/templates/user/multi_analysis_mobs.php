<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$phone_no= !empty($search_post['phonenumber']) ? $search_post['phonenumber']:'';
//echo '<pre>';
//print_r($search_post['phonenumber']);
//exit();
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-search"></i>
        Multi Analysis Search
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Multi Analysis Search</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form class="searchform_input_height" id="search_form" name="search_form" method="post" role="form" action="<?php echo URL::site('user/multi_analysis_against_mob_numbers'); ?>" >
                    <div class="box box-default searchperson">
                        <div class="box-header with-border">
                            <h3 class="box-title">Moblie Multi Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">

                            <div class="form-group col-md-6">
                                <label for="phonenumber">Mobile Number(s) </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-mobile"></i>
                                    </div>
                                    <input type="text" class="form-control" id="phonenumber" value="<?php echo ((!empty($search_post['phonenumber'])) ? $search_post['phonenumber'] : ''); ?>" name="phonenumber" placeholder="e.g. 3001234567,3217654321">
                                </div>
                                <p><b>Note:</b> Enter Multiple (Max 100) Mobile Numbers Comma (,) Separated</p>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group pull-left" style="margin-top: 24px">
                                    <button type="submit" onclick="return validateAndSend()" class="btn btn-primary">Search</button>
                                    <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                </div>
                            </div>
                        </div>        
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-12">
            <div class="box box-success collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Common IMEIs Number</h3>
                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="imei_check" class="panelisopen btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="table-responsive">
                            <table id="imei" class="table table-bordered table-striped">
                                <thead>
                                <tr>

                                    <th class="no-sort" style="width: 15%">IMEI Number </th>
                                    <th class="no-sort" style="width: 13%">Used By Sims</th>
                                    <th class="no-sort" style="width: 10%">Sims Details</th>


                                </tr>
                                </thead>
                                <tbody id="imeis_check">

                                <?php
                                echo Helpers_Layout::get_ajax_loader();
                                ?>

                                </tbody>
                            </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-success collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Common Bparties Number</h3>
                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="bparty_check" class="panelisopen btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="table-responsive">
                            <table id="bparty" class="table table-bordered table-striped">
                                <thead>
                                <tr>

                                    <th class="no-sort" style="width: 15%">Bparty Number </th>
                                    <th class="no-sort" style="width: 13%">A-party Count</th>
                                    <th class="no-sort" style="width: 10%">A-party Details</th>


                                </tr>
                                </thead>
                                <tbody id="bparties_check">

                                <?php
                                echo Helpers_Layout::get_ajax_loader();
                                ?>

                                </tbody>
                            </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>


        <div class="col-md-12">
            <div class="box box-success collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title"> Common Latitude and Longitude</h3>
                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="lat_long_check" class="panelisopen btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="table-responsive">
                                <table id="lat_long" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>

                                        <th class="no-sort" style="width: 15%">Latitude </th>
                                        <th class="no-sort" style="width: 15%">Longitude </th>
                                        <th class="no-sort" style="width: 13%">Common Count</th>
                                        <th class="no-sort" style="width: 10%">Numbers Details</th>


                                    </tr>
                                    </thead>
                                    <tbody id="lats_longs_check">

                                    <?php
                                    echo Helpers_Layout::get_ajax_loader();
                                    ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>

        <div class="col-md-12">
            <div class="box box-success collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Multiple IMSI Numbers</h3>
                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="imsi_check" class="panelisopen btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="table-responsive">
                                <table id="imsi" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>

                                        <th class="no-sort" style="width: 15%">Phone Number </th>
                                        <th class="no-sort" style="width: 15%">IMSI Count </th>
                                        <th class="no-sort" style="width: 10%">IMSI Details</th>


                                    </tr>
                                    </thead>
                                    <tbody id="imsies_check">

                                    <?php
                                    echo Helpers_Layout::get_ajax_loader();
                                    ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-success collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Inter Communication Between Searched Numbers(A-Parties)</h3>
                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="int_com_aparty" class="panelisopen btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="table-responsive">
                                <table id="com_aparty" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>

                                        <th class="no-sort" style="width: 15%">A-Party </th>
                                        <th class="no-sort" style="width: 15%">B-Parties </th>
<!--                                        <th class="no-sort" style="width: 10%">IMSI Details</th>-->


                                    </tr>
                                    </thead>
                                    <tbody id="int_coms_aparty">

                                    <?php
                                    echo Helpers_Layout::get_ajax_loader();
                                    ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-success collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Inter Communication Between Searched Numbers(B-Parties)</h3>
                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="int_com_bparty" class="panelisopen btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="table-responsive">
                                <table id="com_bparty" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>

                                        <th class="no-sort" style="width: 15%">B-Party </th>
                                        <th class="no-sort" style="width: 15%">A-Parties </th>
<!--                                        <th class="no-sort" style="width: 10%">IMSI Details</th>-->


                                    </tr>
                                    </thead>
                                    <tbody id="int_coms_bparty">

                                    <?php
                                    echo Helpers_Layout::get_ajax_loader();
                                    ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-md-12">
            <div class="box box-success collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Common Aparties Number</h3>
                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide" id="aparty_check" class="panelisopen btn btn-box-tool" data-widget="collapse">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-md-12" >
                            <div class="table-responsive">
                                <table id="aparty" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>

                                        <th class="no-sort" style="width: 15%">Aparty Number </th>
                                        <th class="no-sort" style="width: 13%">B-party Count</th>
                                        <th class="no-sort" style="width: 10%">B-party Details</th>
                                        <th class="no-sort"  style="width: 8%">Detail</th>


                                    </tr>
                                    </thead>
                                    <tbody id="aparties_check">

                                    <?php
                                    echo Helpers_Layout::get_ajax_loader();
                                    ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
        </div>



    </div>

</section>
<div class="modal modal-info fade" id="external_search_model">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Search Results</h4>
            </div>

            <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>

                <!--searching data form external sources-->
                <div id="externa_search_results_div" style="display: block;">
                    <div class="col-md-12" style="background-color: #fff;color: black">

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label   for="external_search_key" class="control-label">Search Key:
                                </label>
                                <input name="external_search_key" type="text" class="form-control" id="external_search_key" placeholder="Search Key" readonly >
                            </div>

                            <hr class="style14 " style="margin-top: -10px; ">
                            <div class="col-sm-12" id="external_search_results" style="display: block">

                                <div style='background-image: url("<?php echo URL::base(); ?>dist/img/loader.gif"); width:100%; height:11px; background-position:center; display:block'><label    class="control-label" style="alignment-adjust: central">Exploring External Sources
                                    </label></div>
                                <hr class="style14 ">
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" style="margin-top: 10px" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->

</div>
<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
    
    //
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
        if ($("#msg_to_show").val() !== "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
       //advance search
       var msisdnnumber=$('#phonenumber').val(); 
       var cnicnumber=$("#cnic").val(); 
       var imeinumber=$("#imei").val();
       var requestdata = '' ;
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


       // $('.dataTables_empty').html("Information not found, <a href='<?php // echo URL::site('userrequest/request/1', TRUE); ?>'> Please Request form Here </a>");


// validation for data search
$("#search_form").validate({
                  rules:{
                         phonenumber:{
                           // number: true,
                            alphanumericspecial: true,
                            maxlength: 1000,
                            minlength: 21
                             }                       
                        },
                    messages: {
                         phonenumber:{
                                //Number:"Enter only number",
                                maxlenght:"Maximum 1099 digits",
                                alphanumericspecial:"Only Alpha Numeric Characters",                           
                                minlength:"Minimum Two Mobile Numbers"
                             }                           
                        }, 
                        
                        submitHandler: function () {
                            $("#search_form").submit();
               
                         }                   
                  // $('#upload').show()
                });               

                     
    
    jQuery.validator.addMethod("numberthree", function(value, element) {
        return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");        
                
    });

    jQuery.validator.addMethod("alphanumericspecial", function(value, element) {
        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9-,]+$/);
        }, "Only letters and numbers Allowed."); 

    $.validator.addMethod("check_list",function(sel,element){
            if(sel == "" || sel == 0){
                return false;
             }else{
                return true;
             }
            },"<span>Select One</span>");
            
        function validateAndSend() {
            if (search_form.phonenumber.value == '') {
                alert('Enter Minimum Two Mobile Numbers');
                return false;
            }
        }

    $('#imei_check').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person current location history
                $.ajax({
                    url: "<?php echo URL::site("User/imei_common"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {ph_nos: '<?php echo $phone_no; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {
               if (msg == 2)
			{
                $("#imeis_check").html('<td colspan="4"> Information not found </td>');
                $(".ajax-loader").hide();
<!--			    --><?php //echo '<td colspan="4"> Information not found </td>';?>
			// swal("System Error", "Contact Support Team.", "error");
			}else {
                   $("#imei_check").addClass("already-done");
                   $("#imeis_check").html(msg);
                   $(".ajax-loader").hide();
               }
                    }, beforeSend: function () {
                        $('#imei').parent().children('.ajax-loader').show();
                    }, complete: function () {
                        $('#imei').parent().children('.ajax-loader').hide();
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
    $('#bparty_check').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person current location history
                $.ajax({
                    url: "<?php echo URL::site("User/bparty_common"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {ph_nos: '<?php echo $phone_no; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {
               if (msg == 2)
			{
                $("#bparties_check").html('<td colspan="4"> Information not found </td>');
                $(".ajax-loader").hide();
			// swal("System Error", "Contact Support Team.", "error");
			}else {
                        $("#bparty_check").addClass("already-done");
                        $("#bparties_check").html(msg);
                        $(".ajax-loader").hide();
               }
                    }, beforeSend: function () {
                        $('#bparty').parent().children('.ajax-loader').show();
                    }, complete: function () {
                        $('#bparty').parent().children('.ajax-loader').hide();
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
    $('#aparty_check').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person current location history
                $.ajax({
                    url: "<?php echo URL::site("User/aparty_common"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {ph_nos: '<?php echo $phone_no; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {
                         // $(".ajax-loader").show();
               if (msg == 2)
			{
                $("#aparties_check").html('<td colspan="4"> Information not found </td>');
                //$(".ajax-loader").hide();
			// swal("System Error", "Contact Support Team.", "error");
			}else {
                        $("#aparty_check").addClass("already-done");
                        $("#aparties_check").html(msg);
                      //  $(".ajax-loader").hide();
               }
                    }, beforeSend: function () {
                        $('#aparty').parent().children('.ajax-loader').show();
                    }, complete: function () {
                        $('#aparty').parent().children('.ajax-loader').hide();
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
    $('#lat_long_check').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person current location history
                $.ajax({
                    url: "<?php echo URL::site("User/lat_long_common"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {ph_nos: '<?php echo $phone_no; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {
                        // $(".ajax-loader").show();

                        if (msg == 2) {
                            $("#lats_longs_check").html('<td colspan="4"> Information not found </td>');
                            			// swal("System Error", "Contact Support Team.", "error");
                        } else {
                            $("#lat_long_check").addClass("already-done");
                            $("#lats_longs_check").html(msg);
                            $(".ajax-loader").hide();
                        }
                    }, beforeSend: function () {
                        $('#lat_long').parent().children('.ajax-loader').show();
                    }, complete: function () {
                        $('#lat_long').parent().children('.ajax-loader').hide();
                    }


                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });

    $('#imsi_check').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person current location history
                $.ajax({
                    url: "<?php echo URL::site("User/imsi_common"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {ph_nos: '<?php echo $phone_no; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {

                        if (msg == 2) {
                            $("#imsies_check").html('<td colspan="4"> Information not found </td>');
                            $(".ajax-loader").hide();
                            // swal("System Error", "Contact Support Team.", "error");
                        } else {
                            $("#imsi_check").addClass("already-done");
                            $("#imsies_check").html(msg);
                            $(".ajax-loader").hide();
                        }
                    }, beforeSend: function () {
                        $('#imsi').parent().children('.ajax-loader').show();
                    }, complete: function () {
                        $('#imsi').parent().children('.ajax-loader').hide();
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
    $('#int_com_aparty').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person current location history
                $.ajax({
                    url: "<?php echo URL::site("User/inter_communication_aparty"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {ph_nos: '<?php echo $phone_no; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {
                        if (msg == 2) {
                            $("#int_coms_aparty").html('<td colspan="4"> Information not found </td>');
                            $(".ajax-loader").hide();
                            // swal("System Error", "Contact Support Team.", "error");
                        } else {
                            $("#int_com_aparty").addClass("already-done");
                            $("#int_coms_aparty").html(msg);
                            $(".ajax-loader").hide();
                        }
                    }, beforeSend: function () {
                        $('#com_aparty').parent().children('.ajax-loader').show();
                    }, complete: function () {
                        $('#com_aparty').parent().children('.ajax-loader').hide();
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
    $('#int_com_bparty').click(function () {
        if ($(this).hasClass("panelisopen")) {
            $(this).removeClass("panelisopen");
            if (!$(this).hasClass("already-done")) {
                //ajax call to update person current location history
                $.ajax({
                    url: "<?php echo URL::site("User/inter_communication_bparty"); ?>",
                    //type: 'POST',
                    //data: result,
                    data: {ph_nos: '<?php echo $phone_no; ?>'},
                    cache: false,
                    //dataType: "text",
                    dataType: 'html',
                    success: function (msg) {
                        if (msg == 2) {
                            $("#int_coms_bparty").html('<td colspan="4"> Information not found </td>');
                            $(".ajax-loader").hide();
                            // swal("System Error", "Contact Support Team.", "error");
                        } else {
                            $("#int_com_bparty").addClass("already-done");
                            $("#int_coms_bparty").html(msg);
                            $(".ajax-loader").hide();
                        }
                    }, beforeSend: function () {
                        $('#com_bparty').parent().children('.ajax-loader').show();
                    }, complete: function () {
                        $('#com_bparty').parent().children('.ajax-loader').hide();
                    }
                });
            }
        } else {
            $(this).addClass("panelisopen");
        }
    });
        
   function clearSearch() {
        window.location.href = '<?php echo URL::site('user/multi_analysis_against_mob_numbers', TRUE); ?>';
    }

    // request subscriber
    function external_search_model(mobile) {

        $("#external_search_model").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second
            $(".modal-backdrop.fade.in").remove();
        }, 300);

        if(mobile !=0 || mobile != ''){
            $("#external_search_key").val(mobile);
            search_local_subscriber_detail('msisdn',mobile);
        }

    }
    // request subscriber
    function external_search_model(mobile,cnicnumber) {
        $("#external_search_model").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second
            $(".modal-backdrop.fade.in").remove();
        }, 300);
        var cnic=cnicnumber;
        var msisdn= mobile;
        var is_foreigner = 0;
        if(msisdn !=0 || msisdn != ''){
            $("#external_search_key").val(msisdn);
            search_local_subscriber_detail('msisdn',msisdn);
        }else if(cnic !=0 || cnic != ''){
            $("#external_search_key").val(cnic);
            if(is_foreigner==1){
                search_foreinger_detail('foreigner_profile',cnic);
            }else{
                search_local_subscriber_detail('cnic',cnic);
            }
        }else if(imsi !=0 || imsi != ''){
            $("#external_search_key").val(imsi);
            search_local_subscriber_detail('imsi',imsi);
        }

    }

</script>