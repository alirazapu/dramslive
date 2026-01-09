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
       <i class="fa fa-list"></i> IP Blocked List
        <small>DRAMS</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>	
        <li><a href="#"> Admin Reports</a></li>
        <li class="active">IP Blocked List</li>
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
                                        <th>IP Address</th>
                                        <th>Login Attempts</th>                                                                               
                                        <th>User Name</th>                                                                               
                                        <th>Last Login</th>
                                        <th>Is Blocked</th>
                                        <th>Reason</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <?php if(!empty($records)){ 
                                        foreach($records as $record){ 
                                            $user_name = Helpers_Utilities::get_user_name_from_ip($record['IP']); ?>
                                            <tr class="item-<?php echo $record['id'] ?>">                                        
                                                <td ><?php echo (!empty($record['IP']) ?  $record['IP'] . ' ' .$user_name : ''); ?></td>                                       
                                                <td ><?php echo (!empty($record['Attempts']) ? $record['Attempts'] : ''); ?></td>                                       
                                                <td ><?php echo (!empty($record['Username']) ? $record['Username'] : ''); ?></td>                                       
                                                <td ><?php echo (!empty($record['LastLogin']) ? $record['LastLogin'] : ''); ?></td>                                       
                                                <td ><?php echo ((!empty($record['is_block']) && $record['is_block'] == 1) ? 'YES' : 'NO'); ?></td>                                       
                                                <td ><?php echo (!empty($record['reason']) ? trim($record['reason']) : ''); ?></td>                                       
                                                <td>
                                                    <a class="btn btn-small action" href="javascript:ConfirmChoice('<?php echo $record['id']; ?>')"><i class="fa fa-trash"></i> Delete</a>
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
            'title'     : 'Delete Confirmation',
            'message'   : 'Do you really want to delete this?',
            'buttons'   : {
                'Yes'   : {
                    'class' : 'gray',
                    'action': function(){        
                        $.ajax({
                                url: '<?php echo URL::base() . "Adminreports/delete_ip_from_blocked_ip_list/"; ?>'  + id , 
                                success: function(result){
                                if (result == 1) {
                                    swal("Congratulations!", "IP Delete Was Successfull", "success");
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
    
</script>  