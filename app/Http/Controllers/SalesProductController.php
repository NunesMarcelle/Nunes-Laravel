<?php

namespace App\Http\Controllers;

use App\Models\SalesProduct;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
    // Buscar a venda pela ID
    $sale = SalesProduct::findOrFail($id);

    // Buscar o cliente correspondente à venda
    $customer = Customer::findOrFail($sale->customer_id);  // Assume que 'customer_id' é o relacionamento com o cliente

    // Verificar se o cliente tem o asaas_id
    if (!$customer->asaas_id) {
        return redirect()->back()->with('error', 'Cliente não possui cadastro no Asaas.');
    }

    // Preparar os dados para o boleto
    $payload = [
        "customer" => $customer->asaas_id,  // Pega o asaas_id do cliente no banco
        "billingType" => "BOLETO",
        "value" => $sale->total_price,
        "dueDate" => now()->addDays(7)->toDateString(),
        "description" => "Pagamento referente à venda #{$sale->id}",
    ];

    // Enviar a solicitação para a API do Asaas
    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . env('ASAAS_SANDBOX_API_KEY'),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->post('https://sandbox.asaas.com/api/v3/payments', $payload);

    if ($response->successful()) {
        $payment = $response->json();

        // Salvar as informações do boleto gerado
        $sale->asaas_payment_id = $payment['id'];
        $sale->save();

        return redirect()->back()->with('success', 'Boleto gerado com sucesso!');
    }

    // Se houver erro, exibir mensagem
    $error = $response->json();
    $message = $error['errors'][0]['description'] ?? 'Erro desconhecido ao gerar boleto.';

    return redirect()->back()->with('error', 'Erro ao gerar boleto: ' . $message);
}


    public function destroy($id)
    {
        $saleProduct = SalesProduct::findOrFail($id);
        $saleProduct->delete();

        return redirect()->route('sales_product.index')->with('success', 'Venda excluída com sucesso!');
    }
}
