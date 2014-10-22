@extends('layouts.master')

@section('title')
@if(isset($rezervacija))
Uređivanje rezervacije
@else
Rezerviranje
@endif
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
'min' => 1)
?>
<h2 class="form-heading">Rezerviranje</h2>
@if(isset($rezervacija))
{{ Form::model($rezervacija, array('route' => array('Rezervacija.update', $rezervacija->id),
'method' => 'put')) }}
@else
{{ Form::open(array('route' => 'Rezervacija.store')) }}
@endif
{{ HTML::script('js/errorManager.js') }}
<?php $value = null; ?>
<div class = "row">
	<div class = "col-xs-12 col-sm-5 col-lg-7 row">
		<div class = "col-xs-12 col-lg-6">
			<div class = "form-group">
				<?php
					if(isset($rezervacija))
						$value = date('Y-m-d', strtotime($rezervacija->pocetak_rada));
					else $value = date('Y-m-d');
				?>
				{{ Form::label('Datum') }}
				{{ Form::input('date', 'datum', $value,
				array(
				'class' =>'form-control',
				'required' => 'required',
				'min' => date('Y-m-d'))) }}
			</div>
			<div class = "form-group">
				{{ Form::label('Vrijeme početka') }}
				<div class="row">
					<div class="col-xs-6">
					<?php
						if(isset($rezervacija))
							$value = date('H', strtotime($rezervacija->pocetak_rada));
						else $value = 8;
					?>
					{{ Form::selectRange('startHour', BaseController::START_HOUR, BaseController::END_HOUR, $value,
					$required) }}</div>
					<div class="col-xs-6">
					<?php
						if(isset($rezervacija))
							$value = date('i', strtotime($rezervacija->pocetak_rada));
						else $value = 0;
					?>
					{{ Form::select('startMinute', array(0 => '00', 30 => '30'), $value,
					$required) }}</div>
				</div>
			</div>
			<div class = "form-group">
				<?php
					$rows = Mjera::select('id', 'simbol')->get();
					$mjere = array();
					foreach ($rows as $row) {
						$mjere[$row->id] = $row->simbol;
					}
				?>
				{{ Form::label('Trajanje') }}
				<div class="row">
					<div class="col-xs-6">
					<?php
						if(isset($rezervacija))
							$value = $rezervacija->kolicina;
						else $value = 1;
					?>
					{{ Form::input('number', 'kolicina', $value,
					$requiredPositive) }}</div>
					<div class="col-xs-6">
					{{ Form::select('mjera_id', $mjere, null,
					$required) }}</div>
				</div>
			</div>
		</div>
		<div class = "col-xs-12 col-lg-6">
			@yield('predmet-select')
		</div>
	</div>
	<div class = "col-xs-12 col-sm-7 col-lg-5">
		@yield('klijent-input')
		<div class = "form-group">
		{{ Form::label('Učionica') }}
		{{ Form::select('ucionica_id', $ucionice, null,
		$required) }}
		</div>
	</div>
</div>
<div class = "form-group">
	<?php
	if(isset($rezervacija))
		$value = 'Promijeni';
	else $value = 'Rezerviraj';
	?>
	{{ Form::submit($value, array(
	'class' => 'btn btn-lg btn-primary btn-block',
	'style' => 'margin:auto;max-width:330px')) }}
</div>
{{ Form::close() }}
@endsection