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
        <i class="fa fa-search"></i>
        bParty Search
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Blocked Numbers</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">

            <div class="box box-primary">
                <?php if (isset($_GET["message"]) && $_GET["message"] == 1) { ?>
                    <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> Congratulation! Number is added to block list Successfully.</h4>
                    </div> 
                 <?php } ?>
                <?php if (isset($_GET["message"]) && $_GET["message"] == 2) { ?>
                    <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> Error! Number already present in block list.</h4>
                    </div> 
                 <?php } ?>
                <div class="box-header with-border">
                    <h3 class="box-title">Blocked Numbers  <a class="btn" type="button" onclick="addnew()">[Add New]</a></h3>
                </div>
                <div class="box-body"> 
                    
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="blockednumbers" name="blockednumbers" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="no-sort">Number Type</th>
                                    <th class="no-sort">Number Value</th>
                                    <th class="no-sort">Block Reason</th>
                                    <th class="no-sort">Block Details</th>
                                    <th class="no-sort">Block By</th>
                                    <th>Block Date</th>
                                    <th class="no-sort">Action</th>                                        
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Number Type</th>
                                    <th>Number Value</th>
                                    <th>Block Reason</th>
                                    <th>Block Details</th>
                                    <th>Block By</th>
                                    <th>Block Date</th>
                                    <th class="no-sort">Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>                                            
        </div>
    </div>

</section>
<div class="modal modal-info fade" id="addnewnumber">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add New Number in Block list</h4>
            </div>
            <form class="" name="blocknumber" id="blocknumber" action="<?php echo url::site() . 'Userrequest/add_blocked_number' ?>"  method="post" enctype="multipart/form-data" >  
                <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>
                    <div  style="display:block;margin-top: 5px;margin-bottom: 5px;" class="col-md-12">
                        <div class="col-md-12" style="background-color: #fff;color: black">
                            
                            <div class="form-group col-md-4">
                                <label for="numbervalue">Number Value</label>
                                <input type="text" class="form-control" id="numbervalue" name="numbervalue" placeholder="Number Value">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="account_no">Number Type</label>
                                <select class="form-control" id="numbertype" name="numbertype">
                                    <option value="">Please Select Number Type</option>
                                    <option value="1">Mobile Number</option>
                                    <option value="2">CNIC Number</option>
                                    <option value="3">IMEI Number</option>
                                </select>

                            </div>
                            <div class="form-group col-md-4">
                                <label for="blockreason">Reason of Block</label>
                                <select class="form-control" id="blockreason" name="blockreason">
                                    <option value="">Please Select reason</option>
                                    <option value="Suspended">Suspended</option>
                                    <option value="Blocked">Blocked</option>
                                    <option value="biometric verification failed">Biometric verification failed</option>
                                    <option value="voluntary blocked">Voluntary blocked</option>
                                    <option value="access denied">Access denied</option>
                                </select>
                            </div>
                            <div class="form-group col-md-8">
                                <label for="blockdetails">Block Details</label>
                                <input type="text" class="form-control" id="blockdetails" name="blockdetails" placeholder="Enter Detailed Reason of Blocking Number">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>                    
                    <button type="submit"  class="btn btn-primary ">Save Data</button>
                </div>  
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>

    //
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
        if ($("#msg_to_show").val() !== "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        //advance search
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
        objDT = $('#blockednumbers').dataTable(
                {"aaSorting": [[5, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //  //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userrequest/ajaxblockednumbers', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search by Number:",
                        "sEmptyTable": 'Information not found'
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false
                        }]
                }
        );
    });
        // $('.dataTables_empty').html("Information not found, <a href='<?php // echo URL::site('userrequest/request/1', TRUE);  ?>'> Please Request form Here </a>");


// validation for data search
        $("#blocknumber").validate({
            rules: {
                numbertype: {
                     required: true,
                    //numberthree: true,
                    // maxlength: 10,
                    // minlength: 10
                },
                numbervalue: {
                     required: true,
                     number: true,
                      maxlength: 16,
                     minlength: 10
                },
                blockreason: {
                     required: true,
                },
                blockdetails: {
                     required: true,
                     maxlength: 100,
                     minlength: 10
                }
            },
            messages: {
                numbertype: {
                    //Number:"Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },
                numbervalue: {
                    number:"Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },
                blockreason: {
                    number:"Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },
                blockdetails: {
                    number:"Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                }
            },

            submitHandler: function () {
                $("#blocknumber").submit();

            }
            // $('#upload').show()
        });



        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");



    jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .,/]+$/);
    }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "" || sel == 0) {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    //Confirm choice to mark favourite person
    function addnew()
    {
     $("#addnewnumber").modal("show");
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
   function DeleteBlockedNumber(id) 
    { 
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
       //var elem = $(this).parent().parent();// .closest('.action');
       // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title'     : 'Delete Record confirmation',
            'message'   : 'Do you really want to delete this number from blocked numbers?' ,
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){        
                        $.ajax({url: '<?php echo URL::base() . "Userrequest/deleteblockednumber/"; ?>'  + id , success: function(result){                                 
                                if (result == '-2') { 
                                    alert('Access denied, contact your technical support team');
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

</script>