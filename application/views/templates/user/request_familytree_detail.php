<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$file_details = !empty($results['request_id']) ? Helpers_Upload::get_file_info_with_request_id($results['request_id']) : '';

$login_user = Auth::instance()->get_user();
$permission = Helpers_Utilities::get_user_permission($login_user->id);
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-files-o"></i>
        Request Status
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('userrequest/request_status'); ?>">Request Status</a></li>
        <li class="active">Request Status Detail</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="">
                <?php
                if (isset($_GET["resend"]) && $_GET["resend"] == 1) {
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <?php echo 'successfull'; ?></h4>
                    </div>
                <?php } ?>
                <div class="box box-primary">
                    <div class="row request_status_detail">
                        <div id="request_status" style="">  
                            <?php
                            $DB = Database::instance();
                            $login_user = Auth::instance()->get_user();
                            $permission = Helpers_Utilities::get_user_permission($login_user->id);
                            $current_date = date("Y-m-d H:i:s");
                            //echo $current_date . '<br>';
                            $send_date = !empty($results['sending_date']) ? $results['sending_date'] : '';
                            $status = !empty($results['status']) ? $results['status'] : 0;
                            //echo $send_date . '<br>';
                            $difference = round((strtotime($current_date) - strtotime($send_date)) / 3600, 0);
                         //   $difference= !empty($difference) ? $difference : '';
                            //echo $difference;
                            //if (($results['user_request_type_id']!=10 && $difference >= 2 && ($results['status'] == 1)) || ($results['status'] == 3 || $permission == 1 || $permission == 2)) {
                            if ($difference >= 2 && ($status == 1 || $status == 3) && ($permission == 1 || $permission == 2)) {
                                $rqtid = Helpers_Utilities::encrypted_key($results['request_id'], 'encrypt');
                                ?>
                                <a style="" target="" href="<?php echo URL::base() . '/userrequest/request_resend?request_id=' . $rqtid ?>" class="btn btn-warning pull-right" >Request Resend</a>
                            <?php } ?>                                                             
                            <div class="col-md-12">
                                <h3 class="style14 col-md-12">Response Information</h3>
                                <hr class="style14 col-md-12">                                                                                
                                <div class="form-group col-md-12">
                                    <div style="" id="body">
                                        <label for="body">Received Body </label>
                                        <textarea id="body_txt" readonly="" value=""  name="body" class="textarea form-control" style="width: 100%; height: 800px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">
                                            <?php
                                            if (!empty($results['received_body_raw']) && $results['received_body_raw'] != 'na')
                                                echo str_replace("%20", " ", $results['received_body_raw']);
                                            elseif (!empty($results['received_body']) && $results['received_body'] != 'na')
                                                echo str_replace("%20", " ", $results['received_body']);
                                            else {
                                                echo '';
                                            }
                                            ?>
                                        </textarea>                                           
                                    </div>                                                                               
                                </div>                                        

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
</section>
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>
<script>
    $(function () {
        // Optional: If you still have any old WYSIHTML5 code commented out, you can remove it.
        // $(".textarea").wysihtml5();  // ← safe to delete if not used

        CKEDITOR.replace('body_txt', {
            versionCheck: false,  // This ensures it's disabled for this instance (extra safety)

            // If you want, you can move some/all of your toolbar/custom settings here instead of config.js
            // (but since you already have them in config.js, no need unless overriding per-instance)
            toolbarGroups: [
                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'forms' },
                { name: 'tools' },
                { name: 'document',       groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'others' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
                { name: 'styles' },
                { name: 'colors' },
                { name: 'about' }
            ],
            removeButtons: 'Underline,Subscript,Superscript',
            format_tags: 'p;h1;h2;h3;pre',
            removeDialogTabs: 'image:advanced;link:advanced'
            // Add any other per-instance overrides if needed
        });

        CKEDITOR.disableAutoInline = false;
    });
</script>
<style>
    #cke_1_top {
        display: none;
    }
</style>