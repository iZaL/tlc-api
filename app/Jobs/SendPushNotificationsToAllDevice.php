<?php

namespace App\Jobs;

use Carbon\Carbon;
use Davibennun\LaravelPushNotification\Facades\PushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendPushNotificationsToAllDevice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var array
     */
    private $deviceTokens;
    /**
     * @var string
     */
    private $message;


    private $args;
    /**
     * Create a new job instance.
     *
     * @param array $deviceTokens
     * @param string $message
     * @param array $args
     */
    public function __construct(array $deviceTokens,string $message,$args = [])
    {
        //
        $this->deviceTokens = $deviceTokens;
        $this->message = $message;
        $this->args = $args;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $tokens = [];
        foreach($this->deviceTokens as $token) {
            $tokens[] = PushNotification::Device($token);
        }

        $message = PushNotification::message($this->message,$this->args);
        $devices = PushNotification::DeviceCollection($tokens);
        PushNotification::app('ios')
            ->to($devices)
            ->send($message);
    }
}
