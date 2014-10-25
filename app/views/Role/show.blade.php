@extends('layouts.master')

@section('title')
Uloga {{ $role->ime }}
@endsection

@section('content')
<h2>Uloga {{ $role->ime }}</h2>
<div class = "row">
	<div class = "col-xs-12 col-sm-6">
		<div>
			{{ Form::label('Opis') }}
			<div>{{ $role->opis }}</div>
		</div>
		<div>
			<h3>Dozvole</h3>
			<ul>
				@foreach($role->permissions as $permission)
				<li>{{ $permission->ime }}</li>
				@endforeach
			</ul>
		</div>

		{{ Form::open(array('route' => array('Role.destroy', $role->id), 'method' => 'delete', 'class' => 'form')) }}
		{{ link_to_route('Role.edit', 'Uredi', array($role->id), array('class' => 'btn btn-default')) }}
		{{ Form::submit('Ukloni', array('class' => 'btn btn-warning')) }}
		{{ Form::close() }}
	</div>
	<div class = "col-xs-12 col-sm-6">
		<h3>Ovlaštene osobe <small>ukupno {{ $role->users->count() }}</small></h3>
		<table class = "table">
			<tbody>
				@foreach($role->users as $user)
				<tr>
					<td>{{ $user->link() }}<td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection