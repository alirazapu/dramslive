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
        <i class="fa fa-whatsapp"></i>
        Request  
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Request</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#emailmanager" data-toggle="tab">Email Manager</a></li>
                    <li><a href="#apimanager" data-toggle="tab">API Manager</a></li>
                </ul>
                <div class="tab-content">                   
                    <!-- /.tab-pane -->
                    <div class="active tab-pane" id="emailmanager"> 
                        <?php
                        $person_id = !empty($post['pid']) ? $post['pid'] : 0;

                        $request = !empty($post['request']) ? $post['request'] : "na";
                        $requesttype = !empty($post['requesttype']) ? $post['requesttype'] : 0;
                        $msisdn = !empty($post['msisdn']) ? $post['msisdn'] : 0;
                        $ptcl = !empty($post['ptclnumber']) ? $post['ptclnumber'] : 0;
                        $international = !empty($post['internationalnumber']) ? $post['internationalnumber'] : 0;                        
                        $cnic = !empty($post['cnic']) ? $post['cnic'] : 0;
                        $imsi = !empty($post['imsi']) ? $post['imsi'] : 0;
                        $imei = !empty($post['imei']) ? $post['imei'] : 0;
                        $startdate = !empty($post['startdate']) ? $post['startdate'] : 0;
                        $enddate = !empty($post['enddate']) ? $post['enddate'] : 0;
                        $reference = !empty($post['reference']) ? $post['reference'] : 0;
                        $projectid = !empty($post['projectid']) ? $post['projectid'] : 0;
                        //  echo $msisdn;
                        ?>
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">E-Mail Manager </h3>
                            </div> 
                            <form class="ipf-form request_net" name="requestform" action="<?php echo url::site() . 'email/send' ?>" id="userrequest" method="post" enctype="multipart/form-data" >
                                <div class="box-body">                                    
                                    <div class="alert " id="upload" style="color: '#ff5b3c'; display: none">
