<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $id_conta = auth()->user()->id_conta;

        $schedules = DB::table('schedules')
            ->select('id', 'id_conta', 'title', 'description', 'start', 'end')
            ->where('id_conta', $id_conta)
            ->get();

        $total_clientes = DB::table('customers')
            ->where('id_conta', $id_conta)
            ->where('status', 'active')
            ->count();

        $total_produtos = DB::table('products')
            ->where('id_conta', $id_conta)
            ->where('status', 'active')
            ->count();

        $clientsByMonth = DB::table('customers')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'), DB::raw('COUNT(*) as total_clients'))
            ->where('id_conta', $id_conta)
            ->where('status', 'active')
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get();

            $total_vendas_mes = DB::table('sales_product')
            ->where('id_conta', $id_conta)
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->count();

            $months = [];
            $clientCounts = [];

            foreach ($clientsByMonth as $row) {
                $months[] = $row->month . '/' . $row->year;
                $clientCounts[] = $row->total_clients;
            }

        return view('dashboard.index', compact('total_clientes', 'total_produtos', 'months', 'clientCounts', 'schedules', 'total_vendas_mes'));
    }
}
