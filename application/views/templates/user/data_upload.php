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
    
    
//print_r($post_data); exit;
?>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-upload"></i>Data Upload
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Data Upload</li>
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="nav-tabs-custom">
                                            <ul  class="nav nav-tabs">
                                                <li class=" "><a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> href="#" onclick="showDiv(1);" data-toggle="tab">Against Mobile#</a></li>
                                                <li ><a href="#" <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?>  onclick="showDiv(2);" data-toggle="tab">Against CNIC#</a></li>
                                                <li ><a href="#" <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> onclick="showDiv(3);"  data-toggle="tab">Against IMEI#</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
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
                                    <div class="col-sm-12" id="tabmessage">
                                        <h3><span class="label label-default" ><i class="fa fa-arrow-up"></i> Please Click On Related Tab</span></h3>
                                        </div>
                                    <!-- Check  MSISDN -->
                                    <div id="msisdn_div" style="display: none">
                                        <!-- form start -->
                                        <form name="msisdn_form" id="msisdn_form" class="msisdn_form" action="" method="post" enctype="multipart/form-data">
                                            <div class="form-group col-md-6">
                                                <div class="form-group col-md-12">
                                                    <div class="row">
                                                        <div class="col-sm-10">
                                                            <label style="margin-left: -15px">Enter MSISDN #</label>
                                                            <input <?php if(!empty($post_request_value)){ echo "readonly"; } ?>  style="margin-left: -15px; " type="text" name="msisdn_no" id="msisdn_no" class="form-control">
                                                            <input type="hidden" name="msisdnerrorhandle" id="msisdnerrorhandle"  >
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div >
                                                                <div class="form-group col-sm-12">
                                                                    <button onclick="msisdn();"  style="margin-top: 25px; margin-left: -20px"  class="msisdn_submit_btn btn btn-primary pull-left" >Continue</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div> 

                                            <!-- /.box-body -->
                                        </form>
                                        
                                        <hr class="style14 col-md-12"> 
                                    </div>
                                    <!-- Check  CNIC -->
                                    <div id="cnic_div" style="display: none">
                                        <!-- form start -->
                                        <form name="cnic_form" id="cnic_form" class="cnic_form" action="" method="post" enctype="multipart/form-data">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="CnicForeignerRadio1" class="control-label">Country</label>
                                                    <div class="radio">                                            
                                                        <label>
                                                            <input type="radio" name="cnic_is_foreigner" id="CnicForeignerRadio1" value="0" checked="" >
                                                            Pakistani
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="cnic_is_foreigner" id="CnicForeignerRadio2" value="1" >
                                                            Other
                                                        </label>
                                                    </div>
                                                </div>
                                            </div> 
                                            <div class="form-group col-md-6">
                                                <div class="form-group col-md-12">
                                                    <div class="row">                                                        
                                                        <div class="col-sm-10">
                                                            <label style="margin-left: -15px">Enter CNIC #</label>
                                                            <input <?php if(!empty($post_request_value)){ echo "readonly"; } ?> style="margin-left: -15px; " type="text" name="cnic_no" id="cnic_no" class="form-control">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div >
                                                                <div class="form-group col-sm-12">
                                                                    <button onclick="cnic();"  style="margin-top: 25px; margin-left: -20px"  class="btn btn-primary pull-left cnic_submit_btn" >Continue</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div> 
                                            <!-- /.box-body -->
                                        </form>                                        
                                        <hr class="style14 col-md-12"> 
                                    </div>
                                    <!-- Check  IMEI -->
                                    <div id="imei_div" style="display: none">
                                        <div class="box-header with-border" id="imeititle" style="display: none">
                                            <h3 class="box-title">Manual Data Upload Against IMEI# </h3>
                                        </div>
                                        <div class="col-sm-6" id="device_information">
                                        
                                        </div>
<!--                                         form start -->
                                        <form name="imei_form" id="imei_form" class="imei_form" action="#" method="post" enctype="multipart/form-data">
                                            <div class="col-sm-6">
                                                <div id="imeibuttons" style="display: none">
                                                    <div class="form-group col-sm-12">
                                                        <div class="row">
                                                            <div id="imeicdruploadstatus">
                                                                <ul class="todo-list">
                                                                    <li>
                                                                        <span class="text-black"><b>Action Required: </b><p style="color: red">Please edit and again send IMEI# </p></span>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <hr style="margin-left: -20px" class="stimeirecordtableyle14 col-md-12"> 
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <div class="row">
                                                        <div class="col-sm-10">
                                                            <label style="margin-left: -15px">Enter IMEI#</label>
                                                            <input <?php if(!empty($post_data)){ echo 'readonly'; } ?>  style="margin-left: -15px; " type="text" name="imei_no" id="imei_no" class="form-control">
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div id="imeicontbtn">
                                                                <div class="form-group col-sm-12">
                                                                    <button type="button" style="margin-top: 25px; margin-left: -20px"  class="btn btn-primary pull-left" id="imeirecord">Continue</button>
                                                                </div>
                                                            </div>
                                                            <div id="imeicontbtn1" style="display: none">
                                                                <div class="form-group col-sm-12">
                                                                    <button <?php if(!empty($post_data)){ echo 'disabled'; } ?> type="button" onclick="editimeibtn();" style="margin-top: 25px; margin-left: -20px;"  class="btn btn-primary pull-left" >Edit IMEI</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 

                                            </div>
                                        </form>
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
                                                                                        <label for="body">Received Body</label>
                                                                                        <textarea disabled style="height: 100px" class="form-control" id="body" name="body"><?php if (!empty($post_recieved_body)) {
                                                                                    echo strip_tags($post_recieved_body);
                                                                                } else {
                                                                                    echo "Email Parsing Error: Email is not fetched from inbox.";
                                                                                } ?></textarea>  
                                                                                    </div>
                                                                            <?php } ?>
                                                                <div class="form-group col-sm-12">                                                                    
