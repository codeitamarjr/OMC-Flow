<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class CompanyObligationReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(private readonly array $payload)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $dueDate = Carbon::parse((string) $this->payload['due_date'])->format('d M Y');
        $title = (string) $this->payload['title'];
        $message = (string) $this->payload['message'];
        $companyName = (string) $this->payload['company_name'];
        $docCode = (string) $this->payload['doc_code'];

        return (new MailMessage)
            ->subject($title)
            ->line($message)
            ->line("Company: {$companyName}")
            ->line("Obligation: {$docCode}")
            ->line("Due date: {$dueDate}")
            ->action('Open Companies', route('company.index'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->payload;
    }
}

