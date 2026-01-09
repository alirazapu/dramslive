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
        Panel Log/History
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Panel Log/History</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userreports/panel_log'); ?>">
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                                <input id="xport" name="xport" type="hidden" value="" />
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="field">Select Type</label>
                                    <select class="form-control " name="field" id='field' onchange="showDiv(this)">
                                        <option value="def"> Please Select Type</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'name')) ? 'selected' : ''); ?> value="name">User Name</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'designation')) ? 'selected' : ''); ?>  value="designation"> Designation</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'usertype')) ? 'selected' : ''); ?>  value="usertype"> User Type</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'posting')) ? 'selected' : ''); ?> value="posting"> Posting</option>                                          
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'activity')) ? 'selected' : ''); ?> value="activity"> Activity</option> 

                                    </select>
                                </div>          
                            </div>
                            <div id="posting-hide">
                                <div class="col-md-6 posting_acl">
                                    <div class="form-group">
                                        <label for="posting">Select Posting</label>
                                        <select class="form-control select2" multiple="multiple" id="posting" name="posting[]" style="width: 100%;">
                                            <option value="">Please Select Posting</option>
                                            <optgroup label="Region">                                    
                                                <?php try{
                                                $region_list = Helpers_Utilities::get_region();
                                                foreach ($region_list as $list) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['posting']) && in_array('r-' . $list->region_id, $search_post['posting'])) ? 'Selected' : ''; ?> value="r-<?php echo $list->region_id ?>"><?php echo $list->name ?></option>
                                                <?php } 
                                                }  catch (Exception $ex){   }?>
                                            <optgroup label="District">                                    
                                                <?php try{
                                                $district_list = Helpers_Utilities::get_district();
                                                foreach ($district_list as $list) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['posting']) && in_array('d-' . $list->district_id, $search_post['posting'])) ? 'Selected' : ''; ?> value="d-<?php echo $list->district_id ?>"><?php echo $list->name ?></option>
                                                <?php } 
                                                }  catch (Exception $ex){   }?>
                                            <optgroup label="Police Station">                                    
                                                <?php try{
                                                $police_station_list = Helpers_Utilities::get_police_station();
                                                foreach ($police_station_list as $list) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['posting']) && in_array('p-' . $list->id, $search_post['posting'])) ? 'Selected' : ''; ?> value="p-<?php echo $list->id ?>"><?php echo $list->name ?></option>
                                                <?php }
                                                }  catch (Exception $ex){   }?>
                                            <optgroup label="Head Quarter">                                    
                                                <?php try{
                                                $headquarter_list = Helpers_Utilities::get_headquarter();
                                                foreach ($headquarter_list as $list) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['posting']) && in_array('h-' . $list->id, $search_post['posting'])) ? 'Selected' : ''; ?> value="h-<?php echo $list->id ?>"><?php echo $list->name ?></option>
                                                <?php } 
                                                }  catch (Exception $ex){   }?>                                                      
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="activity-hide">
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label for="posting">Select Activity</label>
                                        <select class="form-control select2" multiple="multiple" id="activity" name="activity[]" style="width: 100%;">
                                            <option value="">Please Select Activity</option>
                                            <optgroup label="Activities">                                    
                                                <?php try{
                                                $activity_list = Helpers_Utilities::get_user_activity_name();
                                                foreach ($activity_list as $list) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['activity']) && in_array($list->id, $search_post['activity'])) ? 'Selected' : ''; ?> value="<?php echo $list->id ?>"><?php echo $list->label ?></option>
                                                <?php } 
                                                }  catch (Exception $ex){   }?>                                                                                                 
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="usertype-hide">
                                <div class="col-md-6 ">
                                    <div class="form-group">
                                        <label for="posting">Select User Type</label>
                                        <select class="form-control select2" multiple="multiple" id="usertype" name="usertype[]" style="width: 100%;">
                                            <optgroup label="User Types">                                    
                                                <?php try{
                                                $usertype_list = Helpers_Utilities::get_user_type();
                                                foreach ($usertype_list as $usertype) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['usertype']) && in_array($usertype->id, $search_post['usertype'])) ? 'Selected' : ''; ?> value="<?php echo $usertype->id ?>"><?php echo $usertype->label ?></option>
                                                <?php } 
                                                }  catch (Exception $ex){   }?>                                                                                                 
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="designation-hide">
                                <div class="form-group col-md-6">
                                <label for="designation">Designation </label>
                                <select  class="form-control" id="designation" name="designation">                                   
                                    <option>Please Select Designation</option>
                                    <option <?php echo (!empty($search_post['designation']) && $search_post['designation']== 'Addl. IG')?"Selected":''; ?> value="Addl. IG">Addl. IG</option>
                                    <option <?php echo (!empty($search_post['designation']) && $search_post['designation']=='DIG')?"Selected":'DIG'; ?> value="DIG">DIG</option>
                                    <option <?php echo (!empty($search_post['designation']) && $search_post['designation']=='SSP')?"Selected":'SSP'; ?>  value="SSP">SSP</option>                                    
                                    <option <?php echo (!empty($search_post['designation']) && $search_post['designation']=='SP')?"Selected":'SP'; ?>  value="SP">SP</option>
                                    <option <?php echo (!empty($search_post['designation']) && $search_post['designation']=='DSP')?"Selected":'DSP'; ?> value="DSP">DSP</option>
                                    <option <?php echo (!empty($search_post['designation']) && $search_post['designation']=='Corporal')?"Selected":'Corporal'; ?>  value="Corporal">Corporal(Corp)</option>
                                    <option <?php echo (!empty($search_post['designation']) && $search_post['designation']=='Inspector')?"Selected":''; ?>  value="Inspector">Inspector (IP)</option>
                                    <option <?php echo (!empty($search_post['designation']) && $search_post['designation']=='Sub-Inspector')?"Selected":''; ?>  value="Sub-Inspector">Sub-Inspector (SI)</option>
                                    <option <?php echo (!empty($search_post['designation']) && $search_post['designation']=='Assistant Sub-Inspector')?"Selected":''; ?> value="Assistant Sub-Inspector">Assistant Sub-Inspector (ASI)</option>
                                    <option <?php echo (!empty($search_post['designation']) && $search_post['designation']=='Head Constable')?"Selected":''; ?>  value="Head Constable">Head Constable (HC)</option>
                                    <option <?php echo (!empty($search_post['designation']) && $search_post['designation']=='Constable')?"Selected":''; ?>  value="Constable">Constable (C)</option>
                                </select>
                            </div>
                            </div>
                            <!-- /.col -->
                            <div id="key-hide">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="searchfield">Search Key</label>
                                        <input type="text" class="form-control" id="searchfield" value="<?php echo (!empty($search_post['key']) ? $search_post['key'] : ''); ?>" name="key" placeholder="Enter Text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
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
                        <h3 class="box-title"><i class="fa fa-search"></i> Panel Log/History</h3>
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="penallog" class="table table-bordered table-striped" >
                                <thead>
                                    <tr>                                        
                                        <th class="no-sort">Username</th>
                                        <th class="no-sort">Designation</th>
                                        <th class="no-sort">User Type</th>
                                        <th class="no-sort">Posted In</th>
                                        <th class="no-sort">Region</th>
                                        <th class="no-sort">Activity</th>
                                        <th>Activity Time</th>
                                    </tr>
                                </thead>
                                <tbody>

                                <tfoot>
                                    <tr>                                        
                                        <th>Username</th>
                                        <th>Designation</th>
                                        <th>User Type</th>
                                        <th>Posted In</th>
                                        <th>Region</th>
                                        <th>Activity</th>
                                        <th>Activity Time</th>
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
<!--        acl right div-->
<div class="modal modal-info fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Search Person Details</h4>
            </div>
            <form class="" name="acl_form" action="<?php echo url::site() . 'userreports/access_control_form' ?>" id="acl_form" method="post">
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Key Name</th>
                                    <th>Key Value</th>                                  
                                </tr>
                            </thead>
                            <tbody id="acl_user_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>
            </form>         
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-project">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Project Added Details</h4>
            </div>
            <form class="" name="acl_form" action="<?php echo url::site() . 'userreports/access_control_form' ?>" id="acl_form" method="post">
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Project Name</th>
                                    <th>Region Name</th>                                                                                                       
                                </tr>
                            </thead>
                            <tbody id="project_data_body">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>
            </form>         
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-requestdata">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">User Request Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Request Type</th>
                                    <th>Request Company</th>
                                    <th>Request  Value</th>                                  
                                </tr>
                            </thead>
                            <tbody id="request_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-categorydetail">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Category Change Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Person Name</th>
                                    <th>Previous Category</th>
                                    <th>New Category</th> 
                                    
                                </tr>
                            </thead>
                            <tbody id="category_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-identitydelete">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Deleted Identities Detail</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Identity Name</th>
                                    <th>Identity Number</th>                                    
                                </tr>
                            </thead>
                            <tbody id="identitydelete_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!--Activity Detail-->