<!--                                                                    <div class="bg-primary" style="margin-left: -5px; padding: 8px;">
                                                                        <a onclick="imeicdrhide();" href="#" class=" pull-right" style="color: red;margin-top: 10px;"><b>(Hide)</b></a>
                                                                        <h4>Manual Upload CDR Against IMEI</h4>
                                                                    </div>    -->
                                                                    <input type="hidden" id="request_type" name="request_type" value="cdr">
                                                                    <input type="hidden" id="phone_number" name="phone_number" value=""> 
                                                                    <label>Upload CDR Against This IMEI:</label>
                                                                    <input  class="form-control "type="text" id="imeicdr" name="imei" value="" readonly>                                                  
                                                                </div>                                    
                                                                <div class="form-group col-sm-12" >
                                                                    <label>Choose Network</label>
                                                                    <select <?php if(!empty($post_company_mnc)){ echo "readonly"; } ?> class="form-control" name="company_name" id="company_name_against_imei">
                                                                        <option value="">Please Select Network</option>
                                                                        <?php try{
                                                                        $comp_name_list = Helpers_Utilities::get_companies_data();
                                                                        foreach ($comp_name_list as $list) {
                                                                            if($list->mnc!=4 ){
                                                                            ?>
                                                                        <option  <?php if(!empty($post_company_mnc) && $post_company_mnc==$list->mnc){ echo "selected"; } ?> value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                                                                <?php  } } 
                                                                                }  catch (Exception $ex){   }?>
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
                                                                                    <label for="body">Received Body</label>
                                                                                    <textarea disabled style="height: 100px" class="form-control" id="body" name="body"><?php if (!empty($post_recieved_body)) { echo strip_tags($post_recieved_body); } else { echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  
                                                                                </div>
                                                                            <?php } ?>
                                                                            <div class="form-group">                                                                                
                                                                                <label title="IMEI"  for="imeisims" class="control-label">Device IMEI 
                                                                                </label>
                                                                                <input name="imeisims" type="text" class="form-control" id="imeisims" placeholder="IMEI" readonly >
                                                                            </div>

                                                                            <div class="form-group">
                                                                                <label title="Select company name for sims" for="imeicompany" class="control-label">SIMs Company Name</label>
                                                                                <select class="form-control" name="imeicompany" id="imeicompany">
                                                                                    <option value="">Select</option>
                                                                                    <?php try{
                                                                                    $comp_name_list = Helpers_Utilities::get_companies_data();
                                                                                    foreach ($comp_name_list as $list) {
                                                                                        ?>
                                                                                        <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                                                                    <?php } 
                                                                                    }  catch (Exception $ex){   }?>

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
                                                <div class="table-responsive">
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
                    </div>
                </div>
                <!--/.col (left) -->


                <!-- Check  Data entry choice -->
                <div id="msisdn_div_options" style="display: none">
                    <!-- left column -->
                    <div class="">
                        <!-- general form elements -->
                        <div class="">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Manual Data Upload Against Phone#</h3>
                                </div>                                
                                <div class="box-body" style="padding: 20px;"> 
                                    <!-- /.box-header -->
                                    <div id="msisdn_div_options_sub_link" style="display: none">                                        
                                        <h4>Subscriber Detail</h4>
                                        <p> <i class="fa fa-upload"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> onclick="openmanualsubentry(0);" class="btn manualsubentry_link">Manual Subscriber Entry</a></p>
                                        <p> <i class="fa  fa-send"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> id="sub_link_val" onclick="requestsubviaemail();"  class="btn">Subscriber Request via Email</a></p>
                                    </div>
                                    <div id="msisdn_div_options_cdr_link"  style="display: none">
                                        <div id="subscriberinfo">
                                            
                                        </div>
                                        <div class="col-md-6">
                                        <h4>MSISDN Detail</h4>
                                        <p> <i class="fa fa-upload"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> onclick="openmanualsubentry(1);" class="btn manualsubentryexist_link">Update Existing Subscriber</a></p>
                                        <p> <i class="fa  fa-send"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> onclick="openmanuallocationentry();"  class="btn">Update Current Location</a></p>                                        
                                        </div>
                                        <div class="col-md-6">
                                        <h4>CDR Detail</h4>                                        
                                        <p> <i class="fa fa-upload"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?>  onclick="openmanualupload();" class="btn">Upload CDR File</a></p>
                                        <p> <i class="fa  fa-send"></i> <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> id="cdr_link_val"  class="btn">Request New CDR</a></p>                                        
                                        
                                        </div>
                                        <div class="col-md-12" id="cdr_against_mobile_upload_status" style=' background-image: url("<?php echo URL::base(); ?>dist/img/loader.gif"); width:100%; height:11px; background-position:center; display:none'></div> 
                                        </div>    
