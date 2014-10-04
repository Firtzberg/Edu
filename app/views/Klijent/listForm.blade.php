@section('klijent-input')
{{ HTML::style('css/autocomplete.css'); }}
{{ HTML::script('js/Klijent/listForm.js') }}
<div class="form-group">
{{Form::label('Polaznici')}}
<div id="form-klijenti-container">
@for($i = 1; Input::old('form-klijenti-item-'.$i.'-broj_mobitela'); $i++)
<?php $key =  'form-klijenti-item-'.$i; ?>
<script type="text/javascript">
klijentManager.add("{{ Input::old($key.'-broj_mobitela') }}", "{{ Input::old($key.'-ime') }}")
</script>
@endfor
</div>
{{ Form::button('New Klijent', array(
'class' => 'btn btn-success',
'id' => 'form-klijent-add')) }}
</div>
@endsection