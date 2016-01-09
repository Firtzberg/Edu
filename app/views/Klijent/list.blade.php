@section('list')
@if($klijenti->count() > 0)
<table class="table table-striped">
    <tbody>
        <tr>
            <th>Ime</th>
            <th>Broj mobitela</th>
            <th>Škola/Fakultet</th>
            <th>Razred/Godina</th>
            <th>Roditelj</th>
            <th>Broj Roditelja</th>
        </tr>
        @foreach($klijenti as $klijent)
        <tr>
            <td>
                {{ $klijent->link() }}
            </td>
            <td>
                {{ Klijent::getReadableBrojMobitela($klijent->broj_mobitela) }}
            </td>
            <td>
                {{ $klijent->skola }}
            </td>
            <td>
                {{ $klijent->razred }}
            </td>
            <td>
                {{ $klijent->roditelj }}
            </td>
            <td>
                {{ Klijent::getReadableBrojMobitela($klijent->broj_roditelja) }}
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