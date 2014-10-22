<table class = "table table-striped table-bordered table-condensed text-center">
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
		<th width='{{ 100/(count($grid)+2) }}%'>Vrijeme</th>
	</tr>
	@for($i = BaseController::START_HOUR*4; $i < (BaseController::END_HOUR+1)*4; $i++)
	<tr>
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
@if(Auth::user()->is_admin||Auth::id() == $instruktor->id)
				{{ link_to_route('Rezervacija.show',
				$podatak[$key]['rezervacija']->predmet?$podatak[$key]['rezervacija']->predmet->ime:'Nema predmeta',
				array('id' => $podatak[$key]['rezervacija']->id)) }}<br>
@else
{{$podatak[$key]['rezervacija']->predmet?$podatak[$key]['rezervacija']->predmet->ime:'Nema predmeta'}}<br>
@endif
				@if(!is_null($podatak[$key]['ucionica']))
					{{ link_to_route('Ucionica.show',
					$podatak[$key]['ucionica']->naziv,
					array('id' => $podatak[$key]['ucionica']->id)) }}
				@endif
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
		else $key = ((int)($i/4)).':'.($i%4*15);
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