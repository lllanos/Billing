<div id="srdp">
	<label class="ttl_label_modal">{!!trans('ayuda.sistema_redeterminacion_precio.introduccion')!!}</label>
    <ul class="container_list ul_style">
            @foreach(trans('ayuda.sistema_redeterminacion_precio.lista_introduccion') as $key => $value)
            <li>{!!$value!!}</li>
            @endforeach
	</ul>

	<p class="parrafo_modal">
		{!!trans('ayuda.sistema_redeterminacion_precio.pantalla_incio.descripcion')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_1.png')}}">
	</div>
	<label class="ttl_label_modal">{!!trans('ayuda.sistema_redeterminacion_precio.paneles_widgets.titulo')!!}</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.sistema_redeterminacion_precio.paneles_widgets.descripcion')!!}
	</p>

	<label class="ttl_label_modal">{!!trans('ayuda.sistema_redeterminacion_precio.solic_redeterminacion_estado.titulo')!!}</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.sistema_redeterminacion_precio.solic_redeterminacion_estado.descripcion')!!}
	</p>

	<label class="ttl_label_modal">{!!trans('ayuda.sistema_redeterminacion_precio.dias_promedio_esperados.titulo')!!}</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.sistema_redeterminacion_precio.dias_promedio_esperados.descripcion')!!}
	</p>

	<label class="ttl_label_modal">{!!trans('ayuda.sistema_redeterminacion_precio.solic_asociacion.titulo')!!}</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.sistema_redeterminacion_precio.solic_asociacion.descripcion')!!}
	</p>

	<label class="ttl_label_modal">{!!trans('ayuda.sistema_redeterminacion_precio.contratos_por_estado.titulo')!!}</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.sistema_redeterminacion_precio.contratos_por_estado.descripcion')!!}
	</p>
	<label class="ttl_label_modal">{!!trans('ayuda.sistema_redeterminacion_precio.mis_asignaciones.titulo')!!}</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.sistema_redeterminacion_precio.mis_asignaciones.descripcion')!!}
	</p>


	<p class="parrafo_modal">
		{!!trans('ayuda.sistema_redeterminacion_precio.solic_asociacion.titlo_lista')!!}
	</p>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.sistema_redeterminacion_precio.solic_asociacion.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<div class="alert alert-info" role="alert">
		{!!trans('ayuda.sistema_redeterminacion_precio.solic_asociacion.alert')!!}
	</div>
</div>
