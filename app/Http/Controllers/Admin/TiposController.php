<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\TipoServicio;

class TiposController extends Controller
{
    public function index(Request $request)
    {
        $search = "";
        $limit = 10;

        if ($request->has('search')) {
            $search = $request->input('search');

            if (trim($search) != '') {
                $data = TipoServicio::where('nombreServicio', 'like', "%$search%")->get();
            } else {
                $data = TipoServicio::all();
            }
        } else {
            $data = TipoServicio::all();
        }

        $currentPage = Paginator::resolveCurrentPage() - 1;
        $perPage = $limit;
        $currentPageSearchResults = $data->slice($currentPage * $perPage, $perPage)->all();
        $data = new LengthAwarePaginator($currentPageSearchResults, count($data), $perPage);

        return view('admin.tipo_servicio.index', ['data' => $data, 'search' => $search, 'page' => $currentPage]);
    }

    public function create()
    {
        return view('admin.tipo_servicio.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombreServicio' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $tipoServicio = new TipoServicio([
                'nombreServicio' => $request['nombreServicio']
            ]);

            $tipoServicio->save();

            DB::commit();
            Session::flash('status', 'Se ha agregado correctamente el tipo de servicio');
            Session::flash('status_type', 'success');
            return redirect(route('tipo_servicio.index'));

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
    public function edit($id)
    {
        $tipoServicio = TipoServicio::findOrFail($id);
        return view('admin.tipo_servicio.edit', ['tipoServicio' => $tipoServicio]);
    }

    public function update(Request $request, $id)
    {
        $tipoServicio = TipoServicio::findOrFail($id);

        $request->validate([
            'nombreServicio' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $tipoServicio->nombreServicio = $request['nombreServicio'];

            $tipoServicio->save();

            DB::commit();
            Session::flash('status', 'Se ha editado correctamente el tipo de servicio');
            Session::flash('status_type', 'success');
            return redirect(route('tipo_servicio.index'));

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

    public function delete($id)
    {
        $tipoServicio = TipoServicio::findOrFail($id);
        return view('admin.tipo_servicio.delete', ['tipoServicio' => $tipoServicio]);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $tipoServicio = TipoServicio::findOrFail($id);
            $tipoServicio->delete();

            DB::commit();
            Session::flash('status', 'Se ha eliminado correctamente el tipo de servicio');
            Session::flash('status_type', 'success');
            return redirect(route('tipo_servicio.index'));

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
}
