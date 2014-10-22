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
@if(Auth::user()->is_admin)
{{ Form::open(array('route' => array('Ucionica.destroy', $ucionica->id), 'method' => 'delete')) }}
{{ link_to_route('Ucionica.edit', 'Uredi', array($ucionica->id), array('class' => 'btn btn-default')) }}
 {{ Form::submit('Ukloni', array('class' => 'btn btn-warning')) }}
{{ Form::close() }}
@endif
@endsection