
<div class="row">
  @foreach ($userWidgets as $widgetKey => $widget)
  <div class="col-md-6">
    <div class="widget-container mb-0">
      @include('dashboard.widgets_layouts.base_content')
    </div>
  </div>
  @endforeach
</div>
