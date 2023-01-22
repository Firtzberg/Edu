@if(is_null($rezervacija->naplata))
<h4>
@if($rezervacija->tecaj)
Tečajevi se ne naplaćuju.
@else
Naplata nije izvršena.
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_NAPLATA))
{{ link_to_route('Naplata.create', 'Naplati', array($rezervacija->id),
array('class' => 'btn btn-primary')) }}
@endif
@endif
</h4>
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
@if($naplata->napomena && !empty($naplata->napomena))
<dt>Napomena</dt><dd>{{ $naplata->napomena }}</dd>
@endif
</dl>
@if(Auth::user()->hasPermission(Permission::PERMISSION_REMOVE_NALATA))
{{ Form::open(array('route' => array('Naplata.destroy', $rezervacija->id), 'method' => 'delete')) }}
{{ Form::submit('Poništi naplatu', array('class' => 'btn btn-danger')) }}
{{ Form::close() }}
@endif
@endif