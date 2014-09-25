<div class="container">
<h1>Učionice</h1>
@if(Session::has('poruka'))
<div class="alert alert-warning">
{{ Session::get('poruka') }}
</div>
@endif
{{ Form::open(array('route' => array('Ucionica.search'), 'method' => 'get', 'class' => 'form', 'style' => 'max-width:200px;padding:15px')) }}
{{ Form::text('searchString', null,
array(
	'class' => 'form-control',
	'required' => 'required',
	'placeholder' => 'Pretraži',
	'autofocus' => 'autofocus'
)) }}
{{ Form::close() }}

@if($ucionice->count() > 0)
<table class="table table-striped">
	<tbody>
		<tr>
			<th>Naziv</th>
			<th>Adresa</th>
			<th>Najveći broj učenika</th>
		</tr>
@foreach($ucionice as $ucionica)
<tr>
	<td>
{{ link_to_route('Ucionica.show', $ucionica->naziv, $parameters = array($ucionica->id)) }}
	</td>
	<td>{{ $ucionica->adresa }}</td>
	<td>{{ $ucionica->max_broj_ucenika }}</td>
</tr>
@endforeach
</tbody>
</table>
{{ $ucionice->links() }}
@else
<div class="container">
<h3>Nema rezultata.</h3>
</div>
@endif
@if(Auth::user()->is_admin)
{{ link_to_route('Ucionica.create', 'Dodaj učionicu') }}
@endif
</div>