<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Comment;

class UnreadInquiryMessages extends Notification
{
    use Queueable;

    protected $comment;
    protected $count;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Comment $comment, $count)
    {
        $this->comment = $comment;
        $this->count = $count;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return['mail'];
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
                    ->subject(($this->count > 1 ? 'Nouveaux messages' : 'Nouveau message') . ' sur votre requête '.$this->comment->commentable->property->title)
                    ->line("Vous avez " . $this->count . ($this->count > 1 ? ' nouveaux messages' : ' nouveau message') . " sur votre requête #".$this->comment->commentable->id." sur le bien \"".$this->comment->commentable->property->title."\".")
                    ->action('Voir la conversation', route('inquiries.show', $this->comment->commentable).'#comments');
    }
}
