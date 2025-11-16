<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Inquiry;
use Auth;

class InquiryCreated extends Notification
{
    use Queueable;

    public $inquiry;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Inquiry $inquiry)
    {
        $this->inquiry = $inquiry;
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
                    ->subject("Nouvelle requête sur un bien")
                    ->line('Il y a une nouvelle requête en attente de votre action.')
                    ->line('Veuillez utiliser le lien ci-dessous pour voir les détails.')
                    ->action('Voir la requête', route('inquiries.show', $this->inquiry));
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
            'url' => route('inquiries.show', $this->inquiry),
            'content' => "Nouvelle requête " . $this->inquiry->property->title,
            'type' => 'Requete',
            'from' => Auth::user()->id
        ];
    }
}
