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
          <div style="
              border-radius: 15px;
              text-align: center;
              width:70px;
              height:80px;
              margin:50px auto;
              border:2px solid rgb(50,50,50);
              background-image: linear-gradient(top, #0695d6, #0695d6 30%, transparent 30%, transparent 100%);
              background-image: -webkit-linear-gradient(top, #0695d6, #0695d6 30%, transparent 30%, transparent 100%)">

            <span style="font-weight: bold; position: absolute; width: 75px;">{{ $introLines[2] }}</span><br>
            <span style="font-weight: bold; position: absolute; width: 75px; top: 30px; font-size: 200%;">{{ $introLines[3] }}</span><br>
            <span style="font-weight: bold; position: absolute; width: 75px; top: 57px;">{{ $introLines[4] }}</span><br>
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
