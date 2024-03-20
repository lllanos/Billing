<div>
	<p class="parrafo_modal">
		{!!trans('ayuda.indices.descripcion')!!}
	</p>
	<ul class="container_list ul_style">			
		@foreach(trans('ayuda.indices.lista') as $key => $value)
			<li>{!!$value!!}</li>
		@endforeach
	</ul>
</div>