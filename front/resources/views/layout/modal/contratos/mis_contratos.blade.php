<div class="col-md-12">
	<div id="modal_mis_contratos">
		<label class="ttl_label_modal">{!!trans('ayuda.contratos.mis_contratos.titulo')!!}</label>
		<p class="parrafo_modal">
			{!!trans('ayuda.contratos.mis_contratos.descripcion')!!}
		</p>
		<p class="parrafo_modal">
			{!!trans('ayuda.contratos.mis_contratos.titulo_lista_seccion')!!}
		</p>
		<ul class="container_list ul_style">
			@foreach(trans('ayuda.contratos.mis_contratos.lista_seccion') as $key => $value)
				<li>{!!$value!!}</li>
			@endforeach
		</ul>
		<p class="parrafo_modal">
			{!!trans('ayuda.contratos.mis_contratos.titulo_lista_mis_contratos')!!}
		</p>
		<ul class="container_list ul_style">
			@foreach(trans('ayuda.contratos.mis_contratos.lista_mis_contratos') as $keyMisContratos => $valueMisContratos)
				@if(!is_array($valueMisContratos))
					<li>
						{!!$valueMisContratos!!}
					</li>
				@else
					<ul class="container_list ul_style">
						@foreach($valueMisContratos as $keysub => $submenu)
							<li>{!!$submenu!!}</li>
						@endforeach
					</ul>
				@endif
			@endforeach
		</ul>
		<div class="container_img_ayuda">
			<img src="{{asset('img/ayuda/listado_contratos.png')}}">
		</div>
		<p class="parrafo_modal">
			{!!trans('ayuda.contratos.mis_contratos.nota_1')!!}
		</p>
		<p class="parrafo_modal">
			{!!trans('ayuda.contratos.mis_contratos.titulo_lista_pasos_detalle_solic')!!}
		</p>
		<ul class="container_list ul_style_num">
			@foreach(trans('ayuda.contratos.mis_contratos.lista_pasos_detalle_solic') as $keys_detalle_solic => $values_detalle_solic)
				<li>{!!$values_detalle_solic!!}</li>
			@endforeach
		</ul>
		<div class="container_img_ayuda">
			<img src="{{asset('img/ayuda/imagen_12.png')}}">
		</div>
		<p class="parrafo_modal">
			{!!trans('ayuda.contratos.mis_contratos.nota_2')!!}
		</p>
		<p class="parrafo_modal">
			{!!trans('ayuda.contratos.mis_contratos.titulo_lista_pasos_detalle_solic_2')!!}
		</p>
		<ul class="container_list ul_style_num">
			@foreach(trans('ayuda.contratos.mis_contratos.lista_pasos_detalle_solic_2') as $keys_detalle_solic_2 => $values_detalle_solic_2)
				<li>{!!$values_detalle_solic_2!!}</li>
			@endforeach
		</ul>
		<div id="contrato_editar_items">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.mis_contratos.subtitulo_editar_itemizado')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_editar_Itemizado')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_19.png')}}">
	</div>

	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_editar_items')!!}
	</p>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.mis_contratos.items_Completo') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	</div>
	<div id="contrato_editar_polinomica">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.mis_contratos.subtitulo_editar_polinomica')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_editar_Polinomica')!!}
	</p>
	</div>
	<div id="contrato_editar_Plan">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.mis_contratos.subtitulo_editar_plan')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_editar_Plan')!!}
	</p>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.mis_contratos.plan_Completo') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_Plan_porcentaje')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_22.png')}}">
	</div>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_Plan_moneda')!!}
	</p>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_Plan_curva')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_24.png')}}">
	</div>

	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_contrato_completo')!!}
	</p>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.mis_contratos.contrato_Completo') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	</div>
	<div id="contrato_adenda">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.mis_contratos.subtitulo_adendas')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_nueva_adenda')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_25.png')}}">
	</div>
	</div>
	<div id="contrato_ampliacion_rep">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.mis_contratos.subtitulo_ampliacion_rep')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_nueva_ampliacion_rep')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_26.png')}}">
	</div>
	</div>
	<div id="contrato_anticipo">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.mis_contratos.subtitulo_anticipo')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_agregar_anticipo')!!}
	</p>
	</div>

	
	</div>
	<div id="contrato_analisis">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.mis_contratos.subtitulo_analisis_precios')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_completar_analisis')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_30.png')}}">
	</div>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.mis_contratos.lista_Analisis_precios') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	</div>
	<div id="contrato_certificados">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.mis_contratos.subtitulo_Certificados')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.mis_contratos.titulo_certificados')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_33.png')}}">
	</div>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.mis_contratos.lista_certificados') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_32.png')}}">
	</div>
	</div>
	</div>
</div>
