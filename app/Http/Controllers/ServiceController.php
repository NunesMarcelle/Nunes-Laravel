<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
{
    $services = Service::where('id_conta', auth()->user()->id_conta)->get();
    return view('services.index', compact('services'));
}
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'status' => 'required',
        'description' => 'required',
    ]);

    $service = new Service();
    $service->name = $request->name;
    $service->price = $request->price;
    $service->status = $request->status;
    $service->description = $request->description;
    $service->id_conta = auth()->user()->id_conta;
    $service->save();

    return redirect()->route('services.index')->with('success', 'Serviço criado com sucesso.');
}

    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'id_conta' => 'required|integer',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'status' => 'required|boolean',
        ]);

        $service->update($request->all());

        return redirect()->route('services.index')->with('success', 'Serviço atualizado com sucesso.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Serviço deletado com sucesso.');
    }
}
