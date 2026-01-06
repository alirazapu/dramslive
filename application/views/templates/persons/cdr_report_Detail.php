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
                        <li><a href="<?php echo URL::site('persons/dashboard/?id='.$_GET['id']); ?>">Person Dashboard </a></li>
                        <li><a href="<?php echo URL::site('persons/cdr_summary/?id='.$_GET['id']); ?>">CDR Summary </a></li>
                        <li>CDR Summary Detail</li>
                    </ol>
                </section>
<!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="">
                            <div class="box box-primary">

                                <form role="form" >


                                    <div class="box box-default collapsed-box">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Advance Search</h3>

                                            <div class="box-tools pull-right">
                                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                                            </div>
                                        </div>
                                        <!-- /.box-header -->
                                        <div class="box-body">

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Select Type</label>
                                                    <select class="form-control select2">                                                        
                                                        <option>Party A</option>
                                                        <option>Party B</option>
                                                        <option>Call Type</option>
                                                        <option>Call Duration</option>
                                                        <option>Date & Time</option>
                                                        <option>IMEI</option>
                                                        <option>IMSI</option>
                                                        <option>LOCATION</option> 
                                                    </select>
                                                </div>          
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="searchfield">Search Key</label>
                                                    <input type="text" class="form-control" id="searchfield" placeholder="Enter Text">
                                                </div>
                                                <div class="form-group pull-right">

                                                    <button type="submit" class="btn btn-primary">Search</button>

                                                </div>
                                            </div>
                                            <!-- /.col -->

                                            <!-- /.row -->
                                        </div>        
                                    </div>




                                </form>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">

                                <div class="box">
                                    <div class="box-header">
                                        <h3 class="box-title"><i class="fa fa-mobile-phone"></i> CDR Summary Detail</h3>
                                    </div>
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="table-responsive">
                                        <table id="example3" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>SR#</th>
                                                    <th>Party A</th>
                                                    <th>Party B</th>
                                                    <th>Call Type</th>
                                                    <th>Call Duration</th>
                                                    <th>Date & Time</th>
                                                    <th>IMEI</th>
                                                    <th>IMSI</th>
                                                    <th>LOCATION</th> 
                                                </tr>
                                            </thead>

                                            <tr>
                                                <td>1</td>
                                                <td>923007102069</td>
                                                <td>03007289011</td>
                                                <td>OUTGOING</td>
                                                <td>130</td>
                                                <td>2/25/2017 6:31:34</td>
                                                <td>35634206143238</td>
                                                <td>41001811821017</td>
                                                <td>Poloos Nagar, house no p-3</td>                                            
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>923007102069</td>
                                                <td>03007289011</td>
                                                <td>OUTGOING</td>
                                                <td>130</td>
                                                <td>2/25/2017 6:31:34</td>
                                                <td>35634206143238</td>
                                                <td>41001811821017</td>
                                                <td>Poloos Nagar, house no p-3</td>                                            
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>923007102069</td>
                                                <td>03007289011</td>
                                                <td>OUTGOING</td>
                                                <td>130</td>
                                                <td>2/25/2017 6:31:34</td>
                                                <td>35634206143238</td>
                                                <td>41001811821017</td>
                                                <td>kal shah kaku Lahore</td>                                            
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>923007102069</td>
                                                <td>03007289011</td>
                                                <td>OUTGOING</td>
                                                <td>130</td>
                                                <td>2/25/2017 6:31:34</td>
                                                <td>35634206143238</td>
                                                <td>41001811821017</td>
                                                <td>Lahore, Pakistan</td>                                            
                                            </tr>
                                            <tfoot>
                                                <tr>
                                                    <th>SR#</th>
                                                    <th>Party A</th>
                                                    <th>Party B</th>
                                                    <th>Call Type</th>
                                                    <th>Call Duration</th>
                                                    <th>Date & Time</th>
                                                    <th>IMEI</th>
                                                    <th>IMSI</th>
                                                    <th>LOCATION</th> 
                                                </tr>
                                            </tfoot>
                                        </table>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                                <!-- /.box -->
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->