<div class="modal modal-info fade" id="modal-activitydetails">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Activity Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Key Name</th>
                                    <th>Key Value</th>                                    
                                </tr>
                            </thead>
                            <tbody id="activitydetails_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-educationdelete">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Deleted Education</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Education Type</th>
                                    <th>Degree Name</th>                                    
                                    <th>Completion year</th>                                    
                                    <th>Institute Name</th>                                    
                                </tr>
                            </thead>
                            <tbody id="educationdelete_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-incomesource">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Deleted Source of Income</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Source Name</th>
                                    <th>Source Details</th>                                    
                                    <th>File Name</th>                                                                        
                                </tr>
                            </thead>
                            <tbody id="incomesource_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-bankdetails">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Deleted Bank Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Account Number</th>
                                    <th>ATM Number</th>                                    
                                    <th>Bank Name</th>                                                                        
                                    <th>Branch Name</th>                                                                        
                                </tr>
                            </thead>
                            <tbody id="bankdetails_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-assets">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Deleted Assets Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Assets Name</th>                                                                        
                                    <th>Assets Details</th>                                                                        
                                    <th>Assets File</th>                                                                        
                                </tr>
                            </thead>
                            <tbody id="assets_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-criminalrecord">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Criminal Record Delete Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Fir No</th>                                                                        
                                    <th>Fir Date</th>                                                                        
                                    <th>Police Station</th>                                                                        
                                    <th>Sections Applied</th>                                                                        
                                    <th>Case position</th>                                                                        
                                    <th>Accused Position</th>                                                                        
                                </tr>
                            </thead>
                            <tbody id="criminalrecord_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-report">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Report Delete Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Report Type</th>                                                                        
                                    <th>Report Reference</th>                                                                        
                                    <th>Report Date</th>                                                                        
                                    <th>Report Brief</th>                                                                        
                                    <th>File Name</th>                                                                                                            
                                </tr>
                            </thead>
                            <tbody id="report_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal modal-info fade" id="modal-affiliation">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Affiliation Delete Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Linked Project</th>                                                                                                            
                                    <th>Organization</th>                                                                                                            
                                    <th>Designation</th>                                                                                                            
                                    <th>Details</th>                                                                                                            
                                    <th>Is Trained</th>                                                                                                            
                                    <th>Training Type</th>                                                                                                            
                                    <th>Training Duration</th>                                                                                                            
                                    <th>Training Year</th>                                                                                                            
                                </tr>
                            </thead>
                            <tbody id="affiliation_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal modal-info fade" id="modal-tagupdation">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Person Tags Updation Details</h4>
            </div>            
                <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                    <div class="form-group">
                        <table class="table table-striped">
                            <thead>
                                <tr>                                  
                                    <th>Old Tags</th>                                                                                                           
                                    <th>New Tags</th>                                                                                                           
                                </tr>
                            </thead>
                            <tbody id="tags_data">

                            </tbody>
                        </table>
                    </div>                            

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>                    
                </div>                     
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
        $("#usertype").select2({
            placeholder: "Please select one or Multiple User Type"   
        });
    });
