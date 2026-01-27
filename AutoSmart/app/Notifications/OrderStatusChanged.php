<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public Order $order;

    public string $oldStatus;

    public function __construct(Order $order, string $oldStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
    }

    public function via($notifiable): array
    {
        $channels = ['database'];

        $settings = $notifiable->notificationSettings;
        if ($settings && $settings->email_orders) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تحديث حالة طلبك #'.$this->order->order_number)
            ->greeting('مرحباً '.$notifiable->name)
            ->line('تم تحديث حالة طلبك إلى: '.$this->order->status_label)
            ->action('عرض الطلب', url('/orders/'.$this->order->id))
            ->line('شكراً لتسوقك معنا!');
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'status_label' => $this->order->status_label,
            'message' => 'تم تحديث حالة طلبك #'.$this->order->order_number.' إلى '.$this->order->status_label,
        ];
    }
}
