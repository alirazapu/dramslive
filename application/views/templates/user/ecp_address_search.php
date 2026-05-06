<?php defined('SYSPATH') or die('No direct script access.'); ?>
<div class="col-md-12">
    <p class="text-muted">
        <strong><?php echo (int) count($rows); ?></strong>
        match<?php echo count($rows) === 1 ? '' : 'es'; ?>
        for <em><?php echo HTML::chars($q); ?></em>
        <?php if (count($rows) >= 100): ?>
            <span class="text-warning">(showing first 100 — refine your query for fewer results)</span>
        <?php endif; ?>
    </p>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th style="width:140px">CNIC</th>
                <th>Name</th>
                <th>Father</th>
                <th>Address (text)</th>
                <th style="width:320px">Address (image)</th>
                <th style="width:60px"></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <?php
                // Normalise the image to a data-URI if not already one
                $img = '';
                if (!empty($r->address_image_base64)) {
                    $img = (strpos($r->address_image_base64, 'data:image') === 0)
                        ? $r->address_image_base64
                        : 'data:image/jpeg;base64,' . $r->address_image_base64;
                }
                $cnic_enc = !empty($r->cnic) ? Helpers_Utilities::encrypted_key($r->cnic, 'encrypt') : '';
            ?>
            <tr>
                <td><?php echo HTML::chars((string) $r->cnic); ?></td>
                <td><?php echo HTML::chars((string) $r->name_text); ?></td>
                <td><?php echo HTML::chars((string) $r->father_text); ?></td>
                <td><?php echo HTML::chars((string) $r->address_text); ?></td>
                <td>
                    <?php if ($img !== ''): ?>
                        <img src="<?php echo $img; ?>" alt="address"
                             style="max-width:300px; max-height:120px; border:1px solid #ddd;">
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?php if ($cnic_enc !== ''): ?>
                        <a href="<?php echo URL::base() . 'persons/ext_db_ecp?cnic=' . urlencode($r->cnic); ?>"
                           class="btn btn-xs btn-primary" target="_blank" title="View full ECP record">
                            <i class="fa fa-external-link"></i>
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
