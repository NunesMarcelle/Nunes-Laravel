<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesService;
use App\Models\Customer;
use App\Models\Service;

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
