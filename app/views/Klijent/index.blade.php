@extends('layouts.index')

@section('naslov')
Klijenti
@endsection

@section('list')
{{ $list }}
@endsection


@section('create_button')
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_KLIJENT))
{{ link_to_route('Klijent.create', 'Dodaj klijenta', null, array('class' => 'btn btn-primary')) }}
@endif
@endsection