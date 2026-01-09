
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
        User's Report 
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li >User's Report</li>
        <li class="active">Transferred User's</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <?php if (isset($_GET["message"]) && $_GET["message"] == 1) { ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> Congratulation! Password changed as (12345678) Successfully.</h4>
                    </div> 
                <?php } ?>
                <?php if (isset($_GET["message"]) && $_GET["message"] == 2) { ?>
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> Congratulation! Password reverted back as original Successfully.</h4>
                    </div> 
                <?php } ?>
                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userreports/users_transferred_list'); ?>">
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
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'name')) ? 'selected' : ''); ?> value="name"> Name</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'username')) ? 'selected' : ''); ?> value="username"> User Name</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'mobile_number')) ? 'selected' : ''); ?> value="mobile_number"> Mobile Number</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'cnic_number')) ? 'selected' : ''); ?> value="cnic_number"> CNIC Number</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'Designation')) ? 'selected' : ''); ?>  value="designation"> Designation</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'posting')) ? 'selected' : ''); ?> value="posting"> Posting</option>
                                        <option <?php echo ((!empty($search_post['field']) && ($search_post['field'] == 'utype')) ? 'selected' : ''); ?> value="utype"> User Type</option>
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
                            <div id="utype">
                                <div class="col-md-6 posting_acl">
                                    <div class="form-group">
                                        <label for="posting">Select User Type</label>
                                        <select class="form-control select2" multiple="multiple" id="utype" name="utype[]" style="width: 100%;">
                                            <optgroup label="Roles">                                    
                                                <?php
                                                $roles_list = Helpers_Utilities::get_roles_data();
                                                foreach ($roles_list as $role) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['utype']) && in_array($role->id, $search_post['utype'])) ? 'Selected' : ''; ?> value="<?php echo $role->id ?>"><?php echo $role->label ?></option>
                                                <?php }?>
                                        </select>

                                    </div>
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
                        <h3 class="box-title"><i class="fa fa-list"></i> Transferred User's List</h3>
                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="userstransferredlist" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                
                                        <th>User Name</th>
                                        <th class="no-sort">Username</th>
                                        <th class="no-sort">User Type / Designation</th>
                                        <th class="no-sort">Posted / CNIC</th>                                         
                                        <th class="no-sort">Mobile#</th>                                          
                                        <th  class="no-sort">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>User Name</th>
                                        <th>Username</th>
                                        <th>User Type / Designation</th>
                                        <th>Posted / CNIC</th>                                        
                                        <th>Mobile#</th>                                        
                                        <th  class="no-sort">Action</th>
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
<div style="display:none" id="div-dialog-warning">
    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><div/></p>
