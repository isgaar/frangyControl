<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivity;
use Exception;

class DatabaseController extends Controller
{
    public function index()
    {
        $connectionName = config('database.default');
        $dbDriver = config("database.connections.{$connectionName}.driver", 'Desconocido');

        return view('admin.respaldos.importar', compact('dbDriver'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'sql_file' => ['required', 'file', 'mimetypes:text/plain,application/sql', 'max:51200'], // max 50MB
        ], [
            'sql_file.required' => 'Por favor, selecciona un archivo SQL.',
            'sql_file.file' => 'El archivo subido no es válido.',
            'sql_file.mimetypes' => 'Solo se permiten archivos con extensión .sql',
            'sql_file.max' => 'El archivo no puede superar los 50MB.',
        ]);

        $file = $request->file('sql_file');

        if ($file->getClientOriginalExtension() !== 'sql') {
            Session::flash('status', 'Solo se permiten archivos con extensión .sql');
            Session::flash('status_type', 'error');
            return back();
        }

        $sqlContent = file_get_contents($file->getRealPath());

        try {
            DB::beginTransaction();

            // Disable foreign key checks for safer import (MySQL specific)
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            }

            // Ejecución limpia del archivo
            DB::unprepared($sqlContent);

            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            DB::commit();

            // Registrar en auditoría
            UserActivity::create([
                'user_id' => Auth::id(),
                'action' => 'Importación de Base de Datos',
                'description' => 'Se ha importado exitosamente el archivo SQL: ' . $file->getClientOriginalName(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Session::flash('status', 'Base de datos importada correctamente. Ningún dato fue corrompido.');
            Session::flash('status_type', 'success');
            return back();

        } catch (Exception $e) {
            DB::rollBack();

            // Attempt to re-enable foreign keys if we disabled them
            if (isset($driver) && $driver === 'mysql') {
                try {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                } catch (Exception $ignore) {}
            }

            // Registrar el intento fallido
            UserActivity::create([
                'user_id' => Auth::id(),
                'action' => 'Fallo al Importar BD',
                'description' => 'Error al intentar importar ' . $file->getClientOriginalName() . '. La transacción fue revertida.',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Session::flash('status', 'Error al importar la base de datos. Se han revertido los cambios para evitar pérdidas. Detalle: ' . $e->getMessage());
            Session::flash('status_type', 'error');
            return back();
        }
    }
}
