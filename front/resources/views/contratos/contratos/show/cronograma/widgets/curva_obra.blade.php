<div class="chart-container" id="curva_inversion"></div>

<script>
  $(document).ready(function() {
    Highcharts.chart('curva_inversion', {
      title: {
        // text: "{{trans('cronograma.vista.nombre.curva_inversion')}}"
        text: ""
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
      tooltip: {
        headerFormat: "<b>{{trans('index.mes')}}" + '{point.key}</b><br>',
        pointFormat: '{series.name}: <b>{point.y}</b>',
        // shared: true
      },
      subtitle: {
        // text: {!! json_encode($title) !!}
        text: ""
      },

      yAxis: {
        title: {
          text: "{{trans('forms.montos')}}"
        }
      },
      xAxis: {
        tickInterval: 1
      },
      legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle'
      },

      plotOptions: {
        series: {
          label: {
            connectorAllowed: false
          },
          pointStart: 1
        }
      },
      series: {!! json_encode($serie1) !!},

      responsive: {
        rules: [{
          condition: {
            maxWidth: 500
          },
          chartOptions: {
            legend: {
              layout: 'horizontal',
              align: 'center',
              verticalAlign: 'bottom'
            }
          }
        }]
      },
      credits: {
         enabled: false
       },
        exporting: {
            filename: {!! json_encode($title) !!} + "_{{date('j_m_Y')}}"
        }
  });
});
</script>
