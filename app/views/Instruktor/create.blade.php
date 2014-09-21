<div class="container">
@if(isset($instruktor))
{{ Form::model($instruktor, array('action' => array('InstruktorController@update', $instruktor->id),
'method' => 'put',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
<h2 class="form-heading">Uređivaje instruktora</h2>
@else
{{ Form::open(array('action' => 'InstruktorController@store',
'style' => 'max-width:330px;padding:15px;margin:auto'))}}
<h2 class="form-heading">Dodavanje instruktora</h2>
@endif
<div class = "form-group">
{{ Form::label('Ime') }}
@if(Auth::user()->is_admin)
{{ Form::text('name', $value = null,
$attributes = array(
'class' =>'form-control',
'required' => 'required')) }}
@else
<br>{{ Form::label($instruktor->name)}}
@endif
</div>
@if(!isset($instruktor))
<div class = "form-group">
{{ Form::label('Zaporka') }}
{{ Form::password('lozinka',
$attributes = array(
'class' =>'form-control',
'required' => 'required')) }}
{{ Form::label('Ponovljena zaporka') }}
{{ Form::password('ponovljena',
$attributes = array(
'class' =>'form-control',
'required' => 'required')) }}
</div>
@endif
<div class = "form-group">
{{ Form::label('broj_mobitela') }}
{{ Form::text('broj_mobitela', $value = null,
$attributes = array(
'class' =>'form-control')) }}
</div>
<div class = "form-group">
{{ Form::label('Email') }}
{{ Form::email('email', $value = null,
$attributes = array(
'class' =>'form-control')) }}
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