@extends('layouts.index')

@section('naslov')
Uloge
@endsection

@section('list')
{{ $list }}
@endsection


@section('create_button')
{{ link_to_route('Role.create', 'Dodaj ulogu', null, array('class' => 'btn btn-primary')) }}
@endsection