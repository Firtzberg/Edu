@extends('layouts.master')

@section('title')
Naplaćivanje
@endsection

@section('content')
<?php
$optional = array('class' =>'form-control');
$required = array(
'class' =>'form-control',
'required' => 'required');
$requiredPositive = array(
'class' =>'form-control',
'required' => 'required',
'min' => 1);
?>
<?php
$rezervacija = $naplata->rezervacija;
$brojPolaznika = $rezervacija->klijenti->count();
$cijena = $rezervacija->predmet->cijene->filter(
	function($cijena) use ($rezervacija){return $cijena->id == $rezervacija->mjera_id;})->first();
$poOsobi = $cijena->pivot->individualno - ($brojPolaznika-1)*$cijena->pivot->popust;
if($poOsobi < $cijena->pivot->minimalno)
 $poOsobi = $cijena->pivot->minimalno;
?>
{{ HTML::script('js/Naplata/mjereManager.js') }}
<h2>Naplaćivanje</h2>
{{ Form::model($naplata, array('route' => array('Naplata.store', $rezervacija->id),
'method' => 'put',
'class' => 'form')) }}
	<p>Predmet {{ $rezervacija->predmet->ime }}</p>
	<div class = "row">
	<div class = "col-xs-12 col-sm-6">
		<h3>Broj osoba <span class = "personCount">{{ $brojPolaznika }}</span></h3>
		<p>{{ Form::checkbox('polaznicichanged', 'yes') }} Nisu svi došli</p>
		<div id = "klijenti-container" class = "container" <?php if(!Input::old('polaznicichanged')) echo 'hidden = "hidden"'?>>
			{{ Form::label('Izostali polaznici') }}
			@foreach($rezervacija->klijenti as $klijent)
			<div class = "">
				{{ Form::checkbox('klijent-came-'.$klijent->broj_mobitela, 'came') }} {{ $klijent->ime }}
			</div>
			@endforeach
		</div>
		<p>Cijena po osobi za <span class = "mjera_ime_display">{{ $rezervacija->mjera->znacenje }}</span>: <strong id = "perPersonPerUnit">{{ $poOsobi }}</strong></p>
	</div>
	<div class = "col-xs-12 col-sm-6">
		<h3>Ukupno odrađeno <span class = "kolicina_display">{{ $rezervacija->kolicina }}</span> <span class = "mjera_ime_display">{{ $rezervacija->mjera->znacenje }}</span></h3>
		<p>{{ Form::checkbox('mjerechanged', 'yes') }} Drugi iznos</p>
		<div id = "mjere-container" class = "form-group" <?php if(!Input::old('mjerechanged')) echo 'hidden = "hidden"'?>>
			<?php
				$mjere = array();
				foreach ($rezervacija->predmet->cijene as $mjera) {
					$mjere[$mjera->id] = $mjera->simbol;
				}
			?>
			{{ Form::label('Trajanje') }}
			<div class="row">
				<div class="col-xs-6">
				{{ Form::input('number', 'stvarna_kolicina', null,
				$requiredPositive) }}</div>
				<div class="col-xs-6">
				{{ Form::select('stvarna_mjera', $mjere, null,
				$required) }}</div>
			</div>
			<script type="text/javascript">
			mjereManager.begin(<?php
				echo json_encode(
					$rezervacija->predmet->cijene->map(function($cijena){
						return array(
							'id' => $cijena->id,
							'ime' => $cijena->znacenje,
							'individualno' => $cijena->pivot->individualno,
							'popust' => $cijena->pivot->popust,
							'minimalno' => $cijena->pivot->minimalno);
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