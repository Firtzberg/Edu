<h2>Klijent {{ $klijent->ime }}</h2>
<dl class = "dl-horizontal">
<dt>Broj mobitela</dt><dd>{{ $klijent->broj_mobitela }}</dd>
<dt>Ime i Prezime</dt><dd>{{ $klijent->ime }}</dd>
@if(!empty($klijent->email))
<dt>Email</dt><dd>{{ $klijent->email }}</dd>
@endif
@if(!empty($klijent->facebook))
<dt>Facebook</dt><dd>{{ $klijent->facebook }}</dd>
@endif

@if(Auth::user()->is_admin)
{{ link_to_route('Klijent.edit', 'Uredi', array($klijent->broj_mobitela), array('class' => 'btn btn-default')) }}
@endif