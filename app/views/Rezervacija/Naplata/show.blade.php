@if(is_null($rezervacija->naplata))
@if(strtotime($rezervacija->pocetak_rada)>time())
<h4>Nije moguće naplatiti prije odrade instrukcija.</h4>
@else
<h4>Naplata nije izvršena.</h4>
{{ link_to_route('Naplata.create', 'Naplati', array($rezervacija->id),
array('class' => 'btn btn-primary')) }}
@endif
@else
<?php $naplata = $rezervacija->naplata ?>
<h4>Naplata je izvršena</h4>
<dl class="dl-horizontal">
<dt>Vrijeme naplate</dt><dd>{{ $naplata->updated_at }}</dd>
<dt>Naplaćeno trajanje</dt><dd>{{ $naplata->stvarna_kolicina.' '.$naplata->stvarnaMjera->znacenje }}</dd>
<dt>Došli polaznici</dt><dd>{{ $rezervacija->klijenti->filter(function($klijent){return $klijent->pivot->missed == 0;})->count() }}</dd>
<dt>Ukupan iznos</dt><dd>{{ $naplata->ukupno_uplaceno }}</dd>
<dt>Za instruktora</dt><dd>{{ $naplata->za_instruktora }}</dd>
<dt>Za tvrtku</dt><dd>{{ $naplata->za_tvrtku }}</dd>
</dl>
@endif