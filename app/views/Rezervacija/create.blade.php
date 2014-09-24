<div class="container">
{{ Form::open(array('action' => 'RezervacijaController@store',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
<h2 class="form-heading">Rezerviranje</h2>
<div class = "form-group">
{{ Form::label('Datum') }}
{{ Form::input('date', 'datum', date('Y-m-d'),
$attributes = array(
'class' =>'form-control',
'required' => 'required',
'min' => date('Y-m-d'))) }}
<div class = "form-group">
{{ Form::label('Vrijeme početka') }}
<div class="container-fluid">
<div class="row">
<div class="col-xs-6">
{{ Form::selectRange('startHour', 8, 22, null,
array('class' => 'form-control')) }}</div>
<div class="col-xs-6">
{{ Form::select('startMinute', array(0 => '00', 30 => '30'), null,
array('class' => 'form-control')) }}</div>
</div>
</div>
</div>
<div class = "form-group">
{{ Form::label('Trajanje') }}
<div class="container-fluid">
<div class="row">
<div class="col-xs-6">
{{ Form::input('number', 'kolicina', $value = 1,
$numAttributes = array(
'class' =>'form-control',
'required' => 'required',
'min' => 1)) }}</div>
<div class="col-xs-6">
{{ Form::select('mjera', $mjere, null,
$attributes = array(
'class' => 'form-control',
'required' => 'required')) }}</div>
</div>
</div>
</div>
<div class = "form-group">
{{ Form::label('Učionica') }}
{{ Form::select('ucionica', $ucionice, null,
$controlAttributes = array(
'class' =>'form-control',
'required' => 'required')) }}
</div>
<div class = "form-group">
{{ Form::label('Usmjerenje') }}
{{ Form::text('usmjerenje', $value = null,
$attributes = array(
'class' =>'form-control')) }}
</div>
<div class = "form-group">
{{ Form::label('Predmet') }}
{{ Form::text('predmet', $value = null,
$attributes) }}
</div>
<div class = "form-group">
{{ Form::label('Broj učenika') }}
{{ Form::input('number', 'broj_ucenika', $value = null,
$numAttributes) }}
</div>
<div class = "form-group">
@if(Session::has('poruka'))
<div class = "alert alert-warning">
{{ Session::get('poruka') }}
</div>
@endif
{{ Form::submit('Rezerviraj', $attributes = array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
</div>
{{ Form::close() }}
</div>