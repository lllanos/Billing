<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreateNotification extends Notification
{
    public $password;

    // use Queueable;
    public function __construct($password) {
      $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
      return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable) {
        if($notifiable->usuario_sistema)
            $url = config('app.url');   
        else     
            $url = ENV('URL_FRONT');

        return (new MailMessage)
	          ->subject(trans(trans('mail.eby') . ' - ' . trans('mail.back.create_user.subject')))
	          ->greeting(trans('mail.back.create_user.hola'))
            // ->line(url(config('app.url')))
            ->line(trans('mail.back.create_user.cuerpo'))
            ->line(trans('mail.back.create_user.con_contrasenia') . $this->password)
            ->action(trans('mail.back.create_user.boton'), url($url . route('register.verify', array($notifiable->codigo_confirmacion), false)))
            ->line(trans('mail.back.create_user.mensaje_despedida'));
    }

    /**
     * Get the array representation of the notification.
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [
            //
        ];
    }

    //Show form to seller where they can reset password
    public function showResetForm(Request $request, $token = null) {
      return view('seller.passwords.reset')->with(
        ['token' => $token, 'email' => $request->email]
      );
    }
}
