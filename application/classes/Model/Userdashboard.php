<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Model_Userdashboard{
    
     public static function insert_cnic($cnic_number, $user_id) {
        $query = DB::update('users_profile')->set(array('cnic_number' => $cnic_number))
                    ->where('user_id', '=', $user_id)
                    ->execute();
            return $query;        
    }
}
