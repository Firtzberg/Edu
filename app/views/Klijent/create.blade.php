@extends('layouts.master')

@section('title')
@if(isset($klijent))
{{ $klijent->ime }} - Uređivanje
@else
Dodavanje Klijenta
@endif
@endsection

@section('content')
<?php
$optional = array('class' =>'form-control');
$required = array(
'class' =>'form-control',
'required' => 'required',
'autocomplete' => 'off');
$mobAttributes = array(
'class' =>'form-control',
'required' => 'required');
?>
@if(isset($klijent))
{{ Form::model($klijent, array('route' => array('Klijent.update', $klijent->broj_mobitela),
'method' => 'put',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
<?php $mobAttributes['readonly'] = 'readonly';?>
<h2 class="form-heading">Uređivanje klijenta</h2>
@else
<?php $mobAttributes['autofocus'] = 'autofocus'?>
{{ Form::open(array('route' => 'Klijent.store',
'style' => 'max-width:330px;padding:15px;margin:auto'))}}
<h2 class="form-heading">Dodavanje klijenta</h2>
@endif
<div class = "form-group">
{{ Form::label('broj_mobitela') }}
{{ Form::text('broj_mobitela', null,
$mobAttributes) }}
</div>
<div class = "form-group">
{{ Form::label('Ime i Prezime') }}
{{ Form::text('ime', null,
$required) }}
</div>
<div class = "form-group">
{{ Form::label('Email') }}
{{ Form::text('email', null,
$optional) }}
</div>
<div class = "form-group">
{{ Form::label('Facebook link') }}
{{ Form::text('facebook', null,
$optional) }}
</div>
<div class = "form-group">
{{ Form::submit('Pohrani', array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
</div>
{{ Form::close() }}
@endsection