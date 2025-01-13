<?php

namespace App\Jobs;

use App\Mail\SendVerification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendVerificationCode implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public $email, $code;
    public function __construct($email, $code)
    {
        $this->email = $email;
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new SendVerification($this->code));
    }
}
