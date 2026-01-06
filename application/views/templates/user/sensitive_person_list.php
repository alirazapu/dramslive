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
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >Person's Report</li>
        <li class="active">Sensitive Person's List</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i>Sensitive Person's List</h3>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="sensitivepersonslist" class="table table-bordered table-striped">
                                <thead>
                                    <tr> 
                                        <th class="no-sort">Person Name</th>
                                        <th class="no-sort">Person CNIC</th>
                                        <th>Added On</th> 
                                        <th class="no-sort">Added By User</th>
                                        <th class="no-sort">User Type</th>
                                        <th class="no-sort">Designation</th>                                                                               
                                        <?php
                                        $login_user = Auth::instance()->get_user();
                                        $permission = Helpers_Utilities::get_user_permission($login_user->id);                        
                                        $userslist = [842, 137, 2031, 1761, 2597, 2603];
                                        if (($permission == 1) && (in_array($login_user->id, $userslist))) {
                                            echo '<th class="no-sort">Action</th>';
                                        }
                                            
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                                                        
                                </tbody>
                                <tfoot>                                    
                                    <tr>                    
                                        <th>Person Name</th>
                                        <th>Person CNIC</th>
                                        <th>Added On</th>
                                        <th>Added By User</th>
                                        <th>User Type</th>
                                        <th>Designation</th>                                        
                                       <?php if (($permission == 1) && (in_array($login_user->id, $userslist))) {
                                            echo '<th class="no-sort">Action</th>';
                                        } ?>
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
<script>
   function ConfirmChoice(userid, personid) 
    { 
       // var elem = $(this).closest('.item');
        //var elem = $(".item-" + id);
        $.confirm({
            'title'     : 'Delete Confirmation',
            'message'   : 'Do you really want to delete this?',
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){        
                        $.ajax({url: '<?php echo URL::base() . "personsreports/sensitive_person_del/"; ?>'  + userid + '/' + personid, success: function(result){
                                    if (result == '-2') {
                                    swal("System Error", "Contact Support Team.", "error");
                                } else {
                                //elem.slideUp();
                                refreshGrid();
                            }
                        }});                        
                    }
                 },
                'No'    : {
                    'class' : 'blue',
                    'action': function(){}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
   } 
    
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

        objDT = $('#sensitivepersonslist').dataTable(
        {   "aaSorting": [[ 2, "desc" ]],    
            "bPaginate" : true,
            "bProcessing" : true,
            //"bStateSave": true,
            "bServerSide" : true,
            "sAjaxSource" : "<?php echo URL::site('personsreports/ajaxsensitivepersonlist',TRUE); ?>",
            "sPaginationType" : "full_numbers",
            "bFilter" : true,
            "bLengthChange" : true,
            "oLanguage": {
                "sProcessing": "Loading...",
                 "sSearch": "Search By Person Name or CNIC:"
              },
            "columnDefs": [ {
          "targets": 'no-sort',
          "orderable": false,
    } ]
        }
    );  
        $('.dataTables_empty').html("Information not found");
        
  });
       
function excel(id){    
            $('#xport').val('excel');
            $('#search_form').submit();            
            $('#xport').val('');
    }
</script>