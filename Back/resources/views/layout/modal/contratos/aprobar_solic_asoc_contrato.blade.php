<div>
    <h4>{!!trans('ayuda.contratos.aprobar_solic_asoc_contrato.titulo')!!}</h4>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.aprobar_solic_asoc_contrato.descripcion')!!}
	</p>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.aprobar_solic_asoc_contrato.modulo_1')!!}
	</p>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.aprobar_solic_asoc_contrato.titulo_lista_aprobar_solic_asoc_contrato')!!}
	</p>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.aprobar_solic_asoc_contrato.lista_modulo_1') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.aprobar_solic_asoc_contrato.modulo_2')!!}
	</p>
	<ul class="container_list ul_style">
		@foreach(trans('ayuda.contratos.aprobar_solic_asoc_contrato.lista_modulo_2') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
	<p class="parrafo_modal">
		{!!trans('ayuda.contratos.aprobar_solic_asoc_contrato.nota')!!}
	</p>
	<div class="alert alert-info" role="alert">
		{!!trans('ayuda.contratos.aprobar_solic_asoc_contrato.alert')!!}
	</div>
</div>
