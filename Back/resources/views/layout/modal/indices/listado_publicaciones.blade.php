<div id="listado_publicaciones">
	<h4>{!!trans('ayuda.indices.listado_publicaciones.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.indices.listado_publicaciones.descripcion')!!}
	</p>		
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_9.png')}}">		
	</div>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.indices.listado_publicaciones.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.indices.listado_publicaciones.circuito')!!}
	</p>
	<div class="container_img_ayuda" style="height: auto;width:40%"">
		<img src="{{asset('img/ayuda/imagen_circuito_publicacion.png')}}">		
	</div>
	<p class="parrafo_modal">
		{!!trans('ayuda.indices.listado_publicaciones.proceso')!!}
	</p>
	<div class="container_img_ayuda" style="height: auto;">
		<img src="{{asset('img/ayuda/imagen_circuito_proceso.png')}}">		
	</div>
</div>