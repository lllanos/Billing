<div class="contenedor_badges_estado_contrato">
	@if($solicitud->en_curso)
		<span class="badge badge-referencias badge_esperando">@trans('index.esperando')</span>
	@endif
	<span class="badge badge-referencias container_estado_redeterminacion @if($solicitud->en_curso) badge_esperando_estado @endif" style="background: #{{$solicitud->estado_nombre_color['color']}}" >
		<span>{{$solicitud->estado_nombre_color['nombre']}}</span>
	</span>
</div>
