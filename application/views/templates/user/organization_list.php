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
       <i class="fa fa-circle-o"></i> Organizations
        <small>Tracer</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>	
        <li><a href="#"> Organization</a></li>
        <li class="active">Organizations List</li>
      </ol>
    </section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">


        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> Organization List</h3>
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
                                        <th style="width:40%">Organization Name</th>
                                        <th style="width:20%" title="Organization name">Org. Acronym</th>                                                   
                                        <th style="width:30%" class="no-sort">Notification Number</th>                                                                               
                                        <th style="width:10%" class="no-sort">Options</th>
                                    </tr>
                                </thead>
                                
                                
                                <tfoot>
                                <tr>                                        
                                        <th>Project Name</th>
                                        <th>Org. Name</th>                                                                           
                                        <th>Details</th>                                                                               
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
        {   "aaSorting": [[ 2, "desc" ]],    
            "bPaginate" : true,
            "bProcessing" : true,
            //"bStateSave": true,
            "bServerSide" : true,
            "sAjaxSource" : "<?php echo URL::site('organization/ajaxprojectslist',TRUE); ?>",
            "sPaginationType" : "full_numbers",
            "bFilter" : true,
            "bLengthChange" : true,
            "oLanguage": {
                "sProcessing": "Loading...",
                "sSearch": "Search By Organization Name Or Acronym:"
              },
            "columnDefs": [ {
          "targets": 'no-sort',
          "orderable": false,
    } ]
        }
    );  
        $('.dataTables_empty').html("Information not found");
        
  });
        
     

</script> 