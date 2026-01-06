<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//echo '<pre>';
//print_r($search_post);
//exit();
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-search"></i>
        Most Active Sim IMEIs
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Active Sim IMEIs</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
                
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> IMEIs Against Active Sim Number</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="bpartysearch" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th class="no-sort">IMEI Number</th>
                                        <th>First Use Date</th>
                                        <th class="no-sort">Last Intraction</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>

                                <th>IMEI Number</th>
                                <th>First Use Date</th>
                                <th>Last Intraction</th>
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
        objDT = $('#bpartysearch').dataTable( 
                {"aaSorting": [[1, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                   // "bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('user/ajaximeisagainstsim/?id='.$_GET['ph_id'], TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search by IMEI Number:",
                        "sEmptyTable": 'Information not found'
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false
                        }]
                }
        );     
                
    });
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