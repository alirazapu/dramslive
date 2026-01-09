<!DOCTYPE html>
<html>
    <head>
        <!-- meta tags-->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">        
        <?php 
           /*  Get current url, action and param            * 
            */ 
           $current_url=request::current()->controller();                    
           $current_action=Request::current()->action();
           $current_controller=Request::current()->controller();
           $current_parm =  Request::current()->param("id");
           $pid = isset($_GET['id']) ? 1 : 0;
           $current_parm_2 =  Request::current()->param("id2");           
         
           /* Get All Css Files             * 
            */
            include_once 'templates/layout/style-files.php';
            if(isset($styles)) foreach ($styles as $style) echo HTML::style($style) . "\n";     
            /* Get Script Files             * 
             */
            include_once 'templates/layout/script-files.php';
            if(isset($scripts)) foreach ($scripts as $script) echo HTML::script($script). "\n"; 
        ?>
        <!-- Set Titles -->
        <title>DRAMS | <?php echo $current_action; ?> </title>
        
            <!--  Favicon Icon-->
            <!--<link rel="icon" href="<?php echo URL::base(); ?>dist/img/icon/icon_5.png" type="image/x-icon">--> 
            <link rel="icon" href="<?php echo URL::base(); ?>dist/img/icon/logo.png" type="image/png"> 
            <link rel="shortcut icon" href="<?php echo URL::base(); ?>dist/img/icon/logo.png" type="image/png"> 
            
    </head>
    <body class="hold-transition skin-blue sidebar-mini"> 
       
<!--        <button type="button" class="btn btn-default btn-lrg ajax" title="Ajax Request">
            <i class="fa fa-spin fa-refresh"></i>&nbsp; Get External Content
          </button>-->
<!--         Pre-Loader-->
         <div id="preloader" style="display: block;">
            <div class="loader">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div> 
        <div class="wrapper">
            <!-- Header Body-->
            <?php echo Helpers_Layout::get_header(); ?>
            <!-- Side Bar -->
            <?php  if((trim($current_controller)=='Personsreports' && trim($current_action) != 'project_persons' && trim($current_action) != 'person_list' && trim($current_action) != 'sensitive_person_list' && trim($current_action) != 'top_search_persons'  && trim($current_action) != 'person_breakup_report'  && trim($current_action) != 'person_breakup_district' && trim($current_action) != 'person_list_district' && trim($current_action) != 'person_call_analysis' && trim($current_action) != 'person_category_wise_list')
                        || trim($current_controller)=='Socialanalysis' || trim($current_controller)=='social_links' 
                        || trim($current_controller)=='add_social_link'|| trim($current_controller)=='Persons' 
                        || trim($current_controller)=='Personprofile'  
                        || (!empty($current_parm) && $current_parm!=1 && trim($current_action) == 'request') 
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'requestcdr' && $pid == 1)
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'requestcdrimei' && $pid == 1)
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'requestlocation' && $pid == 1)
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'requestcdrptcl' && $pid == 1)
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'requestsubptcl' && $pid == 1)
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'requestverisys' && $pid == 1)
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'requestfamilytree' && $pid == 1)
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'familytree_detail' && $pid == 1)
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'requestcdrsms' && $pid == 1)
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'requestsubscriber' && $pid == 1)
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'requestcnicsims' && $pid == 1)
                        || (trim($current_controller) == 'Userrequest' && trim($current_action) == 'requestcdrinternational' && $pid == 1))
                    echo Helpers_Layout::get_sidebar_person();
                else //if($current_controller=='User')
                    echo Helpers_Layout::get_sidebar_user();
            ?>
            <!-- main body-->
            <div class="content-wrapper">
                <?php echo (isset($content))? $content : ""; ?>
            </div>  
            <!--Footer -->
                <?php echo Helpers_Layout::get_sitefooter(); ?>
        </div>  
        
        <!-- Pre Loader -->
        <script>
            jQuery(window).load(function () {
              setTimeout(function () {
                  jQuery("#preloader").hide();
              }, 2);

            });
        
	// To make Pace works on Ajax calls
//	$(document).ajaxStart(function() { Pace.restart(); });
//    $('.ajax').click(function(){
//        $.ajax({url: '#', success: function(result){
//            $('.ajax-content').html('<hr>Ajax Request Completed !');
//        }});
//    });
    $(".alert-success").fadeTo(6000, 800).slideUp(500, function(){
    $(".alert-success").slideUp(500);
        });
    $(".alert-warning").fadeTo(6000, 800).slideUp(500, function(){
    $(".alert-warning").slideUp(500);
        });
        
        window.onload = date_time('date_time');
        $(".select2").on("change", function(){            
            if ($(this).find(":selected").val() == ''){                
             //$('#requesttype option').prop('selected', true);              
             $('.select2 option[value=""]').prop("selected", false);
            }
        });
        
</script>
<script type="text/javascript">
//history.pushState(null, null, '<?php //echo $_SERVER["REQUEST_URI"]; ?>');
//window.addEventListener('popstate', function(event) {
//    window.location.assign("<?php //echo URL::base(); ?>");
//});
 //history.pushState(null, null, document.title);
// history.pushState(null, null, null);
//   window.addEventListener('popstate', function () {
//     //history.pushState(null, null, document.title);
//     history.pushState(null, null, '<?php echo URL::base(); ?>');
//   });

$('input').change(function() {
  $(this).removeClass('error-input');
  $(this).closest('.form-group').find('label.error').remove();
});

$('select').change(function() {
  $(this).removeClass('error-input');
  $(this).closest('.form-group').find('label.error').remove();
});

$('.form-group').on('select2:select', function (e) {    
   $(this).removeClass('error-input');
  $(this).closest('.form-group').find('span.error').remove();
});

//$('form').submit(function() {
//    if($("form").valid()) {
//        $(this).find("button[type='submit']").text('Please wait ...').attr('disabled',true);
//        $(this).find("input[type='submit']").val('Please wait ...').attr('disabled',true);
//    }
//});


<?php if(!empty($login_user->id) && ($login_user->id >= 137 or $login_user->id <= 2031 or $login_user->id <= 842)){
    echo '';
}else{
    ?>
document.addEventListener('contextmenu', function(e) {
  e.preventDefault();
});        
$(document).keydown(function (event) {
    if (event.keyCode == 123) { // Prevent F12
        //return false;
    } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) { // Prevent Ctrl+Shift+I
       // return false;
    }else if (event.ctrlKey && (/* event.keyCode === 67 || event.keyCode === 86 || */ event.keyCode === 85 ||
             event.keyCode === 117 || event.keyCode == 80)) {
            //alert('not allowed');
          //  return false;
        }
});
<?php } ?>
</script>
    </body>
</html>
