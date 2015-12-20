@section('list')
@if($cjenovnici->count() > 0)
<table class="table table-striped">
    <tbody>
        <tr>
            <th>Ime</th>
            <th>Opis</th>
        </tr>
        @foreach($cjenovnici as $cjenovnik)
        <tr>
            <td>
                {{ $cjenovnik->link() }}
            </td>
            <td>
                {{ $cjenovnik->opis }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $cjenovnici->links() }}
@else
<div class="container">
    <h3>Nema rezultata.</h3>
</div>
@endif
@endsection