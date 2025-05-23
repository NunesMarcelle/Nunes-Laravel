<?php

namespace App\Http\Controllers;

use App\Models\SalesProduct;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SalesProductController extends Controller
{
    public function index()
    {
        $idConta = auth()->user()->id_conta;

        $sales_products = SalesProduct::where('id_conta', $idConta)->get();

        $products = DB::table('products')
            ->where('id_conta', $idConta)
            ->where('status', 'active')
            ->get();

        $customers = DB::table('customers')
            ->where('id_conta', $idConta)
            ->where('status', 'active')
            ->get();

        return view('sales_product.index', compact('sales_products', 'products', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->quantity > $product->amount) {
            return redirect()->back()->with('error', 'Quantidade em estoque insuficiente.');
        }

        $unit_price = $product->price;
        $total_price = ($unit_price * $request->quantity) - ($request->discount ?? 0);

        // Cadastra a venda
        SalesProduct::create([
            'id_conta' => Auth::user()->id_conta,
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'unit_price' => $unit_price,
            'total_price' => $total_price,
            'status' => 'pending',
        ]);

        // Atualiza o estoque
        $product->amount -= $request->quantity;
        $product->save();

        return redirect()->route('sales_product.index')->with('success', 'Venda registrada com sucesso!');
    }

    public function markAsPaid($id)
    {
        $sale = SalesProduct::findOrFail($id);
        $sale->status = 'completed';
        $sale->save();

        return redirect()->back()->with('success', 'Pagamento recebido com sucesso!');
    }
    public function generateBoleto($id)
    {
        try {
            $sale = SalesProduct::findOrFail($id);
            $customer = $sale->customer;

            if (!$customer || !$customer->asaas_id) {
                return redirect()->back()->with('error', 'Cliente não possui cadastro válido no Asaas.');
            }

            $data = [
                'customer'    => $customer->asaas_id,
                'value'       => $sale->total_price,
                'dueDate'     => now()->addDays(7)->format('Y-m-d'),
                'billingType' => 'BOLETO',
                'description' => 'Pagamento da venda ID: ' . $sale->id,
            ];

            $asaasAccessToken = env('ASAAS_ACCESS_TOKEN', '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OjM5MDllNjU2LWY0YmUtNDhmZi1hODcwLTViMjM5MDM3ZTc3Mjo6JGFhY2hfZTdmMDRlODItZjVhNi00MmJjLWJlOWMtMGZmNTVlOWY1MTM1');

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'access_token' => $asaasAccessToken,
                'Content-Type' => 'application/json',
            ])->post('https://api-sandbox.asaas.com/v3/payments', $data);

            Log::info('Resposta da API do Asaas ao gerar boleto: ' . $response->body());

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
                Log::error("Erro ao gerar boleto no Asaas (Status: {$statusCode}): " . json_encode($errorDetails));

                $errorDescription = $errorDetails['errors'][0]['description'] ?? 'Erro desconhecido.';
                return redirect()->back()->with('error', "Erro ao gerar boleto no Asaas: {$errorDescription}");
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao gerar boleto no Asaas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro interno ao gerar boleto.');
        }
    }

    public function destroy($id)
    {
        $saleProduct = SalesProduct::findOrFail($id);
        $saleProduct->delete();

        return redirect()->route('sales_product.index')->with('success', 'Venda excluída com sucesso!');
    }
}
