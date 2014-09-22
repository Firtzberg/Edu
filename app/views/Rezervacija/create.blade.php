<div class="container">
{{ Form::open(array('action' => 'RezervacijaController@store',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
<h2 class="form-heading">Rezerviranje</h2>
<div class = "form-group">
{{ Form::label('Datum') }}
{{ Form::input('date', 'datum', date('Y-m-d'),
$attributes = array(
'class' =>'form-control',
'required' => 'required')) }}
<div class = "form-group">
{{ Form::label('Vrijeme početka') }}
<div class="container-fluid">
<div class="row">
<div class="col-xs-4">
{{ Form::selectRange('startHour', 8, 22, null,
array('class' => 'form-control')) }}</div>
<div class="col-xs-4">
{{ Form::select('startMinute', array(0 => '00', 15 => '15', 30 => '30', 45 =>'45'), null,
array('class' => 'form-control')) }}</div>
</div>
</div>
</div>
<div class = "form-group">
{{ Form::label('Vrijeme završetka') }}
<div class="container-fluid">
<div class="row">
<div class="col-xs-4">
{{ Form::selectRange('endHour', 8, 22, null,
array('class' => 'form-control')) }}</div>
<div class="col-xs-4">
{{ Form::select('endMinute', array(0 => '00', 15 => '15', 30 => '30', 45 =>'45'), null,
array('class' => 'form-control')) }}</div>
</div>
</div>
</div>
<div class = "form-group">
{{ Form::label('Učionica') }}
{{ Form::select('ucionica', $ucionice, null,
$attributes = array(
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
$attributes = array(
'class' =>'form-control')) }}
</div>
<div class = "form-group">
{{ Form::label('Broj učenika') }}
{{ Form::input('number', 'broj_ucenika', $value = null,
$attributes = array(
'class' =>'form-control',
'required' => 'required')) }}
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