<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;
use JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCredentialsMail;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $limit = in_array((int) $request->input('limit', 5), [5, 10, 15], true)
            ? (int) $request->input('limit', 5)
            : 5;
        $order = $request->input('order') === 'desc' ? 'desc' : 'asc';

        $data = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name', $order)
            ->paginate($limit)
            ->withQueryString();

        if ($data->isEmpty()) {
            $message = "No hay registros de \"$search\"";
            return view('admin.empleados.index', compact('data', 'search', 'limit', 'order', 'message'));
        } else {
            return view('admin.empleados.index', compact('data', 'search', 'limit', 'order'));
        }
    }

    public function create()
    {
        $roles = Cache::remember('catalogos.roles', now()->addMinutes(10), function () {
            return Role::query()->orderBy('name')->get(['id', 'name']);
        });

        return view('admin.empleados.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:40'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['required', 'exists:roles,id'],
        ], [
            'name.required' => 'Escribe el nombre completo del usuario.',
            'name.max' => 'El nombre no puede tener más de 40 caracteres.',
            'email.required' => 'Escribe un correo electrónico.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.unique' => 'Ese correo ya está registrado.',
            'password.required' => 'Escribe una contraseña.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'roles.required' => 'Selecciona un rol para el usuario.',
            'roles.exists' => 'El rol seleccionado no es válido.',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => trim($validated['name']),
                'email' => strtolower(trim($validated['email'])),
                'password' => Hash::make($validated['password']),
            ]);

            $user->roles()->sync([$validated['roles']]);

            // Enviar credenciales por correo electrónico
            try {
                Mail::to($user->email)->send(new UserCredentialsMail($user, $validated['password']));
            } catch (\Exception $e) {
                // Si falla el correo, no revertimos la transacción, pero podemos registrar el error o notificar
                \Log::error("No se pudo enviar el correo de credenciales a {$user->email}: " . $e->getMessage());
            }

            $token = JWTAuth::fromUser($user);

            DB::commit();
            Cache::forget('catalogos.ordenes.users');

            Session::flash('status', "Se ha agregado correctamente el usuario");
            Session::flash('status_type', 'success');

            return redirect(route('usuarios.index'))->with('token', $token);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            Session::flash('status', $ex->getMessage());
            Session::flash('status_type', 'error-Query');
            return back()->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Session::flash('status', $e->getMessage());
            Session::flash('status_type', 'error');
            return back()->withInput();
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
            
            // Verificar si el usuario actual es el dueño del perfil
            $isOwner = Auth::id() == $id;

            if ($valida != null && $valida->id == $id) {
                if ($isOwner && $request['password'] != null) {
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
                if ($isOwner && $request['password'] != null) {
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
            Cache::forget('catalogos.ordenes.users');
            Session::flash('status', "Se ha editado correctamente el registro");
            Session::flash('status_type', 'success');
            return redirect(route('usuarios.index'));
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
            Cache::forget('catalogos.ordenes.users');
            Session::flash('status', "Se ha eliminado correctamente el registro");
            Session::flash('status_type', 'success');
            return redirect(route('usuarios.index'));
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
