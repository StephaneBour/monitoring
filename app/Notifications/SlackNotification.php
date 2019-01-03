<?php

namespace App\Notifications;

use App\Interfaces\Connection;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class SlackNotification extends Notification
{
    use Queueable;

    /**
     * @var array
     */
    private $_config;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $slack = new SlackMessage();

        if ($this->_config['status'] == Connection::STATUS_OK) {
            $slack = $slack->success();
        } elseif ($this->_config['status'] == Connection::STATUS_FAIL) {
            $slack = $slack->error();
        }

        if (! empty($this->_config['channel'])) {
            $slack = $slack->to('#' . $this->_config['channel']);
        } else {
            $slack = $slack->to('#' . env('SLACK_CHANNEL', 'general'));
        }

        if (! empty($this->_config['from'])) {
            $slack = $slack->from($this->_config['from']);
        } else {
            $slack = $slack->from(env('SLACK_FROM', 'Monitoring'));
        }

        $title = $this->_config['uuid'] . ' is ' . $this->_config['status'];
        if (! empty($this->_config['title'])) {
            $title = $this->_config['title'];
        }

        $content = null;
        if (! empty($this->_config['content'])) {
            $content = $this->_config['content'];
        }

        $slack = $slack->attachment(function ($attachment) use ($title, $content) {
            $attachment->title($title)->content($content);
        });

        return $slack;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }
}
