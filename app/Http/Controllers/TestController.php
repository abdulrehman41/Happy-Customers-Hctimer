<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\TestMail;
class TestController extends Controller
{
    function Testmail()
    {
        $details = [
            'title' => 'Mail from Hctimer',
            'body' => 'This  email send from Hctimer '
        ];

        \Mail::to('tanveer.khan45450@gmail.com')->send(new TestMail($details));
        return 'send emial';
    }
}
