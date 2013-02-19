<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>@yield('title')</title>
        <meta name="description" content="@get('platform/settings::site.tagline')">
		<meta name="author" content="@get('platform/settings::site.title')">
        <meta name="viewport" content="width=device-width">
        <meta name="base_url" content="{{ URL::to() }}">
        <meta name="admin_url" content="{{ URL::to_admin() }}">

		<!-- Queue Styles -->
		{{ Theme::queue_asset('style', 'css/main.less') }}

		<!-- Release Styles -->
		{{ Theme::release_assets('styles') }}

		<!-- Styles -->
		@yield('styles')

		<!-- Apply Style Options -->
        @widget('platform/themes::options.css')

        <!-- Modernizr -->
		{{ Theme::queue_asset('modernizr', 'js/vendor/modernizr/modernizr-2.6.1-respond-1.1.0.min.js') }}

		<link rel="shortcut icon" href="{{ Theme::asset('img/favicon.png') }}">
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ Theme::asset('img/apple-touch-icon-144x144-precomposed.png') }}">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{ Theme::asset('img/apple-touch-icon-114x114-precomposed.png') }}">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{ Theme::asset('img/apple-touch-icon-72x72-precomposed.png') }}">
		<link rel="apple-touch-icon-precomposed" href="{{ Theme::asset('img/apple-touch-icon-precomposed.png') }}">

    </head>
    <body>
		<!--[if lt IE 7]>
		<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
		<![endif]-->
		<div id="base">

			<header class="navbar">
				<div class="navbar-inner">
					<div class="container-fluid">
						<a class="btn btn-navbar" data-toggle="collapse" data-target="#primary-navigation">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</a>

						<a class="brand" href="{{ URL::to_admin() }}">@get('platform/settings::site.title')</a>

						<ul class="nav pull-right">
							<li class="divider-vertical"></li>
							<li>
								<a href="{{ URL::to() }}" target="_self">
									<i class="icon-home"></i> <span>Home</span>
								</a>
							</li>
							<li class="divider-vertical"></li>
							<li>
								<a href="{{ URL::to('/logout') }}" target="_self">
									<i class="icon-signout"></i> <span>Logout</span>
								</a>
							</li>
							<li class="divider-vertical"></li>
						</ul>
						<div id="primary-navigation" class="nav-collapse collapse">
							@widget('platform/menus::menus.nav', 'admin', 1, 'nav', ADMIN)
						</div><!--/.nav-collapse -->
					</div>
				</div>
			</header><!--/.Mobile Navigation -->

			<div class="primary page container-fluid">

				<div class="row-fluid hidden-desktop">
					<div class="span12">
						<nav class="secondary-navigation">
							@widget('platform/menus::menus.nav', 1, 1, 'nav nav-stacked nav-pills', ADMIN)
						</nav>
					</div>
				</div>

				<div class="tabbable tabs-left">

					@widget('platform/menus::menus.nav', 1, 1, 'secondary-navigation nav nav-tabs visible-desktop', ADMIN)

					<div class="secondary page tab-content">
						@widget('platform/application::messages.all')
						@yield('content')
					</div>
  				</div>

			</div>
			<div id="push"></div>

		</div><!--/.base-->

		<footer>
			@content('copyright')
		</footer>



	<!-- Queue Global Scripts -->
	{{ Theme::queue_asset('jquery', 'js/vendor/jquery/jquery-1.8.2.min.js') }}
	{{ Theme::queue_asset('platform-url', 'js/vendor/platform/helpers.js', 'jquery') }}
	{{ Theme::queue_asset('bootstrap-transition', 'js/bootstrap/transition.js', 'jquery') }}
	{{ Theme::queue_asset('bootstrap-collapse', 'js/bootstrap/collapse.js', 'jquery') }}

	<!-- Queue App Specific Scripts -->
	{{ Theme::queue_asset('plugins', 'js/plugins.js') }}
	{{ Theme::queue_asset('main', 'js/main.js') }}

	<!-- Release Scripts -->
	{{ Theme::release_assets('scripts') }}

	<!-- Apply View Specific Scripts -->
	@yield('scripts')

    </body>
</html>
