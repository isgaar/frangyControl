<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampanaPromocional;
use App\Models\TipoServicio;
use Illuminate\Http\Request;

class CampanaController extends Controller
{
    public function index(Request $request)
    {
        $campanas = CampanaPromocional::with('servicio')->orderBy('fecha_programada', 'desc')->paginate(10);
        return view('admin.campanas.index', compact('campanas'));
    }

    public function create()
    {
        $servicios = TipoServicio::all();
        return view('admin.campanas.create', compact('servicios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'mensaje' => 'required|string',
            'servicio_id' => 'nullable|exists:tipo_servicio,id_servicio',
            'fecha_programada' => 'required|date|after_or_equal:now',
        ]);

        CampanaPromocional::create($request->all());

        return redirect()->route('campanas.index')->with('success', 'Campaña programada exitosamente.');
    }

    public function edit(CampanaPromocional $campana)
    {
        $servicios = TipoServicio::all();
        return view('admin.campanas.edit', compact('campana', 'servicios'));
    }

    public function update(Request $request, CampanaPromocional $campana)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'mensaje' => 'required|string',
            'servicio_id' => 'nullable|exists:tipo_servicio,id_servicio',
            'fecha_programada' => 'required|date|after_or_equal:now',
        ]);

        $campana->update($request->all());

        return redirect()->route('campanas.index')->with('success', 'Campaña actualizada exitosamente.');
    }

    public function destroy(CampanaPromocional $campana)
    {
        $campana->delete();
        return redirect()->route('campanas.index')->with('success', 'Campaña eliminada exitosamente.');
    }
}
