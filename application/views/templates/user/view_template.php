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
                        <i class="fa fa-files-o"></i>
                        Request Status
                        <small>DRAMS</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li><a href="<?php echo URL::site('userrequest/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li><a href="<?php echo URL::site('userrequest/request_status'); ?>">Request Status</a></li>
                        <li class="active">Request Status Detail</li>
                    </ol>
                </section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="">
                <div class="box box-primary">
                    <div class="row">
                        <div style="margin:15px; padding-bottom: 15px">                      
                            <div class="col-md-12">
                                <h4 class="col-md-2 det-col">Created By</h4>  
                                <?php try{
                                    $userdetails = Helpers_Utilities::get_user_name($user_id);
                                    ?>
                                <p class="text-muted well well-sm no-shadow col-xs-10" style="margin-top: 10px; ">                      
                                    <?php echo $userdetails; }  catch (Exception $ex){   }?>
                                </p>
                            </div>
                            <div class="col-md-12">
                                <h4 class="col-md-2 det-col">Email Type</h4>
                                <?php try{
                                    $rqts = Helpers_Utilities::emailtemplatetype($results['email_type']);
                                    ?>
                                <p class="text-muted well well-sm no-shadow col-xs-10" style="margin-top: 10px; ">                      
                                    <?php echo $rqts['email_type_name']; }  catch (Exception $ex){   }?>
                                </p>
                            </div>
                            <div class="col-md-12">
                                <h4 class="col-md-2 det-col">Company Name</h4>
                                <?php try{
                                $comp = Helpers_Utilities::get_companies_data($results['company_id']);
                                ?> 
                                <p class="text-muted well well-sm no-shadow col-xs-10" style="margin-top: 10px; ">                      
                                    <?php echo $comp->company_name; }  catch (Exception $ex){   }?>
                                </p>
                            </div>
<!--                            <div class="col-md-12">
                                <h4 class="col-md-2 det-col">SMTP Server</h4>
                                <p class="text-muted well well-sm no-shadow col-xs-10" style="margin-top: 10px; ">                      
                                   <?php echo $results['smtp_server']; ?>
                                </p>
                            </div>
                            <div class="col-md-12">
                                <h4 class="col-md-2 det-col">Email From</h4>
                                <p class="text-muted well well-sm no-shadow col-xs-10" style="margin-top: 10px; ">                      
                                   <?php echo $results['from_email']; ?>
                                </p>
                            </div>-->
                            <div class="col-md-12">
                                <h4 class="col-md-2 det-col">Email Subject</h4>
                                <p class="text-muted well well-sm no-shadow col-xs-10" style="margin-top: 10px; ">                      
                                   <?php echo $results['subject']; ?>
                                </p>
                            </div>
                            <div class="col-md-12">
                                <h4 class="col-md-2 det-col">Email Body</h4>              
                                <pre class="text-muted well well-sm no-shadow col-xs-10" style="margin-top: 10px; "> <?php echo $results['body_txt']; ?></pre>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

