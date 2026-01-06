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
        Rejected Requests
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Rejected Requests</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="box box-primary">

            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-search"></i> Request Status</h3>
                </div>                
                <div class="form-group col-md-12 " >
                    <div class="alert-dismissible notificationclosereports" id="notification_msgreports" style="display: none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divreports"></div></h4>
                    </div>
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
                    <div class="table-responsive-11">
                        <table id="requeststatus" class="table table-bordered table-striped">
                            <thead>
                                <tr>                                   
                                    <th>User</th>                                                                                                          
                                    <th class="no-sort">Request Type</th>                                    
                                    <th class="no-sort">Requested Value</th>                                    
                                    <th >Person</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="no-sort" >Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>                                     
                                    <th>User</th>                                                                                                          
                                    <th>Request Type</th>
                                    <th>Requested Value</th> 
                                    <th>Person</th>
                                    <th>Date</th>
                                    <th>Status</th>
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


<!-- /.content -->
<script type="text/javascript">
    /* For nadra verisis request processing */
    function findphonenumber(cnic,requestid) {

        $("#cnic_number").val(cnic);
        $("#process_request_id").val(requestid);
        $("#process_nadra_verysis").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);

    }

    //table 
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        //validate Person Verisis       
       

    
    //table data
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
                {"aaSorting": [[4, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userrequest/ajaxuserrejectedrequests', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By User Name or Requested Value:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });
// request update
//$(document).ready(function (e) {

function resendrequest(requestid) {
        var result = {process_request_id: requestid}
                    //ajax to upload device informaiton
                    $.ajax({
                        url: "<?php echo URL::site("userrequest/resend_request"); ?>",
                        type: 'POST',
                        data: result,
                        cache: false,
                        success: function (msg) {
                             $("#notification_msg_divreports").html('Successfully Updated');
                            $("#notification_msgreports").show();
                            $("#notification_msgreports").addClass('alert');
                            $("#notification_msgreports").addClass('alert-success');
                            var elem = $(".notificationclosereports");
                            elem.slideUp(2000);
                            refreshGrid();
                            if(msg==2){
                                swal("System Error", "Contact Support Team.", "error");
                            }
                        }
                    });
    
    }
    function deleterejecteduserrequest(id,value) 
    { 
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
       //var elem = $(this).parent().parent();// .closest('.action');
       // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title'     : 'Delete Record confirmation',
            'message'   : 'Do you really want to delete this request ?' ,
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){        
                        $.ajax({url: '<?php echo URL::base() . "Userrequest/deleterejecteduserrequest/"; ?>'  + id , 
                            success: function(result){                                 
                                if (result == -2) { 
                                    
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                else{
                            elem.slideUp(10000);
                            refreshGrid();
                                    }
                        }});
                        
                    }
                 },
                'No'    : {
                    'class' : 'blue',
                    'action': function(){}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
   }
    
    
      
    

//    $("#ImageBrowse").on("change", function() {
//        $("#imageUploadForm").submit();
//    });
//});
    function clearSearch() {
        window.location.href = '<?php echo URL::site('userrequest/request', TRUE); ?>';
    }

</script>