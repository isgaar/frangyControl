<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ordenes;
use Illuminate\Support\Carbon;
use PDF;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data = $this->getAnalyticsData($request);
        return view('admin.reportes.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getAnalyticsData($request);
        
        $html = view('admin.reportes.analiticas_pdf', $data)->render();
        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('analisis_ingresos_' . $data['selectedMonth'] . '_' . $data['selectedYear'] . '.pdf');
    }

    private function getAnalyticsData(Request $request)
    {
        $selectedYear = $request->input('year', now()->year);
        $selectedMonth = $request->input('month', now()->month);

        $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth();

        // Previous month for comparison
        $prevStartDate = $startDate->copy()->subMonth();
        $prevEndDate = $prevStartDate->copy()->endOfMonth();

        // 1. Gráfica Comparativa (Semanas del mes actual vs mes anterior)
        // Group orders by week of the month (1, 2, 3, 4, 5)
        $currentMonthOrders = Ordenes::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->weekOfMonth;
            });

        $prevMonthOrders = Ordenes::whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->created_at)->weekOfMonth;
            });

        $weeklyBars = collect([1, 2, 3, 4, 5])->map(function($weekNum) use ($currentMonthOrders, $prevMonthOrders) {
            $cur = isset($currentMonthOrders[$weekNum]) ? $currentMonthOrders[$weekNum]->count() : 0;
            $pre = isset($prevMonthOrders[$weekNum]) ? $prevMonthOrders[$weekNum]->count() : 0;
            return [
                'lbl' => 'Sem ' . $weekNum,
                'cur' => $cur,
                'pre' => $pre,
            ];
        });

        $maxCount = max(1, $weeklyBars->max('cur'), $weeklyBars->max('pre'));
        $weeklyBars->transform(function($item) use ($maxCount) {
            $item['cur_pct'] = round(($item['cur'] / $maxCount) * 100);
            $item['pre_pct'] = round(($item['pre'] / $maxCount) * 100);
            return $item;
        });

        // 2. Distribución de servicios en el periodo seleccionado
        $totalOrdersCount = Ordenes::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('servicio_id')->count();
            
        $topServices = Ordenes::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('servicio_id')
            ->select('servicio_id', DB::raw('COUNT(*) as count'))
            ->groupBy('servicio_id')
            ->orderByDesc('count')
            ->limit(5)
            ->with('servicio')
            ->get();

        $donutColors = ['#0d6efd', '#198754', '#dc3545', '#ffc107', '#6f42c1'];
        $serviceDistribution = $topServices->map(function($item, $index) use ($totalOrdersCount, $donutColors) {
            $pct = $totalOrdersCount > 0 ? round(($item->count / $totalOrdersCount) * 100) : 0;
            return [
                'name' => optional($item->servicio)->nombreServicio ?? 'Desconocido',
                'pct' => $pct,
                'count' => $item->count,
                'color' => $donutColors[$index % 5],
            ];
        });

        // List of available years for the select (from oldest order to current year)
        $oldestOrder = Ordenes::orderBy('created_at', 'asc')->first();
        $startYear = $oldestOrder ? Carbon::parse($oldestOrder->created_at)->year : now()->year;
        $availableYears = range($startYear, now()->year);

        return [
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'weeklyBars' => $weeklyBars,
            'serviceDistribution' => $serviceDistribution,
            'serviceTotal' => $totalOrdersCount,
            'availableYears' => $availableYears,
            'totalCurrentMonth' => Ordenes::whereBetween('created_at', [$startDate, $endDate])->count(),
            'totalPrevMonth' => Ordenes::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count(),
            'monthName' => $startDate->translatedFormat('F'),
        ];
    }
}
