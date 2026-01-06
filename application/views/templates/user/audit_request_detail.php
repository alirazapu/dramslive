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
		<i class="fa fa-user" ></i>
		User's Report
		<small>Tracer</small>
	</h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('user/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#"> User's Report</a></li>
        <li ><a href="<?php echo URL::site('user/audit_report'); ?>"> Audit Report</a> </li>
        <li class="active">Audit Request detail</li>
      </ol>
    </section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="box box-primary">

            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-search"></i> Request Status</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Company</th>
                                    <th>Request Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>View Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>183</td>
                                    <td>ABC</td>
                                    <td>Mobilink</td>
                                    <td>CDR Against Number</td>
                                    <td>11-7-2014</td>
                                    <td><span class="label label-success">Successful</span></td>
                                    <td><a href="<?php echo URL::site('user/request_status_detail'); ?>">View Detail</a></td>
                                </tr>
                                <tr>
                                    <td>219</td>
                                    <td>XYZ</td>
                                    <td>Mobilink, Ufone</td>
                                    <td>CDR Against Number</td>
                                    <td>11-7-2014</td>
                                    <td><span class="label label-danger">Parsing Error</span></td>
                                    <td><a href="<?php echo URL::site('user/request_status_detail'); ?>">View Detail</a></td>
                                </tr>  
                                <tr>
                                    <td>320</td>
                                    <td>TUY</td>
                                    <td>All</td>
                                    <td>SIMs against CNIC</td>
                                    <td>11-7-2014</td>
                                    <td><span class="label label-primary">Send</span></td>
                                    <td><a href="<?php echo URL::site('user/request_status_detail'); ?>">View Detail</a></td>
                                </tr>
                                <tr>
                                    <td>450</td>
                                    <td>GHJ</td>
                                    <td>Mobilink, Ufone</td>
                                    <td>CDR Against IMSI</td>
                                    <td>11-7-2014</td>
                                    <td><span class="label label-warning">Parsing</span></td>
                                    <td><a href="<?php echo URL::site('user/request_status_detail'); ?>">View Detail</a></td>
                                </tr>
                                <tr>
                                    <td>183</td>
                                    <td>ABC</td>
                                    <td>Mobilink, Ufone</td>
                                    <td>CDR Against Number</td>
                                    <td>11-7-2014</td>
                                    <td><span class="label label-success">Successful</span></td>
                                    <td><a href="<?php echo URL::site('user/request_status_detail'); ?>">View Detail</a></td>
                                </tr>
                                <tr>
                                    <td>219</td>
                                    <td>XYZ</td>
                                    <td>Mobilink, Ufone</td>
                                    <td>CDR Against Number</td>
                                    <td>11-7-2014</td>
                                    <td><span class="label label-danger">Parsing Error</span></td>
                                    <td><a href="<?php echo URL::site('user/request_status_detail'); ?>">View Detail</a></td>
                                </tr>  
                                <tr>
                                    <td>320</td>
                                    <td>TUY</td>
                                    <td>Mobilink, Ufone</td>
                                    <td>SIMs against CNIC</td>
                                    <td>11-7-2014</td>
                                    <td><span class="label label-primary">Send</span></td>
                                    <td><a href="<?php echo URL::site('user/request_status_detail'); ?>">View Detail</a></td>
                                </tr>
                                <tr>
                                    <td>450</td>
                                    <td>GHJ</td>
                                    <td>Mobilink, Ufone</td>
                                    <td>CDR Against IMSI</td>
                                    <td>11-7-2014</td>
                                    <td><span class="label label-warning">Parsing</span></td>
                                    <td><a href="<?php echo URL::site('user/request_status_detail'); ?>">View Detail</a></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Company</th>
                                    <th>Request Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>View Detail</th>
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

</section>
<!-- /.content -->