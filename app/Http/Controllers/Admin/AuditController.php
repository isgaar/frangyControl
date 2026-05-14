<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $limit = in_array((int) $request->input('limit', 15), [15, 30, 50], true)
            ? (int) $request->input('limit', 15)
            : 15;

        $data = UserActivity::with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('action', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('ip_address', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($limit)
            ->withQueryString();

        if ($data->isEmpty() && $search !== '') {
            $message = "No se encontraron registros en la bitácora para \"$search\"";
            return view('admin.auditoria.index', compact('data', 'search', 'limit', 'message'));
        }

        return view('admin.auditoria.index', compact('data', 'search', 'limit'));
    }
}
