<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;


class SendCommercialDocument extends Notification
{
    use Queueable;

    public $document;
    public $pdf_document;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($document, $pdf_document)
    {
        $this->document = $document;
        $this->pdf_document = $pdf_document;
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
        return $this->buildMailMessage($this->document);
    }

    /**
     * Get the reset password notification mail message for the given URL.
     *
     * @param  string  $url
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    protected function buildMailMessage($document)
    {
        $message = (new MailMessage)
        ->greeting(__('general.hello') . ' ' . $document->thirdparty->name)
        ->subject(__('general.send_commercial_document_subject', ['number' => $document->number]))
        ->line(__('general.send_commercial_document', ['number' => $document->number]))
        ->salutation(__('general.send_commercial_document_salutation'))
        ->attachData($this->pdf_document, $document->number.'.pdf');

        return $message;
    }
}
