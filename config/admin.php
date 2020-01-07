<?php

return [

    'navigation_type' => [
        'admin' => '后台系统',
        'backend' => '管理后台',
    ],

    'guard_names' => [
        'admin' => '后台守卫',
    ],

    'system_name' => env("ADMIN_SYSTEM_NAME", "后台管理系统"),

    'model_navigation'=>'admin_navigation',
    'model_permission_groups'=>'admin_permission_groups',
];