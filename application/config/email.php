<?php defined('SYSPATH') or die('No direct script access.');
return array(
    'sender_name' => 'CTD KPK',

    'environments' => array(
        'development' => array(
            'send' => array(
                'encrypted_user' => 'ZnRWM2hVYStYczBicnJ0ekhPT2hCQTJ1Vk1CZkFtTVNlUXdHejVXQkdOOD0=',  // ← real test Gmail address
                'encrypted_password' => 'bWZVREVBaGY4TWpCZGphT1Jtd2hROVNkOXlRSlpWWjdUeUFud0FwSkR6az0==',
            ),
            'receive' => array(
                'encrypted_user' => 'ZnRWM2hVYStYczBicnJ0ekhPT2hCQTJ1Vk1CZkFtTVNlUXdHejVXQkdOOD0=',  // ← real test Gmail address
                'encrypted_password' => 'bWZVREVBaGY4TWpCZGphT1Jtd2hROVNkOXlRSlpWWjdUeUFud0FwSkR6az0==',
            ),
        ),

        'production' => array(
            // These values are ignored in production
            'send' => array('encrypted_user' => null, 'encrypted_password' => null),
            'receive' => array('encrypted_user' => null, 'encrypted_password' => null),
        ),
    ),
);