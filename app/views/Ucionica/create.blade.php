<?php
$optional = array('class' =>'form-control');
$required = array(
'class' =>'form-control',
'required' => 'required');
?>
<div class="container">
@if(isset($ucionica))
{{ Form::model($ucionica, array('route' => array('Ucionica.update', $ucionica->id),
'method' => 'put',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
<h2 class="form-heading">Uređivanje učionice</h2>
@else
{{ Form::open(array('route' => 'Ucionica.store',
'style' => 'max-width:330px;padding:15px;margin:auto'))}}
<h2 class="form-heading">Dodavanje učionice</h2>
@endif
<div class = "form-group">
{{ Form::label('naziv') }}
{{ Form::text('naziv', null,
array(
'class' =>'form-control',
'autofocus' => 'autofocus',
'required' => 'required')) }}
</div>
<div class = "form-group">
{{ Form::label('adresa') }}
{{ Form::text('adresa', null,
$required) }}
</div>
<div class = "form-group">
{{ Form::label('kat') }}
{{ Form::text('kat', null,
$required) }}
</div>
<div class = "form-group">
{{ Form::label('max_broj_ucenika') }}
{{ Form::selectRange('max_broj_ucenika',1,50) }}
</div>
<div class = "form-group">
{{ Form::label('opis') }}
{{ Form::textarea('opis', null,
$required) }}
</div>
<div class = "form-group">
	@if(Session::has('poruka'))
	<div class="alert alert-warning">
		{{ Session::get('poruka') }}
	</div>
	@endif
{{ Form::submit('Pohrani', array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
</div>
{{ Form::close() }}
</div>