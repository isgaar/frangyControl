<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Cotizacion;
use App\Models\TipoServicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class CotizacionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $estado = $request->input('estado');
        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $limit = (int) $request->input('limit', 10);
        $limit = in_array($limit, [5, 10, 15, 25], true) ? $limit : 10;

        $query = Cotizacion::query()->with(['cliente', 'servicio', 'user']);

        if ($search !== '') {
            $matchingClientIds = Cliente::matchingSearchIds($search);

            $query->where(function ($query) use ($search, $matchingClientIds) {
                $query->where('folio', 'like', "%{$search}%")
                    ->orWhereHas('servicio', fn ($query) => $query->where('nombreServicio', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($query) => $query->where('name', 'like', "%{$search}%"));

                if ($matchingClientIds->isNotEmpty()) {
                    $query->orWhereIn('cliente_id', $matchingClientIds->all());
                }
            });
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        $cotizaciones = $query->orderBy('id_cotizacion', $order)->paginate($limit)->withQueryString();

        return view('admin.cotizaciones.index', compact('cotizaciones', 'search', 'estado', 'order', 'limit'));
    }

    public function create()
    {
        return view('admin.cotizaciones.form', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validateCotizacion($request);

        try {
            DB::beginTransaction();

            Cotizacion::create(array_merge($this->pricedPayload($validated), [
                'folio' => $this->nextFolio(),
                'user_id' => ($validated['user_id'] ?? null) ?: $request->user()->id,
            ]));

            DB::commit();
            Session::flash('status', 'Se ha creado correctamente la cotización.');
            Session::flash('status_type', 'success');

            return redirect()->route('cotizaciones.index');
        } catch (\Throwable $exception) {
            DB::rollBack();
            Session::flash('status', $exception->getMessage());
            Session::flash('status_type', 'danger');

            return back()->withInput();
        }
    }

    public function show($id_cotizacion)
    {
        $cotizacion = Cotizacion::with(['cliente', 'servicio', 'user'])->findOrFail($id_cotizacion);

        return view('admin.cotizaciones.show', compact('cotizacion'));
    }

    public function edit($id_cotizacion)
    {
        $cotizacion = Cotizacion::findOrFail($id_cotizacion);

        return view('admin.cotizaciones.form', array_merge($this->formData(), compact('cotizacion')));
    }

    public function update(Request $request, $id_cotizacion)
    {
        $cotizacion = Cotizacion::findOrFail($id_cotizacion);
        $validated = $this->validateCotizacion($request);

        try {
            DB::beginTransaction();

            $cotizacion->fill(array_merge($this->pricedPayload($validated), [
                'user_id' => ($validated['user_id'] ?? null) ?: $request->user()->id,
            ]));
            $cotizacion->save();

            DB::commit();
            Session::flash('status', 'Se ha actualizado correctamente la cotización.');
            Session::flash('status_type', 'success');

            return redirect()->route('cotizaciones.show', $cotizacion->id_cotizacion);
        } catch (\Throwable $exception) {
            DB::rollBack();
            Session::flash('status', $exception->getMessage());
            Session::flash('status_type', 'danger');

            return back()->withInput();
        }
    }

    public function destroy($id_cotizacion)
    {
        $cotizacion = Cotizacion::findOrFail($id_cotizacion);
        $cotizacion->delete();

        Session::flash('status', 'Se ha eliminado correctamente la cotización.');
        Session::flash('status_type', 'warning');

        return redirect()->route('cotizaciones.index');
    }

    private function validateCotizacion(Request $request): array
    {
        return $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id_cliente'],
            'servicio_id' => ['required', 'exists:tipo_servicio,id_servicio'],
            'user_id' => ['nullable', 'exists:users,id'],
            'precio_base' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'descuento_porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vigencia' => ['nullable', 'date', 'after_or_equal:today'],
            'estado' => ['required', Rule::in(['borrador', 'enviada', 'aceptada', 'rechazada', 'vencida'])],
            'notas' => ['nullable', 'string', 'max:2000'],
        ], [
            'cliente_id.required' => 'Selecciona un cliente.',
            'servicio_id.required' => 'Selecciona un servicio.',
            'precio_base.required' => 'Indica el precio de la cotización.',
            'vigencia.after_or_equal' => 'La vigencia no puede ser anterior a hoy.',
        ]);
    }

    private function pricedPayload(array $validated): array
    {
        $precioBase = round((float) $validated['precio_base'], 2);
        $descuentoPorcentaje = round((float) ($validated['descuento_porcentaje'] ?? 0), 2);
        $descuentoMonto = round($precioBase * ($descuentoPorcentaje / 100), 2);

        return [
            'cliente_id' => $validated['cliente_id'],
            'servicio_id' => $validated['servicio_id'],
            'precio_base' => $precioBase,
            'descuento_porcentaje' => $descuentoPorcentaje,
            'descuento_monto' => $descuentoMonto,
            'total' => max(round($precioBase - $descuentoMonto, 2), 0),
            'vigencia' => $validated['vigencia'] ?? null,
            'estado' => $validated['estado'],
            'notas' => $validated['notas'] ?? null,
        ];
    }

    private function formData(): array
    {
        return [
            'clientes' => Cliente::query()
                ->select(['id_cliente', 'nombreCompleto', 'telefono'])
                ->get()
                ->sortBy(fn (Cliente $cliente) => mb_strtolower($cliente->nombreCompleto))
                ->values(),
            'servicios' => TipoServicio::query()
                ->select(['id_servicio', 'nombreServicio', 'precio_base', 'descuento_porcentaje', 'descuento_inicio', 'descuento_fin'])
                ->orderBy('nombreServicio')
                ->get()
                ->map(function (TipoServicio $servicio) {
                    return [
                        'id_servicio' => $servicio->id_servicio,
                        'nombreServicio' => $servicio->nombreServicio,
                        'precio_base' => (float) $servicio->precio_base,
                        'descuento_porcentaje' => (float) $servicio->descuento_porcentaje,
                        'descuento_activo' => $servicio->descuentoActivo(),
                        'precio_cotizado' => $servicio->precioCotizado(),
                    ];
                }),
            'users' => User::query()->select(['id', 'name'])->where('id', '!=', 1)->orderBy('name')->get(),
            'estados' => $this->estados(),
        ];
    }

    private function estados(): array
    {
        return [
            'borrador' => 'Borrador',
            'enviada' => 'Enviada',
            'aceptada' => 'Aceptada',
            'rechazada' => 'Rechazada',
            'vencida' => 'Vencida',
        ];
    }

    private function nextFolio(): string
    {
        $prefix = 'COT-' . now()->format('Ymd') . '-';
        $next = Cotizacion::query()->where('folio', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
