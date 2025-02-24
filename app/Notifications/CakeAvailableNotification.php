<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CakeAvailableNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $cake,
        public readonly string $email
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->cake.' - Cake Available')
            ->line('The cake is available!')
            ->line('Cake: '.$this->cake)
            ->line('Email: '.$this->email)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
