@extends('layouts.master')

@section('title')
Kategorija {{ $kategorija->ime }}
@endsection

@section('content')
<?php $requiredName = array(
'class' => 'form-control',
'required' => 'required',
'autocomplete' => 'off',
'placeholder' => 'Ime')?>
{{ $kategorija->getBreadCrumbs() }}
<h2>{{ $kategorija->ime }}</h2>
<h3>Podkategorje</h3>
<div id="list-podkategorije" class="container">
	@if($kategorija->podkategorije->count() < 1)
	<p>Nema podkategorija.</p>
	@else
	<table id="podkategorije-table" class="table"><tbody>
		<tr>
			<th>Ime podkategorije</th>
			<th></th>
		</tr>
		@foreach($kategorija->podkategorije as $podkategorija)
		<tr>
			<td>{{ $podkategorija->link() }}</td>
			<td>
				{{ Form::open(array('route' => array('Kategorija.destroy', 'id' => $podkategorija->id), 'method' => 'delete')) }}
				{{ Form::submit('Izbriši', array('class' => 'btn btn-danger')) }}
				{{ Form::close() }}
			</td>
		</tr>
		@endforeach
	</tbody></table>
	@endif
	{{ Form::open(array('route' => 'Kategorija.store', 'class' => 'form-inline')) }}
	{{ Form::hidden('nadkategorija_id', $kategorija->id) }}
	<p>Dodaj novu podkategoriju</p>
	{{ Form::text('ime', null, $requiredName) }}
	{{ Form::submit('Dodaj', array('class' => 'btn btn-primary')) }}
	{{ Form::close() }}
</div>
<h3>Predmeti</h3>
<div id="list-predmeti" class="container">
	@if($kategorija->predmeti->count() < 1)
	<p>Nema predmeta.</p>
	@else
	<table class="table"><tbody>
		<tr>
			<th>Ime predmeta</th>
			<th></th>
		</tr>
		@foreach($kategorija->predmeti as $predmet)
		<tr>
			<td>{{ $predmet->link() }}</td>
			<td>
				{{ Form::open(array('route' => array('Predmet.destroy', 'id' => $predmet->id), 'method' => 'delete')) }}
				{{ Form::submit('Izbriši', array('class' => 'btn btn-danger')) }}
				{{ Form::close() }}
			</td>
		</tr>
		@endforeach
	</tbody></table>
	@endif
	{{ link_to_route('Predmet.create', 'Dodaj predmet', array('kategorija_id' => $kategorija->id), array('class' => 'btn btn-primary'))}}
</div>
@endsection