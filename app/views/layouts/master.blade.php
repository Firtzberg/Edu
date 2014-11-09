<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>
		@yield('title')
	</title>
	<meta name = "viewport" content="width=device-width, initial-scale=1.0">
	{{ HTML::style('css/bootstrap.min.css') }}
	{{ HTML::style('css/raspored.css') }}
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
					@if(Auth::user()->hasPermission(Permission::PERMISSION_OWN_REZERVACIJA_HANDLING)||
                                        Auth::user()->hasPermission(Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING))
					<li>{{ link_to_route('Rezervacija.create', 'Nova Rezervacija') }}</li>
					@endif
					<li>{{ link_to_route('Ucionica.index', 'Učionice') }}</li>
					<li>{{ link_to_route('Instruktor.index', 'Instruktori') }}</li>
					@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_PREDMET_KATEGORIJA))
					<li>{{ link_to_route('Kategorija.index', 'Kategorije') }}</li>
					@endif
					@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_ROLE))
					<li>{{ link_to_route('Role.index', 'Uloge') }}</li>
					@endif
					@if(Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_KLIJENT))
					<li>{{ link_to_route('Klijent.index', 'Klijenti') }}</li>
					@endif
					@if(Auth::user()->hasPermission(Permission::PERMISSION_DOWNLOAD_DATA))
					<li>{{ link_to_route('Excel.index', 'Preuzimanje') }}</li>
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
		@yield('content')
	</div>
	{{ HTML::script('js/bootstrap.min.js') }}
</body>
</html>