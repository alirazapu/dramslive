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
        <li><a href="<?php echo URL::site('persons/dashboard/?id='.$_GET['id']); ?>">Person Dashboard </a></li> 
        <li><a href="<?php echo URL::site('persons/call_summary/?id='.$_GET['id']); ?>">Call Summary </a></li>
        <li class="active">Call Summary Detail</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
        </div>

        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-mobile-phone"></i> Call Summary Detail of <?php echo Helpers_Person::get_person_name($person_id); ?></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="callsummarydetail" class="table table-bordered table-striped">
                            <thead>
                                <tr>                                    
                                    <th style="width: 10%">Party A</th>
                                    <th style="width: 12%">Party B</th>
                                    <th style="width: 10%">Call Type</th>
                                    <th style="width: 12%">Call Duration</th>
                                    <th  style="width: 15%">Date & Time</th>                                    
                                    <th>LOCATION</th> 
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>                                    
                                    <th>Party A</th>
                                    <th>Party B</th>
                                    <th>Call Type</th>
                                    <th>Call Duration</th>
                                    <th>Date & Time</th>                                    
                                    <th>LOCATION</th> 
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
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
        elem = $('#type').val();
        if (elem == 'def')
        {
            //Hide
            document.getElementById('blocktohide').style.display = "none";
            //show
            document.getElementById('searchkey').style.display = "block";
            //disable
            $('#searchfield').attr("disabled","disabled");
        } else if (elem == 'location')
        {
            //Hide
            document.getElementById('blocktohide').style.display = "none";
            //show
            document.getElementById('searchkey').style.display = "block";
            //enable
            $('#searchfield').removeAttr("disabled");
        } else if (elem == 'date')
        {
            //show
            document.getElementById('blocktohide').style.display = "block";
            //Hide
            document.getElementById('searchkey').style.display = "none";
        }
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
        objDT = $('#callsummarydetail').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('persons/ajaxcallsummarydetail/?id='.$_GET['id'], TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Date or Location:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );

        $('.dataTables_empty').html("Information not found");

    });

    function clearSearch() {
        window.location.href = '<?php echo URL::site('persons/call_summary_detail/?id='.$_GET['id'], TRUE); ?>';
    }

</script>
<script>
    function showDiv(elem) {
        if (elem.value == 'def')
        {
            //Hide
            document.getElementById('blocktohide').style.display = "none";
            //show
            document.getElementById('searchkey').style.display = "block";
            //disable
            $('#searchfield').attr("disabled","disabled");
        } else if (elem.value == 'location')
        {
            //Hide
            document.getElementById('blocktohide').style.display = "none";
            //show
            document.getElementById('searchkey').style.display = "block";
            //enable
            $('#searchfield').removeAttr("disabled");
        } else if (elem.value == 'date')
        {
            //show
            document.getElementById('blocktohide').style.display = "block";
            //Hide
            document.getElementById('searchkey').style.display = "none";
        }
    }
</script>