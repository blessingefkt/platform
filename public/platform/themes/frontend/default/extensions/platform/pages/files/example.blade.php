@layout('templates.default')

<!-- Title -->
@section('title')
	@get('platform/settings::site.title')
@endsection

<!-- Queue Styles | e.g Theme::queue_asset('name', 'path_to_css', 'dependency') -->

<!-- Styles -->
@section ('styles')
@endsection

<!-- Queue Scripts | e.g. Theme::queue_asset('name', 'path_to_js', 'dependency') -->

<!-- Scripts -->
@section('scripts')
@endsection

<!-- Content -->
@section('content')
<section id="example page default">

	Some Example page goes here

</section>
@endsection
