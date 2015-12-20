@extends('layouts.master')

@section('title')
Cjenovnik {{ $cjenovnik->ime }}
@endsection

@section('content')
<h2>Cjenovnik {{ $cjenovnik->ime }}</h2>
<div class = "row">
    <div class = "col-xs-12">
        <div>
            {{ Form::label('Opis') }}
            <div>{{ $cjenovnik->opis }}</div>
        </div>
        <div>
            {{ View::make('Cjenovnik.table')->with('cjenovnik', $cjenovnik)->render() }}
        </div>
        @if(Auth::user()->hasPermission(Permission::PERMISSION_REMOVE_CJENOVNIK))
        {{ Form::open(array('route' => array('Cjenovnik.destroy', $cjenovnik->id), 'method' => 'delete', 'class' => 'form')) }}
        @endif
        @if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_CJENOVNIK))
        {{ link_to_route('Cjenovnik.edit', 'Uredi', array($cjenovnik->id), array('class' => 'btn btn-default')) }}
        @endif
        @if(Auth::user()->hasPermission(Permission::PERMISSION_REMOVE_CJENOVNIK))
        {{ Form::submit('Ukloni', array('class' => 'btn btn-warning')) }}
        {{ Form::close() }}
        @endif
    </div>
    <div class = "col-xs-12">
        <h3>Predmeti koji koriste cjenovnik <small>ukupno {{ $cjenovnik->c_m_p->count() }}</small></h3>
        <table class = "table table-striped">
            <tbody>
                <?php
                $mjere = Mjera::get()->lists('simbol', 'id');
                ?>
                @foreach($cjenovnik->c_m_p as $predmet)
                <tr>
                    <td>{{ $predmet->link() }}<td>
                    <td>{{ $mjere[$predmet->pivot->mjera_id] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection