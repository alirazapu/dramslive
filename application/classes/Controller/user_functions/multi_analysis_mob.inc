<?php

$this->template->content = View::factory('templates/user/multi_analysis_mobs')
                         ->set('search_post', $post);
//                         ->set('imei_data', $common_imei)
//                         ->set('bparty_data', $common_bparty)
//                         ->set('aparty_data', $common_aparty)
//                         ->set('lat_long_data', $common_lat_long)
//                         ->set('multiple_imsi_data', $multiple_imsi);