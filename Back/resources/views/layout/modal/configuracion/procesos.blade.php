<div id="procesos">
	<label class="ttl_label_modal">
		{!!trans('ayuda.configuracion.procesos.titulo')!!}
	</label>	
	<p class="parrafo_modal">
		{!!trans('ayuda.configuracion.procesos.descripcion')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.configuracion.procesos.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>	
	<p class="parrafo_modal">
		{!!trans('ayuda.configuracion.procesos.nota')!!}
	</p>
</div>