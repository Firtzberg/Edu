@extends('layouts.index')

@section('naslov')
Uloge
@endsection

@section('list')
{{ $list }}
@endsection


@section('create_button')
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_ROLE))
{{ link_to_route('Role.create', 'Dodaj ulogu', null, array('class' => 'btn btn-primary')) }}
@endif
@endsection