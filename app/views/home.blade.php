@extends('layouts.master')

@section('heading')
{{ HTML::style('css/raspored.css') }}
@endsection

@section('title')
Početna
@endsection

@section('content')
<h2>Pregled zauzetosti učionoca</h2>
<ul class="pager">
<li class="previous">{{ link_to_route('home.raspored', '<< Prethodni dan', array('day' => $day-1, 'week' => $week, 'year' => $year), array('class' => 'btn btn-link navbar-left')) }}</li>
<li class="next">{{ link_to_route('home.raspored', 'Sljedeći dan >>', array('day' => $day+1, 'week' => $week, 'year' => $year), array('class' => 'btn btn-link navbar-right')) }}</li>
</ul>
<div id="strana-rasporeda">
{{ \Helpers\Raspored::RasporedForDay($day, $week, $year) }}
</div>
<div>
    <h3>KONTAKTI SURADNIKA</h3>
    <div class="container">
    <div class="row">
    <?php
    $djelatnici = User::orderBy('name')->get();
    ?>
    @foreach($djelatnici as $djelatnik)
    <div class="col-lg-3 col-md-4 col-sm-12">
        <span style="font-weight: bold">{{$djelatnik->name}}</span> - {{$djelatnik->broj_mobitela}}
    </div>
    @endforeach
    </div>
    </div>
</div>
@endsection