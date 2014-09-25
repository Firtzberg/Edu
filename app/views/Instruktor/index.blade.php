<div class = "container"><h2>Instruktori</h2>
@if(Session::has('poruka'))
{{ Session::get('poruka') }}<br><br>
@endif
{{ Form::open(array('route' => array('Instruktor.search'), 'method' => 'get', 'class' => 'form', 'style' => 'max-width:200px;padding:15px')) }}
{{ Form::text('searchString', null,
array(
	'class' => 'form-control',
	'required' => 'required',
	'placeholder' => 'Pretraži',
	'autofocus' => 'autofocus'
)) }}
{{ Form::close() }}

@if($instruktori->count() > 0)
<table class="table">
<tbody>
	<tr>
		<th>Ime</th>
	</tr>
@foreach($instruktori as $i)
<tr>
	<td>
		{{ link_to_route('Instruktor.show', $i->name, $parameters = array($i->id)) }}
	</td>
</tr>
@endforeach
</tbody>
</table>
{{ $instruktori->links() }}
@else
<div class="container">
<h3>Nema rezultata.</h3>
</div>
@endif
@if(Auth::user()->is_admin)
{{ link_to_route('Instruktor.create', 'Dodaj instruktora', null, array('class' => 'btn btn-secondary')) }}
@endif
</div>