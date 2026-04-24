<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\DatosVehiculo;
use App\Models\Ordenes;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

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
        $limit = (int) $request->input('limit', 5);
        $order = $request->input('order', 'desc') === 'asc' ? 'asc' : 'desc';
        $today = Carbon::today();
        $periodStart = now()->startOfMonth();
        $user = $request->user();

        $query = Ordenes::query()->with(['cliente', 'vehiculo', 'servicio', 'user']);

        if (trim($search) !== '') {
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
        $totalOrders = Ordenes::count();
        $ordersInProgress = Ordenes::where('status', 'en proceso')->count();
        $finishedOrders = Ordenes::where('status', 'finalizada')->count();
        $overdueOrdersCount = Ordenes::query()
            ->where('status', '!=', 'finalizada')
            ->whereNotNull('fechaEntrega')
            ->whereDate('fechaEntrega', '<', $today)
            ->count();
        $newClientsInPeriod = Cliente::query()
            ->where('created_at', '>=', $periodStart)
            ->count();
        $unassignedOrdersCount = Ordenes::query()
            ->whereNull('id')
            ->count();

        $stats = [
            [
                'label' => 'Órdenes registradas',
                'value' => $totalOrders,
                'icon' => 'fas fa-clipboard-list',
                'accent' => 'primary',
                'caption' => 'Histórico total',
                'url' => route('ordenes.index'),
            ],
            [
                'label' => 'En proceso',
                'value' => $ordersInProgress,
                'icon' => 'fas fa-tools',
                'accent' => 'info',
                'caption' => 'Trabajo activo',
                'url' => route('ordenes.index'),
            ],
            [
                'label' => 'Finalizadas',
                'value' => $finishedOrders,
                'icon' => 'fas fa-check-circle',
                'accent' => 'success',
                'caption' => 'Órdenes cerradas',
                'url' => route('ordenes.index'),
            ],
            [
                'label' => 'Vencidas',
                'value' => $overdueOrdersCount,
                'icon' => 'fas fa-triangle-exclamation',
                'accent' => 'danger',
                'caption' => 'Fecha de entrega vencida',
                'url' => route('ordenes.index'),
            ],
            [
                'label' => 'Clientes del mes',
                'value' => $newClientsInPeriod,
                'icon' => 'fas fa-user-plus',
                'accent' => 'warning',
                'caption' => 'Altas del periodo',
                'url' => route('clientes.index'),
            ],
            [
                'label' => 'Sin asignar',
                'value' => $unassignedOrdersCount,
                'icon' => 'fas fa-user-clock',
                'accent' => 'danger',
                'caption' => 'Pendientes de responsable',
                'url' => route('ordenes.index'),
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

        $operationalAlerts = collect([
            $unassignedOrdersCount > 0 ? [
                'title' => 'Órdenes sin asignar',
                'count' => $unassignedOrdersCount,
                'tone' => 'warning',
                'message' => 'Conviene asignar responsable para mantener trazabilidad y seguimiento.',
            ] : null,
            $overdueOrdersCount > 0 ? [
                'title' => 'Órdenes vencidas',
                'count' => $overdueOrdersCount,
                'tone' => 'danger',
                'message' => 'Hay órdenes con fechaEntrega anterior a hoy y todavía abiertas.',
            ] : null,
        ])->filter()->values();

        $operationalMessages = collect([
            [
                'title' => 'Estado operativo',
                'eyebrow' => $ordersInProgress > 0 ? 'Activo' : 'En calma',
                'tone' => $ordersInProgress > 0 ? 'info' : 'success',
                'message' => $ordersInProgress > 0
                    ? 'El taller mantiene órdenes en curso; conviene vigilar entregas y asignaciones.'
                    : 'No hay órdenes en proceso registradas en este momento.',
            ],
            [
                'title' => 'Clientes del periodo',
                'eyebrow' => 'Crecimiento',
                'tone' => $newClientsInPeriod > 0 ? 'success' : 'warning',
                'message' => $newClientsInPeriod > 0
                    ? "Este mes se registraron {$newClientsInPeriod} clientes nuevos."
                    : 'Todavía no se han registrado clientes nuevos en el periodo actual.',
            ],
            [
                'title' => 'Búsqueda aplicada',
                'eyebrow' => $search !== '' ? 'Filtrando' : 'Libre',
                'tone' => $search !== '' ? 'info' : 'success',
                'message' => $search !== ''
                    ? "La lista actual está filtrada por el criterio \"{$search}\"."
                    : 'La vista muestra los registros recientes sin filtros activos.',
            ],
        ])->values();

        $quickActions = collect([
            [
                'label' => 'Registrar orden',
                'route' => 'ordenes.create',
                'icon' => 'fas fa-file-circle-plus',
            ],
            [
                'label' => 'Registrar cliente',
                'route' => 'clientes.create',
                'icon' => 'fas fa-user-plus',
            ],
            [
                'label' => 'Registrar vehículo',
                'route' => 'datosv.createunique',
                'icon' => 'fas fa-car',
                'can' => 'admin.datosv.vehiculosnom',
            ],
        ])->filter(function (array $action) use ($user) {
            if (!Route::has($action['route'])) {
                return false;
            }

            if (empty($action['can'])) {
                return true;
            }

            return $user && method_exists($user, 'can') && $user->can($action['can']);
        })->values();

        $dashboardProfile = [
            'primary_role' => $user && method_exists($user, 'getRoleNames')
                ? ($user->getRoleNames()->first() ?: 'Sin rol')
                : 'Sin rol',
            'roles' => $user && method_exists($user, 'getRoleNames')
                ? $user->getRoleNames()->values()->all()
                : [],
        ];

        return view('home', [
            'ordenes' => $ordenes,
            'search' => $search,
            'limit' => $limit,
            'order' => $order,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentClients' => $recentClients,
            'recentVehicles' => $recentVehicles,
            'operationalAlerts' => $operationalAlerts,
            'operationalMessages' => $operationalMessages,
            'quickActions' => $quickActions,
            'dashboardProfile' => $dashboardProfile,
        ]);
    }
    public function about(Request $request)
    {
        return view('acerca');
    }
}
