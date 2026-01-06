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
        <li class="active">Person Devices</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-mobile-phone"></i> Devices Used By: <?php echo Helpers_Person::get_person_name($person_id); ?> </h3>
                    </div>
                    <div style="display:none;" id="custom-form"></div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="devicestable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Device</th>
                                        <th>IMEI#</th>
                                        <th>Phone#</th>
                                        <th>Using Since</th>
                                        <th>Last Interaction</th>
                                        <th class="no-sort">Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Device</th>
                                        <th>IMEI#</th>
                                        <th>Phone#</th>
                                        <th>Using Since</th>
                                        <th>Last Interaction</th>
                                        <th class="no-sort">Action</th>
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
        objDT = $('#devicestable').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('personsreports/ajaxdevicedetail/?id='.$_GET['id'], TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By IMEI# or Phone#:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );

        $('.dataTables_empty').html("Information not found");

    });
//function to call request page for CDR
    function requestimeicdr(imei, personID) {
        var request = "existing";
        var url = window.location.href;
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/requestcdrimei/?id=" . $_GET['id']); ?>" method="POST">');
            newForm.append(jQuery('<input>', {
                'name': 'imei',
                'value': imei,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'requesttype',
                'value': 2,
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
            newForm.append('<input type="submit" id="custom_form_bd_bt_cdr_imei" name="submit" value="true"/>');
            $('#custom-form').append(newForm);
            //$("#custom_form_bd").submit();
            $('#custom_form_bd_bt_cdr_imei').trigger('click');
        
        /*
        var request = "existing";
        var result = {imei: imei, pid: personID, requesttype: 2, request: request}
        $.ajax({
            url: "<?php // echo URL::site("userrequest/request/2/?id=" . $_GET['id']); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (msg) {
                var newDoc = document.open("text/html", "replace");
                newDoc.write(msg);
                newDoc.close();
            }
        });
*/
    }
    
</script>