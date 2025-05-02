<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
        $employees = Employee::where('id_conta', Auth::user()->id_conta)->get();
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
            'cpf'         => 'nullable|required|max:11',
            'phone'         => 'nullable|numeric',
            'position'      => 'required|string|max:100',
            'status'        => 'required|in:active,inactive',
        ]);

        Employee::create([
            'id_conta'      => Auth::user()->id_conta,
            'name'    => $request->name,
            'cpf'         => $request->cpf,
            'phone'         => $request->phone,
            'position'      => $request->position,
            'status'        => $request->status,
        ]);

        return redirect()->route('employees.index')->with('success', 'Funcionário criado com sucesso!');
    }

    public function createAccess(Request $request, $id)
{
    $employee = Employee::findOrFail($id);

    $user = new User();
    $user->name = $employee->name;
    $user->company_name = auth()->user()->company_name;
    $user->id_conta = auth()->user()->id_conta;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);

    if ($request->hasFile('img')) {
        // Salva a imagem na pasta "public/users" e armazena o caminho relativo
        $imagePath = $request->file('img')->store('users', 'public');
        // Armazena no banco apenas o caminho relativo dentro de "storage"
        $user->img = $imagePath;
    }

    $user->save();

    return redirect()->back()->with('success', 'Acesso criado com sucesso!');
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
