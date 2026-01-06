<?php
//print_r($search_post['district_id']); exit;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       <i class="fa fa-circle-o"></i> Watch List Persons
        <small>Tracer</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>	
        <li><a href="#"> Watch List</a></li>
        <li class="active">View Watch List</li>
        <li class="active">View Watch List Details</li>
      </ol>
    </section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <?php// $dist_id=  Helpers_Utilities::encrypted_key($search_post['district_id'], 'encrypt') ?>
                <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('watchlist/view_watch_list_details?district_id='.$search_post['district_id']); ?>" >
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post['category_type'])) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post['category_type'])) ? 'display:block;' : ''; ?>">                            
                              <div class="col-md-12">                                  
                                      <div class="form-group">
                                          <label>Person Category</label>
                                          <div class="checkbox" style="margin-left: 20px">                                          
                                              <?php try{
                                              $tags = Helpers_Watchlist::get_tags_data(); 
                                              foreach ($tags as $tag) {
                                                  ?>
                                                  <div class="form-group col-md-3 watchlistadvancesearch"> 
                                                      <div class="col-md-12">
                                                          <input type="checkbox" <?php echo (isset($search_post['category_type'][$tag->id]) && ($search_post['category_type'][$tag->id]== 'on')) ? 'checked' : ''; ?> name="category_type[<?php echo $tag->id; ?>]" id="personcategory">
                                                          <label for="personcategory" style="padding-left: 12px;"> <?php echo $tag->tag_name; ?> </label>
                                                      </div>
                                                  </div>
                                               <?php                                               
                                              } 
                                              }  catch (Exception $ex){   }?>
                                          </div>
                                      </div>                                                                                                            
                              </div>    
                            <!-- /.col -->
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
                    <h3 class="box-title"><i class="fa fa-search"></i> View Watch List Details</h3>
                </div>
                <?php
                if (!empty($message)) {
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <?php echo $message; ?></h4>
                    </div>
                <?php } ?>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="personlist" class="table table-bordered table-striped">
                            <thead>
                                <tr>                                   
                                    <th class="no-sort">Person Name</th>                                                                                                          
                                    <th class="no-sort">Father/Husband Name</th>                                    
                                    <th class="no-sort">CNIC Number</th>
                                    <th class="no-sort">Address</th>
                                    <th class="no-sort">Category</th>
                                    <th class="no-sort">User Name</th>
                                    <th class="no-sort">Action</th>
                                    
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <tr>                                     
                                    <th>Person Name</th>                                                                                                          
                                    <th>Father/Husband Name</th>                                    
                                    <th>CNIC Number</th>
                                    <th>Address</th>
                                    <th>Category</th>
                                    <th>User Name</th>
                                    <th>Action</th>
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
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>
<script type="text/javascript">
    /* For nadra verisis request processing */
    function findphonenumber(cnic,requestid) {

        $("#cnic_number").val(cnic);
        $("#process_request_id").val(requestid);
        $("#process_nadra_verysis").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second     
            $(".modal-backdrop.fade.in").remove();
        }, 300);

    }

    //table 
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        //validate Person Verisis
        
        $("#veriysispics").validate({
                  rules:{
                        personverysis:{
                            required: true,
                            accept: "jpg,jpeg,gif,png",
                            filesize: 900000,
                                   },
                        },
                    messages: {
                        personverysis:{
                                required:"File Required",  
                              },
                        }                       
                   
                });

$.validator.addMethod('filesize', function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param)
}, 'File size must be less than {0} Kb');
    
    //table data
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
        objDT = $('#personlist').dataTable(
                {"aaSorting": [],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('watchlist/ajaxwatchlistdetails', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Person Name:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");        
        $.fn.dataTable.ext.errMode = 'none';
        $('#personlist').on('error.dt', function(e, settings, techNote, message) {
           swal("System Error", "Contact Technical Support Team.", "error");
        })

    });
// request update
$(document).ready(function (e) {
    $('#veriysispics').on('submit',(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
 if($('#veriysispics').valid())
        {
        $.ajax({
            type:'POST',
            url: $(this).attr('action'),
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                $("#process_nadra_verysis").modal("hide");
                $("#notification_msg_divreports").html('Successfully Updated');
                            $("#notification_msgreports").show();
                            $("#notification_msgreports").addClass('alert');
                            $("#notification_msgreports").addClass('alert-success');
                            var elem = $(".notificationclosereports");
                            elem.slideUp(10000);
                            refreshGrid();
            },
            error: function(data){
                console.log("error");
                console.log(data);
            }
        });
    }
    }));

//    $("#ImageBrowse").on("change", function() {
//        $("#imageUploadForm").submit();
//    });
});
    function clearSearch() {
        window.location.href = '<?php echo URL::site('watchlist/view_watch_list_details?district_id='.$search_post['district_id'], TRUE); ?>';
    }
    
    function ConfirmChoice(id) 
    { 
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
       //var elem = $(this).parent().parent();// .closest('.action');
       // var elem = $(this).closest('tr[class^="action"]');       
        //var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title'     : 'Add to watchlist confirmation',
            'message'   : 'Do you really want to Add this person to your watchlist?' ,
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){        
                        $.ajax({url: '<?php echo URL::base() . "Watchlist/addtowatchlist/"; ?>'  + id ,
                            success: function(result){                                 
                                if (result == '-2') { 
                                    alert('Access denied, contact your technical support team');
                                }
                                else{
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
    function removewatchlist(id) 
    { 
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
       //var elem = $(this).parent().parent();// .closest('.action');
       // var elem = $(this).closest('tr[class^="action"]');       
        //var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title'     : 'Remove watchlist confirmation',
            'message'   : 'Do you really want to Remove this person from your watchlist?' ,
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){        
                        $.ajax({url: '<?php echo URL::base() . "Watchlist/removefromwatchlist/"; ?>'  + id , 
                            success: function(result){                                 
                                if (result == '-2') { 
                                    alert('Access denied, contact your technical support team');
                                }
                                else{
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