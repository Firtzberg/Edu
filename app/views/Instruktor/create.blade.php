@extends('layouts.master')

@section('title')
@if(isset($instruktor))
{{ $instruktor->name }} - Uređivanje
@else
Dodavanje instruktora
@endif
@endsection

@section('content')
<?php
$optional = array('class' =>'form-control');
$required = array(
'class' =>'form-control',
'required' => 'required');
?>
@if(isset($instruktor))
{{ Form::model($instruktor, array('route' => array('Instruktor.update', $instruktor->id),
'method' => 'put',
'class' => 'form row')) }}
<h2 class="form-heading">Uređivaje instruktora</h2>
@else
{{ Form::open(array('route' => 'Instruktor.store',
'class' => 'form row'))}}
<h2 class="form-heading">Dodavanje instruktora</h2>
@endif
<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
<div class = "form-group">
{{ Form::label('Ime') }}
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_USER))
{{ Form::text('name', null,
array(
'class' =>'form-control',
'autofocus' => 'autofocus',
'required' => 'required',
'autocomplete' => 'off')) }}
@else
<br>{{ Form::label($instruktor->name)}}
@endif
</div>
<?php $color = '#ffffff';?>
@if(!isset($instruktor))
<div class = "form-group">
{{ Form::label('Zaporka') }}
{{ Form::password('lozinka',
$required) }}
{{ Form::label('Ponovljena zaporka') }}
{{ Form::password('ponovljena',
$required) }}
</div>
@else
<?php $color = '#'.$instruktor->boja;?>
@endif
<div class = "form-group">
{{ Form::label('Uloga') }}
{{ Form::select('role_id', Role::select('id', 'ime')->get()->lists('ime', 'id'),null,
$required) }}
</div>
<div class = "form-group">
{{ Form::label('Boja') }}
{{ Form::input('color', 'boja', $color,
$required) }}
</div>
<div class = "form-group">
{{ Form::label('broj_mobitela') }}
{{ Form::text('broj_mobitela', null,
$optional) }}
</div>
<div class = "form-group">
{{ Form::label('Email') }}
{{ Form::email('email', null,
$optional) }}
</div>
<div class = "form-group">
{{ Form::submit('Pohrani', array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
</div>
</div>
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_PREDMET_KATEGORIJA))
<div class="col-xs-12 col-sm-6 col-md-8 col-lg-9">
	<h3>Dozvoljeni predmeti</h3>
	<?php $predmeti = Predmet::orderBy('ime')->get();
	$allowed = array();
	if(isset($instruktor))$allowed = $instruktor->predmeti->lists('id');
	$value = false;?>
	<div class = "row">
		@foreach($predmeti as $predmet)
		<?php if(isset($instruktor))$value = in_array($predmet->id, $allowed);?>
			<div class="col-xs-6 col-sm-4 col-md-3 col-lg-2">
				<div class="checkbox">
					<label>{{ Form::checkbox('allowed[]', $predmet->id, $value) }} {{ $predmet->ime }}</label>
				</div>
			</div>
		@endforeach
	</div>
</div>
@endif
{{ Form::close() }}
@endsection