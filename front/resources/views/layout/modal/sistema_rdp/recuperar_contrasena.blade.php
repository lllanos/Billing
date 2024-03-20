<div class="col-md-12">
	<div id="modal_recuperar_contrasena">
		<label class="ttl_label_modal">{!!trans('ayuda.sistema_rdp.recuperar_contrasena.titulo')!!}</label>
		<p class="parrafo_modal">{!!trans('ayuda.sistema_rdp.recuperar_contrasena.descripcion')!!}</p>
		<p class="parrafo_modal m-0">{!!trans('ayuda.sistema_rdp.recuperar_contrasena.titulo_lista')!!}</p>
		<ul class="container_list ul_style_num">			
			@foreach(trans('ayuda.sistema_rdp.recuperar_contrasena.lista') as $key => $value)
				<li>{!!$value!!}</li>
			@endforeach	
		</ul>
	</div>	
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_3.png')}}">		
	</div>
</div>