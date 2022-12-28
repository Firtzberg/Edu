@extends('layouts.index')

@section('naslov')
Neradni Dani
@endsection

@section('list')
{{ $list }}
@endsection


@section('create_button')
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_NERADNI_DAN))
{{ link_to_route('NeradniDan.create', 'Dodaj neradni dan', null, array('class' => 'btn btn-primary')) }}
@endif
@endsection