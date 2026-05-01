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


class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $limit = max(1, (int) $request->input('limit', 10));
        $sortBy = in_array($request->input('sort_by'), ['id_cliente', 'nombreCompleto'], true)
            ? $request->input('sort_by')
            : 'id_cliente';
        $sortOrder = $request->input('sort_order') === 'desc' ? 'desc' : 'asc';

        if ($search !== '' || $sortBy === 'nombreCompleto') {
            $clientes = Cliente::query()
                ->get(['id_cliente', 'nombreCompleto', 'telefono', 'correo', 'rfc'])
                ->when($search !== '', function ($clientes) use ($search) {
                    return $clientes->filter(fn (Cliente $cliente) => ClientDataSecurity::matchesSearch([
                        $cliente->nombreCompleto,
                        $cliente->telefono,
                        $cliente->correo,
                        $cliente->rfc,
                    ], $search));
                });

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

        // Enviar los datos a la vista
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge($this->normalizedClientInput($request));

        $validated = $request->validate(
            $this->clientValidationRules(),
            $this->clientValidationMessages()
        );


        try {
            DB::beginTransaction();

            Cliente::create($validated);
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


    public function update(Request $request, $id_cliente)
    {
        $cliente = Cliente::findOrFail($id_cliente);
        $request->merge($this->normalizedClientInput($request));

        $validated = $request->validate(
            $this->clientValidationRules((int) $id_cliente),
            $this->clientValidationMessages()
        );

        try {
            $cliente->fill($validated)->save();
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

        // Eliminar el cliente
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

    private function normalizedClientInput(Request $request): array
    {
        return [
            'nombreCompleto' => ClientDataSecurity::prepareForStorage('nombreCompleto', $request->input('nombreCompleto')),
            'telefono' => ClientDataSecurity::prepareForStorage('telefono', $request->input('telefono')),
            'correo' => ClientDataSecurity::prepareForStorage('correo', $request->input('correo')),
            'rfc' => ClientDataSecurity::prepareForStorage('rfc', $request->input('rfc')),
        ];
    }

    private function clientValidationRules(?int $ignoreId = null): array
    {
        return [
            'nombreCompleto' => ['required', 'string', 'max:100', $this->uniqueClientValueRule('nombreCompleto', 'Este nombre ya existe, por favor ingresa uno nuevo.', $ignoreId)],
            'telefono' => ['required', 'digits:10', $this->uniqueClientValueRule('telefono', 'Este teléfono ya está registrado.', $ignoreId)],
            'correo' => ['required', 'email', 'max:30', $this->uniqueClientValueRule('correo', 'Este correo ya está registrado.', $ignoreId)],
            'rfc' => ['required', 'string', 'min:12', 'max:13', $this->uniqueClientValueRule('rfc', 'Este RFC ya está registrado.', $ignoreId)],
        ];
    }

    private function clientValidationMessages(): array
    {
        return [
            'nombreCompleto.required' => 'El campo Nombre es requerido.',
            'telefono.required' => 'El campo Teléfono es requerido.',
            'telefono.digits' => 'El teléfono debe tener 10 dígitos.',
            'correo.required' => 'El campo Correo electrónico es requerido.',
            'correo.email' => 'El correo electrónico no tiene un formato válido.',
            'rfc.required' => 'El rfc es requerido.',
            'rfc.min' => 'El RFC debe tener al menos 12 caracteres.',
            'rfc.max' => 'El RFC no puede exceder 13 caracteres.',
        ];
    }

    private function uniqueClientValueRule(string $field, string $message, ?int $ignoreId = null): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($field, $message, $ignoreId): void {
            if (Cliente::existsWithSensitiveValue($field, (string) $value, $ignoreId)) {
                $fail($message);
            }
        };
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
