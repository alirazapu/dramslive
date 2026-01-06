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
        <li class="active">Location Call Log</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="">
                <div class="box box-primary">
                    <form role="form" name="search_form" id="search_form" class="ipf-form call_log_form" method="POST" action="" >
                        <input type="hidden" class="form-control" name="xport" id="xport" value=""> 
                        <div class="box box-default collapsed-box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Advance Search</h3>
                                <div class="box-tools pull-right">
                                    <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post)) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                    <button type="button" title="Close Advance Search" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body" style="<?php echo (!empty($search_post)) ? 'display:block;' : ''; ?>">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select Type</label>
                                        <select class="form-control " name="type" id='type' onchange="showDiv(this)">                                                        
                                            <option value="">Please Select Type</option>
                                            <option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'call')) ? 'selected' : ''); ?> value="call"> Call</option>
                                            <option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'sms')) ? 'selected' : ''); ?>  value="sms"> SMS</option>
                                            <option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'callsms')) ? 'selected' : ''); ?> value="callsms"> Call & SMS</option>                                        
                                            <option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'favfive')) ? 'selected' : ''); ?> value="favfive"> Favourite 5 Locations</option>                                        
                                        </select>
                                    </div>          
                                </div>
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                    <label for="searchfield">Other Person Mobile Number(Optional)</label>
                                    <select class="form-control select2" multiple="multiple" data-placeholder="Select Other Person Number" name="otherphone[]" id="otherphone" style="width: 100%;">                                                        
                                        
                                    </select>                                            
                                </div> 
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="startdate">Start Date (mm/dd/yyyy)</label>
                                        <input type="text"  readonly="readonly" placeholder="mm/dd/yyyy" class="form-control" id="sdate" value="<?php echo (!empty($search_post['sdate']) ? $search_post['sdate'] : ""); ?>" name="sdate">                                                                               
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="enddate">End Date (mm/dd/yyyy)</label>                                        
                                        <input  readonly="readonly" type="text" placeholder="mm/dd/yyyy" class="form-control" id="edate" value="<?php echo (!empty($search_post['edate']) ? $search_post['edate'] : ''); ?>" name="edate">                                                                              
                                    </div>
                                </div>

                                <div class="col-md-1" style="width: 10%">
                                    <label>Limit</label>
                                    <select class="form-control select2" name="limit" id="limit">                                                        
                                        <option <?php echo ((!empty($search_post['limit']) && ($search_post['limit'] == '5')) ? 'selected' : ''); ?> value="5"> 5</option>
                                        <option <?php echo ((!empty($search_post['limit']) && ($search_post['limit'] == '10')) ? 'selected' : ''); ?>  value="10"> 10</option>                                       
                                        <option <?php echo ((!empty($search_post['limit']) && ($search_post['limit'] == '20')) ? 'selected' : ''); ?> value="20"> 20 </option>                                        
                                        <option <?php echo ((!empty($search_post['limit']) && ($search_post['limit'] == '50')) ? 'selected' : ''); ?> value="50"> 50 </option>                                        
                                        <option <?php echo ((!empty($search_post['limit']) && ($search_post['limit'] == '500')) ? 'selected' : ''); ?> value="500"> 50+ </option>                                        
                                    </select>
                                </div>
                                <div class="col-md-3" style="width: 23%">                                    
                                    <div class="form-group pull-right" style="margin-top: 24px">                                         
                                        <!--<button type="submit" class="btn btn-primary">Search</button>-->
                                        <input type="button" value="Search" onclick="person_location('2')" class="btn btn-primary" />
                                        <input type="button" value="Clear Search" onclick="clearSearch()" class="btn btn-danger" />
                                    </div>
                                </div>
                                <!-- /.col -->
                                <!-- /.row -->
                            </div>        
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <!-- MAP & BOX PANE -->
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Person Location's</h3>
                    
                    <a id="exportbutton" title="Export to Excel" href="javascript:excel()" class="btn btn-danger btn-small" style="float: right;"><i class="fa fa-file-excel-o"></i>  Export</a>                        
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pad">
                                <!-- Map will be created here -->
                                <!--<div id="world-map-markers" style="height: 325px;"></div>-->
                                <div id="map" style="height: 355px;"></div>
                                <img id="nodata" style="display:none" class="img-responsive" src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                            </div>                                                                                          
                            <!--                                                                                        <div class="text-center box-footer">                                                                                                                                                                                                                         <b><a href="" ><i class="fa fa-arrow-circle-left"></i></a> 2016-09-19 <a href=""><i class="fa fa-arrow-circle-right"></i></a></b>                                                                                                                   </div>-->
                        </div>
                        <!-- /.col -->               
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
            <!-- TABLE: LATEST ORDERS -->          
            <!-- /.box -->
        </div>
        <!-- /.col -->                            
    </div>
</div>