<!--                                    <a <?php if(!empty($post_data)){ echo 'class="disabled"'; } ?> id="msisdn_back" onclick="msisdn_back();" class="btn btn-default"><i class="fa fa-rotate-left text-blue"></i> Back</a>-->
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/.col (left) -->
                </div>

                <!-- Manual CDR Entry -->
                <div id="manualupload" style="display:none">
                    <!-- left column -->
                    <div class="">
                        <!-- general form elements -->
                        <div class="">
                            <div class="box box-primary">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Manual CDR Uploads</h3>
                                </div>
                                <!-- /.box-header -->
                                <!-- form start -->
                                <form name="manual_uuploads" id="manual_uuploads" action="<?php echo Route::url('default', array('controller' => 'upload', 'action' => 'docupload')) ?>" method="post" enctype="multipart/form-data">
                                    <div class="box-body"> 
                                         <input type="hidden" id="userrequestid" name="userrequestid" value="<?php echo $post_request_id; ?>" /> 
                                        <?php if(!empty($post_data)){ ?>
                                        <div class="col-md-6">
                                            <div class="form-group col-md-12">
                                                <label for="subject">Received File Path</label>
                                                <input disabled type="text" class="form-control" id="subject"  name="subject" value="<?php echo $post_recieved_file_path; ?>" placeholder="File Path if any">
                                            </div>                             
                                            <div class="form-group col-md-12">
                                                <label for="body">Received Body</label>
                                                <textarea disabled style="height: 100px" class="form-control" id="body" name="body"><?php if (!empty($post_recieved_body)) { echo strip_tags($post_recieved_body); } else { echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-6">
                                        <div class="form-group col-sm-12">            
                                            <input type="hidden" id="request_type" name="request_type" value="cdr">                                                
                                            <input type="hidden" id="imei" name="imei" value="">        
                                            <label for="phone_number" >Mobile Number</label>
                                            <input class="form-control" readonly="" type="text" id="phone_number" name="phone_number" value="">                       
                                        </div>                                    
                                        <div class="form-group col-sm-12">
                                            <label title="Make sure suggested network is correct for given mobile number" >Choose Network</label>
                                            <select title="Make sure suggested network is correct for given mobile number" class="form-control" name="company_name" id="company_name">
                                                <option value="">Please Select Network</option>
                                                <?php try{
                                                $comp_name_list = Helpers_Utilities::get_companies_data();
                                                foreach ($comp_name_list as $list) {
                                                    ?>
                                                    <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                                <?php } 
                                                }  catch (Exception $ex){   }?>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-12">
                                            <label for="exampleInputFile">File input</label>                                                
                                            <input type="file" name="file" id="file_control" class="">
                                            <p class="help-block">Upload file from here</p>
                                        </div> 
                                        <div class="form-group col-sm-12">
                                            <button type="submit" class="btn btn-primary pull-right">Submit</button>
                                        </div>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->

                                </form>
                            </div>

                        </div>
                    </div>
                    <!--/.col (left) -->
                </div>                
                <!-- /.box-body -->
            </div>

        </div>
    </div>
    <!--/.col (left) -->

    <!-- Manual Subscriber Entry -->
    <div id="manualsubentry" style="display: none;">

        <div class="box box-primary">
            <?php
            if (isset($_GET["message"]) && $_GET["message"] == 1) {
                ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Congratulation! Data save successfully</h4>
                </div>
            <?php } 
            if(!empty($post_data)){
            ?>
            <div class="box-header with-border">
                    <h3 class="box-title">Recieved Email Response</h3>
                </div>
                <div class="box-body"> 
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <label for="subject">Received File Path</label>
                            <input disabled type="text" class="form-control" id="subject"  name="subject" value="<?php echo $post_recieved_file_path; ?>" placeholder="File Path if any">
                        </div>                             
                        <div class="form-group col-md-12">
                            <label for="body">Received Body</label>
                            <textarea disabled style="height: 100px" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body)){ echo strip_tags($post_recieved_body); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  

                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="box-header with-border">
                <h3 class="box-title">Manual Subscriber Entry</h3>
            </div>
            <form role="form" action="<?php echo url::site() . 'user/data_upload_post' ?>" id="manualsubform" method="post">
                <div class="box-body">                     
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="ForeignerRadio1" class="control-label">Country</label>
                            <div class="radio">                                            
                                <label>
                                    <input type="radio" name="is_foreigner" id="ForeignerRadio1" value="0" checked="" >
                                    Pakistani
                                </label>
                                <label>
                                    <input type="radio" name="is_foreigner" id="ForeignerRadio1" value="1" >
                                    Other
                                </label>
                            </div>
                        </div>
                    </div> 
                    <div class="col-sm-6" id="project_div" style="display: block;margin-top: 10px;">
                        <div class="form-group">                                                            
                                    <label for="inputproject" class="control-label">Link With Project</label> 
                                    <?php try{
                                    $rqts = Helpers_Utilities::get_projects_list();
                                    ?>
                                    <select class="form-control select2" name="inputproject[]" data-placeholder="Select Project Name" id="inputproject" style="width: 100%">
                                        <option value="">Please select project</option>
                                        <?php  foreach ($rqts as $rqt) { ?>
                                            <option value="<?php echo $rqt->id; ?>"><?php echo $rqt->project_name. " [" . $rqt->name . "]"; ?></option>
                                        <?php } 
                                        }  catch (Exception $ex){   }?>                                                                                                                
                                    </select>                                                                                    
                                </div>
                            </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputSubNO" class="control-label">Subscriber Number</label>
                            <input name="mobile_number" type="text" class="form-control" id="inputSubNO" placeholder="Subscriber Number" readonly=""/> 
                            <input name="requestid" type="hidden" class="form-control" id="requestid" value="<?php echo $post_request_id; ?>"  readonly=""/> 
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label title="Date when sim is activated in given company" for="inputActivationDate" class="control-label">SIM Activation Date</label>
                            <input readonly="" title="Date when sim is activated in given company" name="act_date" type="text" class="form-control" id="act_date" placeholder="mm/dd/yyyy"> 
                        </div>
                    </div>                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="imsi" class=" control-label">IMSI</label>
                            <input type="text" name="imsi" class="form-control" id="imsi" placeholder="19 digists number only">
                        </div>
                    </div>                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="imei" class=" control-label">IMEI</label>
                            <input type="text" name="imei" class="form-control" id="inputimei" placeholder="15 digists number only">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputName" class="control-label">First Name</label>
                            <input name="person_name" type="text" class="form-control" id="inputName" placeholder="First Name">
                        </div>
                    </div>                                      
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputName1" class="control-label">Last Name</label>
                            <input name="person_name1" type="text" class="form-control" id="inputName1" placeholder="Last Name">
                        </div>
                    </div>                                      
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label title="In case of foreigner characters in foreigner id replace with digit 9" for="inputCNIC" class="control-label">CNIC</label>
                            <input title="In case of foreigner characters in foreigner id replace with digit 9" name="cnic_number" type="text" class="form-control" id="inputCNIC" placeholder="13 digists number only without dashes">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="inputAddress" class=" control-label">Address</label>
                            <input type="text" name="address" class="form-control" id="inputAddress" placeholder="Address">
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label  for="ConnectionTypeRadio1" class="control-label">Connection Type</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="ConnectionTypeRadios" id="ConnectionTypeRadio1" value="1" checked="">
                                    Pre-Paid
                                </label>                                            
                                <label>
                                    <input type="radio" name="ConnectionTypeRadios" id="ConnectionTypeRadio2" value="0">
                                    Post-Paid
                                </label>
                            </div>

                        </div>
                    </div>                                    
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="StatusRadio1" class="control-label">Status</label>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="StatusRadios" id="StatusRadio1" value="1" checked="">
                                    Active
                                </label>                                            
                                <label>
                                    <input type="radio" name="StatusRadios" id="StatusRadio2" value="0">
                                    In-Active
                                </label>
                            </div>
                        </div>
                    </div>                                     
                    <div class="col-sm-3">
                        <!--                                    <div id="imei_iframe_data" style="display: none">                                            
                                                                <form id="login" method="post" action="http://www.imei.info//login">
                                                                    <input name="next" value="/" type="hidden">
                                                                    <span style="display: none" class="border fleft"><input name="login" id="logini" value="Mohammad Yaser" placeholder="Login" type="text"></span>
                                                                    <span style="display: none" class="border fleft"><input name="password" value="ASADSULTAN" placeholder="Password" type="password"></span>
                                                                    <span class="border fright"><button id="blogin" class="blue" type="submit">Click Here</button></span>
                                                                </form>   
                                                            </div>-->
                        <div class="form-group">
                            <label title="Search details of phone through given imei number, if imei is not given then add default value 999999999999999" for="phone_name" class="control-label">Check Phone Name <a class="btn" target="_blank" href="http://www.imei.info/" <!--onclick="findphonenumber();" --> > 
                                    Click Here                                                     
                                </a>
<!--                                            <span>
                                    <iframe id="foo" style="overflow:hidden; margin: 0; padding: 0; border: none;" scrolling="no" width="75px"  height="50px">                                                        
                                        test
                                    </iframe>
                                    </span>-->
                            </label>
                            <input title="Search details of phone through given imei number, if imei is not given then add default value 999999999999999" name="phone_name" type="text" class="form-control" id="phone_name"> 
                        </div>
                    </div>
                    <div class="col-sm-3" >
<!--                        <div class="form-group">
                            <label for="company_name_get" class="control-label">Company Name </label>                                            
                            <select class="form-control" name="company_name_get" id="company_name_get" style="margin-top: 13px">
                                <option value="">Please Select Company</option>
                                <?php
                              //  $comp_name_list = Helpers_Utilities::get_companies_data();
                              //  foreach ($comp_name_list as $list) {
                                    ?>
                                    <option value="<?php //echo $list->mnc ?>"><?php // echo $list->company_name ?></option>
                                <?php // } ?>

                            </select>
                        </div>-->
                        <div class="form-group"  >
                                            <label title="Search company name of mobile number, this option when you dont know about company name" for="company_name_get" class="control-label"><span>Company Name 
                                                <img id='findcompnay_image' src="<?php echo URL::base(); ?>dist/img/102.gif" style="width: 38px;height: 33px; display: none;">
                                                <a title="Search company name of mobile number, this option when you dont know about company name" id="findcompanyname"  class="btn" onclick="findcompanyname();"> Search (Click) </a></span></label>                                    
                                                <select  class="form-control " name="company_name_get" id="company_name_get"  >
                                                    <option  value="">Please Select Company</option>
                                                <?php   try{                                               
                                                $comp_name_list = Helpers_Utilities::get_companies_data();
                                                foreach($comp_name_list as $list)
                                                {     
                                                 ?>
                                                <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                                <?php } 
                                                }  catch (Exception $ex){   }?>

                                            </select>
                                        </div>

                    </div>
                    <div class="form-group col-sm-12">                                                                                                                
                        <button type="submit" class="btn btn-primary" style="margin-top:10px">Submit</button>                                                        
                    </div>
                </div>
            </form>
        </div>
    </div> 
    <!-- Manual Current Location Entry -->
    <div id="manuallocationentry" style="display: none;">

        <div class="box box-primary">
            <?php
            if (isset($_GET["message"]) && $_GET["message"] == 1) {
                ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Congratulation! Data save successfully</h4>
                </div>
            <?php } 
            if(!empty($post_data)){
            ?>
            <div class="box-header with-border">
                    <h3 class="box-title">Recieved Email Response</h3>
                </div>
                <div class="box-body"> 
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            <label for="subject">Received File Path</label>
                            <input disabled type="text" class="form-control" id="subject"  name="subject" value="<?php  echo $post_recieved_file_path; ?>" placeholder="File Path if any">
                        </div>                             
                        <div class="form-group col-md-12">
                            <label for="body">Received Body</label>
                            <textarea disabled style="height: 100px" class="form-control" id="body" name="body"><?php if(!empty($post_recieved_body)){ echo strip_tags($post_recieved_body); }else{ echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  

                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="box-header with-border">
                <h3 class="box-title">Manual Location Entry</h3>
            </div>                            
            <div class="box-body">
                <form id="manuallocationform" name="manuallocationform" role="form" action="<?php echo url::site() . 'user/location_upload_post' ?>"  method="post" >
                  <input type="hidden" id="locationperson" name="person_id" />                    
                  <input type="hidden" id="requestid" name="requestid" value="<?php echo $post_request_id; ?>" />                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="locationmsisdn" class="control-label">Subscriber Number</label>
                            <input name="locationmsisdn" type="text" class="form-control" id="locationmsisdn" placeholder="Subscriber Number" readonly=""/> 
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="loccompany" class="control-label">Company Name</label>
                            <select  class="form-control" name="loccompany" id="loccompany">
                                <option value="">Select</option>
                                <?php try{
                                $comp_name_list = Helpers_Utilities::get_companies_data();
                                foreach ($comp_name_list as $list) {
                                    ?>
                                    <option value="<?php echo $list->mnc ?>"><?php echo $list->company_name ?></option>
                                <?php }
                                }  catch (Exception $ex){   }?>

                            </select>
                        </div>
                    </div>                    
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="locimsi" class=" control-label">IMSI</label>
                            <input type="text" name="locimsi" class="form-control" id="locimsi" placeholder="IMSI #">
                        </div>
                    </div> 
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="locimei" class=" control-label">IMEI</label>
                            <input type="text" name="locimei" class="form-control" id="locimei" placeholder="IMEI #">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group" >
                            <label for="phone_name" class="control-label">Check Phone Name <a  target="_blank" href="http://www.imei.info/" <!--onclick="findphonenumber();" --> > 
                                    Click Here                                                     
                                </a>
                            </label>
                            <input name="locphonename" type="text" class="form-control" id="phone_name"> 
                        </div>
                    </div>                                     
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="loclat" class="control-label">Latitude</label>
                            <input name="loclat" type="text" class="form-control" id="loclat" placeholder="Latitude">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="loclong" class="control-label">Longitude</label>
                            <input name="loclong" type="text" class="form-control" id="loclong" placeholder="Longitude">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="loccellid" class="control-label">Cell ID</label>
                            <input name="loccellid" type="text" class="form-control" id="loccellid" placeholder="Cell ID">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="loclac" class="control-label">LAC ID</label>
                            <input name="loclac" type="text" class="form-control" id="loclac" placeholder="LAC ID">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="locdate" class="control-label">Location At</label>
                            
                            
                                    <input name="locdate" readonly type="text" class="form-control form_datetime" id="locdate" placeholder="mm/dd/yyyy hh:mm"> 
                                  
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="locnetwork">Network</label>
                        <select class="form-control" id="locnetwork" name="locnetwork">
                            <option value="">Select</option>                                            
                            <option value="0">Unknown</option>
                            <option value="1">2G</option>
                            <option value="2">3G</option>
                            <option value="3">4g/LTE</option>
                        </select>
                    </div> 
                    <div class="form-group col-md-6">
                        <label for="locstatus">Status</label>
                        <select class="form-control" id="locstatus" name="locstatus">
                            <option value="">Select</option>
                            <option value="0">de-attached</option>
                            <option value="1">attached</option>
                            <option value="2">purged</option>
                        </select>
                    </div>                                                                         
                    <div class="form-group col-sm-12">
                        <label for="locaddress" class="control-label">Location Address</label>
                        <input name="locaddress" type="text" class="form-control" id="locaddress" placeholder="Address">
                    </div>
                    <div class="form-group col-sm-12">                                                                                                                
                        <button type="submit" class="btn btn-primary pull-right" style="margin-top:10px">Submit</button>
                    </div>
                </form>
            </div>

        </div>
    </div>           
    <!-- Manual SIMs against CNIC Entry -->
    <div id="manualcnicsimsentry" style="display: none;">

        <div class="box box-primary">
            <?php
            if (isset($_GET["message"]) && $_GET["message"] == 1) {
                ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Congratulation! Data save successfully</h4>
                </div>
            <?php } ?>
            <div class="box-header with-border">
                <h3 class="box-title">Manual SIMs Entry Against CNIC# </h3>
            </div>                            
            <div class="box-body">
                <form role="form" action="<?php echo url::site() . 'user/cnic_upload_post' ?>" id="manualcnicform" name="manualcnicform" method="post">
                <input type="hidden" id="requestid" name="requestid" value="<?php echo $post_request_id; ?>" />                    
                <input type="hidden" id="cnic_is_foreigner_value" name="cnic_is_foreigner_value" value="" />                    
                    <div class="row">
                        <?php if(!empty($post_data)){ ?>
                                        <div class="col-md-12">
                                            <div class="form-group col-md-12">
                                                <label for="subject">Received File Path</label>
                                                <input disabled type="text" class="form-control" id="subject"  name="subject" value="<?php echo $post_recieved_file_path; ?>" placeholder="File Path if any">
                                            </div>                             
                                            <div class="form-group col-md-12">
                                                <label for="body">Received Body</label>
                                                <textarea disabled style="height: 100px" class="form-control" id="body" name="body"><?php if (!empty($post_recieved_body)) { echo strip_tags($post_recieved_body); } else { echo "Email Parsing Error: Email is not fetched from inbox."; } ?></textarea>  
                                            </div>
                                        </div>
                                        <?php } ?>
                        <div class="col-sm-6" id="cnicdetails">
                        </div>
                        <div class="col-sm-6">
                            <input name="cnicsims" type="hidden" class="form-control" id="cnicsims" placeholder="Without Dashes" readonly="">
                            
                            <div class="col-sm-12" id="sim1" style="display: block">
                                <div class="form-group" >
                                    <label for="inputSubNO1" class="control-label">SIM-1</label>
                                    <input name="mobile_number[]" type="text" class="form-control" id="inputSubNO1" placeholder="Subscriber Number" /> 
                                </div>
                            </div>
                            <div class="col-sm-12" id="sim2" style="display: none">
                                <div class="form-group">
                                    <label for="inputSubNO2" class="control-label">SIM-2</label>
                                    <input name="mobile_number[]" type="text" class="form-control" id="inputSubNO2" placeholder="Subscriber Number" /> 
                                </div>
                            </div>
                            <div class="col-sm-12" id="sim3" style="display: none">
                                <div class="form-group">
                                    <label for="inputSubNO3" class="control-label">SIM-3</label>
                                    <input name="mobile_number[]" type="text" class="form-control" id="inputSubNO3" placeholder="Subscriber Number"/> 
                                </div>
                            </div>
                            <div class="col-sm-12" id="sim4" style="display: none">
                                <div class="form-group">
                                    <label for="inputSubNO4" class="control-label">SIM-4</label>
                                    <input name="mobile_number[]" type="text" class="form-control" id="inputSubNO4" placeholder="Subscriber Number" /> 
                                </div>
                            </div>
                            <div class="col-sm-12" id="sim5" style="display: none">
                                <div class="form-group">
                                    <label for="inputSubNO5" class="control-label">SIM-5</label>
                                    <input name="mobile_number[]" type="text" class="form-control" id="inputSubNO5" placeholder="Subscriber Number" /> 
                                </div>
                            </div>
                            <div class="form-group col-sm-12">    
                                <button type="button" name="simcontrol" id="simcontrol"  class="btn btn-primary" onclick="addmoresim();" style="margin-top:10px">More SIM</button>                                                        
                                <button type="submit" onclick="cnic();" class="btn btn-primary pull-right" style="margin-top:10px">Submit</button>                                                        
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        <!-- /sims against cnic record table -->
                                        <div id="cnicrecordtable" style="display: block">
                                            
                                        <hr class="style14 col-md-12"> 
                                            <!-- /.box-header -->
                                            <div class="box-body">
                                                <div class="table-responsive">
                                                    <table id="snicsimstable" class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>SIM#</th>
                                                                <th title="SIM activation on">Activated At</th>
                                                                <th title="SIM last used on">Last Used At</th>
                                                                <th>Status</th>
                                                                <th >Company</th>
                                                                <th title="Current User Of This SIM">SIM User</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>

                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <th>SIM#</th>
                                                                <th title="SIM activation on">Activated At</th>
                                                                <th title="SIM last used on">Last Used At</th>
                                                                <th>Status</th>
                                                                <th >Company</th>
                                                                <th title="Current User Of This SIM">SIM User</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr class="style14 col-md-12"> 
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

        /*
         * 
         var iframe = document.getElementById('foo'),
         iframedoc = iframe.contentDocument || iframe.contentWindow.document;
         iframedoc.body.innerHTML = '<form id="login" method="post" action="http://www.imei.info//login">' + $("#imei_iframe_data").html() + '</form>';
         $("#imei_iframe_data").html('');
         */
        $("#manualsubform").validate({
            rules: {
                mobile_number: {
                    required: true,
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                },
                contact_home: {
                    required: true,
                    number: true,
                    maxlength: 10,
                    minlength: 10
                },
                cnic_number: {
                    required: true,
                    number: true,
                    minlength: 13,
                    maxlength: 13
                },
                act_date: {
                   // required: true,
                    vailddate: true,
                    future:true
                },
                person_name: {
                    required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 30
                },
                person_name1: {
                   // required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 30
                },
                imei: {
                    number: true,
                    minlength: 15,
                    maxlength: 15
                },
                imsi: {
                    number: true,
                    minlength: 19,
                    maxlength: 19
                },
                address: {
                    //required: true,
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 250
                },
                phone_name: {
                    alphanumericspecial: true
                },
                company_name_get: {
                    required: true,
                    check_list: true
                }
            },
            messages: {
                mobile_number: {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },
                contact_home: {
                    required: "Enter Home Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    minlength: "Minimum 10 digits"
                },
                cnic_number: {
                    required: "Enter CNIC Number",
                    Number: "Only number without dashes",
                    maxlenght: "Number should be 13 digits",
                    minlength: "Minimum 13 digits"
                },
                act_date: {
                   // required: "SIM activation date required",
                    vailddate: "mm/dd/yyyy"
                },
                person_name: {
                    required: "Enter Person Name",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
                person_name1: {
                  //  required: "Enter Person Name",
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 1"
                },
                imei: {
                    minlength: "Minimum 15 digits required",
                    maxlenght: "Maximum 15 digits required"
                },
                imsi: {
                    minlength: "Minimum 19 digits required",
                    maxlenght: "Maximum 19 digits required"
                },
                address: {
                    //required: "Enter Address",
                    maxlenght: "Maximum character limit is 250",
                    minlength: "Min character limit is 1"
                },
                phone_name: {
                    alphanumericspecial: "Only Alpha Numaric"
                },
                company_name_get: {
                    required: "Please check Phone Name"
                }
            }

        });
        
        $("#manuallocationform").validate({
            rules: {
                loccompany: {
                    required: true
                },
                locphonename: {
                    alphanumericspecial: true,
                    minlength: 3,
                    maxlength: 30
                },
                locimei: {
                    number: true,
                    minlength: 15,
                    maxlength: 15
                },
                locimsi: {
                    number: true,
                    minlength: 19,
                    maxlength: 19
                },
                loclat: {                    
                    required: true,
                    number: true,
                    minlength: 6,
                    maxlength: 10
                },
                loclong: {
                    number: true,
                    required: true,
                    minlength: 6,
                    maxlength: 10
                },
                loccellid: {
                    number: true,
                    minlength: 4,
                    maxlength: 10
                },
                loclac: {
                    number: true,
                    minlength: 3,
                    maxlength: 10
                },
                locdate: {
                    required: true,
                    dateTime: true,
                    future:true
                },
                locnetwork: {
                    required: true
                },
                locstatus: {
                    required: true
                },
                locaddress: {
                    alphanumericspecial: true,
                    minlength: 1,
                    maxlength: 150
                }
                
                },
                messages:{
                    loccompany: {
                      required:"required"
                  },
                locphonename: {
                    maxlenght: "Maximum character limit is 30",
                    minlength: "Min character limit is 3"
                },
                locimei: {
                    minlength: "Minimum 15 digits required",
                    maxlenght: "Maximum 15 digits required"
                },
                locimsi: {
                    minlength: "Minimum 19 digits required",
                    maxlenght: "Maximum 19 digits required"
                },
                loclat: {                    
                    required:"required",
                    number:"only numbers",
                    minlength: "Minimum 6 digits required",
                    maxlenght: "Maximum 10 digits required"
                },
                loclong: {
                    required:"required",
                    minlength: "Minimum 6 digits required",
                    maxlenght: "Maximum 10 digits required"
                },
                loccellid: {
                    minlength: "Minimum 4 digits required",
                    maxlenght: "Maximum 10 digits required"
                },
                loclac: {
                    minlength: "Minimum 3 digits required",
                    maxlenght: "Maximum 10 digits required"
                },
                locdate: {
                    required: "Location date/time required",
                    dateTime: "mm/dd/yyyy hh:mm"
                },
                locnetwork: {
                    required: "required"
                },
                locstatus: {
                    required: "required"
                },
                locaddress: {
                    maxlenght: "Maximum character limit is 150",
                    minlength: "Min character limit is 1"
                }
                }
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
        $("#manualcnicform").validate({
            rules: {
                "mobile_number[]": {
                    required: true,
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                },
            },
            messages: {
                "mobile_number[]": {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                },

            },

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
        if (elem == 1 || elem.value == 1)
        {
            //Hide      
            $('#cnic_div').hide();
            $('#imei_div').hide();
            $("#msisdn_div_options").hide();
            $("#msisdn_div_options_cdr_link").hide();
            $("#msisdn_div_options_sub_link").hide();
            $("#manualcnicsimsentry").hide();
            $("#manualimeientry").hide();
            $("#manualsubentry").hide();
            $("#manuallocationentry").hide();
            //  $("#manualimeisimsentry").hide();
            $("#manualupload").hide();
            //show   
            $('#msisdn_div').show();
        } else if (elem == 2 || elem.value == 2)
        {
            //Hide      
            $('#msisdn_div').hide();
            $('#imei_div').hide();
            $("#msisdn_div_options").hide();
            $("#msisdn_div_options_cdr_link").hide();
            $("#msisdn_div_options_sub_link").hide();
            $("#manualcnicsimsentry").hide();
            $("#manualimeientry").hide();
            $("#manualsubentry").hide();
            $("#manuallocationentry").hide();
            //  $("#manualimeisimsentry").hide();
            $("#manualupload").hide();
            //show   
            $('#cnic_div').show();
        } else if (elem == 3 || elem.value == 3)
        {
            //Hide      
            $('#cnic_div').hide();
            $('#msisdn_div').hide();
            $("#msisdn_div_options").hide();
            $("#msisdn_div_options_cdr_link").hide();
            $("#msisdn_div_options_sub_link").hide();
            $("#manualcnicsimsentry").hide();
            $("#manualimeientry").hide();
            $("#manualsubentry").hide();
            $("#manuallocationentry").hide();
            // $("#manualimeisimsentry").hide();
            $("#manualupload").hide();
            //show   
            $('#imei_div').show();
            if (countdiv3view == 1) {
                $("#device_information").html('<h3><span class="label label-success" ><b>Please Enter IMEI# Before Uploading<b></span></h3>');
                $("#device_information").hide();
                countdiv3view = countdiv3view + 1;
            }
        } else
        {
            //Hide      
            $('#cnic_div').hide();
            $('#imei_div').hide();
            $('#msisdn_div').hide();
            $("#msisdn_div_options").hide();
            $("#msisdn_div_options_cdr_link").hide();
            $("#msisdn_div_options_sub_link").hide();
            $("#manualcnicsimsentry").hide();
            $("#manualimeientry").hide();
            $("#manualsubentry").hide();
            $("#manuallocationentry").hide();
            //  $("#manualimeisimsentry").hide();
            $("#manualupload").hide();
        }

    }

    //function to add more sims
    var simscount = 1;
    var simscount1 = 1;
    function addmoresim() {
        // function to control button count  
        if (simscount1 == 1) {
            simscount = simscount + 1;
            if (simscount == 6) {
                simscount1 = 2;
            }
        }
        if (simscount1 == 2) {
            if (simscount > 1) {
                if (simscount == 6) {
                    simscount = simscount - 2;
                } else {
                    simscount = simscount - 1;
                }
            } else {
                simscount1 = 1;
                simscount = simscount + 1;
            }
        }
        if (simscount == 5) {
            $('#simcontrol').html('Less SIM');
        }
        if (simscount == 1) {
            $('#simcontrol').html('More SIM');
        }
        //code to control button
        if (simscount > 1)
        {
            $('#sim2').show();
        } else {
            $('#sim2').hide();
        }

        if (simscount > 2)
        {
            $('#sim3').show();
        } else {
            $('#sim3').hide();
        }
        if (simscount > 3)
        {
            $('#sim4').show();
        } else {
            $('#sim4').hide();
        }
        if (simscount > 4)
        {
            $('#sim5').show();
        } else {
            $('#sim5').hide();
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

    /* For cnic */
    function cnic() {
        var form1 = $('.cnic_form');
        form1.validate({
            rules: {
                cnic_no: {
                    required: true,
                    number: true,
                    maxlength: 13,
                    minlength: 13
                },
            },
            messages: {
                cnic_no: {
                    required: "Enter CNIC Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 13 digits",
                    minlength: "Minimum 13 digits"
                },
            },

            submitHandler: function () {
                // alert($('#email_sub').val());
                var cnic_no = $("#cnic_no").val();var cnic_is_foreigner=document.querySelector('input[name="cnic_is_foreigner"]:checked').value;
                var cnic_is_foreigner=document.querySelector('input[name="cnic_is_foreigner"]:checked').value;
                $('#cnic_div').hide();
                $("#manualcnicsimsentry").show();
                $("#cnicsims").val($("#cnic_no").val());
                $("#cnic_is_foreigner_value").val(cnic_is_foreigner);
                //ajax call to get cnic details
                var request = $.ajax({
                    url: "<?php echo URL::site("upload/checkcnicdetails"); ?>",
                    type: "POST",
                    dataType: 'text',
                    data: {cnic_no: cnic_no,cnic_is_foreigner:cnic_is_foreigner},
                    success: function (cnicdetails)
                    {                        
                         $("#cnicdetails").html(cnicdetails);
                        },
                    
                });
               cnicsimstable(); 
            }
        });


    }

    /* For CNIC SIMS Details */
    function cnicsimstable() {
      var cnicno = $("#cnic_no").val();  
      if (typeof objDT != 'undefined')
                {  //objDT.destroy(); 

                    var newUrl = "<?php echo URL::site('personsreports/ajaxcnicsimsdetails/', TRUE); ?>" + "/" + cnicno;

                    objDT.fnReloadAjax(newUrl);

                    //refreshGrid();
                } else {

                    objDT = $('#snicsimstable').dataTable(
                            {//"aaSorting": [[2, "desc"]],
                                "bPaginate": true,
                                "bProcessing": true,
                                //"bStateSave": true,
                                "bServerSide": true,
                                "sAjaxSource": "<?php echo URL::site('personsreports/ajaxcnicsimsdetails/', TRUE); ?>" + "/" + cnicno,
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
    }
    /* For msisdn */
    function msisdn() {
        var form1 = $('.msisdn_form');
        form1.validate({
            rules: {
                msisdn_no: {
                    required: true,
                    number: true,
                    numberthree: true,
                    maxlength: 10,
                    minlength: 10
                }
            },
            messages: {
                msisdn_no: {
                    required: "Enter Mobile Number",
                    Number: "Enter only number",
                    maxlenght: "Maximum 10 digits",
                    numberthree: "Numbers must be 10 digits, starting from 3",
                    minlength: "Minimum 10 digits"
                }
            },

            submitHandler: function () {
                // alert($('#email_sub').val());
                var msisdn_no = $("#msisdn_no").val();  
                var request = $.ajax({
                    url: "<?php echo URL::site("upload/checkmsisdn"); ?>",
                    type: "POST",
                    dataType: 'text',
                    data: {msisdn_no: msisdn_no},
                    success: function (responseTex)
                    {
                        if (responseTex == -1)
                        {
                            $("#msisdn_div_options_sub_link").show();
                            $("#msisdnerrorhandle").val('-1');
                            $("#msisdn_div_options").show();
                            $("#msisdn_div").hide();
                           // $("#sub_link_val").attr("onclick", "<?php // echo URL::site("userrequest/request/1/3"); ?>" + "/" + msisdn_no);

                        } else {                            
                            $("#msisdnerrorhandle").val('');
                            $("#msisdn_div_options_cdr_link").show();
                            $("#msisdn_div_options").show();
                           // checkerror
                            $("#phone_number").val(responseTex);
                            $("#locationperson").val(responseTex);
                            $("#msisdn_div").hide();
                            //Helpers_Utilities::encrypted_key($person_id,"encrypt")
                            $("#cdr_link_val").attr("href", "<?php echo URL::site("persons/dashboard"); ?>" +"/?id="+responseTex);
                       
                       //ajax call to get subscriber info
                       var msisdn_number = msisdn_no;
                var request = $.ajax({
                    url: "<?php echo URL::site("upload/checkmsisdndetail"); ?>",
                    type: "POST",
                    dataType: 'text',
                    data: {msisdn_number: msisdn_number},
                    success: function (msisdndetail)
                    {
                        
                         $("#subscriberinfo").html(msisdndetail);
                        },
                    
                });
                       
                       }

                    },
                    error: function (jqXHR, textStatus) {
                        alert("Unable to reach");
                    }
                });
            }
        });
        jQuery.validator.addMethod("numberthree", function (value, element) {
            return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");

        jQuery.validator.addMethod("alphanumericspecial", function (value, element) {
            return this.optional(element) || value == value.match(/^[-a-zA-Z0-9_ .,/]+$/);
        }, "Only letters, Numbers,Dot & Space/underscore Allowed.");

    }

    /* For msisdn back */
    function msisdn_back() {
        $("#msisdn_div").show();
        $("#cnic_div").hide();
        $("#imei_div").hide()
        $("#msisdn_div_options").hide();
        $("#msisdn_div_options_cdr_link").hide();
        $("#msisdn_div_options_sub_link").hide();
    }
    /* Update Device name*/
    function changedevicename(dev) {
        if(dev==1){            
        $("#updatedevicename").show();
        }else{
           $("#updatedevicename").hide(); 
        }
    }
  

    /* For to open manual location entry */
    function openmanuallocationentry() {
        $("#locationmsisdn").val($("#msisdn_no").val());
        $("#msisdn_div_options").hide();
        $("#msisdn_div").hide();
        $("#cnic_div").hide();
        $("#imei_div").hide();
        $("#manuallocationentry").show();
        $("#manualcnicsimsentry").hide();
        $("#manualsubentry").hide();        
        $("#manualupload").hide();
    }

    /* For to open manual cnic entry */
    function openmanualcnicsimsentry() {
        $("#cnicsims").val($("#cnic_no").val());
        $("#msisdn_div_options").hide();
        $("#msisdn_div").hide();
        $("#cnic_div").hide();
        $("#imei_div").hide();
        $("#manuallocationentry").hide();
        $("#manualcnicsimsentry").show();
        $("#manualsubentry").hide();
    }

    /* For to open manual cdr upload */
    function openmanualupload() {        
        $("#cdr_against_mobile_upload_status").show();
        var subnumber = $("#msisdn_no").val();        
        var request = $.ajax({
            url: "<?php echo URL::site("upload/checkcompany"); ?>",
            type: "POST",
            dataType: 'text',
            data: {number: subnumber},
            success: function (responseTex)
            {
                if (responseTex == -1)
                {
                    alert('Failed to recognize');
                    $("#company_name_get").attr("readonly", false);

                } else {
                   // alert(subnumber);
                    //$("#msisdn_div_options").hide();
                    $("#msisdn_div").hide();
                    $("#manual_uuploads #phone_number").val(subnumber);
                    $("#manualupload").show();
                    $("#company_name").val(responseTex);                    
                      $("#cdr_against_mobile_upload_status").hide();
                }

            },
            error: function (jqXHR, textStatus) {
                alert('Failed to recognize, Please Select Manually');
                $("#company_name_get").attr("readonly", false);
            }
        });


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
                formData.append('imei_no', $('#imei_no').val());
                formData.append('requestid', $('#requestid_cdr_against_imei').val());
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
                /* updated by yaser */
                var imei = $("#imei_no").val();
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
                var imei_no = $("#imei_no").val();
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
                        $("#imeicdruploadstatus").show();
                        $("#imeicdruploadstatus").html(msg);
                    }
                });

                //submit via ajax
                return false;  //This doesn't prevent the form from submitting.
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
   $("#imeicompany").val('');
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
                imeicompany: {
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
                imeicompany: {
                    required: "required",
                }
            },
            submitHandler: function (event, validator) {       
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
     /* For to open manual subscriber entry */
    function openmanualsubentry(type) {
    //alert(type);
        if(type==1) {
            
        $("#manualupload").hide();
        $("#inputSubNO").val($("#msisdn_no").val());
        $("#msisdn_div_options").hide();
        $("#msisdn_div").hide();
        $("#cnic_div").hide();
        $("#imei_div").hide();
        $("#manuallocationentry").hide();
        $("#manualcnicsimsentry").hide();
        $("#manualsubentry").show();
        // ajax call to upadte existing sub info
       var subnumber = $("#inputSubNO").val();
        var request = $.ajax({
            url: "<?php echo URL::site("upload/getsubinfo"); ?>",
            type: "POST",
           dataType: 'json',
            data: {number: subnumber},
            success: function (sub)
            {
              //  console.log(sub.mnc);
              //  alert(sub.mnc);              
              if(sub.actdate==0)
                sub.actdate='';
              $("#act_date").val(sub.actdate);  
              if(sub.imsi_number==0)
                sub.imsi_number='';
              $("#imsi").val(sub.imsi_number);    
              $("#inputName").val(sub.first_name);  
              $("#inputName1").val(sub.last_name);  
              $("#inputCNIC").val(sub.cnic_number);
            //  $("inputCNIC").prop('disabled', true);
              $("#inputAddress").val(sub.address);  
              $("#phone_name").val(sub.phone_name);  
              $("#company_name_get").val(sub.mnc); 
              $("#inputproject").attr('disabled',true); 
              var is_foreigner=sub.is_foreigner;
                if (parseInt(is_foreigner) == 0) {
                        $('input[name="is_foreigner"][value="0"]').not(':checked').prop("checked", true); 
                 } else {
                        $('input[name="is_foreigner"][value="1"]').not(':checked').prop("checked", true);
                }
              var sts=sub.status;
                if (parseInt(sts) == 0) {
                        $('input[name="StatusRadios"][value="0"]').not(':checked').prop("checked", true);  
                 } else {
                        $('input[name="StatusRadios"][value="1"]').not(':checked').prop("checked", true);
                }
              var ctype=sub.connection_type;
                if (parseInt(ctype) == 0) {
                        $('input[name="ConnectionTypeRadios"][value="0"]').not(':checked').prop("checked", true);  
                 } else {
                        $('input[name="ConnectionTypeRadios"][value="1"]').not(':checked').prop("checked", true);
                }

            },
            error: function (jqXHR, textStatus) {
                alert('Failed to get existing details, Please Reload Page');
            }
        }); 
        
        //second ajax call to update imei
        var request = $.ajax({
            url: "<?php echo URL::site("upload/getsimlastdeviceimei"); ?>",
            type: "POST",
           dataType: 'json',
            data: {number: subnumber},
            success: function (imei)
            {
              //  console.log(sub.mnc);
              //  alert(imei.imei_number); 
              //  alert(imei.phone_name); 
              if(imei.imei_number!=-1){
              $("#inputimei").val(imei.imei_number);
              $("#phone_name").val(imei.phone_name);
          }
            }
        }); 
    }else{
        $("#msisdn_div_options").hide();
        $("#msisdn_div").hide();
        $("#cnic_div").hide();
        $("#imei_div").hide();
        $("#manuallocationentry").hide();
        $("#manualcnicsimsentry").hide();
        $("#manualsubentry").show();
        // empty form
        document.getElementById("manualsubform").reset();        
        $("#inputSubNO").val($("#msisdn_no").val());
        } 
    }
        
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
//function to call request page for subscriber
    function requestsubviaemail() {        
        var sim = $("#msisdn_no").val(); 
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
                'value': -1,
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
if(!empty($post_request_type_id) && $post_request_type_id==3 && !empty($post_request_value))
    {    
?>
<script>
 $(document).ready(function () {  
     showDiv(1);
     $("#msisdn_no").val(<?php echo $post_request_value; ?>);
        jQuery(".msisdn_submit_btn").trigger("click");  
        // msisdn();                                    
        var fixsub=$("#msisdnerrorhandle").val();
        //alert(fixsub);
        if(fixsub===-1){
           // jQuery(".manualsubentry_link").trigger("click"); 
          // alert('new');
        openmanualsubentry(0);        
    }else{
       // alert('update')
       // jQuery(".manualsubentryexist_link").trigger("click");  
        openmanualsubentry(1);
    }    
    });
</script>
<?php
    }elseif(!empty($post_request_type_id) && $post_request_type_id==4 && !empty($post_request_value)){
    ?>
<script>
 $(document).ready(function () {  
     showDiv(1);
     $("#msisdn_no").val(<?php echo $post_request_value; ?>);
        jQuery(".msisdn_submit_btn").trigger("click");  
        // msisdn();                                    
        var fixsub=$("#msisdnerrorhandle").val();
        //alert(fixsub);
        if(fixsub===-1){
           // jQuery(".manualsubentry_link").trigger("click"); 
          // alert('new');
       // openmanualsubentry(0);        
    }else{
       // alert('update')
       // jQuery(".manualsubentryexist_link").trigger("click");  
        openmanuallocationentry();
    }    
    });
</script>

    <?php 
    }elseif(!empty($post_request_type_id) && $post_request_type_id==1 && !empty($post_request_value)){
    ?>
<script>
 $(document).ready(function () {  
     showDiv(1);
     $("#msisdn_no").val(<?php echo $post_request_value; ?>);
        jQuery(".msisdn_submit_btn").trigger("click");  
        var fixsub=$("#msisdnerrorhandle").val();
        if(fixsub===-1){     
    }else{ 
        openmanualupload();
    }    
    });
</script>

    <?php
    }elseif(!empty($post_request_type_id) && $post_request_type_id==5 && !empty($post_request_value)){
    ?>
<script>
 $(document).ready(function () {  
     showDiv(2);
     $("#cnic_no").val(<?php echo $post_request_value; ?>);
        jQuery(".cnic_submit_btn").trigger("click");    
           
    });
</script>
    <?php
    }elseif(!empty($post_request_type_id) && $post_request_type_id==2 && !empty($post_request_value)){
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

<script>
