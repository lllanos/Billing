<div class="col-md-12">
	<div id="modal_solicitudes_asociaciones">
		<label class="ttl_label_modal">{!!trans('ayuda.contratos.solicitudes_asociaciones.titulo')!!}</label>
		<p class="parrafo_modal">
			{!!trans('ayuda.contratos.solicitudes_asociaciones.descripcion')!!}
		</p>
		<ul class="container_list ul_style">			
			@foreach(trans('ayuda.contratos.solicitudes_asociaciones.lista') as $key => $value)
				<li>{!!$value!!}</li>
			@endforeach
		</ul>		
		<p class="parrafo_modal">
			{!!trans('ayuda.contratos.solicitudes_asociaciones.titulo_lista_registro')!!}
		</p>
		<ul class="container_list ul_style">			
			@foreach(trans('ayuda.contratos.solicitudes_asociaciones.lista_registro') as $keyRegistro => $valueRegistro)					
				@if(!is_array($valueRegistro))
					<li>
						{!!$valueRegistro!!}
					</li>
				@else
					<ul class="container_list ul_style">
						@foreach($valueRegistro as $keysub => $submenu)
							<li>{!!$submenu!!}</li>
						@endforeach
					</ul>
				@endif				
			@endforeach			
		</ul>		
		<p class="parrafo_modal m-0">
			{!!trans('ayuda.contratos.solicitudes_asociaciones.nota')!!}
		</p>
		<p class="parrafo_modal m-0">
			{!!trans('ayuda.contratos.solicitudes_asociaciones.titulo_lista_detalle_solic')!!}
		</p>
		<div class="container_img_ayuda">
			<img src="{{asset('img/ayuda/imagen_10.png')}}">		
		</div>
		<ul class="container_list ul_style">			
			@foreach(trans('ayuda.contratos.solicitudes_asociaciones.lista_solicitud_detalle') as $keySolcitudDetalle => $valueSolcitudDetalle)
				<li>{!!$valueSolcitudDetalle!!}</li>
			@endforeach
		</ul>		
		<div class="container_img_ayuda">
			<img src="{{asset('img/ayuda/imagen_11.png')}}">		
		</div>
		<div class="alert alert-info" role="alert">
			{!!trans('ayuda.contratos.solicitudes_asociaciones.alert')!!}
		</div>
	</div>
</div>