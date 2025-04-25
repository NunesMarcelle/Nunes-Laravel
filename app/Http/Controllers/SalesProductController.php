<?php

namespace App\Http\Controllers;

use App\Models\SalesProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;



class SalesProductController extends Controller
{
    public function index()
    {
        $idConta = auth()->user()->id_conta;

        $sales = SalesProduct::where('id_conta', $idConta)->get();

        $products = DB::table('products')
                    ->where('id_conta', $idConta)
                    ->where('status', 'active')
        ->get();

        $customers = DB::table('customers')
                    ->where('id_conta', $idConta)
                    ->where('status','active')
                    ->get();

        return view('sales_product.index', compact('sales', 'products', 'customers'));
    }

        public function markAsPaid($id)
    {
        $sale = SalesProduct::findOrFail($id);
        $sale->status = 'completed';
        $sale->save();

        return redirect()->back()->with('success', 'Pagamento recebido com sucesso!');
    }


    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->quantity > $product->stock) {
            return redirect()->back()->with('error', 'Quantidade em estoque insuficiente.');
        }

        $unit_price = $product->price;
        $total_price = ($unit_price * $request->quantity) - $request->discount;

        // Cadastra a venda
        Sale::create([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'unit_price' => $unit_price,
            'total_price' => $total_price,
            'status' => 'pending',
        ]);

        // Atualiza o estoque
        $product->stock -= $request->quantity;
        $product->save();

        return redirect()->route('sales.index')->with('success', 'Venda registrada com sucesso!');
    }

    public function destroy($id)
{
    $saleProduct = SalesProduct::findOrFail($id);
    $saleProduct->delete();
    return redirect()->route('sales_product.index')->with('success', 'Produto da venda excluído com sucesso!');
}


}
