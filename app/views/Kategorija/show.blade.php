<h2>{{ $kategorija->ime }}</h2>
<h3>Podkategorje</h3>
<div id="list-podkategorije" class="container">
	@if($kategorija->podkategorije()->count() < 1)
	Nema podkategorija.
	@else
	<table class="table"><tbody>
		<tr>
			<th>Ime podkategorije</th>
			<th>Broj podkategorija</th>
			<th>Broj predmeta</th>
			<th></th>
		</tr>
		@foreach($kategorija->podkategorije() as $podkategorija)
		<tr>
			<td>{{ link_to_route('Kategorija.show', $podkategorija->ime, array('id' => $podkategorija->id)) }}</td>
			<td>:P</td>
			<td>(:</td>
			<td>{{ link_to_route('Kategorija.destroy', 'Izbriši', array('id' => $podkategorija->id)) }}</td>
		</tr>
		@endforeach
	</tbody></table>
	@endif
	{{ link_to_action('Kategorija.add', 'Dodaj') }}
</div>
<h3>Predmeti</h3>
<div id="list-predmeti" class="container">
	@if($kategorija->predmeti()->count() < 1)
	Nema predmeta.
	@else
	<table class="table"><tbody>
		<tr>
			<th>Ime predmeta</th>
			<th></th>
		</tr>
		@foreach($kategorija->predmeti() as $predmet)
		<tr>
			<td>{{ link_to_route('Kategorija.show', $predmet->ime, array('id' => $predmet->id)) }}</td>
			<td>{{ link_to_route('Kategorija.destroy', 'Izbriši', array('id' => $predmet->id)) }}</td>
		</tr>
		@endforeach
	</tbody></table>
	@endif
	{{ link_to_action('Kategorija.add', 'Dodaj') }}
</div>