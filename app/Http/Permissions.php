<?php 

function user_permissions(){
    $p = [
        'general' => [
            'icon' => '<i class="bi bi-house"></i>',
            'title' => 'Global',
            'keys' => [
                'all' => 'Acceso ilimitado a todo el sistema.',
            ]
        ],
    ];

    return $p;
}