</script>
<script type="text/javascript">
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        var elem = $('#field').val();
        if (elem == 'def')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
            //hide-show user type
            document.getElementById('usertype-hide').style.display = "none";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem == 'name')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
                        //hide-show user type
            document.getElementById('usertype-hide').style.display = "none";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } 
        else if (elem == 'designation')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "block";
                        //hide-show user type
            document.getElementById('usertype-hide').style.display = "none";
            //enable
            // $('#searchfield').removeAttr("disabled");
        } 
        else if (elem == 'usertype')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
            //hide-show user type
            document.getElementById('usertype-hide').style.display = "block";
            //enable
            // $('#searchfield').removeAttr("disabled");
        } 
        else if (elem == 'posting')
        {
            //show
            document.getElementById('posting-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
                        //hide-show user type
            document.getElementById('usertype-hide').style.display = "none";
        }
        else if (elem == 'activity')
        {
            //show
            document.getElementById('activity-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('posting-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
                        //hide-show user type
            document.getElementById('usertype-hide').style.display = "none";
        }
        $.fn.dataTableExt.oApi.fnStandingRedraw = function (oSettings) {
            if (oSettings.oFeatures.bServerSide === false) {
                var before = oSettings._iDisplayStart;
                oSettings.oApi._fnReDraw(oSettings);
                // iDisplayStart has been reset to zero - so lets change it back
                oSettings._iDisplayStart = before;
                oSettings.oApi._fnCalculateEnd(oSettings);
            }

            // draw the 'current' page
            oSettings.oApi._fnDraw(oSettings);
        };
        objDT = $('#penallog').dataTable(
                {"aaSorting": [[6, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
//                    "colReorder" : true,
                    "responsive": true,
                    "mark": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userreports/ajaxpanellog', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,                    
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By User Name:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");

    });
    $("#search_form").validate({
        rules: {
            field: {
                check_list: true,
            },
            key: {
                key_value: true,
                required: true,
            },
            "posting[]": {
                required: true,
            },
            "usertype[]": {
                required: true,
            },
            "activity[]": {
                required: true,
            },
        },
        messages: {
            field: {
                check_list: "Please select search type",
            },
            key: {
                key_value: "Enter Valid Search Value",
            },
           "posting[]": {
                required: "Select any option from list",
            },
           "activity[]": {
                required: "Select any option from list",
            },
        }
    });
    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "def") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("key_value", function (sel, element) {
        if ($('#searchfield').val() !== "") {
            return true;
        } else {
            return false;
        }
    }, "<span>Select One</span>");

    function showDiv(elem) {
        if (elem.value == 'def')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                        //hide
            document.getElementById('designation-hide').style.display = "none";
                        //hide-show user type
            document.getElementById('usertype-hide').style.display = "none";
        } else if (elem.value == 'name')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
                        //hide-show user type
            document.getElementById('usertype-hide').style.display = "none";
        } 
        else if (elem.value == 'designation' )
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
            //show
            document.getElementById('designation-hide').style.display = "block";
                        //hide-show user type
            document.getElementById('usertype-hide').style.display = "none";
        } 
        else if (elem.value == 'usertype' )
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
            //show
            document.getElementById('designation-hide').style.display = "none";
                        //hide-show user type
            document.getElementById('usertype-hide').style.display = "none";
                        //hide-show user type
            document.getElementById('usertype-hide').style.display = "block";
        } 
        else if (elem.value == 'posting')
        {
            //show
            document.getElementById('posting-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('activity-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
                        //hide-show user type
            document.getElementById('usertype-hide').style.display = "none";
        }
        else if (elem.value == 'activity')
        {
            //show
            document.getElementById('activity-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('posting-hide').style.display = "none";
                                    //hide
            document.getElementById('designation-hide').style.display = "none";
                        //hide-show user type
            document.getElementById('usertype-hide').style.display = "none";
        }
    }
    function clearSearch() {
        window.location.href = '<?php echo URL::site('userreports/panel_log', TRUE); ?>';
    }
    function searchdetail(time_line_id) {
        if (time_line_id !== 0) {
            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/searchpersondetails"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {        
                    if (response == 66) 
			{
			swal("System Error", "Contact Technical Support Team.", "error");
			}
                        else{
                    
                    $("#modal-default").modal("show");

                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');

                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);


                    $("#acl_user_data").html(response);
                }

                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function project_details(time_line_id) {
        if (time_line_id !== 0) {
            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/projectdetails"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-project").modal("show");

                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');

                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);


                    $("#project_data_body").html(response);

                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function categorydetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/categorychangedetails"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-categorydetail").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#category_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function identitydeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/identitydeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-identitydelete").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#identitydelete_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function activitydetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/activitydetails"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-activitydetails").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#activitydetails_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function educationdeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/educationdeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-educationdelete").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#educationdelete_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    //here
    function incomesourcedeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/incomesourcedeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-incomesource").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#incomesource_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function bankdetailsdeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/bankdetailsdeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-bankdetails").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#bankdetails_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function assetdeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/assetdeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-assets").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#assets_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function criminalrecorddeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/criminalrecorddeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-criminalrecord").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#criminalrecord_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function reportdeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/reportdeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-report").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#report_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                }
            });
        }
    }
    function affiliationdeletedetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/affiliationdeletedetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-affiliation").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#affiliation_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                }
            });
        }
    }
    function tagupdationdetail(time_line_id) {
        if (time_line_id !== 0) {            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/tagupdationdetail"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {
                    if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    $("#modal-tagupdation").modal("show");
                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');
                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    $("#tags_data").html(response);
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                }
            });
        }
    }
    function requestdetail(time_line_id) {
        if (time_line_id !== 0) {
            
            var request = $.ajax({
                url: "<?php echo URL::site("userreports/requestdetails"); ?>",
                type: "POST",
                dataType: 'html',
                data: {id: time_line_id},
                success: function (response)
                {                    
                  if (response == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}  
                    $("#modal-requestdata").modal("show");

                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');

                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);


                    $("#request_data").html(response);

                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
</script>