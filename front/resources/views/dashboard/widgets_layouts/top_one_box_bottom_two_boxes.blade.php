<div class="row">
@foreach ($userWidgets as $widgetKey => $widget)
  <div class="@if($loop->index==0) col-md-12 @else col-md-6 @endif">
    <div class="widget-container">
      @include('dashboard.widgets_layouts.base_content')
    </div>
  </div>
@endforeach
</div>
