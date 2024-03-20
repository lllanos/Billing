<div id="tabla_i_valores">
	<h4>{!!trans('ayuda.indices.tabla_i_valores.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.indices.tabla_i_valores.descripcion')!!}
	</p>

	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_11.png')}}">		
	</div>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.indices.tabla_i_valores.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
</div>