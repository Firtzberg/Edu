@extends('layouts.index')

@section('naslov')
Instruktori
@endsection

@section('list')
{{ $list }}
@endsection


@section('create_button')
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_USER))
{{ link_to_route('Instruktor.create', 'Dodaj instruktora', null, array('class' => 'btn btn-primary')) }}
@endif
@endsection