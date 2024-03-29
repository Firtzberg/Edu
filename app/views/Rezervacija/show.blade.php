@extends('layouts.master')

@section('title')
Prikaz rezervacije
@endsection

@section('content')
<h2>Prikaz rezervacije</h2>
<div class="row">
<div class="col-xs-12 col-sm-6">
	@include('Rezervacija.Naplata.show')
</div>

<div class="col-xs-12 col-sm-6">
<dl class="dl-horizontal">
<dt>Djelatnik</dt><dd>{{ $rezervacija->instruktor->link() }}</dd>
<dt>Vrijeme početka</dt><dd>{{ $rezervacija->pocetak_rada }}</dd>
<dt>Trajanje</dt><dd>{{ $rezervacija->kolicina.' '.$rezervacija->mjera->znacenje }}</dd>
<dt>Vrijeme završetka</dt><dd>{{ $rezervacija->kraj_rada }}</dd>
<dt>Učionica</dt><dd>
@if(is_null($rezervacija->ucionica))
Uklonjena
@else
{{ $rezervacija->ucionica->link() }}
@endif
</dd>
<dt>Predmet</dt><dd>
@if($rezervacija->predmet)
{{ $rezervacija->predmet->link() }}
@else
Nije definiran
@endif
</dd>
<dt>Rezervacija objavljena</dt><dd>{{ $rezervacija->created_at }}</dd>
<dt>Posljednja izmjena</dt><dd>{{ $rezervacija->updated_at }}</dd>
<dt>Tečaj</dt><dd>{{ $rezervacija->tecaj?'DA':'NE' }}</dd>
@if($rezervacija->tecaj)
<dt>Broj polaznika</dt><dd>{{ $rezervacija->tecaj_broj_polaznika }}</dd>
@endif
@if($rezervacija->napomena && !empty($rezervacija->napomena))
<dt>Napomena</dt><dd>{{ $rezervacija->napomena }}</dd>
@endif
</dl>
    <?php
    $deadline = strtotime($rezervacija->pocetak_rada);
    $deadline -= 60 * 60;
?>
@if($deadline > time() ||
Auth::user()->hasPermission(Permission::PERMISSION_REMOVE_STARTED_REZERVACIJA))
{{ Form::open(array('route' => array('Rezervacija.destroy', $rezervacija->id), 'method' => 'delete')) }}
@endif
@if($deadline > time() ||
Auth::user()->hasPermission(Permission::PERMISSION_EDIT_STARTED_REZERVACIJA))
{{ link_to_route('Rezervacija.edit', 'Uredi', array('id' => $rezervacija->id), array('class' => 'btn btn-default')) }} 
@endif
{{ link_to_route('Rezervacija.copy', 'Kopiraj', array('id' => $rezervacija->id), array('class' => 'btn btn-default')) }} 
@if($deadline > time() ||
Auth::user()->hasPermission(Permission::PERMISSION_REMOVE_STARTED_REZERVACIJA))
{{ Form::submit('Otkaži rezervaciju',
array('class' => 'btn btn-danger')) }}
{{ Form::close() }}
@endif
</div>
</div>
@if(!$rezervacija->tecaj)
<div>
<h3>Klijenti <small>ukupno {{ $rezervacija->klijenti()->count() }}</small></h3>
<dl class="dl-horizontal">
@foreach($rezervacija->klijenti as $klijent)
<br/>
<dt>{{ $klijent->broj_mobitela }}</dt>
<dd>{{ $klijent->link() }}
@if($klijent->pivot->missed)
 <small>izostao</small>
@endif
</dd>
@endforeach
</dl>
</div>
@endif
@endsection