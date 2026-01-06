<?php

if (!empty($user_data)) {
            try {
                //user not exist
                $is_duplicate_username =!empty($user_data['username']) ? Helpers_Utilities::username_duplicate($user_data['username']) : '';
                $is_duplicate_email =!empty($user_data['email']) ?  Helpers_Utilities::email_duplicate($user_data['email']) : '';
                if (!empty($is_duplicate_username)) {
                    $error_code = -13;
                } elseif (!empty($is_duplicate_email)) {
                    $error_code = -14;
                } else {
                    if ($user_data['created_from'] == 'aies') {
                        $user_data['is_active'] = 1;
                        $user_data['is_active_cis'] = 0;
                        $user_data['is_active_ctfu'] = 0;
                        $user_data['login_sites'] = 0;
                    } elseif ($user_data['created_from'] == 'cis') {
                        $user_data['is_active'] = 0;
                        $user_data['is_active_ctfu'] = 0;
                        $user_data['is_active_cis'] = 1;
                        $user_data['login_sites'] = 1;
                    } elseif ($user_data['created_from'] == 'ctfu') {
                        $user_data['is_active'] = 0;
                        $user_data['is_active_cis'] = 0;
                        $user_data['is_active_ctfu'] = 1;
                        $user_data['login_sites'] = 3;
                    } else {
                        $error_code = -2;
                    }
                    if (empty($error_code)) {
                        if (isset($user_data['user_pic']) and $user_data['user_pic'] != "") {
                            $user_img = Helpers_Profile::_save_image($user_data['user_pic'], "user");
                        } else {
                            $user_img = "";
                        }

                        $new_user = ORM::factory('User')->create_user($user_data, array(
                            'username',
                            'password',
                            'email',
                            'login_sites',
                            'is_active',
                            'is_active_cis',
                        ));
                        $new_user->add('roles', ORM::factory('Role', array('name' => $user_data['type'])));

                        $user_data['user_id'] = $new_user->id;
                        $user_data['created_by'] = $user_data['uid'];
                        date_default_timezone_set("Asia/Karachi");
                        $user_data['created_at'] = date("Y-m-d H:i:s");
                        $user_data[''] = date("Y-m-d H:i:s");
                        $user_data['file_name'] = $user_img;

                        $data = new Model_Email();
                        $data1 = $data->user_insert($user_data);
                    }
                }
            } catch (ORM_Validation_Exception $e) {
                $error_code = -5;
            }
        } else {
            $error_code = -1;
        }