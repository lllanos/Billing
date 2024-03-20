@if($analisis_item->item->is_unidad_medida)
  @if($categoria->tiene_subtotales)
    <div class="panel-body panel_sub_tablas pl-1 pt-1 pr-1 pb-0">
      <div class="col-md-12 col-sm-12">
        <div class="row list-table pt-0 pb-0">
          <div class="zui-wrapper zui-action-32px-fixed">
            <div class="zui-scroller zui-no-data"> <!-- zui-no-data -->
              <table class="table table-striped table-hover table-bordered zui-table">
                <tbody class="tbody_tooltip">
                  <tr>
                    <td class="">@trans('analisis_item.subtotal_ejecucion') (@trans('analisis_item.subtotal_mo_eq'))
                    </td>
                    <td class="td-200px">@toDosDec($categoria->subtotal_mo_eq)</td>
                  </tr>
    @endif

    @if($categoria->tiene_rendimiento)
                  <tr>
                    <td class="">@trans('analisis_item.rendimiento')</td>
                    <td class="td-200px">
                      @toDosDec($categoria->rendimiento->valor)
                      @if($categoria->rendimiento->unidad != null) {{$categoria->rendimiento->unidad->nombre}} @endif
                    </td>
                  </tr>
    @endif
      @if($categoria->tiene_subtotales)
                  <tr>
                    <td class="">@trans('analisis_item.subtotal_sobre_rendimiento')</td>
                    <td class="td-200px">@toDosDec($categoria->subtotal_sobre_rendimiento)</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif
@endif
