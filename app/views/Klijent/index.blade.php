<h2>Klijenti</h2>
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
{{ link_to_route('Klijent.create', 'Dodaj klijenta', null, array('class' => 'btn btn-secondary')) }}
@endif
{{ HTML::script('js/search.js') }}