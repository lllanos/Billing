<div class="col-md-12 col-sm-12">
	<ul class="ul--general">
		<div class="">
			<ul class="estado">
				<li class="estadoItem">
					<div class="estadoStaticData">
						<span class="estadoCirculoHideTopLine"></span>
							<div class="estadoCirculoInicial"></div>
					</div>
				</li>
				@php  $i = 1; @endphp
        @foreach ($instancias as $keyInstancia => $valueInstancia)
					<li class="estadoItem">
						<div class="estadoStaticData">
							<span class="estadoCirculo" style="background-color:#{{$valueInstancia->estado_nombre_color['color']}}; border-color:#{{$valueInstancia->estado_nombre_color['color']}};">
								{{$i}}
							</span>
							<h3 class="estadoNombreEtapa dropdown_contenido_fecha">
								{{$valueInstancia->estado_nombre_color['nombre']}}
							</h3>
							@foreach ($valueInstancia->sub_instancias_por_fecha as $keySubInstancia => $valueSubInstanciaPorFecha)
								<div class="etapaEvaluacionContent">
									<div class="container_hist_fecha_dropdown">
										<a href="javascript:void(0)" class="fecha_dropdown_hist">
											<i class="fa fa-calendar" aria-hidden="true"></i>
											<label class="m-0">{{$keySubInstancia}}</label>
											<i class="fa fa-caret-down iconArrow" aria-hidden="true"></i>
										</a>
										@foreach ($valueSubInstanciaPorFecha as $keySubInstanciaPorFecha => $valueSubInstanciaPorFecha)
											@if(strpos($valueSubInstanciaPorFecha->accion, 'create_instancia') !== false)
												<div class="contenido_por_fecha">
													<div class="ml-1">
														<div class="contenido_proceso_hist_redeterminaciones d-flex">
															<span class="contenido_proc_item title_contenido_por_fecha">
																@trans('analisis_precios.acciones_historial.create_instancia_' . $valueInstancia->estado->nombre)</span>
																<span class="contenido_proc_item contenido_por_fecha_user pull-right">
																	<i class="fa fa-user-circle" aria-hidden="true"></i> {{$valueInstancia->user_creator->nombre_apellido}}
																</span>
														</div>
													</div>
												</div>
											@else
												<div class="contenido_por_fecha">
													<div class="ml-1">
														<div class="contenido_proceso_hist_redeterminaciones">
															<span class="contenido_proc_item title_contenido_por_fecha">
																{{$valueSubInstanciaPorFecha->clase->data_historial}}</span>
														</div>
														<div class="ml-1">
															<div class="contenido_proceso_hist_redeterminaciones">
																<div class="contenido_por_fecha_dato_user">
																	<span class="contenido_proc_item">
																		{{$valueSubInstanciaPorFecha->data_historial}} @trans('analisis_precios.acciones_historial.' . $valueSubInstanciaPorFecha->accion)
																		<a href="javascript:void(0)" class="ver_mas"
																		data-id="{{$valueSubInstanciaPorFecha->id}}"
																		data-url="{{route('AnalisisPrecios.historialDetalle', ['subinstacia_id' => $valueSubInstanciaPorFecha->id])}}">@trans('forms.ver_mas')</a>
																	</span>
																	<span class="contenido_proc_item contenido_por_fecha_user">
																		<i class="fa fa-user-circle" aria-hidden="true"></i> {{$valueInstancia->user_creator->nombre_apellido}}
																	</span>
																</div>
																<!--Ver mas-->
																<span class="contenido_proc_item ver_mas_hidden oculto" id="contenido_vermas_{{$valueSubInstanciaPorFecha->id}}"></span>
																<!--Fin Ver mas-->
															</div>
														</div>
													</div>
												</div>
											@endif
										@endforeach
									</div>
								</div>
							@endforeach
						</div>
					</li>
					<br>
					@php $i++; @endphp
				@endforeach
			</ul>
			<div class="circleBottomEmpty"></div>
		</div>
	</ul>
</div>
<script type="text/javascript">
	$('.fecha_dropdown_hist').on('click', function() {
		let contenido_fecha = $(this).siblings('.contenido_por_fecha');
		let iconArrow = $(this).children('.iconArrow');
		if(iconArrow.hasClass('fa-caret-down')) {
			iconArrow.removeClass('fa-caret-down');
			iconArrow.addClass('fa-caret-up');
		}else if(iconArrow.hasClass('fa-caret-up')) {
			iconArrow.removeClass('fa-caret-up');
			iconArrow.addClass('fa-caret-down');
		}

		contenido_fecha.fadeToggle("slow");
	});

	$('.ver_mas').on('click', function() {
		var url = $(this).data('url');
		var id = $(this).data('id');
		let $aVerMas = $(this);
		let ver_mas_hermano = $aVerMas.parent().parent();

		$.get(url, function(data) {
			$('#contenido_vermas_' + id).html(data);
			let ver_mas = ver_mas_hermano.siblings('.ver_mas_hidden');
			if(ver_mas.hasClass('oculto')) {
				$aVerMas.text('Ver Menos');
				ver_mas.removeClass('oculto');
			}else if(ver_mas.not('oculto')) {
				$aVerMas.text('Ver Más');
				ver_mas.addClass('oculto');
			}
			ver_mas.fadeToggle("slow");
		});
	});
</script>
