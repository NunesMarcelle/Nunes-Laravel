<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Barryvdh\DomPDF\Facade as PDF;
use Dompdf\Options;

class CustomerController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar autenticado.');
        }

        $customers = Customer::where('id_conta', Auth::user()->id_conta)->get();
        return view('customers.index', compact('customers'));
    }

    public function gerarRelatorioPDF()
    {
        $clientes = Customer::where('id_conta', Auth::user()->id_conta)->get();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('customers.relatorio', compact('clientes'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('relatorio-clientes.pdf', ['Attachment' => false]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);

        Customer::create([
            'id_conta' => Auth::user()->id_conta,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'status' => $request->status,
        ]);

        return redirect()->route('customers.index')->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }


    public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'string|max:255',
        'email' => 'email',
        'phone' => 'nullable|string|max:20',
        'birth_date' => 'nullable|date',
        'status' => 'in:active,inactive',
    ]);

    $customer = Customer::findOrFail($id); // <-- Carrega o cliente

    $customer->update($request->all());

    return redirect()->route('customers.index')->with('success', 'Cliente atualizado com sucesso!');
}

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Cliente removido com sucesso!');
    }
}
