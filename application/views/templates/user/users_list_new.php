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
        <i class="fa fa-users"></i>
        User's Report 
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >User's Report</li>
        <li class="active">New Users</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">

            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-list"></i> New User's List</h3>                        
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="userslist" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>                                        
                                            <th style="width: 5% ">ID</th>
                                            <th class="no-sort" style="width: 10% ">Name</th>
                                            <th class="no-sort" style="width: 10% ">Designation</th>
                                            <th class="no-sort" style="width: 10% ">User Type</th>
                                            <th class="no-sort" style="width: 10% ">Posted In</th>  
                                            <!--<th class="no-sort" style="width: 10% ">Created by</th>-->                                                                                                                        
                                            <!--<th class="no-sort" style="width: 10% ">Designation</th>-->                                                                                                                        
                                            <th class="no-sort" style="width: 5% ">Order No.</th>                                                                                                                       
                                            <th style="width: 15% ">Created at</th>                                                                                                                        
                                            <th  class="no-sort" style="width: 25%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th class="no-sort">Name</th>
                                            <th class="no-sort">Designation</th>
                                            <th class="no-sort">User Type</th>
                                            <th class="no-sort">Posted In</th>  
                                            <!--<th class="no-sort">Created by</th>-->                                                                                                                        
                                            <!--<th class="no-sort">Designation</th>-->                                                                                                                        
                                            <th class="no-sort">Order No.</th>
                                            <th>Created at</th>
                                            <th  class="no-sort">Action</th>
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
        <div style="display:none" id="div-dialog-warning">
            <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><div/></p>
        </div>
</section>
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
        objDT = $('#userslist').dataTable(
                {
                    "aaSorting": [[6, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userreports/ajaxuserslistnew', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By User Name or Designation:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });
    function ConfirmChoiceBlock(id) {
        swal({
            title: "Reason Of Block User Required!",
            text: "Please Provide Reason of Block of this user",
            type: "input",
            showCancelButton: false,
            closeOnConfirm: false,
            allowEscapeKey: false,
            inputPlaceholder: "Please Provide Reason of Block of this user"
        }, function (inputValue) {
            if (inputValue === false)
                return false;
            if (inputValue === "") {
                swal.showInputError("Please Provide Reason of Block of this user!");
                return false
            }
            if (inputValue.length < 15) {
                swal.showInputError("Minimum 15 Characters Required!");
                return false
            }
            if (inputValue.length > 50) {
                swal.showInputError("Please Enter Not More than 50 Characters");
                return false
            }
            var isnum = /^[0-9a-zA-Z]/.test(inputValue);
            if (!isnum) {
                swal.showInputError("Only Number And Characters Allowed!");
                return false
            }
            var data = {reason: inputValue, id: id};
            $.ajax({
                url: "<?php echo URL::site("user/userblock"); ?>",
                type: 'POST',
                data: data,
                cache: false,
                //dataType: "text",
                dataType: 'json',
                success: function (result) {
                    if (result == '-2') {
                        swal("System Error", "Contact Support Team.", "error");
                    } else if (result == '-3') {
                        swal("Exception Error", "Contact Technical Support Team.", "error");
                    } else {
                        swal("Congratulations!!", "User Blocked Successfully!", "success");
                        refreshGrid();
                    }
                }
            });
        });
    }
    function ConfirmChoiceApprove(id)
    {
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
        //var elem = $(this).parent().parent();// .closest('.action');
        // var elem = $(this).closest('tr[class^="action"]');       
        //var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title': 'Approve user confirmation',
            'message': 'Do you really want to Approve this user?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::base() . "user/userApprove/"; ?>' + id,
                            success: function (result) {
                                if (result == '-2') {
                                    swal("System Error", "Contact Support Team.", "error");
                                } else if (result == '-3') {
                                    swal("System Error", "Contact Technical Support Team.", "error");
                                } else {
                                    refreshGrid();
                                }
                            }});

                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }

</script> 