<!--                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>-->
                                        <h4><i class="icon fa fa-check"></i> 
                                            <span id='parsresult'> Be Patient request in process 
                                                <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                                            </span></h4>
                                    </div>                                    
                                    <div class="alert" id="request_permission_check_status" style="color: '#ff5b3c'; display: none">
                                       <h4><i class="icon fa fa-check"></i> 
                                            <span id='parsresult'> Be Patient ! Preparing Request....
                                                <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                                            </span></h4>
                                    </div>                                    
                                    <div class="form-group col-md-12 " >
                                        <div class="" id="notification_msgbasic1" style="display: none;">
                                            <h4><div id="notification_msg_divbasic1"></div></h4>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-12 " >
                                        <div class="alert-dismissible notificationclosebasic" id="notification_msgbasic" style="display: none;">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divbasic"></div></h4>
                                        </div>
                                        <input type="hidden" value="0" id="requestformcontroler" />
                                        <input type="hidden" value="0" id="requestformcontroler_startdate" />
                                        <input type="hidden" value="0" id="requestformcontroler_enddate" />
                                        <input type="hidden" value="0" id="requestformcontroler_mnc" />
                                    </div>
                                    <div class="col-sm-12 ">
                                        <?php try{
                                        //$id==1 means no person existed in db, record is new where as $id=2 means person existed in db and request is for record upload 
                                        if ($request == "existing" && !empty($post['pid'] && $post['pid'] != -1)) {
                                            $prof = Helpers_Person::get_person_perofile($post['pid']);
                                            $person_name=!empty($prof->first_name ) ? $prof->first_name . " " . $prof->last_name : "Unknown";
                                            ?>
                                            <div >
                                                <h4><b>You Are Requesting For: <?php /* echo $person_id; */ ?></b></h4>

                                            </div> 
                                            <div class="col-sm-6 ">
                                                <ul class="todo-list">
                                                    <li >
                                                        <a href="<?php echo URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($post['pid'],"encrypt")); ?>"> <span class="text-black"> <b>Name:</b> <?php echo $person_name; ?> </span><span class="active">(Profile)</span></a>
                                                    </li>
                                                    <li >
                                                        <span class="text-black"> <b>Father/Husband Name:</b><?php echo $prof->father_name; ?> </span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-sm-6 ">
                                                <ul class="todo-list">
                                                    <li>
                                                        <span class="text-black"> <b>CNIC: </b>  <?php echo Helpers_Person::get_person_cnic($post['pid']); ?> </span>
                                                       
                                                    </li>
                                                    <li >
                                                        <span class="text-black"> <b>Address: </b><?php echo $prof->address; ?></span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <hr class="style14 col-md-12"> 
                                            <?php
                                            //$id==1 means no person existed in db, record is new where as $id=2 means person existed in db and request is for record upload 
                                        } else {
                                            ?>
                                            <div>
                                                <h4><b>You Are Requesting For New Person:</b></h4>
                                                <hr class="style14 col-md-12"> 
                                            </div> 
                                                <?php
                                            }
                                          }  catch (Exception $ex){   }  ?>
                                    </div>  
                                    <div class="col-sm-6">
                                        <div class="form-group">  

                                            <label for="ChooseTemplate" class="control-label">Request Type <?php // echo $request.",".$msisdn.",".$requesttype.",".$pid ;  ?></label> 
                                                <?php try{
                                                $rqts = Helpers_Utilities::emailtemplatetype();
                                                ?>
                                            <input type="hidden" name="person_id" value="<?php echo $person_id; ?>" />
                                            <select class="form-control" id="ChooseTemplate" name="ChooseTemplate" onchange="showDiv(this, 1)"  <?php if ($requesttype != 0) {
                                                echo "readonly";
                                            } 
                                           }  catch (Exception $ex){   } ?>>
                                                <!--                                                <option value="0">Please select Request Type</option>-->
                                                <?php
                                                foreach ($rqts as $rqt) {
                                                    if ($request == "existing") { //request page id for the option to create new person
                                                        if ($rqt['id'] == $requesttype) { ?>
                                                            <option value="<?php echo $rqt['id']; ?>" <?php if ($rqt['id'] == $requesttype) { echo "selected"; } ?>><?php echo $rqt['id'] . "- "; echo $rqt['email_type_name']; ?></option>        
                                                            <?php                                                            
                                                            }
                                                    }else {
                                                        if ($rqt['id'] == $requesttype) { ?>
                                                            <option value="<?php echo $rqt['id']; ?>" <?php if ($rqt['id'] == $requesttype) { echo "selected"; } ?>><?php echo $rqt['id'] . "- "; echo $rqt['email_type_name']; ?></option>        
                                                            <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>                                                                                    
                                        </div>
                                    </div>                            
                                    <hr class="col-md-12 style14"  />
                                    <div class="col-sm-6" id="sub_div" style="display: none">
                                        <div class="form-group" style="padding-top: 15px">                                                            
                                            <label for="inputSubNO" class="control-label">Subscriber No</label>                                                                                                                        
                                            <input type="text" class="form-control" name="inputSubNO" id="inputmsisdn" value="<?php if ($msisdn != 0) {
                                                    echo $msisdn;                                                    
                                                } ?>" placeholder="e.g. 3001234567"  <?php if ($msisdn != 0) {
                                                    echo "readonly";
                                                } ?>>
                                        </div>
                                    </div>
                                    <div style="display:none">
                                        <?php 
                                            if(!empty($msisdn) && $msisdn != 0)
                                            {
                                                $company_mnc_te = '';
                                                $company_mnc_te = Helpers_Utilities::search_mnc_ofmobile($msisdn);
                                                if(!empty($company_mnc_te))
                                                { $company_mnc_tel = $company_mnc_te->mnc; 
                                                }else {
                                                     $company_mnc_tel = '';   
                                                }
                                            }else if(!empty($ptcl) && $ptcl != 0){
                                                $company_mnc_tel = 11;
                                            }else if(!empty($international) && $international != 0){
                                                $company_mnc_tel = 12;
                                            }else if(!empty($requesttype) && $requesttype == 10){
                                                $company_mnc_tel = 13;
                                            }
                                            else{
                                                $company_mnc_tel = '';                                                
                                            }  
                                            echo $company_mnc_tel;
                                        ?>
                                    </div>    
                                    <div class="col-sm-6" id="company_div" style="display: none">
                                        <div class="form-group"  >
                                            <label for="company_name_get" class="control-label"><span>Company Name 
                                                    <img id='findcompnay_image' src="<?php echo URL::base(); ?>dist/img/102.gif" style="width: 38px;height: 33px; display: none;">
                                                    <?php if(empty($company_mnc_tel)){ ?>
                                                        <a id="findcompanyname"  class="btn" onclick="findcompanyname();"> Search Network (Click) </a></span></label>
                                                    <?php }else{ ?>
                                                        <a id="findcompanyname"  class="btn"> Company Name Existed </a></span></label>    
                                                    <?php } ?>
                                            <select readonly class="form-control <?php // if($requesttype==2 OR $requesttype==5){echo "select2"; }  ?>" name="company_name_get[]" id="company_name_get" style="width: 100%;" <?php // if($requesttype==2 OR $requesttype==5){echo "multiple"; }  ?>>
                                                <option value="" >Please Select Company</option>
                                                    <?php try{
                                                    $comp_name_list = Helpers_Utilities::get_companies_data();
                                                    foreach ($comp_name_list as $list) {
                                                        if($list->mnc==13 && ($requesttype==8 || $requesttype ==10))
                                                        {
                                                            ?>
                                                            <option value="<?php echo $list->mnc ?>" <?php echo ($company_mnc_tel==$list->mnc)?" selected ":""; ?> ><?php echo $list->company_name ?></option>
                                                            <?php
                                                        }    
                                                        elseif($list->mnc==11 && ($requesttype==7 || $requesttype==11))
                                                        {
                                                            ?>
                                                            <option value="<?php echo $list->mnc ?>" <?php echo ($company_mnc_tel==$list->mnc)?" selected ":""; ?> ><?php echo $list->company_name ?></option>
                                                            <?php
                                                        }    
                                                        elseif($list->mnc==12 && $requesttype==9)
                                                        {
                                                            ?>
                                                            <option value="<?php echo $list->mnc ?>" <?php echo ($company_mnc_tel==$list->mnc)?" selected ":""; ?> ><?php echo $list->company_name ?></option>
                                                            <?php
                                                            
                                                        }elseif(($list->mnc>=1 && $list->mnc<=7) && ($requesttype>=1 && $requesttype<=6)){
                                                            ?>
                                                            <option value="<?php echo $list->mnc ?>" <?php echo ($company_mnc_tel==$list->mnc)?" selected ":""; ?> ><?php echo $list->company_name ?></option>
                                                            <?php
                                                        }    
                                                      }
                                                      }  catch (Exception $ex){   }?>

                                            </select>
                                        </div>


                                    </div>
                                    <div class="col-sm-6" id="ptcldiv" style="display: none">
                                        <div class="form-group" >                                                            
                                            <label for="inputPTCLNO" class="control-label">PTCL No</label>                                                                                                                        
                                            <input type="text" class="form-control" name="inputPTCLNO" id="inputPTCLNO" value="<?php if (!empty($ptcl)) { echo $ptcl; } ?>" placeholder="PTCL Number"  <?php if (!empty($ptcl)) { echo "readonly"; } ?>>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-6" id="internationaldiv" style="display: none">
                                        <div class="form-group" >                                                            
                                            <label for="inputInternationalNo" class="control-label">Internation Number</label>                                                                                                                        
                                            <input type="text" class="form-control" name="inputInternationalNo" id="inputInternationalNo" value="<?php if (!empty($international)) { echo $international; } ?>" placeholder="International Number"  <?php if (!empty($international)) { echo "readonly"; } ?>>
                                        </div>
                                        
                                    </div>
                                    <div class="col-sm-6" id="cnic_div" style="display: none">
                                        <div class="form-group" >                                                        
                                            <label for="inputCNIC" class="control-label">CNIC: </label>                                                      
                                            <input type="text" class="form-control" name="inputCNIC" id="inputCNIC" value="<?php if (!empty($cnic)) { echo $cnic; } ?>" placeholder="CNIC"  <?php if (!empty($cnic)) { echo "readonly"; } ?>>                                                        
                                        </div>
                                    </div>
                                    <div class="col-sm-6" id="imei_div" style="display: none">
                                        <div class="form-group" >                                                        
                                            <label for="inputIMEI" class="control-label">IMEI Number</label>

                                            <input type="text" class="form-control" name="inputIMEI" id="inputIMEI" value="<?php if ($imei != 0) {
                                                echo $imei;
                                            } ?>" placeholder="IMEI Number" <?php if ($imei != 0) {
                                                echo "readonly";
                                            } ?>>
                                        </div> 
                                    </div>  
                                    <div class="col-sm-6" id="imsi_div" style="display: none">
                                        <div class="form-group" >                                                        
                                            <label for="inputIMSI" class="control-label">IMSI Number</label>                                                                                                         
                                            <input type="text" class="form-control" name="inputIMSI" id="inputIMSI" value="<?php if ($imsi != 0) {
                                                    echo $imsi;
                                                } ?>" placeholder="IMSI Number" <?php if ($imsi != 0) {
                                                    echo "readonly";
                                                } ?>>                                                        
                                        </div> 
                                    </div>
                                    <div class="col-sm-6" id="quickoption_div" style="display: none">
                                        <div class="form-group" >
                                            <label for="quickoption" class="control-label">Quick Options (for start and End Date)</label>
                                            <div class="col-md-12" id="quickoption">
                                                <button type="button" onclick="dateonemonth()" class="btn btn-primary" >Last 30 Days </button>                                            
                                                <button type="button" onclick="datetwomonths()" class="btn btn-primary" >Last 60 Days </button>                                            
                                                <button type="button" onclick="datethreemonths()" class="btn btn-primary" >Last 90 Days </button>
                                                <button type="button" onclick="datesixmonths()" class="btn btn-primary" >Last 180 Days</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3" id="datefrom_div" style="display: none">
                                        <div class="form-group" >
                                            <label for="startDate" class="control-label">Date From (mm/dd/yyyy)</label>
                                            <input type="text"  readonly="readonly" class="form-control" name="startDate" id="startDate" value="<?php if ($startdate != 0) {
                                                     echo $startdate;
                                                    } ?>" placeholder="mm/dd/yyyy" <?php if ($startdate != 0) {
                                                echo "disabled";
                                            } ?>>
                                        </div>
                                    </div>
                                    <div class="col-sm-3" id="dateto_div" style="display: none">
                                        <div class="form-group" >
                                            <label for="endDate" class="control-label">Date To (mm/dd/yyyy)</label>
                                            <input type="text" readonly="readonly" class="form-control" name="endDate" id="endDate" value="<?php if ($enddate != 0) {echo $enddate;} ?>" placeholder="mm/dd/yyyy" <?php if ($enddate != 0) { echo "disabled";} ?>>
                                        </div>
                                    </div>
                                    <div class="col-sm-12" id="reference_div" style="display: none">
                                        <div class="form-group" >                                                            
                                            <label for="inputSubject" class="control-label">Reference</label>
                                            <textarea class="form-control" name="inputreference" id="inputreference"  placeholder="Enter References.." <?php if ($reference != 0) {
                                                echo "disabled";
                                            } ?>><?php echo $reference; ?></textarea>                                                          
                                        </div>
                                    </div>
                                    <div class="col-sm-12" id="project_div" style="display: none;margin-top: 10px; ">
                                        <div class="form-group">                                                            
                                            <label for="inputproject" class="control-label">Linked Project</label> 
                                        <?php try{
                                        $rqts = Helpers_Utilities::get_projects_list();
                                        ?>
                                            <select class="form-control select2" name="inputproject[]" id="inputproject" style="width: 100%!important;">
                                                <option value="">Please select project name</option>
                                        <?php foreach ($rqts as $rqt) { ?>
                                                    <option <?php
                                        if ($projectid == $rqt->id) {
                                            echo 'Selected';
                                        }
                                        ?>
                                            value="<?php echo $rqt->id; ?>"><?php echo $rqt->project_name . " [" . $rqt->name . "]"; ?></option>
                                        <?php }
                                        }  catch (Exception $ex){   }?>                                                                                                                
                                            </select>                                                                                    
                                        </div>
                                    </div>
                                    <div class="col-sm-12" id="reason_div" style="display: none;margin-top: 10px">
                                        <div class="form-group" >                                                            
                                            <label for="inputreason" class="control-label">Reason For This Request</label>
                                            <textarea class="form-control" name="inputreason" id="inputreason"  placeholder="Enter Reason For Request" ></textarea>                                                          
                                        </div>
                                    </div>
                                    <div class="form-group" id="submit_div" style="display: none">
                                        <div class="col-sm-12">
                                            <button  id="userrequestbtn" type="submit" class="btn btn-primary pull-right" style="margin-top:10px" >Submit</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 callout callout-success" id="request_div">
                                    <?php if ($request == "existing") { ?>
                                            <h4>Data Request From Company!</h4>
                                            <div class="request_continue_button">
                                            <p>Once request is ready then please click on proceed button.</p>
                                            <button id="request_continue_button"  type="button" onclick="showDiv(<?php echo $requesttype; ?>, 2)" class="btn btn-primary pull-right" style="" >Continue</button>
                                            </div>
                                     <?php } else { ?>

                                            <h4 >Data Request From Company!</h4>
                                            <div class="request_continue_button">
                                            <p>Once request is ready then please click on proceed button.</p>
                                            <button id="request_continue_button1" type="button" onclick="showDiv(<?php echo $requesttype; ?>, 2)" class="btn btn-primary pull-right" style="" >Continue</button>
                                            </div>
                                    <?php } ?>
                                    </div> 
                                </div>
                            </form>
                        </div>                                            
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="apimanager">
                        <div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">API Manager</h3>
                            </div>
                            <form class="">
                                <div class="box-body">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <label for="apiurl" class="control-label">API URL</label>
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="url" class="form-control" id="apiurl" placeholder="API Link" disabled>
                                        </div>
                                    </div> 
                                    <div class="col-sm-12">
                                        <div class="form-group">

                                            <button  type="submit" class="btn btn-primary" style="margin-top:10px" disabled>Submit</button>                                                           
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>

        </div>
    </div>

