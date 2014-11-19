<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>SignIn</title>
	<meta name = "viewport" content="width=device-width, initial-scale=1.0">
	<link href = "{{URL::to('css/bootstrap.min.css')}}" rel="stylesheet"><script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-56930100-1', 'auto');
  ga('send', 'pageview');

</script>
</head>
<body>
	<div class = "container">
		{{ Form::open(array('route' => 'login',
		'class' => 'form-signin',
		'style' => 'max-width:330px;padding:15px;margin:auto')) }}
		<h2 class="form-signin-heading">Registracija</h2>
		<div class = "form-group">
			{{ Form::text('userName', null,
			array(
				'placeholder' => 'Korisničko ime',
				'class' => 'form-control',
				'autofocus' => 'autofocus',
				'required' => 'required')) }}
		</div>
		<div class = "form-group">
			{{ Form::password('lozinka',
				array(
					'placeholder' => 'Zaporka',
					'class' => 'form-control',
					'required' => 'required')) }}
		</div>
		<div class = 'checkbox'>
			<label>
				{{ Form::checkbox('remember', 'Stay signed in', true, array('id' => 'remember'))}}
				Stay signed in
			</label>
		</div>
		@if(Session::has(\BaseController::DANGER_MESSAGE_KEY))
		<div class = "alert alert-warning">
			{{ Session::get(\BaseController::DANGER_MESSAGE_KEY) }}
		</div>
		@endif
		{{ Form::submit('SignIn', array(
		'class' => 'btn btn-lg btn-primary btn-block')) }}
		{{ Form::close() }}
	</div>
	<script src = "http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src = "{{ URL::to('js/bootstrap.min.js') }}"></script>
</body>
</html>
