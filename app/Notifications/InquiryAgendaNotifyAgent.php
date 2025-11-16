<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\InquiryAgenda;
use Carbon\Carbon;

class InquiryAgendaNotifyAgent extends Notification
{
    use Queueable;

    public $inquiryAgenda;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(InquiryAgenda $inquiryAgenda)
    {
        $this->inquiryAgenda = $inquiryAgenda;
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
                    ->subject("Agenda MCK")
                    ->line("Votre rendez-vous avec le client ". $this->inquiryAgenda->user->full_name. " pour la propriété '" . $this->inquiryAgenda->inquiry->property->title . "' est prévu pour: ".Carbon::parse($this->inquiryAgenda->meeting)->isoFormat("DD MMM YYYY HH:MM"))
                    ->action('Voir les détails', route('properties.show', $this->inquiryAgenda->inquiry->property));
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
