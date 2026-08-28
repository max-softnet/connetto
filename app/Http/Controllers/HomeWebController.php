<?php

namespace App\Http\Controllers;

use App\Models\Appuntamento;
use App\Models\Messaggio;
use App\Models\ModelloMessaggio;
use Illuminate\Support\Carbon;

class HomeWebController extends Controller
{
    public function index()
    {
        if (! auth()->user()->isAdmin()) {
            return redirect()->route('calendario.giorno');
        }

        $appuntamentiOggi = Appuntamento::whereDate('data', Carbon::today())->count();
        $modelliMessaggio = ModelloMessaggio::count();
        $messaggiInBozza = Messaggio::where('stato', 'bozza')->count();

        return view('home', compact('appuntamentiOggi', 'modelliMessaggio', 'messaggiInBozza'));
    }
}
