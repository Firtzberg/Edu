@extends('layouts.master')

@section('title')
Promjena zaporke
@endsection

@section('content')
{{ Form::open(array('route' => array('Djelatnik.postChangePassword',
'id' => $instruktor->id),
'class' => 'form-signin',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
<h2 class="form-signin-heading">Primjena zaporke</h2>
@if(!Auth::user()->hasPermission(Permission::PERMISSION_PASSWORD_RESET))
<div class = "form-group">
	{{ Form::password('oldpass',
		array(
			'placeholder' => 'Stara zaporka',
			'class' => 'form-control',
			'autofocus' => 'autofocus',
			'required' => 'required')) }}
</div>
@endif
<div class = "form-group">
	{{ Form::password('newpass',
		array(
			'placeholder' => 'Nova zaporka',
			'class' => 'form-control',
			'required' => 'required')) }}
</div>
<div class = "form-group">
	{{ Form::password('rep',
		array(
			'placeholder' => 'Ponovi',
			'class' => 'form-control',
			'required' => 'required')) }}
</div>
{{ Form::submit('Promijeni', array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
{{ Form::close() }}
@endsection