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
        <i class="fa fa-users"></i>
        Person's Report 
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >Person's Report</li>
        <li class="active">Project Persons</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="box box-primary">
        <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('personsreports/project_persons'); ?>">
            <div class="box box-default collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">Advance Search</h3>

                    <div class="box-tools pull-right">
                        <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                        <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="field">Select Type</label>
                            <select class="form-control " name="field" id='field' onchange="showDiv(this)">
                                <option value="def"> Please Select Type</option>
                                <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'projectname')) ? 'selected' : ''); ?> value="projectname"> Project Name</option>
<!--                                <option --><?php //echo ((!empty($search_post['field']) && ($search_post['field'] == 'projectregion')) ? 'selected' : ''); ?><!-- value="projectregion"> Project Region</option>-->
<!--                                <option --><?php //echo ((!empty($search_post['field']) && ($search_post['field'] == 'projectdistrict')) ? 'selected' : ''); ?><!-- value="projectdistrict"> Project District</option>-->

                            </select>
                        </div>
                    </div>

<!--                    <div id="posting-hide">-->
<!--                        <div class="col-md-6 posting_acl">-->
<!--                            <div class="form-group">-->
<!--                                <label for="posting">Select Region</label>-->
<!--                                <select class="form-control select2" multiple="multiple" id="posting" name="posting[]" style="width: 100%;">-->
<!--                                    <option value="">Please Select Region</option>-->
<!--                                    <optgroup label="Region">-->
<!--                                        --><?php //try{
//                                            $region_list = Helpers_Utilities::get_region();
//                                            foreach ($region_list as $list) {
//                                                ?>
<!--                                                <option --><?php //echo (!empty($search_post['posting']) && in_array('r-' . $list->region_id, $search_post['posting'])) ? 'Selected' : ''; ?><!-- value="r---><?php //echo $list->region_id ?><!--">--><?php //echo $list->name ?><!--</option>-->
<!--                                            --><?php //}
//                                        }  catch (Exception $ex){   }?>
<!---->
<!---->
<!--                                </select>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div id="posting-hide1">-->
<!--                        <div class="col-md-6 posting_acl">-->
<!--                            <div class="form-group">-->
<!--                                <label for="posting">Select District</label>-->
<!--                                <select class="form-control select2" multiple="multiple" id="posting" name="posting[]" style="width: 100%;">-->
<!--                                    <option value="">Please Select District</option>-->
<!--                                    <optgroup label="District">-->
<!--                                        --><?php //try{
//                                            $district_list = Helpers_Utilities::get_district();
//                                            foreach ($district_list as $list) {
//                                                ?>
<!--                                                <option --><?php //echo (!empty($search_post['posting']) && in_array('d-' . $list->district_id, $search_post['posting'])) ? 'Selected' : ''; ?><!-- value="d---><?php //echo $list->district_id ?><!--">--><?php //echo $list->name ?><!--</option>-->
<!--                                            --><?php //}
//                                        }  catch (Exception $ex){   }?>
<!---->
<!--                                </select>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->

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
                            <input id="xport" name="xport" type="hidden" value="" />
                        </div>
                    </div>
                    <!-- /.col -->
                    <!-- /.row -->

                </div>




        </form>

    </div>
    </div>


        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i>Project Affiliated Person's List</h3>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="personslist" class="table table-bordered table-striped">
                                <thead>
                                    <tr> 
                                        <th>Project Name</th>
                                        <th>Project Organization(s)</th>
                                        <th>Project Region</th>
                                        <th class="no-sort">Project District</th>
                                        <th>Project Status</th> 
                                        <th class="no-sort">Grey Persons</th>
                                        <th class="no-sort">Black Persons</th>
                                        <th class="no-sort">Total</th>                                                                               
                                    </tr>
                                </thead>
                                <tbody>
                                                                        
                                </tbody>
                                <tfoot>                                    
                                    <tr>                    
                                        <th>Project Name</th>
                                        <th>Project Organization(s)</th>
                                        <th>Project Region</th>
                                        <th>Project District</th>
                                        <th>Project Status</th> 
                                        <th>Grey Persons</th>
                                        <th>Black Persons</th>
                                        <th>Total</th>                                        
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

