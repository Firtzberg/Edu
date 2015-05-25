<?php

class IzvjestajController extends \BaseController {

    public function __construct() {
        $this->beforeFilter(function() {
            if (!(Auth::check() && Auth::user()->hasPermission(Permission::PERMISSION_SEE_GLOBAL_IZVJESTAJ))) {
                return Redirect::to('logout');
            }
        }, array('only' => array('ukupni_tjedni_izvjestaj', 'ukupni_godisnji_izvjestaj')));

        $this->beforeFilter(function($route) {
            if (!(Auth::check() && (Auth::id() == $route->getParameter('id') || Auth::user()->hasPermission(Permission::PERMISSION_SEE_FOREIGN_IZVJESTAJ)))) {
                return Redirect::to('logout');
            }
        }, array('only' => array('tjedni_izvjestaj', 'godisnji_izvjestaj')));
    }

    public function tjedni_izvjestaj($id, $tjedan = null, $godina = null) {
        $djelatnik = User::find($id);
        if (!$djelatnik) {
            Session::flash(self::DANGER_MESSAGE_KEY, User::NOT_FOUND_MESSAGE);
            return Redirect::route('home');
        }
        return $this->t_izvjestaj($id, $tjedan, $godina)
                        ->with('instruktor', $djelatnik);
    }

    public function ukupni_tjedni_izvjestaj($tjedan = null, $godina = null) {
        return $this->t_izvjestaj(null, $tjedan, $godina);
    }

    private function t_izvjestaj($id, $tjedan, $godina) {
        if (is_null($tjedan))
            $tjedan = date('W');
        if (is_null($godina))
            $godina = date('o');
        $dto = new DateTime();
        $dto = $dto->setISODate($godina, $tjedan);
        $tjedan = $dto->format('W');
        $godina = $dto->format('o');

        $primanja = Naplata::select(DB::raw('weekday(pocetak_rada)+1 as dan, ' .
                                'COALESCE(sum(stvarna_kolicina*trajanje),0) as minuta, ' .
                                'COALESCE(sum(za_instruktora),0) as za_instruktora, ' .
                                'COALESCE(sum(za_tvrtku),0) as za_tvrtku, ' .
                                'COALESCE(sum(ukupno_uplaceno),0) as ukupno_uplaceno'))
                ->leftJoin('rezervacije', 'naplate.rezervacija_id', '=', 'rezervacije.id')
                ->join('mjere', 'mjere.id', '=', 'naplate.stvarna_mjera');
        if (!is_null($id))
            $primanja = $primanja->where('rezervacije.instruktor_id', '=', $id);
        $primanja = $primanja->where(DB::raw('yearweek(pocetak_rada, 3)'), '=', $godina . $tjedan)
                ->groupBy(DB::raw('weekday(pocetak_rada)'))
                ->get();
        $dani = array(1 => 'Ponedjeljak', 'Utorak', 'Srijeda', 'Četvrtak', 'Petak', 'Subota', 'Nedjelja');
        $di = new DateInterval('P1D');
        $di->invert = 1;
        $dto->add($di);
        $di->invert = 0;
        foreach ($dani as $key => $d) {
            $zarada[$key] = array(
                'datum' => $dto->add($di)->format('d. m.'),
                'dan' => $d,
                'sati' => 0,
                'za_instruktora' => 0,
                'za_tvrtku' => 0,
                'ukupno_uplaceno' => 0);
        }

        $minuta = 0;
        $za_instruktora = 0;
        $za_tvrtku = 0;
        $ukupno_uplaceno = 0;

        foreach ($primanja as $p) {
            $zarada[$p['dan']]['sati'] = $p['minuta'] / 60;
            $minuta += $p['minuta'];
            $zarada[$p['dan']]['za_instruktora'] = $p['za_instruktora'];
            $za_instruktora += $p['za_instruktora'];
            $zarada[$p['dan']]['za_tvrtku'] = $p['za_tvrtku'];
            $za_tvrtku += $p['za_tvrtku'];
            $zarada[$p['dan']]['ukupno_uplaceno'] = $p['ukupno_uplaceno'];
            $ukupno_uplaceno += $p['ukupno_uplaceno'];
        }

        $zarada[] = array(
            'datum' => null,
            'dan' => 'Ukupno',
            'sati' => $minuta / 60,
            'za_instruktora' => $za_instruktora,
            'za_tvrtku' => $za_tvrtku,
            'ukupno_uplaceno' => $ukupno_uplaceno);

        return View::make('Izvjestaj.tjedni_izvjestaj')
                        ->with('tjedan', $tjedan)
                        ->with('godina', $godina)
                        ->with('zarada', $zarada);
    }

