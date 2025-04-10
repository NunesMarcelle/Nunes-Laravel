<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('product.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'price'       => 'required|numeric',
            'amount'      => 'required|integer',
            'min_amount'  => 'required|integer',
            'status'      => 'required|string|in:active,inactive',
        ]);

        Product::create([
            'id_conta' => Auth::user()->id_conta,
            'name' => $request->name,
            'price' => $request->price,
            'amount' => $request->amount,
            'min_amount' => $request->min_amount,
            'status' => $request->status,
        ]);




        return redirect()->route('product.index')->with('success', 'Produto criado com sucesso');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'string|max:100',
            'price'       => 'numeric',
            'amount'      => 'integer',
            'min_amount'  => 'integer',
            'status'      => 'string|in:active,inactive',
        ]);

        $product->update($request->all());

        return redirect()->route('product.index')->with('success', 'Produto editado com sucesso');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('product.index')->with('success', 'Produto deletado com sucesso');
    }
}
