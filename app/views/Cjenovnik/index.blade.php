@extends('layouts.index')

@section('naslov')
Cjenovnici
@endsection

@section('list')
{{ $list }}
@endsection


@section('create_button')
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_CJENOVNIK))
{{ link_to_route('Cjenovnik.create', 'Dodaj cjenovnik', null, array('class' => 'btn btn-primary')) }}
@endif
@endsection