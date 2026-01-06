<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * Profile helper class. It will contain all the Helper Functions realted to Profile
 *
 * @package    Profile Helper
 * @category   Helpers
 */
abstract class Helpers_Inneruse {
        //    get all tags data
    public static function get_nadirakey() {                       
        $key = Model_Inneruse::get_inner_tokens(1);
        $key = str_replace("axHmBf8ri9x","",$key);
        $key = unserialize(base64_decode($key));
        return $key;        
    }

    //get aiesapi key for cis
    public static function get_aieskey() {
        $key = Model_Inneruse::get_inner_tokens(2);
        $key = str_replace("axHmBf8ri9x","",$key);
        $key= Helpers_Utilities::encrypted_key($key, "decrypt");
        return $key;
    }
    //get aiesapi key for cctw
    public static function get_aieskey_cctw() {
        $key ='SZEhiAeCdhIJgQdcbqJc2td5tWZn4Xqu';
//        $key = str_replace("axHmBf8ri9x","",$key);

//        $key= Helpers_Utilities::encrypted_key($key, "decrypt");
//        echo '<pre>';
//        print_r($key);
//        exit;
        return $key;
    }
    //get aiesapi key for cis
    public static function get_command_run_key() {
        $key = Model_Inneruse::get_inner_tokens(3);
        $key = str_replace("axHmBf8ri9x","",$key);
        $key = unserialize(base64_decode($key));
        return $key;
    }
    //get subscriber key for aies
    public static function get_subkey() {
        $key = Model_Inneruse::get_inner_tokens(4);
        $key = str_replace("axHmBf8ri9x","",$key);
        //$key = unserialize(base64_decode($key));
        $key= Helpers_Utilities::encrypted_key($key, "decrypt");
        return $key;
    }
      //get usercreatekey key for cis
    public static function get_usercreatekey() {
        $key = Model_Inneruse::get_inner_tokens(8);
        $key = str_replace("axHmBf8ri9x","",$key);
        $key= Helpers_Utilities::encrypted_key($key, "decrypt");
        return $key;
    }
      //get usercreatekey key for cis
    public static function get_updatecisaiespermissionkey() {
        //-tVr9xlzG_AOJ50[2n9(Lc]Rr:oIMqaF9Tb-q@lwG_:cxO
        $key = Model_Inneruse::get_inner_tokens(9);
        $key = str_replace("axHmBf8ri9x","",$key);
        $key= Helpers_Utilities::encrypted_key($key, "decrypt");
        return $key;
    }
      //get usercreatekey key for cis
    public static function get_tabledatakey() {
    //yG4lH[LhD]ymlix7vBG4(QRf-q@lwG_:cxvAVeCrwc^8aCz*Q7k7Pn@cpcG
        $key = Model_Inneruse::get_inner_tokens(10);
        $key = str_replace("KJfhs8idncl","",$key);
        $key= Helpers_Utilities::encrypted_key($key, "decrypt");
        return $key;
    }
    //get gmail account name and password
    public static function get_gmail_pw() {
        //email send password
        $send_key = Model_Inneruse::get_inner_tokens(5);
        $send_key = str_replace("axHmBf8ri9x","",$send_key);
        //$send_key = unserialize(base64_decode($send_key));        
        //cnJRMG42SzFXWmJKTVNpbDY3baxHmBf8ri9xHNVeWw2UWNMQzRQN2hodGlOWFVTTGJibz0=
                
        $send_key= Helpers_Utilities::encrypted_key($send_key, "decrypt");
        //echo (string)$send_key; exit;
        //cmRCZmlGbXd0aCttNnFxaGJzaxHmBf8ri9xazIxdmlHSGdLR2lVUFk4K3ZjdEp1WU1HRT0=
        //'ltnu oujt kaxp njqr';//
        $send_email = Model_Inneruse::get_inner_value_2(5);
        $email['aies']['send']['name']="CTD Punjab";
        $email['aies']['send']['user'] = (string)$send_email;        
        $email['aies']['send']['password'] = (string)$send_key;
       // exit; //'wkkmsfdaraplpzkn';
        //email receive password
        $receive_key = Model_Inneruse::get_inner_tokens(6);
        $receive_key = str_replace("axHmBf8ri9x","",$receive_key);        
        $receive_key= Helpers_Utilities::encrypted_key($receive_key, "decrypt");
        
        //$receive_key = unserialize(base64_decode($receive_key));
        //$email['aies']['receive']['user'] = "aiesmailbackup@gmail.com";
        $send_email = Model_Inneruse::get_inner_value_2(6);
        $email['aies']['receive']['user'] = (string)$send_email;
        $email['aies']['receive']['password'] = (string)$receive_key; 
        //email irfan password
        $irfan_key = Model_Inneruse::get_inner_tokens(7);
        $irfan_key = str_replace("axHmBf8ri9x","",$irfan_key);
        $irfan_key = unserialize(base64_decode($irfan_key));
        
        $email['irfan']['send']['user'] = "mirfan15ms@gmail.com";
        $email['irfan']['send']['password'] = $irfan_key ;         
        return $email;
    }   

}

?>
