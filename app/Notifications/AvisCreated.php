<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Avis;

class AvisCreated extends Notification
{
    use Queueable;

    public $avis;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Avis $avis)
    {
        $this->avis = $avis;
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
                    ->subject('Nouvel avis sur le service "'.$this->avis->service->label.'"')
                    ->line($this->avis->nom.' ('.$this->avis->note.'/5) :')
                    ->line($this->avis->comment)
                    ->action('Consulter', route('avis.index', ['avis' => $this->avis]));
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
          'url' => route('avis.index', ['avis' => $this->avis]),
          'content' => 'Nouvel avis sur le service "'.$this->avis->service->label.'"',
          'type' => 'Avis',
        ];
    }
}
