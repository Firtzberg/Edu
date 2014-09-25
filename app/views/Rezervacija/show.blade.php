<div class="container">
<h2>Prikaz rezervacije</h2>
@if(Session::has('poruka'))
<div class="alert alert-warning">
{{Session::get('poruka')}}
</div>
@endif
<div class="row">
<div class="col-sm-6">
@if(is_null($rezervacija->naplata))
@if(strtotime($rezervacija->pocetak_rada)>time())
<h4>Nije moguće naplatiti prije odrade instrukcija.</h4>
@else
<h4>Naplata nije zvršena.</h4>
{{ link_to_route('Rezervacija.naplati', 'Naplati', array($rezervacija->id),
array('class' => 'btn btn-primary')) }}
@endif
@else
<h4>Naplata je izvršena</h4>
<dl class="dl-horizontal">
<dt>Vrijeme naplate</dt><dd>{{ $rezervacija->naplata->updated_at }}</dd>
<dt>Ukupan iznos</dt><dd>{{ $rezervacija->naplata->ukupno_uplaceno }}</dd>
<dt>Za instruktora</dt><dd>{{ $rezervacija->naplata->za_instruktora }}</dd>
<dt>Za tvrtku</dt><dd>{{ $rezervacija->naplata->za_tvrtku }}</dd>
</dl>
{{ Form::open(array('action' => array('Rezervacija.destroy_naplata', $rezervacija->id), 'method' => 'delete')) }}
{{ link_to_route('Rezervacija.naplati', 'Uredi naplatu', array($rezervacija->id),
array('class' => 'btn btn-primary')) }}
 {{ Form::submit('Ukloni naplatu',
array('class' => 'btn btn-warning')) }}
{{ Form::close() }}
@endif
</div>

<div class="col-sm-6">
<dl class="dl-horizontal">
<dt>Instruktor</dt><dd>{{ link_to_route('Instruktor.show', $rezervacija->instruktor->name, array('id' => $rezervacija->instruktor->id)) }}</dd>
<dt>Vrijeme početka</dt><dd>{{ $rezervacija->pocetak_rada }}</dd>
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
<dt>Usmjerenje</dt><dd>
@if(empty($rezervacija->usmjerenje))
Nije definirano
@else
{{ $rezervacija->usmjerenje }}
@endif
</dd>
<dt>Predmet</dt><dd>
@if(empty($rezervacija->predmet))
Nije definirano
@else
{{ $rezervacija->predmet }}
@endif
</dd>
<dt>Broj učenika</dt><dd>{{ $rezervacija->broj_ucenika }}</dd>
</dl>
@if(strtotime($rezervacija->pocetak_rada) > time())
{{ Form::open(array('route' => array('Rezervacija.destroy', $rezervacija->id), 'method' => 'delete')) }}
{{ Form::submit('Otkaži rezervaciju',
array('class' => 'btn btn-danger')) }}
{{ Form::close() }}
@endif
</div>
</div>
</div>