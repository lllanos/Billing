<div>
	<label class="ttl_label_modal">
		{!!trans('ayuda.seguridad.seguridad_roles.titulo')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.seguridad.seguridad_roles.descripcion')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.seguridad.seguridad_roles.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.seguridad.seguridad_roles.titulo_lista_nuevo_rol')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.seguridad.seguridad_roles.lista_nuevo_rol') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>		
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_16.png')}}">		
	</div>
	<p class="parrafo_modal">
		{!!trans('ayuda.seguridad.seguridad_roles.titulo_lista_editar_rol')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.seguridad.seguridad_roles.lista_editar_rol') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.seguridad.seguridad_roles.titulo_lista_eliminar_rol')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.seguridad.seguridad_roles.lista_eliminar_rol') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>	
</div>