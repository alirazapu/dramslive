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
        <i class="fa fa-map"></i>
        Person's Identity Breakup Report
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >Person's Identity Breakup Report</li>
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
                    <div class="box-body" id="identity_breakup_page">
                        <div class="col-md-6">
                            <!-- Info Boxes Style 2 -->
                            <div class="info-box bg-yellow">
                                <span class="info-box-icon"><i class="fa fa-book"></i></span>
                                <div class="info-box-content" id="identity_passport">
                                    <?php try{ echo Helpers_Layout::get_ajax_loader(); }  catch (Exception $ex){   }?>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                            </div>
                        <div class="col-md-6">
                            <!-- /.info-box -->
                            <div class="info-box bg-green">
                                <span class="info-box-icon"><i class="fa fa-rocket"></i></span>
                                <div class="info-box-content" id="identity_armedlicence">
                                    <?php try{ echo Helpers_Layout::get_ajax_loader(); }  catch (Exception $ex){   }?>
                                </div>
                                <!-- /.info-box-content -->
                            </div>                            
                        </div>                        
                        <div class="col-md-6">
                            <!-- /.info-box -->
                            <div class="info-box bg-red">
                                <span class="info-box-icon"><i class="fa fa-car"></i></span>

                                <div class="info-box-content" id="identity_drivinglicence">
                                    <?php  try{ echo Helpers_Layout::get_ajax_loader(); }  catch (Exception $ex){   }?>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- /.info-box -->
                            <div class="info-box bg-aqua">
                                <span class="info-box-icon"><i class="fa fa-mobile"></i></span>

                                <div class="info-box-content" id="identity_ntnnumber">
                                    <?php try{ echo Helpers_Layout::get_ajax_loader(); }  catch (Exception $ex){   }?>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- /.info-box -->
                            <div class="info-box bg-olive">
                                <span class="info-box-icon"><i class="fa fa-user"></i></span>
                                <div class="info-box-content" id="identity_foreigner">
                                    <?php try{ echo Helpers_Layout::get_ajax_loader(); }  catch (Exception $ex){   }?>
                                </div>
                                <!-- /.info-box-content -->
                            </div>
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
        //ajax call to view persons having passport Numbers
	$.ajax({
            url: "<?php echo URL::site("Adminreports/persons_with_passpoertnumbers"); ?>",
            //type: 'POST',
            //data: result,
            //data: {id: '<?php /* echo $_GET['id'];*/ ?>'},
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (msg) {
                  if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
               $("#identity_passport").html(msg);
            }
        });
        //ajax call to view persons count with Armed licence number
	$.ajax({
            url: "<?php echo URL::site("Adminreports/persons_with_armedlicence"); ?>",
            //type: 'POST',
            //data: result,
            //data: {id: '<?php /* echo $_GET['id'];*/ ?>'},
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (msg) {
                 if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
               $("#identity_armedlicence").html(msg);
            }
        });
        //ajax call to view persons count with Driving Licence
	$.ajax({
            url: "<?php echo URL::site("Adminreports/persons_with_drivinglicence"); ?>",
            //type: 'POST',
            //data: result,
            //data: {id: '<?php /* echo $_GET['id'];*/ ?>'},
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (msg) {
                if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
               $("#identity_drivinglicence").html(msg);
            }
        });
        //ajax call to view persons count with NTN Number
	$.ajax({
            url: "<?php echo URL::site("Adminreports/persons_with_ntnnumber"); ?>",
            //type: 'POST',
            //data: result,
            //data: {id: '<?php /* echo $_GET['id'];*/ ?>'},
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (msg) {
                if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
               $("#identity_ntnnumber").html(msg);
            }
        });
        //ajax call to view persons count with foreigner CNIC
	$.ajax({
            url: "<?php echo URL::site("Adminreports/persons_with_foreignercnic"); ?>",
            //type: 'POST',
            //data: result,
            //data: {id: '<?php /* echo $_GET['id'];*/ ?>'},
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (msg) {
               $("#identity_foreigner").html(msg);
               if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
            }
        });
    });
   
</script> 
