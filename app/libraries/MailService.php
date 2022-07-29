<?php
namespace App\libraries;
use App\Mail\Notify;
use Illuminate\Support\Facades\Mail;
   

class MailService
{

    public function send(array $data, $htmlTemplate, $textTemplate = null)
    {
        Mail::to('airondev@gmail.com')->send(new Notify($data));
    }
}
