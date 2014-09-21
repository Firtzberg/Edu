<div class = "container">
@if(Session::has('poruka'))
<div class="alert alert-warning">
{{Session::get('poruka')}}
</div>
@endif
<h2>Instruktor {{ $instruktor->name }}</h2>
<dl class="dl-horizontal">
@if(!empty($instruktor->broj_mobitela))
<dt>Broj mobitela</dt><dd> {{ $instruktor->broj_mobitela }}</dd>
@endif
@if(!empty($instruktor->email))
<dt>Email</dt><dd> {{ $instruktor->email}}</dd>
@endif
</dl>

@yield('raspored')
{{ Form::open(array('action' => array('InstruktorController@destroy', $instruktor->id), 'method' => 'delete', 'class' => 'form')) }}
@if(Auth::user()->is_admin || Auth::id() == $instruktor->id)
{{ link_to_action('InstruktorController@edit', 'Uredi', array($instruktor->id), array('class' => 'btn btn-default')) }}
 {{ link_to_action('IzvjestajController@tjedni_izvjestaj', 'Izvjestaj', array($instruktor->id), array('class' => 'btn btn-default')) }}
 {{ link_to_action('InstruktorController@changePassword', 'Promijeni zaporku', array($instruktor->id), array('class' => 'btn btn-default')) }} 
@endif
@if(Auth::user()->is_admin&& !$instruktor->is_admin)
{{ Form::submit('Ukloni', array(
'class' => 'btn btn-warning')) }}
@endif
{{ Form::close() }}
</div>