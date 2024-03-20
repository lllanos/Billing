<div class="col-md-12">
	<div id="modal_que_es_rdp">
		<label class="ttl_label_modal">{{trans('ayuda.sistema_rdp.que_es_rdp.titulo')}}</label>
		<p class="parrafo_modal">{!!trans('ayuda.sistema_rdp.que_es_rdp.descripcion')!!}</p>
		<ul class="container_list ul_style">
			@foreach(trans('ayuda.sistema_rdp.que_es_rdp.lista') as $key => $value)
				<li>{!!$value!!}</li>
			@endforeach
		</ul>
	</div>
</div>
