{{ $predmet->nadkategorija->getBreadCrumbs() }}
<h2>{{ $predmet->ime }}</h2>
<p>Predmet je 
@if($predmet->enabled)
vidljiv
@else
skriven
@endif
.
@if($predmet->enabled)
{{ Form::open(array('route' => array('Predmet.disable', 'id' => $predmet->id))) }}
{{ Form::submit('Sakrij', array('class' => 'btn btn-default')) }}
@else
{{ Form::open(array('route' => array('Predmet.enable', 'id' => $predmet->id))) }}
{{ Form::submit('Omogući', array('class' => 'btn btn-default')) }}
@endif
{{ Form::close() }}
</p>
<h3>Cijene</h3>
<div id='cijene-list' class="container">
	<table class="table"><tbody>
		<tr>
			<th>Mjera</th>
			<th>Individualno</th>
			<th>Popust po dodatnoj osobi</th>
			<th>Minimalno</th>
		</tr>
		@foreach($predmet->cijene as $cijena)
		<tr>
			<td>{{ $cijena->znacenje }}</td>
			<td>{{ $cijena->pivot->individualno }}</td>
			<td>{{ $cijena->pivot->popust }}</td>
			<td>{{ $cijena->pivot->minimalno }}</td>
		</tr>
		@endforeach
	</tbody></table>
</div>
{{ link_to_route('Predmet.edit', 'Uredi', array('id' => $predmet->id), array('class' => 'btn btn-default')) }}