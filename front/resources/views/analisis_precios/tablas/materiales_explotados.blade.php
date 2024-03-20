<div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
	{{-- foreach --}}
		<div class="panel-group acordion colapsable_cero" id="ID_CAMBIAR_____" role="tablist" aria-multiselectable="true">
			<div class="panel panel-default">
				{{-- Dropdown 0 --}}
					<div class="panel-heading p-0 panel_heading_collapse primer_collapse_color" role="tab">
						<h4 class="panel-title titulo_collapse m-0 d-flex">
							<a 
								class="btn_acordion dos_datos collapse_arrow collapsed" role="button" data-toggle="collapse" 
								data-parent="#ID_CAMBIAR_____" href="#ID_CAMBIAR_____PARA_PANEL" 
								aria-expanded="false" aria-controls="ID_CAMBIAR_____PARA_PANEL"
							>
								<div class="d-flex container_datos_drop w-100">
									<span class="container_icon_angle">
										<i class="fa fa-angle-up"></i> 
										{{trans('analisis_precios.materiales_explotados')}}
									</span>					
								</div>
							</a>
							<div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
								<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
									<i class="fa fa-ellipsis-v"></i>
								</button>
								<ul class="dropdown-menu pull-right">
									<li>
		                                <a class="modal-historial" href="javascript:void(0)" data-url="">
		                              		<i class="fa fa-plus"></i></i> {{trans('analisis_precios.agregar_material_directo')}}
		                                </a>
									</li>
								</ul>
							</div>
						</h4>
					</div>
				{{-- Fin Dropdown 0 --}}
		        {{-- 1r panel --}}
	          		<div id="ID_CAMBIAR_____PARA_PANEL" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
	            		<div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
	            			{{-- foreach --}}
								<div class="panel-group colapsable_uno" id="ID_CAMBIAR_____2" role="tablist" aria-multiselectable="true">
									<div class="panel panel-default">
					                    {{-- Dropdown 1 --}}
				                      		<div class="panel-heading  p-0 panel_heading_collapse segundo_collapse_color" role="tab">
					                        	<h4 class="panel-title titulo_collapse m-0 panel_title_btn">
					                          		<a 
						                          		class="collapse_arrow collapsed" role="button" data-toggle="collapse"
							                            data-parent="#ID_CAMBIAR_____2"
							                            href="#ID_CAMBIAR_____PARA_PANEL2" aria-expanded="false"
							                            aria-controls="ID_CAMBIAR_____PARA_PANEL2"
						                          	>
					                            		<div class="d-flex container_datos_drop">
					                              			<span class="container_icon_angle d-flex">
								                                <i class="fa fa-angle-up mr-_5"></i> 
								                                AGREGADO PETREO TRITURADO 06 P/BASE GRANULAR
						                              		</span>
							                              	<span class="d-flex-colum">
								                                <span></span>
								                                <span>TN</span>                                
							                              	</span>
						                              		<span class="d-flex-colum">
						                                		<span class="precio_analisis">$ 5.000</span>
						                                		<span class="total_analisis">{{trans('analisis_precios.total_adaptado')}}</span> 
						                              		</span>
					                            		</div>                            
					                          		</a>
													<div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
														<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
															<i class="fa fa-ellipsis-v"></i>
														</button>
														<ul class="dropdown-menu pull-right">
															<li>
								                                <a class="modal-historial" href="javascript:void(0)" data-url="">
								                              		<i class="fa fa-plus"></i></i> {{trans('analisis_precios.agregar_material_directo')}}
								                                </a>
															</li>
														</ul>
													</div>					                          		
					                        	</h4>
					                      	</div>
					                    {{-- Fin Dropdown 1 --}}
					                    {{-- 2do panel --}}
										<div id="ID_CAMBIAR_____PARA_PANEL2" class="panel-collapse collapse" role="tabpanel">
									  		<div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
									  			{{-- foreach --}}
									  				<div class="panel-group colapsable_dos" id="ID_CAMBIAR_____3" role="tablist" aria-multiselectable="true">
												        <div class="panel panel-default">
												        	{{-- Dropdown 2 --}}
												          		<div class="panel-heading panel_heading_collapse p-0 tercer_collapse_color" role="tab">
													            	<h4 class="panel-title titulo_collapse m-0 panel_title_btn">
																		<a
																			class="collapse_arrow collapsed"
																			role="button" data-toggle="collapse"
																			data-parent="#ID_CAMBIAR_____3"
																			href="#ID_CAMBIAR_____PARA_PANEL3" aria-expanded="false"
																			aria-controls="ID_CAMBIAR_____PARA_PANEL3"
																		>
																			<div class="d-flex container_datos_drop">
																				<span class="container_icon_angle d-flex">
																					<i class="fa fa-angle-up"></i> 
																					MATERIALES
																				</span>
																				<span class="d-flex-colum">
																					<span>$24.500.000,80</span>
																					<span>{{trans('analisis_precios.total_calculado')}}</span>
																				</span>
																				<span class="d-flex-colum"> 
																					<span>$24.500.000</span>
																					<span>{{trans('analisis_precios.total_adaptado')}}</span>
																				</span>
																			</div>            
																		</a>
																		<div class="dropdown container_btn_action" data-toggle="tooltip" data-placement="bottom" title="@trans('index.opciones')">
																			<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
																				<i class="fa fa-ellipsis-v"></i>
																			</button>
																			<ul class="dropdown-menu pull-right">
																				<li>
													                                <a class="modal-historial" href="javascript:void(0)" data-url="">
													                              		<i class="fa fa-plus"></i></i> {{trans('analisis_precios.agregar_material_directo')}}
													                                </a>
																				</li>
																			</ul>
																		</div>
																	</h4>
																</div>
															{{-- Fin Dropdown 2 --}}
															{{-- 3r panel --}}															        
													          	<div id="ID_CAMBIAR_____PARA_PANEL3" class="panel-collapse collapse" role="tabpanel">
													            	<div class="panel-body panel_con_tablas_y_sub_tablas p-0">
													              		<!--Tabla scrollable-->
													                		<div class="col-md-12 col-sm-12">
													                  			<div class="row list-table pt-0 pb-1">
													                    			<div class="zui-wrapper zui-action-32px-fixed">
													                      				<div class="zui-scroller"> <!-- zui-no-data -->
													                        				<table class="table table-striped table-hover table-bordered zui-table">
													                          					<thead>
														                            				<tr>
																										<th>{{trans('forms.nombre')}}</th>
																										<th>Cantidad</th>
																										<th>Valor Unitario</th>
																										<th>Total</th>
																										<th class="actions-col"><i class="glyphicon glyphicon-cog"></i></th>
														                            				</tr>
													                          					</thead>
													                          					<tbody class="tbody_tooltip">
																									<tr>
																										<td>nombre</td>
																										<td>cantidad</td>
																										<td class="text-right">$12313</td>
																										<td class="text-right">$123123112</td>
																										<td class="actions-col noFilter">
																											<div class="dropdown dd-on-table" data-toggle="tooltip" data-placement="left" title="@trans('index.opciones')">
																												<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-label="@trans('index.opciones')">
																													<i class="fa fa-ellipsis-v"></i>
																												</button>
																												<ul class="dropdown-menu pull-right">
																													@if(!isset($isExcel) && $contrato->instancia_actual_analisis->permite_editar)
																														<li><a href=""><i class="glyphicon glyphicon-pencil"></i> @trans('index.editar')</a></li>
																														<li><a href=""><i class="glyphicon glyphicon-remove"></i> @trans('index.eliminar')</a></li>
																													@endif
																												</ul>
																											</div>
																										</td>
																									</tr>
																								</tbody>
																							</table>
													                      				</div>
													                    			</div>
													                  			</div>
													                		</div>
													              		<!--Fin Tabla scrollable-->
													            	</div>
													          	</div>
															{{-- Fin 3r panel --}}
														</div>
													</div>
									  			{{-- fin foreach --}}
									  		</div>
									  	</div>

					                    {{--  Fin 2do panel --}}
									</div>
								</div>
							{{-- endforeach --}}
	            		</div>
	            	</div>
		        {{--Fin 1r panel --}}
			</div>
		</div>
	{{-- endforeach --}}
</div>
