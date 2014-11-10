<table class="table table-striped table-bordered table-condensed text-center">
<tbody>
	<tr>
		<th width='{{ 100/(count($grid)+2) }}%'>
			Vrijeme
		</th>
		<?php $pamti = 0;?>
		@foreach($grid as $dan => $podatak)
		<th width='{{ 100/(count($grid)+2) }}%'>
			{{ $dan }}
			<?php $pamti++; ?>
		</th>
		@if($pamti == 5)
		<th width='{{ 100/(count($grid)+2) }}%'>Vrijeme</th>
		@endif
		@endforeach
	</tr>
	@for($i = BaseController::START_HOUR*4; $i < (BaseController::END_HOUR+1)*4; $i++)
	<tr height = "12px">
		<?php
		$pamti = 0;
		if($i%4==0)
		{
			$key = ((int)($i/4)).':00';
			echo "<td rowspan='4' class = 'vrijeme'>$key</td>";
		}
		else $key = ((int)($i/4)).':'.($i%4*15);
		?>
		@foreach($grid as $podatak)
			@if(isset($podatak[$key]))
			<td rowspan='{{ $podatak[$key]['span'] }}'
				@if(isset($podatak[$key]['rezervacija']))
				class = "reserved" style="background-color: #{{ $podatak[$key]['instruktor']->boja }}"
				@endif
				>
				@if(isset($podatak[$key]['rezervacija']))
				{{ $podatak[$key]['rezervacija']->link() }}<br>
				{{ $podatak[$key]['instruktor']->link() }}
				@endif
			</td>
			@endif
		<?php
		$pamti ++;
		if($i%4==0)
		{
			$key = ((int)($i/4)).':00';
			if($pamti == 5)
				echo "<td rowspan='4' class = 'vrijeme'>$key</td>";
		}
		else $key = ((int)($i/4)).':'.($i%4*15);
		?>
		@endforeach
	</tr>
	@endfor
	<tr>
		<th width='{{ 100/(count($grid)+2) }}%'>
			Vrijeme
		</th>
		<?php $pamti = 0;?>
		@foreach($grid as $dan => $podatak)
		<th width='{{ 100/(count($grid)+2) }}%'>
			{{ $dan }}
			<?php $pamti++; ?>
		</th>
		@if($pamti == 5)
		<th width='{{ 100/(count($grid)+2) }}%'>Vrijeme</th>
		@endif
		@endforeach
	</tr>
</tbody>
</table>