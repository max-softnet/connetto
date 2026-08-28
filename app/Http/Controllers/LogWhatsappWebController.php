<?php

namespace App\Http\Controllers;

use App\Models\LogWhatsapp;

class LogWhatsappWebController extends Controller
{
    public function index()
    {
        $log = LogWhatsapp::with('messaggio')
            ->orderByDesc('created_at')
            ->get();

        return view('log-whatsapp.index', compact('log'));
    }
}
