<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Reservation;

class ReservationAssignedForUser extends Notification
{
    use Queueable;

    public $reservations;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation)
    {
      $this->reservation = $reservation;
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
                    ->subject('Demande de service "'.$this->reservation->service->label.'" assignée')
                    ->line('Votre demande du service "'.$this->reservation->service->label.'" vient d\'être acceptée et assignée à un prestataire.')
                    ->line('Vous pouvez dès maintenant vous connecter à votre espace membre pour suivre le traitement de votre demande et échanger avec votre prestataire.')
                    ->line('Veuillez cliquer sur le bouton ci-dessous pour continuer.')
                    ->action('Voir la demande', route('reservations.show', $this->reservation));
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
          'url' => route('reservations.show', $this->reservation),
          'content' => 'Demande de service "'.$this->reservation->service->label.'" assignée',
          'type' => 'Reservation',
        ];
    }
}
