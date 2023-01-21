
@if($rezervacije->count() > 0)
    <table class="table table-striped">
        <tbody>
        <tr>
            <th>Rezervacija</th>
            <th>Djelatnik</th>
            <th>Vrijeme početka</th>
            <th>Trajanje</th>
        </tr>
        @foreach($rezervacije as $rezervacija)
            <tr>
                <td>{{ $rezervacija->link() }}</td>
                <td>{{ $rezervacija->instruktor->link() }}</td>
                <td>{{ $rezervacija->pocetak_rada }}</td>
                <td>{{ $rezervacija->kolicina.' '.$rezervacija->mjera->znacenje }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $rezervacije->links() }}
@else
    <div class="container">
        <h3>Nema rezultata.</h3>
    </div>
@endif