<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>


<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>

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

     var elem = $('#field').val();
     if (elem == 'def')
     {

         //Hide
         //document.getElementById('posting-hide').style.display = "none";
         // $("#posting-hide").hide();
         // //Hide
         // //document.getElementById('utype').style.display = "none";
         // $("#posting-hide1").hide();
         //show
        // document.getElementById('key-hide').style.display = "block";
         $("#key-hide").show();
         //disable
         // $('#searchfield').attr("disabled","disabled");
     }
    else if(elem == 'projectname')
     {
         //Hide
         // document.getElementById('posting-hide').style.display = "none";
         // //Hide
         // document.getElementById('posting-hide1').style.display = "none";
         //show
         document.getElementById('key-hide').style.display = "block";
         //disable
         // $('#searchfield').attr("disabled","disabled");
     }
     // else if (elem == 'projectregion')
     // {
     //     //Hide
     //     document.getElementById('posting-hide').style.display = "block";
     //     //Hide
     //     document.getElementById('posting-hide1').style.display = "none";
     //     //show
     //     document.getElementById('key-hide').style.display = "none";
     //     //disable
     //     // $('#searchfield').attr("disabled","disabled");
     // }
     // else if (elem == 'projectdistrict')
     // {
     //     //Hide
     //     document.getElementById('posting-hide').style.display = "none";
     //     //Hide
     //     document.getElementById('posting-hide1').style.display = "block";
     //     //show
     //     document.getElementById('key-hide').style.display = "none";
     //     //disable
     //     // $('#searchfield').attr("disabled","disabled");
     // }


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

        objDT = $('#personslist').dataTable(
        {   "aaSorting": [[ 3, "desc" ]],    
            "bPaginate" : true,
            "bProcessing" : true,
            //"bStateSave": true,
            "bServerSide" : true,
            "sAjaxSource" : "<?php echo URL::site('personsreports/ajaxprojectpersons',TRUE); ?>",
            "sPaginationType" : "full_numbers",
            "bFilter" : true,
            "bLengthChange" : true,
            "oLanguage": {
                "sProcessing": "Loading...",
                 "sSearch": "Search By Project Name:"
              },
            "columnDefs": [ {
          "targets": 'no-sort',
          "orderable": false,
    } ],  dom: 'Bfrtip',
                    buttons: [
                        'pageLength','excel', 'pdf', 'print'
                    ]
                
        }
    );  
        $('.dataTables_empty').html("Information not found");
        
  });

    function showDiv(elem) {
        // alert(elem);
        if (elem.value == 'def')
        {
            //Hide
            // document.getElementById('posting-hide').style.display = "none";
            // //Hide
            // document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem.value == 'projectname')
        {
            //Hide
            // document.getElementById('posting-hide').style.display = "none";
            // //Hide
            // document.getElementById('posting-hide1').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }
        // else if (elem.value == 'projectregion')
        // {
        //     //Hide
        //     document.getElementById('posting-hide').style.display = "block";
        //     //Hide
        //     document.getElementById('posting-hide1').style.display = "none";
        //     //show
        //     document.getElementById('key-hide').style.display = "none";
        //     //disable
        //     // $('#searchfield').attr("disabled","disabled");
        // }
        //
        //
        // else if (elem.value == 'projectdistrict')
        // {
        //     //Hide
        //     document.getElementById('posting-hide').style.display = "none";
        //     //Hide
        //     document.getElementById('posting-hide1').style.display = "block";
        //     //show
        //     document.getElementById('key-hide').style.display = "none";
        //     //enable
        //     // $('#searchfield').removeAttr("disabled");
        // }


    }
    function clearSearch() {
        window.location.href = '<?php echo URL::site('personsreports/project_persons', TRUE); ?>';
    }
function excel(id){    
            $('#xport').val('excel');
            $('#search_form').submit();            
            $('#xport').val('');
    }
</script>