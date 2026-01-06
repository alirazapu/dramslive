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
        <li class="active">CDR Call Log</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">    
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <form id="search_form" name="search_form" class="ipf-form cell_log_summary" method="POST" action="<?php echo URL::site('persons/cell_log_summary/?id=' . $_GET['id']); ?>" >
                    <input type="hidden" class="form-control" name="xport" id="xport" value="">   
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>
                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <select class="form-control " name="phone_number" id="phone_number" onchange="person_bparty()">                                                        
                                        <option value="">Please Select Person Number</option>
                                        <?php try{
                                        $sims_list = Helpers_Person::get_person_inuse_SIMs($person_id);
                                        foreach ($sims_list as $sim) {
                                            ?>
                                            <option <?php echo (!empty($search_post['phone_number']) && ($search_post['phone_number'] == $sim->phone_number)) ? 'selected' : '' ?> value=<?php echo $sim->phone_number ?> > <?php echo $sim->phone_number ?> </option>
                                        <?php } 
                                        }  catch (Exception $ex){
                                            
                                        }
                                        ?>
                                    </select>
                                </div>          
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="searchfield">Other Person Mobile Number(Optional)</label>
                                    <select class="form-control select2" multiple="multiple" data-placeholder="Select Other Person Number" name="otherphone[]" id="otherphone" style="width: 100%;">                                                        
                                        
                                    </select>                                            
                                </div>        
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Select Type</label>
                                    <select class="form-control " name="type" id="type" onchange="">  
                                        <option value="def" selected>Please Select any</option>
                                        <option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'all')) ? 'selected' : ''); ?> value="all">Any</option>                                                                                                
                                        <option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'location')) ? 'selected' : ''); ?> value="location">LOCATION</option>                                                                                                
                                        <!--<option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'date')) ? 'selected' : ''); ?> value="date">Date</option>-->                                                                                                
                                    </select>
                                </div>          
                            </div>
                            <!-- /.col -->
                            <div class="col-md-4">
                                <div class="form-group" id="searchkey">
                                    <label for="searchfield">Search Key</label>
                                    <input type="text" class="form-control" id="searchfield" value="<?php echo (!empty($search_post['key']) ? $search_post['key'] : ''); ?>" name="key" placeholder="Enter Text">
                                </div>
                            </div>
                            <!-- /.col -->
                            <!-- /.col -->
                            <div id="blocktohide">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="searchfield">Start Date (mm/dd/yyyy)</label>
                                        <input type="text" readonly="readonly" placeholder="dd/mm/yyyy HH:mm:ss" class="form-control" name="startdate" id="startdate" value="<?php echo (!empty($search_post['startdate']) ? $search_post['startdate'] : ''); ?>">                                        
                                                                             
                                    </div>
                                </div>
                                <!-- /.col -->
                                <!-- /.col -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="searchfield">End Date (mm/dd/yyyy)</label>
                                        <input type="text" readonly="readonly" placeholder="dd/mm/yyyy HH:mm:ss" class="form-control" name="enddate" id="enddate" value="<?php echo (!empty($search_post['enddate']) ? $search_post['enddate'] : ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="duration">
                                        <label for="searchfield">Call Duration</label>
                                        <input type="text" class="form-control" id="duration" value="<?php echo (!empty($search_post['duration']) ? $search_post['duration'] : ''); ?>" name="duration" placeholder="Enter Duration in Seconds">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="lat">
                                        <label for="searchfield">Latitude</label>
                                        <input type="number" class="form-control" id="lat" value="<?php echo (!empty($search_post['lat']) ? $search_post['lat'] : ''); ?>" name="lat" placeholder="Enter Latitude">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="long">
                                        <label for="searchfield">Longitude</label>
                                        <input type="number" class="form-control" id="long" value="<?php echo (!empty($search_post['long']) ? $search_post['long'] : ''); ?>" name="long" placeholder="Enter Longitude">
                                    </div>
                                </div>
                                <!-- /.col -->
                                <!-- /.col -->
                            </div>
                            <div class="form-group pull-right">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                            </div>
                            <!-- /.col -->
                            <!-- /.row -->
                        </div>        
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">                        
                        <h3 class="box-title"><i class="fa fa-mobile-phone"></i>CDR Call Log of <?php echo Helpers_Person::get_person_name($person_id); ?></h3>
                        <a id="exportbutton" title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="celllogtable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">Party A</th>
                                        <th style="width: 12%">Party B</th>
                                        <th style="width: 10%">Call Type</th>
                                        <th style="width: 12%">Call Duration</th>
                                        <th style="width: 15%">Date & Time</th>
                                        <th>Lat/Long</th>
                                        <th>LOCATION</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th>Party A</th>
                                        <th>Party B</th>
                                        <th>Call Type</th>
                                        <th>Call Duration</th>
                                        <th>Date & Time</th>
                                        <th>Lat/Long</th>
                                        <th>LOCATION</th>
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

