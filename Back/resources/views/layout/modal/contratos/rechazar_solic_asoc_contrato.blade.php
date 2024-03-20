<div>	
    <h4>{!!trans('ayuda.contratos.rechazar_solic_asoc_contrato.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.rechazar_solic_asoc_contrato.descripcion')!!}
	</p>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.rechazar_solic_asoc_contrato.modulo_1')!!}
	</p>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.rechazar_solic_asoc_contrato.titulo_pasos_rechazar')!!}
	</p>
	<ul class="container_list ul_style_num">			
		@foreach(trans('ayuda.contratos.rechazar_solic_asoc_contrato.lista_modulo_1') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.rechazar_solic_asoc_contrato.modulo_2')!!}
	</p>
	<ul class="container_list ul_style_num">			
		@foreach(trans('ayuda.contratos.rechazar_solic_asoc_contrato.lista_modulo_2') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.rechazar_solic_asoc_contrato.nota')!!}
	</p>
</div>