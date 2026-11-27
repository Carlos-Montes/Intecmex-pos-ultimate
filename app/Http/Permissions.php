<?php

function user_permissions()
{
    return [

        'general' => [
            'icon' => '<i class="bi bi-house"></i>',
            'title' => 'Global',
            'keys' => [
                'all' => 'Acceso ilimitado a todo el sistema.',
            ],
        ],

        'dashboard' => [
            'icon' => '<i class="bi bi-speedometer2"></i>',
            'title' => 'Dashboard',
            'keys' => [
                'dashboard' => 'Consultar el panel principal.',
            ],
        ],

        'categoryes' => [
            'icon' => '<i class="bi bi-tags"></i>',
            'title' => 'Categorías',
            'keys' => [
                'categories' => 'Consultar categorías.',
                'subcategories' => 'Consultar subcategorías.',
            ],
        ],

        'products' => [
            'icon' => '<i class="bi bi-box-seam"></i>',
            'title' => 'Productos',
            'keys' => [
                'product' => 'Consultar productos.',
                'product_type' => 'Consultar tipos de productos.',
                'product_add' => 'Registrar productos.',
                'product_edit' => 'Editar productos',
                'product_delete' => 'Puede eliminar productos'
            ],
        ],

        'pos' => [
            'icon' => '<i class="bi bi-cart3"></i>',
            'title' => 'Punto de venta',
            'keys' => [
                'pos' => 'Acceder al punto de venta.',
                'sales' => 'Consultar historial de ventas.',
                'sales.detail' => 'Consultar detalle de ventas.',
                'api_pos_products' => 'Puede ver productos en el pos',
                'api_pos_customers' => 'Puede ver clientes con descuento'
            ],
        ],

        'accounting' => [
            'icon' => '<i class="bi bi-journal-text"></i>',
            'title' => 'Contabilidad',
            'keys' => [
                'finances' => 'Consultar cuentas y movimientos financieros.',
            ],
        ],

        'inventory' => [
            'icon' => '<i class="bi bi-clipboard-data"></i>',
            'title' => 'Inventario',
            'keys' => [
                'inventory' => 'Consultar inventario.',
            ],
        ],

        'users' => [
            'icon' => '<i class="bi bi-people"></i>',
            'title' => 'Usuarios',
            'keys' => [
                'users_list' => 'Consultar usuarios.',
                'users_views' => 'Consultar información de usuarios.',
                'new_user' => 'Registrar nuevos usuarios.',
                'users_permissions' => 'Administrar permisos de usuarios.',
                'api_users_inactive' => 'Puede desactivar usuarios'
            ],
        ],

        'reports' => [
            'icon' => '<i class="bi bi-file-earmark-bar-graph"></i>',
            'title' => 'Reportes',
            'keys' => [
                'reports' => 'Acceder a los reportes.',
            ],
        ],

    ];
}
