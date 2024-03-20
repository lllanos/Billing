<div>	
    <h4>{!!trans('ayuda.contratos.asociacion_contratos_finalizadas.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.asociacion_contratos_finalizadas.descripcion')!!}
	</p>		
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_5.png')}}">		
	</div>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.contratos.asociacion_contratos_finalizadas.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.asociacion_contratos_finalizadas.titulo_lista_asoc_contratos_fin')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.contratos.asociacion_contratos_finalizadas.lista_asoc_contratos_fin') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.asociacion_contratos_finalizadas.titulo_detalle')!!}
	</p>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.asociacion_contratos_finalizadas.descripcion_detalle')!!}
	</p>
</div>