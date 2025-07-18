<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
      Schema::create('subscriptions', function (Blueprint $table) {
         $table->id();
         $table->foreignId('user_id')->constrained()->onDelete('cascade');
         $table->string('name');
         $table->string('stripe_id')->nullable();
         $table->string('stripe_status')->nullable();
         $table->string('stripe_price')->nullable();
         $table->integer('quantity')->nullable();
         $table->timestamp('trial_ends_at')->nullable();
         $table->timestamp('ends_at')->nullable();
         $table->enum('type', ['monthly', 'yearly']);
         $table->decimal('amount', 10, 2);
         $table->json('features')->nullable();
         $table->timestamps();
      });

      Schema::create('subscription_items', function (Blueprint $table) {
         $table->id();
         $table->foreignId('subscription_id')->constrained()->onDelete('cascade');
         $table->string('stripe_id')->index();
         $table->string('stripe_product');
         $table->string('stripe_price');
         $table->integer('quantity')->nullable();
         $table->timestamps();

         $table->unique(['subscription_id', 'stripe_price']);
      });
   }

   public function down(): void
   {
      Schema::dropIfExists('subscription_items');
      Schema::dropIfExists('subscriptions');
   }
};
