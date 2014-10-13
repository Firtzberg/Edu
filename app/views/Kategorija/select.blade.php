@section('predmet-select')
{{ HTML::script('js/Kategorija/predmetSelector.js') }}
<div id="predmet-select" class = 'form-group'>
	{{ Form::label('Odaberite predmet...') }}
	<script type="text/javascript">
		jQuery(function(){selectManager.init(
			<?php
			$levels = array();
			if(!isset($predmet_id)){
				$predmet_id  = Input::old('predmet_id');
			}
			if($predmet_id){
				$predmet = Predmet::with('kategorija')->find($predmet_id);
				$kategorije = $predmet->kategorija()->first()->path();
				$length = count($kategorije);
				for ($i = 0; $i < $length; $i ++) {
					$level = array();
					$level['content'] = $kategorije[$i]->getEnabledChildren();
					$level['selected'] = 
						array(
							'type' => ($i == $length-1 ? 'predmet' : 'kategorija'),
							'id' => ($i == $length-1 ? $predmet_id : $kategorije[$i+1]->id)
						);
					$levels[] = $level;
				}
			}
			else{
				$levels[] = 
					array('content' =>
						Kategorija::whereRaw('id = nadkategorija_id')
						->first()
						->getEnabledChildren()
					);
			}
			echo json_encode($levels);
			?>
			);});
	</script>
</div>
@endsection