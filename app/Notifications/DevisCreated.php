<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Devis;

class DevisCreated extends Notification
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
                    ->subject('Devis créé pour la Demande de service #'.$this->devis->reservation_id)
                    ->line($this->devis->user->prenoms.' vient de créer un devis pour la demande du service "'.$this->devis->reservation->service->label.'" faite par '.$this->devis->reservation->user->prenoms.'.')
                    ->line('Vous pouvez utiliser ce lien pour accéder aux détails du devis.')
                    ->action('Voir le Devis', route('reservations.show', $this->devis->reservation));
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
          'content' => 'Devis créé pour la Demande de service #'.$this->devis->reservation_id,
          'type' => 'Devis',
        ];
    }
}
