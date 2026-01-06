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
        <li class="active">Person SIMs</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-mobile-phone"></i> SIMs Used By: <?php echo Helpers_Person::get_person_name($person_id); ?> </h3>
                    </div>
                    <div style="display:none;" id="custom-form"></div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="simstable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="no-sort" style="width: 8%">SIM#</th>
                                        <th style="width: 6%">Owner</th>
                                        <th style="width: 6%">User</th>
                                        <th class="no-sort" style="width: 10%">Last Used</th>
                                        <th class="no-sort" style="width: 6%">Company</th>
                                        <th class="no-sort" style="width: 6%">Status</th>
                                        <th class="no-sort" style="width: 6%">Type</th>
                                        <th class="no-sort" style="width: 10%">Activation</th>
                                        <th class="no-sort" style="width: 9%">Type</th>
                                        <th class="no-sort" style="width: 9%">Linked Portals</th>
                                        <th class="no-sort" >Request Data</th>
                                        <th class="no-sort" style="width: 5%">Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>SIM#</th>
                                        <th>Owner</th>
                                        <th>User</th>
                                        <th>Last Used</th>
                                        <th>Company</th>
                                        <th>Status</th>
                                        <th>Type</th>
                                        <th>Activation</th>
                                        <th>Type</th>
                                        <th>Linked Portals</th>
                                        <th class="no-sort" >Request Data</th>
                                        <th class="no-sort" >Action</th>
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
<!--.content -->
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
        objDT = $('#simstable').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('personsreports/ajaxsimsdetail/?id='.$_GET['id'], TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By SIM Number:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );

        $('.dataTables_empty').html("Information not found");

    });
    //this 1
    //function to call request page for CDR
    function requestcdr(sim, personID) {
        var request = "existing";
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr" id="custom_form_bd_cdr" action="<?php echo URL::site("userrequest/requestcdr/?id=" . $_GET['id']); ?>" method="POST">');
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
            newForm.append(jQuery('<input>', {
            'name': 'requesttype',
            'value': 1,
            'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'pid',
                'value': personID,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'request',
                'value': request,
                'type': 'text'
            }));
            newForm.append('<input type="submit" id="custom_form_bd_bt_cdr" name="submit" value="true"/>');
            $('#custom-form').append(newForm);
            //$("#custom_form_bd").submit();
            $('#custom_form_bd_bt_cdr').trigger('click');

        
        //var result = {msisdn: sim, pid: personID, requesttype: 1, request: request}
        //$.ajax({
        //    url: "<?php //echo URL::site("userrequest/request/2/?id=" . $_GET['id']); ?>//",
        //    type: 'POST',
        //    data: result,
        //    cache: false,
        //    success: function (msg) {
        //        var newDoc = document.open("text/html", "replace");
        //        newDoc.write(msg);
        //        newDoc.close();
        //    }
        //});

    }
    //function to call request page for location
    function requestlocation(sim, personID) {
        var request = "existing";
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/requestlocation/?id=" . $_GET['id']); ?>" method="POST">');
            newForm.append(jQuery('<input>', {
                'name': 'msisdn',
                'value': sim,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'requesttype',
                'value': 4,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
            'name': 'url',
            'value': url,
            'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'pid',
                'value': personID,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'request',
                'value': request,
                'type': 'text'
            }));
            newForm.append('<input type="submit" id="custom_form_bd_bt_location" name="submit" value="true"/>');
            $('#custom-form').append(newForm);
            //$("#custom_form_bd").submit();
            $('#custom_form_bd_bt_location').trigger('click');

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
            'name': 'pid',
            'value': <?php $personID = (int) Helpers_Utilities::encrypted_key($_GET['id'], "decrypt");
            echo $personID; ?>,
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
//function to call request page for sms details
    function requestsmsdetails(sim, personID) {
        var request = "existing";
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_request" id="custom_form_request" action="<?php echo URL::site("userrequest/requestcdrsms/?id=" . $_GET['id']); ?>" method="POST">');
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
            newForm.append(jQuery('<input>', {
                'name': 'pid',
                'value': personID,
                'type': 'text'
            }));
            newForm.append('<input type="submit" id="custom_form_bd_bt_cdrsms" name="submit" value="true"/>');
            $('#custom-form').append(newForm);
            //$("#custom_form_bd").submit();
            $('#custom_form_bd_bt_cdrsms').trigger('click');

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