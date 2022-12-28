@section('list')
@if($neradniDani->count() > 0)
<table class="table table-striped">
	<tbody>
		<tr>
			<th>Naziv</th>
			<th>Dan</th>
			<th>Mjesec</th>
			<th>Godina</th>
			@if (Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_NERADNI_DAN))
				<th>Mogućnosti</th>
			@endif
		</tr>
@foreach($neradniDani as $neradniDan)
<tr>
	<td>
		@if (Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_NERADNI_DAN))
		{{ link_to_route('NeradniDan.edit', $neradniDan->naziv, array('id' => $neradniDan->id)) }}
		@else
		{{ $neradniDan->naziv }}
		@endif
	</td>
	<td>{{ $neradniDan->dan }}</td>
	<td>{{ $neradniDan->mjesec }}</td>
	<td>{{ $neradniDan->godina ? $neradniDan->godina : 'Svaka' }}</td>
	@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_NERADNI_DAN))
	<td>
		{{ Form::open(array('route' => array('NeradniDan.destroy', $neradniDan->id), 'method' => 'delete')) }}
		{{ Form::submit('Ukloni', array('class' => 'btn btn-warning')) }}
		{{ Form::close() }}
	</td>
	@endif
</tr>
@endforeach
</tbody>
</table>
{{ $neradniDani->links() }}
@else
<div class="container">
<h3>Nema rezultata.</h3>
</div>
@endif
@endsection