<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use PDF;

class EmailAll implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $details;
    public $user_email;
    public $first_name;
    public $last_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($_details,$_user_email,$_first_name,$_last_name)
    {
        $this->details = $_details;
        $this->user_email = $_user_email;
        $this->first_name = $_first_name;
        $this->last_name = $_last_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $details = $this->details;
        $f_name = $this->first_name;
        $l_name = $this->last_name;
        $user_email = $this->user_email;


        $pdf = PDF::loadView('paySlipMail', $details);

        Mail::send('paySlipMail', $details, function($message)use($details,$user_email,$pdf,$f_name,$l_name) {
            $message->to($user_email, $user_email)
                ->subject("Mail From HCTIMER")
                ->attachData($pdf->output(), $f_name . " " . $l_name . ".pdf");
        });
    }
}
