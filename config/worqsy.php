<?php

return [
    'allow_demo_login' => (bool) env('WORQSY_ALLOW_DEMO_LOGIN', false),
    'demo_password' => env('WORQSY_DEMO_PASSWORD', 'password'),
    'project_code_prefix_length' => 3,
    'default_task_weight' => 1,
    'project_health' => [
        'overdue_task_threshold' => 1,
        'revision_backlog_threshold' => 3,
    ],
];
