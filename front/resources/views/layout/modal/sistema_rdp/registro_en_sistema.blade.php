<div class="col-md-12" id="modal_registro_en_sistema">
	<div id="registro">
		<label class="ttl_label_modal">{!!trans('ayuda.sistema_rdp.registro_en_sistema.titulo')!!}</label>
		<p class="parrafo_modal">
			{!!trans('ayuda.sistema_rdp.registro_en_sistema.descripcion')!!}
			<a href="https://desa-redeterminacion.vialidad.gob.ar">{!!trans('ayuda.sistema_rdp.url')!!}</a>
		</p>
		<p class="parrafo_modal m-0">{!!trans('ayuda.sistema_rdp.registro_en_sistema.titulo_lista')!!}</p>
		<ul class="container_list ul_style_num">			
			@foreach(trans('ayuda.sistema_rdp.registro_en_sistema.lista') as $key => $value)
				<li>{!!$value!!}</li>
			@endforeach			
		</ul>
	</div>	
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_1.png')}}">		
	</div>
</div>