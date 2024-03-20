<div id="solicitudes_certif_finalizadas">
	<h4>{!!trans('ayuda.solicitudes.solicitudes_certif_finalizadas.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_certif_finalizadas.descripcion')!!}
	</p>	
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_8.png')}}">		
	</div>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.solicitudes.solicitudes_certif_finalizadas.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_certif_finalizadas.titulo_lista_solic_certif')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.solicitudes.solicitudes_certif_finalizadas.lista_solic_certif') as $key => $value)
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
</div>