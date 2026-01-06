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
        Person's Portal
        <small><?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li><a href="<?php echo URL::site('persons/dashboard/?id='.$_GET['id']); ?>">Person Dashboard</a></li>
                        <li class="active">CDR Report</li>
                    </ol>
                </section>
<!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">CDR Report</h3>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <div class="table-responsive">
                                    <table class="table table-hover">
                                        <tr>
                                            <th>ID</th>
                                            <th>Party A</th>
                                            <th>Party B</th>
                                            <th>SMS</th>
                                            <th>Call</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>923465824525</td>
                                            <td>923568515478</td>
                                            <td>150</td>
                                            <td>250</td>
                                            <td><a href="<?php echo URL::site('persons/cdr_report_Detail'); ?>">View Detail</a></td>
                                        </tr>  
                                        <tr>
                                            <td>2</td>
                                            <td>923465824525</td>
                                            <td>923568515684</td>
                                            <td>150</td>
                                            <td>250</td>
                                           <td><a href="<?php echo URL::site('persons/cdr_report_Detail'); ?>">View Detail</a></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>923465824525</td>
                                            <td>923568515684</td>
                                            <td>150</td>
                                            <td>250</td>
                                           <td><a href="<?php echo URL::site('persons/cdr_report_Detail'); ?>">View Detail</a></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>923465824525</td>
                                            <td>923568515684</td>
                                            <td>150</td>
                                            <td>250</td>
                                            <td><a href="<?php echo URL::site('persons/cdr_report_Detail'); ?>">View Detail</a></td>
                                        </tr>
                                    </table>
                                    </div>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                    </div>
                </section>
                <!-- /.content -->