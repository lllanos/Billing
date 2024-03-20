<div class="panel-body panel_con_tablas_y_sub_tablas p-0 contenedor_all_tablas">
	<div class="panel-group acordion colapsable_cero" id="ID_CAMBIAMEEEEE" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
          	<div class="panel-heading panel_heading_collapse p-0 primer_collapse_color" role="tab" id="headingOne_sub_sub_cat">
	            <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
					<a
						class="collapse_arrow collapsed"
						role="button" data-toggle="collapse"
						data-parent="#ID_CAMBIAMEEEEE"
						href="#ID_CAMBIAMEEEEE_PARA_PANELLL" aria-expanded="false"
						aria-controls="ID_CAMBIAMEEEEE_PARA_PANELLL"
					>
						<div class="d-flex container_datos_drop">
					  		<span class="container_icon_angle d-flex">
							    <i class="fa fa-angle-up"></i> 
						    	{{trans('analisis_precios.materiales_directos')}}
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
            {{-- Collapse --}}
				<div id="ID_CAMBIAMEEEEE_PARA_PANELLL" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne_sub_sub_cat">
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
            {{-- Collapse --}}
	    </div>
	</div>
</div>