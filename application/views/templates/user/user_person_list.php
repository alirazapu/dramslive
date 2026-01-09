<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//echo '<pre>';
//print_r($_GET);
//exit();
//?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i  class="fa fa-user"></i>
        User's Report
        <small>DRAMS</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li> </i>User's Report</a></li>
        <li ></i>User's Person</li>
        <li class="active"></i>User's Person List</li>
    </ol>
</section>


<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="">
            <div class="box box-primary">

                <form class="searchform_input_height" id="search_form" name="search_form" method="post" role="form" action="<?php echo URL::site('user/multi_analysis_against_mob_numbers'); ?>" >
                    <div class="box box-default searchperson">
                        <div class="box-header with-border">
                            <h3 class="box-title">Moblie Multi Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">

                            <div class="form-group col-md-6">
                                <label for="phonenumber">Mobile Number(s) </label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-mobile"></i>
                                    </div>
                                    <input type="text" class="form-control" id="phonenumber" value="<?php echo ((!empty($search_post['phonenumber'])) ? $search_post['phonenumber'] : ''); ?>" name="phonenumber" placeholder="e.g. 3001234567,3217654321">
                                </div>
                                <p><b>Note:</b> Enter Multiple (Max 100) Mobile Numbers Comma (,) Separated</p>
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
                        <?php
                        $user_id_encoded = Session::instance()->get('userid');
                        //$user_id_decoded = (int) Helpers_Utilities::encrypted_key($user_id_encoded, "decrypt");
                        ?>
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo Helpers_Utilities::get_user_name($user_id_encoded) ?>'s Persons</h3>
                        <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('userreports/my_persons_analysis/'.Helpers_Utilities::encrypted_key($user_id,"encrypt")); ?>" >
<!--                        <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        -->
<!--                        <input id="xport" name="xport" type="hidden" value="" />-->
                        </form>
                    </div>
                    <!-- /.box-header -->
                    <div class=" box-body">
                        <p><b>Note:</b> Mark Numbers (Atleast 2) for Multi Search from your Persons</p>

                        <div class="table-responsive">

                            <table id="userpersonlist" class="table  table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="no-sort">Person Name</th>
                                        <th class="no-sort">Father/Husband Name</th>
                                        <th class="no-sort">Category</th>
                                        <th class="no-sort">Phone Number(s)</th>
                                        <th class="no-sort">Address</th>
                                        <th class="no-sort">Mark Phone Number</th>
                                        <th class="no-sort">View Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="no-sort">Person Name</th>
                                        <th class="no-sort">Father/Husband Name</th>
                                        <th class="no-sort">Category</th>
                                        <th class="no-sort">Phone Number(s)</th>
                                        <th class="no-sort">Address</th>
                                        <th class="no-sort">Mark Phone Number</th>
                                        <th class="no-sort">View Details</th>
                                    </tr>
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
<script type="text/javascript">
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
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

        // validation for data search
        $("#search_form").validate({
            rules:{
                phonenumber:{
                    // number: true,
                    alphanumericspecial: true,
                    maxlength: 1000,
                    minlength: 21
                }
            },
            messages: {
                phonenumber:{
                    //Number:"Enter only number",
                    maxlenght:"Maximum 1099 digits",
                    alphanumericspecial:"Only Alpha Numeric Characters",
                    minlength:"Minimum Two Mobile Numbers"
                }
            },

            submitHandler: function () {
                $("#search_form").submit();

            }
            // $('#upload').show()
        });


        objDT = $('#userpersonlist').dataTable(
                {//"aaSorting": [[2, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": false,
                    "sAjaxSource": "<?php echo URL::site('userreports/ajaxuserspersonlist', TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By Person Name or Mobile Number:"
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );
        $('.dataTables_empty').html("Information not found");
        $.fn.dataTable.ext.errMode = 'none';
        $('#userpersonlist').on('error.dt', function(e, settings, techNote, message) {
           swal("System Error", "Contact Technical Support Team.", "error");
        })

    });

    jQuery.validator.addMethod("alphanumericspecial", function(value, element) {
        return this.optional(element) || value == value.match(/^[-a-zA-Z0-9-,]+$/);
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
            alert('Enter Minimum Two Mobile Numbers');
            return false;
        }
    }

    function bindPhoneNo(phone) {
        console.log($(this));

        if($('#phonenumber').val() != '')
            $('#phonenumber').val( $('#phonenumber').val() + ',' + phone);
        else
            $('#phonenumber').val(phone);
    }

    function clearSearch() {
        window.location.href = '<?php echo URL::site('userreports/my_persons_analysis/'.Helpers_Utilities::encrypted_key($user_id,"encrypt"), TRUE); ?>';
    }



    // function excel(id){
    //         $('#xport').val('excel');
    //         $('#search_form').submit();
    //         $('#xport').val('');
    // }

</script>

