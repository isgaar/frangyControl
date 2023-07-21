<?php

namespace App\Http\Controllers;

use App\Models\Ordenes;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $limit = $request->input('limit', 5);
        $order = $request->input('order', 'desc');

        $query = Ordenes::query()->with('cliente');

        if (trim($search) != '') {
            $query->where(function ($query) use ($search) {
                $query->where('id_ordenes', 'like', "%$search%")
                    ->orWhereHas('cliente', function ($query) use ($search) {
                        $query->where('nombreCompleto', 'like', "%$search%");
                    })
                    ->orWhereHas('vehiculo', function ($query) use ($search) {
                        $query->where('marca', 'like', "%$search%");
                    })
                    ->orWhere('placas', 'like', "%$search%")
                    ->orWhereHas('servicio', function ($query) use ($search) {
                        $query->where('nombreServicio', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    });
            });
        }



        $ordenes = $query->orderBy('id_ordenes', $order)->paginate($limit);

        if ($ordenes->isEmpty()) {
            $message = "No hay registros de \"$search\"";
            return view('home', [
                'ordenes' => $ordenes,
                'search' => $search,
                'limit' => $limit,
                'order' => $order,
            ]);

        } else {
            return view('home', [
                'ordenes' => $ordenes,
                'search' => $search,
                'limit' => $limit,
                'order' => $order,
            ]);

        }
    }
    public function about(Request $request)
    {
        return view('acerca');
    }
}
