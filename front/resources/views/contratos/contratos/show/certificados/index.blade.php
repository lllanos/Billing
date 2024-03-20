<input type="hidden" id="certificados_version" value="{{$opciones['version']}}" />
<div class="panel-default acordion" id="accordion-certificados" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading p-0 panel_heading_collapse" role="tab" id="headingOne-certificados">
      <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
        <a class="btn_acordion dos_datos collapse_arrow @if(!isset($fromAjax)) visualizacion @endif" role="button" data-toggle="collapse" data-parent="#accordion-certificados" href="#collapseOne_certificados" aria-expanded="true" aria-controls="collapseOne_certificados"
          @if(!isset($fromAjax)) data-seccion="certificados" data-version="{{$opciones['version']}}" @endif>
          <div class="container_icon_angle"><i class="fa fa-angle-down"></i> @trans('forms.certificados')</div>
        </a>
      </h4>
    </div>

    <div id="collapseOne_certificados" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-certificados">
      @if(isset($fromAjax))
        @if($contrato->has_certificados)
         <div class="row">
           <div class="col-md-12">
             <!--is basico-->
             @php ($sufijo = 'basico')
             <div class="panel-group colapsable_top mt-1" id="accordion{{$sufijo}}" role="tablist" aria-multiselectable="true">
               <div class="panel panel-default">
                 <div class="panel-body pt-0 pb-0">
                   <div class="panel-body panel_con_tablas_y_sub_tablas contenedor_all_tablas pt-1 pl-0 pr-0">
                     <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading{{$sufijo}}">
                       <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
                         <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion{{$sufijo}}" href="#collpapse{{$sufijo}}" aria-expanded="true" aria-controls="collpapse{{$sufijo}}">
                           <div class="container_icon_angle">
                             <i class="fa fa-angle-down"></i> @trans('forms.certificados') @trans('certificado.basicos')
                           </div>
                         </a>
                       </h4>
                     </div>

                     <div id="collpapse{{$sufijo}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub{{$sufijo}}">
                       <div class="row">
                         <div class="col-md-12 col-sm-12">
                           <div class="panel-body p-0">
                             <div class="zui-wrapper zui-action-32px-fixed">
                               <div class="zui-scroller zui-no-data">
                                 @include('contratos.contratos.show.certificados.show.tabla', ['empalme' => false, 'redeterminados' => false])
                               </div>
                             </div>
                           </div>
                         </div>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
           </div>
         </div>

        @else
          <div class="panel-body p-0">
            <div class="sin_datos_js"></div>
            <div class="sin_datos">
              <h1 class="text-center">@trans('index.no_datos')</h1>
            </div>
          </div>
        @endif
      @endif
    {{-- </div> --}}

    {{--  @if(count($contrato->certificados_redeterminados_empalme)) --}}
    @if(count($contrato->certificados_redeterminados))
      <div id="collapseOne_certificados" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne-certificados">
        @if(isset($fromAjax))
           <div class="row">
             <div class="col-md-12">
               <!--is redeterminado-->
               @php ($sufijo = 'redeterminado')
               <div class="panel-group colapsable_top mt-1" id="accordion{{$sufijo}}" role="tablist" aria-multiselectable="true">
                 <div class="panel panel-default">
                   <div class="panel-body pt-0 pb-0">
                     <div class="panel-body panel_con_tablas_y_sub_tablas contenedor_all_tablas pt-1 pl-0 pr-0">
                       <div class="panel-heading panel_heading_padre p-0 panel_heading_collapse" role="tab" id="heading{{$sufijo}}">
                         <h4 class="panel-title titulo_collapse m-0 panel_title_btn">
                           <a class="btn_acordion datos_as_table collapse_arrow" role="button" data-toggle="collapse" data-parent="#accordion{{$sufijo}}" href="#collpapse{{$sufijo}}" aria-expanded="true" aria-controls="collpapse{{$sufijo}}">
                             <div class="container_icon_angle">
                               <i class="fa fa-angle-down"></i> @trans('forms.certificados') @trans('certificado.redeterminados')
                             </div>
                           </a>
                         </h4>
                       </div>

                       <div id="collpapse{{$sufijo}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_sub{{$sufijo}}">
                         <div class="row">
                           <div class="col-md-12 col-sm-12">
                             <div class="panel-body p-0">
                               <div class="zui-wrapper zui-action-32px-fixed">
                                 <div class="zui-scroller zui-no-data">
                                   @include('contratos.contratos.show.certificados.show.tabla', ['empalme' => false, 'redeterminados' => true])
                                 </div>
                               </div>
                             </div>
                           </div>
                         </div>
                       </div>
                     </div>
                   </div>
                 </div>
               </div>
             </div>
           </div>
        @endif
      @endif
    </div>
  </div>
</div>
