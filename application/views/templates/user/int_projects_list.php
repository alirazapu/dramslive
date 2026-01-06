<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//echo '<pre>';
//print_r($post);
//exit;
?>
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-circle-o"></i> Projects
        <small>Tracer</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>	
        <li><a href="#"> Projects</a></li>
        <li class="active">Projects List</li>
      </ol>
    </section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('intprojects'); ?>">
            <div class="box box-default collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Advance Search</h3>

                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($post)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                        <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="<?php echo (!empty($_POST)) ? 'display:block;' : ''; ?>">


                    <div id="blocktohide">
                        <div class="col-sm-4" >
                            <div class="form-group">
                                <label class="control-label">Project Name.<span class="text-danger">(*)</span></label>
                                <input type="text" class="form-control" name="pname" id="pname"
                                       placeholder="Enter Project Name" value="<?php echo (!empty($post['pname']) ? $post['pname'] : ''); ?>" >
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="reqbyregion" class="control-label"> Region <span class="text-danger">(*)</span></label>
                                <?php try{
                                $rqts= Helpers_Utilities::get_region();
                                $data = $rqts->as_array();
                                ?>
                                <select <?php if (!empty($post['reqbyregion'])) {echo 'selected readonly="readonly" disabled'; } ?> class="form-control" onchange="region_district()" data-placeholder="Please Select Region Name" name="reqbyregion" id="reqbyregion" style="width: 100%">
                                    <option hidden value="" >Please Select Region Name</option>
                                    <?php foreach ($data as $rqt) { ?>

                                        <option <?php if (!empty($post['reqbyregion']) && $post['reqbyregion'] == $rqt->region_id) { echo 'Selected';} ?>
                                                value="<?php echo $rqt->region_id; ?>"><?php echo $rqt->name; ?></option>
                                    <?php }
                                    }  catch (Exception $ex){   }?>
                                </select>
                            </div>
                        </div>
<!--                                                <div class="col-sm-4">-->
<!--                                                    <div class="form-group">-->
<!--                                                        <label for="reqbydistrict" class="control-label">Requested By District</label>-->
<!--                                                        --><?php //if(!empty($post['reqbydistrict'])) {
//                                                           // $dqts = Helpers_Utilities::get_district_name_by_id($post['reqbydistrict']);
//                                                            $dqts = Helpers_Utilities::get_region_district($post['reqbyregion']);
//
//                                                        }?>
<!--                                                        <select --><?php //if (!empty($post['reqbydistrict'])) {echo 'selected readonly="readonly" disabled'; } ?><!-- class="form-control"  placeholder="Select District" name="reqbydistrict" id="reqbydistrict" style="width: 100%"  >-->
<!--                                                            --><?php
//                                                            if(!empty($dqts)) {
//                                                                foreach ($dqts as $rqt1) { ?>
<!--                                                                    <option --><?php //if (!empty($post['reqbydistrict']) && $post['reqbydistrict'] == $rqt1['district_id']) {
//                                                                        echo 'Selected';
//                                                                    } ?>
<!--                                                                            value="--><?php //echo $rqt1['district_id']; ?><!--">--><?php //echo $rqt1['name']; ?><!--</option>-->
<!--                                                                --><?php //}
//                                                            }else{ ?>
<!--                                                            <option --><?php //if (!empty($post['reqbydistrict']) ) { echo 'selected';} ?>
<!--                                                                  >-->
<!--                                                                  </option>-->
<!--                                                           --><?php //} ?>
<!--                                                        </select>-->
<!--                                                    </div>-->
<!--                                                </div>-->

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="reqbydistrict" class="control-label">District/Police Station <span class="text-danger">(*)</span></label>
                                <?php
//                                $dist_info='';
//                                $dist_info1='';
                                if(!empty($post['reqbyregion'])) {
//                                    if (!empty($post['reqbydistrict'])) {
                                    if (!empty($post['reqbydistrict']) && ($post['reqbydistrict']>=901 && $post['reqbydistrict']<=906)) {
                                        $dist_info1 = Helpers_Utilities::get_region_police($post['reqbyregion']);
                                    }
                                    if (!empty($post['reqbydistrict'])&& $post['reqbydistrict']!=100 ) {
                                        $dist_info = Helpers_Utilities::get_region_district($post['reqbyregion']);
                                    }
                                    if($post['reqbydistrict']==100){
                                        $dist='Self';
                                        $post['reqbydistrict'] = 0;
                                    }
                                }
                                ?>
                                <select <?php if (!empty($post['reqbydistrict'])) {echo 'selected readonly="readonly" disabled'; } ?> class="form-control"  placeholder="Select District/Police Station" name="reqbydistrict" id="reqbydistrict" style="width: 100%">
