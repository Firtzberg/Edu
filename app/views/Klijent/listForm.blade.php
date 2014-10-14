@section('klijent-input')
{{ HTML::style('css/autocomplete.css'); }}
{{ HTML::script('js/Klijent/listForm.js') }}
<div class="form-group">
{{Form::label('Polaznici')}}
<div id="form-klijenti-container">
<script type="text/javascript">
klijentManager.init(
<?php
	$klijentCollection = array();
	if(isset($klijenti))
		$klijentCollection = $klijenti->toArray('ime', 'broj_mobitela');
	else{
		for($i = 1; Input::old('form-klijenti-item-'.$i.'-broj_mobitela'); $i++){
			$key =  'form-klijenti-item-'.$i;
			$klijentItem = array(
				'broj_mobitela' => Input::old($key.'-broj_mobitela'),
				'ime' => Input::old($key.'-ime')
			);
			$klijentCollection[] = $klijentItem;
		}
	}
	echo json_encode($klijentCollection);
?>
);
klijentManager.url = "{{ route('Klijent.Suggestions') }}";
</script>
</div>
{{ Form::button('Dodaj polaznika', array(
'class' => 'btn btn-success',
'id' => 'form-klijent-add')) }}
</div>
@endsection