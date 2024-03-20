<div>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.descripcion')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_2.png')}}">
	</div>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_lista')!!}
	</p>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_lista_contratos')!!}
	</p>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.lista_contratos') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>

	<div id="contrato_nuevo_contrato">


	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.subtitulo_nuevo_contrato')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_nuevo_Contrato')!!}
	</p>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.nuevo_contratos') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_18.png')}}">
	</div>
	</div>
	<div id="contrato_editar_contrato">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.subtitulo_editar_contrato')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_editar_Contrato')!!}
	</p>
	</div>
	<div id="contrato_editar_items">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.subtitulo_editar_itemizado')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_editar_Itemizado')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_19.png')}}">
	</div>
	<div id="contrato_firmar_contrato">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.titulo_firmar_Contrato')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_editar_Contrato')!!}
	</p>
	</div>

	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_editar_items')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_21.png')}}">
	</div>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.items_Completo') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	</div>
	<div id="contrato_editar_polinomica">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.subtitulo_editar_polinomica')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_editar_Polinomica')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_20.png')}}">
	</div>
	</div>
	<div id="contrato_editar_Plan">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.subtitulo_editar_plan')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_editar_Plan')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_28.png')}}">
	</div>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.plan_Completo') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_Plan_porcentaje')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_22.png')}}">
	</div>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_Plan_moneda')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_23.png')}}">
	</div>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_Plan_curva')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_24.png')}}">
	</div>

	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_contrato_completo')!!}
	</p>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.contrato_Completo') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	</div>
	<div id="contrato_adenda">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.subtitulo_adendas')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_nueva_adenda')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_25.png')}}">
	</div>
	</div>
	<div id="contrato_ampliacion_rep">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.subtitulo_ampliacion_rep')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_nueva_ampliacion_rep')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_26.png')}}">
	</div>
	</div>
	<div id="contrato_anticipo">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.subtitulo_anticipo')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_agregar_anticipo')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_27.png')}}">
	</div>
	</div>

	<div id="contrato_empalme">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.subtitulo_empalme')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_agregar_empalme')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_34.png')}}">
	</div>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.lista_Empalme') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_35.png')}}">
	</div>
		<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_36.png')}}">
	</div>
	</div>
	<div id="contrato_analisis">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.subtitulo_analisis_precios')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_completar_analisis')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_30.png')}}">
	</div>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.lista_Analisis_precios') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_31.png')}}">
	</div>
	</div>
	<div id="contrato_certificados">
	<label class="ttl_label_modal">
		{!!trans('ayuda.contratos.subtitulo_Certificados')!!}
	</label>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.titulo_certificados')!!}
	</p>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_33.png')}}">
	</div>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.lista_certificados') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<div class="container_img_ayuda">
		<img src="{{asset('img/ayuda/imagen_32.png')}}">
	</div>
	</div>
</div>
