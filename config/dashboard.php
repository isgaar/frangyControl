<?php

return [
    'brand' => [
        'name' => env('APP_NAME', 'Frangy Control'),
        'tagline' => 'Centro de servicio y operación',
        'logo' => 'franlogo.png',
        'home_route' => 'home',
    ],

    'menu' => [
        [
            'label' => 'General',
            'items' => [
                [
                    'label' => 'Inicio',
                    'description' => 'Resumen operativo',
                    'route' => 'home',
                    'icon' => 'fas fa-home',
                    'active' => ['home'],
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
            ],
        ],
        [
            'label' => 'Catálogos',
            'items' => [
                [
                    'label' => 'Catálogos',
                    'description' => 'Marcas, tipos y servicios',
                    'route' => 'datosv.index',
                    'icon' => 'fas fa-layer-group',
                    'active' => ['datosv.*', 'tipo_servicio.*', 'tipo_vehiculo.*'],
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
                    'route' => 'users.index',
                    'icon' => 'fas fa-user-shield',
                    'active' => ['users.*'],
                    'can' => 'admin.users.usuarios',
                ],
                [
                    'label' => 'Acerca',
                    'description' => 'Créditos y proyecto',
                    'route' => 'acerca',
                    'icon' => 'fas fa-info-circle',
                    'active' => ['acerca'],
                ],
            ],
        ],
    ],
];
