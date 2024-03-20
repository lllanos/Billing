@extends ('layout.app')

@section('title', config('app.name'))

@section('title-nav', trans('index.dashboard'))

@section('content')

	<div class="container_img_inicio">
		<section class="jumbotron img_inicio" style="background-image: url({{asset('img/banner_inicio.jpg')}});">
			<div class="barra_links">
				<a  class="link_eby" href="#">@trans('index.eby')</a> / <a href="#">@trans('index.redeterminaciones_precios')</a>
			</div>
			<div class="container_info_banner_inicio">
				<span class="titulo_banner">@trans('index.titulo_banner')</span>
				<span class="sub_titulo_banner">@trans('index.titulo_sub_banner')</span>
			</div>
		</section>
	</div>

	@include('index.descargas')

@endsection
