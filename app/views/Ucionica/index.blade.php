<h1>Učionice</h1>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
{{ Form::text('searchString', null,
array(
	'class' => 'form-control',
	'required' => 'required',
	'placeholder' => 'Pretraži',
	'autofocus' => 'autofocus',
	'autocomplete' => 'off',
	'style' => 'max-width:200px;padding:15px'
)) }}

<div id="collection-list">
@yield('list')
</div>
@if(Auth::user()->is_admin)
{{ link_to_route('Ucionica.create', 'Dodaj učionicu') }}
@endif
{{ HTML::script('js/search.js') }}