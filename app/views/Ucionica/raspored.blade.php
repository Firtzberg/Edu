@section('raspored')
<ul class="pager">
<li class="previous">{{ link_to_route('Ucionica.raspored', '<< Prethodni tjedan', array($ucionica->id, 'tjedan' => $tjedan-1, 'godina' => $godina), array('class' => 'btn btn-link navbar-left')) }}</li>
<li class="next">{{ link_to_route('Ucionica.raspored', 'Sljedeći tjedan >>', array($ucionica->id, 'tjedan' => $tjedan+1, 'godina' => $godina), array('class' => 'btn btn-link navbar-right')) }}</li>
</ul>
<div class="table-responsive">
<table class="table table-striped table-bordered table-condensed text-center">
<tbody>
	<tr>
		<th width='{{ 100/(count($grid)+2) }}%'>
			Vrijeme
		</th>
		@foreach($grid as $dan => $podatak)
		<th width='{{ 100/(count($grid)+2) }}%'>
			{{ $dan }}
		</th>
		@endforeach
		<th width='{{ 100/(count($grid)+2) }}%'>
			Vrijeme
		</th>
	</tr>
	@for($i = $startHour*4; $i < ($endHour+1)*4; $i++)
	<tr height='1'>
		<?php
		if($i%4==0)
		{
			$key = ((int)($i/4)).':00';
			echo "<td rowspan='4'>$key</td>";
		}
		else $key = ((int)($i/4)).':'.($i%4*30);
		?>
		@foreach($grid as $podatak)
			@if(isset($podatak[$key]))
			<td rowspan='{{ $podatak[$key]['span'] }}'>
				@if(isset($podatak[$key]['rezervacija']))
@if(Auth::user()->is_admin||Auth::id() == $podatak[$key]['instruktor']->id)
				{{ link_to_route('Rezervacija.show',
				$podatak[$key]['rezervacija']->predmet,
				array('id' => $podatak[$key]['rezervacija']->id)) }}<br>
@else
{{$podatak[$key]['rezervacija']->predmet}}<br>
@endif
				{{ link_to_route('Instruktor.show',
				$podatak[$key]['instruktor']->name,
				array('id' => $podatak[$key]['instruktor']->id)) }}
				@endif
			</td>
			@endif
		@endforeach
		<?php
		if($i%4==0)
		{
			$key = ((int)($i/4)).':00';
			echo "<td rowspan='4'>$key</td>";
		}
		else $key = ((int)($i/4)).':'.($i%4*30);
		?>
	</tr>
	@endfor
	<tr>
		<th>Vrijeme</th>
		@foreach($grid as $dan => $podatak)
		<th>{{ $dan }}</th>
		@endforeach
		<th>Vrijeme</th>
	</tr>
</tbody>
</table>
</div>
@endsection