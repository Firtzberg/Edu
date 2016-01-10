@extends('layouts.master')

@section('title')
Rezervacije klijenta {{ $klijent->ime }} kod djelatnika {{ $user?$user->name:'Nepoznato' }} iz predmeta {{ $predmet?$predmet->ime:'Nepoznato' }}
@endsection

@section('content')
<h2>Rezervacije klijenta {{ $klijent->link() }} kod djelatnika {{ $user?$user->link():'Nepoznato' }} iz predmeta {{ $predmet?$predmet->link():'Nepoznato' }}</h2>
<table class="table table-striped">
    <tbody>
        <tr>
            <th>
                Poveznica rezervacije
            </th>
            <th>
                Status
            </th>
            <th>
                Datum
            </th>
            <th>
                Naplaćena količina
            </th>
            <th>
                Ukupno naplaćeno
            </th>
            <th>
                Udio tvtke
            </th>
        </tr>
        @foreach($rezervacije as $rezervacija)
        <?php
        $rezervacija->jePlatio = (!$rezervacija->pivot->missed && $rezervacija->naplata);
        ?>
        <tr>
            <td>
                {{ $rezervacija->link() }}
            </td>
            <td>
                {{ $rezervacija->pivot->missed?'Izostanak':$rezervacija->naplata?'Naplaćeno':'Nenaplaćeno' }}
            </td>
            <td>
                {{ (new DateTime($rezervacija->pocetak_rada))->format('d.m.Y') }}
            </td>
            <td>
                {{ $rezervacija->jePlatio?$rezervacija->naplata->stvarna_kolicina.' '.$rezervacija->naplata->stvarnaMjera->simbol:'' }}
            </td>
            <td>
                {{ $rezervacija->jePlatio?(int)$rezervacija->naplata->ukupno_uplaceno/$rezervacija->polaznici_count:'' }}
            </td>
            <td>
                {{ $rezervacija->jePlatio?(int)$rezervacija->naplata->za_tvrtku/$rezervacija->polaznici_count:'' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection