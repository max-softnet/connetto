<?php

namespace App\Http\Controllers;

use App\Models\LogAutomazione;

class LogAutomazioneWebController extends Controller
{
    public function index()
    {
        $log = LogAutomazione::with('automazione')
            ->orderByDesc('eseguita_at')
            ->get();

        return view('log-automazioni.index', compact('log'));
    }
}
