@extends('layouts.master')

@section('title')
Preuzimanje
@endsection

@section('content')
{{ Form::open(array('route' => 'Excel.download', 'class' => 'form', 'style="max-width:330px;margin:auto"')) }}
<h2>Preuzimanje</h2>
<div class="form-group">
	{{ Form::label('Od mjeseca (uključujući)') }}
	<div class = "row">
		<div class = "col-xs-6">
			{{ Form::selectRange('startMonth', 1, 12, null, array('class' => 'form-control')) }}
		</div>
		<div class = "col-xs-6">
			{{ Form::selectRange('startYear', 2014, date('Y'), null, array('class' => 'form-control')) }}
		</div>
	</div>
</div>
<div class="form-group">
	{{ Form::label('Do mjeseca (uključujući)') }}
	<div class = "row">
		<div class = "col-xs-6">
			{{ Form::selectRange('endMonth', 1, 12, null, array('class' => 'form-control')) }}
		</div>
		<div class = "col-xs-6">
			{{ Form::selectRange('endYear', 2014, date('Y'), null, array('class' => 'form-control')) }}
		</div>
	</div>
</div>
<div class="form-group">
	{{ Form::label('Format') }}
	{{ Form::select('format', array('xls' => '.xls (MS Excel 2003)', 'xlsx' => '.xlsx (MS Excel 2007)'), null, array('class' => 'form-control')) }}
</div>
<div class="form-group">
{{ Form::submit('Download', array('class' => 'btn btn-success')) }}
</div>
{{ Form::close() }}
@endsection