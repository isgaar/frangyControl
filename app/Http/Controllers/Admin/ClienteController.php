<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ClientDataSecurity;
use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\SaveClienteRequest;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $limit = max(1, (int) $request->input('limit', 10));
        $sortBy = in_array($request->input('sort_by'), ['id_cliente', 'nombreCompleto'], true)
            ? $request->input('sort_by')
            : 'id_cliente';
        $sortOrder = $request->input('sort_order') === 'desc' ? 'desc' : 'asc';

        if ($search !== '' || $sortBy === 'nombreCompleto') {
            $clientes = Cache::remember('catalogos.ordenes.clientes', now()->addMinutes(10), fn () =>
                Cliente::query()->get(['id_cliente', 'nombreCompleto', 'telefono', 'correo', 'rfc'])
            );

            if ($search !== '') {
                $clientes = $clientes->filter(fn (Cliente $cliente) => ClientDataSecurity::matchesSearch([
                    $cliente->nombreCompleto,
                    $cliente->telefono,
                    $cliente->correo,
                    $cliente->rfc,
                ], $search));
            }

            $clientes = $sortBy === 'nombreCompleto'
                ? $clientes->sortBy(fn (Cliente $cliente) => ClientDataSecurity::normalize('nombreCompleto', $cliente->nombreCompleto), SORT_REGULAR, $sortOrder === 'desc')
                : $clientes->sortBy('id_cliente', SORT_REGULAR, $sortOrder === 'desc');

            $data = $this->paginateClients($clientes->values(), $limit, $request);
        } else {
            $data = Cliente::query()
                ->orderBy('id_cliente', $sortOrder)
                ->paginate($limit)
                ->withQueryString();
        }

        return view('admin.clientes.index', [
            'data' => $data,
            'search' => $search,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function create()
    {
        return view('admin.clientes.create');
    }

    public function store(SaveClienteRequest $request)
    {
        try {
            DB::beginTransaction();

            Cliente::create($request->validated());
            Cache::forget('catalogos.ordenes.clientes');

            DB::commit();

            Session::flash('status', 'Se ha agregado exitosamente el cliente');
            Session::flash('status_type', 'success');
            return redirect()->route('clientes.index');

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

    public function show($id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);

        return view('admin.clientes.show', ['cliente' => $cliente]);
    }

    public function edit($id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);

        return view('admin.clientes.edit', ['cliente' => $cliente]);
    }

    public function update(SaveClienteRequest $request, $id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);

        try {
            $cliente->fill($request->validated())->save();
            Cache::forget('catalogos.ordenes.clientes');

            return redirect(route('clientes.index'))->with('status', 'Se ha editado correctamente el cliente')->with('status_type', 'success');
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('status', $ex->getMessage())->with('status_type', 'error-Query');
        } catch (\Exception $e) {
            return back()->with('status', $e->getMessage())->with('status_type', 'error');
        }
    }

    public function delete($id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);

        return view('admin.clientes.delete', ['cliente' => $cliente]);
    }

    public function destroy($id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);

        try {
            $cliente->delete();
            Cache::forget('catalogos.ordenes.clientes');
            return redirect(route('clientes.index'))->with('status', 'Se ha eliminado correctamente el cliente')->with('status_type', 'warning');
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('status', $ex->getMessage())->with('status_type', 'error-Query');
        } catch (\Exception $e) {
            return back()->with('status', $e->getMessage())->with('status_type', 'error');
        }
    }

    private function paginateClients($clientes, int $limit, Request $request): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage('page');

        return new LengthAwarePaginator(
            $clientes->slice(($page - 1) * $limit, $limit)->values(),
            $clientes->count(),
            $limit,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );
    }
}
