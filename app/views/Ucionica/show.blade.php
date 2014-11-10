@extends('layouts.master')

@section('title')
Učionica {{ $ucionica->naziv }}
@endsection

@section('content')
<h2>Učionica {{ $ucionica->naziv }}</h2>
<dl class = "dl-horizontal">
<dt>Najveći broj učenika</dt><dd>{{ $ucionica->max_broj_ucenika }}</dd>
<dt>Adresa</dt><dd>{{ $ucionica->adresa }}</dd>
<dt>Sprat</dt><dd>{{ $ucionica->kat }}</dd>
<dt>Opis</dt><dd>{{ $ucionica->opis }}</dd>

{{ $raspored }}
@if(Auth::user()->hasPermission(Permission::PERMISSION_REMOVE_UCIONICA))
{{ Form::open(array('route' => array('Ucionica.destroy', $ucionica->id), 'method' => 'delete')) }}
@endif
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_UCIONICA))
{{ link_to_route('Ucionica.edit', 'Uredi', array($ucionica->id), array('class' => 'btn btn-default')) }} 
@endif
@if(Auth::user()->hasPermission(Permission::PERMISSION_REMOVE_UCIONICA))
{{ Form::submit('Ukloni', array('class' => 'btn btn-warning')) }}
{{ Form::close() }}
@endif
@endsection