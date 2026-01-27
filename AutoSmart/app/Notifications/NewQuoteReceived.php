<?php

namespace App\Notifications;

use App\Models\PartRequestQuote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewQuoteReceived extends Notification
{
    use Queueable;

    public PartRequestQuote $quote;

    public function __construct(PartRequestQuote $quote)
    {
        $this->quote = $quote;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('عرض سعر جديد لطلبك')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تلقيت عرض سعر جديد من ' . $this->quote->store->name)
            ->line('السعر: ' . number_format($this->quote->price, 2) . ' ر.س')
            ->action('عرض العروض', url('/part-requests/' . $this->quote->part_request_id))
            ->line('شكراً لاستخدام AutoSmart!');
    }

    public function toArray($notifiable): array
    {
        return [
            'quote_id' => $this->quote->id,
            'part_request_id' => $this->quote->part_request_id,
            'store_name' => $this->quote->store->name,
            'price' => $this->quote->price,
            'message' => 'تلقيت عرض سعر جديد من ' . $this->quote->store->name,
        ];
    }
}
