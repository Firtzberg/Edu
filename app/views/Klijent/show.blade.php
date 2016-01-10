@extends('layouts.master')

@section('title')
Klijent {{ $klijent->ime }}
@endsection

@section('content')
<h2>Klijent {{ $klijent->ime }}</h2>
<dl class = "dl-horizontal">
    <dt>Broj mobitela</dt><dd>{{ Klijent::getReadableBrojMobitela($klijent->broj_mobitela) }}</dd>
    <dt>Ime i Prezime</dt><dd>{{ $klijent->ime }}</dd>
    @if(!empty($klijent->skola))
    <dt>Škola/Fakultet</dt><dd>{{ $klijent->skola }}</dd>
    @endif
    @if(!empty($klijent->razred))
    <dt>Razred/Godina</dt><dd>{{ $klijent->razred }}</dd>
    @endif
    @if(!empty($klijent->email))
    <dt>Email</dt><dd>{{ $klijent->email }}</dd>
    @endif
    @if(!empty($klijent->facebook))
    <dt>Facebook</dt><dd>{{ $klijent->facebook }}</dd>
    @endif
    @if(!empty($klijent->roditelj))
    <dt>Roditelj</dt><dd>{{ $klijent->roditelj }}</dd>
    @endif
    @if(!empty($klijent->broj_roditelja))
    <dt>Broj Roditelja</dt><dd>{{ Klijent::getReadableBrojMobitela($klijent->broj_roditelja) }}</dd>
    @endif
</dl>
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_KLIJENT))
{{ link_to_route('Klijent.edit', 'Uredi', array($klijent->broj_mobitela), array('class' => 'btn btn-default')) }}
@endif
<?php
$suma_ukupno = 0;
$suma_za_tvrtku = 0;
$suma_rezervacija = 0;
$suma_missed = 0;
$suma_naplate_count = 0;
?>
<div>
    <h4>Pohađane instrukcije</h4>
    <table class="table table-striped">
        <tbody>
            <tr>
                <th>
                    Predavač
                </th>
                <th>
                    Predmet
                </th>
                <th>
                    Broj rezervacija
                </th>
                <th>
                    Broj izostanaka
                </th>
                <th>
                    Broj naplata
                </th>
                <th>
                    Ukupno naplaćeno
                </th>
                <th>
                    Udio tvtke
                </th>
                <th>
                    Posljednja rezervacija
                </th>
                <th>
                    Detalji
                </th>
            </tr>
            @foreach($interesi as $interes)
            <?php
            $user = User::find($interes->instruktor_id);
            if ($user)
                $predavacDisplay = $user->link();
            else $predavacDisplay = "Napoznato";
            $predmet = Predmet::find($interes->predmet_id);
            if ($predmet)
                $predmetDisplay = $predmet->link();
            else $predmetDisplay = "Napoznato";
            $suma_ukupno += $interes->ukupno;
            $suma_za_tvrtku += $interes->za_tvrtku;
            $suma_rezervacija += $interes->count;
            $suma_missed += $interes->missed_count;
            $suma_naplate_count += $interes->naplate_count;
            ?>
            <tr>
                <td>
                    {{ $predavacDisplay }}
                </td>
                <td>
                    {{ $predmetDisplay }}
                </td>
                <td>
                    {{ $interes->count }}
                </td>
                <td>
                    {{ $interes->missed_count }}
                </td>
                <td>
                    {{ $interes->naplate_count }}
                </td>
                <td>
                    {{ (int)$interes->ukupno }}
                </td>
                <td>
                    {{ (int)$interes->za_tvrtku }}
                </td>
                <td>
                    {{ (new DateTime($interes->posljednji_put))->format('d.m.Y') }}
                </td>
                <td>
                    {{ link_to_route('Klijent.rezervacije', 'Popis >>', array($klijent->broj_mobitela, $interes->instruktor_id, $interes->predmet_id)); }}
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2">
                    Ukupno
                </td>
                <td>
                    {{ $suma_rezervacija }}
                </td>
                <td>
                    {{ $suma_missed }}
                </td>
                <td>
                    {{ $suma_naplate_count }}
                </td>
                <td>
                    {{ $suma_ukupno }}
                </td>
                <td>
                    {{ $suma_za_tvrtku }}
                </td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
@endsection