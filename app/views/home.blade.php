@extends('layouts.master')

@section('heading')
{{ HTML::style('css/raspored.css') }}
@endsection

@section('title')
Početna
@endsection

@section('content')
<h2>Početna</h2>
{{ \Helpers\Raspored::RasporedForDay(3, 46, 2014) }}
@endsection