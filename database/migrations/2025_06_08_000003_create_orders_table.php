<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
   {
      Schema::create('orders', function (Blueprint $table) {
         $table->id();
         $table->foreignId('user_id')->constrained()->onDelete('cascade');
         $table->foreignId('shop_id')->constrained()->onDelete('cascade');
         $table->foreignId('promoter_id')->nullable()->constrained('users')->onDelete('set null');
         $table->string('order_number')->unique();
         $table->decimal('subtotal', 10, 2);
         $table->decimal('tax', 10, 2);
         $table->decimal('shipping', 10, 2);
         $table->decimal('total', 10, 2);
         $table->decimal('commission_amount', 10, 2);
         $table->decimal('promoter_commission', 10, 2)->nullable();
         $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
         $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
         $table->string('payment_method');
         $table->string('shipping_address');
         $table->string('billing_address');
         $table->string('tracking_number')->nullable();
         $table->text('notes')->nullable();
         $table->decimal('total_amount', 10, 2)->default(0);
         $table->string('shipping_method');
         $table->decimal('shipping_cost', 10, 2)->default(0);
         $table->timestamps();
         $table->softDeletes();
      });

      Schema::create('order_items', function (Blueprint $table) {
         $table->id();
         $table->foreignId('order_id')->constrained()->onDelete('cascade');
         $table->foreignId('product_id')->constrained()->onDelete('cascade');
         $table->string('product_name');
         $table->string('product_sku');
         $table->integer('quantity');
         $table->decimal('price', 10, 2);
         $table->decimal('subtotal', 10, 2);
         $table->json('options')->nullable();
         $table->timestamps();
      });
   }

   public function down(): void
   {
      Schema::dropIfExists('order_items');
      Schema::dropIfExists('orders');
   }
};
