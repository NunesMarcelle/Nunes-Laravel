<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;


class ScheduleController extends Controller
{
    public function index()
    {
        $events = Schedule::all();
        return view('schedules.index', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $start = \Carbon\Carbon::parse($request->start)->format('Y-m-d H:i:s');
        $end = \Carbon\Carbon::parse($request->end)->format('Y-m-d H:i:s');

        $schedule = new Schedule();
        $schedule->start = $start;
        $schedule->end = $end;
        $schedule->title = $request->title;
        $schedule->description = $request->description;
        $schedule->id_conta = auth()->user()->id_conta;
        $schedule->save();

        return redirect()->route('schedules.index')->with('success', 'Agendamento criado com sucesso!');
    }



    public function getEvents()
    {
        $events = Schedule::all();
        return response()->json($events);
    }
}
