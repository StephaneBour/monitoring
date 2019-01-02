<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Alert extends Mailable
{
    use Queueable, SerializesModels;

    private $_config;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.alert', ['config' => $this->_config]);
    }
}