<div class="modal modal-info fade" id="external_search_model">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button onclick="" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    
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
                <button onclick="" type="button" style="margin-top: 10px" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->

</div>
<!-- /.content -->
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
        person_bparty();
        elem = $('#type').val();
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
        objDT = $('#celllogtable').dataTable(
                {"aaSorting": [[4, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('persons/ajaxcelllogsummary/?id='.$_GET['id'], TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Party B or Location:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");
        //here

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

    //function to search subscriber in local sources
    function search_local_subscriber_detail(search_type, search_value ) {
        var result = {search_type: search_type,search_value:search_value}
        $.ajax({
            url: "<?php echo URL::site('userreports/msisdn_data_search', TRUE); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (msg) {
                $("#external_search_results").html(msg);
            }
        });

    }

    function person_bparty() {
        var phonenumber = $('#phone_number').val();
        var searchresults = {phone: phonenumber}
        $.ajax({
            url: "<?php echo URL::site("Persons/other_person_phone_number/?id=".$_GET['id']); ?>",
            type: 'POST',
            data: searchresults,
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (data)
            {
                if (data == 2) 
                {
                swal("System Error", "Contact Support Team.", "error");
                }
                $("#otherphone").html(data);
            }
        });
    }
    $("#search_form").validate({
        rules: {
            type: {
                check_list: true,
            },
            phone_number: {
                check_list1: true,
            },
            key: {
                key_value: true,
            },
            startdate: {
                sdate_value: true,
               // vailddate: true
            },
            enddate: {
                edate_value: true,
                greaterThan: "#startDate"
            },
        },
        messages: {
            type: {
                check_list: "Please select search type",
            },
            phone_number: {
                check_list1: "Please select Phone Number",
            },
            key: {
                key_value: "Enter Valid Location Name",
            },
            startdate: {
                sdate_value: "Please select start date",
            },
            enddate: {
                edate_value: "Please select End date",
            },
        }
    });
    $.validator.addMethod("check_list1", function (sel, element) {
        if (sel == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");
    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "def") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("key_value", function (sel, element) {
        if ($('#type').val() == "location" && $('#searchfield').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("sdate_value", function (sel, element) {
        if ($('#type').val() == "date" && $('#startdate').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("edate_value", function (sel, element) {
        if ($('#type').val() == "date" && $('#enddate').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");
             jQuery.validator.addMethod("vailddate",
                function (value, element) {
                    var isValid = false;
                    var reg = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
                    if (reg.test(value)) {
                        var splittedDate = value.split('/');
                        var mm = parseInt(splittedDate[0], 10);
                        var dd = parseInt(splittedDate[1], 10);
                        var yyyy = parseInt(splittedDate[2], 10);
                        var newDate = new Date(yyyy, mm - 1, dd);
                        if ((newDate.getFullYear() == yyyy) && (newDate.getMonth() == mm - 1)
                                && (newDate.getDate() == dd))
                            isValid = true;
                        else
                            isValid = false;
                    } else
                        isValid = false;
                    return this.optional(element) || isValid;
                },
                "Please enter a valid date (mm/dd/yyyy)");
        
      jQuery.validator.addMethod("greaterThan",
                function (value, element, params) {
                    
                    if ($('#type').val() == "date" && $('#startdate').val() !== "") {
                      var date_1 = new Date($('#startdate').val());
                      var date_2 = new Date($('#enddate').val());
                      if(date_2>=date_1)
                      {
                          return true;
                      }else{
                          return false; 
                      }    
                    /*
                    if (!/Invalid|NaN/.test(new Date(value))) {
                        return new Date(value) > new Date($(params).val());
                    }*/
                    
                    /*var t= isNaN(value) && isNaN($(params).val())
                            || (Number(value) > Number($(params).val()));*/
                    
                }else {
                  //  console.log('test');
                    return true;
                }
                    
                }, 'Must be greater than Start Date');

//Date picker   //datepicker
    $('#startdate').datetimepicker({
        format: "yyyy-mm-dd hh:ii",
   autoclose: true,
   todayBtn: true,   
   //startDate: new Date(),
   minuteStep: 10,
   pickerPosition: "bottom-left"
 
    });
//Date picker
    $('#enddate').datetimepicker({
      //autoclose: true,
      //format: 'dd/mm/yyyy hh:mm'
      format: "yyyy-mm-dd hh:ii",
   autoclose: true,
   todayBtn: true,   
   minuteStep: 10,
   pickerPosition: "bottom-left"
    });
function clearSearch() {
        window.location.href = '<?php echo URL::site('persons/cell_log_summary/?id='.$_GET['id'], TRUE); ?>';
    }
function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
</script>