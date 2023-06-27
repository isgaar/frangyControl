<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = "";
        $limit = 5;
        if ($request->has('search')) {
            $search = $request->input('search');

            if (trim($search) != '') {
                $data = User::where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")->get();
            } else {
                $data = User::all();
            }
        } else {
            $data = User::all();
        }

        $currentPage = Paginator::resolveCurrentPage() - 1;
        $perPage = $limit;
        $currentPageSearchResults = $data->slice($currentPage * $perPage, $perPage)->all();
        $data = new LengthAwarePaginator($currentPageSearchResults, count($data), $perPage);

        if ($data->isEmpty()) {
            $message = "No hay registros de \"$search\"";
            return view('admin.empleados.index', ['data' => $data, 'search' => $search, 'page' => $currentPage, 'message' => $message]);
        } else {
            return view('admin.empleados.index', ['data' => $data, 'search' => $search, 'page' => $currentPage]);
        }
    }

    public function create()
{
    $roles = Role::all();
    return view('admin.empleados.create', compact('roles'));
}

    public function store(Request $request)
{
    try {
        DB::beginTransaction();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:40'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        if ($request->has('roles')) {
            $user->roles()->attach($request['roles']);
        }

        DB::commit();
        Session::flash('status', "Se ha agregado correctamente el usuario");
        Session::flash('status_type', 'success');
        return redirect(route('users.index'));
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
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.empleados.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $valida = User::where('email', '=', $request['email'])->first();
            if ($valida != null && $valida->id == $id) {
                if ($request['password'] != null) {
                    $data = [
                        'name' => $request['name'],
                        'email' => $request['email'],
                        'password' => Hash::make($request['password']),
                    ];
                } else {
                    $data = [
                        'name' => $request['name'],
                        'email' => $request['email'],
                    ];
                }
            } elseif ($valida == null) {
                if ($request['password'] != null) {
                    $data = [
                        'name' => $request['name'],
                        'email' => $request['email'],
                        'password' => Hash::make($request['password']),
                    ];
                } else {
                    $data = [
                        'name' => $request['name'],
                        'email' => $request['email'],
                    ];
                }
            } else {
                Session::flash('status', "Correo electrónico ya asignado");
                Session::flash('status_type', 'warning');
                return back();
            }

            $user = User::findOrFail($id);
            $user->update($data);

            // Actualizar roles
            $user->roles()->sync($request->input('roles'));

            DB::commit();
            Session::flash('status', "Se ha editado correctamente el registro");
            Session::flash('status_type', 'success');
            return redirect(route('users.index'));
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

    public function show($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.empleados.show', compact('user', 'roles'));
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        return view('admin.empleados.delete', ['user' => $user]);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->delete();
            DB::commit();
            Session::flash('status', "Se ha eliminado correctamente el registro");
            Session::flash('status_type', 'success');
            return redirect(route('users.index'));
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
