<div class="chart-container" id="redeterminaciones_por_estado"></div>

<script>
  $(document).ready(function() {

    Highcharts.chart('redeterminaciones_por_estado', {
      chart: {
          plotBackgroundColor: null,
          plotBorderWidth: null,
          plotShadow: false,
          type: 'pie',
          height: 325,
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
      tooltip: {
          pointFormat: "{{trans('index.highcharts.total')}}" + " <b>{point.y}</b><br>" +
                       "{{trans('index.highcharts.porcentaje')}}" + " <b>{point.percentage:.1f}%</b>"
      },
      plotOptions: {
          pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: false
              },
              showInLegend: true
          }
      },
      series: [{
        name: 'Cantidad',
          data:  {!! json_encode($serie1) !!}
      }],
      credits: {
         enabled: false
       },
        exporting: {
            filename: "{{trans('widgets.redeterminaciones_por_estado.nombre')}}_{{date('j_m_Y')}}"
        }
    });
  });

</script>
