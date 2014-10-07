<h2>{{ $predmet->ime }}</h2>
<h3>Cijene</h3>
<div id='cijene-list' class="container">
	<table><tbody>
		<tr>
			<th>Mjera</th>
			<th>Individualno</th>
			<th>Popust za novu osobu</th>
			<th>Minimalno</th>
		</tr>
		@foreach($predmet->cijene() as $cijena)
		<tr>
			<td>{{ $cijena->mjera->znacenje() }}</td>
			<td>{{ $cijena->individualno }}</td>
			<td>{{ $cijena->popust }}</td>
			<td>{{ $cijena->minimalno }}</td>
		</tr>
		@endforeach
	</tbody></table>
</div>
Save