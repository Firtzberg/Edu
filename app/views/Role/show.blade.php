@extends('layouts.master')

@section('title')
Uloga {{ $role->ime }}
@endsection

@section('content')
<h2>Uloga {{ $role->ime }}</h2>
<div class = "row">
    <div class = "col-xs-12 col-sm-6">
        <div>
            {{ Form::label('Opis') }}
            <div>{{ $role->opis }}</div>
        </div>
        <div>
            <h3>Dozvole</h3>
            <table class = "table table-striped">
                <tbody>
                    @foreach($role->permissions as $permission)
                    <tr>
                        <td>{{ $permission->ime }}<td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(Auth::user()->hasPermission(Permission::PERMISSION_REMOVE_ROLE))
        {{ Form::open(array('route' => array('Role.destroy', $role->id), 'method' => 'delete', 'class' => 'form')) }}
        @endif
        @if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_ROLE))
        {{ link_to_route('Role.edit', 'Uredi', array($role->id), array('class' => 'btn btn-default')) }}
        @endif
        @if(Auth::user()->hasPermission(Permission::PERMISSION_REMOVE_ROLE))
        {{ Form::submit('Ukloni', array('class' => 'btn btn-warning')) }}
        {{ Form::close() }}
        @endif
    </div>
    <div class = "col-xs-12 col-sm-6">
        <h3>Ovlaštene osobe <small>ukupno {{ $role->users->count() }}</small></h3>
        <table class = "table table-striped">
            <tbody>
                @foreach($role->users as $user)
                <tr>
                    <td>{{ $user->link() }}<td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection