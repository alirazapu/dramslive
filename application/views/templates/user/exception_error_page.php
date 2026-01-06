<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">

            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body no-padding">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="pad">
                                        <!-- Map will be created here -->
                                        <!--<div id="world-map-markers" style="height: 325px;"></div>-->                                        
                                        <img id="nodata" style=" " class="img-responsive" src="<?php echo URL::base() . 'dist/img/something_went_wrong.png'; ?>" alt="No Data">
                                    </div>                                                                                          
                                    <!--                                                                                        <div class="text-center box-footer">                                                                                                                                                                                                                         <b><a href="" ><i class="fa fa-arrow-circle-left"></i></a> 2016-09-19 <a href=""><i class="fa fa-arrow-circle-right"></i></a></b>                                                                                                                   </div>-->
                                </div>
                                <!-- /.col -->               
                            </div>
                            <!-- /.row -->
                        </div>
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
    </div>
    <div style="display:none" id="div-dialog-warning">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><div/></p>
    </div>
</section>
