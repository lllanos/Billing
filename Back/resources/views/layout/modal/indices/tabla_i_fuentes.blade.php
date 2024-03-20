<div id="tabla_i_fuentes">
	<h4>{!!trans('ayuda.indices.tabla_i_fuentes.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.indices.tabla_i_fuentes.descripcion')!!}
	</p>

	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_12.png')}}">		
	</div>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.indices.tabla_i_fuentes.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
</div>