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
        <li><a href="<?php echo URL::site('persons/dashboard/?id=' . $_GET['id']); ?>">Person Dashboard </a></li>
        <li class="active">Link With Affiliated Persons</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="box box-primary">
                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site('persons/person_affiliation/?id=' . $_GET['id']); ?>" >
                    <div class="box box-default collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>
                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <select class="form-control " name="phone_number" id="phone_number" onchange="">                                                        
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
                            <div class="form-group col-md-4 buttons" style="margin-top: 25px">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
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
                        <h3 class="box-title"><i class="fa fa-link"></i>  <?php try{ echo Helpers_Person::get_person_name($person_id); 
                        }  catch (Exception $ex){
                                            
                                        }
                        ?>'s Links With Affiliated Persons</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table id="personfavperson" class="table table-bordered table-striped">
                            <thead>
                                <tr>                                    
                                    <th class="no-sort" style="width: 10%">Party A</th>
                                    <th class="no-sort" style="width: 10%">Party B</th>
                                    <th class="no-sort" style="width: 20%">Person's Name</th>
                                    <th class="no-sort" style="width: 13%">Person's CNIC</th>
                                    <th class="no-sort" >Total Call</th>
                                    <th class="no-sort" >Total SMS</th>
                                    <th class="no-sort">Project Affiliated</th>
                                    <th class="no-sort">Organization Affiliated</th>                                    
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>                                    
                                    <th>Party A</th>
                                    <th>Party B</th>
                                    <th>Person's Name</th>
                                    <th>Person's CNIC</th>
                                    <th>Total Call</th>
                                    <th>Total SMS</th>
                                    <th>Project Affiliated</th>
                                    <th>Organization Affiliated</th>                                    
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
</section>
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
        elem = $('#type').val();
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
        objDT = $('#personfavperson').dataTable(
                {"aaSorting": [[1, "desc"]],
                    "bPaginate": true,
                    "bProcessing": true,
                    //"bStateSave": true,
                    "bServerSide": true,
                    "sAjaxSource": "<?php echo URL::site('persons/ajaxpersonaffiliation/?id=' . $_GET['id'], TRUE); ?>",
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
    //function to change org name
    function selectorgname(pro) {

        if (pro.value == 1) {
            $("#org_div").show();
        } else {
            $("#afforg").val("").trigger('change');
            $("#org_div").hide();
        }
    }
    //function to change org name
    function selectproname(org) {
        var org_id = org.value;
        if (org_id == 1) {

        } else if (org_id != 1 && org_id != '') {

            var result = {org_id: org_id}

            $.ajax({
                url: "<?php echo URL::site("personprofile/get_project_id"); ?>",
                type: 'POST',
                data: result,
                dataType: 'json',
                cache: false,
                success: function (msg) {
                    if (msg == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
                    if (msg == 1) {
                        $("#affproject").val(msg).trigger('change');
                    } else {
                        $("#affproject").val(msg).trigger('change');
                        $("#org_div").hide();
                    }
                }
            });

        }
    }
    //function to reset org
    function resetorg() {
        $("#afforg").val("").trigger('change');
        showDiv1();
    }
    $("#search_form").validate({
        rules: {
            affproject: {
                check_list: true,
            },
        },
        messages: {
            affproject: {
                check_list: "Please select Any Project",
            },
        }
    });
    $.validator.addMethod("check_list", function (sel, element) {
        if (sel == " ") {
            return false;
        } else {
            return true;
        }
    }, "<span>Select One</span>");

    function clearSearch() {
        window.location.href = '<?php echo URL::site('persons/person_affiliation/?id=' . $_GET['id'], TRUE); ?>';
    }
</script>
