<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Rules\Cpf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Log;

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

        $dompdf = new \Dompdf\Dompdf($options);

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
        'cpf' => ['required', new Cpf],
    ]);

    $asaasAccessToken = env('ASAAS_ACCESS_TOKEN', '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OjM5MDllNjU2LWY0YmUtNDhmZi1hODcwLTViMjM5MDM3ZTc3Mjo6JGFhY2hfZTdmMDRlODItZjVhNi00MmJjLWJlOWMtMGZmNTVlOWY1MTM1');

    try {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'access_token' => $asaasAccessToken,
            'Content-Type' => 'application/json',
        ])->post('https://api-sandbox.asaas.com/v3/customers', [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone') ?? '',
            'birthDate' => $request->input('birth_date') ?? null,
            'status' => $request->input('status'),
            'cpf' => $request->input('cpf'),
        ]);

        if ($response->successful()) {
            $asaasId = $response->json('id');
            if ($asaasId) {
                $customer = new Customer([
                    'id_conta' => Auth::user()->id_conta,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'birth_date' => $request->birth_date,
                    'status' => $request->status,
                    'asaas_id' => $asaasId,
                    'cpf' => $request->cpf,
                ]);
                $customer->save();

                return redirect()->route('customers.index')->with('success', 'Cliente cadastrado com sucesso!');
            } else {
                Log::error('Erro: ID do cliente não encontrado na resposta do Asaas.');
                return redirect()->route('customers.index')->with('error', 'Erro ao obter ID do cliente do Asaas.');
            }
        } else {
            $statusCode = $response->status();
            $errorDetails = $response->json();
            Log::error("Erro ao criar cliente no Asaas (Status: {$statusCode}): " . json_encode($errorDetails));

            $errorDescription = $errorDetails['errors'][0]['description'] ?? 'Erro desconhecido.';
            return redirect()->route('customers.index')->with('error', "Erro ao criar cliente no Asaas: {$errorDescription}");
        }
    } catch (\Exception $e) {
        Log::error('Exceção ao criar cliente no Asaas: ' . $e->getMessage());
        return redirect()->route('customers.index')->with('error', 'Erro interno ao criar cliente.');
    }
}



    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name'       => 'string|max:255',
            'email'      => 'email',
            'phone'      => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'status'     => 'in:active,inactive',
        ]);

        $customer->update($request->only(['name', 'email', 'phone', 'birth_date', 'status']));

        return redirect()->route('customers.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Cliente removido com sucesso!');
    }
}
