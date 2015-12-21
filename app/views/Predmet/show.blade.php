@extends('layouts.master')

@section('title')
Predmet {{ $predmet->ime }}
@endsection

@section('content')
{{ $predmet->kategorija->getBreadCrumbs() }}
<h2>{{ $predmet->ime }}</h2>
<h3>Cijene</h3>
<div id='cijene-list'>
    @foreach(Mjera::lists('znacenje', 'id') as $id => $znacenje)
    <div class = "container">
        <?php
        $cjenovnik = $predmet->cjenovnik($id);
        ?>
        @if($cjenovnik)
        Cjenovnik za {{ $znacenje }} je {{ $cjenovnik->link() }}.
        {{ View::make('Cjenovnik.table')->with('cjenovnik', $cjenovnik)->render() }}
        @else
        Cjenovnik za {{ $znacenje }} nije postavljen.
        @endif
    </div>
    @endforeach
</div>
@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_PREDMET_KATEGORIJA))
{{ link_to_route('Predmet.edit', 'Uredi', array('id' => $predmet->id), array('class' => 'btn btn-default')) }}
@endif
<h3>Predavači</h3>
<div clas="container">
    <div class = "row">
        @foreach($predmet->users as $user)
        <div class = "col-xs-6 col-sm-4 col-md-3 col-lg-2">{{ $user->link() }}</div>
        @endforeach
    </div>
</div>
@endsection