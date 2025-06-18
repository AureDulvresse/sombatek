<?php

namespace App\Notifications;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShopRejected extends Notification implements ShouldQueue
{
   use Queueable;

   public function __construct(public Shop $shop) {}

   public function via($notifiable): array
   {
      return ['mail', 'database'];
   }

   public function toMail($notifiable): MailMessage
   {
      return (new MailMessage)
         ->subject('Votre demande de boutique a été rejetée')
         ->greeting('Bonjour,')
         ->line('Nous regrettons de vous informer que votre demande de création de boutique "' . $this->shop->name . '" a été rejetée.')
         ->line('Pour plus d\'informations sur les raisons de ce rejet, veuillez contacter notre service client.')
         ->action('Contacter le support', route('contact'))
         ->line('Nous vous remercions de votre compréhension.');
   }

   public function toArray($notifiable): array
   {
      return [
         'shop_id' => $this->shop->id,
         'shop_name' => $this->shop->name,
         'message' => 'Votre demande de création de boutique a été rejetée.'
      ];
   }
}
