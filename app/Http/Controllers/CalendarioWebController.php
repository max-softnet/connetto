<?php

namespace App\Http\Controllers;

use App\Models\Appuntamento;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarioWebController extends Controller
{
    public function giorno(Request $request)
    {
        $data = $this->parseData($request->query('data'));

        $appuntamenti = $this->scopaPerRuolo(
            Appuntamento::with(['tipoAppuntamento', 'operatoreAppuntamento'])
        )
            ->whereDate('data', $data->toDateString())
            ->orderBy('ora_inizio')
            ->get();

        return view('calendario.giorno', [
            'data' => $data,
            'appuntamenti' => $appuntamenti,
        ]);
    }

    public function elenco(Request $request)
    {
        $data = $this->parseData($request->query('data'));

        $appuntamenti = $this->scopaPerRuolo(
            Appuntamento::with(['tipoAppuntamento', 'operatoreAppuntamento'])
        )
            ->whereDate('data', $data->toDateString())
            ->orderBy('ora_inizio')
            ->get();

        return view('calendario.elenco', [
            'data' => $data,
            'appuntamenti' => $appuntamenti,
        ]);
    }

    public function mese(Request $request)
    {
        $mese = $this->parseMese($request->query('mese'));

        $inizioMese = $mese->copy()->startOfMonth();
        $fineMese = $mese->copy()->endOfMonth();
        $inizioGriglia = $inizioMese->copy()->startOfWeek(Carbon::MONDAY);
        $fineGriglia = $fineMese->copy()->endOfWeek(Carbon::SUNDAY);

        $appuntamentiPerGiorno = $this->scopaPerRuolo(
            Appuntamento::with(['tipoAppuntamento', 'operatoreAppuntamento'])
        )
            ->whereBetween('data', [$inizioGriglia->toDateString(), $fineGriglia->toDateString()])
            ->orderBy('ora_inizio')
            ->get()
            ->groupBy(fn (Appuntamento $appuntamento) => $appuntamento->data->format('Y-m-d'));

        $settimane = [];
        $cursore = $inizioGriglia->copy();

        while ($cursore->lte($fineGriglia)) {
            $settimana = [];

            for ($i = 0; $i < 7; $i++) {
                $settimana[] = $cursore->copy();
                $cursore->addDay();
            }

            $settimane[] = $settimana;
        }

        return view('calendario.mese', [
            'mese' => $mese,
            'settimane' => $settimane,
            'appuntamentiPerGiorno' => $appuntamentiPerGiorno,
        ]);
    }

    private function scopaPerRuolo(Builder $query): Builder
    {
        $utente = auth()->user();

        if ($utente->isOperatore()) {
            if ($utente->operatore_id) {
                $query->where('operatore', $utente->operatore->nome);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($utente->isPaziente()) {
            if ($utente->filemaker_persona_id) {
                $query->where('filemaker_persona_id', $utente->filemaker_persona_id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }

    private function parseData(?string $data): Carbon
    {
        if ($data) {
            try {
                return Carbon::createFromFormat('Y-m-d', $data)->startOfDay();
            } catch (\Exception) {
                //
            }
        }

        return Carbon::today();
    }

    private function parseMese(?string $mese): Carbon
    {
        if ($mese) {
            try {
                return Carbon::createFromFormat('Y-m', $mese)->startOfMonth();
            } catch (\Exception) {
                //
            }
        }

        return Carbon::now()->startOfMonth();
    }
}
