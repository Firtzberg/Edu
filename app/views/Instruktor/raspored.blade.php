<ul class="pager">
<li class="previous">{{ link_to_route('Instruktor.raspored', '<< Prethodni tjedan', array($instruktor->id, 'tjedan' => $tjedan-1, 'godina' => $godina), array('class' => 'btn btn-link navbar-left')) }}</li>
<li class="next">{{ link_to_route('Instruktor.raspored', 'Sljedeći tjedan >>', array($instruktor->id, 'tjedan' => $tjedan+1, 'godina' => $godina), array('class' => 'btn btn-link navbar-right')) }}</li>
</ul>
{{ $strana_rasporeda }}