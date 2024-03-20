<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReConfirmUserNotification extends Notification {
    public $token;
    public $password;

    public function __construct($token, $password) {
      $this->token = $token;
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
      return (new MailMessage)
		        ->subject(trans(trans('mail.eby') . ' - ' . trans('mail.back.reconfirmar_usuario.subject')))
		        ->greeting(trans('mail.back.reconfirmar_usuario.hola'))
            ->line(trans('mail.back.reconfirmar_usuario.cuerpo'))
            ->line(trans('mail.back.reconfirmar_usuario.con_contrasenia').$this->password)
            ->action(trans('mail.back.reconfirmar_usuario.boton'), url(config('app.url').route('register.verify', array($this->token), false)))
            ->line(trans('mail.back.reconfirmar_usuario.mensaje_despedida'));
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
