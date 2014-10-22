@extends('layouts.master')

@section('title')
@if(isset($instruktor))
{{ $instruktor->name }} - 
@endif
Tjedni izvještaj
@endsection

@section('content')
<p>
Tjedni izvjestaj
@if(isset($instruktor))
 instruktora
{{ link_to_route('Instruktor.show', $instruktor->name, array('id' => $instruktor->id)) }}
@endif
 za {{$tjedan}}. tjedan u {{ $godina }}. godini</p>
<ul class="pager">
@if(isset($instruktor))
<li class="previous">{{ link_to_route('Izvjestaj.tjedni', '<< Prethodni tjedan', array('id' => $instruktor->id, 'tjedan' => $tjedan-1, 'godina' => $godina)) }}</li>
<li class="next">{{ link_to_route('Izvjestaj.tjedni', 'Sljedeći tjedan >>', array('id' => $instruktor->id, 'tjedan' => $tjedan+1, 'godina' => $godina)) }}</li>
@else
<li class="previous">{{ link_to_route('Izvjestaj.ukupni_tjedni', '<< Prethodni tjedan', array('tjedan' => $tjedan-1, 'godina' => $godina)) }}</li>
<li class="next">{{ link_to_route('Izvjestaj.ukupni_tjedni', 'Sljedeći tjedan >>', array('tjedan' => $tjedan+1, 'godina' => $godina)) }}</li>
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
{{ link_to_route('Izvjestaj.godisnji', 'Godišni izvještaj', array('id' => $instruktor->id), array('class' => 'btn btn-link navbar')) }}
@if(Auth::user()->is_admin)
{{ link_to_route('Izvjestaj.ukupni_godisnji', 'Ukupni godišnji izvještaj', null, array('class' => 'btn btn-link navbar')) }}
{{ link_to_route('Izvjestaj.ukupni_tjedni', 'Ukupni tjedni izvještaj', null, array('class' => 'btn btn-link navbar')) }}
@endif
@else
{{ link_to_route('Izvjestaj.ukupni_godisnji', 'Ukupni godišnji izvještaj', null, array('class' => 'btn btn-link navbar')) }}
@endif
</div>
@endsection