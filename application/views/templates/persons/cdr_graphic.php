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
        <li><a href="<?php echo URL::site('Userdashboard/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo URL::site('persons/dashboard/?id='.$_GET['id']); ?>">Person Dashboard</a></li>
        <li class="active">CDR Graphic View</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="">
            <div class="col-md-12">
                <div class="">
                    <div class="box box-primary">   
                        <form role="form" name="search_form" id="search_form" class="ipf-form" method="POST" action="" >
                            <div class="box box-default collapsed-box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">Advance Search</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" title="Show/Hide Advance Search" class="btn btn-box-tool" data-widget="collapse"><i class="fa <?php echo (!empty($search_post['type'])) ? 'fa-minus' : 'fa-plus'; ?>"></i></button>
                                    </div>
                                </div>
                                <!-- /.box-header -->
                                <div class="box-body" style="<?php echo (!empty($search_post['type'])) ? 'display:block;' : ''; ?>">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Select Type</label>
                                            <select class="form-control " name="type" id='type' onchange="showDiv(this)">                                                        
                                                <option value="">Please Select Type</option>
                                                <option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'call')) ? 'selected' : ''); ?> value="call"> Call</option>
                                                <option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'sms')) ? 'selected' : ''); ?>  value="sms"> SMS</option>
                                                <option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'callsms')) ? 'selected' : ''); ?> value="callsms"> Call & SMS</option>                                        
                                                <option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'favfive')) ? 'selected' : ''); ?> value="favfive"> Favourite Five Numbers</option>                                        
                                                <option <?php echo ((!empty($search_post['type']) && ($search_post['type'] == 'linked')) ? 'selected' : ''); ?> value="linked"> Linked Persons</option>                                        
                                            </select>
                                        </div>          
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Mobile Number</label>
                                            <select class="form-control " name="phone_number" id="phone_number" onchange="person_bparty()">                                                        
                                                <option value="">Please Select Person Number</option>
                                                <?php
                                                $sims_list = Helpers_Person::get_person_inuse_SIMs($person_id);
                                                foreach ($sims_list as $sim) {
                                                    ?>
                                                    <option <?php echo (!empty($search_post['phone_number']) && ($search_post['phone_number'] == $sim->phone_number)) ? 'selected' : '' ?> value=<?php echo $sim->phone_number ?> > <?php echo $sim->phone_number ?> </option>
                                                <?php } ?>
                                            </select>
                                        </div>          
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="searchfield">Other Person Mobile Number(Optional)</label>
                                            <select class="form-control select2" multiple="multiple" data-placeholder="Select Other Peson Number" name="otherphone" id="otherphone" style="width: 100%;">                                                        
                                                <option value="">All</option>                                                
                                                <?php /*
                                                  $bparty_list = Helpers_Person::get_person_total_bparty($person_id);
                                                  // print_r($bparty_list); exit;
                                                  foreach ($bparty_list as $sim) {
                                                  ?>
                                                  <option <?php echo (!empty($search_post['otherphone']) && ($search_post['otherphone'] == $sim->ophone)) ? 'selected' : '' ?> value=<?php echo $sim->ophone ?> > <?php echo $sim->ophone ?> </option>
                                                  <?php } */ ?>
                                            </select>                                            
                                        </div>
                                    </div>
                                    <div class="col-md-12" >                                    
                                        <div class="form-group pull-right">                                         
                                            <!--<button type="submit" class="btn btn-primary">Search</button>-->
                                            <input type="button" value="Search" onclick="person_location()" class="btn btn-primary" />
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
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title" style="float: left"><i class="fa fa-mobile-phone"></i> CDR Summary of <?php echo Helpers_Person::get_person_name($person_id) . ' Of Mobile Number ' ?> <span id="mobile"></span> </h3>                                        
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body" style="height: 600px; width: 100%">
                        <div id="cy" ></div>
                        <img id="nodata" style="display:none" class="img-responsive" src="<?php echo URL::base() . 'dist/img/noperson.png'; ?>" alt="No Data">
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
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
<script>
    $(document).ready(function () {
        person_location();
        //validation();
    });
    function person_location() {
        if ($('#search_form').valid())
        {
            var type = $('#type').val();
            var phonenumber = $('#phone_number').val();
            var otherphone = $('#otherphone').val();
            if (type == 'favfive' || type == 'linked')
            { 
                $('#otherphone').attr("disabled","disabled");           
            }
            else
            {
                $('#otherphone').removeAttr("disabled");
            }
            var searchresults = {type: type, phone: phonenumber, ophone: otherphone, id: '<?php echo $_GET['id'];?>'};
            document.getElementById("mobile").innerHTML = phonenumber;
            $.ajax({
                url: "<?php echo URL::site("Persons/recent_five_calls"); ?>",
                type: 'POST',
                data: searchresults,
                cache: false,
                //dataType: "text",
                dataType: 'json',
                success: function (data)
                {
                 if (data == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}   
                    // console.log(data);
                    if (data == '-1')
                    {
                        $('#cy').hide();
                        $('#nodata').show();
                    } else {
                        $(function () {
                            $('#nodata').hide();
                            $('#cy').show();
                            var person_summary = data;
                            var number = person_summary.personnumber;
                            document.getElementById("mobile").innerHTML = number;
                            // console.log(person_summary.edge);
                            cytoscape({
                                container: document.getElementById('cy'),
                                layout: {
                                    name: 'cose',
                                    padding: 10
                                },
                                style: cytoscape.stylesheet()
                                        .selector('node')
                                        .css({
                                            'shape': 'data(faveShape)',
                                            'width': 'mapData(weight, 40, 80, 20, 60)',
                                            'content': 'data(name)',
                                            'font-size': '8px',
                                            'text-valign': 'center',
                                            'text-outline-width': 2,
                                            'text-outline-color': 'data(faveColor)',
                                            'background-color': 'data(faveColor)',
                                            'color': '#fff'
                                        })
                                        .selector(':selected')
                                        .css({
                                            'border-width': 3,
                                            'border-color': '#333'
                                        })
                                        .selector('edge')
                                        .css({
                                            'curve-style': 'bezier',
                                            'opacity': 0.666,
                                            'width': 'mapData(strength, 70, 100, 2, 6)',
                                            'target-arrow-shape': 'triangle',
                                            'source-arrow-shape': 'circle',
                                            'line-color': 'data(faveColor)',
                                            'source-arrow-color': 'data(faveColor)',
                                            'target-arrow-color': 'data(faveColor)',
                                            'label': 'data(name)'
                                        })
                                        .selector('edge.questionable')
                                        .css({
                                            'line-style': 'dashed',
                                            'target-arrow-shape': 'diamond'
                                        })
                                        .selector('.faded')
                                        .css({
                                            'opacity': 0.25,
                                            'text-opacity': 0
                                        }),

                                elements: {

                                    nodes: person_summary.node,
//                                [ 
//                                    
//                                   // {data: {id: person_number, name: person_number, weight: 65, faveColor: '#6FB1FC', faveShape: 'octagon'}}, //triangle
//                                    //{data: {id: other_person, name: other_person, weight: 45, faveColor: '#EDA1ED', faveShape: 'ellipse'}}, //ellipse
//                                    {data: {id: 'k', name: '0303498514', weight: 100, faveColor: '#86B342', faveShape: 'rectangle'}}, //octagon
//                                    {data: {id: 'g', name: '0300459826', weight: 70, faveColor: '#F5A45D', faveShape: 'triangle'}} //rectangle                                    
//                                ],
                                    edges: person_summary.edge,
//                                    [
//                                    //{data: {source: person_number, target: other_person, label: 'Calls:'.calls_received, faveColor: '#EDA1ED', strength: 60, name: calls_received}, classes: 'questionable'},
//
//                                   // {data: {source: person_number, target: other_person, faveColor: '#86B342', strength: 100, name: calls_received}},
//                                    {data: {source: 'k', target: 'g', faveColor: '#86B342', strength: 100, name: "Call:2"}},
//                                    {data: {source: 'k', target: 'g', faveColor: '#86B342', strength: 100, name: "Call:21"}},
//
//                                   // {data: {source: 'g', target: 'j', faveColor: '#F5A45D', strength: 100, name: "Call:24"}}
//                                    ]
                                },

                                ready: function () {
                                    window.cy = this;
                                }
                            });
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
                if (data == 2) 
			{
			swal("System Error", "Contact Support Team.", "error");
			}
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
    
</script>                
<script type="text/javascript">
    function clearSearch() {
        window.location.href = '<?php echo URL::site('persons/cdr_graphic/?id='.$_GET['id'], TRUE); ?>';
    }
    function showDiv(elem) {
        var type = $('#type').val();
        if (type == 'favfive' || type == 'linked')
        { 
            $('#otherphone').attr("disabled","disabled");          
        }
        else
        {
            $('#otherphone').removeAttr("disabled");

        }
}
</script>