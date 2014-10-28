@extends('layouts.master')

@section('title')
Predmet {{ $predmet->ime }}
@endsection

@section('content')
{{ $predmet->kategorija->getBreadCrumbs() }}
<h2>{{ $predmet->ime }}</h2>
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
@if(Auth::user()->is_admin)
<h3>Predavači</h3>
<div clas="container">
	<div class = "row">
		@foreach($predmet->users as $user)
			<div class = "col-xs-6 col-sm-4 col-md-3 col-lg-2">{{ $user->link() }}</div>
		@endforeach
	</div>
</div>
@endif
@endsection