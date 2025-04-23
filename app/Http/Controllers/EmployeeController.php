<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Barryvdh\DomPDF\Facade as PDF;
use Dompdf\Options;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    public function gerarRelatorioPDF()
    {
        $employees = Employee::where('id_conta', Auth::user()->id_conta)->get();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('employees.relatorio', compact('employees'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('relatorio-funcionarios.pdf', ['Attachment' => false]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'         => 'required|email|unique:employees,email',
            'access_level'  => 'required|in:admin,funcionario',
            'phone'         => 'nullable|numeric',
            'position'      => 'required|string|max:100',
            'salary'        => 'required|numeric',
            'status'        => 'required|in:active,inactive',
        ]);

        Employee::create([
            'id_conta'      => Auth::user()->id_conta,
            'name'    => $request->name,
            'email'         => $request->email,
            'access_level'  => $request->access_level,
            'phone'         => $request->phone,
            'position'      => $request->position,
            'salary'        => $request->salary,
            'status'        => $request->status,
        ]);

        return redirect()->route('employees.index')->with('success', 'Funcionário criado com sucesso!');
    }



    public function update(Request $request, $id)
{
    $employee = Employee::findOrFail($id);
    $employee->update($request->all());
    return redirect()->route('employees.index')->with('success', 'Funcionário atualizado com sucesso!');
}

    public function destroy($id)
{
    $employee = Employee::findOrFail($id);
    $employee->delete();
    return redirect()->route('employees.index')->with('success', 'Funcionário excluído com sucesso!');
}
}
