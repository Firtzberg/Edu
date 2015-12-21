@extends('layouts.master')

@section('title')
@if(isset($predmet))
{{ $predmet->ime }} - Uređivanje
@else
Dodavanje Predmeta
@endif
@endsection

@section('content')
<?php
$required = array(
'class' =>'form-control',
'required' => 'required',
'style' => 'max-width:200px');
$requiredPositive = array(
'class' =>'form-control',
'required' => 'required',
'min' => 0)
?>
@if(isset($predmet))
{{ $predmet->kategorija->getBreadCrumbs() }}
@else
{{ Kategorija::find($kategorija_id)->getBreadCrumbs() }}
@endif

@if(isset($predmet))
{{ Form::model($predmet, array('route' => array('Predmet.update', $predmet->id),
'method' => 'put')) }}
<h2 class="form-heading">Uređivanje predmeta</h2>
@else
{{ Form::open(array('route' => 'Predmet.store'))}}
<h2 class="form-heading">Dodavanje predmeta</h2>
@endif

<div class="form-group">
	{{ Form::label('Ime') }}
	{{ Form::text('ime', null, $required) }}
</div>

@if(!isset($predmet))
{{ Form::hidden('kategorija_id', $kategorija_id) }}
@endif

<h3>Cijene</h3>
<div id='cijene-list' class="container">
    <?php
    $mjere = Mjera::lists('znacenje', 'id');
    $cjenovnici = Cjenovnik::lists('ime', 'id');
    ?>
    @foreach($mjere as $id => $znacenje)
    <div class="form-group">
        <?php
        $cjenovnik = null;
        $cjenovnik_id = null;
        if (isset($predmet)) {
            $cjenovnik = $predmet->cjenovnik($id);
            if ($cjenovnik) {
                $cjenovnik_id = $cjenovnik->id;
            }
        }
        $cjenovnik_id = Input::old("cjenovnik_id_$id", $cjenovnik_id);
        if ($cjenovnik_id) {
            $cjenovnik = Cjenovnik::find($cjenovnik_id);
        }
        else {
            $cjenovnik = null;
        }
        ?>
        @if($cjenovnik)
        Cjenovnik za <strong>{{ $znacenje }}</strong> {{ Form::select("cjenovnik_id_$id", $cjenovnici, $cjenovnik->id, $required) }}
        <div class="cjenovnik_table">
            {{ View::make('Cjenovnik.table')->with('cjenovnik', $cjenovnik)->render() }}
        </div>
        @else
        Cjenovnik za <strong>{{ $znacenje }}</strong> {{ Form::select("cjenovnik_id_$id", $cjenovnici, null, $required) }}
        <div class="cjenovnik_table"></div>
        @endif
    </div>
    @endforeach
    {{ HTML::script('js/Predmet/cjenovnikUpdater.js') }}
</div>
<div class="form-group">
{{ Form::submit('Pohrani', array('class' => 'btn btn-primary')) }}
</div>
<div class ="form-group">
	<h3>Predavači</h3>
	<div class="container">
	<div class="row">
                <?php $users = User::withPermission(Permission::PERMISSION_OWN_REZERVACIJA_HANDLING)
                        ->orderBy('name')->get();
		$allowed = array();
		if(isset($predmet))$allowed = $predmet->users->lists('id');
		$value = false;?>
		@foreach($users as $user)
			<?php if(isset($predmet))$value = in_array($user->id, $allowed);?>
			<div class = "col-xs-6 col-sm-4 col-md-3 col-lg-2">
				<div class="checkbox">
				<label>
					{{ Form::checkbox('allowed[]', $user->id, $value) }} {{ $user->name }}
				</label>
				</div>
			</div>
		@endforeach
	</div>
</div>
</div>
{{ Form::close() }}
@endsection