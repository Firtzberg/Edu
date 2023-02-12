<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>
		@yield('title')
	</title>
	<meta name = "viewport" content="width=device-width, initial-scale=1.0">
	{{ HTML::style('css/bootstrap.min.css') }}
        @yield('heading')
	<script src = "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script><script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-56930100-1', 'auto');
  ga('send', 'pageview');

</script>
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
					<li>{{ link_to_route('home', 'Početna') }}</li>
					@if(Auth::user()->hasPermission(Permission::PERMISSION_OWN_REZERVACIJA_HANDLING))
					<li>{{ link_to_route('Rezervacija.create', 'Nova Rezervacija') }}</li>
					@endif
					@if(Auth::user()->hasPermission(Permission::PERMISSION_VIEW_UCIONICA))
					<li>{{ link_to_route('Ucionica.index', 'Učionice') }}</li>
					@endif
					@if(Auth::user()->hasPermission(Permission::PERMISSION_VIEW_NERADNI_DAN))
					<li>{{ link_to_route('NeradniDan.index', 'Neradni Dani') }}</li>
					@endif
					@if(Auth::user()->hasPermission(Permission::PERMISSION_VIEW_USER))
					<li>{{ link_to_route('Djelatnik.index', 'Djelatnici') }}</li>
                                        @endif
					@if(Auth::user()->hasPermission(Permission::PERMISSION_VIEW_PREDMET_KATEGORIJA))
					<li>{{ link_to_route('Kategorija.index', 'Kategorije') }}</li>
					@endif
					@if(Auth::user()->hasPermission(Permission::PERMISSION_VIEW_CJENOVNIK))
					<li>{{ link_to_route('Cjenovnik.index', 'Cijene') }}</li>
					@endif
					@if(Auth::user()->hasPermission(Permission::PERMISSION_VIEW_ROLE))
					<li>{{ link_to_route('Role.index', 'Uloge') }}</li>
					@endif
					@if(Auth::user()->hasPermission(Permission::PERMISSION_VIEW_KLIJENT))
					<li>{{ link_to_route('Klijent.index', 'Klijenti') }}</li>
					@endif
					@if(Auth::user()->hasPermission(Permission::PERMISSION_DOWNLOAD_DATA))
					<li>{{ link_to_route('Excel.index', 'Preuzimanje') }}</li>
					@endif
					<li>{{ link_to_route('Djelatnik.show', 'Profil', Auth::id()) }}</li>
					<li>{{ link_to_route('logout', 'Odjava') }}</li>
				</ul>
			</div>
		</div>
	</div>
	<div id="content-container" class="container">
		@if(Session::has(BaseController::SUCCESS_MESSAGE_KEY))
		<div class="alert alert-success">{{ Session::get(BaseController::SUCCESS_MESSAGE_KEY) }}</div>
		@endif
		@if(Session::has(BaseController::DANGER_MESSAGE_KEY))
		<div class="alert alert-danger">{{ Session::get(BaseController::DANGER_MESSAGE_KEY) }}</div>
		@endif
		@yield('content')
	</div>
	{{ HTML::script('js/bootstrap.min.js') }}
	{{ HTML::script('js/Raspored/rezervacijaLauncher1.js') }}
</body>
</html>