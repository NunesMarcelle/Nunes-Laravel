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

    return view('dashboard.index', compact('total_clientes', 'total_produtos'));
}
}
