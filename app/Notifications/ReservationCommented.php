<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Comment;
use Auth;

class ReservationCommented extends Notification
{
    use Queueable;

    public $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $via = ['database'];
        /*
        if($notifiable->is($this->comment->commentable->user) || $notifiable->is($this->comment->commentable->prestataire))
          $via[] = 'mail';
        */
        return $via;
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
                    ->subject('Nouveau message pour la demande de service #'.$this->comment->commentable->id)
                    ->line("Il y a un nouveau message pour la demande N°".$this->comment->commentable->id." du service \"".$this->comment->commentable->service->label."\".")
                    ->action('Voir la conversation', route('reservations.show', $this->comment->commentable).'#comments');
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
          'url' => route('reservations.show', $this->comment->commentable).'#comments',
          'content' => 'Nouveau message pour la demande de service #'.$this->comment->commentable->id,
          'type' => 'Comment',
          'from' => Auth::user()->id
        ];
    }
}
