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
        Uploaded CDRs List
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Uploaded CDRs</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="box box-primary">

            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-search"></i> Uploaded CDRs</h3>
                </div>                
                <div class="form-group col-md-12 " >
                    <div class="alert-dismissible notificationclosereports" id="notification_msgreports" style="display: none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divreports"></div></h4>
                    </div>
                </div>
                <form class="" name="fixerror" id="fixerror" action="<?php echo url::site() . 'user/upload_against_imei' ?>"  method="post"  >
            <input type="hidden" name="requestid" id="requestid" value="" >
            <input type="hidden" name="receivedfilepath" id="receivedfilepath" value="" >
            <input type="hidden" name="receivedbody" id="receivedbody" value="" >
            <input type="hidden" name="requesttype" value="2" >
            <input type="hidden" name="requestvalue" id="requestvalue" value="" >           
        </form>
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
                        <table id="uploadedcdrs" class="table table-bordered table-striped">
                            <thead>
                                <tr>                                   
                                    <th>User</th>                                                                                                          
                                    <th >CDR Type</th>                                      
                                    <th >File Info</th>
                                    <th>Company</th>                                    
                                    <th>Created On</th>
                                    <th>Status</th>
                                    <th>Error</th>
                                    <th class="no-sort" >Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>                                     
                                    <th>User</th>                                                                                                          
                                    <th >CDR Type</th>                                      
                                    <th >File Info</th>
                                    <th>Company</th>                                    
                                    <th>Created On</th>
                                    <th>Status</th>
                                    <th>Error</th>
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

</section>


<!-- /.content -->
<script type="text/javascript">
        //request full parse cdr against imei
    function fullparseimeicdr(requestid,recfile,recbody,requestedvalue) {
    $("#requestid").val(requestid);
    $("#receivedfilepath").val(recfile);
    $("#receivedbody").val(recbody);
    $("#requestvalue").val(requestedvalue);
    $("#fixerror").submit();
    //alert($("#receivedbody").val());
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
   

$.validator.addMethod('filesize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
}, 'File size must be less than {0} Kb');
    
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
        objDT = $('#uploadedcdrs').dataTable(
                {"aaSorting": [[4, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('user/ajaxuseruplloadedcdrs', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By User Name, IMEI Number or Mobile Number:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });

function deletecdr(id,number) 
    { 
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
       //var elem = $(this).parent().parent();// .closest('.action');
       // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title'     : 'Delete Record !',
            'message'   : 'Do you really want to delete CDR against ' + number + ' from directory?' ,
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){        
                        $.ajax({url: '<?php echo URL::base() . "User/deletecdr_with_error/"; ?>'  + id , 
                            success: function(result){      
                                 if(result== 2){
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                                if (result == '-2') { 
                                    alert('Access denied, contact your technical support team');
                                }
                                else{
                            $("#notification_msg_divreports").html('Successfully Deleted');
                            $("#notification_msgreports").show();
                            $("#notification_msgreports").addClass('alert');
                            $("#notification_msgreports").addClass('alert-success');
                            var elem = $(".notificationclosereports");
                            elem.slideUp(5000);
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
</script>