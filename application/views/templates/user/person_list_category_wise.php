<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//echo '<pre>';
//print_r($search_post['posting'][0]);
//exit;
?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <i class="fa fa-users"></i>
       Category Wise Person's Report
        <small>Tracer</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li > Category Wise Person's Report</li>
        <li class="active">Person list</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="box box-primary">
                <?php
                $url = 'personsreports/person_category_wise_list/';
//                if (!empty($search_post['posting'])) {
//                   $enc_posting= Helpers_Utilities::encrypted_key($search_post['posting'][0],'encrypt');
//                    $url = 'personsreports/person_category_wise_list/?posting='.$enc_posting;
//                }
                ?>
                <form id="search_form" name="search_form" class="ipf-form" method="POST" action="<?php echo URL::site($url); ?>">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title">Advance Search</h3>

                            <div class="box-tools pull-right">
                                <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post)) ? 'fa-plus' : 'fa-minus'; ?>"></i></button>
                                <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="posting">Select Region </label>
                                    <select class="form-control select2"  id="posting" name="posting[]"  data-placeholder="Select Regiont" style="width: 100%;">
                                        <option value="">Please Select Posting </option>
                                        <optgroup label="Region">
                                            <?php
                                            try {
                                                $region_list = Helpers_Utilities::get_region();
                                                foreach ($region_list as $list) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['posting']) && in_array('r-' . $list->region_id, $search_post['posting'])) ? 'Selected' : ''; ?> value="r-<?php echo $list->region_id ?>"><?php echo $list->name ?></option>
                                                <?php }
                                            } catch (Exception $ex) {

                                            }
                                            ?>
                                    </select>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-md-12">
                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                    <input id="xport" name="xport" type="hidden" value="" />
                                </div>
                            </div>
                            <!-- /.col -->
                            <!-- /.row -->

                        </div>




                </form>

            </div>

            <?php

                $user_obj = Auth::instance()->get_user();
                $permission = Helpers_Utilities::get_user_permission($user_obj->id);
                $cat_white = Helpers_Utilities::encrypted_key(0, 'encrypt');
                $cat_grey = Helpers_Utilities::encrypted_key(1, 'encrypt');
                $cat_black = Helpers_Utilities::encrypted_key(2, 'encrypt');

            $vlaue='';
            $vlaue = !empty($search_post['posting'][0])?$search_post['posting'][0]: null;
            $enc_posting='';
            if(!empty($vlaue)) {
                $enc_posting = Helpers_Utilities::encrypted_key($vlaue, 'encrypt');
            }
            //$enc_posting = !empty($enc_posting) ? $enc_posting: '';


            if ($permission == 1 || $permission == 5 || $permission == 2 || $permission == 3) {
                    $black = Helpers_Utilities::get_users_black_person(null,$vlaue);
                    $grey = Helpers_Utilities::get_users_grey_person(null,$vlaue);
                    $white = Helpers_Utilities::get_users_white_person(null,$vlaue);

                    $total = $black + $grey + $white;
                } else if ($permission == 4) {
                    $black = Helpers_Utilities::get_users_black_person($user_id,$vlaue);
                    $grey = Helpers_Utilities::get_users_grey_person($user_id,$vlaue);
                    $white = Helpers_Utilities::get_users_white_person($user_id,$vlaue);
                    $total = $black + $grey + $white;
                }

//                      echo '<pre>';
//                      print_r($vlaue);
//                      exit;
            ?>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                <th>Category</th>
                <th>No.of Person</th>
                <th>View Details</th>
                </tr>
                </thead>
                <tr>
                   <td> <?php echo 'Black';?></td>
                   <td> <?php echo $black;?></td>
                   <td>  <a href="<?php echo URL::site('personsreports/person_list/?category=' . $cat_black.'&reg=' .$enc_posting); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a></td>
                </tr>
                <tr>
                   <td> <?php echo 'Grey';?></td>
                   <td> <?php echo $grey;?></td>
                   <td>  <a href="<?php echo URL::site('personsreports/person_list/?category=' . $cat_grey.'&reg=' .$enc_posting); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a></td>
                </tr>
                <tr>
                   <td> <?php echo 'White';?></td>
                   <td> <?php echo $white;?></td>
                   <td>  <a href="<?php echo URL::site('personsreports/person_list/?category=' . $cat_white.'&reg=' .$enc_posting); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a></td>
                </tr>
                <tr>
                   <td> <?php echo 'Total';?></td>
                   <td> <?php echo $total;?></td>
                   <td>   <a href="<?php echo URL::site('personsreports/person_list/?reg=' .$enc_posting); ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a></td>
                </tr>
                <body>

                </body>
                <tfoot>
                <tr>
                    <th>Category</th>
                    <th>No.of Person</th>
                    <th>View Details</th>
                </tr>
                </tfoot>
            </table>
        </div>


    </div>
    </div>

</section>
<!-- /.content -->
<script type="text/javascript">
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
    var objDT;

    function refreshGrid() {
        // objDT.fnDraw();
        objDT.fnStandingRedraw();
        if ($("#msg_to_show").val() != "") {
            $("#msg_" + $("#msg_to_show").val()).show();
        }
    }


    function clearSearch() {
        window.location.href = '<?php echo URL::site('personsreports/person_category_wise_list', TRUE); ?>';
    }
    function excel(id) {
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
    }
</script>