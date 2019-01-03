<?php

namespace App\Actions;

use App\Interfaces\Action;
use App\Notifications\SlackNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class Slack implements Action
{
    use Notifiable;

    protected $_config;

    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * @param string       $type
     * @param Notification $notification
     *
     * @return mixed
     */
    public function routeNotificationForSlack()
    {
        if (! empty($this->_config['webhook_url'])) {
            return $this->_config['webhook_url'];
        }

        return env('SLACK_WEBHOOK_URL');
    }

    /**
     * Send the notification.
     */
    public function send()
    {
        \Illuminate\Support\Facades\Notification::send($this, new SlackNotification($this->_config));
    }
}
