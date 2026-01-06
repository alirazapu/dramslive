<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//echo '<pre>';
//print_r($person_id);
//exit();
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Person's Portal
        <small><?php  echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id='.$_GET['id']); ?>">Person Dashboard </a></li>
        <li class="active">Person's DB Match</li>
    </ol>
</section>
<!-- Main content -->

<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <div style="display:none;" id="custom-form"></div>
                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('persons/person_db_match/?id='.$_GET['id']); ?>" >
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
                                    <label>Mobile Number</label> <?php echo isset($search_post['phone_number']) ? '<b style="color:red"> Selected </b>' : 'not set';?>
                                    <select class="form-control " name="phone_number" id="phone_number" >                                                        
                                        <option value="">Please Select Person Number</option>
                                        <?php try{
                                        $sims_list = Helpers_Person::get_person_inuse_SIMs($person_id);
                                        foreach ($sims_list as $sim) {
                                            ?>
                                            <option <?php echo (!empty($search_post['phone_number']) && ($search_post['phone_number'] == $sim->phone_number)) ? 'selected' : '' ?> value=<?php echo $sim->phone_number ?> > <?php echo $sim->phone_number ?> </option>
                                        <?php } ?>
                                             echo <option <?php echo (!empty($search_post['phone_number']) && ($search_post['phone_number'] == 3)) ? 'selected' : '' ?> value='3' > All </option>   
                                        <?php }  catch (Exception $ex){                                             
                                        }
                                        ?>
                                    </select>
                                </div>
                                <input type="hidden" id="searched_values" name="searched_values" value="<?php echo isset($search_post['otherphone']) ? implode(",", $search_post['otherphone']) : ''?>">
                            </div>  
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Select Category</label> <?php echo isset($search_post['category']) ? '<b style="color:red"> Selected </b>' : 'not set';?>
                                    <select class="form-control " name="category" id="phone_number" >                                                        
                                        <option value="">Please Select Person Number</option>                                        
                                            <option <?php echo (isset($search_post['category']) && ($search_post['category'] == 0)) ? 'selected' : '' ?> value='0'> White </option>
                                            <option <?php echo (!empty($search_post['category']) && ($search_post['category'] == 1)) ? 'selected' : '' ?> value='1' > Grey </option>
                                            <option <?php echo (!empty($search_post['category']) && ($search_post['category'] == 2)) ? 'selected' : '' ?> value='2' > Black </option>
                                            <option <?php echo (!empty($search_post['category']) && ($search_post['category'] == 3)) ? 'selected' : '' ?> value='3' > Black & Grey </option>
                                            <option <?php echo (!empty($search_post['category']) && ($search_post['category'] == 4)) ? 'selected' : '' ?> value='4' > All </option>
                                    </select>
                                </div>
                                <input type="hidden" id="searched_values" name="searched_values" value="<?php echo isset($search_post['otherphone']) ? implode(",", $search_post['otherphone']) : ''?>">
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
                            
                            
                            <div class="form-group pull-right buttons" style="margin-right: 15px;">
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
                        <h3 class="box-title"><i class="fa fa-users"></i>  <?php try{ echo Helpers_Person::get_person_name($person_id); }  catch (Exception $ex){   }?>'s DB Match</h3>
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="personfavperson" class="table table-bordered table-striped">
                            <thead>
                                <tr>                                    
                                    <th>Phone Number</th>
                                    <th class="no-sort">Party B</th>
                                    <th class="no-sort" >Person's Info</th>
                                    <th >I Calls</th>
                                    <th >O Calls</th>
                                    <th >I SMS</th>
                                    <th >O SMS</th>
                                    <th>Last Calls/SMS Detail</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>                                    
                                    <th>Phone Number</th>
                                    <th>Party B</th>
                                    <th>Person's Name</th>
                                    <th>I Calls</th>
                                    <th>O Calls</th>
                                    <th>I SMS</th>
                                    <th>O SMS</th>
                                    <th>Last Calls/SMS Detail</th>
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
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>


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
        objDT = $('#personfavperson').dataTable(
                {"aaSorting": [[5, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('persons/ajaxpersondbmatch/?id='.$_GET['id'], TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Person Name:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }],
                    dom: 'Bfrtip',
                    buttons: [
                        'pageLength','excel', 'pdf', 'print'
                    ]
                }
        );

        $('.dataTables_empty').html("Information not found");

    });
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
                var onumber = $('#searched_values').val(); 
                var Values = new Array();
                Values = onumber.split(",");
                $("#otherphone").val(Values).trigger("change");
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
                edate_value: "Please select End date",
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
                        return new Date(value) > new Date($(params).val());
                    }
                    return isNaN(value) && isNaN($(params).val())
                            || (Number(value) > Number($(params).val()));
                }else{
                    return true;
                }
                }, 'Must be greater than ( Date From )');
    //Date picker
    $('#startdate').datepicker({
      autoclose: true
    });
//Date picker
    $('#enddate').datepicker({
      autoclose: true
    });
    
    function clearSearch() {
        window.location.href = '<?php echo URL::site('persons/person_db_match/?id='.$_GET['id'], TRUE); ?>';
    }       
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
           
    
</script>
