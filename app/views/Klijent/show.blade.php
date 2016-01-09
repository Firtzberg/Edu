@extends('layouts.master')

@section('title')
Klijent {{ $klijent->ime }}
@endsection

@section('content')
<h2>Klijent {{ $klijent->ime }}</h2>
<dl class = "dl-horizontal">
    <dt>Broj mobitela</dt><dd>{{ Klijent::getReadableBrojMobitela($klijent->broj_mobitela) }}</dd>
    <dt>Ime i Prezime</dt><dd>{{ $klijent->ime }}</dd>
    @if(!empty($klijent->skola))
    <dt>Škola/Fakultet</dt><dd>{{ $klijent->skola }}</dd>
    @endif
    @if(!empty($klijent->razred))
    <dt>Razred/Godina</dt><dd>{{ $klijent->razred }}</dd>
    @endif
    @if(!empty($klijent->email))
    <dt>Email</dt><dd>{{ $klijent->email }}</dd>
    @endif
    @if(!empty($klijent->facebook))
    <dt>Facebook</dt><dd>{{ $klijent->facebook }}</dd>
    @endif
    @if(!empty($klijent->roditelj))
    <dt>Roditelj</dt><dd>{{ $klijent->roditelj }}</dd>
    @endif
    @if(!empty($klijent->broj_roditelja))
    <dt>Broj Roditelja</dt><dd>{{ Klijent::getReadableBrojMobitela($klijent->broj_roditelja) }}</dd>
    @endif
</dl>

@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_KLIJENT))
{{ link_to_route('Klijent.edit', 'Uredi', array($klijent->broj_mobitela), array('class' => 'btn btn-default')) }}
@endif
@endsection