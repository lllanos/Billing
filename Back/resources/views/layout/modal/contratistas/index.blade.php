<div>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratistas.descripcion')!!}
	</p>

	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_13.png')}}">		
	</div>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.contratistas.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratistas.titulo_lista_contratistas')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.contratistas.lista_contratistas') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratistas.nota')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_14.png')}}">		
	</div>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratistas.titulo_nuevo_contratista')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.contratistas.lista_nuevo_contratistas') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_29.png')}}">		
	</div>
	
</div>