<div id="modalDashboard" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="fa fa-times fa-2x"></span>
        </button>
        <h4 class="modal-title">{!!trans('index.widgets')!!}</h4>
      </div>
      <div class="modal-body">
				<div class="modalContentScrollable">

          <div class="col-md-12">
            {{ Form::open(array('url' => 'update/widgets', 'method' => 'POST') )}}
              {{ csrf_field() }}

              @foreach ($widgets as $widgetskey => $widget)
                <div class="row row-border-bottom">
                  <div class="col-md-3 aa">
                    <input type="checkbox" name="widget[{{$widget->id}}]" class="chk-widget hidden"
                        @if(array_key_exists($widget->id, $userWidgetsEdit)) checked @endif>
                    <img src="{{asset('img/widgets-icons/'.$widget->nombre.'.png')}}"
                    class="img-widget @if(array_key_exists($widget->id, $userWidgetsEdit)) widget-seleccionado @endif"
                         border="0">
                  </div>
                  <div class="col-md-9 ss">
                    <strong>{{trans('widgets.'.$widget->nombre.'.nombre')}}</strong>
                    <br>
                    {{trans('widgets.'.$widget->nombre.'.descripcion')}}
                  </div>
                </div>
                <br>
              @endforeach

            <div class="modal-actions">
              {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right')) }}
            </div>
            {{ Form::close() }}
          </div>
				</div>
      </div><!-- /.modal-body -->
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal-id -->
