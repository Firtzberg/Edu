@section('list')
@if($instruktori->count() > 0)
<table class="table">
<tbody>
	<tr>
		<th>Ime</th>
	</tr>
@foreach($instruktori as $i)
<tr>
	<td>
		{{ link_to_route('Instruktor.show', $i->name, $parameters = array($i->id)) }}
	</td>
</tr>
@endforeach
</tbody>
</table>
{{ $instruktori->links() }}
@else
<div class="container">
<h3>Nema rezultata.</h3>
</div>
@endif
@endsection