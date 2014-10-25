@section('list')
@if($ucionice->count() > 0)
<table class="table table-striped">
	<tbody>
		<tr>
			<th>Naziv</th>
			<th>Adresa</th>
			<th>Najveći broj učenika</th>
		</tr>
@foreach($ucionice as $ucionica)
<tr>
	<td>
{{ $ucionica->link() }}
	</td>
	<td>{{ $ucionica->adresa }}</td>
	<td>{{ $ucionica->max_broj_ucenika }}</td>
</tr>
@endforeach
</tbody>
</table>
{{ $ucionice->links() }}
@else
<div class="container">
<h3>Nema rezultata.</h3>
</div>
@endif
@endsection