@extends('layouts.master')

@section('title')
	@yield('naslov')
@endsection

@section('content')
<h2>
	@yield('naslov')
</h2>
<div class = "form-group">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
{{ Form::text('searchString', null,
array(
	'class' => 'form-control',
	'required' => 'required',
	'placeholder' => 'Pretraži',
	'autofocus' => 'autofocus',
	'autocomplete' => 'off',
	'style' => 'max-width:200px'
)) }}
</div>
<div id="collection-list">
@yield('list')
</div>
@yield('create_button')
{{ HTML::script('js/search.js') }}
@endsection