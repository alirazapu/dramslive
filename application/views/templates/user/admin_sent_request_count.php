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
        <i class="fa fa-files-o"></i>
        Request Count
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Request Count</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('Adminrequest/admin_sent_request_count'); ?>" >
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>
                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post) && (empty($search_post['message']) || $search_post['message'] != 1)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="col-sm-4">
                                                <div class="form-group" >
                                                    <label for="field">Please select request type</label>
                                                    <select class="form-control " name="request_type"  id='field'  onchange="request_against(this)">
                                                        <option value=""> Please Select Type</option>
                                                        <option value="3" <?php echo (isset($search_post['request_type']) && $search_post['request_type'] == 3) ? 'selected' : '' ?>> Subscriber Against Mobile Number</option>
                                                        <option value="4" <?php echo (isset($search_post['request_type']) && $search_post['request_type'] == 4) ? 'selected' : '' ?>> Location Against Mobile Number</option>
                                                        <option value="1" <?php echo (isset($search_post['request_type']) && $search_post['request_type'] == 1) ? 'selected' : '' ?>> CDR Against Mobile Number</option>
                                                        <option value="2" <?php echo (isset($search_post['request_type']) && $search_post['request_type'] == 2) ? 'selected' : '' ?>> CDR Against IMEI Number </option>
                                                        <option value="5" <?php echo (isset($search_post['request_type']) && $search_post['request_type'] == 5) ? 'selected' : '' ?>> SIM's Against CNIC Number</option>
                                                    </select>
                                                </div>
                                            </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="searchfield">Start Date (mm/dd/yyyy)</label>
                                            <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="startdate" id="startdate" value="<?php echo (!empty($search_post['startdate']) ? $search_post['startdate'] : ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label title="If not selected, Today is default Date" for="searchfield">End Date (mm/dd/yyyy)</label>
                                            <input title="If not selected, Today is default Date" type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="enddate" id="enddate" value="<?php echo (!empty($search_post['enddate']) ? $search_post['enddate'] : ''); ?>">
                                        </div>
                                    </div>



                                        </div>
                                     </div>

                            <!-- /.col -->
                            <div class="col-md-12">
                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                </div>
                            </div>
                    </div>
                    </div>
                </form>

            </div>
        </div>


        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> Request Status</h3>
                    </div>
                    <?php
                    if (!empty($message)) {
                        ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> <?php echo $message; ?></h4>
                        </div>
                    <?php } ?>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="requeststatus" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Count</th>
                                    <th class="no-sort">Name </th>
                                    <th class="no-sort">Action </th>

                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Count</th>
                                    <th >Name </th>
                                    <th >Action </th>

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
        objDT = $('#requeststatus').dataTable(
            {"aaSorting": [[1, "desc"]],
                "bPaginate": true,
                "bProcessing": true,
                //"bStateSave": true,
                "bServerSide": true,
                "sAjaxSource": "<?php echo URL::site('adminrequest/ajaxadminsentrequestcount', TRUE); ?>",
                "sPaginationType": "full_numbers",
                "bFilter": true,
                "bLengthChange": true,
                "oLanguage": {
                    "sProcessing": "Loading...",
                    "sSearch": "Search by Name:"
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
                required: true,
            },
            "requesttype[]": {
                required: true,
            },
        },
        messages: {
            field: {
                required: "Please select search type",
            },
            "posting[]": {
                required: "Select any option from list",
            },
        }
    });

    function clearSearch() {
        window.location.href = '<?php echo URL::site('adminrequest/admin_sent_request_count', TRUE); ?>';
    }


    $("#requesttype").on("change", function(){
        if ($(this).find(":selected").text() == "All Request"){
            $('#requesttype option').prop('selected', true);
            $('#requesttype option[value=99]').prop("selected", false);
            $('#requesttype option[value=""]').prop("selected", false);
        }
    });






    //Date picker
    $('#startdate').datepicker({
        autoclose: true
    });
    //Date picker
    $('#enddate').datepicker({
        autoclose: true
    });
    
    $("#rqtbyname").on("keyup", function(){
        var rqtbyname = $(this).val();
        if (rqtbyname !=="") {
          $.ajax({
            url:"<?php echo URL::site("adminrequest/autocomplete"); ?>",
            type:"POST",
            cache:false,
            data:{rqtbyname:rqtbyname},
            success:function(data){
              $("#rqtbynamelist").html(data);
              $("#rqtbynamelist").fadeIn();
            }  
          });
        }else{
          $("#rqtbynamelist").html("");  
          $("#rqtbynamelist").fadeOut();
        }
    });
$(document).on("click","li", function(){
        $('#rqtbyname').val($(this).text());
        $('#rqtbynamelist').fadeOut("fast");
      });
</script>
<style>
#rqtbynamelist ul.list-unstyled {
    background-color: #def;
    padding: 10px;
    cursor: pointer;
}
</style>