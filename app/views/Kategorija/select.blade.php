@section('predmet-select')
{{ HTML::script('js/Kategorija/predmetSelector.js') }}
{{ Form::hidden('instruktor_id', $instruktor->id) }}
@if($instruktor->hasPermission(\Permission::PERMISSION_TECAJ))
<div class = "form-group">
    {{ Form::label('Tečaj') }}
{{ Form::select('tecaj', array(0 => 'NE', 1 => 'DA'), null,
            array('class' => 'form-control',
    'required' => 'required')) }}
</div>
@else
{{ Form::hidden('tecaj', 0) }}
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
        $level[Kategorija::JSON_CONTENT_IDENTIFIER] = $kategorije[$i]->getChildrenFor($instruktor->id);
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
            ->getHierarchyFor($instruktor->id);
}
echo json_encode($levels);
?>
            );
            selectManager.urlPrefix = "{{ route('Kategorija.index').'/' }}";
        });
    </script>
</div>
@endsection