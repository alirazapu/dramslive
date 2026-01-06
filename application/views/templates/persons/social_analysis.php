<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 //get person assets dowload path
 $person_download_data_path = !empty(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) ? Helpers_Person::get_person_download_data_path(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")) : '';
 

?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Person's Portal
        <small><?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
    
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id='.$_GET['id']); ?>">Person Dashboard </a></li>                        
        <li class="active">Social Analysis</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <div class="alert " id="search_links" style="color: '#ff5b3c'; display: none;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> 
                        <span id='parsresult'> Be patient aies is searching social links... 
                            <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                        </span></h4>
                </div>
                <div class="form-group col-md-12 " >
                    <div class="alert-dismissible notificationcloseaffiliations" id="notification_msgaffiliations" style="display: none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divaffiliations"></div></h4>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header">
                        <span>
                            <h3 class="box-title"><i class="fa fa-mars-double"></i> 
                                Social Links
                            </h3>
                            <div style="display: none">
                                <button title="Let AIES to search social links on ineternet" id="startsearch" class="btn btn-primary pull-right" onclick="startsearch();">
                                    Start Auto Search
                                </button>
                            </div>
                        </span>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="sociallinkstable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th >Social Website</th>
                                        <th class="no-sort">Site ID</th>
                                        <th class="no-sort">Linked Mobile#</th>
                                        <th class="no-sort">Profile Link</th>
                                        <th class="no-sort">Suggested By</th>
                                        <th>Authenticity</th>
                                        <th >Updated On</th>
                                        <th class="no-sort">Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th >Social Website</th>
                                        <th >Site ID</th>
                                        <th >Linked Mobile#</th>
                                        <th >Profile Link</th>
                                        <th >Suggested By</th>
                                        <th>Authenticity</th>
                                        <th >Updated On</th>
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
<!--        acl right div-->
<div class="modal modal-info fade" id="sociallink_details">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" >
            <div class="modal-header" style="border-bottom:none !important;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Social Link Details</h4>
            </div>            
            <div class="modal-body" style='background-color: #00a7d0 !important ; color: #000 !important;'>

                <div class="form-group col-md-12" style="background-color: #fff; margin-bottom: -5px; margin-top: -20px;">
<!--                    <table>
                        <tbody>
                            <tr>
                                <td style="width: 30%"><label title="Person Website ID Like:Email, Phone#,Username" id="" style="text-align: left !important;" >Person Website ID</label></td>
                                <td style="width: 70%"><label title="Person Website ID Like:Email, Phone#,Username" id="" style="text-align: right !important;" >Person Website ID</label></td>
                            </tr>                            
                        </tbody>
                    </table>-->
                    <div class="row" style="padding-top: 5px;">
                        <div class="col-md-3">
                           <label title="Linked Social Website Name"  style="text-align: left !important;" >Social Website: </label> 
                        </div>
                        <div class="col-md-7">
                           <b title="Website name" id="md_website" style="text-align: left !important;" ></b> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                           <label title="Person Website ID Like:Email, Phone#,Username"  style="text-align: left !important;" >Person Website ID: </label> 
                        </div>
                        <div class="col-md-7">
                           <b title="Person Website ID Like:Email, Phone#,Username" id="md_pw_id" style="text-align: left !important;" ></b> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                           <label title="Person Social Website Profile Link" id="" style="text-align: left !important;" >Website Profile Link: </label> 
                        </div>
                        <div class="col-md-7">
                           <b title="Click to view profile details on website" id="md_pw_profile" style="text-align: left !important;" ></b> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                           <label title="Person Phone Number Linked With Website Account" id="" style="text-align: left !important;" >Phone Number: </label> 
                        </div>
                        <div class="col-md-7">
                           <b title="Person Phone Number Linked With Website Account" id="md_phone_number" style="text-align: left !important;" ></b> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                           <label title="Details attached by agent" id="" style="text-align: left !important;" >Attached File: </label> 
                        </div>
                        <div class="col-md-7">
                           <b title="Click here to download" id="md_linked_file" style="text-align: left !important;" ></b> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                           <label title="This social link is suggested by " id="" style="text-align: left !important;" >Suggested By: </label> 
                        </div>
                        <div class="col-md-7">
                           <b title="This social link is suggested by this agent" id="md_suggestedby" style="text-align: left !important;" ></b> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                           <label title="Either information is verified by agent or not" id="" style="text-align: left !important;" >Authenticity: </label> 
                        </div>
                        <div class="col-md-7">
                           <b title="Either information is verified by agent or not" id="md_authenticity" style="text-align: left !important;" ></b> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                           <label title="Last information update time" id="" style="text-align: left !important;" >Updated On: </label> 
                        </div>
                        <div class="col-md-7">
                           <b title="Last information update time" id="md_updatedon" style="text-align: left !important;" ></b> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                           <label title="Other detailed information about this social link" id="" style="text-align: left !important;" >Other Details: </label> 
                        </div>
                        <div class="col-md-7">
                           <b title="Other detailed information about this social link" id="md_information" style="text-align: left !important;" ></b> 
                        </div>
                    </div>
                </div>                            

                </div>
            <div class="modal-footer" style="margin-top: auto;border-top:none !important;padding: 15px !important;">
                <button style="margin-top: 15px !important;" type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>                    
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
    });
    
    //social media site details
    function view_link_details(record_id) {
        if (record_id !== '')
        {
            
            
            var request = $.ajax({
                url: "<?php echo URL::site("socialanalysis/view_link_details"); ?>",
                type: "POST",
                dataType: 'json',
                data: {id: record_id},
                success: function (text)
                {
                    if(text==2){
                                    swal("System Error", "Contact Support Team.", "error");
                                    exit;
                                }

                    $("#sociallink_details").modal("show");

                    //appending modal background inside the blue div
                    $('.modal-backdrop').appendTo('.blue');

                    //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
                    $('body').removeClass("modal-open");
                    $('body').css("padding-right", "");
                    setTimeout(function () {
                        // Do something after 1 second     
                        $(".modal-backdrop.fade.in").remove();
                    }, 300);
                    // data in fields
                    var websitename='';
                    if(text.website_image!==""){
                     var link1= '<img src="<?php echo URL::base(); ?>/dist/img/social_websites/'  ;
                     var link2= text.website_image  ;
                     var link3= '" height="50%" width="50%" />' ;
                     websitename=link1+link2+link3;
                    }else{
                    websitename=text.website_name;
                    }
                    $("#md_website").html(websitename);
                    $("#md_pw_id").html(text.person_sw_id);
                    if(text.profile_link!=='' || text.profile_link!==0){
                    var prolink1='<a target="_blank" href="http:\//';
                    var prolink2=text.profile_link;
                    var prolink3='">';
                    var prolink4='</a>';
                    var prolink=prolink1+prolink2+prolink3+"View Profile On Website"+prolink4;
                }else{
                    prolink='NA';
                    }
                    
                    $("#md_pw_profile").html(prolink);
                    var mobile='';
                    if(text.phone_number!==0 || text.phone_number!==''){
                        mobile=text.phone_number;
                    }else{
                        mobile="NA";
                    }
                    $("#md_phone_number").html(mobile);
                    if(text.file_link !='' && text.file_link != 0){
                    var filelink1='<a target="_blank" href=" <?php echo $person_download_data_path; ?>';
                    var filelink2=text.file_link;
                    var filelink3='"> Download '+text.file_link+'</a>';
                    var filelink=filelink1+filelink2+filelink3;
                }else{
                    filelink='NA';
                    }
                    $("#md_linked_file").html(filelink);
                    var suggested=text.suggested_by;
                    if(suggested===0 || suggested===''){
                        suggested="AIES";
                    }else{
                        suggested="Agent";
                    }
                    $("#md_suggestedby").html(suggested);
                    var auth=text.authenticity;
                    if(auth===0 || auth===""){
                        auth="Not Verified"
                    }else{
                        auth="Verified"
                    }
                    $("#md_authenticity").html(auth);
                    $("#md_updatedon").html(text.time_stamp);
                    $("#md_information").html(text.information);
                    

                },
                error: function (jqXHR, textStatus) {
                    alert('Unable To Provide Info');
                   // $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }
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
        objDT = $('#sociallinkstable').dataTable(
                {"aaSorting": [[6, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('socialanalysis/ajaxsociallinks?id='.$_GET['id'], TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Social website or Site ID:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );

        $('.dataTables_empty').html("Information not found");

    });
      
   
        $.validator.addMethod("check_list1", function (sel, element) {
        if (sel == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");
    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "def") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("key_value", function (sel, element) {
        if ($('#field').val() == "location" && $('#searchfield').val() == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

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
    
    //control aies auto search
var controlsearch=1;
function startsearch() {
    if(controlsearch==1){
$("#search_links").show();
$("#startsearch").html("Stop Search");
$("#startsearch").removeClass("btn-primary");
$("#startsearch").addClass("btn-danger");
 controlsearch=2;
    }else{
      $("#search_links").hide();
$("#startsearch").html("Start Auto Search");
$("#startsearch").removeClass("btn-danger");
$("#startsearch").addClass("btn-primary");
 controlsearch=1;
    }
    }
    
    //delete entries
function deletelink(id,sw) {
 //function to delete record

         $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
       //var elem = $(this).parent().parent();// .closest('.action');
       // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title'     : 'Delete Confirmation',
            'message'   : 'Do you really want to delete '+sw+' ?',
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){        
                        $.ajax({url: '<?php echo URL::base() . "socialanalysis/delete_link/"; ?>'  + id , 
                     success: function(msg){    
                            var elem = $(".notificationcloseaffiliations");
                            if(msg==1){
                                    swal("Congratulations!", "Record deleted successfully.", "warning");
                                }
		 					else if(msg==2){
                                    swal("System Error", "Contact Support Team.", "error");
                                }
                            refreshGrid();
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
    
    //verify record
function approverecord(id) {
 //function to delete record

         $(".odd").addClass('actiontd');
        $(".even").addClass('actiontd');
       //var elem = $(this).parent().parent();// .closest('.action');
       // var elem = $(this).closest('tr[class^="action"]');       
        var elem = $(".item-" + id).closest('.actiontd');
        $.confirm({
            'title'     : 'Update Confirmation',
            'message'   : 'Are you sure record is correct?',
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){        
                        $.ajax({url: '<?php echo URL::base() . "socialanalysis/approve_record/"; ?>'  + id , 
                     success: function(msg){    
                            var elem = $(".notificationcloseaffiliations");
                            if(msg==1){
                                    swal("Congratulations!", "Record approved successfully.", "success");
                                }
		 		else if(msg==2){
                                    swal("System Error", "Contact Support Team.", "error");
                                }

                            refreshGrid();
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