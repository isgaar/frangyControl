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
            $matchingClientIds = Cliente::matchingSearchIds($search);

            $query->where(function ($query) use ($search, $matchingClientIds) {
                $query->where('id_ordenes', 'like', "%$search%")
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

                if ($matchingClientIds->isNotEmpty()) {
                    $query->orWhereIn('cliente_id', $matchingClientIds->all());
                }
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
                'route' => 'catalogos.marcas.create',
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

        // 1. Gráfica Semanal (Órdenes creadas por día, semana actual vs pasada)
        $startOfCurrentWeek = now()->startOfWeek();
        $startOfPreviousWeek = now()->subWeek()->startOfWeek();
        $endOfPreviousWeek = now()->subWeek()->endOfWeek();

        $currentWeekOrders = Ordenes::where('created_at', '>=', $startOfCurrentWeek)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $previousWeekOrders = Ordenes::whereBetween('created_at', [$startOfPreviousWeek, $endOfPreviousWeek])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $dayLabels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $weeklyBars = collect($dayLabels)->map(function($dayName, $index) use ($currentWeekOrders, $previousWeekOrders, $startOfCurrentWeek, $startOfPreviousWeek) {
            $currDate = $startOfCurrentWeek->copy()->addDays($index)->format('Y-m-d');
            $prevDate = $startOfPreviousWeek->copy()->addDays($index)->format('Y-m-d');
            return [
                'lbl' => $dayName,
                'cur' => $currentWeekOrders->get($currDate, 0),
                'pre' => $previousWeekOrders->get($prevDate, 0),
            ];
        });

        $maxCount = max(1, $weeklyBars->max('cur'), $weeklyBars->max('pre'));
        $weeklyBars->transform(function($item) use ($maxCount) {
            $item['cur_pct'] = round(($item['cur'] / $maxCount) * 100);
            $item['pre_pct'] = round(($item['pre'] / $maxCount) * 100);
            return $item;
        });

        // 2. Gráfica de Dona (Distribución de servicios)
        $totalOrdersCount = Ordenes::whereNotNull('servicio_id')->count();
        $topServices = Ordenes::whereNotNull('servicio_id')
            ->select('servicio_id', \DB::raw('COUNT(*) as count'))
            ->groupBy('servicio_id')
            ->orderByDesc('count')
            ->limit(4)
            ->with('servicio')
            ->get();

        $donutColors = ['#0d6efd', '#198754', '#dc3545', '#ffc107'];
        $serviceDistribution = $topServices->map(function($item, $index) use ($totalOrdersCount, $donutColors) {
            $pct = $totalOrdersCount > 0 ? round(($item->count / $totalOrdersCount) * 100) : 0;
            return [
                'name' => optional($item->servicio)->nombreServicio ?? 'Desconocido',
                'pct' => $pct,
                'count' => $item->count,
                'color' => $donutColors[$index % 4],
            ];
        });
        $serviceTotal = $totalOrdersCount;

        // 3. Feed de Actividad Reciente
        $feedOrders = Ordenes::with(['cliente'])->latest('updated_at')->limit(5)->get()->map(function($o) {
            $isNew = $o->created_at == $o->updated_at;
            return [
                'type' => 'orden',
                'timestamp' => Carbon::parse($o->updated_at),
                'color' => $isNew ? '#0d6efd' : '#ffc107',
                'title' => 'Orden #' . $o->id_ordenes . ($isNew ? ' registrada' : ' actualizada'),
                'meta' => (optional($o->cliente)->nombreCompleto ?? 'Cliente general') . ' · ' . Carbon::parse($o->updated_at)->diffForHumans()
            ];
        });

        $feedClients = Cliente::latest('created_at')->limit(5)->get()->map(function($c) {
            return [
                'type' => 'cliente',
                'timestamp' => Carbon::parse($c->created_at),
                'color' => '#198754',
                'title' => 'Nuevo cliente registrado',
                'meta' => $c->nombreCompleto . ' · ' . Carbon::parse($c->created_at)->diffForHumans()
            ];
        });

        $activityFeed = $feedOrders->concat($feedClients)
            ->sortByDesc(fn($item) => $item['timestamp']->timestamp)
            ->take(6)
            ->values();

        // 4. Marcas frecuentes
        $topBrandsData = Ordenes::whereNotNull('vehiculo_id')
            ->select('vehiculo_id', \DB::raw('COUNT(*) as count'))
            ->groupBy('vehiculo_id')
            ->orderByDesc('count')
            ->limit(5)
            ->with('vehiculo')
            ->get()
            ->map(function($item, $index) {
                $brandColors = ['#dc3545', '#ffc107', '#0d6efd', '#198754', '#6610f2'];
                return [
                    'name' => optional($item->vehiculo)->marca ?? 'Desconocido',
                    'count' => $item->count,
                    'color' => $brandColors[$index % 5],
                ];
            });
        $maxBrandCount = max(1, $topBrandsData->max('count') ?? 1);
        $topBrands = $topBrandsData->map(function($item) use ($maxBrandCount) {
            $item['max'] = $maxBrandCount;
            return $item;
        });

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
            'weeklyBars' => $weeklyBars,
            'serviceDistribution' => $serviceDistribution,
            'serviceTotal' => $serviceTotal,
            'activityFeed' => $activityFeed,
            'topBrands' => $topBrands,
        ]);
    }
    public function about(Request $request)
    {
        return view('acerca');
    }
}
