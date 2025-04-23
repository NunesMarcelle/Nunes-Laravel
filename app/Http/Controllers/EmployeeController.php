<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone_number' => 'nullable|string|max:15',
            'status' => 'nullable|required',
            'employee_position' => 'nullable|required',
        ]);

        Employee::create([
            'id_conta' => Auth::user()->id_conta, // Pega o id_conta do usuário logado
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'status' => $request->status,
            'employee_position'=> $request->employee_position,
        ]);

        return redirect()->route('employees.index')->with('success', 'Funcionário criado com sucesso');
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        return view('employees.show', compact('employee'));
    }
}
