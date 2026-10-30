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
        'dashboard' => [
            'icon' => '<i class="bi bi-house"></i>',
            'title' => 'Dashboard',
            'keys' => [

            ]
        ],
        'categoryes' => [
            'icon' => '<i class="bi bi-tags"></i>',
            'title' => 'Categorías',
            'keys' => [

            ]
        ],
        'products' => [
            'icon' => '<i class="bi bi-box"></i>',
            'title' => 'Productos',
            'keys' => [

            ]
        ],
        'pos' => [
            'icon' => '<i class="bi bi-file-post"></i>',
            'title' => 'Pos',
            'keys' => [

            ]
        ],
        'accounting' => [
            'icon' => '<i class="bi bi-book"></i>',
            'title' => 'Contabilidad',
            'keys' => [

            ]
        ],
        'inventory' => [
            'icon' => '<i class="bi bi-card-checklist"></i>',
            'title' => 'Inventario',
            'keys' => [

            ]
        ],
        'users' => [
            'icon' => '<i class="bi bi-people"></i>',
            'title' => 'Usuarios',
            'keys' => [

            ]
        ],  
        'reports' => [
            'icon' => '<i class="bi bi-box-arrow-in-down"></i>',
            'title' => 'Reportes',
            'keys' => [

            ]
        ],
        'configuration' => [
            'icon' => '<i class="bi bi-gear"></i>',
            'title' => 'Configuración',
            'keys' => [

            ]
        ],   
    ];

    return $p;
}