<?php
$anonymisation = [
    'Data_base' => 'qsite',

    'KeyWord' => [
        'Psychiatry' => 'XXXX',
    ],

    'Counter' => '100',

    'Tables' => [

        'fos_user' => [
            'alias' => 'fos_user fosu',
            'mapping' => [
                'email' => "fosu.email = |email",
                'email_canonical'=> "email_canonical = rd.email",
                'first_name' => "first_name = rd.firstname",
                'last_name' => "last_name =  rd.lastname",
                'created_by' => "created_by = |User#quipment.fr",
                'updated_by' => "updated_by = |User#quipment.fr",
                'username' => "username = |UserX#",
                'username_canonical' => "username_canonical =|UserX#"
            ],
            'condition' => ' WHERE (MOD(fosu.id,1499) +1) = rd.id'
        ],
      ]
    ];

return $anonymisation;
