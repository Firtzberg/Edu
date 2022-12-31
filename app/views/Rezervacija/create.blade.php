@extends('layouts.master')

@section('title')
@if(isset($rezervacija))
Uređivanje rezervacije
@else
Rezerviranje
@endif
@endsection

@section('content')
<?php
$optional = array('class' => 'form-control');
$required = array(
    'class' => 'form-control',
    'required' => 'required');
$requiredPositive = array(
    'class' => 'form-control',
    'required' => 'required',
    'min' => 1);
$tecaj = false;
if(isset($rezervacija->tecaj)){
    $tecaj = $rezervacija->tecaj;
}
?>
<script type="text/javascript">
    var isTecajHTML = '';
    function onTecajChanged() {
        var tecaj = jQuery('select.form-control[name=tecaj]').val();
        var isTecaj = jQuery('div#is-tecaj');
        if(tecaj === '1') {
            isTecaj.html(isTecajHTML);
            isTecaj.show();
            jQuery('div#is-not-tecaj').hide();
            klijentManager.term();
        } else {
            isTecajHTML = isTecaj.html();
            isTecaj.hide();
            isTecaj.empty();
            jQuery('div#is-not-tecaj').show();
            klijentManager.init([]);
        }
    }
    jQuery(function () {
        var tecaj = jQuery('select.form-control[name=tecaj]').val();
        var isTecaj = jQuery('div#is-tecaj');
        if(tecaj === '1') {
            jQuery('div#is-not-tecaj').hide();
            klijentManager.term();
        } else {
            isTecajHTML = isTecaj.html();
            isTecaj.empty();
            isTecaj.hide();
        }
    });
</script>
<h2 class="form-heading">Rezerviranje</h2>
@if(isset($rezervacija))
@if(isset($rezervacija->id))
{{ Form::model($rezervacija, array('route' => array('Rezervacija.update', $rezervacija->id),
'method' => 'put')) }}
@else
{{ Form::model($rezervacija, array('route' => 'Rezervacija.store')) }}
@endif
@else
{{ Form::open(array('route' => 'Rezervacija.store')) }}
@endif
{{ HTML::script('js/errorManager1.js') }}
<?php $value = null; ?>
<div class = "row">
    <div class = "col-xs-12 col-sm-5 col-lg-3">
        <div class = "form-group">
            <?php
            if (isset($rezervacija))
                $value = date('Y-m-d', strtotime($rezervacija->pocetak_rada));
            else
                $value = date('Y-m-d');
            $attributes = array(
                'class' => 'form-control',
                'required' => 'required');
            if (!Auth::user()->hasPermission(Permission::PERMISSION_EDIT_STARTED_REZERVACIJA))
                $attributes['min'] = date('Y-m-d');
            ?>
            {{ Form::label('Datum') }}
            {{ Form::input('date', 'datum', $value, $attributes) }}
        </div>
        <div class = "form-group">
            {{ Form::label('Vrijeme početka') }}
            <div class="row">
                <div class="col-xs-6">
                    <?php
                    if (isset($rezervacija))
                        $value = date('H', strtotime($rezervacija->pocetak_rada));
                    else
                        $value = 8;
                    ?>
                    {{ Form::selectRange('startHour', BaseController::START_HOUR, BaseController::END_HOUR, $value,
				$required) }}</div>
                <div class="col-xs-6">
                    <?php
                    if (isset($rezervacija))
                        $value = date('i', strtotime($rezervacija->pocetak_rada));
                    else
                        $value = 0;
                    ?>
                    {{ Form::select('startMinute', array(0 => '00', 15 => '15', 30 => '30', 45 => '45'), $value,
				$required) }}</div>
            </div>
        </div>
        <div class = "form-group">
            <?php
            $rows = Mjera::select('id', 'simbol')->get();
            $mjere = array();
            foreach ($rows as $row) {
                $mjere[$row->id] = $row->simbol;
            }
            ?>
            {{ Form::label('Trajanje') }}
            <div class="row">
                <div class="col-xs-6">
                    <?php
                    if (isset($rezervacija))
                        $value = $rezervacija->kolicina;
                    else
                        $value = 1;
                    ?>
                    {{ Form::input('number', 'kolicina', $value,
				$requiredPositive) }}</div>
                <div class="col-xs-6">
                    {{ Form::select('mjera_id', $mjere, null,
				$required) }}</div>
            </div>
        </div>
        <div class = "form-group">
{{ Form::label('Vrijeme završetka') }}
            <div class="row">
                <div class="col-xs-6">
                    <?php
                    if (isset($rezervacija))
                        $value = date('H', strtotime($rezervacija->kraj_rada));
                    else
                        $value = 8;
                    ?>
                    {{ Form::selectRange('endHour', BaseController::START_HOUR, BaseController::END_HOUR, $value,
				$required) }}</div>
                <div class="col-xs-6">
                    <?php
                    if (isset($rezervacija))
                        $value = date('i', strtotime($rezervacija->kraj_rada));
                    else
                        $value = 0;
                    ?>
                    {{ Form::select('endMinute', array(0 => '00', 15 => '15', 30 => '30', 45 => '45'), $value,
				$required) }}</div>
            </div>
        </div>
    </div>
    <div class = "col-xs-12 col-sm-7 col-lg-9 row">
        <div class = "col-xs-12 col-lg-7">
            <div class = "form-group">
                <?php
                $rows = Ucionica::select('id', 'naziv', 'max_broj_ucenika')->get();
                $ucionice = array();
                foreach ($rows as $row) {
                    $ucionice[$row->id] = $row->naziv . '(' . $row->max_broj_ucenika . ')';
                }
                if(!isset($local_ucionica_id)){
                    $local_ucionica_id = null;
                }
                ?>
                {{ Form::label('Učionica') }}
                {{ Form::select('ucionica_id', $ucionice, $local_ucionica_id,
			$required) }}
            </div>
            <div id="is-tecaj" class="form-group">
                {{ Form::label('Broj polaznika') }}
                {{ Form::input('number', 'tecaj_broj_polaznika', null, $requiredPositive) }}
            </div>
            <div id="is-not-tecaj">
                @yield('klijent-input')
            </div>
        </div>
        <div class = "col-xs-12 col-lg-5">
            {{ Form::hidden('instruktor_id', $instruktor->id) }}
            @if($instruktor->hasPermission(\Permission::PERMISSION_TECAJ))
                <div class = "form-group">
                    {{ Form::label('Tečaj') }}
                    {{ Form::select('tecaj', array(0 => 'NE', 1 => 'DA'), $tecaj,
                                array('class' => 'form-control',
                        'required' => 'required', 'onchange' => 'onTecajChanged()')) }}
                </div>
            @else
                {{ Form::hidden('tecaj', 0) }}
            @endif
            @yield('predmet-select')
            <div class = "form-group">
                {{ Form::label('Napomena') }}
                {{ Form::textarea('napomena', null, array('class' =>'form-control', 'rows' => 2)) }}
            </div>
        </div>
    </div>
</div>
<div class = "form-group">
    <?php
    if (isset($rezervacija->id))
        $value = 'Promijeni';
    else
        $value = 'Rezerviraj';
    ?>
    {{ Form::submit($value, array(
	'class' => 'btn btn-lg btn-primary btn-block',
	'style' => 'margin:auto;max-width:330px')) }}
</div>
{{ Form::close() }}
@endsection