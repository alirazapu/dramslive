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
                        <i class="fa fa-user" ></i>
                        User's Report
                        <small>DRAMS</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li> User's Report</a></li>
                        <li class="active">Request Send Log</li>
                    </ol>
                </section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <?php 
        $project_id = Helpers_Utilities::encrypted_key($search_post['project_id'], 'decrypt'); 
        $sdate = !empty($search_post['sdate'])? '&sdate='. $search_post['sdate']:'';
        $edate = !empty($search_post['edate'])? '&edate='. $search_post['edate']:'';
        
        
        
        //print_r($search_post); exit;
        ?>
        <form role="form" id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userreports/project_request_type/?project_id='.$search_post['project_id'] . $sdate . $edate); ?>" >
         <input id="xport" name="xport" type="hidden" value="" />
        </form>
        <?php
        $summary = isset($summary) ? $summary : array('totals' => array(), 'types' => array());
        $totals = $summary['totals'];
        $project_name = isset($project_id) ? Helpers_Utilities::get_projects_names($project_id) : "UnKnown";
        $sdate_get = isset($search_post['sdate']) ? $search_post['sdate'] : '';
        $edate_get = isset($search_post['edate']) ? $search_post['edate'] : '';
        $project_id_en = isset($search_post['project_id']) ? $search_post['project_id'] : '';
        // user details looked up once per user
        $user_cache = array();
        $user_info = function ($row) use (&$user_cache) {
            $uid = $row['user_id'];
            if (!isset($user_cache[$uid])) {
                $user_cache[$uid] = array(
                    'name' => Helpers_Utilities::get_user_name($uid),
                    'designation' => Helpers_Utilities::get_user_job_title($uid),
                    'region' => !empty($row['region_id']) ? Helpers_Utilities::get_region($row['region_id']) : 'Head Quarters',
                    'posting' => isset($row['posted']) ? Helpers_Profile::get_user_posting($row['posted']) : 'NA',
                );
            }
            return $user_cache[$uid];
        };
        ?>
        <style>
            .prt-card { background: #fff; border-radius: 3px; border-top: 3px solid #3c8dbc; padding: 10px 12px; margin-bottom: 15px; box-shadow: 0 1px 1px rgba(0,0,0,.1); }
            .prt-card .prt-num { font-size: 24px; font-weight: bold; line-height: 1.2; }
            .prt-card .prt-lbl { color: #777; font-size: 12px; text-transform: uppercase; }
            .prt-type-row { cursor: pointer; }
            .prt-type-row:hover { background: #f5f9fc !important; }
            .prt-type-row .fa-chevron-right { transition: transform .15s; color: #3c8dbc; }
            .prt-type-row.open .fa-chevron-right { transform: rotate(90deg); }
            .prt-users td { background: #fafafa; padding: 0 !important; }
            .prt-users table { margin: 0; }
            .prt-users th { background: #eef3f7; font-size: 12px; }
            .prt-progress { margin: 0; height: 14px; min-width: 110px; }
            .prt-progress .progress-bar { font-size: 10px; line-height: 14px; }
        </style>

        <!-- Summary cards -->
        <div class="row">
            <div class="col-md-2 col-sm-4 col-xs-6"><div class="prt-card"><div class="prt-num"><?php echo number_format(isset($totals['total']) ? $totals['total'] : 0); ?></div><div class="prt-lbl">Total Requests</div></div></div>
            <div class="col-md-2 col-sm-4 col-xs-6"><div class="prt-card" style="border-top-color:#605ca8;"><div class="prt-num"><?php echo isset($totals['types']) ? $totals['types'] : 0; ?></div><div class="prt-lbl">Request Types</div></div></div>
            <div class="col-md-2 col-sm-4 col-xs-6"><div class="prt-card" style="border-top-color:#00c0ef;"><div class="prt-num"><?php echo isset($totals['users']) ? $totals['users'] : 0; ?></div><div class="prt-lbl">Users</div></div></div>
            <div class="col-md-2 col-sm-4 col-xs-6"><div class="prt-card" style="border-top-color:#00a65a;"><div class="prt-num text-green"><?php echo number_format(isset($totals['received']) ? $totals['received'] : 0); ?></div><div class="prt-lbl">Received</div></div></div>
            <div class="col-md-2 col-sm-4 col-xs-6"><div class="prt-card" style="border-top-color:#f39c12;"><div class="prt-num text-yellow"><?php echo number_format(isset($totals['pending']) ? $totals['pending'] : 0); ?></div><div class="prt-lbl">Pending (Queue / Sent)</div></div></div>
            <div class="col-md-2 col-sm-4 col-xs-6"><div class="prt-card" style="border-top-color:#dd4b39;"><div class="prt-num text-red"><?php echo number_format(isset($totals['failed']) ? $totals['failed'] : 0); ?></div><div class="prt-lbl">Error / Rejected</div></div></div>
        </div>

        <!-- Request type summary -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-list-alt"></i> Request Type Summary of Project <u><?php echo $project_name; ?></u></h3>
                        <div class="box-tools pull-right">
                            <a href="javascript:void(0)" onclick="prtToggleAll(true)" class="btn btn-default btn-xs"><i class="fa fa-plus"></i> Expand All</a>
                            <a href="javascript:void(0)" onclick="prtToggleAll(false)" class="btn btn-default btn-xs"><i class="fa fa-minus"></i> Collapse All</a>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (empty($summary['types'])) { ?>
                            <div class="text-center text-muted" style="padding: 20px;">No requests found for this project.</div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="prt_summary">
                                <thead>
                                    <tr style="background:#f4f4f4;">
                                        <th style="width:30px;"></th>
                                        <th>Request Type</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center" title="Distinct numbers / CNICs / IMEIs requested">Unique Values</th>
                                        <th class="text-center">Users</th>
                                        <th class="text-center"><span class="text-green">Received</span></th>
                                        <th class="text-center"><span class="text-yellow">In Queue</span></th>
                                        <th class="text-center"><span class="text-yellow">Sent</span></th>
                                        <th class="text-center"><span class="text-red">Error</span></th>
                                        <th class="text-center"><span class="text-red">Rejected</span></th>
                                        <th>Progress</th>
                                        <th>Period</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($summary['types'] as $type_id => $type) {
                                    $received_pct = $type['total'] > 0 ? round($type['received'] * 100 / $type['total']) : 0;
                                    $pending_pct = $type['total'] > 0 ? round(($type['in_queue'] + $type['sent']) * 100 / $type['total']) : 0;
                                    $failed_pct = $type['total'] > 0 ? round(($type['send_error'] + $type['rejected']) * 100 / $type['total']) : 0;
                                    $request_type_en = Helpers_Utilities::encrypted_key($type_id, 'encrypt');
                                    ?>
                                    <tr class="prt-type-row" data-type="<?php echo (int) $type_id; ?>" title="Click to see user wise detail">
                                        <td class="text-center"><i class="fa fa-chevron-right"></i></td>
                                        <td><b><?php echo HTML::chars($type['type_name']); ?></b></td>
                                        <td class="text-center"><span class="badge bg-blue"><?php echo number_format($type['total']); ?></span></td>
                                        <td class="text-center"><?php echo number_format($type['unique_values']); ?></td>
                                        <td class="text-center"><?php echo $type['users']; ?></td>
                                        <td class="text-center text-green"><b><?php echo number_format($type['received']); ?></b></td>
                                        <td class="text-center"><?php echo number_format($type['in_queue']); ?></td>
                                        <td class="text-center"><?php echo number_format($type['sent']); ?></td>
                                        <td class="text-center text-red"><?php echo number_format($type['send_error']); ?></td>
                                        <td class="text-center text-red"><?php echo number_format($type['rejected']); ?></td>
                                        <td>
                                            <div class="progress prt-progress" title="Received <?php echo $received_pct; ?>% / Pending <?php echo $pending_pct; ?>% / Error-Rejected <?php echo $failed_pct; ?>%">
                                                <div class="progress-bar progress-bar-success" style="width: <?php echo $received_pct; ?>%"><?php echo $received_pct >= 15 ? $received_pct . '%' : ''; ?></div>
                                                <div class="progress-bar progress-bar-warning" style="width: <?php echo $pending_pct; ?>%"></div>
                                                <div class="progress-bar progress-bar-danger" style="width: <?php echo $failed_pct; ?>%"></div>
                                            </div>
                                        </td>
                                        <td><small><?php echo date('d-M-Y', strtotime($type['first_request'])); ?><?php if (date('Y-m-d', strtotime($type['first_request'])) != date('Y-m-d', strtotime($type['last_request']))) { ?> to <?php echo date('d-M-Y', strtotime($type['last_request'])); ?><?php } ?></small></td>
                                    </tr>
                                    <tr class="prt-users" data-type="<?php echo (int) $type_id; ?>" style="display:none;">
                                        <td colspan="12">
                                            <table class="table table-condensed table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>User Name</th>
                                                        <th>Designation</th>
                                                        <th>Region</th>
                                                        <th>Posting</th>
                                                        <th class="text-center">Requests</th>
                                                        <th class="text-center">Received</th>
                                                        <th class="text-center">Pending</th>
                                                        <th class="text-center">Error / Rejected</th>
                                                        <th class="text-center">Detail</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($type['users_list'] as $user_row) {
                                                    $info = $user_info($user_row);
                                                    $detail_url = URL::site('userreports/project_request_send_detail/?userid=' . Helpers_Utilities::encrypted_key($user_row['user_id'], 'encrypt') . '&request_type=' . $request_type_en . '&project_id=' . $project_id_en . '&sdate=' . $sdate_get . '&edate=' . $edate_get);
                                                    ?>
                                                    <tr>
                                                        <td><?php echo HTML::chars($info['name']); ?></td>
                                                        <td><?php echo HTML::chars($info['designation']); ?></td>
                                                        <td><?php echo HTML::chars($info['region']); ?></td>
                                                        <td><?php echo HTML::chars($info['posting']); ?></td>
                                                        <td class="text-center"><b><?php echo number_format($user_row['total']); ?></b></td>
                                                        <td class="text-center text-green"><?php echo number_format($user_row['received']); ?></td>
                                                        <td class="text-center text-yellow"><?php echo number_format($user_row['in_queue'] + $user_row['sent']); ?></td>
                                                        <td class="text-center text-red"><?php echo number_format($user_row['send_error'] + $user_row['rejected']); ?></td>
                                                        <td class="text-center"><a href="<?php echo $detail_url; ?>" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> View Detail</a></td>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                    <tr style="background:#f4f4f4; font-weight:bold;">
                                        <td></td>
                                        <td>Total</td>
                                        <td class="text-center"><?php echo number_format($totals['total']); ?></td>
                                        <td></td>
                                        <td class="text-center"><?php echo $totals['users']; ?></td>
                                        <td class="text-center text-green"><?php echo number_format($totals['received']); ?></td>
                                        <td class="text-center" colspan="2"><?php echo number_format($totals['pending']); ?></td>
                                        <td class="text-center text-red" colspan="2"><?php echo number_format($totals['failed']); ?></td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users worked on this project -->
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-users"></i> Users Worked on Project <u><?php echo $project_name; ?></u>
                            <span class="badge bg-aqua"><?php echo !empty($summary['users']) ? count($summary['users']) : 0; ?></span></h3>
                        <div class="box-tools pull-right">
                            <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (empty($summary['users'])) { ?>
                            <div class="text-center text-muted" style="padding: 20px;">No users found for this project.</div>
                        <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="prt_users">
                                <thead>
                                    <tr style="background:#f4f4f4;">
                                        <th style="width:35px;">#</th>
                                        <th>User Name</th>
                                        <th>Designation</th>
                                        <th>Region</th>
                                        <th>Posting</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center"><span class="text-green">Received</span></th>
                                        <th class="text-center"><span class="text-yellow">Pending</span></th>
                                        <th class="text-center"><span class="text-red">Error / Rejected</span></th>
                                        <th>Request Types</th>
                                        <th>Active Period</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sr = 1;
                                foreach ($summary['users'] as $team_user) {
                                    $info = $user_info($team_user);
                                    $user_en = Helpers_Utilities::encrypted_key($team_user['user_id'], 'encrypt');
                                    $first_day = date('d-M-Y', strtotime($team_user['first_request']));
                                    $last_day = date('d-M-Y', strtotime($team_user['last_request']));
                                    ?>
                                    <tr>
                                        <td><?php echo $sr++; ?></td>
                                        <td><b><?php echo HTML::chars($info['name']); ?></b></td>
                                        <td><?php echo HTML::chars($info['designation']); ?></td>
                                        <td><?php echo HTML::chars($info['region']); ?></td>
                                        <td><?php echo HTML::chars($info['posting']); ?></td>
                                        <td class="text-center"><span class="badge bg-blue"><?php echo number_format($team_user['total']); ?></span></td>
                                        <td class="text-center text-green"><b><?php echo number_format($team_user['received']); ?></b></td>
                                        <td class="text-center text-yellow"><?php echo number_format($team_user['pending']); ?></td>
                                        <td class="text-center text-red"><?php echo number_format($team_user['failed']); ?></td>
                                        <td>
                                            <?php foreach ($team_user['types'] as $type_id => $user_type) {
                                                $detail_url = URL::site('userreports/project_request_send_detail/?userid=' . $user_en . '&request_type=' . Helpers_Utilities::encrypted_key($type_id, 'encrypt') . '&project_id=' . $project_id_en . '&sdate=' . $sdate_get . '&edate=' . $edate_get);
                                                ?>
                                                <a href="<?php echo $detail_url; ?>" class="label label-primary" style="display:inline-block; margin:0 3px 3px 0; font-weight:normal;"
                                                   title="View <?php echo HTML::chars($user_type['name']); ?> requests of this user"><?php echo HTML::chars($user_type['name']); ?> <b>(<?php echo $user_type['total']; ?>)</b></a>
                                            <?php } ?>
                                        </td>
                                        <td><small><?php echo $first_day; ?><?php echo ($first_day != $last_day) ? ' to ' . $last_day : ''; ?></small></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box collapsed-box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i>User Wise Request Log of Project <u> <?php echo $project_name; ?></u></h3>
                        <div class="box-tools pull-right">
                            <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-xs"><i class="fa fa-file-excel-o"></i>  Export</a>
                            <button type="button" title="Show/Hide" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="projectrequestsend" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>User Name</th>
                                        <th class="no-sort">Designation</th>
                                        <th class="no-sort">Region</th>
                                        <th class="no-sort">Posting</th>                                        
                                        <th class="no-sort">Request Type</th>
                                        <th>Request Count</th>
                                        <th class="no-sort">View Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>User Name</th>
                                        <th>Designation</th>
                                        <th>region</th>
                                        <th>Posting</th>                                        
                                        <th>Request Type</th>
                                        <th>Request Count</th>
                                        <th>View Detail</th>
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
      
    function refreshGrid(){
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if($("#msg_to_show").val() != ""){
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }
    
 $(document).ready(function(){
        $.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
    if(oSettings.oFeatures.bServerSide === false){
        var before = oSettings._iDisplayStart;
        oSettings.oApi._fnReDraw(oSettings); 
        // iDisplayStart has been reset to zero - so lets change it back
        oSettings._iDisplayStart = before;
        oSettings.oApi._fnCalculateEnd(oSettings);
    }
      
    // draw the 'current' page
    oSettings.oApi._fnDraw(oSettings);
};
        objDT = $('#projectrequestsend').dataTable(
        {   "aaSorting": [[ 0, "desc" ]],    
            "bPaginate" : true,
            "bProcessing" : true,
            //"bStateSave": true,
            "bServerSide" : true,
            "sAjaxSource" : "<?php echo URL::site('userreports/ajaxprojectrequesttype',TRUE); ?>",
            "sPaginationType" : "full_numbers",
            "bFilter" : false,
            "bLengthChange" : true,
            "oLanguage": {
                "sProcessing": "Loading..."
              },
            "columnDefs": [ {
          "targets": 'no-sort',
          "orderable": false,
    } ]
        }
    );  
        $('.dataTables_empty').html("Information not found");
        $.fn.dataTable.ext.errMode = 'none';
        $('#userfavouritepersonlist').on('error.dt', function(e, settings, techNote, message) {
           swal("System Error", "Contact Technical Support Team.", "error");
        })
        
  });
   $("#search_form").validate({
        rules: {
            field: {
                check_list: true,
            },
            key: {
                key_value: true,
                required: true,
            },
        },
        messages: {
            field: {
                check_list: "Please select search type",
            },
            key: {
                key_value: "Enter Valid Search Value",
            },
        }
    });
    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "def") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("key_value", function (sel, element) {
        if ($('#searchfield').val() !== "") {
            return true;
        } else {
            return false;
        }
    }, "<span>Select One</span>");
         
function clearSearch(){
        window.location.href = '<?php echo URL::site('userreports/no_request_send',TRUE); ?>';
    }        
// request type summary: click a type row to show / hide its users
$(document).on('click', '.prt-type-row', function () {
    var type = $(this).data('type');
    $(this).toggleClass('open');
    $('.prt-users[data-type="' + type + '"]').toggle($(this).hasClass('open'));
});
function prtToggleAll(open) {
    $('.prt-type-row').toggleClass('open', open);
    $('.prt-users').toggle(open);
}
function excel(id){
            $('#xport').val('excel');
            $('#search_form').submit();            
            $('#xport').val('');
    }
</script>