    public function ukupni_godisnji_izvjestaj($godina = null) {
        return $this->g_izvjestaj(null, $godina);
    }

    public function godisnji_izvjestaj($id, $godina = null) {
        $djelatnik = User::find($id);
        if (!$djelatnik) {
            Session::flash(self::DANGER_MESSAGE_KEY, User::NOT_FOUND_MESSAGE);
            return Redirect::route('home');
        }
        return $this->g_izvjestaj($id, $godina)
                        ->with('instruktor', $djelatnik);
    }

    private function g_izvjestaj($id, $godina) {
        if (is_null($godina))
            $godina = date('Y');

        $primanja = Naplata::select(DB::raw('month(pocetak_rada) as mjesec, ' .
                                'COALESCE(sum(stvarna_kolicina*trajanje),0) as minuta, ' .
                                'COALESCE(sum(za_instruktora),0) as za_instruktora, ' .
                                'COALESCE(sum(za_tvrtku),0) as za_tvrtku, ' .
                                'COALESCE(sum(ukupno_uplaceno),0) as ukupno_uplaceno'))
                ->leftJoin('rezervacije', 'naplate.rezervacija_id', '=', 'rezervacije.id')
                ->join('mjere', 'mjere.id', '=', 'naplate.stvarna_mjera');
        if (!is_null($id))
            $primanja = $primanja->where('rezervacije.instruktor_id', '=', $id);
        $primanja = $primanja->where(DB::raw('year(pocetak_rada)'), '=', $godina)
                ->groupBy(DB::raw('month(pocetak_rada)'))
                ->get();

        $mjeseci = array(1 => 'Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj',
            'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac');
        foreach ($mjeseci as $m) {
            $zarada[$m] = array(
                'sati' => 0,
                'za_instruktora' => 0,
                'za_tvrtku' => 0,
                'ukupno_uplaceno' => 0);
        }
        $minuta = 0;
        $za_instruktora = 0;
        $za_tvrtku = 0;
        $ukupno_uplaceno = 0;
        foreach ($primanja as $p) {
            $zarada[$mjeseci[$p['mjesec']]]['sati'] = $p['minuta'] / 60;
            $minuta += $p['minuta'];
            $zarada[$mjeseci[$p['mjesec']]]['za_instruktora'] = $p['za_instruktora'];
            $za_instruktora += $p['za_instruktora'];
            $zarada[$mjeseci[$p['mjesec']]]['za_tvrtku'] = $p['za_tvrtku'];
            $za_tvrtku += $p['za_tvrtku'];
            $zarada[$mjeseci[$p['mjesec']]]['ukupno_uplaceno'] = $p['ukupno_uplaceno'];
            $ukupno_uplaceno += $p['ukupno_uplaceno'];
        }
        $zarada['Ukupno'] = array(
            'sati' => $minuta / 60,
            'za_instruktora' => $za_instruktora,
            'za_tvrtku' => $za_tvrtku,
            'ukupno_uplaceno' => $ukupno_uplaceno);

        return View::make('Izvjestaj.godisnji_izvjestaj')
                        ->with('godina', $godina)
                        ->with('zarada', $zarada);
    }

}
