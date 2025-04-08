<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Apenas usuários autenticados podem acessar

    public function index()
    {
        return view('dashboard.index');
    }
}
