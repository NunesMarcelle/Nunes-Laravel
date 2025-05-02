<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesService;
use App\Models\Customer;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class SalesServiceController extends Controller
{
    public function index()
    {
        $salesServices = SalesService::where('id_conta', auth()->user()->id_conta)->get();
        $customers = Customer::where('id_conta', auth()->user()->id_conta)
        ->where('status', 'active')
        ->get();
        $services = Service::where('id_conta', auth()->user()->id_conta)->get();

        return view('sales_service.index', compact('salesServices', 'customers', 'services'));
    }

    public function store(Request $request)
    {
        // Validação dos dados do formulário
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'customer_id' => 'required|exists:customers,id',
            'price' => 'required',
            'discount' => 'required',
            'total_price' => 'required',
        ]);

        SalesService::create([
            'service_id' => $request->service_id,
            'customer_id' => $request->customer_id,
            'price' => $request->price,
            'discount' => $request->discount,
            'total_price' => $request->total_price,
            'id_conta' => auth()->user()->id_conta,
        ]);

        return redirect()->route('sales_service.index')->with('success', 'Serviço adicionado com sucesso!');
    }

    public function markAsPaid($id)
{
    $service = SalesService::findOrFail($id);
    $service->status = 'completed';
    $service->save();

    return redirect()->back()->with('success', 'Pagamento recebido com sucesso!');
}

public function generateBoleto($id)
{
    try {
        $sale = SalesService::findOrFail($id);
        $customer = $sale->customer;

        if (!$customer || !$customer->asaas_id) {
            return redirect()->back()->with('error', 'Cliente não possui cadastro válido no Asaas.');
        }

        $data = [
            'customer'    => $customer->asaas_id,
            'value'       => $sale->total_price,
            'dueDate'     => now()->addDays(7)->format('Y-m-d'),
            'billingType' => 'BOLETO',
            'description' => 'Pagamento do serviço ID: ' . $sale->id,
        ];

        $asaasAccessToken = env('ASAAS_ACCESS_TOKEN', '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OjM5MDllNjU2LWY0YmUtNDhmZi1hODcwLTViMjM5MDM3ZTc3Mjo6JGFhY2hfZTdmMDRlODItZjVhNi00MmJjLWJlOWMtMGZmNTVlOWY1MTM1');

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'access_token' => $asaasAccessToken,
            'Content-Type' => 'application/json',
        ])->post('https://api-sandbox.asaas.com/v3/payments', $data);

        Log::info('Resposta da API do Asaas ao gerar boleto (Serviço): ' . $response->body());

        if ($response->successful()) {
            $payment = $response->json();

            if (isset($payment['id'])) {
                return redirect($payment['bankSlipUrl']);
            } else {
                $errorDetails = $payment['errors'][0]['description'] ?? 'Erro desconhecido.';
                return redirect()->back()->with('error', "Erro ao gerar boleto: {$errorDetails}");
            }
        } else {
            $statusCode = $response->status();
            $errorDetails = $response->json();
            Log::error("Erro ao gerar boleto no Asaas (Serviço - Status: {$statusCode}): " . json_encode($errorDetails));

            $errorDescription = $errorDetails['errors'][0]['description'] ?? 'Erro desconhecido.';
            return redirect()->back()->with('error', "Erro ao gerar boleto no Asaas: {$errorDescription}");
        }
    } catch (\Exception $e) {
        Log::error('Exceção ao gerar boleto no Asaas (Serviço): ' . $e->getMessage());
        return redirect()->back()->with('error', 'Erro interno ao gerar boleto.');
    }
}


    public function update(Request $request, $id)
{
    $request->validate([
        'service_id' => 'required|exists:services,id',
        'customer_id' => 'required|exists:customers,id',
    ]);

    $salesService = SalesService::where('id_conta', auth()->user()->id_conta)->findOrFail($id);

    $salesService->service_id = $request->service_id;
    $salesService->customer_id = $request->customer_id;
    $salesService->save();

    return redirect()->route('sales_service.index')->with('success', 'Serviço de venda atualizado com sucesso!');
}

    public function destroy($id)
{
    try {
        $salesService = SalesService::findOrFail($id);
        $salesService->delete();

        return redirect()->back()->with('success', 'Serviço excluído com sucesso.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Erro ao excluir o serviço');
    }
}

}
