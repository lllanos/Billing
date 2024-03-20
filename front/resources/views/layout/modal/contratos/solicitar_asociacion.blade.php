<div class="col-md-12">
	<div id="modal_solicitar_asociacion">
		<label class="ttl_label_modal">{!!trans('ayuda.contratos.solicitar_asociacion.titulo')!!}</label>
		<p class="parrafo_modal">
			{!!trans('ayuda.contratos.solicitar_asociacion.descripcion')!!}
		</p>
		<p class="parrafo_modal m-0">
			{!!trans('ayuda.contratos.solicitar_asociacion.titulo_lista')!!}
		</p>
		<ul class="container_list ul_style_num">			
			@foreach(trans('ayuda.contratos.solicitar_asociacion.lista') as $key => $value)					
				@if(!is_array($value))
					<li>
						{!!$value!!}
					</li>
				@else
					<ul class="container_list ul_style_num">
						@foreach($value as $keysub => $submenu)
							<li>{!!$submenu!!}</li>
						@endforeach
					</ul>
				@endif				
			@endforeach			
		</ul>
		<div class="container_img_ayuda">
			<img src="{{asset('img/ayuda/imagen_9.png')}}">		
		</div>		
		<p class="parrafo_modal m-0">
			{!!trans('ayuda.contratos.solicitar_asociacion.nota')!!}
		</p>
	</div>
</div>