</section>
<!-- /.content -->
<script src="https://maps.google.com/maps/api/js?key=AIzaSyBwL7lXZwan5Cp6GjddHiNNM3VJhZ3oYvE&sensor=false" type="text/javascript"></script>
<script src="<?php echo URL::base() . 'plugins/select2/select2.full.min.js'; ?>"></script>
<script>
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>
<script>
$(document).ready(function () {
    person_location();
});
function person_location() {
        if ($('#search_form').valid())
        {
        var type = $('#type').val();
        var phonenumber = $('#phone_number').val();
        var otherphone = $('#otherphone').val();
        var sdate = $('#sdate').val();
        var edate = $('#edate').val();
        var limit = $('#limit').val();
       // alert(type);
        if (type == 'favfive')
        { 
            //$('#otherphone').val('');
            jQuery("#otherphone").select2().select2('val', ['']);
            $('#sdate').val('');
            $('#edate').val('');
            //$('#limit').val('');
            jQuery("#limit").select2().select2('val', ['']);
            $('#otherphone').attr("readonly","readonly");
            $('#sdate').attr("readonly","readonly");
            $('#edate').attr("readonly","readonly");
            $('#limit').attr("readonly","readonly");            
        }
        else
        {
            $('#otherphone').removeAttr("readonly");
            $('#sdate').removeAttr("readonly");
            $('#edate').removeAttr("readonly");
            $('#limit').removeAttr("readonly");
        }
        var searchresults = {type: type, phonenumber: phonenumber, otherphone: otherphone, sdate: sdate, edate: edate , limit :limit, id: '<?php echo $_GET['id'];?>'}
        $.ajax({
            url: "<?php echo URL::site("Persons/last_five_calls/?id=".$_GET['id']); ?>",
            type: 'POST',
            data: searchresults,
            cache: false,
            //dataType: "text",
            dataType: 'json',
            success: function (msg)
            {
            if(msg == '-1')
                {
                    $('#map').hide();
                    $('#exportbutton').hide();                    
                    $('#nodata').show();
                } 
                else{
                    $(function () {
                        $('#nodata').hide();
                        $('#exportbutton').show();
                        $('#map').show();

                        var locations = msg;
                        var map = new google.maps.Map(document.getElementById('map'), {
                            zoom: 10,
                            center: new google.maps.LatLng(locations[0][1], locations[0][2]),
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        });

                        var infowindow = new google.maps.InfoWindow();

                        var marker, i;

                        for (i = 0; i < locations.length; i++) {
                            marker = new google.maps.Marker({
                                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                                map: map,
                                animation: google.maps.Animation.DROP
                            });

                            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                                return function () {
                                    infowindow.setContent(locations[i][0]);
                                    infowindow.open(map, marker);
                                    if (marker.getAnimation() !== null) {
                                        marker.setAnimation(null);
                                    } else {
                                        marker.setAnimation(google.maps.Animation.BOUNCE);
                                    }
                                }
                            })(marker, i));

                            // marker.addListener('click', toggleBounce);
                        }
                    });
                }
            }

        });
        }
    }
    function person_bparty() {
        var phonenumber = $('#phone_number').val();
        var searchresults = {phone: phonenumber}
        $.ajax({
            url: "<?php echo URL::site("Persons/other_person_phone_number/?id=".$_GET['id']); ?>",
            type: 'POST',
            data: searchresults,
            cache: false,
            //dataType: "text",
            dataType: 'html',
            success: function (data)
            {
                $("#otherphone").html(data);
            }
        });
    }   
$("#search_form").validate({
        rules: {
            type: {
                check_list: true,
            },
            phone_number: {
                check_list: true,
            },
        },
        messages: {
            type: {
                check_list: "Please select Call type",
            },
            phone_number: {
                check_list: "Please select Phone Number",
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
function clearSearch() {
    window.location.href = '<?php echo URL::site('persons/location_call_log/?id='.$_GET['id'], TRUE); ?>';
}
function showDiv(elem) {
        var type = $('#type').val();
        if (type == 'favfive')
        { 
             //$('#otherphone').val('');
            jQuery("#otherphone").select2().select2('val', ['']);
            $('#sdate').val('');
            $('#edate').val('');
            //$('#limit').val('');
            jQuery("#limit").select2().select2('val', ['']);
            $('#otherphone').attr("readonly","readonly");
            $('#sdate').attr("readonly","readonly");
            $('#edate').attr("readonly","readonly");
            $('#limit').attr("readonly","readonly");            
        }
        else
        {
            $('#otherphone').removeAttr("readonly");
            $('#sdate').removeAttr("readonly");
            $('#edate').removeAttr("readonly");
            $('#limit').removeAttr("readonly");
        }
}
function excel(id) {
     
//        if ($('#search_form').valid())
//        {
//            $('#xport').val('excel');
//            person_location();
//            $('#xport').val('');
//        }else{
        $('#xport').val('excel');
        $('#search_form').submit();
        $('#xport').val('');
   // }
    }
    //Date picker
    $('#sdate').datepicker({
      autoclose: true
    });
//Date picker
    $('#edate').datepicker({
      autoclose: true
    });
</script>

