<div id="solicitudes_certif_proceso">
	<h4>{!!trans('ayuda.solicitudes.solicitudes_certif_proceso.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_certif_proceso.introduccion')!!}
	</p>
	<div class="container_img_ayuda" style="height: auto;">
		<img src="{{asset('img/ayuda/circuito_certificados.png')}}">
	</div>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.solicitudes.solicitudes_certif_proceso.lista_introduccion') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_certif_proceso.descripcion')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_6.png')}}">
	</div>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.solicitudes.solicitudes_certif_proceso.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.solicitudes.solicitudes_certif_proceso.titulo_lista_solic_certif')!!}
	</p>
	<ul class="container_list ul_style_num">
		@foreach(trans('ayuda.solicitudes.solicitudes_certif_proceso.lista_solic_certif') as $key => $value)
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
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_7.png')}}">
	</div>
</div>
