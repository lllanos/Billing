<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UserCreateNotification extends Notification
{
    public $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable) {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        return (new MailMessage)
		          ->subject(trans(trans('mail.eby').' - '.trans('mail.front.create_user.subject')))
		          ->greeting(trans('mail.front.create_user.hola'))
              ->line(trans('mail.front.create_user.cuerpo'))
              ->line(trans('mail.front.create_user.con_contrasenia').$this->password)
              // ->line(trans('mail.front.create_user.con_cuit').$notifiable->user_publico->cuit)
              ->action(trans('mail.front.create_user.boton'), url(config('app.url').route('register.verify', array($notifiable->codigo_confirmacion), false)))
              ->line(trans('mail.front.create_user.mensaje_despedida'));

    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
    //Show form to seller where they can reset password
    public function showResetForm(Request $request, $token = null)
    {
        return view('seller.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
}
