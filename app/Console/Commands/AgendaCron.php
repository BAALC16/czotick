<?php

namespace App\Console\Commands;

use App\Notifications\InquiryAgendaNotifyUser;
use App\Notifications\InquiryAgendaNotifyAgent;
use App\Models\InquiryAgenda;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AgendaCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agenda:cron';

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
        $inquiriesAgendas = InquiryAgenda::where("status", 1)->get();

        foreach($inquiriesAgendas as $inquiriesAgenda){

            $inquiriesAgenda->status = 3;
            $inquiriesAgenda->save();

            if(Carbon::parse(Str::substr($inquiriesAgenda->meeting, 0, 10))->eq(Carbon::tomorrow()->format('Y-m-d'))){
                $inquiriesAgenda->user->notify((new InquiryAgendaNotifyUser($inquiriesAgenda)));
                $inquiriesAgenda->agent->notify((new InquiryAgendaNotifyAgent($inquiriesAgenda)));
            }

        }
    }
}
