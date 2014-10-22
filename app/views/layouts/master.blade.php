<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>{{ $title }}</title>
	<meta name = "viewport" content="width=device-width, initial-scale=1.0">
	<link href = "{{URL::to('css/bootstrap.min.css')}}" rel="stylesheet">
	<script src = "http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src = "http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
</head>
<body>
	<div class = "navbar navbar-inverse">
		<div class = "container">
			<button class="navbar-toggle" data-toggle = "collapse" data-target = ".navHeaderCollapse">
				<span class = "icon-bar"></span>
				<span class = "icon-bar"></span>
				<span class = "icon-bar"></span>
			</button>
			<div class = "collapse navbar-collapse navHeaderCollapse">
				<ul class = "nav navbar-nav navbar-right">
					<li>{{ link_to_route('Rezervacija.create', 'Nova Rezervacija') }}</li>
					<li>{{ link_to_route('Ucionica.index', 'Učionice') }}</li>
					<li>{{ link_to_route('Instruktor.index', 'Instruktori') }}</li>
					@if(Auth::user()->is_admin)
					<li>{{ link_to_route('Kategorija.index', 'Kategorije') }}</li>
					<li>{{ link_to_route('Klijent.index', 'Klijenti') }}</li>
					@endif
					<li>{{ link_to_route('Instruktor.show', 'Profil', Auth::id()) }}</li>
					<li>{{ link_to_route('logout', 'Odjava') }}</li>
				</ul>
			</div>
		</div>
	</div>
	<div id="content-container" class="container">
		@if(Session::has(BaseController::SUCCESS_MESSAGE_KEY))
		<div class="alert alert-success">{{ Session::get('poruka') }}</div>
		@endif
		@if(Session::has(BaseController::DANGER_MESSAGE_KEY))
		<div class="alert alert-danger">{{ Session::get('greska') }}</div>
		@endif
		{{ $content }}
	</div>
	{{ HTML::script('js/bootstrap.min.js') }}
</body>
</html>