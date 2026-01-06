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
        <i class="fa fa-list"></i> IP Blocked List
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>	
        <li><a href="#"> Admin Reports</a></li>
        <li class="active">IP Blocked List</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">   
                <div class="table-responsive">
                    <table id="requeststatus" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <?php
                                $roles = Helpers_Utilities::get_roles_data();
                                foreach ($roles as $role) {
                                    ?>
                                    <th> <?php echo $role->label; ?></th>
                                    <?php } ?>                                    
                            </tr>
                        </thead>  
                        <tbody>
                            <?php
                            $menu_data = Helpers_Utilities::get_manu_data();
                            $roles = Helpers_Utilities::get_roles_data();                                                        
                            foreach ($menu_data as $menu) {
                                ?>
                                <tr>        
                                    <td><?php echo $menu->manu_name; ?></td>
                                    <?php
                                    foreach ($roles as $role) {
                                        $menu_id = !empty($menu->id) ? $menu->id : 0;
                                        $role_id = !empty($role->id) ? $role->id : 0;
                                        $status = Helpers_Utilities::chek_role_access($role_id,$menu_id);
                                        $checked = ($status == 1) ? "checked" : '';
                                        ?>
                                        <td class="text-center">
                                            <input class="menu_managment_checkbox" <?php echo $checked; ?> value="1" id="<?php echo $menu->id; ?>:<?php echo $role->id; ?>" name="<?php echo $menu->id; ?>:<?php echo $role->id; ?>" type="checkbox">                                            
                                        </td>
                                <?php } ?>
                                </tr>
                            <?php } ?>                                        
                        </tbody>
                    </table>
                </div>
            </div>
        </div>        
    </div>

</section>
<div class="modalwait"><!-- Place at bottom of page --></div>
<!-- /.content -->

<script>
    $(document).ready(function () {        
        $('.menu_managment_checkbox').change(function () {
            var id = $(this).attr('id');
            $("body").addClass("loading");
            $.ajax({
                url: "<?php echo URL::site("Adminreports/menu_update"); ?>",
                data: {cb_id: id},
                type: "POST",
                cache: false,
                dataType: 'html',
                success: function (msg) {
                $("body").removeClass("loading");
                }
            });            
           // alert(id);
        });
    });
</script>  
<script src=" <?php echo URL::base() . 'dist/js/bootstrap-toggle.min.js' ?>"></script>