<div class="col-md-12" id="modal_inicio_sesion">
	<div id="inicio_sesion">
		<label class="ttl_label_modal">{!!trans('ayuda.sistema_rdp.inicio_sesion.titulo')!!}</label>
		<p class="parrafo_modal">{!!trans('ayuda.sistema_rdp.inicio_sesion.descripcion')!!}</p>
		<p class="parrafo_modal m-0">{!!trans('ayuda.sistema_rdp.inicio_sesion.titulo_lista')!!}</p>
		<ul class="container_list ul_style_num">			
			@foreach(trans('ayuda.sistema_rdp.inicio_sesion.lista') as $key => $value)
				<li>{!!$value!!}</li>
			@endforeach		
		</ul>
	</div>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_2.png')}}">		
	</div>
</div>