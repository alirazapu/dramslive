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
        <i  class="fa fa-user"></i>
        User's Report
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li> </i>User's Report</a></li>
        <li ></i>User's Favourite Users</li>
        <li class="active"></i>User's Favourite Agent</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">        
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo Helpers_Utilities::get_user_name(Session::instance()->get('userid')) ?>'s Favourite Agent</h3>
                        <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userreports/users_favourite_agent/' . $user_id); ?>" >                            
                            <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                            <input id="xport" name="xport" type="hidden" value="" />
                        </form>
                    </div>
                    <!-- /.box-header -->
                    <div class=" box-body">
                        <div class="table-responsive">
                            <table id="usersfavouriteagent" class="table  table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>User Type</th>
                                        <th>Designation</th>
                                        <th>Posted In</th>
                                        <th>View Details</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Name</th>
                                        <th>User Type</th>
                                        <th>Designation</th>
                                        <th>Posted In</th>                                                        
                                        <th>View Details</th>
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
        objDT = $('#usersfavouriteagent').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userreports/ajaxusersfavouriteagent', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By User Name:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
</script>

<script>
    function ConfirmChoice(id)
    {
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
        //var elem = $(this).parent().parent();// .closest('.action');
        // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title': 'Delete Confirmation',
            'message': 'Do you really want to delete this user from your Favourite list?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({url: '<?php echo URL::base() . "Userreports/deletefavouriteuser/"; ?>' + id, success: function (result) {
                                if (result == '-2') {
                                    alert('Access denied, contact your technical support team');
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