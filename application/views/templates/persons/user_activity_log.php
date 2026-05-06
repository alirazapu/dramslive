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
        <i class="fa fa-odnoklassniki"></i>
        Panel Log/History
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id='.$_GET['id']); ?>">Person Dashboard </a></li>                        
        <li class="active">User Activity Log</li>
    </ol>
</section>
<!-- Main content -->
<section class="content user_activity_log">
    <div class="container-fluid">
        <?php
        // -----------------------------------------------------------------
        // Combined "Activity & Requests" panel.
        //
        // Renders both of the page's data tables inside a SINGLE box with
        // two tabs. Replaces the previous two-box layout (with a separate
        // attachment gallery), per operator request: gallery removed,
        // tabs only.
        //
        // Tab 1 — User Activity Log (default): the existing #activitylog
        //         DataTable, server-side via /persons/ajaxuseractivitylog.
        // Tab 2 — Requests & Attachments: every user_request and
        //         admin_request row tied to this person, with the
        //         attachment rendered inline as a thumbnail (images) or
        //         a download link (other types). Clicking an image
        //         thumbnail opens the lightbox modal at the bottom of
        //         the file.
        //
        // Request data is provided by Helpers_Person::get_person_requests()
        // through the include at persons_functions/user_activity_log.inc.
        // -----------------------------------------------------------------
        $req_rows = isset($person_requests) && is_array($person_requests) ? $person_requests : array();

        // Static lookups — kept inline to avoid threading another helper
        // dependency. Source of truth is the column comments on
        // user_request and the company list rendered on the request forms.
        $company_map = array(
            1  => 'Mobilink/Jazz',
            2  => 'Warid',
            3  => 'Ufone',
            4  => 'Zong',
            5  => 'SCOM',
            6  => 'Telenor',
            7  => 'Warid',
            9  => 'PTCL Mobile',
            11 => 'PTCL',
            12 => 'International',
            13 => 'NADRA',
            14 => 'Travel',
        );
        $status_map = array(
            0 => array('Not Sent',       'default'),
            1 => array('Sent',           'info'),
            2 => array('Email Received', 'primary'),
            3 => array('Send Error',     'danger'),
            4 => array('Rejected',       'warning'),
        );
        $proc_map = array(
            0 => 'Waiting Response',
            1 => 'Email Format Error',
            2 => 'No Data Found',
            3 => 'Parsing Error',
            4 => 'Waiting for Parsing',
            5 => 'Parsing Completed',
            6 => 'Partially Parsed',
            7 => 'Marked Completed',
        );

        // Counter for the requests-tab badge.
        $attachment_count = 0;
        foreach ($req_rows as $r) {
            if (!empty($r['file_name'])) $attachment_count++;
        }

        $img_exts = array('jpg', 'jpeg', 'png', 'gif');
        ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-history"></i>
                            Person Activity &amp; Requests
                        </h3>
                        <div class="box-tools pull-right">
                            <small class="text-muted">
                                <?php echo (int) count($req_rows); ?> request<?php echo count($req_rows) === 1 ? '' : 's'; ?>
                                · <?php echo (int) $attachment_count; ?> attachment<?php echo $attachment_count === 1 ? '' : 's'; ?>
                            </small>
                        </div>
                    </div>
                    <div class="box-body">
                        <ul class="nav nav-tabs" id="ualTabs" role="tablist">
                            <li class="active">
                                <a href="#ual_tab_activity" data-toggle="tab" role="tab">
                                    <i class="fa fa-search"></i> User Activity Log
                                </a>
                            </li>
                            <li>
                                <a href="#ual_tab_requests" data-toggle="tab" role="tab">
                                    <i class="fa fa-folder-open"></i> Requests &amp; Attachments
                                    <?php if (count($req_rows) > 0): ?>
                                        <span class="badge" style="background:#3c8dbc; margin-left:4px;"><?php echo (int) count($req_rows); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" style="padding-top:14px;">

                            <!-- ============ TAB 1: USER ACTIVITY LOG ============ -->
                            <div class="tab-pane active" id="ual_tab_activity">
                                <div class="table-responsive">
                                    <table id="activitylog" class="table table-bordered table-striped" style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th class="no-sort">Username</th>
                                                <th class="no-sort">Designation</th>
                                                <th class="no-sort">User Type</th>
                                                <th class="no-sort">Posted In</th>
                                                <th class="no-sort">Region</th>
                                                <th class="no-sort">Activity</th>
                                                <th>Activity Time</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Username</th>
                                                <th>Designation</th>
                                                <th>User Type</th>
                                                <th>Posted In</th>
                                                <th>Region</th>
                                                <th>Activity</th>
                                                <th>Activity Time</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- ============ TAB 2: REQUESTS & ATTACHMENTS ============ -->
                            <div class="tab-pane" id="ual_tab_requests">
                                <?php if (empty($req_rows)) { ?>
                                    <div class="text-muted text-center" style="padding:24px;">
                                        <i class="fa fa-info-circle fa-lg"></i>
                                        <div style="margin-top:6px;">
                                            No user_request or admin_request rows are linked to this person.
                                        </div>
                                    </div>
                                <?php } else { ?>
                                <div class="table-responsive">
                                    <table id="reqInfoTable" class="table table-bordered table-hover" style="font-size:12.5px;">
                                        <thead style="background:#f5f7fa;">
                                            <tr>
                                                <th style="width:120px;">Date</th>
                                                <th style="width:70px;">Source</th>
                                                <th style="width:90px;">Ref #</th>
                                                <th>Type</th>
                                                <th style="width:110px;">Telco</th>
                                                <th>Requested Value</th>
                                                <th style="width:130px;">Status</th>
                                                <th>Reason</th>
                                                <th style="width:80px;">Attachment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($req_rows as $r) {
                                            $rid     = isset($r['request_id']) ? (int) $r['request_id'] : 0;
                                            $src     = isset($r['source']) ? $r['source'] : 'user';
                                            $rid_enc = $rid ? Helpers_Utilities::encrypted_key($rid, 'encrypt') : '';
                                            $type_label = !empty($r['request_type_name']) ? $r['request_type_name'] : ('#' . (int) (isset($r['user_request_type_id']) ? $r['user_request_type_id'] : 0));
                                            $cn = isset($r['company_name']) ? (int) $r['company_name'] : 0;
                                            $company_label = isset($company_map[$cn]) ? $company_map[$cn] : ($cn ? 'MNC ' . $cn : '-');
                                            $st = isset($r['status']) ? (int) $r['status'] : 0;
                                            $st_pair = isset($status_map[$st]) ? $status_map[$st] : array('Unknown', 'default');
                                            $proc = isset($r['processing_index']) ? (int) $r['processing_index'] : 0;
                                            $proc_label = isset($proc_map[$proc]) ? $proc_map[$proc] : '';
                                            $reason = !empty($r['reason']) ? $r['reason'] : '';
                                            $reason_short = mb_strlen($reason) > 80 ? (mb_substr($reason, 0, 78) . '…') : $reason;

                                            $att_cell = '<span class="text-muted">—</span>';
                                            if (!empty($r['file_name'])) {
                                                $ext = strtolower(pathinfo($r['file_name'], PATHINFO_EXTENSION));
                                                $is_img = in_array($ext, $img_exts, true);
                                                $url = URL::site('persons/attachment') . '?s=' . urlencode($src) . '&r=' . urlencode($rid_enc);
                                                if ($is_img) {
                                                    $att_cell = '<a href="' . HTML::chars($url) . '" data-rqt-zoom="1" title="View attachment">'
                                                              . '<img src="' . HTML::chars($url) . '" alt="attachment"'
                                                              . ' style="max-height:42px; max-width:64px; border:1px solid #ccc; padding:2px; background:#fff; border-radius:3px;"></a>';
                                                } else {
                                                    $att_cell = '<a href="' . HTML::chars($url) . '" target="_blank" title="Download ' . HTML::chars(basename($r['file_name'])) . '">'
                                                              . '<i class="fa fa-file-' . HTML::chars($ext) . '-o fa-lg"></i></a>';
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo HTML::chars(!empty($r['created_at']) ? date('Y-m-d H:i', strtotime($r['created_at'])) : '-'); ?></td>
                                                <td>
                                                    <?php if ($src === 'admin') { ?>
                                                        <span class="label label-warning" title="admin_request">Admin</span>
                                                    <?php } else { ?>
                                                        <span class="label label-info" title="user_request">User</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo HTML::chars(!empty($r['reference_id']) ? $r['reference_id'] : '-'); ?></td>
                                                <td><?php echo HTML::chars($type_label); ?></td>
                                                <td><?php echo HTML::chars($company_label); ?></td>
                                                <td><?php echo HTML::chars(!empty($r['requested_value']) ? $r['requested_value'] : '-'); ?></td>
                                                <td>
                                                    <span class="label label-<?php echo HTML::chars($st_pair[1]); ?>"><?php echo HTML::chars($st_pair[0]); ?></span>
                                                    <?php if ($proc_label !== '' && $st === 2) { ?>
                                                        <small class="text-muted" style="display:block; margin-top:2px;"><?php echo HTML::chars($proc_label); ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td title="<?php echo HTML::chars($reason); ?>" style="max-width:260px; word-break:break-word;">
                                                    <?php echo $reason !== '' ? HTML::chars($reason_short) : '<span class="text-muted">—</span>'; ?>
                                                </td>
                                                <td class="text-center"><?php echo $att_cell; /* HTML pre-built and HTML::chars-applied above */ ?></td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php } ?>
                            </div>

                        </div><!-- /.tab-content -->
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div>
        </div>

        <!-- Lightbox modal for image attachments. Triggered by any
             [data-rqt-zoom="1"] anchor in the requests tab. The
             backdrop-cleanup pattern matches the other modals on this
             page (.modal-backdrop is forcibly removed on hide so a
             stale overlay can never block clicks). -->
        <div class="modal fade" id="rqt-zoom-modal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-picture-o"></i> Attachment Preview</h4>
                    </div>
                    <div class="modal-body text-center" style="background:#222; padding:6px;">
                        <img id="rqt-zoom-img" src="" alt="" style="max-width:100%; max-height:75vh;">
                    </div>
                    <div class="modal-footer" style="background:#f5f5f5;">
                        <a id="rqt-zoom-link" href="#" target="_blank" class="btn btn-default btn-sm pull-left">
                            <i class="fa fa-external-link"></i> Open in new tab
                        </a>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->
<!--        acl right div-->
<div class="modal modal-info fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Search Person Details</h4>
            </div>
            <form class="" name="acl_form" action="<?php echo url::site() . 'userreports/access_control_form' ?>" id="acl_form" method="post">
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Key Name</th>
                                    <th>Key Value</th>                                  
                                </tr>
                            </thead>
                            <tbody id="acl_user_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>
            </form>         
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-project">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Project Added Details</h4>
            </div>
            <form class="" name="acl_form" action="<?php echo url::site() . 'userreports/access_control_form' ?>" id="acl_form" method="post">
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Project Name</th>
                                    <th>Region Name</th>                                                                                                       
                                </tr>
                            </thead>
                            <tbody id="project_data_body">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>
            </form>         
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-requestdata">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">User Request Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Request Type</th>
                                    <th>Request Company</th>
                                    <th>Request  Value</th>                                  
                                </tr>
                            </thead>
                            <tbody id="request_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-categorydetail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Category Change Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Person Name</th>
                                    <th>Previous Category</th>
                                    <th>New Category</th> 
                                    
                                </tr>
                            </thead>
                            <tbody id="category_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-identitydelete">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Deleted Identities Detail</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Identity Name</th>
                                    <th>Identity Number</th>                                    
                                </tr>
                            </thead>
                            <tbody id="identitydelete_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-educationdelete">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Deleted Education</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Education Type</th>
                                    <th>Degree Name</th>                                    
                                    <th>Completion year</th>                                    
                                    <th>Institute Name</th>                                    
                                </tr>
                            </thead>
                            <tbody id="educationdelete_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-incomesource">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Deleted Source of Income</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Source Name</th>
                                    <th>Source Details</th>                                    
                                    <th>File Name</th>                                                                        
                                </tr>
                            </thead>
                            <tbody id="incomesource_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-bankdetails">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Deleted Bank Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Account Number</th>
                                    <th>ATM Number</th>                                    
                                    <th>Bank Name</th>                                                                        
                                    <th>Branch Name</th>                                                                        
                                </tr>
                            </thead>
                            <tbody id="bankdetails_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-assets">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Deleted Assets Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Assets Name</th>                                                                        
                                    <th>Assets Details</th>                                                                        
                                    <th>Assets File</th>                                                                        
                                </tr>
                            </thead>
                            <tbody id="assets_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-criminalrecord">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Criminal Record Delete Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Fir No</th>                                                                        
                                    <th>Fir Date</th>                                                                        
                                    <th>Police Station</th>                                                                        
                                    <th>Sections Applied</th>                                                                        
                                    <th>Case position</th>                                                                        
                                    <th>Accused Position</th>                                                                        
                                </tr>
                            </thead>
                            <tbody id="criminalrecord_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-report">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Report Delete Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Report Type</th>                                                                        
                                    <th>Report Reference</th>                                                                        
                                    <th>Report Date</th>                                                                        
                                    <th>Report Brief</th>                                                                        
                                    <th>File Name</th>                                                                                                            
                                </tr>
                            </thead>
                            <tbody id="report_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-affiliation">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Affiliation Delete Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Linked Project</th>                                                                                                            
                                    <th>Organization</th>                                                                                                            
                                    <th>Designation</th>                                                                                                            
                                    <th>Details</th>                                                                                                            
                                    <th>Is Trained</th>                                                                                                            
                                    <th>Training Type</th>                                                                                                            
                                    <th>Training Duration</th>                                                                                                            
                                    <th>Training Year</th>                                                                                                            
                                </tr>
                            </thead>
                            <tbody id="affiliation_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!--Activity Detail-->
<div class="modal modal-info fade" id="modal-activitydetails">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Activity Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Key Name</th>
                                    <th>Key Value</th>                                    
                                </tr>
                            </thead>
                            <tbody id="activitydetails_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-tagupdation">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Tags Updation Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Old Tags</th>                                                                                                           
                                    <th>New Tags</th>                                                                                                           
                                </tr>
                            </thead>
                            <tbody id="tags_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

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
        var elem = $('#field').val();
        if (elem == 'def')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem == 'name')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem == 'designation')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "block";
            //enable
            // $('#searchfield').removeAttr("disabled");
        } 
        else if (elem == 'posting')
        {
            //show
            document.getElementById('posting-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
        }
        else if (elem == 'activity')
        {
            //show
            document.getElementById('activity-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('posting-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
        }
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
        objDT = $('#activitylog').dataTable(
                {"aaSorting": [[6, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('persons/ajaxuseractivitylog', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Value:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

        // Lightbox-style preview for image attachments in the Requests
        // tab. Delegated off document so it works with rows that may
        // be re-rendered (e.g. if the future requests table becomes a
        // DataTable too). Ctrl/Cmd/Shift/middle-click bypass the
        // lightbox so users can still open the image in a new tab.
        $(document).on('click', 'a[data-rqt-zoom="1"]', function (e) {
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.which === 2) return;
            e.preventDefault();
            var src = $(this).attr('href');
            $('#rqt-zoom-img').attr('src', src);
            $('#rqt-zoom-link').attr('href', src);
            $('#rqt-zoom-modal').modal('show');
        });

        // Robust backdrop / body-state cleanup. The page hosts ~14
        // other Bootstrap modals using a custom .blue-container hack
        // (search the file for "appendTo('.blue')"). When the lightbox
        // is opened/closed against that backdrop machinery, Bootstrap
        // sometimes leaves a stale .modal-backdrop attached to <body>
        // which blocks every click on the page until reload. The
        // listener below — fired on both shown and hidden — deletes
        // any backdrop element that doesn't have a visible modal
        // associated with it, and undoes the body-state classes so
        // the page returns to a clickable state in every case.
        $('#rqt-zoom-modal').on('shown.bs.modal hidden.bs.modal', function (e) {
            // Free the image src on hide to keep memory tidy when an
            // analyst clicks through many thumbnails in a session.
            if (e && e.type === 'hidden') {
                $('#rqt-zoom-img').attr('src', '');
                $('#rqt-zoom-link').attr('href', '#');
            }
            setTimeout(function () {
                if ($('.modal:visible').length === 0) {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open').css('padding-right', '');
                }
            }, 250);
        });
        // Belt-and-suspenders: clicking the dimmed backdrop should
        // always dismiss the lightbox, even if Bootstrap got into a
        // weird state.
        $(document).on('click', '.modal-backdrop', function () {
            if ($('#rqt-zoom-modal').hasClass('in')) {
                $('#rqt-zoom-modal').modal('hide');
            }
        });

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
            "posting[]": {
                required: true,
            },
            "activity[]": {
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
           "posting[]": {
                required: "Select any option from list",
            },
           "activity[]": {
                required: "Select any option from list",
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

    function showDiv(elem) {
        if (elem.value == 'def')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                        //hide
            document.getElementById('designation-hide').style.display = "none";
        } else if (elem.value == 'name')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
        } else if (elem.value == 'designation' )
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
            //show
            document.getElementById('designation-hide').style.display = "block";
        } 
        else if (elem.value == 'posting')
        {
            //show
            document.getElementById('posting-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
        }
        else if (elem.value == 'activity')
        {
            //show
            document.getElementById('activity-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('posting-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
        }
    }
    function clearSearch() {
        window.location.href = '<?php echo URL::site('userreports/panel_log', TRUE); ?>';
    }
    function searchdetail(time_line_id) {
        if (time_line_id !== 0) {
            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/searchpersondetails"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {    
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    
                    $("#modal-default").modal("show");

                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');

                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);


                    $("#acl_user_data").html(response);

                },
                error: function (jqXHR, textStatus) {
                   swal("System Error", "Contact Support Team.", "error");
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function project_details(time_line_id) {
        if (time_line_id !== 0) {
            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/projectdetails"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-project").modal("show");

                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');

                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);


                    $("#project_data_body").html(response);

                },
                error: function (jqXHR, textStatus) {
                    swal("System Error", "Contact Support Team.", "error");
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function categorydetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/categorychangedetails"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-categorydetail").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#category_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    swal("System Error", "Contact Support Team.", "error");
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function identitydeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/identitydeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-identitydelete").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#identitydelete_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                swal("System Error", "Contact Support Team.", "error");
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function educationdeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/educationdeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-educationdelete").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#educationdelete_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    swal("System Error", "Contact Support Team.", "error");
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    //here
    function incomesourcedeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/incomesourcedeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-incomesource").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#incomesource_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    swal("System Error", "Contact Support Team.", "error");
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function bankdetailsdeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/bankdetailsdeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-bankdetails").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#bankdetails_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    swal("System Error", "Contact Support Team.", "error");
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function assetdeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/assetdeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-assets").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#assets_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    swal("System Error", "Contact Support Team.", "error");
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function criminalrecorddeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/criminalrecorddeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-criminalrecord").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#criminalrecord_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                  swal("System Error", "Contact Support Team.", "error");
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function reportdeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/reportdeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-report").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#report_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                 swal("System Error", "Contact Support Team.", "error");
                }
            });
        }
    }
    function affiliationdeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/affiliationdeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-affiliation").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#affiliation_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                   swal("System Error", "Contact Support Team.", "error");
                }
            });
        }
    }
    function tagupdationdetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/tagupdationdetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-tagupdation").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#tags_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                  swal("System Error", "Contact Support Team.", "error");
                }
            });
        }
    }
    function requestdetail(time_line_id) {
        if (time_line_id !== 0) {
            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/requestdetails"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {        
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    
                    $("#modal-requestdata").modal("show");

                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');

                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);


                    $("#request_data").html(response);

                },
                error: function (jqXHR, textStatus) {
                   swal("System Error", "Contact Support Team.", "error");
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function activitydetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/activitydetails"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-activitydetails").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#activitydetails_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                   swal("System Error", "Contact Support Team.", "error");
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
</script>