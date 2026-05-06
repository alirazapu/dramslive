<?php defined('SYSPATH') or die('No direct script access.'); ?>
<?php
// Variables expected (set by Controller_Databank::action_ecp_family()):
//   $seed    — stdClass row from ecp_persons (the person whose family
//              we're showing) OR null if the id wasn't found
//   $family  — array of stdClass rows from ecp_persons sharing the
//              seed's (code, folder, file, uc_block) tuple, EXCLUDING
//              the seed itself. Empty array if none.
?>
<section class="content-header">
    <h1>
        <i class="fa fa-sitemap"></i>
        ECP Family Tree
        <?php if ($seed && !empty($seed->name_text)): ?>
            <small><?php echo HTML::chars($seed->name_text); ?></small>
        <?php endif; ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">DRAMS Databank</a></li>
        <li><a href="<?php echo URL::site('databank/ecp_advanced'); ?>">ECP Search</a></li>
        <li class="active">Family Tree</li>
    </ol>
</section>

<section class="content user_activity_log">
    <?php if (!$seed): ?>
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i>
            No ECP record found for that id. Go back to
            <a href="<?php echo URL::site('databank/ecp_advanced'); ?>">ECP Search</a>
            and try again.
        </div>
    <?php else: ?>

        <?php
            $seed_image = '';
            if (!empty($seed->address_image_base64)) {
                $seed_image = (strpos($seed->address_image_base64, 'data:image') === 0)
                    ? $seed->address_image_base64
                    : 'data:image/jpeg;base64,' . $seed->address_image_base64;
            }
        ?>

        <!-- Seed person -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-user"></i> Seed Person
                </h3>
                <div class="box-tools pull-right">
                    <small class="text-muted">ECP ID <?php echo (int) $seed->id; ?></small>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered" style="margin-bottom:0;">
                    <tr>
                        <th style="width:160px;">CNIC</th>
                        <td><?php echo HTML::chars($seed->cnic); ?></td>
                        <th style="width:160px;">Age / Gender</th>
                        <td>
                            <?php echo HTML::chars((string) $seed->age); ?>
                            <?php if (!empty($seed->gender)): ?>
                                · <?php echo HTML::chars((string) $seed->gender); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td><?php echo HTML::chars((string) $seed->name_text); ?></td>
                        <th>Father</th>
                        <td><?php echo HTML::chars((string) $seed->father_text); ?></td>
                    </tr>
                    <tr>
                        <th>Address (text)</th>
                        <td colspan="3"><?php echo HTML::chars((string) $seed->address_text); ?></td>
                    </tr>
                    <?php if ($seed_image !== ''): ?>
                    <tr>
                        <th>Address (image)</th>
                        <td colspan="3">
                            <img src="<?php echo HTML::chars($seed_image); ?>" alt=""
                                 style="max-width:380px; max-height:120px; border:1px solid #ddd;">
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Family Identity</th>
                        <td colspan="3" style="font-family:monospace; font-size:12px;">
                            code <strong><?php echo HTML::chars((string) $seed->code); ?></strong> ·
                            file <strong><?php echo HTML::chars((string) $seed->file_name); ?></strong> ·
                            folder <strong><?php echo HTML::chars((string) $seed->folder_name); ?></strong> ·
                            UC/Block <strong><?php echo HTML::chars((string) $seed->uc_block_code); ?></strong>
                            <?php if (!empty($seed->family_number)): ?>
                                · family # <strong><?php echo HTML::chars((string) $seed->family_number); ?></strong>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Family members -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-users"></i>
                    Family Members
                    <small class="text-muted" style="margin-left:8px;">
                        <?php echo (int) count($family); ?> other record<?php echo count($family) === 1 ? '' : 's'; ?>
                        share<?php echo count($family) === 1 ? 's' : ''; ?> this family identity
                    </small>
                </h3>
            </div>
            <div class="box-body">
                <?php if (empty($family)): ?>
                    <div class="text-muted text-center" style="padding:24px;">
                        <i class="fa fa-info-circle fa-lg"></i>
                        <div style="margin-top:6px;">
                            No other ecp_persons rows share this family identity tuple.
                        </div>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" style="width:100%;">
                            <thead>
                                <tr>
                                    <th style="width:90px;">ECP ID</th>
                                    <th style="width:130px;">CNIC</th>
                                    <th>Name</th>
                                    <th>Father</th>
                                    <th style="width:60px;">Age</th>
                                    <th style="width:70px;">Gender</th>
                                    <th>Phones</th>
                                    <th style="width:110px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($family as $f):
                                    $fid = isset($f->id) ? (int) $f->id : 0;
                                ?>
                                <tr>
                                    <td><?php echo (int) $fid; ?></td>
                                    <td><?php echo HTML::chars((string) $f->cnic); ?></td>
                                    <td><?php echo HTML::chars((string) $f->name_text); ?></td>
                                    <td><?php echo HTML::chars((string) $f->father_text); ?></td>
                                    <td><?php echo HTML::chars((string) $f->age); ?></td>
                                    <td><?php echo HTML::chars((string) $f->gender); ?></td>
                                    <td><?php echo HTML::chars((string) (isset($f->linked_numbers) ? $f->linked_numbers : '')); ?></td>
                                    <td>
                                        <?php if ($fid > 0): ?>
                                            <a href="<?php echo URL::site('databank/ecp_family') . '?ecp_id=' . $fid; ?>"
                                               class="btn btn-xs btn-default" title="Pivot family tree to this person">
                                                <i class="fa fa-sitemap"></i> Pivot
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
