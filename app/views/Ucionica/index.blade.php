@extends('layouts.index')

@section('naslov')
Učionice
@endsection

@section('list')
{{ $list }}
@endsection


@section('create_button')
{{ link_to_route('Ucionica.create', 'Dodaj učionicu', null, array('class' => 'btn btn-primary')) }}
@endsection