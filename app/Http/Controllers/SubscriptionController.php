<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Importe a classe Log

class SubscriptionController extends Controller
{
    public function index()
    {
        $id_conta = Auth::id();
        $clientes = Customer::where('id_conta', $id_conta)->get();
        $subscriptions = Subscription::where('id_conta', $id_conta)->get();
        return view('subscriptions.index', compact('subscriptions', 'clientes'));
    }

    public function create()
    {
        return view('subscriptions.create');
    }

    public function store(Request $request)
    {
        $id_conta = Auth::user()->id_conta;

        // Adicionando log para verificar os dados recebidos
        Log::info('Dados recebidos para criar assinatura:', [
            'id_conta' => $id_conta,
            'customer_id' => $request->customer_id,
            'billing_type' => $request->billing_type,
            'next_due_date' => $request->next_due_date,
            'value' => $request->value,
            'cycle' => $request->cycle,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        $request->validate([
            'customer_id' => 'required|string',
            'billing_type' => 'required',
            'next_due_date' => 'required|date',
            'value' => 'required|numeric',
            'cycle' => 'required',
            'description' => 'required|string',
        ]);

        Subscription::create([
            'id_conta' => Auth::id(),
            'customer_id' => $request->customer_id,
            'billing_type' => $request->billing_type,
            'next_due_date' => $request->next_due_date,
            'value' => $request->value,
            'cycle' => $request->cycle,
            'description' => $request->description,
            'status' => $request->status, // valor padrão
        ]);

        return redirect()->route('subscriptions.index')->with('success', 'Assinatura criada com sucesso.');
    }

    public function update(Request $request, $id)
{
    // Validação dos dados recebidos
    $request->validate([
        'customer_id' => 'required|exists:customers,id',  // Verifica se o cliente existe
        'billing_type' => 'required|in:BOLETO,CREDIT_CARD,PIX',
        'next_due_date' => 'required|date',
        'value' => 'required|numeric',
        'cycle' => 'required|in:WEEKLY,MONTHLY,BIMONTHLY,QUARTERLY,SEMIANNUALLY,YEARLY',
        'description' => 'required|string',
    ]);

    // Encontra a assinatura a ser atualizada
    $subscription = Subscription::findOrFail($id);  // Se não encontrar, lança um erro 404

    // Atualiza os dados da assinatura
    $subscription->update([
        'customer_id' => $request->customer_id,
        'billing_type' => $request->billing_type,
        'next_due_date' => $request->next_due_date,
        'value' => $request->value,
        'cycle' => $request->cycle,
        'description' => $request->description,
        'status' => $request->status, // Se o status for passado como input
    ]);

    // Redireciona para a página de visualização com uma mensagem de sucesso
    return redirect()->route('subscriptions.index')->with('success', 'Assinatura atualizada com sucesso.');
}


}
