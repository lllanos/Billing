<div class="col-md-12" id="modal_mis_solicitudes_redeterminacion">
	<label class="ttl_label_modal">{!!trans('ayuda.solicitudes_redeterminacion.mis_solicitudes_redeterminacion.titulo')!!}</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes_redeterminacion.mis_solicitudes_redeterminacion.descripcion')!!}
	</p>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes_redeterminacion.mis_solicitudes_redeterminacion.titulo_lista')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.solicitudes_redeterminacion.mis_solicitudes_redeterminacion.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes_redeterminacion.mis_solicitudes_redeterminacion.titulo_lista_mis_solic')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.solicitudes_redeterminacion.mis_solicitudes_redeterminacion.lista_mis_solic') as $keyMisSolicitudes => $valueMisSolicitudes)
				@if(!is_array($valueMisSolicitudes))
					<li>
						{!!$valueMisSolicitudes!!}
					</li>
				@else
					<ul class="container_list ul_style">
						@foreach($valueMisSolicitudes as $keysub => $submenu)
							<li>{!!$submenu!!}</li>
						@endforeach
					</ul>
				@endif
		@endforeach
	</ul>	
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_14.png')}}">		
	</div>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes_redeterminacion.mis_solicitudes_redeterminacion.nota')!!}
	</p>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes_redeterminacion.mis_solicitudes_redeterminacion.titulo_lista_detalle_rede')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.solicitudes_redeterminacion.mis_solicitudes_redeterminacion.lista_detalle_rede') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>	
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_15.png')}}">		
	</div>

</div>