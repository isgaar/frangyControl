<?php

return [
    'brand' => [
        'name' => env('APP_NAME', 'Frangy Control'),
        'tagline' => 'Centro de servicio y operación',
        'logo' => 'franlogo.png',
        'home_route' => 'panel.index',
    ],

    'menu' => [
        [
            'label' => 'General',
            'items' => [
                [
                    'label' => 'Inicio',
                    'description' => 'Resumen operativo',
                    'route' => 'panel.index',
                    'icon' => 'fas fa-home',
                    'active' => ['panel.index'],
                ],
                [
                    'label' => 'Órdenes',
                    'description' => 'Seguimiento del taller',
                    'route' => 'ordenes.index',
                    'icon' => 'fas fa-clipboard-list',
                    'active' => ['ordenes.*'],
                ],
                [
                    'label' => 'Clientes',
                    'description' => 'Directorio y atención',
                    'route' => 'clientes.index',
                    'icon' => 'fas fa-users',
                    'active' => ['clientes.*'],
                ],
                [
                    'label' => 'Cotizaciones',
                    'description' => 'Precios y propuestas',
                    'route' => 'cotizaciones.index',
                    'icon' => 'fas fa-file-invoice-dollar',
                    'active' => ['cotizaciones.*'],
                ],
                [
                    'label' => 'Campañas',
                    'description' => 'Ofertas y recordatorios',
                    'route' => 'campanas.index',
                    'icon' => 'fas fa-bullhorn',
                    'active' => ['campanas.*'],
                ],
                [
                    'label' => 'Inventario',
                    'description' => 'Almacén y Refacciones',
                    'route' => 'inventario.index',
                    'icon' => 'fas fa-boxes',
                    'active' => ['inventario.*'],
                ],
                [
                    'label' => 'Chat',
                    'description' => 'Mensajes internos',
                    'route' => 'chat.index',
                    'icon' => 'fas fa-comments',
                    'active' => ['chat.*'],
                ],
            ],
        ],
        [
            'label' => 'Catálogos',
            'items' => [
                [
                    'label' => 'Catálogos',
                    'description' => 'Marcas, tipos y servicios',
                    'route' => 'catalogos.index',
                    'icon' => 'fas fa-layer-group',
                    'active' => ['catalogos.*'],
                    'can' => 'admin.datosv.vehiculosnom',
                ],
            ],
        ],
        [
            'label' => 'Administración',
            'items' => [
                [
                    'label' => 'Usuarios',
                    'description' => 'Accesos y permisos',
                    'route' => 'usuarios.index',
                    'icon' => 'fas fa-user-shield',
                    'active' => ['usuarios.*'],
                    'can' => 'admin.users.usuarios',
                ],
                [
                    'label' => 'Bitácora',
                    'description' => 'Registro de actividad',
                    'route' => 'auditoria.index',
                    'icon' => 'fas fa-history',
                    'active' => ['auditoria.*'],
                    'can' => 'admin.users.usuarios',
                ],
                [
                    'label' => 'Importar BD',
                    'description' => 'Carga masiva de datos',
                    'route' => 'database.import.index',
                    'icon' => 'fas fa-database',
                    'active' => ['base-de-datos.*'],
                    'can' => 'admin.users.usuarios',
                ],
                [
                    'label' => 'Acerca',
                    'description' => 'Créditos y proyecto',
                    'route' => 'acerca.index',
                    'icon' => 'fas fa-info-circle',
                    'active' => ['acerca.index'],
                ],
            ],
        ],
    ],
];
