<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    $post_request_id=!empty($post_data['requestid']) ? $post_data['requestid'] : '';
    $post_company_mnc=!empty($post_data['mnc']) ? $post_data['mnc'] : '';
    $post_request_type_id=!empty($post_data['requesttype']) ? $post_data['requesttype'] : '';
    $post_request_value=!empty($post_data['requestvalue']) ? $post_data['requestvalue'] : '';
    $post_recieved_file_path=!empty($post_data['receivedfilepath']) ? $post_data['receivedfilepath'] : '';
    $post_recieved_body=!empty($post_data['receivedbody']) ? $post_data['receivedbody'] : '';
    $post_recieved_body_raw=!empty($post_data['receivedbodyraw']) ? $post_data['receivedbodyraw'] : '';
    
    
//print_r($post_data); exit;
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-upload"></i>Data Upload (IMEI#)
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Data Upload Against IMEI</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
                   <?php
                    if (!empty($_GET['accessmessage'])) {
                        ?>
                        <div class="alert alert-success alert-dismissible" style="height: 60px; margin-top: 40px">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> <?php echo $_GET['accessmessage']; ?></h4>
                        </div>
                    <?php } ?>
        <div class="form-group col-md-12 " >
            <div class="alert-dismissible notificationclosereports" id="notification_msgreports" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> <div id="notification_msg_divreports"></div></h4>
            </div>
        </div>
        <div class="col-md-12">
            <div class="nav-tabs-custom">  
                <div style="display:none;" id="custom-form"></div>
                <div class="alert " id="upload_full" style="color: '#ff5b3c'; display: none;">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> 
                            <span id='parsresult'> Be Patient parsing in process... 
                                <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                            </span></h4>
                    </div>
              <?php
                if (!empty($upload) && $upload == 1) {
                    ?>
                    <div class="alert " id="upload" style="color: '#ff5b3c'">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> 
                            <span id='parsresult_f'> Congratulation! File is Uploaded! Be Patient parsing in process 
                                <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                            </span></h4>
                    </div>
                    <input type="hidden" name="path" id="path" value="<?php echo $path; ?>">    
                    <input type="hidden" name="company_name" id="company_name" value="<?php echo $company_name; ?>">    
                    <input type="hidden" name="phone_number" id="phone_number_cdr" value="<?php echo $phone_number; ?>">    
                    <input type="hidden" name="userrequestid" id="userrequestid" value="<?php echo $userrequestid; ?>">    
                    <script type="text/javascript">
                        $(document).ready(function () {
                            parsing_start();
                        });
                        function parsing_start() {
                            var path = $('#path').val();
                            var company_name = $('#company_name').val();
                            var phone_number = $('#phone_number_cdr').val();
                            var userrequestid = $('#userrequestid').val();
                            var array_val = {path: path, company_name: company_name, phone_number:phone_number,userrequestid:userrequestid}
                            $.ajax({
                                url: "<?php echo URL::site("upload/parse_start"); ?>",
                                type: 'POST',
                                data: array_val,
                                cache: false,
                                //dataType: "text",
                                //dataType: 'html',
                                success: function (data)
                                { 
                                    if(data==3)
                                    {    
                                        $("#parsresult_f").html('A Party not mach with field input !');
                                    }else if(data==5){
                                        $("#parsresult_f").html('A Party duplication !');
                                    }else if(data==1){
                                        $("#parsresult_f").html('Parsing Completed !');
                                    }else if(data==404){
                                        $("#parsresult_f").html('Company Not Match !');
                                    }else{
                                        $("#parsresult_f").html('Parsing Error !');
                                    }
                                    
                                }
                            });
                        }
                    </script>                            
                <?php }
                ?>  
                <?php
                if (isset($_GET["message"]) && $_GET["message"] == 1) {
                    ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-check"></i> Congratulation! Information Successfully Added.</h4>
                    </div>
                <?php } ?>

                <div class="">
                    <!-- left column -->
                    <div class="">
                        <!-- general form elements -->
                        <div class="">
                            <div class="box box-primary">
<!--                                <div class="box-header with-border">
                                    <h3 class="box-title">Upload Data</h3>
                                </div>-->
                                <!-- /.box-header -->
                                <div class="box-body"> 

                                    <div class="form-group col-md-12 " >
                                        <div class="alert-dismissible notificationcloseidentity" id="notification_msgidentity" style="display: none;">
                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                            <h4><i class="icon fa fa-check"></i> <div id="notification_msg_dividentity"></div></h4>
                                        </div>
                                    </div>    
                                    
                                    <!-- Check  IMEI -->
                                    <div style="display: block">                                        
