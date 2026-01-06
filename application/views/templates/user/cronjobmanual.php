
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

 <?php 
    $user = Auth::instance()->get_user();
    $permission = Helpers_Utilities::get_user_permission($user->id);
    //get user posting
    $login_user = Auth::instance()->get_user();
    $login_user_profile = Helpers_Profile::get_user_perofile($login_user->id);
    $posting = $login_user_profile->posted;
    $result = explode('-', $posting);
?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa fa-list"></i> Cron Job Manual Page</h3>                        
                        </div>
                        <div class="box-body no-padding">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="loader-div" style="display: none">
                                    <div class="loader1">Loading...</div>
                                    </div>
                                    <div class="pad">
                                       <?php if ($permission==1) { ?>  
                                            <input id="send_email" class="blue btn-primary" type="button" value="Send Emails">
                                            <input id="send_current_email" class="blue btn-primary" type="button" value="Send Current Location Emails">
                                       <?php } ?>  
                                        <input id="receive_email"  class="blue btn-primary" type="button" value="Receive Emails">
                                        <input id="parse_cdr_file"  class="blue btn-primary" type="button" value="Parse CDR Against Mobile Number">
                                        <input id="parse_imei_file"  class="blue btn-primary" type="button" value="Parse CDR Against IMEI Number">
                                        <input id="parse_sub_file"  class="blue btn-primary" type="button" value="Parse Subscriber Against Mobile Number">
                                        <input id="parse_loc_file"  class="blue btn-primary" type="button" value="Parse Current Location">
                                        <input id="parse_sim_file"  class="blue btn-primary" type="button" value="Parse SIM's Against CNIC">                                        
                                        <input id="reset_parse_queue"  class="blue btn-primary" type="button" value="Resend Parse Error in Parsing Queue">
                                        <input id="family_tree_complete"  class="blue btn-primary" type="button" value="Mark Complete Family and PTCL">
                                        <input id="resend_queue"  class="blue btn-primary" type="button" value="Resend Email Sending Error">
                                        <a target="_blank" class="blue btn-primary" href="http://www.aies.ctdpunjab.com/cronjob/email_parse_phone_1"><i class="fa fa-circle-o"></i> Mobilink CDR Parse</a>
                                        <a target="_blank" class="blue btn-primary" href="http://www.aies.ctdpunjab.com/cronjob/email_parse_phone_7"><i class="fa fa-circle-o"></i> Warid CDR Parse</a>
                                        <a target="_blank" class="blue btn-primary" href="http://www.aies.ctdpunjab.com/cronjob/email_parse_phone_3"><i class="fa fa-circle-o"></i> Ufone CDR Parse</a>
                                        <a target="_blank" class="blue btn-primary" href="http://www.aies.ctdpunjab.com/cronjob/email_parse_phone_6"><i class="fa fa-circle-o"></i> Telenor CDR Parse</a>
                                        <a target="_blank" class="blue btn-primary" href="http://www.aies.ctdpunjab.com/cronjob/email_parse_phone_4"><i class="fa fa-circle-o"></i> Zong CDR Parse</a>
                                        
                                    </div>                                                                                                                              
                                </div>
                                <!-- /.col -->               
                            </div>
                            <!-- /.row -->
                        </div>
                    </div>
                    <!-- /.box -->
                </div>
            </div>
        </div>
    </div>
    <div style="display:none" id="div-dialog-warning">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span><div/></p>
    </div>
</section>


<style>

.blue {
    font-size: 18px;
    border: 2px solid #4fc1e9;
    color: #1e1f1f;
    padding: 8px 15px;
    display: inline-block;
    text-align: center;
    font-weight: 500;
    outline: none;
    cursor: pointer;    
    background: none;
    margin: 10px;    
}    

#loading-indicator {
  position: absolute;
  left: 10px;
  top: 10px;
}

.loader1 {
  width: 250px;
  height: 50px;
  line-height: 50px;
  text-align: center;
  position: absolute;
  top: 50%;
  left: 50%;
  -webkit-transform: translate(-50%, -50%);
          transform: translate(-50%, -50%);
  font-family: helvetica, arial, sans-serif;
  text-transform: uppercase;
  font-weight: 900;
  color: #ce4233;
  letter-spacing: 0.2em;
}
.loader1::before, .loader1::after {
  content: "";
  display: block;
  width: 15px;
  height: 15px;
  background: #ce4233;
  position: absolute;
  -webkit-animation: load .7s infinite alternate ease-in-out;
          animation: load .7s infinite alternate ease-in-out;
}
.loader1::before {
  top: 0;
}
.loader1::after {
  bottom: 0;
}

@-webkit-keyframes load {
  0% {
    left: 0;
    height: 30px;
    width: 15px;
  }
  50% {
    height: 8px;
    width: 40px;
  }
  100% {
    left: 235px;
    height: 30px;
    width: 15px;
  }
}

@keyframes load {
  0% {
    left: 0;
    height: 30px;
    width: 15px;
  }
  50% {
    height: 8px;
    width: 40px;
  }
  100% {
    left: 235px;
    height: 30px;
    width: 15px;
  }
}

</style>
<script src="<?php echo URL::base(); ?>dist/js/styletype.js"></script>