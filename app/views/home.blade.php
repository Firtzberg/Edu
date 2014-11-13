@extends('layouts.master')

@section('heading')
{{ HTML::style('css/raspored.css') }}
@endsection

@section('title')
Početna
@endsection

@section('content')
<h2>Pregled zauzetosti svih učionoca</h2>
<ul class="pager">
<li class="previous">{{ link_to_route('home', '<< Prethodni dan', array('day' => $day-1, 'week' => $week, 'year' => $year), array('class' => 'btn btn-link navbar-left')) }}</li>
<li class="next">{{ link_to_route('home', 'Sljedeći dan >>', array('day' => $day+1, 'week' => $week, 'year' => $year), array('class' => 'btn btn-link navbar-right')) }}</li>
</ul>
<div id="strana-rasporeda">
{{ \Helpers\Raspored::RasporedForDay($day, $week, $year) }}
</div>
@endsection