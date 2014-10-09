@section('predmet-select')
{{ HTML::script('js/Kategorija/predmetSelector.js') }}
<div class = 'form-group'>
	{{ Form::label('Odaberite predmet...') }}
	<input type='hidden' name='predmet_id'>
	<script type="text/javascript">
		selectManager.onCategoryChosen(jQuery('input[type=hidden][name=predmet_id]'),
			{{ Kategorija::select('id')->whereRaw('id = nadkategorija_id')->first()->id }});
	</script>
</div>
@endsection