<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\SendNotificationsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $fcmToken ;
    protected $message; 
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $fcmToken , $message)
    {
        $this->fcmToken=$fcmToken;
        $this->message=$message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        for($i=0;$i<5;$i++)
    (new SendNotificationsService)->sendByFcm($this->fcmToken,$this->message);
    }
}
