@extends('layouts.master')

@section('title')
@if(isset($instruktor))
{{ $instruktor->name }} - Uređivanje
@else
Dodavanje instruktora
@endif
@endsection

@section('content')
<?php
$optional = array('class' =>'form-control');
$required = array(
'class' =>'form-control',
'required' => 'required');
?>
@if(isset($instruktor))
{{ Form::model($instruktor, array('route' => array('Instruktor.update', $instruktor->id),
'method' => 'put',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
<h2 class="form-heading">Uređivaje instruktora</h2>
@else
{{ Form::open(array('route' => 'Instruktor.store',
'style' => 'max-width:330px;padding:15px;margin:auto'))}}
<h2 class="form-heading">Dodavanje instruktora</h2>
@endif
<div class = "form-group">
{{ Form::label('Ime') }}
@if(Auth::user()->is_admin)
{{ Form::text('name', null,
array(
'class' =>'form-control',
'autofocus' => 'autofocus',
'required' => 'required',
'autocomplete' => 'off')) }}
@else
<br>{{ Form::label($instruktor->name)}}
@endif
</div>
<?php $color = '#ffffff';?>
@if(!isset($instruktor))
<div class = "form-group">
{{ Form::label('Zaporka') }}
{{ Form::password('lozinka',
$required) }}
{{ Form::label('Ponovljena zaporka') }}
{{ Form::password('ponovljena',
$required) }}
</div>
@else
<?php $color = '#'.$instruktor->boja;?>
@endif
<div class = "form-group">
{{ Form::label('Uloga') }}
{{ Form::select('role_id', Role::select('ime')->get()->lists('ime'),null,
$required) }}
</div>
<div class = "form-group">
{{ Form::label('Boja') }}
{{ Form::input('color', 'boja', $color,
$required) }}
</div>
<div class = "form-group">
{{ Form::label('broj_mobitela') }}
{{ Form::text('broj_mobitela', null,
$optional) }}
</div>
<div class = "form-group">
{{ Form::label('Email') }}
{{ Form::email('email', null,
$optional) }}
</div>
<div class = "form-group">
{{ Form::submit('Pohrani', array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
</div>
{{ Form::close() }}
@endsection