@section('list')
@if($klijenti->count() > 0)
<table class="table">
<tbody>
	<tr>
		<th>Ime</th>
	</tr>
@foreach($klijenti as $k)
<tr>
	<td>
		{{ link_to_route('Klijent.show', $k->ime, array($k->broj_mobitela)) }}
	</td>
</tr>
@endforeach
</tbody>
</table>
{{ $klijenti->links() }}
@else
<div class="container">
<h3>Nema rezultata.</h3>
</div>
@endif
@endsection