<div>
    <h4>{!!trans('ayuda.contratos.asociacion_contratos_pendientes.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.asociacion_contratos_pendientes.descripcion')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_3.png')}}">
	</div>
	{{-- <ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.asociacion_contratos_pendientes.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul> --}}
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.asociacion_contratos_pendientes.titulo_lista_asoc')!!}
	</p>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.asociacion_contratos_pendientes.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<label class="ttl_label_modal">{!!trans('ayuda.contratos.asociacion_contratos_pendientes.detalles_solic_asoc_contratos_pendientes')!!}</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.asociacion_contratos_pendientes.detalles_solic_asoc_contratos_pendientes_descrip')!!}
	</p>

	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_4.png')}}">
	</div>
</div>
