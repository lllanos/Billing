<div class="row">
  @foreach ($userWidgets as $widgetKey => $widget)
  <div class="@if($loop->index < 2) col-md-6 @else col-md-12 @endif">
    <div class="widget-container">
      @include('dashboard.widgets_layouts.base_content')
    </div>
  </div>
  @endforeach
</div>
