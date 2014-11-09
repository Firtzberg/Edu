@section('predmet-select')
{{ HTML::script('js/Kategorija/predmetSelector.js') }}
@if(Auth::user()->hasPermission(Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING))
<div class = 'form-group'>
    {{ Form::label('Odaberite predavača...') }}
    <?php
    $value = null;
    if(Auth::user()->hasPermission(Permission::PERMISSION_OWN_REZERVACIJA_HANDLING))
        $value = Auth::id();
    if(isset($rezervacija))
        $value = $rezervacija->instruktor_id;
    ?>
    {{ Form::select('instruktor_id',
                User::withPermission(Permission::PERMISSION_OWN_REZERVACIJA_HANDLING)
                ->get()->lists('name', 'id'),
                $value,
                array('class' => 'form-control',
        'required' => 'required')) }}
</div>
@else
{{ Form::hidden('instruktor_id', Auth::id()) }}
@endif
<div id="predmet-select" class = 'form-group'>
	{{ Form::label('Odaberite predmet...') }}
	<script type="text/javascript">
		jQuery(function(){selectManager.init(
                <?php
                $levels = array();
                if (!isset($predmet_id)) {
                    $predmet_id = Input::old('predmet_id');
                }
                if ($predmet_id) {
                    $predmet = Predmet::with('kategorija')->find($predmet_id);
                    $kategorije = $predmet->kategorija()->first()->path();
                    $length = count($kategorije);
                    for ($i = 0; $i < $length; $i ++) {
                        $level = array();
                        $level['content'] = $kategorije[$i]->getChildrenFor($value);
                        $level['selected'] = array(
                            'type' => ($i == $length - 1 ? 'predmet' : 'kategorija'),
                            'id' => ($i == $length - 1 ? $predmet_id : $kategorije[$i + 1]->id)
                        );
                        $levels[] = $level;
                    }
                } else {
                    $levels = Kategorija::whereRaw('id = nadkategorija_id')
                            ->first()
                            ->getChildrenFor($value);
                }
                echo json_encode($levels);
                ?>
			);
			selectManager.urlPrefix = "{{ route('Kategorija.index').'/' }}";
		});
	</script>
</div>
@endsection