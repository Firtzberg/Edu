@section('list')
@if($klijenti->count() > 0)
<table class="table">
<tbody>
	<tr>
		<th>Ime</th>
	</tr>
@foreach($klijenti as $klijent)
<tr>
	<td>
		{{ $klijent->link() }}
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