</section>
<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>
<script>
    
$('#userrequest').one('submit', function() {
    $(this).find('input[type="submit"]').attr('disabled','disabled');
});
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();
        });
</script>
<script>
    function findcompanyname() {
        var subnumber = $("#inputmsisdn").val();
        if (subnumber == '')
        {
            alert('Subscriber Number is empty');
        } else {

            $("#findcompnay_image").show();
            $("#findcompanyname").html('');
            var request = $.ajax({
                url: "<?php echo URL::site("upload/checkcompany"); ?>",
                type: "POST",
                dataType: 'text',
                data: {number: subnumber},
                success: function (responseTex)
                {
                    if (responseTex == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    if (responseTex == -1)
                    {
                        alert('Failed to recognize');
                        $("#company_name_get").attr("readonly", false);

                    } else {
                        $("#findcompnay_image").hide();
                        $("#company_name_get").val(responseTex).trigger('change');
                        // $("#company_name_get").val(responseTex);
                    }
                    $("#findcompanyname").html('Again Search Network');
                    //$("#findcompanyname").css('pointer-events', 'none');
                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize, Please Select Manually');
                    $("#company_name_get").attr("readonly", false);
                    $("#findcompanyname").html('Search Network (Click)');
                }
            });
        }
    }

</script>
<script type="text/javascript">
    $(document).ready(function () {
        //function to compare dates
        function toTimestamp(myDate){
        //var datum = Date.parse(strDate);
        //return datum/1000;
       // var myDate="26-02-2012";
//myDate="'" + myDate + "'";
myDate=myDate.split("/");
//var newDate=myDate[1]+"/"+myDate[0]+"/"+myDate[2];
var newDate=myDate[0] + " " + myDate[1] + " "  + myDate[2];
var result=  (new Date(newDate).getTime()); //will alert 1330210800000
//console.log((newDate));
//console.log(result);
return (Date.parse(newDate)); //will alert 1330210800000
//return (Date.parse(newDate)); //will alert 1330210800000
        }
        // start initial request controller
                $('.request_continue_button').hide();  
                $('#request_permission_check_status').show();  
                $('#request_continue_button').attr("disabled","true");
                $('#request_continue_button1').attr("disabled","true");                               
               var requesttype=$("#ChooseTemplate").val();
               var msisdn=$("#inputmsisdn").val();
               var ptclno=$("#inputPTCLNO").val();
               var cnic=$("#inputCNIC").val();
               var imei=$("#inputIMEI").val();
               var mnc=$("#company_name_get").val();
               var startdate=$("#startDate").val();
               var enddate=$("#endDate").val();
                var result = {requesttype: requesttype,msisdn:msisdn,cnic:cnic,imei:imei,mnc:mnc,startdate:startdate,enddate:enddate,ptclnumber:ptclno}
                    //ajax to upload device informaiton
                    $.ajax({
                        url: "<?php echo URL::site("userrequest/sendrequestpermission"); ?>",
                        type: 'POST',
                        data: result,
                        cache: false,
                        success: function (result) {
                           if (result == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			} 
                          var result = JSON.parse(result);
                          var  request_permission=result.permission;
                          var  request_message=result.message;
                          var  request_startdate=result.startdate;
                          var  request_enddate=result.enddate;
                          var  request_type=result.request_type;
                          var  request_mnc=result.mnc;
                            $('#request_permission_check_status').hide();                            
                            if(request_permission==-1){
                             $("#notification_msg_divbasic1").html(request_message);
                            $("#notification_msgbasic1").show();
                            $("#notification_msgbasic1").addClass('alert');
                            $("#notification_msgbasic1").addClass('alert-danger');
                            }else if(request_permission==0){      
                                //alert(request_permission);
                               $("#requestformcontroler").val("1"); 
                               $('.request_continue_button').show();  
                             //  $("#requestformcontroler_mnc").val(request_mnc);
                                $("#notification_msg_divbasic1").html(request_message);
                                $("#notification_msgbasic1").show();
                                $("#notification_msgbasic1").addClass('alert');
                                $("#notification_msgbasic1").addClass('alert-success-1');
                                $('#request_continue_button').attr("disabled","false");
                                $('#request_continue_button1').attr("disabled","false");
                                $('#request_continue_button').trigger("click");
                                $('#request_continue_button1').trigger("click");      
                                // request form controller flag, permission is granted to submit request
                            }else if(request_permission==1){
                            $("#notification_msg_divbasic1").html(request_message);
                            $("#notification_msgbasic1").show();
                            $("#notification_msgbasic1").addClass('alert');
                            $("#notification_msgbasic1").addClass('alert-danger');
                            }else if(request_permission==2){   
                                $('.request_continue_button').show();  
                               $("#requestformcontroler_startdate").val(request_startdate);
                               $("#requestformcontroler_enddate").val(request_enddate);
                            $("#notification_msg_divbasic1").html(request_message);
                                $("#notification_msgbasic1").show();
                                $("#notification_msgbasic1").addClass('alert');
                                $("#notification_msgbasic1").addClass('alert-warning');
                                $('#request_continue_button').attr("disabled","false");
                                $('#request_continue_button1').attr("disabled","false");
                                $('#request_continue_button').trigger("click");
                                $('#request_continue_button1').trigger("click"); 
                            }else{
                            $("#notification_msg_divbasic1").html("Unknown Error Occured! Please contact with Technical Support Team");
                            $("#notification_msgbasic1").show();
                            $("#notification_msgbasic1").addClass('alert');
                            $("#notification_msgbasic1").addClass('alert-danger'); 
                                
                            }
                        }
                    }); 
    // close intital request controller

        $("#userrequest").validate({
            rules: {
                ChooseTemplate: {
                    required: true,
                    check_list: true
                },
                "company_name_get[]": {
                    required: true,
                    check_list: true
                },
                "inputproject[]": {
                    required: true,
                    check_list: true
                },
                inputSubNO: {
                    required: true,
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                },
                inputCNIC: {
                    required: true,
                   // number: true,
                    minlength: 13,
                    maxlength: 13
                },
                inputIMEI: {
                    required: true,
                    number: true,
                    minlength: 15,
                    maxlength: 15
                },
                inputIMSI: {
                    required: true,
                    number: true,
                    minlength: 19,
                    maxlength: 19
                },
                startDate: {
                    required: true,
                    vailddate: true
                },
                endDate: {
                    required: true,
                    greaterThan: "#startDate"
                },
                inputreference: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 20
                },
                inputreason: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 5,
                    maxlength: 500
                }
            },
            messages: {
                ChooseTemplate: {
                    required: "Select Request Type"
                },
                company_name_get: {
                    required: "Select atleast one company"
                },
                inputproject: {
                    required: "Select atleast one project"
                },
                inputSubNO: {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },                
                inputCNIC: {
                    required: "Enter CNIC Number",
                  //  Number: "Only number without dashes",
                    maxlenght: "Number should be 13 digits",
                    minlength: "Minimum 13 digits"
                },
                inputIMEI: {
                    required: "Enter IMEI Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum character limit is 15",
                    minlength: "Minimum 15 digits"
                },
                inputIMSI: {
                    required: "Enter IMSI Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum character limit is 19",
                    minlength: "Minimum 19 digits"
                },
                startDate: {
                    required: "Enter date from"
                },
                endDate: {
                    required: "Enter date to"
                },
                inputreference: {
                    required: "Enter request reference",
                    maxlenght: "Maximum character limit is 20",
                    minlength: "Min character limit is 1"
                },
                inputreason: {
                    required: "Enter reason for request",
                    maxlenght: "Maximum character limit is 500",
                    minlength: "Min character limit is 10"
                }
            },

            submitHandler: function () {
              //  $('#upload').show();
               var requestformcontroler=$("#requestformcontroler").val();
             //  alert("form controler value " + requestformcontroler);
               if(requestformcontroler == 0){
              // var requesttype=$("#ChooseTemplate").val();
              // var msisdn=$("#inputmsisdn").val();
              // var cnic=$("#inputCNIC").val();
              // var imei=$("#inputIMEI").val();
             //  var mnc=$("#company_name_get").val();
               var startdate=$("#startDate").val();
               var enddate=$("#endDate").val();               
               var cdr_startdate=$("#requestformcontroler_startdate").val();
               var cdr_enddate=$("#requestformcontroler_enddate").val(); 
              // alert("end date input " + cdr_enddate);
              //  console.log('Dates: input start' + (startdate) + " input end "+ (enddate) + " cdr end " + (cdr_enddate)  + " cdr start "+ (cdr_startdate));
              //  console.log('Dates: input start' + toTimestamp(startdate) + " cdr end " + toTimestamp(cdr_enddate)  + " cdr start "+ toTimestamp(cdr_startdate) + " input end "+ toTimestamp(enddate));
               if((toTimestamp(startdate) > toTimestamp(cdr_enddate)) && (toTimestamp(enddate) > toTimestamp(cdr_enddate))){
                  // alert('dates are grater allowed: input' + startdate + " > " + cdr_enddate +" && "+ enddate + " > "+ cdr_enddate);
                            $('#upload').show();
                            $("#userrequest").submit();
                            // $("#userrequestbtn").trigger("click");
                 }else if((toTimestamp(startdate) < toTimestamp(cdr_startdate)) && (toTimestamp(enddate) < toTimestamp(cdr_startdate))){
                            $('#upload').show();
                         // alert('dates are less allowed: input ' + startdate + " < " + cdr_startdate +" && "+ enddate + " < "+ cdr_startdate);
                            $("#userrequest").submit();
                           // $("#userrequestbtn").trigger("click");
                 }else {
                            $("#notification_msg_divbasic").html('Selected dates fall in prohibited duration');
                            $("#notification_msgbasic").show();
                            $("#notification_msgbasic").addClass('alert');
                            $("#notification_msgbasic").addClass('alert-danger');
                            var elem = $(".notificationclosebasic");
                            elem.slideUp(20000);                
                 }
                 }else{                       
                   // var db_mnc=$("#requestformcontroler_mnc").val(); 
                    $('#upload').show();  
                     $("#userrequest").submit();
                 
                 } // condition to control duplicate
                 
            }
               
            // $('#upload').show()
        });


        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "" || sel == 0) {
                return false;
            } else {
                return true;
            }
        }, "<span>Select One</span>");

        jQuery.validator.addMethod("vailddate",
                function (value, element) {
                    var isValid = false;
                    var reg = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
                    if (reg.test(value)) {
                        var splittedDate = value.split('/');
                        var mm = parseInt(splittedDate[0], 10);
                        var dd = parseInt(splittedDate[1], 10);
                        var yyyy = parseInt(splittedDate[2], 10);
                        var newDate = new Date(yyyy, mm - 1, dd);
                        if ((newDate.getFullYear() == yyyy) && (newDate.getMonth() == mm - 1)
                                && (newDate.getDate() == dd))
                            isValid = true;
                        else
                            isValid = false;
                    } else
                        isValid = false;
                    return this.optional(element) || isValid;
                },
                "Please enter a valid date (mm/dd/yyyy)");

        $.validator.addMethod("vailddated1", function (value, element) {
            var check = false,
                    re = /^\d{1,2}\/\d{1,2}\/\d{4}$/,
                    adata, gg, mm, aaaa, xdata;
            if (re.test(value)) {
                adata = value.split("/");
                gg = parseInt(adata[0], 10);
                mm = parseInt(adata[1], 10);
                aaaa = parseInt(adata[2], 10);
                xdata = new Date(aaaa, mm - 1, gg, 12, 0, 0, 0);
                if ((xdata.getUTCFullYear() === aaaa) && (xdata.getUTCMonth() === mm - 1) && (xdata.getUTCDate() === gg)) {
                    check = true;
                } else {
                    check = false;
                }
            } else {
                check = false;
            }
            return this.optional(element) || check;
        }, "Please enter a correct date");

        jQuery.validator.addMethod("greaterThan",
                function (value, element, params) {

                    if (!/Invalid|NaN/.test(new Date(value))) {
                        return new Date(value) > new Date($(params).val());
                    }
                    return isNaN(value) && isNaN($(params).val())
                            || (Number(value) > Number($(params).val()));
                }, 'Must be greater than ( Date From )');


        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .,/]+$/);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");

    });
    //Date picker
    var today = new Date();
    $('#startDate').datepicker({
        endDate: "today",
        maxDate: today,
        autoclose: true
    });
    var today = new Date();
    $('#endDate').datepicker({
        endDate: "today",
        maxDate: today,
        autoclose: true
    });
    var changecount = 1;

    function dateonemonth() {    
        var today = currentdate();
        var onemonthago = backdate(30);
        document.getElementById('endDate').value = today;               
        document.getElementById('startDate').value = onemonthago;               
    }
    function datetwomonths() {
        var today = currentdate();
        var twomonthsago = backdate(60);
        document.getElementById('endDate').value = today;               
        document.getElementById('startDate').value = twomonthsago; 
    }
    function datethreemonths() {
        var today = currentdate();
        var threemonthsago = backdate(86);
        document.getElementById('endDate').value = today;               
        document.getElementById('startDate').value = threemonthsago;
    }
    function datesixmonths() {
        var today = currentdate();
        var sixmonthago = backdate(170);
        document.getElementById('endDate').value = today;               
        document.getElementById('startDate').value = sixmonthago;
    }
    function currentdate(){
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        
        if(dd<10) {
            dd = '0'+dd
        } 
        if(mm<10) {
            mm = '0'+mm
        } 
        today = mm + '/' + dd + '/' + yyyy;
        return today;
    }
    function backdate(value){
        var beforedate = new Date();
        var priordate = new Date();
        priordate.setDate(beforedate.getDate()-value);
        var dd2 = priordate.getDate();
        var mm2 = priordate.getMonth()+1;//January is 0, so always add + 1
        var yyyy2 = priordate.getFullYear();
        if(dd2<10){dd2='0'+dd2};
        if(mm2<10){mm2='0'+mm2};
        var datefrommonthago = mm2 + '/' + dd2 + '/' + yyyy2;
        
        return datefrommonthago;
    }    
    //function to show and hide dives    
    function showDiv(elem, elem1) {
//          alert(elem.value);
        if (elem == 1 || elem.value == 1)
        {
            //Hide      
            $('#imei_div').hide();
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#ptcldiv').hide();
            $('#internationaldiv').hide();
            //show   
            $('#company_div').show();
            $('#findcompanyname').show();
            $('#sub_div').show();
            $('#dateto_div').show();
            $('#datefrom_div').show();
            $('#quickoption_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();
        } else if (elem == 2 || elem.value == 2)
        {
            //  alert('a');
            //Hide      
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#sub_div').hide();
            $('#ptcldiv').hide();
            $('#internationaldiv').hide();
            $('#findcompanyname').hide();
            //show   
            $('#imei_div').show();
            $('#company_div').show();
            $('#dateto_div').show();
            $('#datefrom_div').show();
            $('#quickoption_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            //to change company for multiple values
            $("#company_name_get").attr("multiple", "multiple");
            jQuery("#company_name_get").select2().select2('val', ['-1']);
        } else if (elem == 3 || elem.value == 3)
        {
            //Hide      
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#imei_div').hide();
            $('#dateto_div').hide();
            $('#datefrom_div').hide();
            $('#quickoption_div').hide();
            $('#ptcldiv').hide();
            $('#internationaldiv').hide();
            //show    
            $('#sub_div').show();
            $('#findcompanyname').show();
            $('#company_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();
        } else if (elem == 4 || elem.value == 4)
        {
            //Hide      
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#imei_div').hide();
            $('#dateto_div').hide();
            $('#datefrom_div').hide();
            $('#quickoption_div').hide();
            $('#ptcldiv').hide();
            $('#internationaldiv').hide();
            //show    
            $('#sub_div').show();
            $('#company_div').show();
            $('#findcompanyname').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();
        } else if (elem == 5 || elem.value == 5)
        {
            //Hide      
            $('#imsi_div').hide();
            $('#request_div').hide();
            $('#imei_div').hide();
            $('#dateto_div').hide();
            $('#datefrom_div').hide();
            $('#quickoption_div').hide();
            $('#ptcldiv').hide();
            $('#internationaldiv').hide();
            $('#sub_div').hide();
            $('#findcompanyname').hide();
            //show
            $('#company_div').show();
            $('#cnic_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            //to change company for multiple values
            $("#company_name_get").attr("multiple", "multiple");
            jQuery("#company_name_get").select2().select2('val', ['-1']);
        } else if (elem == 6 || elem.value == 6)
        {
            //Hide      
            $('#imei_div').hide();
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#ptcldiv').hide();
            $('#internationaldiv').hide();
            //show   
            $('#company_div').show();
            $('#sub_div').show();
            $('#dateto_div').show();
            $('#datefrom_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();
        } 
        else if (elem == 7 || elem.value == 7)
        {
            //Hide      
            $('#request_div').hide();
            $('#company_div').hide();
            $('#sub_div').hide();
            $('#cnic_div').hide();
            $('#imsi_div').hide();
            $('#imei_div').hide();
            $('#findcompanyname').hide();
            $('#internationaldiv').hide();
            //show   
            $('#ptcldiv').show();
            $('#dateto_div').show();
            $('#datefrom_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();

        } 
        else if (elem == 9 || elem.value == 9)
        {
            //Hide      
            $('#request_div').hide();
            $('#company_div').hide();
            $('#sub_div').hide();
            $('#cnic_div').hide();
            $('#imsi_div').hide();
            $('#imei_div').hide();
            $('#findcompanyname').hide();
            $('#ptcldiv').hide();
            //show   
            $('#internationaldiv').show();
            $('#dateto_div').show();
            $('#datefrom_div').show();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();

        } 
        else if (elem == 10 || elem.value == 10)
        {
            //Hide      
            $('#request_div').hide();
            $('#company_div').hide();
            $('#sub_div').hide();
            $('#cnic_div').hide();
            $('#imsi_div').hide();
            $('#imei_div').hide();
            $('#findcompanyname').hide();
            $('#ptcldiv').hide();
            //show   
            $('#internationaldiv').hide();
            $('#dateto_div').hide();
            $('#datefrom_div').hide();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();

        } 
        else if (elem == 11 || elem.value == 11)
        {
            //Hide      
            $('#request_div').hide();
            $('#company_div').show();
            $('#sub_div').hide();
            $('#cnic_div').hide();
            $('#imsi_div').hide();
            $('#imei_div').hide();
            $('#findcompanyname').hide();
            $('#ptcldiv').show();
            //show   
            $('#internationaldiv').hide();
            $('#dateto_div').hide();
            $('#datefrom_div').hide();
            $('#submit_div').show();
            $('#reason_div').show();
            $('#project_div').show();
            // to remove multiple companies selection
            $("#company_name_get").removeAttr('multiple');
            jQuery("#company_name_get").select2();

        } 
        else if (elem == 0 || elem.value == 0)
        {
            //Hide      
            $('#imei_div').hide();
            $('#imsi_div').hide();
            $('#cnic_div').hide();
            $('#request_div').hide();
            $('#company_div').hide();
            $('#sub_div').hide();
            $('#dateto_div').hide();
            $('#datefrom_div').hide();
            $('#quickoption_div').hide();
            $('#submit_div').hide();
            $('#ptcldiv').hide();
            $('#reason_div').hide();
            $('#project_div').hide();
        }
        if (elem1 == 1 || elem1.value == 1 && changecount > 1)
        {
            $('#userrequest').find("input[type=text], textarea").val("");
            $('#ChooseTemplate').val(elem.value);
            changecount = changecount + 1;
        }
    }
</script>