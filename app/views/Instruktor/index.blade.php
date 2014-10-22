@extends('layouts.index')

@section('naslov')
Instruktori
@endsection

@section('list')
{{ $list }}
@endsection


@section('create_button')
{{ link_to_route('Instruktor.create', 'Dodaj instruktora', null, array('class' => 'btn btn-primary')) }}
@endsection