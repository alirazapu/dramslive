<?php
$project_name = !empty($project['project_name']) ? $project['project_name'] : 'NA';
$creator = trim($project['creator_name']) != '' ? trim($project['creator_name']) . ' (' . $project['creator_username'] . ')' : 'NA';
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-users"></i> Project Members
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('intprojects'); ?>"> Projects</a></li>
        <li class="active">Members</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <?php if (isset($_GET['message']) && $_GET['message'] == 1) { ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Member assigned to project.</h4>
                </div>
            <?php } elseif (isset($_GET['message']) && $_GET['message'] == 2) { ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Member removed from project.</h4>
                </div>
            <?php } ?>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo HTML::chars($project_name); ?></h3>
                </div>
                <div class="box-body">
                    <p>
                        <b>Region:</b> <?php echo HTML::chars(!empty($project['region_name']) ? $project['region_name'] : 'NA'); ?>
                        &nbsp;|&nbsp; <b>Created By:</b> <?php echo HTML::chars($creator); ?>
                        &nbsp;|&nbsp; <b>Status:</b> <?php echo (isset($project['project_status']) && $project['project_status'] == 0) ? 'Open' : 'Close'; ?>
                    </p>
                    <p class="text-muted">The project creator and all assigned members can see this project and link requests to it.</p>

                    <form method="post" action="<?php echo URL::site('intprojects/member_add'); ?>" class="form-inline">
                        <?php echo Form::hidden('csrf', Security::token()); ?>
                        <input type="hidden" name="project" value="<?php echo HTML::chars($project_enc); ?>">
                        <div class="form-group" style="width: 60%;">
                            <select class="form-control" name="user_id" id="member_user_id" style="width: 100%;" required></select>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-user-plus"></i> Assign Member</button>
                    </form>
                </div>
            </div>

            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Assigned Members (<?php echo count($members); ?>)</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username / Role</th>
                                    <th>Designation</th>
                                    <th>Posting</th>
                                    <th>Assigned By</th>
                                    <th>Assigned At</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($members)) { ?>
                                    <tr><td colspan="7" class="text-center">No members assigned yet</td></tr>
                                <?php } ?>
                                <?php foreach ($members as $member) { ?>
                                    <tr>
                                        <td><?php echo HTML::chars(trim($member['first_name'] . ' ' . $member['last_name'])); ?></td>
                                        <td><b><?php echo HTML::chars($member['username']); ?></b><br><?php echo HTML::chars(Helpers_Utilities::get_user_role_name($member['user_id'])); ?></td>
                                        <td><?php echo HTML::chars(!empty($member['job_title']) ? $member['job_title'] : 'NA'); ?></td>
                                        <td><?php echo HTML::chars(Helpers_Profile::get_user_region_district($member['user_id'])); ?></td>
                                        <td><?php echo HTML::chars(trim($member['assigned_by_name'])); ?></td>
                                        <td><?php echo date('d-m-Y h:i A', strtotime($member['assigned_at'])); ?></td>
                                        <td>
                                            <form method="post" action="<?php echo URL::site('intprojects/member_remove'); ?>" onsubmit="return confirm('Remove this member from the project?');">
                                                <?php echo Form::hidden('csrf', Security::token()); ?>
                                                <input type="hidden" name="project" value="<?php echo HTML::chars($project_enc); ?>">
                                                <input type="hidden" name="user_id" value="<?php echo (int) $member['user_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(document).ready(function () {
        $('#member_user_id').select2({
            placeholder: 'Search user by name, username or designation',
            minimumInputLength: 2,
            ajax: {
                url: '<?php echo URL::site('intprojects/member_search'); ?>',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {q: params.term, project: '<?php echo HTML::chars($project_enc); ?>'};
                },
                processResults: function (data) {
                    return {results: data.results};
                }
            }
        });
    });
</script>
