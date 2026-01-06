<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>	<i class="fa fa-envelope" aria-hidden="true"></i>
        Add Project
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#"></i> Projects</a></li>
        <li class="active"></i>Add Project</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="">
                <div class="box box-primary">  
                        <?php
                        if (isset($_GET["message"]) && $_GET["message"] == 1) {
                            ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> Congratulation! Project Is Successfully Added.</h4>
                        </div>
                                    <?php } ?>
                                    <?php
                                    if (isset($_GET["message"]) && $_GET["message"] == 2) {
                                        ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> Congratulation! Project Is Successfully Updated.</h4>
                        </div>
                                <?php } ?>
                    <form class="" name="addprojectform" action="<?php echo url::site() . 'intprojects/post' ?>" id="addprojectform" method="post">
                        <div class="box-body">
                                    <?php if (!empty($records)) { ?>
                                        <?php           try{                              
                                        foreach ($records as $record) {                                            
                                            $projectid = !empty($record['id']) ? $record['id'] : 0;                                            
                                            $pname = !empty($record['project_name']) ? $record['project_name'] : "NA";
                                            $pdetails = !empty($record['details']) ? $record['details'] : "NA";
                                            $region_id = !empty($record['region_id']) ? $record['region_id'] : "NA";
                                            $district_id = !empty($record['district_id']) ? $record['district_id'] : "NA";
                                            $project_status = !empty($record['project_status']) ? $record['project_status'] : 0;
                                            //to get organization of project 
                                            $organizations = Helpers_Utilities::get_project_organizations($projectid);
                                            //echo '<pre>';print_r($organizations); exit;
                                        }
                                        }  catch (Exception $ex){   
                                            
                                        }
                                    } else {
                                        $projectid = '';
                                        $pname = '';
                                        $pdetails = '';
                                        $region_id = '';
                                        $district_id = '';
                                        $project_status = '';
                                    }
                                    ?>  
                            <div class="col-sm-4">
                                <!-- email subject -->
                                <div class="form-group">
                                    <label for="projectname" >Project Name</label>
                                    <input type="text" name="projectname" id="projectname" placeholder="Enter Project Name" value="<?php echo !empty($pname)?$pname:''; ?>" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">                                                            
                                    <label for="projectregion" class="control-label">Project Region</label> 
                                        <?php try{
                                        $rqts= Helpers_Utilities::get_region();
                                        $data = $rqts->as_array();
                                        ?>                                    
                                    <select <?php if (!empty($region_id)) {echo 'selected readonly="readonly" disabled'; } ?> class="form-control" onchange="region_district()" data-placeholder="Please Select Region Name" name="projectregion" id="projectregion" style="width: 100%">
                                        <option hidden value="" >Please Select Region Name</option>
                                        <?php foreach ($data as $rqt) { ?>
                                            <option <?php if (!empty($region_id) && $region_id == $rqt->region_id) { echo 'Selected';} ?>
                                                value="<?php echo $rqt->region_id; ?>"><?php echo $rqt->name; ?></option>
                                        <?php }
                                        }  catch (Exception $ex){   }?>                                                                                                                
                                    </select>                                                                                    
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">                                                            
                                    <label for="projectdistrict" class="control-label">Project Region/District/Police Station</label>
                                    <select <?php if (!empty($district_id)) {echo 'selected readonly="readonly" disabled'; } ?> class="form-control"  placeholder="Select Region/District/Police Station" name="projectdistrict" id="projectdistrict" style="width: 100%">
                                                                                                                                                       
                                    </select>                                                                                    
                                </div>
                            </div>
                            <!-- operating company names -->
                            <div class="col-sm-12">
                                <div class="form-group">                                                            
                                    <label for="porganization" class="control-label">Linked Organization </label> 
                                    <a href="<?php echo URL::site('organization/showform'); ?>" style="float: right"> Add New Organization</a>
                                        <?php try{
                                        $rqts = Model_Intprojects::get_banned_org_list();
                                        ?>                                    
                                    <select <?php if (true) {echo 'readonly'; } ?> class="form-control select2" data-placeholder="Select Organizations name" multiple="multiple" name="porganization[]" id="porganization"  style="width: 100%;">
                                        <?php foreach ($rqts as $rqt) { ?>
                                            <option <?php if(!empty($organizations)){ foreach($organizations as $orgs){ if ($orgs['org_id'] == $rqt['org_id']) { echo 'Selected';} }}?>
                                                value="<?php echo $rqt['org_id']; ?>"><?php echo $rqt['org_name']; ?></option>
                                        <?php } 
                                        }  catch (Exception $ex){   }?>                                                                                                                
                                    </select>                                                                                    
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <!-- email format -->
                                <div class="form-group"> 
                                    <label for="projectdetails" >Project Details</label>  
                                    <div class="box">
                                        <!-- /.box-header -->
                                        <div class="box-body pad">                                             
                                            <textarea id="projectdetails" value=""  name="projectdetails" class="textarea form-control" placeholder="Please enter project details" style="width: 100%; height: 150px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php echo !empty($pdetails)?$pdetails:''; ?></textarea>                                        
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Project Status</label>
                                    <div class="radio radio-primary" style="margin-left: 20px">                                            
                                        <input type="radio" <?php echo (isset($project_status) && $project_status == 0) ? 'checked' : ''; ?> name="project_status" id="project_status_1" value="0">
                                        <label for="project_status_1" style="padding-left: 2px; margin-right: 25px">
                                            Open
                                        </label>
                                        <input type="radio" <?php echo (isset($project_status) && $project_status == 1) ? 'checked' : ''; ?>  name="project_status" id="project_status_2" value="1">
                                        <label for="project_status_2" style="padding-left: 2px; margin-right: 25px">
                                            Close
                                        </label>                                            
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- </box-body> -->
                        <!-- form buttons -->
                        <div class="box-footer">
                            <div class="col-sm-12">	
                                <input type="button"  onclick="clearform();" class="btn btn-success" value="Clear"/>
                                <input type="hidden" value="<?php echo !empty($projectid)?$projectid:''; ?>" name="id" id="id" />
                                <input type="hidden" value="<?php echo !empty($region_id)?$region_id:''; ?>" name="region" id="region" />
                                <input type="submit" value="Submit" class="btn btn-primary" />
                            </div>
                        </div>
                    </form> 
                </div>
            </div>
        </div>

    </div>
