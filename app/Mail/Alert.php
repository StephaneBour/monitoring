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
        return $this->subject($this->_config['subject'])
                ->to($this->_config['to'])
                ->view('emails.alert', [
                    'subject' => $this->_config['subject'],
                    'content' => $this->_config['content'],
                    'status' => $this->_config['status'],
                ]);
    }
}
