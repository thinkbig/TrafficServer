<?php
namespace Scripts;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Illuminate\Contracts\Mail\Mailer as Mail;

Mail::send('emails.welcome', 'asdfeeeeeeeeee', function($message)
{
    $message->from('us@example.com', 'Laravel');
    $message->to('developer@carmap.me');

    $message->subject('asdfasdfasdfasdfasdfsdf');
});