<!--                                         form start -->
                                        <form name="imei_form" id="imei_form" class="imei_form" action="#" method="post" enctype="multipart/form-data">
                                            <div class="col-sm-6">
                                                <div class="form-group col-md-12">
                                                    <div class="row">
                                                        <div class="col-sm-10">
                                                            <label style="margin-left: -15px">Enter IMEI#</label>
                                                            <?php 
                                                                if(!empty($_GET['imei']))
                                                                    $reslut_imei = $_GET['imei'];
                                                                else
                                                                    $reslut_imei = '';
                                                            ?>
                                                            <input <?php if(!empty($post_data) || !empty($reslut_imei)){ echo 'readonly'; } ?>  style="margin-left: -15px; " type="text" name="imei_no" id="imei_no" value="<?php echo $reslut_imei; ?>" class="form-control">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div id="imeicontbtn">
                                                                <div class="form-group col-sm-12">
                                                                    <button type="submit" style="margin-top: 25px; margin-left: -20px"  class="btn btn-primary pull-left" id="imeirecord">Continue</button>
                                                                </div>
                                                            </div>
                                                            <div id="imeicontbtn1" style="display: none">
                                                                <div class="form-group col-sm-12">
                                                                    <button <?php if(!empty($post_data)){ echo 'disabled'; } ?> type="button" onclick="editimeibtn();" style="margin-top: 25px; margin-left: -20px;"  class="btn btn-primary pull-left" >Back</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                    <div id="imei_div" style="display: none"> 
                                        <div class="box-header with-border" id="imeititle" style="display: none">
                                            <h3 class="box-title">Manual Data Upload Against IMEI# </h3>
                                        </div>
                                        <div class="col-sm-6" id="device_information">
                                        
                                        </div>
                                        <div class="col-sm-6" >
                                        <div id="imeibuttons" style="display: none">
                                                    <div class="form-group col-sm-12">
                                                        <div class="row">
                                                            <div id="imeicdruploadstatus">
                                                                <div class="alert " id="upload" style="color: '#ff5b3c'">
                                                                    <h4><i ></i> 
                                                                        <span id='parsresult_f'> Be Patient! page loading..
                                                                            <img style="width: 12%; height: 29px;" id="parse_loader" src="<?php echo URL::base() . 'dist/img/103.gif'; ?>">
                                                                        </span></h4>
                                                                </div>
                                                                <ul class="todo-list">
                                                                    <li>
                                                                        <span style="color: '#ff5b3c'" class="text-black"><b>Note: </b><p style="color: red">Please wait until table is loading. In case of failure edit and again send IMEI# </p>
                                                                    
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                        <!--        manual upload cdr against imei number-->
                                        <div class="modal modal-info fade" id="modalcdrupload">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title">Manual Upload CDR Against IMEI</h4>
                                                    </div>
                                                    <form name="cdr_against_imei" id="cdr_against_imei" action="<?php echo Route::url('default', array('controller' => 'upload', 'action' => 'imeidocupload')) ?>" method="post" enctype="multipart/form-data">
                                                    <input type="hidden" id="requestid_cdr_against_imei" name="requestid" value="<?php echo $post_request_id; ?>" /> 
                                                        <div class="modal-body" style='background-color: #fff !important; color: black !important; '>
                                                        
                                                        <!--Manual Upload CDR Against IMEI-->
                                                        <div id="manualuploadimeicdr" style="display:block;margin-top: 5px;margin-bottom: 5px;" class="col-md-12">                                            
                                                            <div id="loader-div" style='background-image: url("<?php echo URL::base(); ?>dist/img/loader.gif"); width:100%; height:11px; background-position:center; display:none'></div>
                                                            <div class="col-md-12">
                                            <div class="form-group col-md-12">
                                                <label ><u>CDR Upload Instructions!</u></label>
                                                <p><u>1.</u> Please make sure uploaded cdr has same format as in accepted list of cdr upload format document. </p>
                                                <p><u>2.</u> Uploaded cdr without matching format will generate parsing error. Error type will be shown on cdr upload status tab. <a style="color: white" target="NEW" href="<?php echo URL::site('User/uploaded_cdrs'); ?>" class="pull-right">CDR Status (Click)</a> </p>
                                                <p><u>3.</u> In case of error in cdr upload status please particular delete file and again upload after fixing error from file. </p>
                                            <p><u>4.</u> Acceptable cdr format. <a target="NEW" style="color: white" href="<?php echo URL::site('User/cdr_upload_format'); ?>" class="pull-right"> Format (Click) </a></p>
                                            </div>
                                        </div>
                                                            <div class="col-md-12" style="background-color: #fff;color: black">
                                                                <hr class="style14 ">
                                                                
                                                                <?php if(!empty($post_data)){ ?>
                                                                                <div class="form-group col-md-6">
                                                                                    <label for="subject">Received File Path</label>
                                                                                        <input readonly="" type="text" class="form-control" id="file_against_imei_path"  name="file_against_imei_path" value="<?php echo $post_recieved_file_path; ?>" placeholder="File Path if any">
                                                                                </div>
                                                                <div class="form-group col-md-6">
                                                                                    <?php
                                                                                    if (!empty($post_recieved_file_path) && (trim($post_recieved_file_path) != 'na' )) {
                                                                                        ?>
                                                                                        <a style="margin-top: 25px" target="" href="<?php echo URL::base() . '/personprofile/download?fid=5&file=' . $post_recieved_file_path ?>" class="btn btn-danger pull-right" >Download File</a>
                                                                                    <?php } ?>
                                                                                        </div> 
                                                                                    <div class="form-group col-md-12">
                                                                                        <label for="body">Received Body Encoded</label>
                                                                                    <textarea readonly style="height: 100%" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body)){ echo strip_tags($post_recieved_body); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  
                                                                                    <label for="body">Received Body Decoded</label>
                                                                                    <textarea readonly style="height: 100%" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body_raw)){ echo strip_tags($post_recieved_body_raw); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  
                                                                                    </div>
                                                                            <?php } ?>
                                                                <div class="form-group col-sm-12">                                                                    
<!--                                                                    <div class="bg-primary" style="margin-left: -5px; padding: 8px;">
                                                                        <a onclick="imeicdrhide();" href="#" class=" pull-right" style="color: red;margin-top: 10px;"><b>(Hide)</b></a>
                                                                        <h4>Manual Upload CDR Against IMEI</h4>
                                                                    </div>    -->
                                                                    <input type="hidden" id="request_type" name="request_type" value="2">
                                                                    <input type="hidden" id="phone_number" name="phone_number" value=""> 
                                                                    <label>Upload CDR Against This IMEI:</label>
                                                                    <input  class="form-control "type="text" id="imeicdr" name="imei" value="" readonly>                                                  
                                                                </div>                                    
                                                                <div class="form-group col-sm-12" >
                                                                    <label>Choose Network</label>
                                                                    <select <?php if(!empty($post_company_mnc)){ echo "readonly"; } ?> class="form-control" name="company_name" id="company_name_against_imei">
                                                                        <option value="">Please Select Network</option>
                                                                        <?php
                                                                        $comp_name_list = Helpers_Utilities::get_companies_data();
                                                                        foreach ($comp_name_list as $list) {
                                                                            if($list->mnc!=4 ){
                                                                            ?>
                                                                        <option  <?php if(!empty($post_company_mnc) && $post_company_mnc==$list->mnc){ echo "selected"; } ?> value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                                                                <?php  } } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group col-sm-12" >
                                                                    <label for="exampleInputFile">File input</label>                                                
                                                                    <input type="file" name="file" id="file_against_imei" class="">
                                                                    <p class="help-block">Upload file from here</p>
                                                                </div> 
                                                                <div class="form-group col-sm-12" >
                                                                    <hr class="style14 ">
