<div class = "container">
<p>
Godišnji izvjestaj
@if(isset($instruktor))
 instruktora
{{ link_to_route('Instruktor.show', $instruktor->name, array('id' => $instruktor->id)) }}
@endif
 za {{ $godina }}. godinu</p>
<ul class="pager">
@if(isset($instruktor))
<li class="previous">{{ link_to_route('Izvjestaj.godisnji', '<< Prethodna godina', array('id' => $instruktor->id, 'godina' => $godina - 1)) }}</li>
<li class="next">{{ link_to_route('Izvjestaj.godisnji', 'Sljedeća godina >>', array('id' => $instruktor->id, 'godina' => $godina + 1)) }}</li>
@else
<li class="previous">{{ link_to_route('Izvjestaj.ukupni_godisnji', '<< Prethodna godina', array('godina' => $godina - 1)) }}</li>
<li class="next">{{ link_to_route('Izvjestaj.ukupni_godisnji', 'Sljedeća godina >>', array('godina' => $godina + 1)) }}</li>
@endif
</ul>
<div class="table-responsive">
<table class="table table-striped table-bordered">
<tbody>
	<tr>
		<th>Mjesec</th>
		<th>Sati rada</th>
		<th>Za instruktora</th>
		<th>Za tvrtku</th>
		<th>Ukupno</th>
	</tr>
@foreach($zarada as $m => $z)
<tr>
	<td>{{ $m }}</td>
	<td>{{ $z['sati'] }}</td>
	<td>{{ $z['za_instruktora'] }}</td>
	<td>{{ $z['za_tvrtku'] }}</td>
	<td>{{ $z['ukupno_uplaceno'] }}</td>
</tr>
@endforeach
</tbody>
</table>

<div class="navbar">
@if(isset($instruktor))
{{ link_to_route('Izvjestaj.tjedni', 'Tjedni izvještaj', array('id' => $instruktor->id), array('class' => 'btn btn-link navbar')) }}
@if(Auth::user()->is_admin)
{{ link_to_route('Izvjestaj.ukupni_godisnji', 'Ukupni godišnji izvještaj', null, array('class' => 'btn btn-link navbar')) }}
{{ link_to_route('Izvjestaj.ukupni_tjedni', 'Ukupni tjedni izvještaj', null, array('class' => 'btn btn-link navbar')) }}
@endif
@else
{{ link_to_route('Izvjestaj.ukupni_tjedni', 'Ukupni tjedni izvještaj', null, array('class' => 'btn btn-link navbar')) }}
@endif
</div>
</div>