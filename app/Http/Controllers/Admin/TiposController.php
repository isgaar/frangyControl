<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Models\TipoServicio;

class TiposController extends Controller
{
    public function index(Request $request)
    {
        $query = [];

        if ($request->filled('search')) {
            $query['service_search'] = $request->input('search');
        }

        return redirect(route('catalogos.index', $query) . '#servicios');
    }

    public function create()
    {
        return redirect(route('catalogos.index', ['open' => 'service']) . '#servicios');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipos.*.nombreServicio' => 'required|string|max:70',
            'tipos.*.precio_base' => 'nullable|numeric|min:0|max:99999999.99',
            'tipos.*.descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'tipos.*.descuento_inicio' => 'nullable|date',
            'tipos.*.descuento_fin' => 'nullable|date',
        ]);
    
        try {
            DB::beginTransaction();
    
            foreach ($request->input('tipos', []) as $tipo) {
                TipoServicio::create($this->servicePayload($tipo));
            }
    
            DB::commit();
            Cache::forget('catalogos.ordenes.tipos_servicio');
            Session::flash('status', 'Se ha agregado correctamente el tipo de servicio');
            Session::flash('status_type', 'success');
            return redirect(route('catalogos.index') . '#servicios');
    
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            Session::flash('status', $ex->getMessage());
            Session::flash('status_type', 'error-Query');
            return back();
    
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('status', $e->getMessage());
            Session::flash('status_type', 'error');
            return back();
        }
    }
    


    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'nombreServicio' => 'required'
    //     ]);

    //     try {
    //         DB::beginTransaction();

    //         $tipoServicio = new TipoServicio([
    //             'nombreServicio' => $request['nombreServicio']
    //         ]);

    //         $tipoServicio->save();

    //         DB::commit();
    //         Session::flash('status', 'Se ha agregado correctamente el tipo de servicio');
    //         Session::flash('status_type', 'success');
    //         return redirect(route('catalogos.servicios.index'));

    //     } catch (\Illuminate\Database\QueryException $ex) {
    //         DB::rollBack();
    //         Session::flash('status', $ex->getMessage());
    //         Session::flash('status_type', 'error-Query');
    //         return back();

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Session::flash('status', $e->getMessage());
    //         Session::flash('status_type', 'error');
    //         return back();
    //     }
    // }
    public function edit($id_servicio)
    {
        $tipoServicio = TipoServicio::findOrFail($id_servicio);
        return view('admin.tipo_servicio.edit', ['tipoServicio' => $tipoServicio]);
    }

    public function update(Request $request, $id_servicio)
    {
        $tipoServicio = TipoServicio::findOrFail($id_servicio);

        $request->validate([
            'nombreServicio' => 'required|string|max:70',
            'precio_base' => 'nullable|numeric|min:0|max:99999999.99',
            'descuento_porcentaje' => 'nullable|numeric|min:0|max:100',
            'descuento_inicio' => 'nullable|date',
            'descuento_fin' => 'nullable|date|after_or_equal:descuento_inicio',
        ]);

        try {
            DB::beginTransaction();

            $tipoServicio->update($this->servicePayload($request->only([
                'nombreServicio',
                'precio_base',
                'descuento_porcentaje',
                'descuento_inicio',
                'descuento_fin',
            ])));

            DB::commit();
            Cache::forget('catalogos.ordenes.tipos_servicio');
            Session::flash('status', 'Se ha editado correctamente el nombre del servicio');
            Session::flash('status_type', 'success');
            return redirect(route('catalogos.index') . '#servicios');

        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            Session::flash('status', $ex->getMessage());
            Session::flash('status_type', 'error-Query');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('status', $e->getMessage());
            Session::flash('status_type', 'error');
            return back();
        }
    }

    public function delete($id_servicio)
    {
        $tipoServicio = TipoServicio::findOrFail($id_servicio);
        return view('admin.tipo_servicio.delete', ['tipoServicio' => $tipoServicio]);
    }

    public function destroy($id_servicio)
    {
        try {
            DB::beginTransaction();

            $tipoServicio = TipoServicio::findOrFail($id_servicio);
            $tipoServicio->delete();

            DB::commit();
            Cache::forget('catalogos.ordenes.tipos_servicio');
            Session::flash('status', 'Se ha eliminado correctamente el nombre del servicio');
            Session::flash('status_type', 'warning');
            return redirect(route('catalogos.index') . '#servicios');

        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            Session::flash('status', $ex->getMessage());
            Session::flash('status_type', 'error-Query');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('status', $e->getMessage());
            Session::flash('status_type', 'error');
            return back();
        }
    }

    private function servicePayload(array $data): array
    {
        $descuentoInicio = $data['descuento_inicio'] ?? null;
        $descuentoFin = $data['descuento_fin'] ?? null;

        return [
            'nombreServicio' => trim((string) ($data['nombreServicio'] ?? '')),
            'precio_base' => round((float) ($data['precio_base'] ?? 0), 2),
            'descuento_porcentaje' => round((float) ($data['descuento_porcentaje'] ?? 0), 2),
            'descuento_inicio' => $descuentoInicio ?: null,
            'descuento_fin' => $descuentoFin ?: null,
        ];
    }
}
