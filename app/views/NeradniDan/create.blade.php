@extends('layouts.master')

@section('title')
@if(isset($neradniDan))
{{ $neradniDan->naziv }} - Uređivanje
@else
Dodavanje Neradnog Dana
@endif
@endsection

@section('content')
<?php
$optional = array('class' =>'form-control');
$required = array(
'class' =>'form-control',
'required' => 'required',
'autocomplete' => 'off');
?>
@if(isset($neradniDan))
{{ Form::model($neradniDan, array('route' => array('NeradniDan.update', $neradniDan->id),
'method' => 'put',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
<h2 class="form-heading">Uređivanje neradnog dana</h2>
@else
{{ Form::open(array('route' => 'NeradniDan.store',
'style' => 'max-width:330px;padding:15px;margin:auto'))}}
<h2 class="form-heading">Dodavanje neradnog dana</h2>
@endif
<div class = "form-group">
{{ Form::label('naziv') }}
{{ Form::text('naziv', null,
array(
'class' =>'form-control',
'autofocus' => 'autofocus',
'required' => 'required',
'autocomplete' => 'off')) }}
</div>
<div class = "form-group">
    <div class="row">
        <div class="col-xs-6">
            {{ Form::label('Dan') }}
            {{ Form::selectRange('dan',1,31, null, $required) }}
        </div>
        <div class="col-xs-6">
            {{ Form::label('Mjesec') }}
            {{ Form::selectRange('mjesec',1,12, null, $required) }}
        </div>
    </div>
</div>
<div class = "form-group">
    {{ Form::label('Godina') }}
    {{ Form::text('godina', null,
    $optional) }}
    <p><small>Nemojte unositi godinu ukoliko je neradni dan svake godine na isti datum</small></p>
</div>
<div class = "form-group">
{{ Form::submit('Pohrani', array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
</div>
{{ Form::close() }}
@endsection