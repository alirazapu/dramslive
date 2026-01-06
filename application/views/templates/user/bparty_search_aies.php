<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//echo '<pre>';
//print_r('testing');
//exit();
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-search"></i>
        Person Search
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Mobile Search</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form class="searchform_input_height" id="search_form" name="search_form" method="post" role="form" action="<?php echo URL::site('user/bparty_search_aies'); ?>" >
                    <div class="box box-default searchperson">
                        <div class="box-header with-border">
                            <h3 class="box-title">Mobile Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">

                            <div class="form-group col-md-6">
                                <label for="phonenumber">Mobile Number </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-mobile"></i>
                                    </div>
                                    <input type="text" class="form-control" id="phonenumber" value="<?php echo ((!empty($search_post['phonenumber'])) ? $search_post['phonenumber'] : ''); ?>" name="phonenumber" placeholder="Mobile Number">
                                </div>
                                <p><b>e.g.</b> Mobile Number, B-Party Number</p>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group pull-left" style="margin-top: 24px">
                                    <button type="submit" onclick="return validateAndSend()" class="btn btn-primary">Search</button>
                                    <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                </div>
                            </div>
                        </div>        
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> Person with given Number</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="searchperson" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th class="no-sort" style="width: 15%">Person's Name</th>
                                        <th class="no-sort" style="width: 13%">Father/Husband Name</th>
                                        <th class="no-sort" style="width: 10%">CNIC</th>                                                                                  
                                        <th class="no-sort" style="width: 10%">Category</th>
                                        <th class="no-sort" style="width: 8%">Detail</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $arrContextOptions = array(
                                            "ssl" => array(
                                                "verify_peer" => false,
                                                "verify_peer_name" => false,
                                            ),
                                        );                
                
                                    if (!empty($person_data)) {
                                        foreach ($person_data as $data){
                                            /* person category */
                                            $cat_id = Helpers_Person::get_person_category_id($data->p_id);
                                            $cat_name = Helpers_Utilities::get_category_name($cat_id);
                                            echo '<tr>';
                                            echo '<td>'. $data->name .'</td>';
                                            echo '<td>'. $data->father_name .'</td>';
                                            echo '<td>'. $cnic = Helpers_Person::get_person_cnic($data->p_id) .'</td>';                                            
                                            echo '<td>'. $cat_name .'</td>';
                                            echo '<td> <a href="' . URL::site('persons/dashboard/?id=' . Helpers_Utilities::encrypted_key($data->p_id,"encrypt")) . '" > View Detail </a> </td>';
                                            echo '</tr>';
                                        }
                                    }else{
                                        echo '<tr>';
                                        echo '<td colspan="6"> Information not found </td>';
                                        echo '</tr>';
                                    }
                                        
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> Persons with given bParty</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="bpartysearch" class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th class="no-sort" style="width: 15%">Person's Name</th>
<!--                                        <th class="no-sort" style="width: 13%">Father Name</th>-->
                                        <th class="no-sort" style="width: 10%">CNIC</th>                                        
<!--                                        <th class="no-sort"  style="width: 10%">Category</th>-->
                                        <th class="no-sort"  style="width: 8%">Detail</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>

                                <th>Person's Name</th>
<!--                                <th>Father Name</th>-->
                                <th>CNIC</th>                                 
<!--                                <th>Category</th>-->
                                <th>Detail</th>
                                </tfoot>
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
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>

<script>
    
    //
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();
        });
        
        
</script>
<script type="text/javascript">
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() !== "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
       //advance search
       var msisdnnumber=$('#phonenumber').val(); 
       var cnicnumber=$("#cnic").val(); 
       var imeinumber=$("#imei").val();
       var requestdata = '' ;
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
        objDT = $('#bpartysearch').dataTable( 
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": false,
                    "bProcessing": true,
                  //  //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('user/ajaxbpartysearch_aies', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": false,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search:",
                        "sEmptyTable": 'Information not found'
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false
                        }],
                    dom: 'Bfrtip',
                    buttons: [
                        'pageLength','excel', 'pdf', 'print'
                    ]
                }
        );

       // $('.dataTables_empty').html("Information not found, <a href='<?php // echo URL::site('userrequest/request/1', TRUE); ?>'> Please Request form Here </a>");


// validation for data search
$("#search_form").validate({
                  rules:{
                         phonenumber:{
                           // number: true,
                            alphanumericspecial: true,
                            maxlength: 15,
                            minlength: 4
                             } ,
                        },
                    messages: {
                         phonenumber:{
                                //Number:"Enter only number",
                                maxlenght:"Maximum 15 digits",                                
                                alphanumericspecial:"Only Alpha Numeric Characters",                           
                                minlength:"Minimum 4 digits"  
                             }                           
                        }, 
                        
                        submitHandler: function () {
                            $("#search_form").submit();
               
                         }                   
                  // $('#upload').show()
                });               

                     
    
    jQuery.validator.addMethod("numberthree", function(value, element) {
        return this.optional(element) || value == value.match(/^[3]\d{9}$/);
        }, "Number should be 10 digits and start from 3.");        
                
    });

    jQuery.validator.addMethod("alphanumericspecial", function(value, element) {
        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9]+$/);
        }, "Only letters and numbers Allowed."); 

    $.validator.addMethod("check_list",function(sel,element){
            if(sel == "" || sel == 0){
                return false;
             }else{
                return true;
             }
            },"<span>Select One</span>");
            
        function validateAndSend() {
            if (search_form.phonenumber.value == '') {
                alert('Enter bParty Number');
                return false;
            }
        }
        
   function clearSearch() {
        window.location.href = '<?php echo URL::site('user/bparty_search', TRUE); ?>';
    }

</script>