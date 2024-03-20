<div id="configuracion_distritos">
	<label class="ttl_label_modal">
		{!!trans('ayuda.configuracion.configuracion_distritos.titulo')!!}
	</label>	
	<p class="parrafo_modal">
		{!!trans('ayuda.configuracion.configuracion_distritos.descripcion')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.configuracion.configuracion_distritos.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
</div>