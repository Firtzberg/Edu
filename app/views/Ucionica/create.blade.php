<div class="container">
@if(isset($ucionica))
{{ Form::model($ucionica, array('action' => array('UcionicaController@update', $ucionica->id),
'method' => 'put',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
<h2 class="form-heading">Uređivanje učionice</h2>
@else
{{ Form::open(array('action' => 'UcionicaController@store',
'style' => 'max-width:330px;padding:15px;margin:auto'))}}
<h2 class="form-heading">Dodavanje učionice</h2>
@endif
<div class = "form-group">
{{ Form::label('naziv') }}
{{ Form::text('naziv', $value = null,
$attributes = array(
'class' =>'form-control',
'autofocus' => 'autofocus',
'required' => 'required')) }}
</div>
<div class = "form-group">
{{ Form::label('adresa') }}
{{ Form::text('adresa', $value = null,
$attributes = array(
'class' =>'form-control',
'required' => 'required')) }}
</div>
<div class = "form-group">
{{ Form::label('kat') }}
{{ Form::text('kat', $value = null,
$attributes = array(
'class' =>'form-control',
'required' => 'required')) }}
</div>
<div class = "form-group">
{{ Form::label('max_broj_ucenika') }}
{{ Form::selectRange('max_broj_ucenika',1,50) }}
</div>
<div class = "form-group">
{{ Form::label('opis') }}
{{ Form::textarea('opis', $value = null,
$attributes = array(
'class' =>'form-control',
'required' => 'required')) }}
</div>
<div class = "form-group">
	@if(Session::has('poruka'))
	<div class="alert alert-warning">
		{{ Session::get('poruka') }}
	</div>
	@endif
{{ Form::submit('Pohrani', $attributes = array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
</div>
{{ Form::close() }}
</div>