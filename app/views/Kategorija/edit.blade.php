@extends('layouts.master')

@section('title')
{{ $kategorija->naziv }} - Uređivanje
@endsection

@section('content')
{{ Form::model($kategorija, array('route' => array('Kategorija.update', $kategorija->id),
'method' => 'put',
'style' => 'max-width:330px;padding:15px;margin:auto')) }}
<h2 class="form-heading">Uređivanje kategorije</h2>
<div class = "form-group">
{{ Form::label('ime') }}
{{ Form::text('ime', null,
array(
'class' =>'form-control',
'autofocus' => 'autofocus',
'required' => 'required',
'autocomplete' => 'off')) }}
</div>
<div class = "form-group">
{{ Form::submit('Pohrani', array(
'class' => 'btn btn-lg btn-primary btn-block')) }}
</div>
{{ Form::close() }}
@endsection