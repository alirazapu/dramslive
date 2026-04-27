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
        <li class="active">B-Party Subscriber (Slow)</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">

                <form role="form" id="call_summary_form" name="search_form" method="post"  action="<?php echo URL::site('persons/bparty_subscriber/?id='.$_GET['id']); ?>" >
                   <input type="hidden" class="form-control" name="xport" id="xport" value="">
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>
                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post['phone_number'])) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="<?php echo (!empty($search_post['phone_number'])) ? 'display:block;' : ''; ?>">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <select class="form-control " name="phone_number" id="phone_number" onchange="person_bparty()">
                                        <option value="">Please Select Person Number</option>
                                        <?php try{
                                        $sims_list = Helpers_Person::get_person_inuse_SIMs($person_id);
                                        foreach ($sims_list as $sim) {
                                            ?>
                                            <option <?php echo (!empty($search_post['phone_number']) && ($search_post['phone_number'] == $sim->phone_number)) ? 'selected' : '' ?> value=<?php echo $sim->phone_number ?> > <?php echo $sim->phone_number ?> </option>
                                        <?php }
                                        }  catch (Exception $ex){

                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="searchfield">Other Person Mobile Number(Optional)</label>
                                    <select class="form-control select2" multiple="multiple" data-placeholder="Select Other Person Number" name="otherphone[]" id="otherphone" style="width: 100%;">

                                    </select>
                                </div>
                            </div>
                            <div class="form-group pull-right">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <!--<input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />-->
                                <button type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger">Clear Search</button>
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
                        <h3 class="box-title"><i class="fa fa-mobile-phone"></i> B-Party Subscriber of <?php echo Helpers_Person::get_person_name($person_id); ?></h3>
                    <a title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="bparty_subscirber_table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>                                                    
                                        <th class="no-sort">Phone Number</th>
                                        <th class="no-sort">Party B</th>
                                        <th>SMS COUNT</th>
                                        <th>Call COUNT</th>                                                    
                                    </tr>
                                </thead> 
                                <tfoot>
                                    <tr>                                                    
                                        <th>Phone Number</th>
                                        <th>Party B</th>
                                        <th>SMS</th>
                                        <th>Call</th>                                                    
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

<div class="modal modal-info fade" id="external_search_model">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button onclick="clearSearch1()" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Search Results</h4>
            </div>

            <div class="modal-body" style='background-color: #00a7d0 !important; color: black !important; '>

                <!--searching data form external sources-->
                <div id="externa_search_results_div" style="display: block;">
                    <div class="col-md-12" style="background-color: #fff;color: black">

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label   for="external_search_key" class="control-label">Search Key:
                                </label>
                                <input name="external_search_key" type="text" class="form-control" id="external_search_key" placeholder="Search Key" readonly >
                            </div>

                            <hr class="style14 " style="margin-top: -10px; ">
                            <div class="col-sm-12" id="external_search_results" style="display: block">

                                <div style='background-image: url("<?php echo URL::base(); ?>dist/img/loader.gif"); width:100%; height:11px; background-position:center; display:block'><label    class="control-label" style="alignment-adjust: central">Exploring External Sources
                                    </label></div>
                                <hr class="style14 ">
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button onclick="clearSearch1()" type="button" style="margin-top: 10px" class="btn btn-primary pull-right" data-dismiss="modal">Close</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->

</div>

<!-- /.content -->
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>

<script>
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
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }

    $(document).ready(function () {
        person_bparty();
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
        objDT = $('#bparty_subscirber_table').dataTable(
                {"aaSorting": [[3, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "lengthMenu": [[10, 25,50, 100], [10, 25, 50, 100]],
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('persons/ajaxbpartysubscriber/?id='.$_GET['id'], TRUE); ?>",
                    "sPaginationType": "full_numbers",
                    "bFilter": true,
                    "bLengthChange": true,
                    "oLanguage": {
                        "sProcessing": "Loading...",
                        "sSearch": "Search By B Party Number:"
                        
                    },
                    "columnDefs": [{
                            "targets": 'no-sort',
                            "orderable": false,
                        }]
                }
        );

        $('.dataTables_empty').html("Information not found");

    });


    // request subscriber
    function external_search_model(mobile) {

        $("#external_search_model").modal("show");
        //appending modal background inside the blue div
        $('.modal-backdrop').appendTo('.blue');

        //remove the padding right and modal-open class from the body tag which bootstrap adds when a modal is shown
        $('body').removeClass("modal-open");
        $('body').css("padding-right", "");
        setTimeout(function () {
            // Do something after 1 second
            $(".modal-backdrop.fade.in").remove();
        }, 300);

        if(mobile !=0 || mobile != ''){
            $("#external_search_key").val(mobile);
            search_local_subscriber_detail('msisdn',mobile);
        }

    }

    //function to search subscriber in local sources
    function search_local_subscriber_detail(search_type, search_value ) {
        var result = {search_type: search_type,search_value:search_value}
        $.ajax({
            url: "<?php echo URL::site('userreports/msisdn_data_search', TRUE); ?>",
            type: 'POST',
            data: result,
            cache: false,
            success: function (msg) {
                $("#external_search_results").html(msg);
            }
        });

    }

    // Previously-submitted B-party numbers, sent only on the first AJAX call so that
    // after Search/Export the dropdown re-mounts with the user's prior selection
    // pre-checked. Subsequent calls (from #phone_number onchange) skip this so the
    // user gets a fresh list when they switch A-party numbers.
    var initialOtherPhones = "<?php
        $sel = (!empty($search_post['otherphone']) && is_array($search_post['otherphone']))
            ? $search_post['otherphone']
            : array();
        echo htmlspecialchars(implode(',', $sel), ENT_QUOTES, 'UTF-8');
    ?>";

    function person_bparty() {

        var phonenumber = $('#phone_number').val();
        var searchresults = {phone: phonenumber};
        if (initialOtherPhones) {
            searchresults.otherphonenumbers = initialOtherPhones;
            initialOtherPhones = ''; // consume on first call only
        }
        $.ajax({
            url: "<?php echo URL::site("Persons/other_person_phone_number/?id=".$_GET['id']); ?>",
            type: 'POST',
            data: searchresults,
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (data)
            {
                if (data == 2)
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                $("#otherphone").html(data);
                // Refresh select2 so the `selected` options coming back from the
                // server actually render as chips instead of staying invisible.
                $("#otherphone").trigger('change');
            }
        });
    }
    $("#call_summary_form").validate({
        rules: {
            phone_number: {
                check_list: true,
            },
            key: {
                key_value: true,
                required: true,
                number: true,
            },
        },
        messages: {
            phone_number: {
                check_list: "Please select search type",
            },
            key: {
                key_value: "Enter Valid Mobile Number",
                Number: "Enter only number",
            },
        }
    });
    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == "") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    $.validator.addMethod("key_value", function (sel, element) {
        if (($('#type').val() == "phonenumber" || $('#type').val() == "partyb") && $('#searchfield').val() !== "") {
            return true;
        } else {
            return false;
        }
    }, "<span>Select One</span>");
    function clearSearch() {
        window.location.href = '<?php echo URL::site('persons/cdr_summary/?id='.$_GET['id'], TRUE); ?>';
    }
function clearSearch1() {
       // window.location.href = '<?php //echo URL::site('user/search_person', TRUE); ?>';
       $("#search_form").trigger("reset");
       $('#search_form')[0].reset();
       $("#search_form").get(0).reset();
       $(':input').val('');
       
    }
    jQuery.fn.reset = function () {
    $(this).each (function() { this.reset(); });
  }
function excel(id) {
        $('#xport').val('excel');
        $('#call_summary_form').submit();
        $('#xport').val('');
    }
</script>