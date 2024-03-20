<div class="col-md-12" id="modal_solicitar_redeterminacion">
	<label class="ttl_label_modal">{!!trans('ayuda.solicitudes_redeterminacion.solicitar_redeterminacion.titulo')!!}</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes_redeterminacion.solicitar_redeterminacion.descripcion')!!}
	</p>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes_redeterminacion.solicitar_redeterminacion.titulo_lista_solic_rede')!!}
	</p>
	<ul class="container_list ul_style_num">			
		@foreach(trans('ayuda.solicitudes_redeterminacion.solicitar_redeterminacion.lista_solic_rede') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>	
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_13.png')}}">		
	</div>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes_redeterminacion.solicitar_redeterminacion.nota')!!}
	</p>		
	<div class="alert alert-info" role="alert">
		{!!trans('ayuda.solicitudes_redeterminacion.solicitar_redeterminacion.alert')!!}
	</div>
</div>