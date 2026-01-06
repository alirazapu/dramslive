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
       <i class="fa fa-list"></i> Password Reset Requests
        <small>Tracer</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>	
        <li><a href="#"> Admin Reports</a></li>
        <li class="active">Password Reset Requests</li>
      </ol>
    </section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">


        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <?php
                    if (!empty($_GET['accessmessage'])) {
                        ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> <?php echo $_GET['accessmessage']; ?></h4>
                        </div>
                    <?php } ?>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                        
                                        <th>Name</th>
                                        <th>User name</th>                                                                               
                                        <th>Region</th>                                                                               
                                        <th>Posting</th>
                                        <th>Job Titile</th>
                                        <th>Mobile Number</th>                                        
                                        <th>Temp Password</th>                                        
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <?php if(!empty($records)){ 
                                        foreach($records as $record){ 
                                            ?>
                                            <tr class="item-<?php echo $record['id'] ?>">                                        
                                                <td ><?php echo (!empty($record['name']) ? $record['name'] : ''); ?></td>                                       
                                                <td ><?php echo (!empty($record['username']) ? $record['username'] : ''); ?></td>                                       
                                                <td ><?php echo (!empty($record['region_id']) ? Helpers_Utilities::get_region($record['region_id']) : ''); ?></td>                                       
                                                <td ><?php echo (!empty($record['posted']) ? Helpers_Profile::get_user_posting($record['posted']) : ''); ?></td>                    
                                                <td ><?php echo (!empty($record['job_title']) ? ($record['job_title']) : ''); ?></td>                                       
                                                <td ><?php echo (!empty($record['mobile_number']) ? ($record['mobile_number']) : ''); ?></td>                                                                                       
                                                <td ><?php echo (!empty($record['reset_password_text']) ? ($record['reset_password_text']) : ''); ?></td>                                                                                       
                                                <td>
                                                    <a class="btn btn-small action" href="javascript:ConfirmChoice('<?php echo $record['id']; ?>')"><i class="fa fa-send"></i> Set Password </a>
                                                    <a class="btn btn-small action" href="javascript:cancelrequest('<?php echo $record['id']; ?>')"><i class="fa fa-close"></i> Cancel </a>
                                                </td>
                                            </tr>    
                                    <?php                                      
                                        }                                         
                                    } ?>
                                </tbody>
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
   function ConfirmChoice(id) 
    { 
       // var elem = $(this).closest('.item');
        var elem = $(".item-" + id);
        $.confirm({
            'title'     : 'Password Reset',
            'message'   : 'Do you really want to set this password as user\'s new password?',
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){        
                        $.ajax({
                                url: '<?php echo URL::base() . "Adminreports/set_temp_password/"; ?>'  + id , 
                                success: function(result){
                                if (result == 1) {
                                    swal("Congratulations!", "Password changed successfully", "success");
                                    elem.slideUp();
                                } else {
                                    swal("System Error", "Contact Technical Support Team.", "error");                                
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
   //Cancel password reset request
    function cancelrequest(id) {
        var elem = $(".item-" + id);
        $.confirm({
            'title': 'Cancel request confirmation',
            'message': 'Do you really want to cancel password reset request?',
            'buttons': {
                'Yes': {
                    'class': 'gray',
                    'action': function () {
                        $.ajax({
                            url: "<?php echo URL::site("user/request_cancel"); ?>" + "/" + id,
                            cache: false,
                            success: function (msg) {
                                elem.slideUp();
                            }
                        });
                    }
                },
                'No': {
                    'class': 'blue',
                    'action': function () {}  // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }
    
</script>  