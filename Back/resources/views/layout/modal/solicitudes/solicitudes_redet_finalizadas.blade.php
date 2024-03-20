<div id="solicitudes_redet_finalizadas">
	<h4>{!!trans('ayuda.solicitudes.solicitudes_redet_finalizadas.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_redet_finalizadas.descripcion')!!}
	</p>	
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_8.png')}}">		
	</div>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.solicitudes.solicitudes_redet_finalizadas.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_redet_finalizadas.titulo_lista_solic_redet_finalizadas')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.solicitudes.solicitudes_redet_finalizadas.lista_solic_redet_finalizadas') as $key => $value)
			@if(!is_array($value))
				<li>
					{!!$value!!}
				</li>
			@else
				<ul class="container_list ul_style">
					@foreach($value as $key=> $subvalue)
						<li>{!!$subvalue!!}</li>
					@endforeach
				</ul>
			@endif
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_redet_finalizadas.nota')!!}
	</p>
</div>