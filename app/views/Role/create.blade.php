@extends('layouts.master')

@section('title')
@if(isset($role))
{{ $role->ime }} - Uređivanje
@else
Dodavanje uloge
@endif
@endsection

@section('content')
<?php
$optional = array('class' =>'form-control');
$required = array(
'class' =>'form-control',
'required' => 'required');
?>
@if(isset($role))
{{ Form::model($role, array('route' => array('Role.update', $role->id),
'method' => 'put',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
<h2 class="form-heading">Uređivaje uloge</h2>
@else
{{ Form::open(array('route' => 'Role.store',
'style' => 'max-width:330px;padding:15px;margin:auto'))}}
<h2 class="form-heading">Dodavanje uloge</h2>
@endif
<div class = "form-group">
{{ Form::label('Ime') }}
{{ Form::text('ime', null,
array(
'class' =>'form-control',
'autofocus' => 'autofocus',
'required' => 'required',
'autocomplete' => 'off')) }}
</div>
<div class = "form-group">
{{ Form::label('Opis') }}
{{ Form::textarea('opis', null,
$optional) }}
</div>
<div class = "form-group">
	{{ Form::label('Dozvole') }}
	<?php $permissions = Permission::all();
	$allowed = array();
	if(isset($role))$allowed = $role->permissions->lists('id');
	$value = false;?>
	@foreach($permissions as $permission)
	<?php if(isset($role))$value = in_array($permission->id, $allowed);?>
	<div class = "checkbox">
		<label>
			{{ Form::checkbox('allowed[]', $permission->id, $value) }} {{ $permission->ime }}
		</label>
	</div>
	@endforeach
</div>
<div class = "form-group">
{{ Form::submit('Pohrani', array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
</div>
{{ Form::close() }}
@endsection