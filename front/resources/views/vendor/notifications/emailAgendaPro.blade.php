@component('vendor.mail.html.message')
  {{-- Greeting --}}
  @if (! empty($greeting))
    <h1> {{ $greeting }}

    </h1>
  @endif

  <table cellpadding="0" style="width:100%">
    <tbody>
      <tr>
        <td style=" padding: 21px; vertical-align: top; width: 75px;">
          <!-- Para pintar un % del container -->
          <div class="calendar" style="
                                      width: 150px;
                                      height: 170px;
                                      display: block;
                                      float:left;
                                      border:1px solid #ccc;
                                      border-top: 1px solid #ccc;
                                      border-bottom: 7px solid #ccc;
                                      -webkit-border-radius: 4px;
                                      -moz-border-radius: 4px;
                                      border-radius: 4px;
                                      overflow: hidden;
                                      text-align: center;
                                      box-shadow: 0 1px 6px rgba(75,75,75,0.3);
                                      position: relative;
                                      margin-bottom: 20px;">
                                      <div class="calendar_month" style=" width: 100%;
                                                                          display: block;
                                                                          float:left;
                                                                          background: #0695d6;
                                                                          color:#fff;
                                                                          font-weight: bold;
                                                                          font-size: 18px;
                                                                          text-transform: uppercase;
                                                                          padding:8px 0;">
                                        {{ $introLines[2] }}
                                      </div>
                                      <div class="calendar_day" style=" width: 100%;
                                                                        display: block;
                                                                        float:left;
                                                                        line-height: 30px;
                                                                        background: #fff;
                                                                        color:#4b4b4b;">
                                        <div class="calendar_day_number" style="  font-size: 49px;
                                                                                  font-weight: normal;
                                                                                  width: 100%;
                                                                                  display: block;
                                                                                  float:left;
                                                                                  line-height: 52px;">
                                          {{ $introLines[3] }}
                                        </div>
                                        <div class="calendar_day_name" style="  font-size: 22px;
                                                                                font-weight: normal;
                                                                                width: 100%;
                                                                                display: block;
                                                                                float:left;">
                                          {{ $introLines[4] }}
                                        </div>
                                        <div class="calendar_day_hour" style="  width: 100%;
                                                                                display: block;
                                                                                float:left;">
                                        {{ $introLines[0] }} hs.
                                      </div>
                                    </div>

                                  </div>
            </td>
            <td>
          <table>
            <tbody>
              <tr>
                <td><strong>{!! trans('mail.cuando')!!}</strong> {{ $introLines[0] }}</td>
              </tr>
              <tr>
                <td><strong>{!! trans('mail.donde')!!}</strong> {{ $introLines[1] }}
                  @if( $introLines[5] !=='')
                    <a href ="{{ $introLines[5] }}" > {!! trans('mail.ver_en_mapa')!!} </a>
                  @endif
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>

{{-- Action Button --}}
@if (isset($actionText))
<?php
  switch ($level) {
	  case 'success':
		  $color = 'green';
		  break;
	  case 'error':
		  $color = 'red';
		  break;
	  default:
		  $color = 'blue';
  }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endif

<!-- Subcopy -->
@if (isset($actionText))
@component('mail::subcopy')
{!! trans('mail.copiar_link', ['actiontext' => $actionText ])!!} [{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent
@endif
@endcomponent
