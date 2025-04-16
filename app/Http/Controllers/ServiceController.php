<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Barryvdh\DomPDF\Facade as PDF;
use Dompdf\Options;


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

    public function gerarRelatorioPDF()
    {
        $services = Service::where('id_conta', Auth::user()->id_conta)->get();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        $html = view('services.relatorio', compact('services'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('relatorio-servicos.pdf', ['Attachment' => false]);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'status' => 'required|in:active,inactive',
        ]);

        $service = Service::findOrFail($id);
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->status = $request->status;
        $service->save();

        return redirect()->back()->with('success', 'Serviço atualizado com sucesso!');
    }


    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('services.index')->with('success', 'Serviço deletado com sucesso.');
    }
}
