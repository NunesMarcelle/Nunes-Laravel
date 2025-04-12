<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        $id_conta = auth()->user()->id_conta;

        $total_clientes = \DB::table('customers')
            ->where('id_conta', $id_conta)
            ->where('status', 'active')
            ->count();

        $total_produtos = \DB::table('products')
            ->where('id_conta', $id_conta)
            ->where('status', 'active')
            ->count();

        $clientsByMonth = \DB::table('customers')
        ->select(\DB::raw('MONTH(created_at) as month'), \DB::raw('YEAR(created_at) as year'), \DB::raw('COUNT(*) as total_clients'))
        ->where('id_conta', $id_conta)
        ->where('status', 'active')
        ->groupBy(\DB::raw('YEAR(created_at), MONTH(created_at)'))
        ->orderBy(\DB::raw('YEAR(created_at), MONTH(created_at)'))
        ->get();

        foreach ($clientsByMonth as $row) {
            $months[] = $row->month . '/' . $row->year;
            $clientCounts[] = $row->total_clients;
        }

        return view('dashboard.index', compact('total_clientes', 'total_produtos', 'months', 'clientCounts'));
    }
}
