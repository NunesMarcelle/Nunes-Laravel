<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

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
        $request->validate([
            'customer_id' => 'required|string',
            'billing_type' => 'required|in:BOLETO,CREDIT_CARD,PIX',
            'next_due_date' => 'required|date',
            'value' => 'required|numeric',
            'cycle' => 'required|in:WEEKLY,MONTHLY,BIMONTHLY,QUARTERLY,SEMIANNUALLY,YEARLY',
            'description' => 'required|string',
        ]);

        $asaasResponse = Http::withToken(env('ASAAS_ACCESS_TOKEN'))
            ->post('https://www.asaas.com/api/v3/subscriptions', [
                'customer' => $request->customer_id,
                'billingType' => $request->billing_type,
                'nextDueDate' => $request->next_due_date,
                'value' => $request->value,
                'cycle' => $request->cycle,
                'description' => $request->description,
            ]);

        if ($asaasResponse->successful()) {
            $data = $asaasResponse->json();

            Subscription::create([
                'user_id' => Auth::id(),
                'asaas_subscription_id' => $data['id'],
                'customer_id' => $request->customer_id,
                'billing_type' => $request->billing_type,
                'next_due_date' => $request->next_due_date,
                'value' => $request->value,
                'cycle' => $request->cycle,
                'description' => $request->description,
                'status' => $data['status'] ?? 'ACTIVE',
            ]);

            return redirect()->route('subscriptions.index')->with('success', 'Assinatura criada com sucesso.');
        }

        return back()->withErrors(['error' => 'Erro ao criar assinatura no Asaas.']);
    }
}
