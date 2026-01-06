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
        CDR Upload Format
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">CDR Upload Format</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-body" style=" height: 800px">
                        <?php
                        
                            $file_name = 'cdrformat.pdf';
                    
                        //print_r($file_name); exit;
                        ?>
                        
                        <iframe frameborder="0" width="100%" height="100%" src="<?php echo URL::base(); ?>dist/user_manual/<?php echo $file_name;?>" alt="User Manual"> </iframe>
                    </div>
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
</section>

<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>
<script type="text/javascript">
  
</script>