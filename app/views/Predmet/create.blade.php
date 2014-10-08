<?php
$required = array(
'class' =>'form-control',
'required' => 'required');
$requiredPositive = array(
'class' =>'form-control',
'required' => 'required',
'min' => 0)
?>


@if(isset($predmet))
{{ Form::model($predmet, array('route' => array('Predmet.update', $predmet->id),
'method' => 'put')) }}
<h2 class="form-heading">Uređivanje predmeta</h2>
@else
{{ Form::open(array('route' => 'Predmet.store'))}}
<h2 class="form-heading">Dodavanje predmeta</h2>
@endif

<div class="form-group">
	{{ Form::label('Ime') }}
	{{ Form::text('ime', null, $required) }}
</div>

@if(!isset($predmet))
{{ Form::hidden('kategorija_id', $kategorija_id) }}
@endif

<h3>Cijene</h3>
<div id='cijene-list' class="container">
	<table class="table"><tbody>
		<tr>
			<th>Mjera</th>
			<th>Individualno</th>
			<th>Popust po dodatnoj osobi</th>
			<th>Minimalno</th>
		</tr>
		@if(isset($predmet))
		@foreach($predmet->cijene as $cijena)
		<tr>
			<td>{{ $cijena->znacenje }}</td>
			<td>{{ Form::input('number', 'individualno-cijena-'.$cijena->id, $cijena->pivot->individualno, $requiredPositive) }}</td>
			<td>{{ Form::input('number', 'popust-cijena-'.$cijena->id, $cijena->pivot->popust, $requiredPositive) }}</td>
			<td>{{ Form::input('number', 'minimalno-cijena-'.$cijena->id, $cijena->pivot->minimalno, $requiredPositive) }}</td>
		</tr>
		@endforeach
		@else
		@foreach(Mjera::all() as $mjera)
		<tr>
			<td>{{ $mjera->znacenje }}</td>
			<td>{{ Form::input('number', 'individualno-cijena-'.$mjera->id, 0, $requiredPositive) }}</td>
			<td>{{ Form::input('number', 'popust-cijena-'.$mjera->id, 0, $requiredPositive) }}</td>
			<td>{{ Form::input('number', 'minimalno-cijena-'.$mjera->id, 0, $requiredPositive) }}</td>
		</tr>
		@endforeach
		@endif
	</tbody></table>
</div>
{{ Form::submit('Pohrani', array('class' => 'btn btn-primary')) }}
{{ Form::close() }}