<div class="hist_instancia_icon">
	<i class="fa fa-calendar" aria-hidden="true"></i>
	<label>{{ $valueInstancia->created_at}}</label>
</div>
<div class="hist_instancia_icon">
	<i class="fa fa-user-circle" aria-hidden="true"></i>
	<label>
		@if($redeterminacion->user_contratista_id != null)
			{{$redeterminacion->contratista->user->nombre_apellido}}
		@else
			{{trans('redeterminaciones.redetermino_automaticamente')}}
		@endif
	</label>
</div>
