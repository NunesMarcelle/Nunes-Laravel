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
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'total_price' => 'required|numeric',
        ]);

        $idConta = auth()->user()->id_conta;

        // Buscar o produto
        $product = Product::find($request->product_id);

        if ($product->amount < $request->quantity) {
            return redirect()->back()->withErrors(['quantity' => 'Quantidade em estoque insuficiente.']);
        }

        // Registrar a venda
        $saleProduct = new SalesProduct();
        $saleProduct->customer_id = $request->customer_id;
        $saleProduct->product_id = $request->product_id;
        $saleProduct->quantity = $request->quantity;
        $saleProduct->unit_price = $request->unit_price;
        $saleProduct->discount = $request->discount;
        $saleProduct->total_price = $request->total_price;
        $saleProduct->id_conta = $idConta;
        $saleProduct->save();

        $product->amount -= $request->quantity;
        $product->save();

        return redirect()->route('sales_product.index')->with('success', 'Venda registrada com sucesso!');
    }


    public function destroy($id)
{
    $saleProduct = SalesProduct::findOrFail($id);
    $saleProduct->delete();
    return redirect()->route('sales_product.index')->with('success', 'Produto da venda excluído com sucesso!');
}


}
