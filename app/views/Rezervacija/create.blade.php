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
{{ Form::open(array('route' => 'Rezervacija.store')) }}
<div class = "row">
	<div class = "col-xs-12 col-sm-6 col-md-8 row">
		<div class = "col-xs-12 col-md-6">
			<div class = "form-group">
				{{ Form::label('Datum') }}
				{{ Form::input('date', 'datum', date('Y-m-d'),
				array(
				'class' =>'form-control',
				'required' => 'required',
				'min' => date('Y-m-d'))) }}
			</div>
			<div class = "form-group">
				{{ Form::label('Vrijeme početka') }}
				<div class="row">
					<div class="col-xs-6">
					{{ Form::selectRange('startHour', BaseController::START_HOUR, BaseController::END_HOUR, null,
					$required) }}</div>
					<div class="col-xs-6">
					{{ Form::select('startMinute', array(0 => '00', 30 => '30'), null,
					$required) }}</div>
				</div>
			</div>
			<div class = "form-group">
				{{ Form::label('Trajanje') }}
				<div class="row">
					<div class="col-xs-6">
					{{ Form::input('number', 'kolicina', 1,
					$requiredPositive) }}</div>
					<div class="col-xs-6">
					{{ Form::select('mjera', $mjere, null,
					$required) }}</div>
				</div>
			</div>
		</div>
		<div class = "col-xs-12 col-md-6">
			@yield('predmet-select')
		</div>
	</div>
	<div class = "col-xs-12 col-sm-6 col-md-4">
		@yield('klijent-input')
		<div class = "form-group">
		{{ Form::label('Učionica') }}
		{{ Form::select('ucionica', $ucionice, null,
		$required) }}
		</div>
	</div>
</div>
<div class = "form-group">
	{{ Form::submit('Rezerviraj', array(
	'class' => 'btn btn-lg btn-primary btn-block',
	'style' => 'margin:auto;max-width:330px')) }}
</div>
{{ Form::close() }}