<!--                                                                    <button type="button" id="cdr_against_imei_btn" class="btn btn-primary pull-right">Submit</button>-->

                                                                </div>
                                                                <!--<div class="form-group col-sm-12"></div>-->
                                                                <span id="" class="text-black" > </span>
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                                                        <button type="button" id="cdr_against_imei_btn" class="btn btn-primary ">Upload</button>
                                                    </div>  
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!--        manual upload sims against imei-->
                                        <div class="modal modal-info fade" id="modalimeisimsupload">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title">Manual Upload SIMs Against IMEI</h4>
                                                    </div>
                                                     <form role="form" action="<?php echo URL::site("user/imei_upload_post"); ?>" id="manualimeiform" name="manualimeiform" method="post" enctype="multipart/form-data">
                                                    <div class="modal-body" style='background-color: #fff !important; color: black !important; '>
                                                        <div style='background-image: url("<?php echo URL::base(); ?>dist/img/loader.gif"); width:100%; height:11px; background-position:center; display:block'></div> 
                                                        <!--Manual Upload SIMs against IMEI-->
                                                       <div id="manualimeisimsentry" style="display: block;">                                                            
                                                            <div class="col-md-12" style="background-color: #fff;color: black"> 
                                                                    <hr style="" class="style14 col-md-12"> 
                                                                    <input type="hidden" id="requestid" name="requestid" value="<?php echo $post_request_id; ?>" /> 
                                                                    <div class="col-sm-12"> 
<!--                                                                            <div class="bg-primary" style="margin-left: -5px; padding: 8px;">
                                                                                <a class="act-lnk pull-right" href="#" onclick="hideimeisimsmanual();" style="margin-top:10px; margin-right: 5px; color: red"><b>(Hide)</b></a>
                                                                                <h4>Manual Upload SIMs Against IMEI</h4>
                                                                            </div>      -->
                                                                            <?php
                                                                            if (isset($_GET["message"]) && $_GET["message"] == 1) {
                                                                                ?>
                                                                                <div class="alert alert-success alert-dismissible">
                                                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                                                    <h4><i class="icon fa fa-check"></i> Congratulation! Data save successfully</h4>
                                                                                </div>
                                                                            <?php } ?>
<!--                                                                            <div class="form-group">
                                                                                <input name="imeisims" type="hidden" class="form-control" id="imeisims" placeholder="IMEI" >
                                                                                <label title="Update device name"  for="phone_name" class="control-label">Device Name <a title="Click here to find device name" class="btn" onclick="findphonenumber();"> 
                                                                                        Click Here                                                     
                                                                                    </a>
                                                                                </label>
                                                                                <input title="Update device name, leave blank if not necessary" name="imeiphonename" type="text" class="form-control" id="imeiphonename" placeholder="Update Device Name"> 
                                                                            </div>-->
                                                                            <?php if(!empty($post_data)){ ?>
                                                                                <div class="form-group col-md-12">
                                                                                    <label for="subject">Received File Path</label>
                                                                                    <input disabled type="text" class="form-control" id="subject"  name="subject" value="<?php echo $post_recieved_file_path; ?>" placeholder="File Path if any">
                                                                                </div>                             
                                                                                <div class="form-group col-md-12">
                                                                                    <label for="body">Received Body Encoded</label>
                                                                                    <textarea readonly style="height: 100%" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body)){ echo strip_tags($post_recieved_body); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  
                                                                                    <label for="body">Received Body Decoded</label>
                                                                                    <textarea readonly style="height: 100%" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body_raw)){ echo strip_tags($post_recieved_body_raw); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  
                                                                                </div>
                                                                            <?php } ?>
                                                                            <div class="form-group">                                                                                
                                                                                <label title="IMEI"  for="imeisims" class="control-label">Device IMEI 
                                                                                </label>
                                                                                <input name="imeisims" type="text" class="form-control" id="imeisims" placeholder="IMEI" readonly >
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label title="Select company name for sims" for="mnc_imei" class="control-label">SIMs Company Name</label>
                                                                                <select class="form-control" name="mnc_imei" id="mnc_imei">
                                                                                    <option value="">Select</option>
                                                                                    <?php
                                                                                    $comp_name_list = Helpers_Utilities::get_companies_data();
                                                                                    foreach ($comp_name_list as $list) {
                                                                                        ?>
                                                                                        <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                                                                    <?php } ?>

                                                                                </select>
                                                                            </div>
                                                                            <div class="form-group" >
                                                                                <span><label  class="control-label" >SIMs Information</label> <a title="You can add or remove number of sims" name="imeisimcontrol" id="imeisimcontrol" href="#"  class="active" onclick="addmoreimeisim();" >  Add More SIM</a> </span>

                                                                            </div>
                                                                            <hr class="style14 " style="margin-top: -10px; "> 
                                                                            <div class="col-sm-12" id="imeisim1" style="display: block">                                            
                                                                                <div class="form-group" >
                                                                                    <label for="inputSubNO1" class="control-label">SIM-1</label>
                                                                                    <input name="imei_mobile_number[]" type="text" class="form-control" id="inputSubNO1" placeholder="Subscriber Number" /> 
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="usefrom" class="control-label">Used From</label>
                                                                                        <input readonly="" name="usefrom[]" type="datetime" class="form-control" id="usefrom" placeholder="mm/dd/yyyy hh:mm"> 
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="useto" class="control-label">Used To</label>
                                                                                        <input readonly=""  name="useto[]" type="datetime" class="form-control" id="useto" placeholder="mm/dd/yyyy hh:mm"> 
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-12" id="imeisim2" style="display: none">
                                                                                <div class="form-group">
                                                                                    <label for="inputSubNO2" class="control-label">SIM-2</label>
                                                                                    <input name="imei_mobile_number[]" type="text" class="form-control" id="inputSubNO2" placeholder="Subscriber Number" /> 
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="usefrom1" class="control-label">Used From</label>
                                                                                        <input readonly=""  name="usefrom[]" type="datetime" class="form-control" id="usefrom1" placeholder="mm/dd/yyyy hh:mm"> 
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="useto1" class="control-label">Used To</label>
                                                                                        <input readonly=""  name="useto[]" type="datetime" class="form-control" id="useto1" placeholder="mm/dd/yyyy hh:mm"> 
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-12" id="imeisim3" style="display: none">
                                                                                <div class="form-group">
                                                                                    <label for="inputSubNO3" class="control-label">SIM-3</label>
                                                                                    <input  name="imei_mobile_number[]" type="text" class="form-control" id="inputSubNO3" placeholder="Subscriber Number"/> 
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="usefrom3" class="control-label">Used From</label>
                                                                                        <input  readonly="" name="usefrom[]" type="datetime" class="form-control" id="usefrom3" placeholder="mm/dd/yyyy hh:mm"> 
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="useto3" class="control-label">Used To</label>
                                                                                        <input readonly=""  name="useto[]" type="datetime" class="form-control" id="useto3" placeholder="mm/dd/yyyy hh:mm"> 
                                                                                    </div>
                                                                                </div> 
                                                                            </div>
                                                                            <div class="col-sm-12" id="imeisim4" style="display: none">
                                                                                <div class="form-group">
                                                                                    <label for="inputSubNO4" class="control-label">SIM-4</label>
                                                                                    <input   name="imei_mobile_number[]" type="text" class="form-control" id="inputSubNO4" placeholder="Subscriber Number" /> 
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="usefrom4" class="control-label">Used From</label>
                                                                                        <input readonly=""  name="usefrom[]" type="datetime" class="form-control" id="usefrom4" placeholder="mm/dd/yyyy hh:mm"> 
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="useto4" class="control-label">Used To</label>
                                                                                        <input readonly=""  name="useto[]" type="datetime" class="form-control" id="useto4" placeholder="mm/dd/yyyy hh:mm"> 
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-12" id="imeisim5" style="display: none">
                                                                                <div class="form-group">
                                                                                    <label for="inputSubNO5" class="control-label">SIM-5</label>
                                                                                    <input  name="imei_mobile_number[]" type="text" class="form-control" id="inputSubNO5" placeholder="Subscriber Number" /> 
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="usefrom5" class="control-label">Used From</label>
                                                                                        <input readonly=""  name="usefrom[]" type="datetime" class="form-control" id="usefrom5" placeholder="mm/dd/yyyy hh:mm"> 
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <div class="form-group">
                                                                                        <label for="useto5" class="control-label">Used To</label>
                                                                                        <input  readonly="" name="useto[]" type="datetime" class="form-control" id="useto5" placeholder="mm/dd/yyyy hh:mm"> 
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group col-sm-12">
                                                                                <hr class="style14 ">
