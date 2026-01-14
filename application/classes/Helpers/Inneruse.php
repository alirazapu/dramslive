<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Profile helper class. It will contain all the Helper Functions realted to Profile
 *
 * @package    Profile Helper
 * @category   Helpers
 */
abstract class Helpers_Inneruse
{
    //    get all tags data
    public static function get_nadirakey()
    {
        $key = Model_Inneruse::get_inner_tokens(1);
        $key = str_replace("axHmBf8ri9x", "", $key);
        $key = unserialize(base64_decode($key));
        return $key;
    }

    //get aiesapi key for cis
    public static function get_aieskey()
    {
        $key = Model_Inneruse::get_inner_tokens(2);
        $key = str_replace("axHmBf8ri9x", "", $key);
        $key = Helpers_Utilities::encrypted_key($key, "decrypt");
        return $key;
    }

    //get aiesapi key for cctw
    public static function get_aieskey_cctw()
    {
        $key = 'SZEhiAeCdhIJgQdcbqJc2td5tWZn4Xqu';
//        $key = str_replace("axHmBf8ri9x","",$key);

//        $key= Helpers_Utilities::encrypted_key($key, "decrypt");
//        echo '<pre>';
//        print_r($key);
//        exit;
        return $key;
    }

    //get aiesapi key for cis
    public static function get_command_run_key()
    {
        $key = Model_Inneruse::get_inner_tokens(3);
        $key = str_replace("axHmBf8ri9x", "", $key);
        $key = unserialize(base64_decode($key));
        return $key;
    }

    //get subscriber key for aies
    public static function get_subkey()
    {
        $key = Model_Inneruse::get_inner_tokens(4);
        $key = str_replace("axHmBf8ri9x", "", $key);
        //$key = unserialize(base64_decode($key));
        $key = Helpers_Utilities::encrypted_key($key, "decrypt");
        return $key;
    }

    //get usercreatekey key for cis
    public static function get_usercreatekey()
    {
        $key = Model_Inneruse::get_inner_tokens(8);
        $key = str_replace("axHmBf8ri9x", "", $key);
        $key = Helpers_Utilities::encrypted_key($key, "decrypt");
        return $key;
    }

    //get usercreatekey key for cis
    public static function get_updatecisaiespermissionkey()
    {
        //-tVr9xlzG_AOJ50[2n9(Lc]Rr:oIMqaF9Tb-q@lwG_:cxO
        $key = Model_Inneruse::get_inner_tokens(9);
        $key = str_replace("axHmBf8ri9x", "", $key);
        $key = Helpers_Utilities::encrypted_key($key, "decrypt");
        return $key;
    }

    //get usercreatekey key for cis
    public static function get_tabledatakey()
    {
        //yG4lH[LhD]ymlix7vBG4(QRf-q@lwG_:cxvAVeCrwc^8aCz*Q7k7Pn@cpcG
        $key = Model_Inneruse::get_inner_tokens(10);
        $key = str_replace("KJfhs8idncl", "", $key);
        $key = Helpers_Utilities::encrypted_key($key, "decrypt");
        return $key;
    }

    /**
     * Returns email credentials in consistent format:
     *   ['send']   => array('name', 'user', 'password')
     *   ['receive'] => array('user', 'password')
     *
     * @return array
     */
    public static function get_gmail_pw()
    {
        $config = Kohana::$config->load('email');
        $sender_name = $config['sender_name'] ?? 'CTD KPK';

        $credentials = [
            'send' => [
                'name' => $sender_name,
                'user' => null,
                'password' => null,
            ],
            'receive' => [
                'user' => null,
                'password' => null,
            ],
        ];

        // Determine environment name
        $env_name = (Kohana::$environment === Kohana::DEVELOPMENT) ? 'development' : 'production';

        // Load environments and pick current one
        $environments = $config['environments'] ?? [];
        $env_config = $environments[$env_name] ?? [];

        // Log if missing (optional but useful)
        if (empty($env_config)) {
            Kohana::$log->add(Log::WARNING, "No email configuration found for environment: $env_name");
        }

        if ( Kohana::$environment === Kohana::PRODUCTION) {
            // ── PRODUCTION / other environments: load from database ──────────────
            // Send credentials (token ID 5)
            $send_token = Model_Inneruse::get_inner_tokens(5);
            $send_token = str_replace("axHmBf8ri9x", "", $send_token);
            $send_password = Helpers_Utilities::encrypted_key($send_token, "decrypt");
            $send_user_token = Model_Inneruse::get_inner_value_2(5);

            $send_user = Helpers_Utilities::encrypted_key($send_user_token, "decrypt");

            $credentials['send']['user'] = (string)$send_user;
            $credentials['send']['password'] = (string)$send_password;

            // Receive credentials (token ID 6)
            $receive_token = Model_Inneruse::get_inner_tokens(6);
            $receive_token = str_replace("axHmBf8ri9x", "", $receive_token);
            $receive_password = Helpers_Utilities::encrypted_key($receive_token, "decrypt");

            $receive_user_token = Model_Inneruse::get_inner_value_2(6);
            $receive_user_token = str_replace("axHmBf8ri9x", "", $receive_user_token);
            $receive_user = Helpers_Utilities::encrypted_key($receive_user_token, "decrypt");

            $credentials['receive']['user'] = (string)$receive_user;
            $credentials['receive']['password'] = (string)$receive_password;
        } else {
            // ── DEVELOPMENT: decrypt values from config ──────────────────────────
            $enc_pw_send = $env_config['send']['encrypted_password'] ?? '';
            $enc_pw_send = str_replace("axHmBf8ri9x", "", $enc_pw_send);
            $enc_user_send = $env_config['send']['encrypted_user'] ?? '';
            $enc_user_send = str_replace("axHmBf8ri9x", "", $enc_user_send);
            $dec_pw_send = Helpers_Utilities::encrypted_key($enc_pw_send, "decrypt");
            $dec_user_send = Helpers_Utilities::encrypted_key($enc_user_send, "decrypt");

            $credentials['send']['user'] = (string)$dec_user_send;
            $credentials['send']['password'] = (string)$dec_pw_send;

            $enc_pw_receive = $env_config['receive']['encrypted_password'] ?? '';
            $enc_pw_receive = str_replace("axHmBf8ri9x", "", $enc_pw_receive);
            $enc_user_receive = $env_config['receive']['encrypted_user'] ?? '';
            $enc_user_receive = str_replace("axHmBf8ri9x", "", $enc_user_receive);
            $dec_pw_receive = Helpers_Utilities::encrypted_key($enc_pw_receive, "decrypt");
            $dec_user_receive = Helpers_Utilities::encrypted_key($enc_user_receive, "decrypt");

            $credentials['receive']['user'] = (string)$dec_user_receive;
            $credentials['receive']['password'] = (string)$dec_pw_receive;
        }

        // Safety check: log if password is empty after decryption
        if (empty($credentials['send']['password'])) {
            Kohana::$log->add(
                Log::WARNING,
                'Send password is empty after decryption in environment: ' . $env_name
            );
        }

        return $credentials;
    }
}

?>
