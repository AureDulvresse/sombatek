<?php

namespace App\Notifications;

use App\Models\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShopApproved extends Notification implements ShouldQueue
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
         ->subject('Votre boutique a été approuvée !')
         ->greeting('Félicitations !')
         ->line('Votre boutique "' . $this->shop->name . '" a été approuvée et est maintenant active sur SombaTeK.')
         ->line('Vous pouvez dès maintenant commencer à ajouter vos produits et recevoir des commandes.')
         ->action('Accéder à ma boutique', route('shops.show', $this->shop))
         ->line('Merci de faire confiance à SombaTeK !');
   }

   public function toArray($notifiable): array
   {
      return [
         'shop_id' => $this->shop->id,
         'shop_name' => $this->shop->name,
         'message' => 'Votre boutique a été approuvée et est maintenant active.'
      ];
   }
}
