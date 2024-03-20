<div class="col-md-12">
	<div id="modal_pantalla_inicio">
		<label class="ttl_label_modal">{!!trans('ayuda.sistema_rdp.pantalla_inicio.titulo')!!}</label>
		<p class="parrafo_modal">
			{!!trans('ayuda.sistema_rdp.pantalla_inicio.descripcion')!!}
		</p>
		<p class="parrafo_modal m-0">
			{!!trans('ayuda.sistema_rdp.pantalla_inicio.titulo_lista_contratos')!!}
		</p>
		<ul class="container_list ul_style">			
			@foreach(trans('ayuda.sistema_rdp.pantalla_inicio.lista_contratos') as $key => $value)
				<li>{!!$value!!}</li>
			@endforeach			
		</ul>
		<p class="parrafo_modal m-0">
			{!!trans('ayuda.sistema_rdp.pantalla_inicio.titulo_lista_solicitudes')!!}
		</p>
		<ul class="container_list ul_style">			
			@foreach(trans('ayuda.sistema_rdp.pantalla_inicio.lista_solicitudes') as $key => $value)
				<li>{!!$value!!}</li>
			@endforeach
		</ul>
		<div class="container_img_ayuda">
			<img src="{{asset('img/ayuda/imagen_7.png')}}">		
		</div>
		<p class="parrafo_modal">
			{!!trans('ayuda.sistema_rdp.pantalla_inicio.nota')!!}
		</p>
	</div>
	
</div>