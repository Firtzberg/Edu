@section('klijent-input')
{{ HTML::style('css/autocomplete.css'); }}
{{ HTML::script('js/Klijent/listForm.js') }}
<div class="form-group">
{{Form::label('Polaznici')}}
<div id="form-klijenti-container">
@if(Input::old('form-klijenti-item-1-broj_mobitela'))
<script type="text/javascript">
@for($i = 1; Input::old('form-klijenti-item-'.$i.'-broj_mobitela'); $i++)
<?php $key =  'form-klijenti-item-'.$i; ?>
klijentManager.add("{{ Input::old($key.'-broj_mobitela') }}", "{{ Input::old($key.'-ime') }}")
@endfor
</script>
@endif
</div>
{{ Form::button('New Klijent', array(
'class' => 'btn btn-success',
'id' => 'form-klijent-add')) }}
</div>
@endsection