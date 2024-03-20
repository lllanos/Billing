<div class="contenedor_badges_estado_contrato">
	@if($redeterminacion->en_curso)
		<span class="badge badge-referencias badge_esperando">{{trans('index.esperando')}}</span>
	@endif
	<span class="badge badge-referencias container_estado_redeterminacion @if($redeterminacion->en_curso) badge_esperando_estado @endif" style="background: #{{$redeterminacion->estado_nombre_color['color']}}" >
		<span>{{$redeterminacion->estado_nombre_color['nombre']}}</span>
	</span>
</div>