<!--                                                                                <button type="submit" class="btn btn-primary pull-right" style="margin-top:10px;">Submit</button>                                                        -->

                                                                            </div>
                                                                    </div>                                                            
                                                            </div>
                                                        </div>


                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" style="margin-top: 10px" class="btn btn-primary pull-left" data-dismiss="modal">Close</button>
                                                        <button type="button" id="imeisimsupdate_btn" class="btn btn-primary" style="margin-top:10px;">Submit</button>                                                        
                                                    </div>  
                                                    </form>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>




                                        <!-- /imei record table -->
                                        <div id="imeirecordtable" style="display: none">
                                            <hr class="stimeirecordtableyle14 col-md-12"> 
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                                <div class="">
                                                    <table id="devicestable" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>SIM# Used</th>
                                                                <th title="Owner of SIM">SIM Owner</th>
                                                                <th title="current user of the SIM">SIM User</th>
                                                                <th title="First Time SIM Used In This Device">SIM First Use</th>
                                                                <th title="Last Time SIM Used In This Device">SIM Last Use</th>
                                                                <th title="Current User Of This Device">Device User</th>
                                                                <th class="no-sort">Action</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>

                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>SIM# Used</th>
                                                                <th title="Owner of SIM">SIM Owner</th>
                                                                <th title="current user of the SIM">SIM User</th>
                                                                <th title="First Time SIM Used In This Device">SIM First Use</th>
                                                                <th title="Last Time SIM Used In This Device">SIM Last Use</th>
                                                                <th title="Current User Of This Device">Device User</th>
                                                                <th class="no-sort">Action</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr class="style14 col-md-12"> 
                                    </div> 
                                    <!-- /.check imei -->
                                    
                                </div>                                      
                            </div>
                        </div>
                  

    


</section>

<div class="modal modal-info fade" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Change Status</h4>
            </div>
            <div class="modal-body" style='background-color: #fff !important ; color: #000 !important;'>

                <!--<iframe id="imeiiframe" style="overflow:hidden;" scrolling="no" width="670px"  height="550px"  src="http://www.imei.info/"></iframe>-->

            </div>                    
        </div>

        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- /.content -->
