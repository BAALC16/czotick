<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class UserAccountCreated extends Notification
{
    use Queueable;

    public $password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($password = "")
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
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
                    ->subject("Votre compte utilisateur ".config("app.name"))
                    ->line('Votre compte Utilisateur vient d\'être créé. Vous pouvez maintenant vous connecter pour accéder à votre tableau de bord.')
                    ->line('Vos identifiants sont : ')
                    ->line("E-mail : ".$notifiable->email)
                    ->line("Mot de passe : ".(!empty($this->password) ? $this->password : "(Le mot de passe que vous avez choisi)"))
                    ->action('Accéder à mon compte', route('backend'));
                    // ->line('Merci !');
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
}
