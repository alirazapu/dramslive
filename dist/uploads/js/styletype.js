/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function()
        {
            $('.blue').each(function(i) {
                $(this).css("border-color", "hsla(" + Math.floor(Math.random() * (360)) + ", 75%, 58%, 1)"); 
            });
        });

jQuery('#send_email').on('click', function() {
    
    //jQuery('#send_email').attr('disabled',true);
    var url  = location.protocol + '//' + location.hostname + '/cronjob/email_send';    
    $.ajax({
        url:url,
        beforeSend: function(){
            // Handle the beforeSend event
            jQuery('.loader-div').show();
          },
          complete: function(){
            // Handle the complete event
            jQuery('.loader-div').hide();
          }
          // ......
    });
});
jQuery('#send_current_email').on('click', function() {
    
   // jQuery('#send_current_email').attr('disabled',true);
    var url  = location.protocol + '//' + location.hostname + '/cronjob/email_send_loc';    
    $.ajax({
        url:url,
        beforeSend: function(){
            // Handle the beforeSend event
            jQuery('.loader-div').show();
          },
          complete: function(){
            // Handle the complete event
            jQuery('.loader-div').hide();
          }
          // ......
    });
});
jQuery('#receive_email').on('click', function() {    
    //jQuery('#receive_email').attr('disabled',true);
    var url  = location.protocol + '//' + location.hostname + '/cronjob/email_receive';    
    $.ajax({
        url:url,
        beforeSend: function(){
            // Handle the beforeSend event
            jQuery('.loader-div').show();
          },
          complete: function(){
            // Handle the complete event
            jQuery('.loader-div').hide();
          }
          // ......
    });
});
jQuery('#parse_sub_file').on('click', function() {    
    //jQuery('#parse_sub_file').attr('disabled',true);
    var url  = location.protocol + '//' + location.hostname + '/cronjob/email_parse_sub';    
    $.ajax({
        url:url,
        beforeSend: function(){
            // Handle the beforeSend event
            jQuery('.loader-div').show();
          },
          complete: function(){
            // Handle the complete event
            jQuery('.loader-div').hide();
          }
          // ......
    });
});
jQuery('#parse_loc_file').on('click', function() {    
    //jQuery('#parse_loc_file').attr('disabled',true);
    var url  = location.protocol + '//' + location.hostname + '/cronjob/email_parse_loc';    
    $.ajax({
        url:url,
        beforeSend: function(){
            // Handle the beforeSend event
            jQuery('.loader-div').show();
          },
          complete: function(){
            // Handle the complete event
            jQuery('.loader-div').hide();
          }
          // ......
    });
});
jQuery('#parse_sim_file').on('click', function() {    
    //jQuery('#parse_sim_file').attr('disabled',true);
    var url  = location.protocol + '//' + location.hostname + '/cronjob/email_parse_nic';    
    $.ajax({
        url:url,
        beforeSend: function(){
            // Handle the beforeSend event
            jQuery('.loader-div').show();
          },
          complete: function(){
            // Handle the complete event
            jQuery('.loader-div').hide();
          }
          // ......
    });
});
jQuery('#parse_cdr_file').on('click', function() {    
    //jQuery('#parse_cdr_file').attr('disabled',true);
    var url  = location.protocol + '//' + location.hostname + '/cronjob/email_parse_phone';    
    $.ajax({
        url:url,
        beforeSend: function(){
            // Handle the beforeSend event
            jQuery('.loader-div').show();
          },
          complete: function(){
            // Handle the complete event
            jQuery('.loader-div').hide();
          }
          // ......
    });
});
jQuery('#parse_imei_file').on('click', function() {    
   // jQuery('#parse_imei_file').attr('disabled',true);
    var url  = location.protocol + '//' + location.hostname + '/cronjob/email_parse_imei';    
    $.ajax({
        url:url,
        beforeSend: function(){
            // Handle the beforeSend event
            jQuery('.loader-div').show();
          },
          complete: function(){
            // Handle the complete event
            jQuery('.loader-div').hide();
          }
          // ......
    });
});
jQuery('#family_tree_complete').on('click', function() {    
   // jQuery('#family_tree_complete').attr('disabled',true);
    var url  = location.protocol + '//' + location.hostname + '/cronjob/family_tree_complete';    
    $.ajax({
        url:url,
        beforeSend: function(){
            // Handle the beforeSend event
            jQuery('.loader-div').show();
          },
          complete: function(){
            // Handle the complete event
            jQuery('.loader-div').hide();
          }
          // ......
    });
});
jQuery('#reset_parse_queue').on('click', function() {    
   // jQuery('#reset_parse_queue').attr('disabled',true);
    var url  = location.protocol + '//' + location.hostname + '/cronjob/resend_in_parse_queue';    
    $.ajax({
        url:url,
        beforeSend: function(){
            // Handle the beforeSend event
            jQuery('.loader-div').show();
          },
          complete: function(){
            // Handle the complete event
            jQuery('.loader-div').hide();
          }
          // ......
    });
});

jQuery('#resend_queue').on('click', function() {        
   // jQuery('#resend_error_queue').attr('disabled',true);
    var url  = location.protocol + '//' + location.hostname + '/cronjob/family_tree_complete';    
    $.ajax({
        url:url,
        beforeSend: function(){
            // Handle the beforeSend event
            jQuery('.loader-div').show();
          },
          complete: function(){
            // Handle the complete event
            jQuery('.loader-div').hide();
          }
          // ......
    });
});




