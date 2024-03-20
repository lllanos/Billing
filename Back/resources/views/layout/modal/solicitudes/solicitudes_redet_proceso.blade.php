<div id="solicitudes_redet_proceso">
	<h4>{!!trans('ayuda.solicitudes.solicitudes_redet_proceso.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_redet_proceso.descripcion')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_6.png')}}">
	</div>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.solicitudes.solicitudes_redet_proceso.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_redet_proceso.titulo_lista_solic_redet')!!}
	</p>
	<ul class="container_list ul_style_num">
		@foreach(trans('ayuda.solicitudes.solicitudes_redet_proceso.lista_solic_redet') as $key => $value)
			@if(!is_array($value))
				<li>
					{!!$value!!}
				</li>
			@else
				<ul class="container_list ul_style_num">
					@foreach($value as $key=> $subvalue)
						<li>{!!$subvalue!!}</li>
					@endforeach
				</ul>
			@endif
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_redet_proceso.nota')!!}
	</p>
	
	<h4>{!!trans('ayuda.solicitudes.solicitudes_redet_proceso.detalle.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_redet_proceso.detalle.descripcion')!!}
	</p>
		<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_7.png')}}">
	</div>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_redet_proceso.detalle.circuito')!!}
	</p>
	<div class="container_img_ayuda" style="height: auto;">
		<img src="{{asset('img/ayuda/imagen_circuito_redeterminacion.png')}}">
	</div>
	<ul class="container_list ul_style_num">
		@foreach(trans('ayuda.solicitudes.solicitudes_redet_proceso.detalle.lista_circuito') as $key => $value)
			@if(!is_array($value))
				<li>
					{!!$value!!}
				</li>
			@else
				<ul class="container_list ul_style_num">
					@foreach($value as $key=> $subvalue)
						<li>{!!$subvalue!!}</li>
					@endforeach
				</ul>
			@endif
		@endforeach
	</ul>

	<div class="alert alert-info" role="alert">
		{!!trans('ayuda.solicitudes.solicitudes_redet_proceso.detalle.nota')!!}
	</div>
	
</div>
