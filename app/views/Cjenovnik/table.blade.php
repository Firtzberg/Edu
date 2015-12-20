@section('table')
<table class = "table table-striped">
    <tbody>
        <tr>
            <th>Br. osoba</th>
            <th colspan="5">Cijena po osobi</th>
            <th colspan="2">Predavač/Edukos</th>
            <th colspan="2">Predavač/Edukos postotak</th>
        </tr>
        <tr>
            <th></th>
            <th>1. osoba</th>
            <th>2. osoba</th>
            <th>3. osoba</th>
            <th>4. osoba</th>
            <th>Iznos</th>
            <th>Predavač</th>
            <th>Edukos</th>
            <th>Predavač</th>
            <th>Edukos</th>
        </tr>
        <?php
        $data[1]['cijene'] = $cjenovnik->cijena_1_osoba;
        $data[2]['cijene'] = $cjenovnik->cijena_2_osobe;
        $data[3]['cijene'] = $cjenovnik->cijena_3_osobe;
        $data[4]['cijene'] = $cjenovnik->cijena_4_osobe;
        $data[1]['instruktor'] = $cjenovnik->instruktor_1_osoba;
        $data[2]['instruktor'] = $cjenovnik->instruktor_2_osobe;
        $data[3]['instruktor'] = $cjenovnik->instruktor_3_osobe;
        $data[4]['instruktor'] = $cjenovnik->instruktor_4_osobe;
        ?>
        @foreach($data as $index => $info)
        <tr>
            <td>{{ $index }} osoba</td>
            @for($i = 0; $i < $index; $i++)
            <td>{{ $info['cijene'] }}</td>
            @endfor
            @for(;$i < 4; $i++)
            <td>-</td>
            @endfor
            <td>{{ $info['cijene'] * $index }}</td>
            <td>{{ $info['instruktor'] }}</td>
            <td>{{ $info['cijene'] * $index - $info['instruktor'] }}</td>
            @if($info['cijene'] == 0)
            <td>0</td>
            <td>0</td>
            @else
            <td>{{ $info['instruktor'] * 100 / ($info['cijene'] * $index) }}</td>
            <td>{{ ($info['cijene'] * $index - $info['instruktor']) * 100 / ($info['cijene'] * $index) }}</td>
            @endif
        </tr>
        @endforeach
        <tr>
            <td>5 i više osoba</td>
            <td colspan="4">{{ $cjenovnik->cijena_vise_osoba }} po osobi</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $cjenovnik->instruktor_udio_vise_osoba }}</td>
            <td>{{ 100 - $cjenovnik->instruktor_udio_vise_osoba }}</td>
        </tr>
    </tbody>
</table>
@endsection