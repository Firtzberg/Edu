@extends('layouts.master')

@section('title')
Instruktor {{ $instruktor->name }}
@endsection

@section('content')
<h2>Instruktor {{ $instruktor->name }}</h2>
<dl class="dl-horizontal">
@if(!empty($instruktor->broj_mobitela))
<dt>Broj mobitela</dt><dd> {{ $instruktor->broj_mobitela }}</dd>
@endif
@if(!empty($instruktor->email))
<dt>Email</dt><dd> {{ $instruktor->email}}</dd>
@endif
</dl>

{{ $raspored }}
{{ Form::open(array('route' => array('Instruktor.destroy', $instruktor->id), 'method' => 'delete', 'class' => 'form')) }}
@if(Auth::user()->is_admin || Auth::id() == $instruktor->id)
{{ link_to_route('Instruktor.edit', 'Uredi', array($instruktor->id), array('class' => 'btn btn-default')) }}
 {{ link_to_route('Izvjestaj.tjedni', 'Izvjestaj', array($instruktor->id), array('class' => 'btn btn-default')) }}
 {{ link_to_route('Instruktor.changePassword', 'Promijeni zaporku', array($instruktor->id), array('class' => 'btn btn-default')) }} 
@endif
@if(Auth::user()->is_admin&& !$instruktor->is_admin)
{{ Form::submit('Ukloni', array(
'class' => 'btn btn-warning')) }}
@endif
{{ Form::close() }}
@endsection