<!--                                    --><?php //foreach ($dist_info as $rqt1) { ?>
<!---->
<!--                                    <option --><?php //if (!empty($post['reqbyregion']) && $post['reqbyregion'] == $rqt->region_id) { echo 'Selected';} ?>
<!--                                            value="--><?php //echo $rqt->region_id; ?><!--">--><?php //echo $rqt->name; ?><!--</option>-->
<!--                                    --><?php //}?>

                                    <?php
                                    if(!empty($dist_info)) {
                                        foreach ($dist_info as $rqt1) { ?>
                                            <option <?php if (!empty($post['reqbydistrict']) && $post['reqbydistrict'] == $rqt1['district_id']) {
                                                echo 'Selected';
                                            } ?>
                                                    value="<?php echo $rqt1['district_id']; ?>"><?php echo $rqt1['name']; ?></option>
                                        <?php }
                                    }
                                    elseif(!empty($dist_info1)) {
                                        foreach ($dist_info1 as $rqt2) { ?>
                                            <option <?php if (!empty($post['reqbydistrict']) && $post['reqbydistrict'] == $rqt2['id']) {
                                                echo 'Selected';
                                            } ?>
                                                    value="<?php echo $rqt2['id']; ?>"><?php echo $rqt2['description']; ?></option>
                                        <?php }
                                    }elseif(!empty($dist)){?>
                                        <option <?php if (!empty($post['reqbydistrict']) ) { echo 'selected';} ?>
                                                value="<?php echo $post['reqbydistrict']; ?>"><?php echo $dist; ?>

                                        </option>
                                    <?php }
                                    else{ ?>
                                        <option <?php if (!empty($post['reqbydistrict']) ) { echo 'selected';} ?>
                                        >
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="searchfield">Start Date (mm/dd/yyyy)<span class="text-danger">(*)</span></label>
                                <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="startdate" id="startdate" value="<?php echo (!empty($post['startdate']) ? $post['startdate'] : ''); ?>" required>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- /.col -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="searchfield">End Date (mm/dd/yyyy)<span class="text-danger">(*)</span></label>
                                <input type="text" readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" name="enddate" id="enddate" value="<?php echo (!empty($post['enddate']) ? $post['enddate'] : ''); ?>" required>
                            </div>
                        </div>
                        <!-- /.col -->
                        <!-- /.col -->
                    </div>

                    <div class="col-md-12">
                        <div class="form-group pull-right">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
<!--                            <input id="xport" name="xport" type="hidden" value="" />-->
                        </div>
                    </div>
                    <!-- /.col -->
                    <!-- /.row -->
                </div>
            </div>




        </form>


        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i>Projects List</h3>
                    </div>
                    <?php
                    if (!empty($_GET['accessmessage'])) {
                        ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> <?php echo $_GET['accessmessage']; ?></h4>
                        </div>
                    <?php } ?>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="projectstable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                        
                                        <th style="width:20%" class="no-sort" >Name</th>
                                        <th style="width:10%" class="no-sort" title="Project Region">Region</th>                                                   
                                        <th style="width:10%" class="no-sort" title="Project District">District</th>                                                   
                                        <th style="width:20%" class="no-sort" title="Project Organizations">Organizations</th>                                                   
                                        <th style="width:5%"  title="Project Status">Status</th>                                                   
                                        <th style="width:35%" class="no-sort">Details</th>                                                                               
                                        <th style="" class="no-sort">Requests</th>                                                                               
                                        <th style="" class="no-sort">Options</th>
                                    </tr>
                                </thead>
                                
                                
                                <tfoot>
                                <tr>                                        
                                        <th>Name</th>
                                        <th>Region</th>                                                                           
                                        <th>District</th>                                                                           
                                        <th>Organizations</th>                                                                           
                                        <th>Status</th>                                                                           
                                        <th>Details</th>                                                                               
                                        <th>Requests</th>                                                                               
                                        <th>Options</th>
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

<script type="text/javascript">
    var objDT;
      
    function refreshGrid(){
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if($("#msg_to_show").val() != ""){
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }
    
 $(document).ready(function(){
        $.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
    if(oSettings.oFeatures.bServerSide === false){
        var before = oSettings._iDisplayStart;
        oSettings.oApi._fnReDraw(oSettings); 
        // iDisplayStart has been reset to zero - so lets change it back
        oSettings._iDisplayStart = before;
        oSettings.oApi._fnCalculateEnd(oSettings);
    }
      
    // draw the 'current' page
    oSettings.oApi._fnDraw(oSettings);
};
        objDT = $('#projectstable').dataTable(
        {   "aaSorting": [[ 4, "asc" ]],    
            "bPaginate" : true,
            "bProcessing" : true,
            //"bStateSave": true,
            "bServerSide" : true,
            "sAjaxSource" : "<?php echo URL::site('intprojects/ajaxprojectslist',TRUE); ?>",
            "sPaginationType" : "full_numbers",
            "bFilter" : true,
            "bLengthChange" : true,
            "oLanguage": {
                "sProcessing": "Loading...",
                "sSearch": "Search By Project Name Or Details:"
              },
            "columnDefs": [ {
          "targets": 'no-sort',
          "orderable": false,
    } ]
        }
    );  
        $('.dataTables_empty').html("Information not found");
        
  });

    $("#search_form").validate({
        rules: {
            pname: {
                required: true,
            },
            reqbyregion: {
                required: true,
            },
            reqbydistrict: {
                required: true,
            },
            startdate: {
                sdate_value: true,
            },
            enddate: {
                edate_value: true,
            },
        },
        messages: {
            pname: {
                required: "Enter Project Name",
            },
            reqbyregion: {
                required: "Please Select Region",
            },
            reqbydistrict: {
                required: "Please Select District",
            },

            startdate: {
                sdate_value: "Select Start Date",
            },
            etartdate: {
                edate_value: "Select End Date",
            },
        }
    });
    $.validator.addMethod("sdate_value", function (sel, element) {
        if ($('#field').val() == "date" && $('#startdate').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("edate_value", function (sel, element) {
        if ($('#field').val() == "date" && $('#enddate').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");
    //Date picker
    $('#startdate').datepicker({
        autoclose: true
    });
    //Date picker
    $('#enddate').datepicker({
        autoclose: true
    });


    function clearSearch(){
        // alert('test');
        window.location.href = '<?php echo URL::site('intprojects', TRUE); ?>';
    }

    $('#field').change(function () {
        // $('#cnic').val('')
        // $('#inputreason').val('')
        // $('#date').val('')
        $('#reqbyregion').val('').change();
        $('#reqbydistrict').val('').change();

    });

    function region_district() {
        var region_id = $('#reqbyregion').val();
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
                 $("#reqbydistrict").html(data);
            }
        });
    }
        
     

</script> 