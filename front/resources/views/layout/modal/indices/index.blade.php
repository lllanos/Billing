<div class="col-md-12">
	<p class="parrafo_modal">
		{!!trans('ayuda.indices.descripcion')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.indices.lista_publicaciones') as $key => $value)
				@if(!is_array($value))
					<li>
						{!!$value!!}
					</li>
				@else
					<ul class="container_list ul_style">
						@foreach($value as $keysub => $submenu)
							<li>{!!$submenu!!}</li>
						@endforeach
					</ul>
				@endif
		@endforeach
	</ul>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_4.png')}}">		
	</div>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.indices.lista_tabla_valores') as $key => $value)
				@if(!is_array($value))
					<li>
						{!!$value!!}
					</li>
				@else
					<ul class="container_list ul_style">
						@foreach($value as $keysub => $submenu)
							<li>{!!$submenu!!}</li>
						@endforeach
					</ul>
				@endif
		@endforeach
	</ul>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_5.png')}}">		
	</div>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.indices.lista_tabla_fuentes') as $key => $value)
				@if(!is_array($value))
					<li>
						{!!$value!!}
					</li>
				@else
					<ul class="container_list ul_style">
						@foreach($value as $keysub => $submenu)
							<li>{!!$submenu!!}</li>
						@endforeach
					</ul>
				@endif
		@endforeach
	</ul>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_6.png')}}">		
	</div>
	
</div>