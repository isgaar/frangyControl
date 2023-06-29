<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Orden;

class OrdenController extends Controller
{
    public function index(Request $request)
    {
        $search = "";
        $limit = 5;
        
        if ($request->has('search')) {
            $search = $request->input('search');
        
            if (trim($search) != '') {
                $data = Orden::where('placas', 'like', "%$search%")
                    ->orWhereHas('cliente', function ($query) use ($search) {
                        $query->where('nombreCompleto', 'like', "%$search%");
                    })->orWhereHas('servicio', function ($query) use ($search) {
                        $query->where('nombreServicio', 'like', "%$search%");
                    })->get();
            } else {
                $data = Orden::all();
            }
        } else {
            $data = Orden::all();
        }
        
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() - 1;
        $perPage = $limit;
        $currentPageSearchResults = $data->slice($currentPage * $perPage, $perPage)->all();
        $data = new \Illuminate\Pagination\LengthAwarePaginator($currentPageSearchResults, count($data), $perPage);
        

        if ($data->isEmpty()) {
            $message = "No hay registros de \"$search\"";
            return view('admin.ordenes.index', ['data' => $data, 'search' => $search, 'page' => $currentPage, 'message' => $message]);
        } else {
            return view('admin.ordenes.index', ['data' => $data, 'search' => $search, 'page' => $currentPage]);
        }
    }
    public function create()
    {
        return view('admin.ordenes.create');
    }

}
