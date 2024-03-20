<div id="procesos_alarmas">
	<label class="ttl_label_modal">
		{!!trans('ayuda.configuracion.procesos_alarmas.titulo')!!}
	</label>	
	<p class="parrafo_modal">
		{!!trans('ayuda.configuracion.procesos_alarmas.descripcion')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.configuracion.procesos_alarmas.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>	
	<p class="parrafo_modal">
		{!!trans('ayuda.configuracion.procesos_alarmas.titulo_lista_crear_alarma')!!}
	</p>
	<ul class="container_list ul_style_num">			
		@foreach(trans('ayuda.configuracion.procesos_alarmas.lista_crear_alarma') as $key => $value)
			@if(is_array($value))
				<ul class="container_list ul_style_num">
					@foreach($value as $key => $subvalue)
						<li>{!!$subvalue!!}</li>
					@endforeach
				</ul>
			@else
				<li>{!!$value!!}</li>
			@endif
		@endforeach
	</ul>	
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_17.png')}}">		
	</div>	
	<p class="parrafo_modal">
		{!!trans('ayuda.configuracion.procesos_alarmas.titulo_lista_editar_alarma')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.configuracion.procesos_alarmas.lista_editar_alarma') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>	
	<p class="parrafo_modal">
		{!!trans('ayuda.configuracion.procesos_alarmas.titulo_lista_deshabilitar_alarma')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.configuracion.procesos_alarmas.lista_deshabilitar_alarma') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>	
</div>