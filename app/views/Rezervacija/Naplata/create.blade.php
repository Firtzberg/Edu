@extends('layouts.master')

@section('title')
Naplaćivanje
@endsection

@section('content')
<?php
$optional = array('class' => 'form-control');
$required = array(
    'class' => 'form-control',
    'required' => 'required');
$requiredPositive = array(
    'class' => 'form-control',
    'required' => 'required',
    'min' => 1);
?>
<?php
$rezervacija = $naplata->rezervacija;
$brojPolaznika = $rezervacija->klijenti->count();
$cjenovnik = $rezervacija->predmet->cjenovnik($rezervacija->mjera_id);
$ukupno_satnica = $cjenovnik->getUkupnaSatnica($brojPolaznika);
$poOsobi = $ukupno_satnica / $brojPolaznika;
?>
{{ HTML::script('js/Naplata/mjereManager.js') }}
<h2>Naplaćivanje</h2>
{{ Form::model($naplata, array('route' => array('Naplata.store', $rezervacija->id),
'method' => 'put',
'class' => 'form')) }}
<p>Predmet {{ $rezervacija->predmet->link() }}</p>
<div class = "row">
    <div class = "col-xs-12 col-sm-6">
        <div class="form-group">
            <h3>Broj osoba <span class = "personCount">{{ $brojPolaznika }}</span></h3>
            <div class="checkbox"><label>{{ Form::checkbox('polaznicichanged', 'yes') }} Nisu svi došli</label></div>
            <div id = "klijenti-container" class = "container" <?php if (!Input::old('polaznicichanged')) echo 'hidden = "hidden"' ?>>
                {{ Form::label('Izostali polaznici') }}
                @foreach($rezervacija->klijenti as $klijent)
                <div class = "checkbox"><label>{{ Form::checkbox('klijent-came-'.$klijent->broj_mobitela, 'came') }} {{ $klijent->ime }}</label></div>
                @endforeach
            </div>
            <p>Cijena po osobi za <span class = "mjera_ime_display">{{ $rezervacija->mjera->znacenje }}</span>: <strong id = "perPersonPerUnit">{{ $poOsobi }}</strong></p>
        </div>
    </div>
    <div class = "col-xs-12 col-sm-6">
        <h3>Ukupno odrađeno <span class = "kolicina_display">{{ $rezervacija->kolicina }}</span> <span class = "mjera_ime_display">{{ $rezervacija->mjera->znacenje }}</span></h3>
        <div class="checkbox"><label>{{ Form::checkbox('mjerechanged', 'yes') }} Drugi iznos</label></div>
        <div id = "mjere-container" class = "form-group" <?php if (!Input::old('mjerechanged')) echo 'hidden = "hidden"' ?>>
            {{ Form::label('Trajanje') }}
            <div class="row">
                <div class="col-xs-6">
                    {{ Form::input('number', 'stvarna_kolicina', null,
				$requiredPositive) }}</div>
                <div class="col-xs-6">
                    {{ Form::select('stvarna_mjera', \Mjera::all()->lists('simbol', 'id'), $rezervacija->mjera_id,
				$required) }}</div>
            </div>
            <script type="text/javascript">
                mjereManager.begin(<?php
$cjenovnici = Cjenovnik::whereIn('id', $rezervacija->predmet->c_m_p->map(function($mjera) {
                    return $mjera->pivot->cjenovnik_id;
                })->toArray())->get();
                
echo json_encode(
        $rezervacija->predmet->c_m_p->map(function($mjera) use ($cjenovnici) {
            $cjenovnik = $cjenovnici->first(function($index, $cjenovnik) use ($mjera) {
                return $cjenovnik->id == $mjera->pivot->cjenovnik_id;
            });
            return array(
                'id' => $mjera->id,
                'ime' => $mjera->znacenje,
                'cijena_1_osoba' => $cjenovnik->cijena_1_osoba,
                'cijena_2_osobe' => $cjenovnik->cijena_2_osobe,
                'cijena_3_osobe' => $cjenovnik->cijena_3_osobe,
                'cijena_4_osobe' => $cjenovnik->cijena_4_osobe,
                'cijena_vise_osoba' => $cjenovnik->cijena_vise_osoba);
        }
        )->toArray()
);
?>);
            </script>
        </div>
        <p>Ukupno po osobi <strong class = "perPersonDisplay">{{ $poOsobi*$rezervacija->kolicina }}</strong></p>
    </div>
</div>
<p>Ukupno za platiti <strong class = "total_display">{{ $poOsobi*$rezervacija->kolicina*$brojPolaznika }}</strong></p>
<div class = "form-group">
    {{ Form::label('Napomena') }}
    {{ Form::textarea('napomena', null, array('class' => 'form-control', 'rows' => '2', 'style' => 'max-width:400px')) }}
</div>
<div class = "form-group">
    {{ Form::submit('Naplati', array(
		'class' => 'btn btn-lg btn-primary btn-block',
	'style' => 'margin:auto;max-width:330px')) }}
</div>
{{ Form::close() }}
@endsection