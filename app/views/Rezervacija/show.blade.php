@extends('layouts.master')

@section('title')
Prikaz rezervacije
@endsection

@section('content')
<h2>Prikaz rezervacije</h2>
<div class="row">
<div class="col-sm-6">
	@include('Rezervacija.Naplata.show')
</div>

<div class="col-sm-6">
<dl class="dl-horizontal">
<dt>Instruktor</dt><dd>{{ $rezervacija->instruktor->link() }}</dd>
<dt>Vrijeme početka</dt><dd>{{ $rezervacija->pocetak_rada }}</dd>
<dt>Trajanje</dt><dd>{{ $rezervacija->kolicina.' '.$rezervacija->mjera->znacenje }}</dd>
<dt>Vrijeme završetka</dt><dd>{{ $rezervacija->kraj_rada() }}</dd>
<dt>Učionica</dt><dd>
@if(is_null($rezervacija->ucionica))
Uklonjena
@else
{{ link_to_route('Ucionica.show',
	$rezervacija->ucionica->naziv.', '.$rezervacija->ucionica->adresa,
	array($rezervacija->ucionica->id)) }}
@endif
</dd>
<dt>Predmet</dt><dd>
@if($rezervacija->predmet)
@if(Auth::user()->is_admin)
{{ $rezervacija->predmet->link() }}
@else
{{ $rezervacija->predmet->ime }}
@endif
@else
Nije definiran
@endif
</dd>
<dt>Rezervacija obavljena</dt><dd>{{ $rezervacija->created_at }}</dd>
<dt>Posljednja izmjena</dt><dd>{{ $rezervacija->updated_at }}</dd>
</dl>
@if(strtotime($rezervacija->pocetak_rada) > time() || Auth::user()->is_admin)
{{ Form::open(array('route' => array('Rezervacija.destroy', $rezervacija->id), 'method' => 'delete')) }}
{{ link_to_route('Rezervacija.edit', 'Uredi', array('id' => $rezervacija->id), array('class' => 'btn btn-default')) }} 
@endif
{{ link_to_route('Rezervacija.copy', 'Kopiraj', array('id' => $rezervacija->id), array('class' => 'btn btn-default')) }} 
@if(strtotime($rezervacija->pocetak_rada) > time() || Auth::user()->is_admin)
{{ Form::submit('Otkaži rezervaciju',
array('class' => 'btn btn-danger')) }}
{{ Form::close() }}
@endif
</div>
</div>
<div>
<h3>Klijenti <small>ukupno {{ $rezervacija->klijenti()->count() }}</small></h3>
<dl class="dl-horizontal">
@foreach($rezervacija->klijenti as $klijent)
<br/>
<dt>{{ $klijent->broj_mobitela }}</dt>
<dd>{{ $klijent->ime }}
@if($klijent->pivot->missed)
<small> izostao</small>
@endif
</dd>
@endforeach
</dl>
</div>
@endsection