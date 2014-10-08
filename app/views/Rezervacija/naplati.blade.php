<?php
$optional = array('class' =>'form-control');
$required = array(
'class' =>'form-control',
'required' => 'required');
?>
<h2>Naplaćivanje</h2>
<div class="row">
<div class="col-sm-6">
@if(isset($naplata))
{{ Form::model($naplata, array('route' => array('Rezervacija.naplata', $rezervacija->id),
'method' => 'put',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
@else
{{ Form::open(array('route' => array('Rezervacija.naplata', $rezervacija->id),
'method' => 'put',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
@endif
<div class = "form-group">
{{ Form::label('Ukupan iznos') }}
{{ Form::input('number', 'ukupno_uplaceno', null,
$required) }}
</div>
<div class = "form-group">
{{ Form::label('Za instruktora') }}
{{ Form::input('number', 'za_instruktora', null,
$required) }}
</div>
<div class = "form-group">
{{ Form::label('Za tvrtku') }}
{{ Form::input('number', 'za_tvrtku', null,
$required) }}
{{ Form::hidden('rezervacija_id', $rezervacija->id) }}
</div>
<div class = "form-group">
{{ Form::submit('Naplati', array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
</div>
{{ Form::close() }}
</div>
<div class="col-sm-6">
<dl class="dl-horizontal">
<dt>Instruktor</dt><dd>{{ link_to_route('Instruktor.show', $rezervacija->instruktor->name, array('id' => $rezervacija->instruktor->id)) }}</dd>
<dt>Vrijeme početka</dt><dd>{{ $rezervacija->pocetak_rada }}</dd>
<dt>Vrijeme završetka</dt><dd>{{ $rezervacija->kraj_rada() }}</dd>
<dt>Učionica</dt><dd>{{ link_to_route('Ucionica.show',
	$rezervacija->ucionica->naziv.', '.$rezervacija->ucionica->adresa,
	array($rezervacija->ucionica->id)) }}</dd>
<dt>Usmjerenje</dt><dd>
@if(empty($rezervacija->usmjerenje))
Nije definirano
@else
{{ $rezervacija->usmjerenje }}
@endif
</dd>
<dt>Predmet</dt><dd>
@if(empty($rezervacija->predmet))
Nije definirano
@else
{{ $rezervacija->predmet }}
@endif
</dd>
<dt>Broj učenika</dt><dd>{{ $rezervacija->broj_ucenika }}</dd>
</dl>
</div>
</div>