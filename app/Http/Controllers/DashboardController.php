<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function index()
    {
        // Obtém o id da conta do usuário autenticado
        $id_conta = auth()->user()->id_conta;

        // Total de clientes ativos
        $total_clientes = \DB::table('customers')
            ->where('id_conta', $id_conta)
            ->where('status', 'active')
            ->count();

        // Total de produtos ativos
        $total_produtos = \DB::table('products')
            ->where('id_conta', $id_conta)
            ->where('status', 'active')
            ->count();

        // Contar os clientes por mês
        $clientsByMonth = \DB::table('customers')
        ->select(\DB::raw('MONTH(created_at) as month'), \DB::raw('YEAR(created_at) as year'), \DB::raw('COUNT(*) as total_clients'))
        ->where('id_conta', $id_conta)
        ->where('status', 'active')
        ->groupBy(\DB::raw('YEAR(created_at), MONTH(created_at)'))
        ->orderBy(\DB::raw('YEAR(created_at), MONTH(created_at)'))  // Corrigido: Remover a palavra 'ASC' redundante
        ->get();
        // Preparar os dados para o gráfico
        foreach ($clientsByMonth as $row) {
            $months[] = $row->month . '/' . $row->year; // Formato: Mês/Ano
            $clientCounts[] = $row->total_clients;
        }

        // Passa as variáveis para a view
        return view('dashboard.index', compact('total_clientes', 'total_produtos', 'months', 'clientCounts'));
    }
}
