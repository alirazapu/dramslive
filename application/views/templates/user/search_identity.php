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
        <i class="fa fa-search-plus"></i>
        Search Person
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">identity Search</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form class="searchform_input_height" id="search_form" name="search_form" method="post" role="form" action="<?php echo URL::site('User/search_identity'); ?>" >
                    <div class="box box-default searchperson">
                        <div class="box-header with-border">
                            <h3 class="box-title">Identity Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">
                            <div class="col-sm-6" >
                                        <div class="form-group">                                                            
                                            <label for="search_identity_type">Identity Type</label>
                                            <?php try{
                                            $person_identity_list = Helpers_Person::get_person_identity_type();
                                            ?>
                                            <select class="form-control" id="search_identity_type" name="search_identity_type">
                                                <option <?php if( (isset($search_post['search_identity_type']) && ($search_post['search_identity_type'] == -7))){ echo 'selected'; }else{} ?> value="-7">Select Identity Type</option>
                                                <?php foreach ($person_identity_list as $person_identity){ ?>
                                                <option value="<?php echo $person_identity->id; ?>"  <?php if(empty($search_post['search_identity_type']) && $person_identity->id==4){ echo "selected"; } if( (isset($search_post['search_identity_type']) && ($search_post['search_identity_type'] == $person_identity->id))){ echo 'selected'; }else{} ?> ><?php echo $person_identity->identity; ?></option>
                                                <?php } 
                                                }  catch (Exception $ex){   }?>                                                                                                                
                                            </select>                                                                                    
                                        </div>
                                    </div>                            
                            <div class="form-group col-md-6">
                                <label for="identity_search">Person Identity </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-star"></i>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo ((!empty($search_post['identity_search'])) ? $search_post['identity_search'] : ''); ?>" id="identity_search" name="identity_search" placeholder="Search Key">
                                    
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group pull-right" >
                                    <button type="submit" onclick="return validateAndSend()" class="btn btn-primary ">Search</button>
                                    <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                </div>
                            </div>
                        </div>        
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> Searched Persons</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="searchperson" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th >Person's Name</th>
                                        <th >Father/Husband Name</th>
                                        <th class="no-sort" >Identity Type</th>                                          
                                        <th class="no-sort" >Identity#</th>                                          
                                        <th class="no-sort"  >Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <td>Information not found</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                </tbody>
                                <tfoot style="background-color: lightblue;">

                                        <th >Person's Name</th>
                                        <th >Father/Husband Name</th>
                                        <th class="no-sort" >Identity Type</th>                                          
                                        <th class="no-sort" >Identity#</th>                                          
                                        <th class="no-sort"  >Detail</th>
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
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
    
    //
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();
        });
        
        
</script>
<script type="text/javascript">
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() !== "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        //nadra request
        // validate cnic request send
        
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
        <?php if(!empty($search_post['search_identity_type'])){ ?>
        objDT = $('#searchperson').dataTable( 
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                  //  //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php  echo URL::site('user/ajaxsearchidentity', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Name, Father Name and CNIC:",
                        "sEmptyTable": 'Information not found'
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false
                        }]
                }
        );
        <?php  } ?>
       // $('.dataTables_empty').html("Information not found, <a href='<?php // echo URL::site('userrequest/request/1', TRUE); ?>'> Please Request form Here </a>");


// validation for data search
$("#search_form").validate({
                  rules:{
                             identity_search:{
                                 alphanumericspecial:true,
                            minlength:3,
                            maxlength: 20
                            }                      
                        },
                    messages: {
                         identity_search:{                            
                                maxlenght:"Maximum character limit is 20",
                                minlength:"Minimum character limit is 3" 
                            }                           
                        }, 
                        
                        submitHandler: function () {
                            $("#search_form").submit();
               
                         }                   
                  // $('#upload').show()
                });               
                
                $.validator.addMethod("alphanumericspecial", function(value, element) {
        return this.optional(element) || /^[a-z0-9\#\-\_]+$/i.test(value);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed."); 
                
               
                
    });

    

    $.validator.addMethod("check_list",function(sel,element){
            if(sel == "" || sel == 0){
                return false;
             }else{
                return true;
             }
            },"<span>Select One</span>");
            function isNumber(arg) {
                var is_number = Number(arg);
                if(is_number == arg )
                    return 'Number';
                else
                    return 'String';  
            }
        function validateAndSend() {
            if(search_form.identity_search.value == "" ){
                alert('Please Enter Valid Identity');
                return false;
            }else if((isNumber(search_form.identity_search.value)!="Number"  && search_form.search_identity_type.value == 4)) {
                alert('Please Enter Valid Pakistani CNIC');
                return false;
            }
        }
    function clearSearch() {
        window.location.href = '<?php echo URL::site('User/search_identity', TRUE); ?>';
    }

</script>