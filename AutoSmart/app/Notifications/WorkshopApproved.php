<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Workshop;

class WorkshopApproved extends Notification
{
    public function __construct(public Workshop $workshop) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تم اعتماد ورشتك في AutoSmart')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تمت الموافقة على ورشتك "' . $this->workshop->name . '" بنجاح!')
            ->line('يمكنك الآن استقبال طلبات الصيانة والحجوزات.')
            ->action('انتقل للوحة التحكم', route('workshop.dashboard'))
            ->line('شكراً لانضمامك إلى AutoSmart!');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'تم اعتماد ورشتك',
            'message' => 'تمت الموافقة على ورشة "' . $this->workshop->name . '" بنجاح',
            'workshop_id' => $this->workshop->id,
            'type' => 'workshop_approved',
        ];
    }
}
