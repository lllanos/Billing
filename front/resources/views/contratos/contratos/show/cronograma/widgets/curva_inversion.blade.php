<div class="chart-container" id="curva_inversion"></div>

<script>
  $(document).ready(function() {
    Highcharts.chart('curva_inversion', {
      chart: {
        zoomType: 'xy'
      },
      title: {
        // text: "{{trans('cronograma.vista.nombre.curva_inversion')}}"
        text: ""
      },
      subtitle: {
        // text: {!! json_encode($title) !!}
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
      xAxis: [{
        categories: {!! json_encode($categories) !!},
       crosshair: true
     }],
     yAxis: [{
       labels: {
         format: '{value}',
         style: {
           color: Highcharts.getOptions().colors[2]
         }
       },
       title: {
         text: "{{trans('cronograma.curva_inversion.acumulado')}}",
         style: {
           color: Highcharts.getOptions().colors[2]
         }
       },
       opposite: true
     }, {
       gridLineWidth: 0,
       title: {
         text: "{{trans('cronograma.curva_inversion.mensual')}}",
         style: {
           color: Highcharts.getOptions().colors[0]
         }
       },
       labels: {
         style: {
           color: Highcharts.getOptions().colors[0]
         }
       }
     }, {
       gridLineWidth: 0,
       title: {
         text: "{{trans('forms.montos')}}",
         style: {
           color: Highcharts.getOptions().colors[1]
         }
       },
       labels: {
         style: {
           color: Highcharts.getOptions().colors[1]
         }
       },
       opposite: true
     }],
     tooltip: {
       headerFormat: "<b>{{trans('index.mes')}}" + '{point.key}</b><br>',
       pointFormat: '{series.name}: <b>{point.y}</b>',
       // shared: true
     },
     legend: {
       layout: 'vertical',
       align: 'left',
       x: 80,
       y: 55,
       verticalAlign: 'top',
       floating: true,
       backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'rgba(255,255,255,0.25)'
     },
     series: {!! json_encode($serie1) !!},
     responsive: {
       rules: [{
         condition: {
           maxWidth: 500
         },
         chartOptions: {
           legend: {
             floating: false,
             layout: 'horizontal',
             align: 'center',
             verticalAlign: 'bottom',
             x: 0,
             y: 0
           },
           yAxis: [{
             labels: {
               align: 'right',
               x: 0,
               y: -6
             },
             showLastLabel: false
           }, {
             labels: {
               align: 'left',
               x: 0,
               y: -6
             },
             showLastLabel: false
           }, {
             visible: false
           }]
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
