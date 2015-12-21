@extends('layouts.master')

@section('title')
@if(isset($cjenovnik))
{{ $cjenovnik->ime }} - Uređivanje
@else
Dodavanje cjenovnika
@endif
@endsection

@section('content')
<?php
$opisParams = array(
    'class' => 'form-control',
    'rows' => 2);
$required = array(
    'class' => 'form-control',
    'required' => 'required');
$requiredPositive = array(
    'class' => 'form-control',
    'required' => 'required',
    'min' => 0);
$requiredPercentage = array(
    'class' => 'form-control',
    'required' => 'required',
    'min' => 0,
    'max' => 100);
?>
@if(isset($cjenovnik))
{{ Form::model($cjenovnik, array('route' => array('Cjenovnik.update', $cjenovnik->id),
'method' => 'put',
'class' => 'form')) }}
<h2 class="form-heading">Uređivanje cjenovnika</h2>
@else
{{ Form::open(array('route' => 'Cjenovnik.store',
'class' => 'form'))}}
<h2 class="form-heading">Dodavanje cjenovnika</h2>
@endif
<div class = "row">
    <div class = "col-xs-12 col-sm-6">
        <div class = "form-group">
            {{ Form::label('Ime') }}
            {{ Form::text('ime', null,
array(
'class' =>'form-control',
'autofocus' => 'autofocus',
'required' => 'required',
'autocomplete' => 'off')) }}
        </div>
    </div>
    <div class = "col-xs-12 col-sm-6">
        <div class = "form-group">
            {{ Form::label('Opis') }}
            {{ Form::textarea('opis', null, $opisParams) }}
        </div>
    </div>
</div>
<div>
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
            $data[1]['key'] = 'cijena_1_osoba';
            $data[2]['key'] = 'cijena_2_osobe';
            $data[3]['key'] = 'cijena_3_osobe';
            $data[4]['key'] = 'cijena_4_osobe';
            $AdditionalData['key'] = 'cijena_vise_osoba';
            $data[1]['key2'] = 'instruktor_1_osoba';
            $data[2]['key2'] = 'instruktor_2_osobe';
            $data[3]['key2'] = 'instruktor_3_osobe';
            $data[4]['key2'] = 'instruktor_4_osobe';
            $AdditionalData['key2'] = 'instruktor_udio_vise_osoba';
            if (isset($cjenovnik)) {
                // Edit
                for ($i = 1; $i < 5; $i++) {
                    $data[$i]['cijena'] = $cjenovnik[$data[$i]['key']];
                    $data[$i]['instruktor'] = $cjenovnik[$data[$i]['key2']];
                }
                $AdditionalData['cijena'] = $cjenovnik->cijena_vise_osoba;
                $AdditionalData['instruktor'] = $cjenovnik->instruktor_udio_vise_osoba;
            } else {
                // Create
                for ($i = 1; $i < 5; $i++) {
                    $data[$i]['cijena'] = 0;
                    $data[$i]['instruktor'] = 0;
                }
                $AdditionalData['cijena'] = 0;
                $AdditionalData['instruktor'] = 50;
            }
            // Get old input if possible
            for ($i = 1; $i < 5; $i++) {
                $data[$i]['cijena'] = Input::old($data[$i]['key'], $data[$i]['cijena']);
                $data[$i]['instruktor'] = Input::old($data[$i]['key2'], $data[$i]['instruktor']);
            }
            $AdditionalData['cijena'] = Input::old('cijena_vise_osoba', $AdditionalData['cijena']);
            $AdditionalData['instruktor'] = Input::old('instruktor_udio_vise_osoba', $AdditionalData['instruktor']);
            ?>
            @foreach($data as $index => $info)
            <tr>
                <td>{{ $index }} osoba</td>
                <th>{{ Form::input('number', $info['key'], $info['cijena'], $requiredPositive + array('id' => "cijena_$index")) }}</th>
                @for($i = 1; $i < $index; $i++)
                <td class="cijena_copy_{{ $index }}">{{ $info['cijena'] }}</td>
                @endfor
                @for(;$i < 4; $i++)
                <td>-</td>
                @endfor
                <td class="total_{{ $index }}">{{ $info['cijena'] * $index }}</td>
                <td>{{ Form::input('number', $info['key2'], $info['instruktor'], $requiredPositive + array('id' => "instruktor_$index")) }}</td>
                <td class="tvrtka_{{ $index }}">{{ $info['cijena'] * $index - $info['instruktor'] }}</td>
                @if($info['cijena'] == 0)
                <td class="instruktor_pcnt_{{ $index }}">0</td>
                <td class="tvrtka_pcnt_{{ $index }}">0</td>
                @else
                <td class="instruktor_pcnt_{{ $index }}">{{ number_format($info['instruktor'] * 100 / ($info['cijena'] * $index), 2, '.', '') }}</td>
                <td class="tvrtka_pcnt_{{ $index }}">{{ number_format(($info['cijena'] * $index - $info['instruktor']) * 100 / ($info['cijena'] * $index), 2, '.', '') }}</td>
                @endif
            </tr>
            @endforeach
            <tr>
                <td>5 i više osoba</td>
                <td colspan="4">{{ Form::input('number', 'cijena_vise_osoba', $AdditionalData['cijena'], $requiredPositive) }} po osobi</td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ Form::input('number', 'instruktor_udio_vise_osoba', $AdditionalData['instruktor'], $requiredPercentage + array('id' => 'ins_more_pcnt')) }}</td>
                <td id="tvtka_more_pcnt">{{ 100 - $AdditionalData['instruktor'] }}</td>
            </tr>
        </tbody>
    </table>
    {{ HTML::script('js/Cjenovnik/tableUpdater.js') }}
</div>
<div class = "form-group" style="max-width: 330px; margin: auto">
    {{ Form::submit('Pohrani', array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
</div>
{{ Form::close() }}
@endsection