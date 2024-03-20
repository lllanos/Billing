<div class="row">
  @foreach ($userWidgets as $widgetKey => $widget)
  <div class="@if($loop->index < 2) col-md-6 mb-1_5 @else col-md-12 @endif">
    <div class="widget-container mb-0">
      @include('dashboard.widgets_layouts.base_content')
    </div>
  </div>
  @endforeach
</div>
