<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Comment;
use Auth;

class InquiryCommented extends Notification
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
                    ->subject('Nouveau message sur votre requête '.$this->comment->commentable->property->title)
                    ->line("Il y a un nouveau message sur votre requête sur le bien \"".$this->comment->commentable->property->title."\".")
                    ->action('Voir la conversation', route('inquiries.show', $this->comment->commentable).'#comments');
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
          'url' => route('inquiries.show', $this->comment->commentable).'#comments',
          'content' => 'Nouveau message sur votre requête '.$this->comment->commentable->property->title,
          'type' => 'Comment',
          'from' => Auth::user()->id
        ];
    }
}
