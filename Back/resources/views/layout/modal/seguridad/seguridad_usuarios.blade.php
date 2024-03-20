<div>
	<label class="ttl_label_modal">
		{!!trans('ayuda.seguridad.seguridad_usuarios.titulo')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.seguridad.seguridad_usuarios.descripcion')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.seguridad.seguridad_usuarios.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.seguridad.seguridad_usuarios.titulo_lista_pasos_crear_usuario')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.seguridad.seguridad_usuarios.lista_pasos_crear_usuario') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.seguridad.seguridad_usuarios.titulo_lista_pasos_editar_usuario')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.seguridad.seguridad_usuarios.lista_pasos_editar_usuario') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.seguridad.seguridad_usuarios.titulo_lista_pasos_eliminar_usuario')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.seguridad.seguridad_usuarios.lista_pasos_eliminar_usuario') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>	
</div>