</div>
</section>
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
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem == 'name')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }
        else if (elem == 'username')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }
        else if (elem == 'mobile_number')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }

        else if (elem == 'cnic_number')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }


        else if (elem == 'designation')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
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
            document.getElementById('utype').style.display = "none";
        }
        else if (elem == 'utype')
        {
            //show
            document.getElementById('utype').style.display = "block";
            //hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('key-hide').style.display = "none";
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
        objDT = $('#userstransferredlist').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('userreports/ajaxuserstransferredlist', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By User Name, Mobile #, CNIC or username:"
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
        },
        messages: {
            field: {
                check_list: "Please select search type",
            },
            key: {
                key_value: "Enter Valid Search Value",
            },
            "posting[]": {
                required: "Enter Valid Search Value",
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
     // alert(elem);
        if (elem.value == 'def')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        } else if (elem.value == 'name')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }
        else if (elem.value == 'username')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }
        else if (elem.value == 'mobile_number')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }
        else if (elem.value == 'cnic_number')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //disable
            // $('#searchfield').attr("disabled","disabled");
        }

        else if (elem.value == 'designation')
        {
            //Hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
            //show
            document.getElementById('key-hide').style.display = "block";
            //enable
            // $('#searchfield').removeAttr("disabled");
        }
        else if (elem.value == 'posting')
        {
            //show
            document.getElementById('posting-hide').style.display = "block";
            //Hide
            document.getElementById('key-hide').style.display = "none";
            //Hide
            document.getElementById('utype').style.display = "none";
        }
        else if (elem.value == 'utype')
        {
            //show
            document.getElementById('utype').style.display = "block";
            //hide
            document.getElementById('posting-hide').style.display = "none";
            //Hide
            document.getElementById('key-hide').style.display = "none";
        }
    }
    function clearSearch() {
        window.location.href = '<?php echo URL::site('userreports/users_transferred_list', TRUE); ?>';
    }
    function excel(id){          
            $('#xport').val('excel');
            $('#search_form').submit();            
            $('#xport').val('');
    }
   function ConfirmChoice(id) 
    { 
        $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
       //var elem = $(this).parent().parent();// .closest('.action');
       // var elem = $(this).closest('tr[class^="action"]');       
        //var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title'     : 'Add Favourite confirmation',
            'message'   : 'Do you really want to Add this user as your Favourite user?' ,
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){        
                        $.ajax({url: '<?php echo URL::base() . "Userreports/addfavouriteuser/"; ?>'  + id , success: function(result){                                 
                                if (result == '-2') { 
                                    swal("System Error", "Contact Support Team.", "error");
                                } else if (result == '-3') { 
                                    swal("System Error", "Contact Support Team.", "error");
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
    
    function ConfirmChoiceBlock(id) {
        swal({
            title: "Reason Of Block User Required!",
            text: "Please Provide Reason of Block of this user",
            type: "input",
            showCancelButton: false,
            closeOnConfirm: false,
            allowEscapeKey: false,
            inputPlaceholder: "Please Provide Reason of Block of this user"
        }, function (inputValue) {
            if (inputValue === false)
                return false;
            if (inputValue === "") {
                swal.showInputError("Please Provide Reason of Block of this user!");
                return false
            }
            if (inputValue.length < 15) {
                swal.showInputError("Minimum 15 Characters Required!");
                return false
            }
            if (inputValue.length > 50) {
                swal.showInputError("Please Enter Not More than 50 Characters");
                return false
            }
            var isnum = /^[0-9a-zA-Z]/.test(inputValue);
            if (!isnum) {
                swal.showInputError("Only Number And Characters Allowed!");
                return false
            }
            var data = {reason: inputValue,id:id};
                         $.ajax({
                             url: "<?php echo URL::site("user/userblock"); ?>",
                                type: 'POST',
                                data: data,
                                cache: false,
                                //dataType: "text",
                                dataType: 'json',
                             success: function(result){ 
                                if (result == '-2') { 
                                    swal("System Error", "Contact Support Team.", "error");
                                    } 
                                    else if (result=='-3') {
                                        swal("Exception Error", "Contact Technical Support Team.", "error");
                                    }
                                    else{
                                        swal("Congratulations!!", "User Blocked Successfully!", "success");
                                        refreshGrid();
                                    }
                        }
                    });
        });
    }
    
     function convertpassword(id) 
    { 
        var elem = $(this).closest('.item');
        $.confirm({
            'title'     : 'Password Change Confirmation',
            'message'   : 'Do you really want to change this user password?',
            'buttons'   : {
                'Yes'   : {
                    'class' : 'blue',
                    'action': function(){
                         $.ajax({url: '<?php echo URL::base() . "user/convertpassword/"; ?>'  + id , 
                         success: function(msg){ 
                                if (msg == '-2') { 
                                    swal("System Error", "Contact Support Team.", "error");
                                } else if (msg=='-3') {
                                    swal("Exception Error", "Contact Technical Support Team.", "error");
                                }             
                         
                        refreshGrid();  
                    }
                     });
                    }
	                },
                'No'    : {
                    'class' : 'gray',
                    'action': function(){}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
   }
   function revert(id) 
    { 
        var elem = $(this).closest('.item');
        $.confirm({
            'title'     : 'Password Revert Confirmation',
            'message'   : 'Do you really want to revert this user password?',
            'buttons'   : {
                'Yes'   : {
                    'class' : 'blue',
                    'action': function(){
                     $.ajax({url: '<?php echo URL::base() . "user/revert/"; ?>'  + id , 
                         success: function(msg){ 
                                 if (msg == -2) {
                                    swal("System Error", "Contact Support Team.", "error");
                                }            
                         
                        refreshGrid();
                    
                    }
                     });
	                },
                            },
                'No'    : {
                    'class' : 'gray',
                    'action': function(){}  // Nothing to do in this case. You can as well omit the action property.
                
            }
            }
        });
   }
</script> 
