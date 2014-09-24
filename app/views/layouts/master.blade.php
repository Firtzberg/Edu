<!doctype html>
<html>
</head>
<head>
	<meta charset="UTF-8">
	<title>{{ $title }}</title>
	<meta name = "viewport" content="width=device-width, initial-scale=1.0">
	<link href = "{{URL::to('css/bootstrap.min.css')}}" rel="stylesheet">
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
					<li>{{ link_to_route('Instruktor.show', 'Profil', Auth::id()) }}</li>
					<li>{{ link_to_route('logout', 'Odjava') }}</li>
				</ul>
			</div>
		</div>
	</div>
	{{ $content }}
	<script src = "http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src = "{{ URL::to('js/bootstrap.min.js') }}"></script>
</body>
</html>