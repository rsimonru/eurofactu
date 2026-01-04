<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;


class SendBudget extends Notification
{
    use Queueable;

    public $budget;
    public $budget_pdf;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($budget, $budget_pdf)
    {
        $this->budget = $budget;
        $this->budget_pdf = $budget_pdf;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return $this->buildMailMessage($this->budget);
    }

    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($budget)
    {
        $message = (new MailMessage)
        ->greeting('Hola ' . $budget->thirdparty->name)
        ->subject('Presupuesto ' . $budget->number)
        ->line('Le enviamos el presupuesto ' . $budget->number)
        ->salutation('Atentamente')
        ->attachData($this->budget_pdf, $budget->number.'.pdf');

        return $message;
    }
}
