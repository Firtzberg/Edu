@extends('layouts.master')

@section('title')
Uloga {{ $role->ime }}
@endsection

@section('content')
<h2>Uloga {{ $role->ime }}</h2>
<div>
	{{ Form::label('Opis') }}
	<div>{{ $role->opis }}</div>
</div>

{{ Form::open(array('route' => array('Role.destroy', $role->id), 'method' => 'delete', 'class' => 'form')) }}
{{ link_to_route('Role.edit', 'Uredi', array($role->id), array('class' => 'btn btn-default')) }}
{{ Form::submit('Ukloni', array('class' => 'btn btn-warning')) }}
{{ Form::close() }}
@endsection