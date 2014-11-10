@extends('layouts.index')

@section('naslov')
Učionice
@endsection

@section('list')
{{ $list }}
@endsection


@section('create_button')
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_UCIONICA))
{{ link_to_route('Ucionica.create', 'Dodaj učionicu', null, array('class' => 'btn btn-primary')) }}
@endif
@endsection