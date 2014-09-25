<div class = "container"><h2>Klijenti</h2>
@if(Session::has('poruka'))
{{ Session::get('poruka') }}<br><br>
@endif
{{ Form::open(array('route' => array('Klijent.search'), 'method' => 'get', 'class' => 'form', 'style' => 'max-width:200px;padding:15px')) }}
{{ Form::text('searchString', null,
array(
	'class' => 'form-control',
	'required' => 'required',
	'placeholder' => 'Pretraži',
	'autofocus' => 'autofocus'
)) }}
{{ Form::close() }}

@if($klijenti->count() > 0)
<table class="table">
<tbody>
	<tr>
		<th>Ime</th>
	</tr>
@foreach($klijenti as $k)
<tr>
	<td>
		{{ link_to_route('Klijent.show', $k->ime, array($k->broj_mobitela)) }}
	</td>
</tr>
@endforeach
</tbody>
</table>
{{ $klijenti->links() }}
@else
<div class="container">
<h3>Nema rezultata.</h3>
</div>
@endif
@if(Auth::user()->is_admin)
{{ link_to_route('Klijent.create', 'Dodaj klijenta', null, array('class' => 'btn btn-secondary')) }}
@endif
</div>