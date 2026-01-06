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
                        <li class="active">Family Tree</li>
                    </ol>
                </section>
<!-- Main content -->
                <section class="content">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">Family Tree</h3>

<!--                                    <div class="box-tools">
                                        <div class="input-group input-group-sm" style="width: 150px;">
                                            <input type="text" name="table_search" class="form-control pull-right" placeholder="Search">

                                            <div class="input-group-btn">
                                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>-->
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Sub_No</th>
                                                <th>Person's Name</th>
                                                <th>Person's City</th>
                                                <th>CNIC</th>
                                                <th>Relation</th>
                                                <th>Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th scope="row">1</th>
                                                <td>923004562593</td>
                                                <td>Ali Hassan</td>
                                                <td>Lahore</td>
                                                <td>3525688652325</td>
                                                <td>Father</td>
                                                <td><a href="#">View Detail</a></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">2</th>
                                                <td>923004562593</td>
                                                <td>Ali Hassan</td>
                                                <td>Lahore</td>
                                                <td>3525688652325</td>
                                                <td>Father</td>
                                                <td><a href="#">View Detail</a></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">3</th>
                                                <td>923004562593</td>
                                                <td>Ali Hassan</td>
                                                <td>Lahore</td>
                                                <td>3525688652325</td>
                                                <td>Father</td>
                                                <td><a href="#">View Detail</a></td>
                                            </tr>
                                            <tr>
                                                <th scope="row">4</th>
                                                <td>923004562593</td>
                                                <td>Ali Hassan</td>
                                                <td>Lahore</td>
                                                <td>3525688652325</td>
                                                <td>Father</td>
                                                <td><a href="#">View Detail</a></td>
                                            </tr>
                                        </tbody>
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