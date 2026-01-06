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
        <li class="active">Physical Location Summary</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="">
                <div class="box box-primary">
                    <form id="search_form" name="search_form" class="ipf-form cell_log_summary" method="POST" action="<?php echo URL::site('persons/physical_location_summary/?id=' . $_GET['id']); ?>" >
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
                                            <?php
                                            try {
                                                $sims_list = Helpers_Person::get_person_inuse_SIMs($person_id);
                                                foreach ($sims_list as $sim) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['phone_number']) && ($search_post['phone_number'] == $sim->phone_number)) ? 'selected' : '' ?> value=<?php echo $sim->phone_number ?> > <?php echo $sim->phone_number ?> </option>
                                                <?php
                                                }
                                            } catch (Exception $ex) {
                                                
                                            }
                                            ?>
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
                                        <label for="searchfield">End Date (mm/dd/yyyy)</label>
                                        <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="enddate" id="enddate" value="<?php echo (!empty($search_post['enddate']) ? $search_post['enddate'] : ''); ?>">
                                    </div>
                            </div>                                
                                <div class="col-md-12">                                    
                                    <div class="form-group pull-right" style="">                                         
                                        <!--<button type="submit" class="btn btn-primary">Search</button>-->
                                        <button type="submit" class="btn btn-primary">Search</button>
                                        <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                    </div>
                                </div>
                                <!-- /.col -->
                                <!-- /.row -->
                            </div>        
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <!-- MAP & BOX PANE -->
            <div class="box">               
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">                        
                                    <h3 class="box-title"><i class="fa fa-mobile-phone"></i>Physical Location Summary of <?php echo Helpers_Person::get_person_name($person_id); ?></h3>
                                    <a id="exportbutton" title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table id="locationsummary" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="no-sort" style="width: 10%;">Party A</th>
                                                    <th class="no-sort" style="width: 65%;">Location</th>
                                                    <th class="no-sort" style="width: 15%;">Latitude</th>
                                                    <th class="no-sort" style="width: 15%;">Longitude</th>
                                                    <th style="width: 15%;">Location Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>

                                            <tfoot>
                                                <tr>
                                                    <th>Party A</th>
                                                    <th>Location</th>
                                                    <th>Latitude</th>
                                                    <th>Longitude</th>
                                                    <th>Location Count</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>                                                                                         
                                        <!--                                                                                        <div class="text-center box-footer">                                                                                                                                                                                                                         <b><a href="" ><i class="fa fa-arrow-circle-left"></i></a> 2016-09-19 <a href=""><i class="fa fa-arrow-circle-right"></i></a></b>                                                                                                                   </div>-->
                        </div>
                        <!-- /.col -->               
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
            <!-- TABLE: LATEST ORDERS -->          
            <!-- /.box -->
        </div>
        <!-- /.col -->                            
    </div>
</div>

</section>
<!-- /.content -->
<script src="https://maps.google.com/maps/api/js?key=AIzaSyBwL7lXZwan5Cp6GjddHiNNM3VJhZ3oYvE&sensor=false" type="text/javascript"></script>
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>
<script>
                                            $(function () {
                                                //Initialize Select2 Elements
                                                $(".select2").select2();
                                            });
</script>
<script>
    $(document).ready(function () {
        
        objDT = $('#locationsummary').dataTable(
                {"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('persons/ajaxphysicallocationsummary/?id=' . $_GET['id'], TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Location:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");
        
    });

    function person_bparty() {
        var phonenumber = $('#phone_number').val();
        var searchresults = {phone: phonenumber}
        $.ajax({
            url: "<?php echo URL::site("Persons/other_person_phone_number/?id=" . $_GET['id']); ?>",
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
            phone_number: {
                required: true,
            },
             enddate: {
                check_list1: true,
                greaterThan: "#startdate",
            },
        },
        messages: {            
            enddate: {
                greaterThan: "Must be greater than start date",
            },
        }
    });
    $.validator.addMethod("check_list1", function (sel, element) {    
        if ($('#enddate').val() != "" && $('#startdate').val() == ""){
            return false;
        } else {
            return true;
        }
    }, "<span>Please Select Start Date</span>");  
    
   jQuery.validator.addMethod("greaterThan",function (value, element, params) {
       if ($('#enddate').val() != "") {
                    if (!/Invalid|NaN/.test(new Date(value))) {
                        return new Date(value) >= new Date($(params).val());
                    }
                    return isNaN(value) && isNaN($(params).val())
                            || (Number(value) >= Number($(params).val()));
                }else{
                    return true;
                }
                }, 'Must be greater than ( Date From )');
                
    function clearSearch() {
        window.location.href = '<?php echo URL::site('persons/physical_location_summary/?id=' . $_GET['id'], TRUE); ?>';
    }    
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
    //Date picker
    $('#startdate').datepicker({
      autoclose: true
    });
//Date picker
    $('#enddate').datepicker({
      autoclose: true
    });
</script>

