<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Devis;

class DevisCreatedForUser extends Notification
{
    use Queueable;

    public $devis;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Devis $devis)
    {
        $this->devis = $devis;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
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
                    ->subject('Vous avez reçu un devis pour votre demande du service "'.$this->devis->reservation->service->label.'"')
                    ->line('Un devis vient d\'être créé pour le traitement de votre récente demande du service "'.$this->devis->reservation->service->label.'".')
                    ->line('Veuillez cliquer sur le bouton ci-dessous pour visualiser la demande ainsi que le nouveau devis.')
                    ->action('Voir le devis', route('reservations.show', $this->devis->reservation));
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
          'url' => route('reservations.show', $this->devis->reservation),
          'content' => 'Devis créé pour votre demande de service #'.$this->devis->reservation_id,
          'type' => 'Devis',
        ];
    }
}
