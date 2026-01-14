<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderReceived extends Notification
{
    use Queueable;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('طلب جديد #'.$this->order->order_number)
            ->greeting('مرحباً '.$notifiable->name)
            ->line('تلقيت طلب جديد من '.$this->order->user->name)
            ->line('المبلغ الإجمالي: '.number_format($this->order->total, 2).' ر.س')
            ->action('عرض الطلب', url('/seller/orders/'.$this->order->id))
            ->line('يرجى تجهيز الطلب في أقرب وقت!');
    }

    public function toArray($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer_name' => $this->order->user->name,
            'total' => $this->order->total,
            'message' => 'طلب جديد #'.$this->order->order_number.' من '.$this->order->user->name,
        ];
    }
}
