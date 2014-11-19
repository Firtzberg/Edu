@section('list')
@if($instruktori->count() > 0)
<table class="table table-striped">
    <tbody>
        <tr>
            <th>Ime</th>
            <th>Uloga</th>
            <th></th>
        </tr>
        @foreach($instruktori as $i)
        <tr>
            <td>{{ $i->link() }}</td>
            <td>{{ $i->role->link() }}</td>
            <td>
                @if(Auth::user()->hasPermission(Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING) &&
                $i->hasPermission(Permission::PERMISSION_OWN_REZERVACIJA_HANDLING))
                {{ link_to_route('Rezervacija.create', 'Rezerviraj', array($i->id), array('class' => 'btn btn-default')) }}
                @endif
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