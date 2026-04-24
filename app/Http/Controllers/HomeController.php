<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DatosVehiculo;
use App\Models\Ordenes;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $limit = $request->input('limit', 5);
        $order = $request->input('order', 'desc');

        $query = Ordenes::query()->with(['cliente', 'vehiculo', 'servicio', 'user']);

        if (trim($search) != '') {
            $query->where(function ($query) use ($search) {
                $query->where('id_ordenes', 'like', "%$search%")
                    ->orWhereHas('cliente', function ($query) use ($search) {
                        $query->where('nombreCompleto', 'like', "%$search%");
                    })
                    ->orWhereHas('vehiculo', function ($query) use ($search) {
                        $query->where('marca', 'like', "%$search%");
                    })
                    ->orWhere('placas', 'like', "%$search%")
                    ->orWhereHas('servicio', function ($query) use ($search) {
                        $query->where('nombreServicio', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });
            });
        }



        $ordenes = $query->orderBy('id_ordenes', $order)->paginate($limit)->withQueryString();

        $stats = [
            [
                'label' => 'Órdenes registradas',
                'value' => Ordenes::count(),
                'icon' => 'fas fa-clipboard-list',
                'accent' => 'primary',
                'caption' => 'Histórico total',
                'url' => route('ordenes.index'),
            ],
            [
                'label' => 'En proceso',
                'value' => Ordenes::where('status', 'en proceso')->count(),
                'icon' => 'fas fa-tools',
                'accent' => 'info',
                'caption' => 'Trabajo activo',
                'url' => route('ordenes.index'),
            ],
            [
                'label' => 'Finalizadas',
                'value' => Ordenes::where('status', 'finalizada')->count(),
                'icon' => 'fas fa-check-circle',
                'accent' => 'success',
                'caption' => 'Órdenes cerradas',
                'url' => route('ordenes.index'),
            ],
            [
                'label' => 'Clientes',
                'value' => Cliente::count(),
                'icon' => 'fas fa-user-tie',
                'accent' => 'warning',
                'caption' => 'Registros activos',
                'url' => route('clientes.index'),
            ],
            [
                'label' => 'Vehículos',
                'value' => DatosVehiculo::count(),
                'icon' => 'fas fa-car-side',
                'accent' => 'dark',
                'caption' => 'Catálogo cargado',
                'url' => route('datosv.index'),
            ],
        ];

        $recentOrders = Ordenes::query()
            ->with(['cliente', 'vehiculo', 'servicio'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function (Ordenes $orden) {
                $createdAt = $orden->getRawOriginal('created_at');

                return [
                    'id' => $orden->id_ordenes,
                    'cliente' => optional($orden->cliente)->nombreCompleto ?? 'Sin cliente',
                    'vehiculo' => optional($orden->vehiculo)->marca ?? 'Sin vehículo',
                    'servicio' => optional($orden->servicio)->nombreServicio ?? 'Sin servicio',
                    'status' => $orden->status ?? 'sin estado',
                    'created_at' => $createdAt
                        ? Carbon::parse($createdAt)->format('d/m/Y H:i')
                        : 'Sin fecha',
                    'url' => route('ordenes.show', $orden->id_ordenes),
                ];
            });

        $recentClients = Cliente::query()
            ->latest('created_at')
            ->limit(5)
            ->get();

        $recentVehicles = DatosVehiculo::query()
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('home', [
            'ordenes' => $ordenes,
            'search' => $search,
            'limit' => $limit,
            'order' => $order,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentClients' => $recentClients,
            'recentVehicles' => $recentVehicles,
        ]);
    }
    public function about(Request $request)
    {
        return view('acerca');
    }
}
