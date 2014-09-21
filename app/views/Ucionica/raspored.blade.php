@section('raspored')
<ul class="pager">
<li class="previous">{{ link_to_action('UcionicaController@showT', '<< Prethodni tjedan', array($ucionica->id, 'tjedan' => $tjedan-1, 'godina' => $godina), array('class' => 'btn btn-link navbar-left')) }}</li>
<li class="next">{{ link_to_action('UcionicaController@showT', 'Sljedeći tjedan >>', array($ucionica->id, 'tjedan' => $tjedan+1, 'godina' => $godina), array('class' => 'btn btn-link navbar-right')) }}</li>
</ul>
<div class="table-responsive">
<table class="table table-striped table-bordered table-condensed text-center">
<tbody>
	<tr>
		<th width='{{ 100/(count($grid)+1) }}%'>
			Vrijeme
		</th>
		@foreach($grid as $dan => $podatak)
		<th width='{{ 100/(count($grid)+1) }}%'>
			{{ $dan }}
		</th>
		@endforeach
	</tr>
	@for($i = $startHour*4; $i < ($endHour+1)*4; $i++)
	<tr height='1'>
		<?php
		if($i%4==0)
		{
			$key = ((int)($i/4)).':00';
			echo "<td rowspan='4'>$key</td>";
		}
		else $key = ((int)($i/4)).':'.($i%4*15);
		?>
		@foreach($grid as $podatak)
			@if(isset($podatak[$key]))
			<td rowspan='{{ $podatak[$key]['span'] }}'>
				@if(isset($podatak[$key]['rezervacija']))
@if(Auth::user()->is_admin||Auth::id() == $podatak[$key]['instruktor']->id)
				{{ link_to_action('RezervacijaController@show',
				$podatak[$key]['rezervacija']->predmet,
				array('id' => $podatak[$key]['rezervacija']->id)) }}<br>
@else
{{$podatak[$key]['rezervacija']->predmet}}<br>
@endif
				{{ link_to_action('InstruktorController@show',
				$podatak[$key]['instruktor']->name,
				array('id' => $podatak[$key]['instruktor']->id)) }}
				@endif
			</td>
			@endif
		@endforeach
	</tr>
	@endfor
</tbody>
</table>
</div>
@endsection