@extends('layouts.master')

@section('heading')
{{ HTML::style('css/raspored1.css') }}
@endsection

@section('title')
@if($instruktor->role)
{{ $instruktor->role->ime }}
@else
Djelatnik
@endif
 {{ $instruktor->name }}
@endsection

@section('content')
<h2>
    @if($instruktor->role)
    {{ $instruktor->role->link() }}
    @else
    Djelatnik
    @endif
    {{ $instruktor->name }}</h2>
<dl class="dl-horizontal">
    @if(!empty($instruktor->broj_mobitela))
    <dt>Broj mobitela</dt><dd> {{ $instruktor->broj_mobitela }}</dd>
    @endif
    @if(!empty($instruktor->email))
    <dt>Email</dt><dd> {{ $instruktor->email}}</dd>
    @endif
</dl>

<div id="strana-rasporeda">
{{ $raspored }}
</div>
@if(Auth::user()->hasPermission(Permission::PERMISSION_REMOVE_USER) && !($instruktor->id == Auth::id()))
{{ Form::open(array('route' => array('Djelatnik.destroy', $instruktor->id), 'method' => 'delete', 'class' => 'form')) }}
@endif
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_USER) || Auth::id() == $instruktor->id)
{{ link_to_route('Djelatnik.edit', 'Uredi', array($instruktor->id), array('class' => 'btn btn-default')) }}
{{ link_to_route('Izvjestaj.tjedni', 'Izvjestaj', array($instruktor->id), array('class' => 'btn btn-default')) }}
@if(Auth::user()->hasPermission(Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING))
{{ link_to_route('Rezervacija.create', 'Rezerviraj', array($instruktor->id), array('class' => 'btn btn-default')) }}
@endif
{{ link_to_route('Djelatnik.changePassword', 'Promijeni zaporku', array($instruktor->id), array('class' => 'btn btn-default')) }} 
@endif
@if(Auth::user()->hasPermission(Permission::PERMISSION_REMOVE_USER) && !($instruktor->id == Auth::id()))
{{ Form::submit('Ukloni', array(
'class' => 'btn btn-warning')) }}
{{ Form::close() }}
@endif

@if(($instruktor->id == Auth::id()) || Auth::user()->hasPermission(Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING))
<div>
    <h4>Nenaplaćene rezervacije</h4>
    @include('Rezervacija.list', array('rezervacije' => $instruktor->rezervacije()->nenaplacene()->with('predmet', 'mjera', 'instruktor')->paginate()))
</div>
@endif

@if(Auth::user()->hasPermission(Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING) || $instruktor->id == Auth::id())
    <div>
        <h4>Dozvoljeni predmeti</h4>
        <div class = "row">
            @foreach($instruktor->predmeti as $predmet)
                <div class = "col-xs-6 col-sm-4 col-md-3 col-lg-2">{{ $predmet->link() }}</div>
            @endforeach
        </div>
    </div>
@endif
@endsection