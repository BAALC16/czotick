<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comment;

class ChatCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //return 0;
        $comments = Comment::where("read_at", NULL)->get();

        foreach($comments as $comment){

            $comment->to->notify((new ChatNotifyUser($comment)));

        }
    }
}
