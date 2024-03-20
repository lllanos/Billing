<div class="chart-container" id="tiempos_redeterminaciones"></div>

<script>
  $(document).ready(function() {
    Highcharts.chart('tiempos_redeterminaciones', {
      chart: {
        type: 'column'
    },
    lang: {
      viewFullscreen:     "{{trans('index.highcharts.pantalla_completa')}}",
      contextButtonTitle: "{{trans('index.highcharts.menu_descarga')}}",
      printChart:         "{{trans('index.highcharts.print_chart')}}",
      downloadJPEG:       "{{trans('index.highcharts.download_jpg')}}",
      downloadPDF:        "{{trans('index.highcharts.download_pdf')}}",
      downloadPNG:        "{{trans('index.highcharts.download_png')}}",
      downloadSVG:        "{{trans('index.highcharts.download_svg')}}",
    },
    title: {
        text: ''
    },
    xAxis: {
        categories: [
            '',
        ]
    },
    yAxis: [{
        min: 0,
        title: {
            text: "{{trans('index.highcharts.dias')}}"
        }
    }, {
        title: {
            text: ""
        },
        opposite: true
    }],
    plotOptions: {
        column: {
            grouping: false,
            shadow: false,
            borderWidth: 0
        }
    },
    series: {!! json_encode($serie1) !!},
    credits: {
       enabled: false
     },
  });
});
</script>
