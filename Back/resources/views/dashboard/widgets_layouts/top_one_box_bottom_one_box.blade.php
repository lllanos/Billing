<div class="row">
@foreach ($userWidgets as $widgetKey => $widget)
  <div class="col-md-12">
    <div class="widget-container">
      @include('dashboard.widgets_layouts.base_content')
    </div>
  </div>
@endforeach
</div>
