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
        <small><?php  echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id='.$_GET['id']); ?>">Person Dashboard </a></li>
        <li class="active">Person's Favourite Persons</li>
    </ol>
</section>
<!-- Main content -->

<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <div style="display:none;" id="custom-form"></div>
                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('persons/person_favourite_person/?id='.$_GET['id']); ?>" >
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
                                    <label>Mobile Number</label> <?php echo isset($search_post['advance_search']) ? $search_post['advance_search'] : 'not set';?>
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
                                <input type="hidden" id="searched_values" name="searched_values" value="<?php echo isset($search_post['otherphone']) ? implode(",", $search_post['otherphone']) : ''?>">
                            </div>
                            <div class="col-md-8">
                                <div class="form-group personfavperson-mobile">
                                    <label for="searchfield">Other Person Mobile Number(Optional)</label>
                                    <select class="form-control select2" multiple="multiple" data-placeholder="Select Other Person Number" name="otherphone[]" id="otherphone" style="width: 100%;">
                                    </select>                                            
                                </div>        
                            </div>
                            <!-- /.col -->
                            <!-- /.col -->
                            <!-- /.col -->

                            <div class="form-group pull-right buttons">
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
                        <h3 class="box-title"><i class="fa fa-users"></i>  <?php try{ echo Helpers_Person::get_person_name($person_id); }  catch (Exception $ex){   }?>'s Favourite Persons</h3>
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="personfavperson" class="table table-bordered table-striped">
                            <thead>
                                <tr>                                    
                                    <th style="width: 10%">Party B</th>
                                    <th class="no-sort" style="width: 25%">Person's Name</th>
                                    <th class="no-sort" style="width: 15%">Father/Husband Name</th>
                                    <th class="no-sort" style="width: 15%">Person's CNIC</th>
                                    <th style="width: 10%">Total Call</th>
                                    <th style="width: 10%">Total SMS</th>
                                    <th class="no-sort">Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>                                    
                                    <th>Party B</th>
                                    <th>Person's Name</th>
                                    <th>Father/Husband Name</th>
                                    <th>Person's CNIC</th>
                                    <th>Total Call</th>
                                    <th>Total SMS</th>
                                    <th>Action</th>
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
                {"aaSorting": [[4, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('persons/ajaxpersonfavouriteperson/?id='.$_GET['id'], TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading..."
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );

        $('.dataTables_empty').html("Information not found");

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
                var onumber = $('#searched_values').val(); 
                var Values = new Array();
                Values = onumber.split(",");
                $("#otherphone").val(Values).trigger("change");
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
            },
            enddate: {
                edate_value: true,
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
    function clearSearch() {
        window.location.href = '<?php echo URL::site('persons/person_favourite_person/?id='.$_GET['id'], TRUE); ?>';
    }
    //function to call request page for subscriber new irfan
    function requestsub(sim) {    
        var phonenumber = $("#phonenumber").val();
        var url = window.location.href;        
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestsubscriber/?id=" . $_GET['id']); ?>" method="POST">');
        newForm.append(jQuery('<input>', {
            'name': 'msisdn',
            'value': sim,
            'type': 'text'
        }));
        newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
        }));
        newForm.append('<input type="submit" id="custom_form_request_button" name="submit" value="true"/>');
        $('#custom-form').append(newForm);
        //$("#custom_form_bd").submit();
        $('#custom_form_request_button').trigger('click');

    }        
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
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
    //function to search subscriber in local sources
    function search_local_subscriber_detail(search_type, search_value ) {
        var result = {search_type: search_type,search_value:search_value}
        $.ajax({
            url: "<?php echo URL::site('userreports/msisdn_data_search', TRUE); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (msg) {
                if (msg == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                $("#external_search_results").html(msg);
            }
        });

    }
    
</script>
