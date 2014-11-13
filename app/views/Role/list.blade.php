@section('list')
@if($roles->count() > 0)
<table class="table table-striped">
<tbody>
	<tr>
		<th>Ime</th>
	</tr>
@foreach($roles as $role)
<tr>
	<td>
		{{ $role->link() }}
	</td>
</tr>
@endforeach
</tbody>
</table>
{{ $roles->links() }}
@else
<div class="container">
<h3>Nema rezultata.</h3>
</div>
@endif
@endsection