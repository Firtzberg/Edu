@extends('layouts.index')

@section('naslov')
Djelatnici
@endsection

@section('list')
{{ $list }}
@endsection


@section('create_button')
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_USER))
{{ link_to_route('Djelatnik.create', 'Dodaj djelatnika', null, array('class' => 'btn btn-primary')) }}
@endif
@endsection