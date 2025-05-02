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


            $total_produtos = DB::table('sales_product')
            ->where('id_conta', $id_conta)
            ->where('status', 'completed')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('total_price');


            $total_servicos = DB::table('sales_service')
            ->where('id_conta', $id_conta)
            ->where('status', 'completed')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->sum('total_price');

            $total_mes = $total_produtos + $total_servicos;

            $months = [];
            $clientCounts = [];

            foreach ($clientsByMonth as $row) {
                $months[] = $row->month . '/' . $row->year;
                $clientCounts[] = $row->total_clients;
            }

        return view('dashboard.index', compact('total_clientes', 'total_mes', 'total_servicos', 'total_produtos', 'months', 'clientCounts', 'schedules'));
    }
}
