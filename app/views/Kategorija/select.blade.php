@section('predmet-select')
{{ HTML::script('js/Kategorija/predmetSelector.js') }}
<?php
if (isset($disableDropdown)) {
    $disableDropdown = true;
} else {
    $disableDropdown = false;
}
if(!isset($instruktor_id)){
$instruktor_id = null;
if (Auth::user()->hasPermission(Permission::PERMISSION_OWN_REZERVACIJA_HANDLING))
    $instruktor_id = Auth::id();
$instruktor_id = Input::old('instruktor_id', $instruktor_id);
}
?>
@if(Auth::user()->hasPermission(Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING)
&& !$disableDropdown)
<div class = 'form-group'>
    {{ Form::label('Odaberite predavača...') }}
    {{ Form::select('instruktor_id',
        User::withPermission(Permission::PERMISSION_OWN_REZERVACIJA_HANDLING)
            ->get()->lists('name', 'id'),
        $instruktor_id,
        array('class' => 'form-control',
            'required' => 'required'))
    }}
</div>
@else
{{ Form::hidden('instruktor_id', $instruktor_id) }}
@endif
<div id="predmet-select" class = 'form-group'>
    {{ Form::label('Odaberite predmet...') }}
    <script type="text/javascript">
        jQuery(function () {
            selectManager.init(
<?php
$levels = array();
if (!isset($predmet_id)) {
    $predmet_id = Input::old('predmet_id');
}
if ($predmet_id) {
    $predmet = Predmet::with('kategorija')->find($predmet_id);
    $kategorije = $predmet->kategorija->path();
    $length = count($kategorije);
    for ($i = 0; $i < $length; $i ++) {
        $level = array();
        $level[Kategorija::JSON_CONTENT_IDENTIFIER] = $kategorije[$i]->getChildrenFor($instruktor_id);
        $level[Kategorija::JSON_SELECTED_IDENTIFIER] = array(
            Kategorija::JSON_TYPE_IDENTIFIER => ($i == $length - 1 ?
                    Kategorija::JSON_SELECTED_PREDMET_IDENTIFIER :
                    Kategorija::JSON_SELECTED_KATEGORIJA_IDENTIFIER),
            Kategorija::JSON_ID_IDENTIFIER => ($i == $length - 1 ? $predmet_id : $kategorije[$i + 1]->id)
        );
        $levels[] = $level;
    }
} else {
    $levels = Kategorija::whereRaw('id = nadkategorija_id')
            ->first()
            ->getHierarchyFor($instruktor_id);
}
echo json_encode($levels);
?>
            );
            selectManager.urlPrefix = "{{ route('Kategorija.index').'/' }}";
        });
    </script>
</div>
@endsection