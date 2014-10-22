@section('raspored')
<ul class="pager">
<li class="previous">{{ link_to_route('Ucionica.raspored', '<< Prethodni tjedan', array($ucionica->id, 'tjedan' => $tjedan-1, 'godina' => $godina), array('class' => 'btn btn-link navbar-left')) }}</li>
<li class="next">{{ link_to_route('Ucionica.raspored', 'Sljedeći tjedan >>', array($ucionica->id, 'tjedan' => $tjedan+1, 'godina' => $godina), array('class' => 'btn btn-link navbar-right')) }}</li>
</ul>
<div class="table-responsive">
{{$strana_rasporeda}}
</div>
@endsection