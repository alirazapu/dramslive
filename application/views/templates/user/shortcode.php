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
       <i class="fa fa-circle-o"></i> Short Code List
        <small>Tracer</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>	
        <li><a href="#"> Short Code</a></li>
        <li class="active">List Short Code</li>
      </ol>
    </section>
<!-- Main content -->
<section class="content">

    <div class="container-fluid">


        <div class="row">
            <div class="col-xs-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> Short Code List</h3>
                    </div>
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

                                        <th>Company Name</th>
                                        <th>Code</th>
                                        <th>Created By</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <?php if(!empty($records)){ ?>
                                    <?php foreach($records as $record){
                                        $data = Helpers_Utilities::get_shortcode_data($record['id']);
//                                        echo '<pre>';
//                                        print_r($data);
//                                        exit();

                                        $comp_name = $data->company_name;
                                        $code = $data->code;
                                       // $code = $data['code'];
                                        $created_by = !empty($data->created_by) ? $data->created_by : 0;

                                        $created_by_name = Helpers_Utilities::get_user_name($created_by);
                                        $e_record_id = Helpers_Utilities::encrypted_key($record['id'],"encrypt") ;
                                        ?>
                                    <tr class="item-<?php echo $record['id'] ?>">

                                        <td><?php echo $comp_name; ?></td>
                                        <td><?php echo $code; ?></td>
                                        <td><?php echo $created_by_name; ?></td>
                                        <td>
<!--                                            <a class="btn btn-small action" href="--><?php //echo URL::base() . "shortcode/view_short_code/" .$e_record_id ; ?><!--"><i class="fa fa-folder-open-o"></i> View</a>-->
                                            <a class="btn btn-small action" href="<?php echo URL::base() . "shortcode/showform/" . $e_record_id; ?>"><i class="fa fa-edit"></i> Edit</a>
                                            <a class="btn btn-small action" href="javascript:ConfirmChoice('<?php echo $record['id']; ?>')"><i class="fa fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                    <?php  } ?>
                                    <?php  } ?>
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
                        $.ajax({url: '<?php echo URL::base() . "shortcode/del/"; ?>'  + id , success: function(result){
                                    if (result == '-2') {
                                    swal("System Error", "Contact Support Team.", "error");
                                } else {
                                elem.slideUp();
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