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
        Person's Portal
        <small><?php echo Helpers_Person::get_person_name(Helpers_Utilities::encrypted_key($_GET['id'], "decrypt")); ?></small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id='.$_GET['id']); ?>">Person Dashboard </a></li>
        <li class="active">User Feedback</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
        <div class="box box-warning direct-chat direct-chat-warning">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">User's Feedback about <?php try{ echo Helpers_Person::get_person_name($person_id_feed); }  catch (Exception $ex){   }?></h3>
                    <div class="box-tools pull-right">
                        <span id="feed_count" data-toggle="tooltip" title="" class="badge bg-yellow" data-original-title="Total Feedback"><?php echo $rowcount_feed ?></span>
                        <button type="button" title="Show/Hide Feedback" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                                            
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-footer">
                <div class="input-group">
                        <input type="text" id="message_text" name="message_text" placeholder="Type Message to send feedback about this person..." class="form-control">
                        <span class="input-group-btn">
                            <button type="button" onclick="sendmessage(<?php echo $user_id_feed; ?>,<?php echo $person_id_feed; ?>)" class="btn btn-primary btn-flat">Send</button>
                        </span>
                    </div>
                </div>
                <div class="box-body">
                    <!-- Conversations are loaded here -->
                    <div id="cont" class="direct-chat-messages" style="height: 400px">

                    </div>
                </div>
                <!-- /.box-body -->
<!--                <div class="box-footer">

                    <div class="input-group">
                        <input type="text" id="message_text" name="message_text" placeholder="Type Message ..." class="form-control">
                        <span class="input-group-btn">
                            <button type="button" onclick="sendmessage(<?php /* echo $user_id_feed; */?>,<?php /* echo $person_id_feed; */ ?>)" class="btn btn-warning btn-flat">Send</button>
                        </span>
                    </div>

                </div>-->
                <!-- /.box-footer-->
            </div>
            <!--/.direct-chat -->
        </div>
        </div>
    </div>
</section>
<!-- /.content -->
<script >
    $(document).ready(function () {
        // alert("I am an alert box!");
        $.ajax({url: "<?php echo URL::site("persons/ajaxusersfeedback/?id=".$_GET['id']); ?>",
            cache: false,
              data: {id: '<?php echo $_GET['id'];?>'},
            dataType: "html",
            success: function (result) {
                if (result == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                $("#cont").html(result);
               // $("#cont").animate({scrollTop: $('#cont').prop("scrollHeight")}, 1000)
            }});
    });
    function sendmessage(u_id, p_id) {
        var message = $("#message_text").val();
        if ($("#message_text").val() == "") {
            alert("Enter some text as feedback");
        } else {
            var result = {messg: message, uid: u_id, pid: p_id};
            $.ajax({
                url: "<?php echo URL::site("persons/feedback/?id=".$_GET['id']); ?>",
                type: 'POST',
                data: {messg: message, uid: u_id, pid: p_id},
                cache: false,
                //dataType: "text",
                success: function (msg) {
                    $("#message_text").val("");
                    ajax_call();
                    var count = $("#feed_count").html();
                    var count2 = parseInt(count) + 1;
                    $("#feed_count").html(count2);
                    
                    //scrolled=scrolled-300;
                    $("#cont").animate({ scrollTop: 0 }, "slow");
                    /*$("#cont").animate({
                            scrollTop:  scrolled
                       });*/
                   // $("#cont").animate({scrollTop: $('#cont').prop("scrollHeight")}, 1000)

                }
            });
        }
    }
    setInterval(function () {
        ajax_call();
    }, 10000);
    function ajax_call() {
        $.ajax({url: "<?php echo URL::site("persons/ajaxusersfeedback/?id=".$_GET['id']); ?>",
            cache: false,
            dataType: "html",
            success: function (result) {
                if (result == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                $("#cont").html(result);
            }});
        //$("#cont").animate({scrollTop: $('#cont').prop("scrollHeight")}, 1000)
    }
</script>

