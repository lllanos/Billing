    {{ Form::open(array('url' => 'update/widgets', 'method' => 'POST') )}}
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" id="close_modal" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true" class="fa fa-times fa-2x"></span>
          </button>
          <h4 class="modal-title">{{trans('index.widgets')}}</h4>
        </div>
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="modalContentScrollable">
            <div class="row">
              <div class="col-md-12 col-sm-12">

            @foreach ($widgets as $widgetskey => $widget)
              @permissions(($widget->nombre))
                <div class="row row-border-bottom">
                  <div class="col-md-3">
                    <input type="checkbox" name="widget[{{$widget->id}}]" class="chk-widget hidden"
                        @if(array_key_exists($widget->id, $userWidgetsEdit)) checked @endif>
                    <img src="{{asset('img/widgets-icons/'.$widget->nombre.'.png')}}"
                    class="img-widget @if(array_key_exists($widget->id, $userWidgetsEdit)) widget-seleccionado @endif"
                        >
                  </div>
                  <div class="col-md-9">
                    <strong>{{trans('widgets.'.$widget->nombre.'.nombre')}}</strong>
                    <br>
                    {{trans('widgets.'.$widget->nombre.'.descripcion')}}
                  </div>
                </div>
                <br>
                @endpermission
            @endforeach

          </div>
        </div>
      </div>
    </div><!-- /.modal-body -->
    <div class="modal-footer no-padding-bottom">
      <div class="col-md-12 col-sm-12 content-buttons-bottom-form footer-confirm">
        <div class="text-right">
          {{ Form::submit(trans('forms.guardar'), array('class' => 'btn btn-primary pull-right')) }}
         </div>
      </div>
    </div>
  </div><!-- /.modal-content -->
{{ Form::close() }}