</section>
<!-- /.content -->
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();
        });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        region_district();
    });
    $("#addprojectform").validate({
            rules: {
                projectname: {
                    required: true,
                    // alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 50,
                },
                "porganization[]": {
                    required: true,
                    check_list: true,
                },
                projectdetails: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 900,
                },
            },
            messages: {
                projectname: {
                    required: "Please Enter Name",
                    maxlenght: "Maximum character limit is 50",
                    minlength: "Min character limit is 1"
                },
                porganization: {
                    required: "Please Select Organization",
                },
                projectdetails: {
                    required: "Please Enter Details",
                    maxlenght: "Maximum character limit is 900",
                    minlength: "Min character limit is 1",
                },
            }

        });

        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            //alert(value);
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ () " " .:,/]+$/);
        }, "Only letters, Numbers & Space Allowed."); 
        
        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "") {
                return false;
            } else {
                return true;
            }
        }, "<span>Select One</span>");
    // function to clear form
    function clearform() {
        var ab = '';
        $("#projectname").val(ab);
        jQuery("#porganization").select2().select2('val', ['']);
        jQuery("#projectregion").val($("#projectregion").data("default-value"));
        jQuery("#projectdistrict option").remove();
        jQuery("#projectdistrict optgroup").remove();        
        jQuery("#projectdistrict").append('<option value="">Please Select region Name</option>');                
        $("#projectdetails").val(ab);
    }
</script>
<script src="<?php echo URL::base() . 'dist/js/typeahead.js'; ?>"></script>
 <script>
    $(document).ready(function () {
        $('#projectname').typeahead({
            source: function (query, result) {
                $.ajax({
                    url: "<?php echo URL::base() . "intprojects/project_name/"; ?>",
		    data: 'query=' + query,            
                    dataType: "json",
                    type: "POST",
                    success: function (data) {
                                result($.map(data, function (item) {
                                return item;
                        }));
                        if (data == 2){
                                 swal("System Error", "Contact Support Team.", "error");
                             }
                    }
                    
                });
            }
        });
    });
    function region_district() {
        var region_id = $('#projectregion').val();
        var district = <?php echo (!empty($record['district_id']) ? $record['district_id'] : '0')?>;
        var searchresults = {region: region_id , district: district}
        $.ajax({
            url: "<?php echo URL::site("intprojects/region_district")?>",
            type: 'POST',
            data: searchresults,
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (data)
            {
                if(data== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                $("#projectdistrict").html(data);
            }
        });
    }    
</script>