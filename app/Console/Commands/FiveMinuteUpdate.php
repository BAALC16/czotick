<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Inquiry;
use Illuminate\Console\Command;
use App\Notifications\UnreadInquiryMessages;
use Illuminate\Notifications\DatabaseNotification;
use Carbon\Carbon;

class FiveMinuteUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiveminute:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Every 5 minutes update';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $comments = Comment::where('commentable_type', Inquiry::class)
            ->whereNull('read_at')->whereBetween('created_at', [Carbon::now()->subMinutes(5), Carbon::now()])->get();

        $comments->map(function($comment) {
            $comment->to->notify(new UnreadInquiryMessages($comment, 1));
        });

        return 0;
    }
}