<script type="text/javascript">
    
    
$(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
    
    
    var countdiv3view = 1;
    var objDT;
    var imei = '000000';

    
    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    jQuery.fn.dataTableExt.oApi.fnReloadAjax = function (oSettings, sNewSource, fnCallback, bStandingRedraw)
    {
        // DataTables 1.10 compatibility - if 1.10 then `versionCheck` exists.
        // 1.10's API has ajax reloading built in, so we use those abilities
        // directly.
        if (jQuery.fn.dataTable.versionCheck) {
            var api = new jQuery.fn.dataTable.Api(oSettings);

            if (sNewSource) {
                api.ajax.url(sNewSource).load(fnCallback, !bStandingRedraw);
            } else {
                api.ajax.reload(fnCallback, !bStandingRedraw);
            }
            return;
        }

        if (sNewSource !== undefined && sNewSource !== null) {
            oSettings.sAjaxSource = sNewSource;
        }

        // Server-side processing should just call fnDraw
        if (oSettings.oFeatures.bServerSide) {
            this.fnDraw();
            return;
        }

        this.oApi._fnProcessingDisplay(oSettings, true);
        var that = this;
        var iStart = oSettings._iDisplayStart;
        var aData = [];

        this.oApi._fnServerParams(oSettings, aData);

        oSettings.fnServerData.call(oSettings.oInstance, oSettings.sAjaxSource, aData, function (json) {
            /* Clear the old information from the table */
            that.oApi._fnClearTable(oSettings);

            /* Got the data - add it to the table */
            var aData = (oSettings.sAjaxDataProp !== "") ?
                    that.oApi._fnGetObjectDataFn(oSettings.sAjaxDataProp)(json) : json;

            for (var i = 0; i < aData.length; i++)
            {
                that.oApi._fnAddData(oSettings, aData[i]);
            }

            oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();

            that.fnDraw();

            if (bStandingRedraw === true)
            {
                oSettings._iDisplayStart = iStart;
                that.oApi._fnCalculateEnd(oSettings);
                that.fnDraw(false);
            }

            that.oApi._fnProcessingDisplay(oSettings, false);

            /* Callback user function - for event handlers etc */
            if (typeof fnCallback == 'function' && fnCallback !== null)
            {
                fnCallback(oSettings);
            }
        }, oSettings);
    };
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

    $(document).ready(function () {      
        
        // modal to upload cdr against imei number
        $("body").on("click", ".cdruploadimei", function () {
            $("#modalcdrupload").modal("show");
            //appending modal background inside the blue div
            $('.modal-backdrop').appendTo('.blue');
            //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
            $('body').removeClass("modal-open")
            $('body').css("padding-right", "");
            setTimeout(function () {
                // Do something after 1 second     
                $(".modal-backdrop.fade.in").remove();
            }, 300);

        });
        
        // modal to upload sims against imei number
        $("body").on("click", ".simsuploadimei", function () {
            $("#modalimeisimsupload").modal("show");
            //appending modal background inside the blue div
            $('.modal-backdrop').appendTo('.blue');
            //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
            $('body').removeClass("modal-open")
            $('body').css("padding-right", "");
            setTimeout(function () {
                // Do something after 1 second     
                $(".modal-backdrop.fade.in").remove();
            }, 300);

        });

        
        
        $("#manual_uuploads").validate({
            rules: {
                request_type: {
                    required: true,
                    check_list: true
                },
                company_name: {
                    required: true,
                    check_list: true
                },
                file: {
                    required: true,
                    accept: "xls|xlsx|csv",
                    filesize: 2048576
                }
            },
            messages: {
                request_type: {
                    required: "Please select file type",
                },

                company_name: {
                    required: "Please choose Newtork"
                },
                file: "File must be XLS, XLSX less than 2MB"
            }

        });
        

        // Validators file size
        $.validator.addMethod('filesize', function (value, element, param) {
            // param = size (in bytes) 
            // element = element to validate (<input>)
            // value = value of the element (file name)
            return this.optional(element) || (element.files[0].size <= param)
        });
        // Validators checklist
        $.validator.addMethod("check_list", function (sel, element) {
            if (sel == "" || sel == 0) {
                return false;
            } else {
                return true;
            }
        }, "<span>Select One</span>");
        // Validators less than
        jQuery.validator.addMethod("future", function(value, element) { 
    return this.optional(element) || Date.parse(value) < new Date().getTime(); 
}, "Please enter only old dates");
        // Validators valid date
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
        // Validators alphanumericspecial
        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ ]+$/);
        }, "Only letters, Numbers & Space/underscore Allowed.");
        // Validators numberthree
        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");
        // Validator date and time
        jQuery.validator.addMethod("dateTime", function (value, element) {
        var stamp = value.split(" ");
        var validDate = !/Invalid|NaN/.test(new Date(stamp[0]).toString());
        var validTime = /^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/i.test(stamp[1]);
        return this.optional(element) || (validDate && validTime);
    }, "Please enter a valid date and time (mm/dd/yyyy hh:mm).");

    });

    function showDiv(elem) {
        // alert(elem);
        $('#tabmessage').hide();
        if (elem == 2 || elem.value == 2)
        {
            //Hide      
            //$('#imei_div').hide();
            $("#manualimeientry").hide();
            $("#manualsubentry").hide();
            $("#manuallocationentry").hide();
            //  $("#manualimeisimsentry").hide();
        } else
        {
            //Hide      
           // $('#imei_div').hide();
            $("#manualcnicsimsentry").hide();
            $("#manualimeientry").hide();
        }

    }

    
    //function to add more sims
    var simscountimei1 = 1;
    var simscountimei = 1;
    function addmoreimeisim() {

        // function to control button count  
        if (simscountimei1 === 1) {
            simscountimei = simscountimei + 1;
            if (simscountimei === 6) {
                simscountimei1 = 2;
            }
        }
        if (simscountimei1 === 2) {
            if (simscountimei > 1) {
                if (simscountimei === 6) {
                    simscountimei = simscountimei - 2;
                } else {
                    simscountimei = simscountimei - 1;
                }
            } else {
                simscountimei1 = 1;
                simscountimei = simscountimei + 1;
            }
        }
        if (simscountimei === 5) {
            $('#imeisimcontrol').html('  Less SIM');
        }
        if (simscountimei === 1) {
            $('#imeisimcontrol').html('  Add More SIM');
        }
        //code to control button
        if (simscountimei > 1)
        {
            $('#imeisim2').show();
        } else {
            $('#imeisim2').hide();
        }

        if (simscountimei > 2)
        {
            $('#imeisim3').show();
        } else {
            $('#imeisim3').hide();
        }
        if (simscountimei > 3)
        {
            $('#imeisim4').show();
        } else {
            $('#imeisim4').hide();
        }
        if (simscountimei > 4)
        {
            $('#imeisim5').show();
        } else {
            $('#imeisim5').hide();
        }
    }

    //Date picker
    $('#datetime').datepicker({
        autoclose: true
    });
     $("#locdate").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#usefrom").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#usefrom1").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#usefrom2").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#usefrom3").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#usefrom4").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#useto").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#useto1").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#useto2").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#useto3").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
     $("#useto4").datetimepicker({format: 'mm/dd/yyyy hh:ii'});
    
    /*$('#locdate').datepicker({
        autoclose: true
    });*/

   
    /* Update Device name*/
    function changedevicename(dev) {
        if(dev==1){            
        $("#updatedevicename").show();
        }else{
           $("#updatedevicename").hide(); 
        }
    }
  

    //show manual imei cdr
    function imeicdr() {
        // $("#imei_div").hide();
      //  $("#manualuploadimeicdr").show();
        $("#imeicdr").val($("#imei_no").val());

    }
    //action IMEI CDR Upload Confirmation
    function imeicdruploadconfirm(imei) {
      //  $("#parsresult").html('Full Parsing Started.. !');
        $("#upload_full").show();
        var array_val = {imei: imei};        
        $.ajax({
                url: "<?php echo URL::site("upload/imeidocuploadfull"); ?>",
                type: 'POST',
                data: array_val,
                cache: false,
                //dataType: "text",
                dataType: 'html',
                success: function (data)
                {
                     if(data== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                    if(data==3)
                    {    
                        $("#parsresult").html('A Party not mach with field input !');
                    }else if(data==5){
                        $("#parsresult").html('IMEI duplication !');
                    }else if(data==1){
                        $("#parsresult").html('Parsing Completed !');
                        $("#confirm_action_link").html('<b>Full Uploaded</b>');
                        $("#confirm_action_link").addClass("disabled");
                        
                    }else if(data==6){
                        $("#parsresult").html('File Already Parsed !');
                    }else{
                        $("#parsresult").html('Parsing Error !');
                    }

                }
            });
        
    }
    //close manual imei cdr
    function imeicdrhide() {
        // $("#imei_div").hide();        
        $("#imeirecordtable").css("position", "relative");
       // $("#manualuploadimeicdr").hide();
        $("#imeirecordtable").hide();
        $("#imeirecordtable").show();
        $("#imeirecordtable").css("position", "static");

    }

    //edit imei no
    function editimeibtn() {

        $("#imeicontbtn").show();
        $("#imeicontbtn1").hide();
        $("#imei_div").hide();
        $("#imei_no").attr("readonly", false);
    }

    /* For find msisdn */
    function findphonenumber() {

        $("#modal-default").modal("show");
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

    /* For to find company name */
    function findcompanyname() {
        $("#findcompnay_image").show();
        $("#findcompanyname").html('');
        var subnumber = $("#inputSubNO").val();
        if (subnumber == '')
        {
            alert('Subscriber Number is empty');
        } else {
            var request = $.ajax({
                url: "<?php echo URL::site("upload/checkcompany"); ?>",
                type: "POST",
                dataType: 'text',
                data: {number: subnumber},
                success: function (responseTex)
                {
                     if(responseTex== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                    if (responseTex == -1)
                    {
                        alert('Failed to recognize');
                        $("#company_name_get").attr("readonly", false);
                        $("#findcompanyname").html('Search (Click)');

                    } else {
                        $("#findcompnay_image").hide();
                        $("#company_name_get").val(responseTex);
                        $("#findcompanyname").html('Search (Click)');
                    }

                },
                error: function (jqXHR, textStatus) {
                    alert('Failed to recognize, Please Select Manually');
                    $("#company_name_get").attr("readonly", false);
                }
            });
        }
    }


    /* For Manual Upload CDR Against IMEI */
    $("#cdr_against_imei_btn").click(function () { 
        $("#cdr_against_imei").submit(function (e) {            
            
            e.preventDefault();
        }).validate({
            rules: {                
                company_name: {
                    required: true,
                    check_list: true
                },
                file: {
                    required: true,
                    accept: "xls|xlsx|csv",
                    filesize: 2048576
                }
            },
            messages: {
                company_name: {
                    required: "Please choose Newtork"
                },
                file: "File must be XLS, XLSX less than 2MB"
            },
            submitHandler: function (form) {
                $("#loader-div").show();
                //var formData = new FormData($("#cdr_against_imei"));
                var formData = new FormData();
                formData.append('file', $('input[id=file_against_imei]')[0].files[0]);
                formData.append('company_name', $('#company_name_against_imei').val());
                formData.append('imei', $('#imei_no').val());
                formData.append('requestid', $('#requestid_cdr_against_imei').val());
                formData.append('ismanualfrm', 1);
                $.ajax({
                    type:'POST',
                    url: $("#cdr_against_imei").attr('action'),
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(data){
                        
                        $("#modalcdrupload").modal("hide");
                        //loader hide
                        if(data==1)    
                        {    
                        $("#notification_msg_divreports").html('Successfully Updated');
                        }else if(data==2)    
                        {    
                            $("#notification_msg_divreports").html('IMEI not Mach');
                        }else if(data==4)    
                        {    
                            $("#notification_msg_divreports").html('IMEI Duplicated');
                        }else if(data==404)    
                        {    
                            $("#notification_msg_divreports").html('Company Not Match !');
                        }else{
                            $("#notification_msg_divreports").html('Error');
                        }
                        
                                    $("#notification_msgreports").show();
                                    $("#notification_msgreports").addClass('alert');
                                    $("#notification_msgreports").addClass('alert-success');
                                    var elem = $(".notificationclosereports");
                                    elem.slideUp(10000);
                                    refreshGrid();
                                    $("#loader-div").hide();
                                        var imei_no = $("#imei_no").val();
                                        if (typeof objDT != 'undefined')
                                        {  //objDT.destroy();                     
                                            var newUrl = "<?php echo URL::site('personsreports/ajaximeidetail/', TRUE); ?>" + "/" + imei_no;
                                            objDT.fnReloadAjax(newUrl);
                                            //refreshGrid();
                                        }

                                        var result = {imei_no: imei_no}
                                        //ajax to upload device informaiton
                                        $.ajax({
                                            url: "<?php echo URL::site("personsreports/device_information"); ?>",
                                            type: 'POST',
                                            data: result,
                                            cache: false,
                                            success: function (msg) {
                                                 if(msg== 2){
                                             swal("System Error", "Contact Support Team.", "error");
                                                }
                                                $("#device_information").show();
                                                $("#device_information").html(msg);
                                            }
                                        });
                                        //ajax to upload status upload
                                        $.ajax({
                                            url: "<?php echo URL::site("personsreports/last_update_imei_cdr_status"); ?>",
                                            type: 'POST',
                                            data: result,
                                            cache: false,
                                            success: function (msg) {
                                                if(msg== 2){
                                          swal("System Error", "Contact Support Team.", "error");
          }
                                                $("#imeicdruploadstatus").show();
                                                $("#imeicdruploadstatus").html(msg);
                                            }
                                        });       
                                    
                    },
                    error: function(data){
                        //error message show and loader hide
                        console.log("error");
                        //console.log(data);
                    }
                });
                
                //submit via ajax
                return false;  //This doesn't prevent the form from submitting.
            }
            
        });
        $("#cdr_against_imei").trigger('submit'); 
        
    });
    /* For to check imei number */
    $("#imeirecord").click(function () {

        $(".imei_form").submit(function (e) {
            e.preventDefault();
        }).validate({
            rules: {
                imei_no: {
                    required: true,
                    number: true,
                    maxlength: 15,
                    minlength: 15
                }
            },
            messages: {
                imei_no: {
                    required: "Enter IMEI Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 15 digits",
                    minlength: "Minimum 15 digits"
                }
            },
            submitHandler: function (form) {
                var imei_no = $("#imei_no").val();
                //to calculate imei
                 var result = {imei_no: imei_no}
                //ajax to upload device informaiton
                $.ajax({
                    url: "<?php echo URL::site("personsreports/find_imei_last_digit"); ?>",
                    type: 'POST',
                    data: result,
                    cache: false,
                    success: function (msg) {
                        $("#imei_no").val(msg);
                   if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                $("#imei_div").show();
                var imei = msg;
                $("#imeirecordtable").show();
                $("#imeibuttons").show();
                $("#imeicontbtn").hide();
                $("#imeicontbtn1").show();
                $("#imeititle").show();
                $("#imei_no").attr("readonly", true);
                if (typeof objDT != 'undefined')
                {  //objDT.destroy(); 

                    var newUrl = "<?php echo URL::site('personsreports/ajaximeidetail/', TRUE); ?>" + "/" + imei;

                    objDT.fnReloadAjax(newUrl);

                    //refreshGrid();
                } else {

                    objDT = $('#devicestable').dataTable(
                            {//"aaSorting": [[2, "desc"]],
                                "bPaginate": true,
                                "bProcessing": true,
                                //"bStateSave": true,
                                "bServerSide": true,
                                "sAjaxSource": "<?php echo URL::site('personsreports/ajaximeidetail/', TRUE); ?>" + "/" + imei,
                                "sPaginationType": "full_numbers",
                                "bFilter": true,
                                "bLengthChange": true,
                                "oLanguage": {
                                    "sProcessing": "Loading..."
                                },
                                retrieve: true,
                                "columnDefs": [{
                                        "targets": 'no-sort',
                                        "orderable": false,
                                    }]
                            }
                    );

                    $('.dataTables_empty').html("Information not found");
                }
                /* updated by end */
                var imei_no = msg;
                var result = {imei_no: imei_no}
                //ajax to upload device informaiton
                $.ajax({
                    url: "<?php echo URL::site("personsreports/device_information"); ?>",
                    type: 'POST',
                    data: result,
                    cache: false,
                    success: function (msg) {
                        $("#device_information").show();
                        $("#device_information").html(msg);
                    }
                });
                //ajax call to update status upload
                $.ajax({
                    url: "<?php echo URL::site("personsreports/last_update_imei_cdr_status"); ?>",
                    type: 'POST',
                    data: result,
                    cache: false,
                    success: function (msg) {
                              if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                        $("#imeicdruploadstatus").show();
                        $("#imeicdruploadstatus").html(msg);
                    }
                });

                //submit via ajax
                return false;  //This doesn't prevent the form from submitting.
            
            
             }
                });
            
            
            
            }
        });
        $(".imei_form").trigger('submit');
    });
    


    /* form for manual update sims against imei  */
    function imeisimsmanualentry() {
        $("#manualimeisimsentry").show();
        $("#imeisims").val($("#imei_no").val());
    }
    /* form for manual update sims against imei  */
    function hideimeisimsmanual() {
        $("#imeirecordtable").css("position", "relative");
        $("#manualimeisimsentry").hide();
        $("#imeirecordtable").hide();
        $("#imeirecordtable").show();
        $("#imeirecordtable").css("position", "static");
    }

    //function to call request page for CDR
    function requestimeicdr(imei) {
        var request = "existing";
        var newForm = jQuery('<form name="custom_form_cdr_imei" id="custom_form_bd_cdr_imei" action="<?php echo URL::site("userrequest/request/1"); ?>" method="POST">');
            newForm.append(jQuery('<input>', {
                'name': 'imei',
                'value': imei,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'requesttype',
                'value': 2,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'pid',
                'value': -1,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'request',
                'value': request,
                'type': 'text'
            }));
            newForm.append('<input type="submit" id="custom_form_bd_bt_cdr_imei" name="submit" value="true"/>');
            $('#custom-form').append(newForm);
            //$("#custom_form_bd").submit();
            $('#custom_form_bd_bt_cdr_imei').trigger('click');
        
        
    }
    
    
     /* For Manual change device name against imei */
   // $("#updateimeidevicename").click(function () { 
   function updateimeidevicename() {
      //  alert('func');
        $("#imeidevicenameform").submit(function (e) {            
          //  alert('form');
            e.preventDefault();
        }).validate({
            rules: {
                imeiphonenameupdate: {
                    required: true,
                    alphanumericspecial: true,
                    maxlength: 30,
                    minlength: 3
                }
            },
            messages: {
                imeiphonenameupdate: {
                    required: "Name Required",
                    maxlenght: "Maximum 30 characters",
                    minlength: "Minimum 3 characters"
                }
            },
            submitHandler: function (form) {
                var formData = new FormData();
                formData.append('phone_name', $('#imeiphonenameupdate').val());
                formData.append('imei_no', $('#imei_no').val());
                $.ajax({
                    type:'POST',
                    url: $("#imeidevicenameform").attr('action'),
                    data:formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success:function(data){
                        
                        changedevicename(2);
                        //loader hide
                        $("#notification_msg_divreports").html('Successfully Updated');
                                    $("#notification_msgreports").show();
                                    $("#notification_msgreports").addClass('alert');
                                    $("#notification_msgreports").addClass('alert-success');
                                    var elem = $(".notificationclosereports");
                                    elem.slideUp(10000);
                                  //  refreshGrid();
                                  var imei_no = $("#imei_no").val();
                var result = {imei_no: imei_no}
                //ajax to upload device informaiton
                $.ajax({
                    url: "<?php echo URL::site("personsreports/device_information"); ?>",
                    type: 'POST',
                    data: result,
                    cache: false,
                    success: function (msg) {
                                       if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                        $("#device_information").show();
                        $("#device_information").html(msg);
                      //  refreshGrid();
                    }
                });
                    },
                    error: function(data){
                        //error message show and loader hide
                        console.log("error");
                      //  console.log(data);
                    }
                });
                
                //submit via ajax
                return false;  //This doesn't prevent the form from submitting.
            }
            
        });
        $("#imeidevicenameform").trigger('submit'); 
        
  //  });
    
    }
    //clear manual imei form data
    function clearmanualimeiformdate(){
   $("#mnc_imei").val('');
   $("#inputSubNO1").val('');
    $("#usefrom").val('');
    $("#useto").val('');
   $("#inputSubNO2").val('');
    $("#usefrom1").val('');
    $("#useto1").val('');
   $("#inputSubNO3").val('');
    $("#usefrom2").val('');
    $("#useto2").val('');
   $("#inputSubNO4").val('');
    $("#usefrom3").val('');
    $("#useto3").val('');
   $("#inputSubNO5").val('');
    $("#usefrom4").val('');
    $("#useto4").val('');
    }
     /* For Manual Upload SIMs against imei */
    $("#imeisimsupdate_btn").click(function (e) { 
        
        e.preventDefault();
        $("#manualimeiform").validate({
            rules: {
                'imei_mobile_number[]': {
                    required: true,
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                },
                'usefrom[]': {
                    dateTime: true                    
                },
                'useto[]': {
                    dateTime: true,
                    future:true
                },
                mnc_imei: {
                    required: true,
                    check_list: true
                }
            },
            messages: {
                'imei_mobile_number[]': {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },
                'usefrom[]': {
                    dateTime: "mm/dd/yyyy hh:mm"
                },
                'useto[]': {
                    dateTime: "mm/dd/yyyy hh:mm"
                },
                mnc_imei: {
                    required: "required",
                }
            },
            submitHandler: function (form) {  
                e.preventDefault();
                    var formData = new FormData();
                    var imeiform=$('#manualimeiform').val();
                /*    formData.append('imeisims', $('#imeisims').val());
                    formData.append('imeicompany', $('#imeicompany').val());
                    formData.append('mobile_number[]', $("[name='imei_mobile_number[]'", imeiform).val());
                    formData.append('usefrom[]', $("[name='usefrom[]']", imeiform).val());
                    formData.append('useto[]', $("[name='useto[]']", imeiform).val());*/
                    $.ajax({
                        type:'POST',
                        url: $("#manualimeiform").attr('action'),
                        data:$("#manualimeiform").serialize(),
                        cache:false,
                     //   contentType: false,
                     //   processData: false,
                        success:function(data){
                            $("#modalimeisimsupload").modal("hide");
                            //loader hide
                            $("#notification_msg_divreports").html('Successfully Updated');
                                        $("#notification_msgreports").show();
                                        $("#notification_msgreports").addClass('alert');
                                        $("#notification_msgreports").addClass('alert-success');
                                        var elem = $(".notificationclosereports");
                                        elem.slideUp(10000);
                                        refreshGrid();
                                        $("#loader-div").hide();
                                        clearmanualimeiformdate();
                                        
                            //var requeststatus="<?php // if(!empty($_GET['status'])){ echo $_GET['status']; } ?>";
                            //alert(data);
                            if(data==-7){
                              //  alert('divert');
                                window.location.replace("<?php echo URL::site("userrequest/request_status"); ?>");
                              
                            }
                        },
                        error: function(data){
                            //error message show and loader hide
                            console.log("error");
                            //console.log(data);
                        }
                    });
                    var imei_no = $("#imei_no").val();
                    if (typeof objDT != 'undefined')
                    {  //objDT.destroy();                     
                        var newUrl = "<?php echo URL::site('personsreports/ajaximeidetail/', TRUE); ?>" + "/" + imei_no;
                        objDT.fnReloadAjax(newUrl);
                        //refreshGrid();
                    }

                    var result = {imei_no: imei_no}
                    //ajax to upload device informaiton
                    $.ajax({
                        url: "<?php echo URL::site("personsreports/device_information"); ?>",
                        type: 'POST',
                        data: result,
                        cache: false,
                        success: function (msg) {
                            $("#device_information").show();
                            $("#device_information").html(msg);
                        }
                    });
                    //ajax to upload status upload
                    $.ajax({
                        url: "<?php echo URL::site("personsreports/last_update_imei_cdr_status"); ?>",
                        type: 'POST',
                        data: result,
                        cache: false,
                        success: function (msg) {
                                  if(msg== 2){
              swal("System Error", "Contact Support Team.", "error");
          }
                            $("#imeicdruploadstatus").show();
                            $("#imeicdruploadstatus").html(msg);
                        }
                    });

                    //submit via ajax
                    return false;  //This doesn't prevent the form from submitting.
                
                
            }
            
        });
        $("#manualimeiform").trigger('submit'); 
        
    });
  //Date picker
    $('#act_date').datepicker({
      autoclose: true
    });
    //function to call request page for subscriber
    function requestsub(sim,simownerid) { 
        var request = "existing";
        var newForm = jQuery('<form name="custom_form_sub" id="custom_form_bd_sub" action="<?php echo URL::site("userrequest/request/1"); ?>" method="POST">');
            newForm.append(jQuery('<input>', {
                'name': 'msisdn',
                'value': sim,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'requesttype',
                'value': 3,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'pid',
                'value': simownerid,
                'type': 'text'
            }));
            newForm.append(jQuery('<input>', {
                'name': 'request',
                'value': request,
                'type': 'text'
            }));
            newForm.append('<input type="submit" id="custom_form_bd_bt_sub" name="submit" value="true"/>');
            $('#custom-form').append(newForm);
            //$("#custom_form_bd").submit();
            $('#custom_form_bd_bt_sub').trigger('click');
        
       
    }
    
</script>
<?php
if(!empty($post_request_type_id) && $post_request_type_id==2 && !empty($post_request_value)){
    ?>
<script>
 $(document).ready(function () {  
     showDiv(3);
     $("#imei_no").val(<?php echo $post_request_value; ?>);
        jQuery("#imeirecord").trigger("click");    
        jQuery(".cdruploadimei").trigger("click");    
        
        
           
    });
    
       
    
</script>
    <?php } ?>

