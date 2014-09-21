<div class="container">
<p>
Tjedni izvjestaj
@if(isset($instruktor))
 instruktora
{{ link_to_action('InstruktorController@show', $instruktor->name, array('id' => $instruktor->id)) }}
@endif
 za {{$tjedan}}. tjedan u {{ $godina }}. godini</p>
<ul class="pager">
@if(isset($instruktor))
<li class="previous">{{ link_to_action('IzvjestajController@tjedni_izvjestaj', '<< Prethodni tjedan', array('id' => $instruktor->id, 'tjedan' => $tjedan-1, 'godina' => $godina)) }}</li>
<li class="next">{{ link_to_action('IzvjestajController@tjedni_izvjestaj', 'Sljedeći tjedan >>', array('id' => $instruktor->id, 'tjedan' => $tjedan+1, 'godina' => $godina)) }}</li>
@else
<li class="previous">{{ link_to_action('IzvjestajController@ukupni_tjedni_izvjestaj', '<< Prethodni tjedan', array('tjedan' => $tjedan-1, 'godina' => $godina)) }}</li>
<li class="next">{{ link_to_action('IzvjestajController@ukupni_tjedni_izvjestaj', 'Sljedeći tjedan >>', array('tjedan' => $tjedan+1, 'godina' => $godina)) }}</li>
@endif
</ul>
<div class="table-responsive">
<table class="table table-striped table-bordered">
<tbody>
	<tr>
		<th>Datum</th>
		<th>Dan</th>
		<th>Sati rada</th>
		<th>Za instruktora</th>
		<th>Za tvrtku</th>
		<th>Ukupno</th>
	</tr>
@foreach($zarada as $z)
<tr>
	<td>{{ $z['datum'] }}</td>
	<td>{{ $z['dan'] }}</td>
	<td>{{ $z['sati'] }}</td>
	<td>{{ $z['za_instruktora'] }}</td>
	<td>{{ $z['za_tvrtku'] }}</td>
	<td>{{ $z['ukupno_uplaceno'] }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>

<div class="navbar">
@if(isset($instruktor))
{{ link_to_action('IzvjestajController@godisnji_izvjestaj', 'Godišni izvještaj', array('id' => $instruktor->id), array('class' => 'btn btn-link navbar')) }}
@if(Auth::user()->is_admin)
{{ link_to_action('IzvjestajController@ukupni_godisnji_izvjestaj', 'Ukupni godišnji izvještaj', null, array('class' => 'btn btn-link navbar')) }}
{{ link_to_action('IzvjestajController@ukupni_tjedni_izvjestaj', 'Ukupni tjedni izvještaj', null, array('class' => 'btn btn-link navbar')) }}
@endif
@else
{{ link_to_action('IzvjestajController@ukupni_godisnji_izvjestaj', 'Ukupni godišnji izvještaj', null, array('class' => 'btn btn-link navbar')) }}
@endif
</div>
</div>